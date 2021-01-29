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

class MassDeleteOrder extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Order\Delete
     */
    protected $delete;

    /**
     * MassOrder constructor.
     * @param Context $context
     * @param Filter $filter
     * @param OrderManagementInterface $orderManagement
     * @param CollectionFactory $orderCollectionFactory
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Order\Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Webiators\DeleteOrdersFromAdmin\Model\Order\Delete $delete
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->orderCollectionFactory = $orderCollectionFactory;
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
        $collectionInvoice = $this->filter->getCollection($this->orderCollectionFactory->create());

        foreach ($collectionInvoice as $order) {
            $orderId = $order->getId();
            $incrementId = $order->getIncrementId();
            try {
                $this->deleteOrdersFromAdmin($orderId);
                $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');
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
     * @param $orderId
     * @throws \Exception
     */
    protected function deleteOrdersFromAdmin($orderId)
    {
        $this->delete->deleteOrdersFromAdmin($orderId);
    }
}
