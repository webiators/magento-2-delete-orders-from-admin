<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;

class DeleteCreditmemo extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;
    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Creditmemo\Delete
     */
    protected $delete;

    /**
     * Creditmemo constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Creditmemo\Delete $delete
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Webiators\DeleteOrdersFromAdmin\Model\Creditmemo\Delete $delete
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->delete = $delete;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        $creditmemo = $this->creditmemoRepository->get($creditmemoId);
        try {
            $this->delete->deleteCreditmemoFromAdmin($creditmemoId);
            $this->messageManager->addSuccessMessage(__('Successfully deleted credit memo #%1.', $creditmemo->getIncrementId()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete credit memo #%1.', $creditmemo->getIncrementId()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/creditmemo/');
        return $resultRedirect;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webiators_DeleteOrdersFromAdmin::delete_order');
    }
}
