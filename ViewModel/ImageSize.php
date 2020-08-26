<?php

declare(strict_types=1);

namespace Snowdog\CategoryAttributes\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryReadInterface;

class ImageSize implements ArgumentInterface
{
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
        if (!$category->getImage() || !$this->getPubDirectory()->isReadable($category->getImage())) {
            return null;
        }

        $imagePath = $this->getPubDirectory()->getAbsolutePath($category->getImage());
        [$width, $height] = getimagesize($imagePath);

        return $width ? ['width' => $width, 'height' => $height] : null;
    }

    private function getPubDirectory(): DirectoryReadInterface
    {
        if (!$this->pubDirectory) {
            $this->pubDirectory = $this->filesystem->getDirectoryRead(DirectoryList::PUB);
        }

        return $this->pubDirectory;
    }
}
