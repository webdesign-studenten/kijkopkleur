<?php
namespace WebdesignStudenten\EasySync\Observer\Catalog;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
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

    //   if ($this->helper->getDataMode() == "receiver") return;

    $dataModified = $this->helper->getRecursiveArrayDiff($observer->getEvent()->getProduct()->getData(), $observer->getEvent()->getProduct()->getOrigData());

    $changeLog = '';
    $oldLog = '';
    $countChanges = 1;
    foreach ($dataModified as $key => $value) {
        if (in_array($key, ['updated_at', 'required_options', 'has_options', 'quantity_and_stock_status'])) continue;
        if (is_array($value)) break;
        $changeLog .= $countChanges . ') Attribute: "' . $key . '", Value: ' . $value . ' <br />';
        $oldLog .= $countChanges . ') Attribute: "' . $key . '", Value: ' . $observer->getEvent()->getProduct()->getOrigData($key) . ' <br />';
        $countChanges++;
    }

      $productId = $observer->getEvent()->getProduct()->getId();
      $easySyncCollectionFactory = $this->easySyncCollectionFactory->create();
        //     ->getCollection()
        //   ->addFieldToFilter('dataScope', 'product')
        //   ->addFieldToFilter('dataID', array('eq' => $productId))
        //   ->getFirstItem();
      if ($easySyncCollectionFactory->hasData()) {
          $updatedCustomer = $this->easySyncCollectionFactory->create()->load($easySyncCollectionFactory['data_sync_id']);
          $updatedCustomer->setData('UpdateFlag','1');
          $updatedCustomer->setData('UpdateDate',$this->storeDate->gmtDate());
          $serverAddress = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
          $updatedCustomer->setData('ServerLocation', $serverAddress);
          $updatedCustomer->setData('LogType', 'Send');
          if (!empty($updatedCustomer->getData('ChangeLog'))) {
            $changeLog = $updatedCustomer->getData('ChangeLog') . $changeLog;
          }
          if (!empty($updatedCustomer->getData('OldValue'))) {
            $oldLog = $updatedCustomer->getData('OldValue') . $oldLog;
          } 
          $updatedCustomer->setData('ChangeLog', $changeLog);
          $updatedCustomer->setData('OldValue', $oldLog);
          $updatedCustomer->save();
      } else {
          
          if (!empty($changeLog)) {
            $model = $this->easySyncCollectionFactory->create();
            $model->addData([
                "dataID" => $productId,
                // "ServerLocation" => $_SERVER['SERVER_ADDR'],
                "dataScope" => 'product',
                "LogType" => ($this->helper->getDataMode() == "receiver") ? 'Receive' : 'Send',
                "ChangeLog" => $changeLog,
                "OldValue" => $oldLog,
                "UpdateDate" => $this->storeDate->gmtDate(),
                "UpdateFlag" => ($this->helper->getDataMode() == "receiver") ? 0 : 1
            ]);
            $model->save();
          }
          
      }
    }
}
