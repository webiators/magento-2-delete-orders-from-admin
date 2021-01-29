<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Plugin\Creditmemo;

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
     * @param \Magento\Sales\Block\Adminhtml\Order\Creditmemo\View $subject
     * @param $result
     * @return mixed
     */
    public function afterGetBackUrl(\Magento\Sales\Block\Adminhtml\Order\Creditmemo\View $subject, $result)
    {
        if ($this->isAllowedResources()) {
            $params = $subject->getRequest()->getParams();
            $message = __('Are you sure you want to do this?');
            if ($subject->getRequest()->getFullActionName() == 'sales_order_creditmemo_view') {
                $subject->addButton(
                    'webiators-deleteOrdersFromAdmin',
                    ['label' => __('Delete'), 'onclick' => 'confirmSetLocation(\'' . $message . '\',\'' . $this->getDeleteUrl($params['creditmemo_id']) . '\')', 'class' => 'webiators-deleteOrdersFromAdmin'],
                    -1
                );
            }
        }

        return $result;
    }

    /**
     * @param string $creditmemoId
     * @return mixed
     */
    public function getDeleteUrl($creditmemoId)
    {
        return $this->helper->getUrl(
            'orderdelete/order/deletecreditmemo',
            [
                'creditmemo_id' => $creditmemoId
            ]
        );
    }
}
