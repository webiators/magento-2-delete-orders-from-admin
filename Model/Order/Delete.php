<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Model\Order;

use Magento\Framework\App\ResourceConnection;

class Delete
{
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * Delete constructor.
     * @param ResourceConnection $resource
     * @param \Webiators\DeleteOrdersFromAdmin\Helper\Data $helper
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        ResourceConnection $resource,
        \Webiators\DeleteOrdersFromAdmin\Helper\Data $helper,
        \Magento\Sales\Model\Order $order
    ) {
        $this->resource = $resource;
        $this->helper = $helper;
        $this->order = $order;
    }

    /**
     * @param $orderId
     * @throws \Exception
     */
    public function deleteOrdersFromAdmin($orderId)
    {
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $invoiceGridTable = $connection->getTableName($this->helper->getTableName('sales_invoice_grid'));
         $shippmentGridTable = $connection->getTableName($this->helper->getTableName('sales_shipment_grid'));
        $creditmemoGridTable = $connection->getTableName($this->helper->getTableName('sales_creditmemo_grid'));
        $order = $this->order->load($orderId);
        $order->delete();
        $connection->rawQuery('DELETE FROM `'.$invoiceGridTable.'` WHERE order_id='.$orderId);
        $connection->rawQuery('DELETE FROM `'.$shippmentGridTable.'` WHERE order_id='.$orderId);
        $connection->rawQuery('DELETE FROM `'.$creditmemoGridTable.'` WHERE order_id='.$orderId);
    }
}
