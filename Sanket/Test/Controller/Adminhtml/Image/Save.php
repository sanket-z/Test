<?php
namespace Sanket\Test\Controller\Adminhtml\Image;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Message\Manager;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Sanket\Test\Api\ImageRepositoryInterface;
use Sanket\Test\Api\Data\ImageInterface;
use Sanket\Test\Api\Data\ImageInterfaceFactory;
use Sanket\Test\Controller\Adminhtml\Image;
use Sanket\Test\Model\Uploader;
use Sanket\Test\Model\UploaderPool;

class Save extends Image
{
    protected $messageManager;
    protected $imageRepository;
    protected $imageFactory;
    protected $dataObjectHelper;
    protected $uploaderPool;
    public function __construct(
        Registry $registry,
        ImageRepositoryInterface $imageRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Manager $messageManager,
        ImageInterfaceFactory $imageFactory,
        DataObjectHelper $dataObjectHelper,
        UploaderPool $uploaderPool,
        Context $context
    ) {
        parent::__construct($registry, $imageRepository, $resultPageFactory, $dateFilter, $context);
        $this->messageManager   = $messageManager;
        $this->imageFactory      = $imageFactory;
        $this->imageRepository   = $imageRepository;
        $this->dataObjectHelper  = $dataObjectHelper;
        $this->uploaderPool = $uploaderPool;
    }
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
       
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('test_id');            
            if ($id) {
                $model = $this->imageRepository->getById($id);
            } else {
                unset($data['test_id']);
                $model = $this->imageFactory->create();               
            }

            try {
                $image = $this->getUploader('image')->uploadFileAndGetName('image', $data);
                $data['image'] = $image;
                $this->dataObjectHelper->populateWithArray($model, $data, ImageInterface::class);
                $this->imageRepository->save($model);

                if ($id) {
                    $model1 = $this->imageRepository->getById($id);
                } else {
                    $model1 = $this->imageRepository->getById($model->getId());
                }
                $model1->setTestName($data['test_name']);
                $model1->setTestNo($data['test_no']);
                $model1->save();
            
                $this->messageManager->addSuccessMessage(__('You saved this image.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['test_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the image:' . $e->getMessage())
                );
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['test_id' => $this->getRequest()->getParam('test_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    protected function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }
}
