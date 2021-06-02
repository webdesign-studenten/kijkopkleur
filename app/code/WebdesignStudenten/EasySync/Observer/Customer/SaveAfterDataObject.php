<?php
/**
 * Admin can sync customer, products, sales, cart, newsletter subscribers, wishlist etc.
 * Copyright (C) 2019  
 * 
 * This file is part of WebdesignStudenten/EasySync.
 * 
 * WebdesignStudenten/EasySync is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace WebdesignStudenten\EasySync\Observer\Customer;
use Magento\Framework\Event\ObserverInterface;

class SaveAfterDataObject implements ObserverInterface
{   
    protected $logger;
    protected $storeDate;
    protected $helper;
    protected $easySyncCollectionFactory;
    protected $_storeManager;
    public function __construct (
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \WebdesignStudenten\EasySync\Model\DataIDFactory $easySyncCollectionFactory
    ) {
        $this->logger = $logger;
        $this->storeDate = $date;
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
        $this->easySyncCollectionFactory = $easySyncCollectionFactory;
	 }

     private function compare_objects($a, $b) {
         $diff = array();
         foreach(get_class_methods($a) as $attr) {
            if (in_array($attr, ['getCustomAttribute', 'getUpdatedAt', 'getExtensionAttributes' , 'getAddresses']) || $attr[0] != 'g') continue;
            
            $aVal = $a->$attr();
            $bVal = $b->$attr();
            if ($aVal !== $bVal) {
                $diff[] = $attr;
            }
         }
        return  $diff;
        
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // if ($this->helper->getDataMode() == "receiver") return;
		
        $customer =  $observer->getEvent()->getCustomerDataObject();
        $customerOrigData =  $observer->getEvent()->getOrigCustomerDataObject();
        
        $dataModified = $this->compare_objects($customer, $customerOrigData);
        $changeLog = '';
        $oldLog = '';
        $countChanges = 1;
        foreach ($dataModified as $value) {
            // if (in_array($key, ['updated_at', 'required_options', 'has_options', 'quantity_and_stock_status'])) continue;
            // if (is_array($value)) break;
            $changeLog .= $countChanges . ') Attribute: "' . substr($value, 3) . '", Value: ' . $customer->$value() . ' <br />';
            $oldLog .= $countChanges . ') Attribute: "' . substr($value, 3) . '", Value: ' . $customerOrigData->$value() . ' <br />';
            $countChanges++;
        }

        $easySyncCollectionFactory = $this->easySyncCollectionFactory->create();
            // ->getCollection()
            // ->addFieldToFilter('dataScope', 'customer')
            // ->addFieldToFilter('dataID', array('eq' => $customer->getId()))
            // ->getFirstItem();
        if($easySyncCollectionFactory->hasData()){
            $updatedCustomer = $this->easySyncCollectionFactory->create()->load($easySyncCollectionFactory['data_sync_id']);
            $updatedCustomer->setData('UpdateFlag','1');
            $updatedCustomer->setData('UpdateDate',$this->storeDate->gmtDate());
            $updatedCustomer->setData('ServerLocation',$_SERVER['SERVER_ADDR']);
            $updatedCustomer->save();
        }else{
            $model = $this->easySyncCollectionFactory->create();
            $model->addData([
                "dataID" => $customer->getId(),
//                "ServerLocation" => $this->_storeManager->getStore()->getBaseUrl(),
                "ServerLocation" => $_SERVER['SERVER_ADDR'],
                "dataScope" => 'customer',
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
