<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;

class DeleteShipment extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $shipment;

    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Shipment\Delete
     */
    protected $delete;

    /**
     * Shipment constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Shipment\Delete $delete
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Webiators\DeleteOrdersFromAdmin\Model\Shipment\Delete $delete
    ) {
        $this->shipment = $shipment;
        $this->delete = $delete;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipment = $this->shipment->load($shipmentId);
        try {
            $this->delete->deleteShipmentFromAdmin($shipmentId);
            $this->messageManager->addSuccessMessage(__('Successfully deleted shipment #%1.', $shipment->getIncrementId()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete shipment #%1.', $shipment->getIncrementId()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/');
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
