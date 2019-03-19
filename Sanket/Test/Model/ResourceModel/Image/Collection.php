<?php
namespace Sanket\Test\Model\ResourceModel\Image;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{    
    protected $_idFieldName = 'test_id';
    protected function _construct()
    {
        $this->_init(
            'Sanket\Test\Model\Image',
            'Sanket\Test\Model\ResourceModel\Image'
        );
    }
}
