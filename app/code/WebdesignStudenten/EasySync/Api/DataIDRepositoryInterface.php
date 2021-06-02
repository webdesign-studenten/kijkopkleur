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

namespace WebdesignStudenten\EasySync\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface DataIDRepositoryInterface
{

    /**
     * Save dataID
     * @param \WebdesignStudenten\EasySync\Api\Data\DataIDInterface $dataID
     * @return \WebdesignStudenten\EasySync\Api\Data\DataIDInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \WebdesignStudenten\EasySync\Api\Data\DataIDInterface $dataID
    );

    /**
     * Retrieve dataID
     * @param string $dataidId
     * @return \WebdesignStudenten\EasySync\Api\Data\DataIDInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataidId);

    /**
     * Retrieve dataID matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \WebdesignStudenten\EasySync\Api\Data\DataIDSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete dataID
     * @param \WebdesignStudenten\EasySync\Api\Data\DataIDInterface $dataID
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \WebdesignStudenten\EasySync\Api\Data\DataIDInterface $dataID
    );

    /**
     * Delete dataID by ID
     * @param string $dataidId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dataidId);
}
