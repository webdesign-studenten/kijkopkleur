<?php
/**
 * MageVision Admin Name Order Comment Extension
 *
 * @category     MageVision
 * @package      MageVision_AdminNameOrderComment
 * @author       MageVision Team
 * @copyright    Copyright (c) 2018 MageVision (http://www.magevision.com)
 * @license      LICENSE_MV.txt or http://www.magevision.com/license-agreement/
 */
namespace MageVision\CartProductComment\Observer;

use Magento\Framework\Event\ObserverInterface;
use MageVision\CartProductComment\Helper\Data as Helper;
use Magento\Checkout\Model\Cart as CheckoutCart;

class CheckoutCartAddProductCompleteObserver implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var CheckoutCart
     */
    protected $cart;

    /**
     * @param Helper $helper
     * @param CheckoutCart $cart
     */
    public function __construct(
        Helper $helper,
        CheckoutCart $cart
    ) {
        $this->helper = $helper;
        $this->cart = $cart;
    }

    /**
     * Add product comment
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();
        if ($this->helper->isEnabled()) {
            $quoteItem = $this->cart->getQuote()->getItemByProduct($product);
            if ($quoteItem) {
                try {
                    if ($comment = $request->getParam('product_'.$product->getId().'_comment')){
                        $quoteItem->setComment($comment);
                        $quoteItem->save();
                    }
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save comment'), $e);
                }
            }
        }
    }
}
