<?php
/**
 * @category   Webiators
 * @package    Webiators_DeleteOrdersFromAdmin
 * @author     Webitaors Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\DeleteOrdersFromAdmin\Ui\Component\Control;

use Magento\Ui\Component\Control\Action;

class DeleteAction extends Action
{
    /**
     * setup url
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        $context = $this->getContext();
        $config['url'] = $context->getUrl(
            $config['deleteAction'],
            ['order_id' => $context->getRequestParam('order_id')]
        );
        $this->setData('config', (array)$config);
        parent::prepare();
    }
}
