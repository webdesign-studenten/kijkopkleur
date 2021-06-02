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

namespace WebdesignStudenten\EasySync\Observer\Newsletter;

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

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->getDataMode() == "receiver") return;

        $subscriberId = $observer->getEvent()->getSubscriber()->getId();

        $easySyncCollectionFactory = $this->easySyncCollectionFactory->create()->getCollection()
            ->addFieldToFilter('dataScope', 'newsletter')
            ->addFieldToFilter('dataID', array('eq' => $subscriberId))
            ->getFirstItem();
        if($easySyncCollectionFactory->hasData()){
            $updatedCustomer = $this->easySyncCollectionFactory->create()->load($easySyncCollectionFactory['data_sync_id']);
            $updatedCustomer->setData('UpdateFlag','1');
            $updatedCustomer->setData('UpdateDate',$this->storeDate->gmtDate());
            $updatedCustomer->setData('ServerLocation',$_SERVER['SERVER_ADDR']);
            $updatedCustomer->save();
        } else {
            $model = $this->easySyncCollectionFactory->create();
            $model->addData([
                "dataID" => $subscriberId,
                "ServerLocation" => $_SERVER['SERVER_ADDR'],
                "dataScope" => 'newsletter',
                "UpdateDate" => $this->storeDate->gmtDate(),
                "UpdateFlag" => 1
			      ]);
            $model->save();
        }

    }
}
