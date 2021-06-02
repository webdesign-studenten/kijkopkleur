<?php
namespace WebdesignStudenten\EasySync\Observer\Category;

class CategorySaveAfter implements \Magento\Framework\Event\ObserverInterface
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

    // if ($this->helper->getDataMode() == "receiver") return;

    $dataModified = $this->helper->getRecursiveArrayDiff($observer->getEvent()->getCategory()->getData(), $observer->getEvent()->getCategory()->getOrigData());

    $changeLog = '';
    $oldLog = '';
    $countChanges = 1;
    foreach ($dataModified as $key => $value) {
        if (in_array($key, ['updated_at', 'required_options', 'has_options', 'quantity_and_stock_status'])) continue;
        if (is_array($value)) break;
        $changeLog .= $countChanges . ') Attribute: "' . $key . '", Value: ' . $value . ' <br />';
        $oldLog .= $countChanges . ') Attribute: "' . $key . '", Value: ' . $observer->getEvent()->getCategory()->getOrigData($key) . ' <br />';
        $countChanges++;
    }

      $categoryId = $observer->getEvent()->getCategory()->getId();
      $easySyncCollectionFactory = $this->easySyncCollectionFactory->create();
          // ->getCollection()
          // ->addFieldToFilter('dataScope', 'category')
          // ->addFieldToFilter('dataID', array('eq' => $categoryId))
          // ->getFirstItem();
      if ($easySyncCollectionFactory->hasData()) {
          $updatedCustomer = $this->easySyncCollectionFactory->create()->load($easySyncCollectionFactory['data_sync_id']);
          $updatedCustomer->setData('UpdateFlag','1');
          $updatedCustomer->setData('UpdateDate',$this->storeDate->gmtDate());
          $updatedCustomer->setData('ServerLocation',$_SERVER['SERVER_ADDR']);
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
          $model = $this->easySyncCollectionFactory->create();
          $model->addData([
              "dataID" => $categoryId,
              "ServerLocation" => $_SERVER['SERVER_ADDR'],
              "dataScope" => 'category',
              "LogType" => 'Send',
              "ChangeLog" => $changeLog,
              "OldValue" => $oldLog,
              "UpdateDate" => $this->storeDate->gmtDate(),
              "UpdateFlag" => 1
          ]);
          $model->save();
      }
    }
}
