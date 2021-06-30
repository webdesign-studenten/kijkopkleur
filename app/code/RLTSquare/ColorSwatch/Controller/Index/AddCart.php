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
 * Class AddCart
 * @package RLTSquare\ColorSwatch\Controller\Index
 */
class AddCart extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\App\Action\Context $context,
        FormKey $formKey,
        Cart $cart,
        Product $product
    ) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->product = $product;
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
        $_product = $this->product->load($data['product_id']);
        //INSERIMENTO NEL CARRELLO PER PROD SEMPLICE
        if($_product->getTypeId() == 'simple'){
            //AGGIUNTA NEL CARRELLO PROD SINGOLO
            $params = $data['qty'];
            $this->cart->addProduct($_product, $params);
            $this->cart->save();
        }
        $productdata['success']=true;
        $resultJson->setData($productdata);
        return $resultJson;
    }
}
