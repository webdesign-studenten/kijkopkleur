<?php
/**
 * Admin can sync customer, products, sales, cart, Product subscribers, wishlist etc.
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

class ProductDataSync
{

    protected $logger;
    protected $helper;
    protected $productFactory;


    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->productFactory = $productFactory;
    }

    private function getCategoryLinkManagement() {

        $this->categoryLinkManagement = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Api\CategoryLinkManagementInterface');
        return $this->categoryLinkManagement;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
      if (!$this->helper->isEnabled()) return;
        $updatedProductAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedProducts';
        $sXML = $this->helper->getApiData($updatedProductAPIUrl);
        foreach ($sXML->item as $oEntry) {
            $ProductAPIUrl = 'rest/V1/webdesignstudenten-easysync/product/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($ProductAPIUrl);
            $ProductData = json_decode($sXML);
            $ProductDataArray = json_decode(json_encode($ProductData), true);

            if ($product = $this->productFactory->create()->load($ProductDataArray['entity_id'])) {
              $catLink = $ProductDataArray['category_ids'];
              // unset($ProductDataArray['category_ids']);
              unset($ProductDataArray['extension_attributes']);
              unset($ProductDataArray['media_gallery']);
              unset($ProductDataArray['options']);
              //print_r($ProductDataArray); die;
              // print_r(get_class_methods($product));
              foreach ($ProductDataArray as $key => $value) {
                $product->setData($key, $value);
              }
              $product->save();

              $this->getCategoryLinkManagement()->assignProductToCategories(
                $product->getSku(),
                $catLink
              );


            } else {
              $catLink = $ProductDataArray['category_ids'];
              // unset($ProductDataArray['category_ids']);
              unset($ProductDataArray['extension_attributes']);
              unset($ProductDataArray['media_gallery']);
              unset($ProductDataArray['options']);
              $product = $this->productFactory->create();
                foreach ($ProductDataArray as $key => $value) {
                  $product->setData($key, $value);
                }
                $product->save();

                $this->getCategoryLinkManagement()->assignProductToCategories(
                  $product->getSku(),
                  $catLink
                );
            }

            $this->helper->setApiData($updatedProductAPIUrl . '/' . $oEntry->data_sync_id);
        }
    }
}
