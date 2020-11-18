<?php

declare(strict_types=1);

namespace Snowdog\CategoryAttributes\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category\FileInfo as CategoryFileInfo;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryReadInterface;

class ImageSize implements ArgumentInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DirectoryReadInterface
     */
    private $baseDirectory;

    /**
     * @var DirectoryReadInterface
     */
    private $mediaDirectory;

    /**
     * @var DirectoryReadInterface
     */
    private $pubDirectory;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function get(CategoryInterface $category): ?array
    {
        $image = $this->getImagePath($category);
        if (!$image) {
            return null;
        }

        [$width, $height] = getimagesize($image);

        return $width ? ['width' => $width, 'height' => $height] : null;
    }

    private function getImagePath(CategoryInterface $category): ?string
    {
        $image = $category->getImage();
        if (!$image) {
            return null;
        }

        if ($this->isBeginsWithMediaDirectoryPath($image)) {
            $pubDir = $this->getPubDirectory();
            return $pubDir->isReadable($image) ? $pubDir->getAbsolutePath($image) : null;
        }

        $mediaDir = $this->getMediaDirectory();
        $image = CategoryFileInfo::ENTITY_MEDIA_PATH . '/' . $image;

        return $mediaDir->isReadable($image) ? $mediaDir->getAbsolutePath($image) : null;
    }

    private function isBeginsWithMediaDirectoryPath(string $fileName): bool
    {
        $filePath = ltrim($fileName, '/');
        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath($filePath);

        return strpos($filePath, $mediaDirectoryRelativeSubpath) === 0;
    }

    private function getMediaDirectoryPathRelativeToBaseDirectoryPath(string $filePath = ''): string
    {
        $baseDirectory = $this->getBaseDirectory();
        $baseDirectoryPath = $baseDirectory->getAbsolutePath();
        $mediaDirectoryPath = $this->getMediaDirectory()->getAbsolutePath();
        $pubDirectoryPath = $this->getPubDirectory()->getAbsolutePath();

        $mediaDirectoryRelativeSubpath = substr($mediaDirectoryPath, strlen($baseDirectoryPath));
        $pubDirectory = $baseDirectory->getRelativePath($pubDirectoryPath);

        if (strpos($mediaDirectoryRelativeSubpath, $pubDirectory) === 0
            && strpos($filePath, $pubDirectory) !== 0
        ) {
            $mediaDirectoryRelativeSubpath = substr($mediaDirectoryRelativeSubpath, strlen($pubDirectory));
        }

        return $mediaDirectoryRelativeSubpath;
    }

    private function getBaseDirectory(): DirectoryReadInterface
    {
        if (!$this->baseDirectory) {
            $this->baseDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        }

        return $this->baseDirectory;
    }

    private function getMediaDirectory(): DirectoryReadInterface
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    private function getPubDirectory(): DirectoryReadInterface
    {
        if (!$this->pubDirectory) {
            $this->pubDirectory = $this->filesystem->getDirectoryRead(DirectoryList::PUB);
        }

        return $this->pubDirectory;
    }
}
