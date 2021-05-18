<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WebdesignStudenten\MassAction\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Backend\App\Action;


class Complete extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Sales::cancel';
    
    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;
    
    private $orderRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface|null $orderManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository
    ) {
        
         $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->orderRepository =$orderRepository;
        parent::__construct($context);
    }

    /**
     * Complete selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
		 $completeStatus = \Magento\Sales\Model\Order::STATE_COMPLETE;
        $countOrder = 0;
         $collection = $this->filter->getCollection($this->collectionFactory->create());
	try{
		foreach ($collection->getItems() as $order) {
			
			$order = $this->orderRepository->get($order->getEntityId());
            $order->setStatus($completeStatus)->setState($completeStatus)->addStatusHistoryComment(__('Completed using mass action'));
            $this->orderRepository->save($order);
            $countOrder++;
        }

        if ($countOrder) {
            $this->messageManager->addSuccessMessage(__('We completed %1 order(s).', $countOrder));
        }
		
	}catch(\Exception $e){
		$countNonOrder = $collection->count() - $countOrder;

        if ($countNonOrder && $countOrder) {
            $this->messageManager->addErrorMessage(__('%1 order(s) cannot be completed.', $countNonOrder));
        } elseif ($countNonOrder) {
            $this->messageManager->addErrorMessage(__('You cannot complete the order(s).'.'   '.$e->getMessage()));
        }
		//$this->messageManager->addErrorMessage($e->getMessage());
	}
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/index');
        return $resultRedirect;
    }
}
