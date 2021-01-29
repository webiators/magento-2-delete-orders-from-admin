<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Plugin\Order;

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
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     * @param $result
     * @return mixed
     */
    public function afterGetBackUrl(\Magento\Sales\Block\Adminhtml\Order\View $subject, $result)
    {
        if ($this->isAllowedResources()) {
            $params = $subject->getRequest()->getParams();
            $message = __('Are you sure you want to Delete this?');
            if ($subject->getRequest()->getFullActionName() == 'sales_order_view') {
                $subject->addButton(
                    'webiators-deleteOrdersFromAdmin',
                    ['label' => __('Delete'), 'onclick' => 'confirmSetLocation(\'' . $message . '\',\'' . $this->getDeleteUrl($params['order_id']) . '\')', 'class' => 'webiators-deleteOrdersFromAdmin'],
                    -1
                );
            }
        }
        return $result;
    }

    /**
     * @param string $orderId
     * @return mixed
     */
    public function getDeleteUrl($orderId)
    {
        return $this->helper->getUrl(
            'orderdelete/order/deleteorder',
            [
                'order_id' => $orderId
            ]
        );
    }
}
