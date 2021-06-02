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

namespace WebdesignStudenten\EasySync\Api\Data;

interface DataIDSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get dataID list.
     * @return \WebdesignStudenten\EasySync\Api\Data\DataIDInterface[]
     */
    public function getItems();

    /**
     * Set dataID list.
     * @param \WebdesignStudenten\EasySync\Api\Data\DataIDInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
