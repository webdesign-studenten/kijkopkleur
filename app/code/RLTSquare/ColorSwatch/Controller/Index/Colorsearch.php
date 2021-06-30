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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
/**
 * Class Colorsearch
 * @package RLTSquare\ColorSwatch\Controller\Index
 */
class Colorsearch extends \Magento\Framework\App\Action\Action
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
     * @var \RLTSquare\ColorSwatch\Block\ColorSwatch
     */
    protected $blockColorSwatch;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param FormKey $formKey
     * @param Cart $cart
     * @param Product $product
     */

    public function __construct(
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $colorSwatch,
        \RLTSquare\ColorSwatch\Block\ColorSwatch $blockColorSwatch,
        \Magento\Framework\App\Action\Context $context,
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
        $productdata=[];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $data = $this->getRequest()->getParams();
        $productdata['product_id']=$data['product_id'];
        $attributeid  = $this->colorSwatch->getAttributeId('brands');
        $brands  = $this->colorSwatch->getBrandValue($attributeid,$data['product_id']);
        if($brands) {
            $brands = explode(",", $brands);
            $productdata['braindids'] = $brands;
            $colorSwatchids = $this->colorSwatch->getColorSwatchIds($brands);
            if(!empty($colorSwatchids)) {
                $productids = $this->colorSwatch->getRelatedProducts($colorSwatchids);
                $arraycount = 0;
                foreach ($productids as $key => $productid) {
                    $_product = $this->product->load($productid['product_id']);
                    $productname = $_product->getName();
                    $user_string = $data['user_string'];
                    for ($i=0;  $i < strlen($data['user_string']) ; $i++)  {
                        if(strcasecmp($productname[$i],$user_string[$i] ) == 0) {
                            $match = true;
                        } else {
                            $match = false;
                            break;
                        }
                    }
                    if($match) {
                        $color = $this->blockColorSwatch->getBackgroundColor($productid['product_id']);
                        $productdata['products'][$arraycount]['product_id']=$productid['product_id'];
                        $productdata['products'][$arraycount]['name']=$_product->getName();
                        if( $color['enable'] ) {
                            $productdata['products'][$arraycount]['color']=$color['color'];
                        } else {
                            $imgurl = $this->blockColorSwatch->getProductImageUrl($_product);
                            $productdata['products'][$arraycount]['imgurl']=$imgurl;
                        }
                        $arraycount++;
                    }
                }
            }
        }
        $resultJson->setData($productdata);
        return $resultJson;
    }
}
