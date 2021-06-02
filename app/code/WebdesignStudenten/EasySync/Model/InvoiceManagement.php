<?php
/**
 * Admin can sync customer, invoices, invoice, cart, newsletter subscribers, wishlist etc.
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

class InvoiceManagement implements \WebdesignStudenten\EasySync\Api\InvoiceManagementInterface
{
  /**
   * Newsletter registry.
   *
   * @var \Magento\Catalog\Model\invoiceRepository
   */
  protected $invoiceRepository;

  public function __construct(
      \Magento\Sales\Model\Order\Invoice $invoiceRepository
  ) {
      $this->invoiceRepository = $invoiceRepository;
  }
  /**
   * {@inheritdoc}
   */
  public function getInvoice($invoiceID)
  {
      $invoice = $this->invoiceRepository->load($invoiceID);
      $invoiceData['invoice_info'] = $invoice->getData();
    //   $invoiceData['invoice_items'] = $invoice->getAllItems()
      foreach ($invoice->getAllItems() as $item) {
        $invoiceData['invoice_items'][] = $item->getData();
      }
      foreach ($invoice->getComments() as $comment) {
        $invoiceData['invoice_comments'][] = $comment->getData();
      }
      // $orderData['payment_info'] = $invoice->getPayment()->getData();
      // $orderData['billing_address'] = $invoice->getBillingAddress()->getData();
      // $orderData['shipping_address'] = $invoice->getShippingAddress()->getData();
      return json_encode($invoiceData);
  }
}
