<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Plugin;

class PluginAbstract
{
    /**
     * @var \Magento\Authorization\Model\Acl\AclRetriever
     */
    protected $aclRetriever;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * PluginAbstract constructor.
     * @param \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->aclRetriever = $aclRetriever;
        $this->authSession = $authSession;
    }

    /**
     * @return bool
     */
    public function isAllowedResources()
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        if (in_array("Magento_Backend::all", $resources) || in_array("Webiators_DeleteOrdersFromAdmin::delete_order", $resources)) {
            return true;
        }
        return false;
    }
}
