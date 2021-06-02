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

namespace WebdesignStudenten\EasySync\Model;

use WebdesignStudenten\EasySync\Api\Data\DataIDInterface;
use WebdesignStudenten\EasySync\Api\Data\DataIDInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class DataID extends \Magento\Framework\Model\AbstractModel
{

    protected $dataidDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'webdesignstudenten_easysync_data_sync';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DataIDInterfaceFactory $dataidDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \WebdesignStudenten\EasySync\Model\ResourceModel\DataID $resource
     * @param \WebdesignStudenten\EasySync\Model\ResourceModel\DataID\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        DataIDInterfaceFactory $dataidDataFactory,
        DataObjectHelper $dataObjectHelper,
        \WebdesignStudenten\EasySync\Model\ResourceModel\DataID $resource,
        \WebdesignStudenten\EasySync\Model\ResourceModel\DataID\Collection $resourceCollection,
        array $data = []
    ) {
        $this->dataidDataFactory = $dataidDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve dataid model with dataid data
     * @return DataIDInterface
     */
    public function getDataModel()
    {
        $dataidData = $this->getData();
        
        $dataidDataObject = $this->dataidDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataidDataObject,
            $dataidData,
            DataIDInterface::class
        );
        
        return $dataidDataObject;
    }
    
//    public function __construct(
//        \Magento\Framework\Model\Context $context,
//        \Magento\Framework\Registry $registry,
//        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
//        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
//        array $data = []
//    ) {
//        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
//    }
//
//    /**
//     * @return void
//     */
//    public function _construct()
//    {
//        $this->_init('WebdesignStudenten\EasySync\Model\ResourceModel\DataID');
//    }

}
