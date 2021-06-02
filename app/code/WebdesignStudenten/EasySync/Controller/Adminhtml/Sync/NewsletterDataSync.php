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

namespace WebdesignStudenten\EasySync\Controller\Adminhtml\Sync;

class NewsletterDataSync extends \Magento\Backend\App\Action
{

    protected $logger;
    protected $helper;
    private $newsletterFactory;


    /**
     * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Newsletter\Model\Subscriber $newsletterFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->newsletterFactory = $newsletterFactory;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $updatedNewsletterAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedNewsletter';
        $sXML = $this->helper->getApiData($updatedNewsletterAPIUrl);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($sXML->item as $oEntry) {
            $newsletterAPIUrl = 'rest/V1/webdesignstudenten-easysync/newsletter/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($newsletterAPIUrl);
            if ($sXML == false) {
                $this->helper->setApiData($updatedNewsletterAPIUrl . '/' . $oEntry->data_sync_id);
                continue;
            }
            $newsletterData = json_decode($sXML);
            $newsletterDataArray = json_decode(json_encode($newsletterData), true);
            if (empty($newsletterDataArray)) {
                $this->helper->setApiData($updatedNewsletterAPIUrl . '/' . $oEntry->data_sync_id);
                continue;
            } 
            $objectManager->create('Magento\Newsletter\Model\Subscriber')->subscribe($newsletterDataArray['subscriber_email']);
            if ($subscriber = $this->newsletterFactory->loadByEmail($newsletterDataArray['subscriber_email'])) {
              $subscriber->setStoreId($newsletterDataArray['store_id']);
              if ($newsletterDataArray['subscriber_status'] == 3) {
                $subscriber->unsubscribe();
              }
              $subscriber->save();
              $this->helper->setApiData($updatedNewsletterAPIUrl . '/' . $oEntry->data_sync_id);
            }
        }
    }
}
