<?php
/**
 * Admin can sync customer, shipments, shipment, cart, newsletter subscribers, wishlist etc.
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

class ShipmentManagement implements \WebdesignStudenten\EasySync\Api\ShipmentManagementInterface
{
  /**
   * Newsletter registry.
   *
   * @var \Magento\Catalog\Model\shipmentRepository
   */
  protected $shipmentRepository;

  public function __construct(
      \Magento\Sales\Model\Order\Shipment $shipmentRepository
  ) {
      $this->shipmentRepository = $shipmentRepository;
  }
  /**
   * {@inheritdoc}
   */
  public function getShipment($shipmentID)
  {
      $shipment = $this->shipmentRepository->load($shipmentID);
      $shipmentData['shipment_info'] = $shipment->getData();
      foreach ($shipment->getAllItems() as $item) {
        $shipmentData['shipment_items'][] = $item->getData();
      }
      foreach ($shipment->getComments() as $comment) {
        $shipmentData['shipment_comments'][] = $comment->getData();
      }
      return json_encode($shipmentData);
  }
}
