<?php
namespace Sanket\Test\Controller\Adminhtml\Image;

use Sanket\Test\Controller\Adminhtml\Image;

class Index extends Image
{    
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sanket_Test::image');
        $resultPage->getConfig()->getTitle()->prepend(__('Test Details'));
        $resultPage->addBreadcrumb(__('Test Details'), __('Test Details'));
        return $resultPage;
    }
}
