<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;

class MassDeleteInvoice extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Invoice\Delete
     */
    protected $delete;

    /**
     * MassInvoice constructor.
     * @param Context $context
     * @param Filter $filter
     * @param OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Invoice\Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Webiators\DeleteOrdersFromAdmin\Model\Invoice\Delete $delete
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->delete = $delete;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function massAction(AbstractCollection $collection)
    {
        $params = $this->getRequest()->getParams();
        $selected = [];
        $collectionInvoice = $this->filter->getCollection($this->invoiceCollectionFactory->create());
        foreach ($collectionInvoice as $invoice) {
            array_push($selected, $invoice->getId());
        }
        if ($selected) {
            foreach ($selected as $invoiceId) {
                $invoice = $this->invoiceRepository->get($invoiceId);
                try {
                    $order = $this->deleteInvoiceFromAdmin($invoiceId);
                    $this->messageManager->addSuccessMessage(__('Successfully deleted invoice #%1.', $invoice->getIncrementId()));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Error delete invoice #%1.', $invoice->getIncrementId()));
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($params['namespace'] == 'sales_order_view_invoice_grid') {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
        } else {
            $resultRedirect->setPath('sales/invoice/');
        }
        return $resultRedirect;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webiators_DeleteOrdersFromAdmin::delete_order');
    }

    /**
     * @param $invoiceId
     * @return \Webiators\DeleteOrdersFromAdmin\Model\Invoice\Delete
     */
    protected function deleteInvoiceFromAdmin($invoiceId)
    {
        return $this->delete->deleteInvoiceFromAdmin($invoiceId);
    }
}
