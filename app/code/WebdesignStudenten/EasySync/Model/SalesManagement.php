<?php
/**
 * Admin can sync customer, saless, sales, cart, newsletter subscribers, wishlist etc.
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

class SalesManagement implements \WebdesignStudenten\EasySync\Api\SalesManagementInterface
{
  /**
   * Newsletter registry.
   *
   * @var \Magento\Catalog\Model\salesRepository
   */
  protected $salesRepository;

  public function __construct(
      \Magento\Sales\Api\Data\OrderInterfaceFactory $salesRepository
  ) {
      $this->salesRepository = $salesRepository;
  }
  /**
   * {@inheritdoc}
   */
  public function getSales($incrementID)
  {
      $sales = $this->salesRepository->create()->loadByIncrementId($incrementID);
      $orderData['order_info'] = $sales->getData();
    //   foreach ($sales->getAllItems() as $item) {
    //     $orderData['order_items'][] = $item->getData();
    //   }
      $orderData['payment_info'] = $sales->getPayment()->getData();
      $orderData['billing_address'] = $sales->getBillingAddress()->getData();
      $orderData['shipping_address'] = $sales->getShippingAddress()->getData();
      return json_encode($orderData);
  }
}
