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

class AddressSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $logger;
    protected $storeDate;
    protected $helper;
    protected $easySyncCollectionFactory;
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \WebdesignStudenten\EasySync\Model\DataIDFactory $easySyncCollectionFactory
    ){
        $this->logger = $logger;
        $this->storeDate = $date;
        $this->helper = $helper;
        $this->easySyncCollectionFactory = $easySyncCollectionFactory;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        // if ($this->helper->getDataMode() == "receiver") return;
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        $dataModified = $this->helper->getRecursiveArrayDiff($customerAddress->getData(), $customerAddress->getOrigData());

        $changeLog = '';
        $oldLog = '';
        $countChanges = 1;
        foreach ($dataModified as $key => $value) {
            if (in_array($key, ['updated_at', 'required_options', 'has_options', 'quantity_and_stock_status'])) continue;
            if (is_array($value)) break;
            $changeLog .= $countChanges . ') Attribute: "' . $key . '", Value: ' . $value . ' <br />';
            $oldLog .= $countChanges . ') Attribute: "' . $key . '", Value: ' . $customerAddress->getOrigData($key) . ' <br />';
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
            if (!empty($_SERVER['SERVER_ADDR'])) {
                $updatedCustomer->setData('ServerLocation',$_SERVER['SERVER_ADDR']);
            }
            $updatedCustomer->save();
        }else{			
            $model = $this->easySyncCollectionFactory->create();
            $model->addData([
                "dataID" => $customer->getId(),
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
