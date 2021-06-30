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

/**
 * Class Product
 * @package RLTSquare\ColorSwatch\Controller\Index
 */
class Product extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch
     */
    protected $colorSwatch;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $image;

    /**
     * Product constructor.
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Catalog\Helper\Image $image
     * @param \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $colorSwatch
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Helper\Image $image,
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $colorSwatch,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->productModel = $productModel;
        $this->colorSwatch = $colorSwatch;
        $this->image = $image;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $productdata=[];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $productid = $this->getRequest()->getParam('product_id');
        $productdata['product_id'] = $productid;
        $product = $this->productModel->load($productid);
        $productdata['product_name'] = $product->getName();
        $imageUrl = $this->image->init($product, 'product_page_image_small')
            ->setImageFile($product->getColorSwatchImage()) // image,small_image,thumbnail
            ->resize(380)
            ->getUrl();
        $productdata['imageurl'] = $imageUrl;
        if (!empty($productid)) {
            $productdata['buttonshow'] = true;
        } else {
            $productdata['buttonshow'] = false;
        }
        $resultJson->setData($productdata);
        return $resultJson;
    }

}
