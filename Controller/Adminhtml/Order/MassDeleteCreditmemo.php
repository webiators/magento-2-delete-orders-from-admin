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

class MassDeleteCreditmemo extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory
     */
    protected $memoCollectionFactory;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var \Webiators\DeleteOrdersFromAdmin\Model\Creditmemo\Delete
     */
    protected $delete;

    /**
     * MassCreditmemo constructor.
     * @param Context $context
     * @param Filter $filter
     * @param OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Webiators\DeleteOrdersFromAdmin\Model\Creditmemo\Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Webiators\DeleteOrdersFromAdmin\Model\Creditmemo\Delete $delete
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->memoCollectionFactory = $memoCollectionFactory;
        $this->creditmemoRepository = $creditmemoRepository;
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
        $collectionMemo = $this->filter->getCollection($this->memoCollectionFactory->create());
        foreach ($collectionMemo as $memo) {
            array_push($selected, $memo->getId());
        }

        if ($selected) {
            foreach ($selected as $creditmemoId) {
                $creditmemo = $this->creditmemoRepository->get($creditmemoId);
                try {
                    $order = $this->deleteCreditmemoFromAdmin($creditmemoId);

                    $this->messageManager->addSuccessMessage(__('Successfully deleted credit memo #%1.', $creditmemo->getIncrementId()));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Error delete credit memo #%1.', $creditmemo->getIncrementId()));
                }
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($params['namespace'] == 'sales_order_view_creditmemo_grid') {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
        } else {
            $resultRedirect->setPath('sales/creditmemo/');
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
     * @param $creditmemoId
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    protected function deleteCreditmemoFromAdmin($creditmemoId)
    {
        return $this->delete->deleteCreditmemoFromAdmin($creditmemoId);
    }
}
