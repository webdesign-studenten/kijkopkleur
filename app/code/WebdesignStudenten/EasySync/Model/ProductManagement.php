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

class ProductManagement implements \WebdesignStudenten\EasySync\Api\ProductManagementInterface
{
    /**
     * Newsletter registry.
     *
     * @var \Magento\Catalog\Model\productRepository
     */
    protected $productRepository;

    public function __construct(
        \Magento\Catalog\Model\Product $productRepository
    ) {
        $this->productRepository = $productRepository;
    }
    /**
     * {@inheritdoc}
     */
    public function getProduct($prodID)
    {
        $product = $this->productRepository->load($prodID);
        $productData = $product->getData();
        $productData['website_ids'] = $product->getWebsiteIds();
        return json_encode($productData);
    }
}
