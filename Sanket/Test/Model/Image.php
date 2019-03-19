<?php
namespace Sanket\Test\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use Sanket\Test\Api\Data\ImageInterface;

class Image extends AbstractModel implements ImageInterface
{
    const CACHE_TAG = 'test_details';
    protected $uploaderPool;
    public function __construct(
        Context $context,
        Registry $registry,
        UploaderPool $uploaderPool,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->uploaderPool    = $uploaderPool;
    }
    protected function _construct()
    {     
        $this->_init('Sanket\Test\Model\ResourceModel\Image');
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    public function getImage()
    {
        return $this->getData(ImageInterface::IMAGE);
    }
    public function setImage($image)
    {
        return $this->setData(ImageInterface::IMAGE, $image);
    }
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }
}
