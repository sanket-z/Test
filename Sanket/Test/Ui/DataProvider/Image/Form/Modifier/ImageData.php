<?php
namespace Sanket\Test\Ui\DataProvider\Image\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Sanket\Test\Model\ResourceModel\Image\CollectionFactory;

class ImageData implements ModifierInterface
{    
    protected $collection;
    public function __construct(
        CollectionFactory $imageCollectionFactory
    ) {
        $this->collection = $imageCollectionFactory->create();
    }
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
    public function modifyData(array $data)
    {
        $items = $this->collection->getItems();
        foreach ($items as $image) {
            $_data = $image->getData();
            if (isset($_data['image'])) {
                $imageArr = [];
                $imageArr[0]['name'] = 'Image';
                $imageArr[0]['url'] = $image->getImageUrl();
                $_data['image'] = $imageArr;
            }
            $image->setData($_data);
            $data[$image->getId()] = $_data;
        }
        return $data;
    }
}
