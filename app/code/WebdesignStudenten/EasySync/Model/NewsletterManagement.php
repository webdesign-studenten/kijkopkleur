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

class NewsletterManagement implements \WebdesignStudenten\EasySync\Api\NewsletterManagementInterface
{
    /**
     * Newsletter registry.
     *
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $newsletterSubscriber;
 
    public function __construct(
        \Magento\Newsletter\Model\Subscriber $newsletterSubscriber
    ) {
        $this->newsletterSubscriber = $newsletterSubscriber;
    }
    /**
     * {@inheritdoc}
     */
    public function getNewsletter($newsletterID)
    {
        $newsletter = $this->newsletterSubscriber->load($newsletterID);
        return json_encode($newsletter->getData());
    }
}
