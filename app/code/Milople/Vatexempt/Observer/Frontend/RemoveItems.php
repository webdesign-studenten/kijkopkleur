<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_VATExempt
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento-extensions/vat-exempt-m2.html
*
**/
namespace Milople\Vatexempt\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RemoveItems
 */
class RemoveItems implements ObserverInterface
{
      	public function __construct(
         \Psr\Log\LoggerInterface $logger
        ) {
            $this->logger = $logger;
       }
      # Set the class id to original as it is now removed.
      public function execute(Observer $observer){
        
          $item = $observer->getQuoteItem();
        	$item->getProduct()->setTaxClassId(2);
      }
}