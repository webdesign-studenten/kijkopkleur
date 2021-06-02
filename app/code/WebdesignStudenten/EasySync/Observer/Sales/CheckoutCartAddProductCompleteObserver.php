<?php

namespace WebdesignStudenten\EasySync\Observer\Sales;
use Magento\Checkout\Model\Session as CheckoutSession;
class CheckoutCartAddProductCompleteObserver implements \Magento\Framework\Event\ObserverInterface
{
  protected $logger;
  protected $storeDate;
  protected $helper;
  protected $easySyncCollectionFactory;
  private $_checkoutSession;

  public function __construct (
      \Psr\Log\LoggerInterface $logger,
      \Magento\Framework\Stdlib\DateTime\DateTime $date,
      \WebdesignStudenten\EasySync\Helper\Data $helper,
      \WebdesignStudenten\EasySync\Model\DataIDFactory $easySyncCollectionFactory,
      CheckoutSession $checkoutSession
  ) {
      $this->logger = $logger;
      $this->storeDate = $date;
      $this->helper = $helper;
      $this->easySyncCollectionFactory = $easySyncCollectionFactory;
      $this->_checkoutSession = $checkoutSession;
 }

 public function execute(\Magento\Framework\Event\Observer $observer) {

      if ($this->helper->getDataMode() == "receiver") return;
      
      $quoteId = $this->_checkoutSession->getQuote()->getId();
      $easySyncCollectionFactory = $this->easySyncCollectionFactory->create()->getCollection()
          ->addFieldToFilter('dataScope', 'quote')
          ->addFieldToFilter('dataID', array('eq' => $quoteId))
          ->getFirstItem();
      if ($easySyncCollectionFactory->hasData()) {
          $updatedCustomer = $this->easySyncCollectionFactory->create()->load($easySyncCollectionFactory['data_sync_id']);
          $updatedCustomer->setData('UpdateFlag','1');
          $updatedCustomer->setData('UpdateDate',$this->storeDate->gmtDate());
          $updatedCustomer->setData('ServerLocation',$_SERVER['SERVER_ADDR']);
          $updatedCustomer->save();
      } else {
          $model = $this->easySyncCollectionFactory->create();
          $model->addData([
              "dataID" => $quoteId,
              "ServerLocation" => $_SERVER['SERVER_ADDR'],
              "dataScope" => 'quote',
              "UpdateDate" => $this->storeDate->gmtDate(),
              "UpdateFlag" => 1
          ]);
          $model->save();
      }
    }
}
