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
    private $mediaDirectory;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function get(CategoryInterface $category): ?array
    {
        if (!$category->getImage() || !$this->getMediaDirectory()->isReadable()) {
            return null;
        }

	    $catImagePath = '/catalog/category/';
	    $imagePath = $this->mediaDirectory->getAbsolutePath($catImagePath.$category->getImage());
        [$width, $height] = getimagesize($imagePath);

        return $width ? ['width' => $width, 'height' => $height] : null;
    }

    private function getMediaDirectory(): DirectoryReadInterface
    {
	    if (!$this->mediaDirectory) {
		    $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

	    return $this->mediaDirectory;
    }
}
