<?php
namespace Sanket\Test\Block\Adminhtml\Image\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Sanket\Test\Api\ImageRepositoryInterface;

class Generic
{
    protected $context;
    protected $imageRepository;
    public function __construct(
        Context $context,
        ImageRepositoryInterface $imageRepository
    ) {
        $this->context = $context;
        $this->imageRepository = $imageRepository;
    }
    public function getImageId()
    {
        try {
            return $this->imageRepository->getById(
                $this->context->getRequest()->getParam('test_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
