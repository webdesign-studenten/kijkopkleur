<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */

namespace RLTSquare\ColorSwatch\Controller\Index;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use RLTSquare\ColorSwatch\Block\ColorSwatch;

/**
 * Class Groupsearch
 * @package RLTSquare\ColorSwatch\Controller\Index
 */
class Groupsearch extends Action
{
    /**
     * @var
     */
    protected $productModel;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch
     */
    protected $colorSwatch;
    /**
     * @var
     */
    protected $resultPageFactory;
    /**
     * @var FormKey
     */
    protected $formKey;
    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var Product
     */
    protected $product;
    /**
     * @var ColorSwatch
     */
    protected $blockColorSwatch;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Session $customerSession
     * @param FormKey $formKey
     * @param Cart $cart
     * @param Product $product
     */

    public function __construct(
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $colorSwatch,
        ColorSwatch $blockColorSwatch,
        Context $context,
        FormKey $formKey,
        Cart $cart,
        Product $product
    ) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->blockColorSwatch = $blockColorSwatch;
        $this->colorSwatch = $colorSwatch;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $productdata = [];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $data = $this->getRequest()->getParams();
        $productdata['product_id'] = $data['product_id'];
        $user_string = $data['user_string'];
        if ($user_string) {
            $attributeid = $this->colorSwatch->getAttributeId('brands');
            $brands = $this->colorSwatch->getBrandValue($attributeid, $data['product_id']);
            if ($brands) {
                $brands = explode(",", $brands);
                if (!empty($brands)) {
                    $arraycount = 0;
                    foreach ($brands as $key => $brandid) {
                        $brandLabel = $this->colorSwatch->getOptionValues($brandid);
                        $brandLabel = $brandLabel['0']['value'];
                        $user_string = $data['user_string'];
                        for ($i = 0; $i < strlen($data['user_string']); $i++) {
                            if (strcasecmp($brandLabel[$i], $user_string[$i]) == 0) {
                                $match = true;
                            } else {
                                $match = false;
                                break;
                            }
                        }
                        if ($match) {
                            $productdata['brands'][$arraycount]['brand_id'] = $brandid;
                            $productdata['brands'][$arraycount]['name'] = $brandLabel;
                            $arraycount++;
                        }
                    }
                }
            }
        } else {
            $productdata['show_all'] = true;
        }
        $resultJson->setData($productdata);
        return $resultJson;
    }

}
