<?php
namespace Sanket\Test\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Sanket\Test\Model\ResourceModel\Image\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
   public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $imageCollectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection   = $imageCollectionFactory->create();
        $this->pool         = $pool;
        $this->meta         = $this->prepareMeta($this->meta);
    }
    public function prepareMeta(array $meta)
    {
        $meta = parent::getMeta();
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }
    public function getData()
    {
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }
        return $this->data;
    }
}
