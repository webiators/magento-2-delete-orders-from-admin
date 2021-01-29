<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;

class DeleteInvoice extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Invoice\Delete
     */
    protected $delete;

    /**
     * Invoice constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Invoice\Delete $delete
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Webiators\DeleteOrdersFromAdmin\Model\Invoice\Delete $delete
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->delete = $delete;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $invoice = $this->invoiceRepository->get($invoiceId);
        try {
            $this->delete->deleteInvoiceFromAdmin($invoiceId);
            $this->messageManager->addSuccessMessage(__('Successfully deleted invoice #%1.', $invoice->getIncrementId()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete invoice #%1.', $invoice->getIncrementId()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/invoice/');
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
