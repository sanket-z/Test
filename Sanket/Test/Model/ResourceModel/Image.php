<?php
namespace Sanket\Test\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Image extends AbstractDb
{
   public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }
    protected function _construct()
    {     
        $this->_init('test_details', 'test_id');
    }
}
