<?php
/**
 * Admin can sync customer, quotes, quote, cart, newsletter subscribers, wishlist etc.
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

class QuoteManagement implements \WebdesignStudenten\EasySync\Api\QuoteManagementInterface
{
  /**
   * Cart registry.
   *
   * @var \Magento\Quote\Api\CartRepositoryInterface    
   */
  protected $quoteRepository;

  public function __construct(
    \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
  ) {
    $this->quoteRepository = $quoteRepository;
  }
  /**
   * {@inheritdoc}
   */
  public function getQuotes($quoteID)
  {
      $quote = $this->quoteRepository->get($quoteID);
      $quoteData['quote_info'] = $quote->getData();
      foreach ($quote->getAllItems() as $item) {
        $quoteData['quote_items'][] = $item->getData();
      }
      $quoteData['payment_info'] = $quote->getPayment()->getData();
      $quoteData['billing_address'] = $quote->getBillingAddress()->getData();
      $quoteData['shipping_address'] = $quote->getShippingAddress()->getData();
      return json_encode($quoteData);
  }
}
