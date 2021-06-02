<?php

namespace WebdesignStudenten\EasySync\Observer\Sales;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
  protected $logger;
  protected $storeDate;
  protected $helper;
  protected $easySyncCollectionFactory;

  public function __construct (
      \Psr\Log\LoggerInterface $logger,
      \Magento\Framework\Stdlib\DateTime\DateTime $date,
      \WebdesignStudenten\EasySync\Helper\Data $helper,
      \WebdesignStudenten\EasySync\Model\DataIDFactory $easySyncCollectionFactory
  ) {
      $this->logger = $logger;
      $this->storeDate = $date;
      $this->helper = $helper;
      $this->easySyncCollectionFactory = $easySyncCollectionFactory;
 }

 public function execute(\Magento\Framework\Event\Observer $observer) {

      if ($this->helper->getDataMode() == "receiver") return;

      $orderIncrementId = $observer->getEvent()->getOrder()->getIncrementId();
      $easySyncCollectionFactory = $this->easySyncCollectionFactory->create()->getCollection()
          ->addFieldToFilter('dataScope', 'sales')
          ->addFieldToFilter('dataID', array('eq' => $orderIncrementId))
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
              "dataID" => $orderIncrementId,
              "ServerLocation" => $_SERVER['SERVER_ADDR'],
              "dataScope" => 'sales',
              "UpdateDate" => $this->storeDate->gmtDate(),
              "UpdateFlag" => 1
          ]);
          $model->save();
      }
    }
}
