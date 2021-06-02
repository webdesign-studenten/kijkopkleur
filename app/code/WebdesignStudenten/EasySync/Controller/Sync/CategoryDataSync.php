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

namespace WebdesignStudenten\EasySync\Cron;

class CategoryDataSync
{

    protected $logger;
    protected $helper;
    protected $categoryFactory;
    protected $categoryRepository;


    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Catalog\Model\Category $categoryFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $updatedCategoryAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedCategories';
        $sXML = $this->helper->getApiData($updatedCategoryAPIUrl);
        foreach ($sXML->item as $oEntry) {
            $categoryAPIUrl = 'rest/V1/webdesignstudenten-easysync/category/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($categoryAPIUrl);
            $categoryData = json_decode($sXML);
            $categoryDataArray = json_decode(json_encode($categoryData), true);

            if ($category = $this->categoryFactory->load($categoryDataArray['entity_id'])) {
              foreach ($categoryDataArray as $key => $value) {
                $category->setData($key, $value);
              }
              $category->save();

            } else {
              $category = $this->categoryFactory->create();
                foreach ($categoryDataArray as $key => $value) {
                  $category->setData($key, $value);
                }
                $category->save();
            }
            $this->helper->setApiData($updatedCategoryAPIUrl . '/' . $oEntry->data_sync_id);
        }
    }
}
