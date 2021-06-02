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

class UpdatedSalesManagement implements \WebdesignStudenten\EasySync\Api\UpdatedSalesManagementInterface
{
    /**
     * Updated Data[po0o85r  n23] registry.
     *
     * @var \WebdesignStudenten\EasySync\Model\DataIDFactory
     */
    protected $_dataFactory;

    public function __construct(
        \WebdesignStudenten\EasySync\Model\DataIDFactory  $dataFactory
    ) {
        $this->_dataFactory   = $dataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedSales()
    {
        $updatedProductModel = $this->_dataFactory->create()->getCollection()->addFieldToFilter('dataScope', 'sales')->addFieldToFilter('UpdateFlag', '1');
        return $updatedProductModel->getData();
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedSalesFlag($syncId)
    {
        $updatedCustomerModel = $this->_dataFactory->create()->load($syncId);
        $updatedCustomerModel->setData('UpdateFlag', '0');
        $saveData = $updatedCustomerModel->save();
        if($saveData){
            return true;
        } else {
            return false;
        }
    }
}
