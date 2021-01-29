<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;

class DeleteOrder extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Order\Delete
     */
    protected $delete;

    /**
     * Order constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Model\Order $order
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Order\Delete $delete
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Webiators\DeleteOrdersFromAdmin\Model\Order\Delete $delete
    ) {
        $this->order = $order;
        $this->delete = $delete;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->order->load($orderId);
        $incrementId = $order->getIncrementId();
        try {
            $this->delete->deleteOrdersFromAdmin($orderId);
            $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
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
}
