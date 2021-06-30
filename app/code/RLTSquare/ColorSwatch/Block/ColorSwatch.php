<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Block;

/**
 * Class ColorSwatch
 * @package RLTSquare\ColorSwatch\Block
 */
class ColorSwatch extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CardType
     */
    private $card_type;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch
     */
    protected $colorswatch;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $image;

    /**
     * ColorSwatch constructor.
     * @param \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $colorswatch
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Image $image
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $colorswatch,
        \Magento\Catalog\Model\Product $product,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Image $image,
        \Magento\Framework\View\Element\Template\Context $context
    )
    {
        $this->product = $product;
        $this->registry = $registry;
        $this->colorswatch = $colorswatch;
        $this->eavConfig = $eavConfig;
        $this->image = $image;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function isConfigureable()
    {
        $product = $this->registry->registry('current_product');
        return $product->getTypeId();
    }

    /**
     * @return mixed
     */
    public function getChildsProducts()
    {
        $product = $this->registry->registry('current_product');
        $_children = $product->getTypeInstance()->getUsedProducts($product);
        return $_children;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getProductImageUrl($product)
    {
        $attributeid  = $this->colorswatch->getAttributeId('color_swatch_image');
        $image  = $this->colorswatch->getImageValue($attributeid,$product->getId());
        if( !empty($image) ) {
            $path = $image;
        } else {
            $path = $product->getSmallImage();
        }
        $imageUrl = $this->image->init($product, 'product_page_image_small')
            ->setImageFile($path) // image,small_image,thumbnail
            ->resize(380)
            ->getUrl();
        return $imageUrl;
    }

    /**
     * @param $id
     * @return array
     */
    public function getAttachedProducts($id)
    {
        $linktypeid = $this->colorswatch->getIdByCode("customlinked");
        $productids = $this->colorswatch->getAttachedProductIds($id,$linktypeid);
        return $productids;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getProduct($id)
    {
        return $this->product->load($id);
    }

    /**
     * @param $braindid
     * @return array
     */
    public function getRelatedProducts($braindid)
    {
        $colorswatchid = $this->colorswatch->getColorSwatchId($braindid);
        $ids = [];
        if(isset($colorswatchid['0']['colorswatch_id'])){
            $colorswatchid = $colorswatchid['0']['colorswatch_id'];
            $productids = $this->colorswatch->getProductIds($colorswatchid);
            $ids = [];
            foreach ($productids as $key => $value) {
                $ids[]=$value['product_id'];
            }
            return $ids;
        }
        return $ids;
    }

    /**
     * @param $id
     * @return array
     */
    public function getBrands($id)
    {
        $attributeid  = $this->colorswatch->getAttributeId('brands');
        $brands  = $this->colorswatch->getBrandValue($attributeid,$id);
        if(!empty($brands)) {
            $brands = explode(",", $brands);
            $attribute = $this->eavConfig->getAttribute('catalog_product', 'company_shades');
            $options = $attribute->getSource()->getAllOptions();
            $catagoryList = [];
            foreach ($options as $option) {
                if(in_array($option['value'], $brands) ) {
                    $catagoryList[$option['value']] = $option['label'];
                }
            }
            return $catagoryList;
        } else {
            return [];
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function getBackgroundColor($id)
    {
        $attributeid  = $this->colorswatch->getAttributeId('background_color');
        $color  = $this->colorswatch->getColorValue($attributeid,$id);
        $data = [];
        $data['enable'] = false;
        if(!empty($color)) {
            $data['color'] = $color;
            $data['enable'] = true;
            return $data;
        }
        return $data;
    }

    public function EnableColorSwatch()
    {
        $product = $this->registry->registry('current_product');
        return $product->getData('enable_Colorswatch');
    }



}
