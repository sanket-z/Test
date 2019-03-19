<?php
namespace Sanket\Test\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Sanket\Test\Api\ImageRepositoryInterface;
use Sanket\Test\Api\Data\ImageInterface;
use Sanket\Test\Api\Data\ImageInterfaceFactory;
use Sanket\Test\Model\ResourceModel\Image as ResourceImage;
use Sanket\Test\Model\ResourceModel\Image\CollectionFactory as ImageCollectionFactory;

class ImageRepository implements ImageRepositoryInterface
{
    protected $instances = [];
    protected $resource;
    protected $imageCollectionFactory;
    protected $imageInterfaceFactory;
    protected $dataObjectHelper;
    public function __construct(
        ResourceImage $resource,
        ImageCollectionFactory $imageCollectionFactory,
        ImageInterfaceFactory $imageInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->imageInterfaceFactory = $imageInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }
    public function save(ImageInterface $image)
    {
        try {     
            $this->resource->save($image);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the image: %1',
                $exception->getMessage()
            ));
        }
        return $image;
    }
    public function getById($imageId)
    {
        if (!isset($this->instances[$imageId])) {
            $image = $this->imageInterfaceFactory->create();
            $this->resource->load($image, $imageId);
            if (!$image->getId()) {
                throw new NoSuchEntityException(__('Requested image doesn\'t exist'));
            }
            $this->instances[$imageId] = $image;
        }
        return $this->instances[$imageId];
    }
    public function delete(ImageInterface $image)
    {     
        $id = $image->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($image);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove image %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }
    public function deleteById($imageId)
    {
        $image = $this->getById($imageId);
        return $this->delete($image);
    }
}
