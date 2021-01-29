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
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Api\OrderManagementInterface;

class MassDeleteShipment extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $shipment;

    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Shipment\Delete
     */
    protected $delete;

    /**
     * MassShipment constructor.
     * @param Context $context
     * @param Filter $filter
     * @param OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Shipment\Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Webiators\DeleteOrdersFromAdmin\Model\Shipment\Delete $delete
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->shipment = $shipment;
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
        $collectionShipment = $this->filter->getCollection($this->shipmentCollectionFactory->create());
        foreach ($collectionShipment as $shipment) {
            array_push($selected, $shipment->getId());
        }
        if ($selected) {
            foreach ($selected as $shipmentId) {
                $shipment = $this->getShipmentbyId($shipmentId);
                try {
                    $order = $this->deleteShipmentFromAdmin($shipmentId);
                    $this->messageManager->addSuccessMessage(__('Successfully deleted shipment #%1.', $shipment->getIncrementId()));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Error delete shipment #%1.', $shipment->getIncrementId()));
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/');
        if ($params['namespace'] == 'sales_order_view_shipment_grid') {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
        } else {
            $resultRedirect->setPath('sales/shipment/');
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
     * @param $shipmentId
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    protected function deleteShipmentFromAdmin($shipmentId)
    {
        return $this->delete->deleteShipmentFromAdmin($shipmentId);
    }

    /**
     * @param $shipmentId
     * @return \Magento\Sales\Model\Order\Shipment
     */
    protected function getShipmentbyId($shipmentId)
    {
        return $this->shipment->load($shipmentId);
    }
}
