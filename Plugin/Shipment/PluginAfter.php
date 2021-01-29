<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Plugin\Shipment;

class PluginAfter extends \Webiators\DeleteOrdersFromAdmin\Plugin\PluginAbstract
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $helper;

    /**
     * PluginAfter constructor.
     * @param \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Helper\Data $helper
     */
    public function __construct(
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Helper\Data $helper
    ) {
        parent::__construct($aclRetriever, $authSession);
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Shipping\Block\Adminhtml\View $subject
     * @param $result
     * @return mixed
     */
    public function afterGetBackUrl(\Magento\Shipping\Block\Adminhtml\View $subject, $result)
    {
        if ($this->isAllowedResources()) {
            $params = $subject->getRequest()->getParams();
            $message = __('Are you sure you want to do this?');
            if ($subject->getRequest()->getFullActionName() == 'adminhtml_order_shipment_view') {
                $subject->addButton(
                    'webiators-deleteOrdersFromAdmin',
                    ['label' => __('Delete'), 'onclick' => 'confirmSetLocation(\'' . $message . '\',\'' . $this->getDeleteUrl($params['shipment_id']) . '\')', 'class' => 'webiators-deleteOrdersFromAdmin'],
                    -1
                );
            }
        }

        return $result;
    }

    /**
     * @param string $shipmentId
     * @return mixed
     */
    public function getDeleteUrl($shipmentId)
    {
        return $this->helper->getUrl(
            'orderdelete/order/deleteshipment',
            [
                'shipment_id' => $shipmentId
            ]
        );
    }
}
