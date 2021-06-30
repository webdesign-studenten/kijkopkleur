<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Helper;

/**
 * Class Data
 * @package RLTSquare\ColorSwatch\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $attributeValues;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
     */
    protected $tableFactory;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory
     */
    protected $optionFactory;
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    public $eavSetupFactory;
    /**
     * @var
     */
    public $eavAttribute;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch
     */
    public $resourceModelColorSwatch;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    public $attribute;
    /**
     * @param \Magento\Framework\App\Helper\Context   $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $resourceModelColorSwatch,
        \Magento\Eav\Model\Entity\Attribute $attribute,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttribute,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->resourceModelColorSwatch = $resourceModelColorSwatch;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->attribute = $attribute;
    }
    /**
     * get products tab Url in admin
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('colorswatch/index/products', ['_current' => true]);
    }

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode);
    }
    /**
     * Find or create a matching attribute option
     *
     * @param string $attributeCode Attribute the option should exist in
     * @param string $label Label to find or add
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createOrGetId($attributeCode, $label)
    {
        if (strlen($label) < 1) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Label for %1 must not be empty.', $attributeCode)
            );
        }
        // Does it already exist?
        $optionId = $this->getOptionId($attributeCode, $label);
        if (!$optionId) {
            $attributeCode = $attributeCode; /* on size, 90 on color ... */
            $languageValues[0] = $label;
            $languageValues[1] = $label;
            $attibuteid = $this->resourceModelColorSwatch->getAttributeId($attributeCode);
            $attr = $this->attribute->load($attibuteid);
            $option = [];
            $option['value'][$languageValues[0]] = $languageValues;
            $attr->addData(array('option' => $option));
            $attr->save();
            $optionId = $this->getOptionId($attributeCode, $label, true);
        }
        return $optionId;
    }
    /**
     * Find the ID of an option matching $label, if any.
     *
     * @param string $attributeCode Attribute code
     * @param string $label Label to find
     * @param bool $force If true, will fetch the options even if they're already cached.
     * @return int|false
     */
    public function getOptionId($attributeCode, $label, $force = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);
        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];
            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.
            // * @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);
            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }
        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }
        // Return false if does not exist
        return false;
    }
    // public function getOptionId($attributeCode,$label)
    // {


    //    $attributeid = $this->resourceModelColorSwatch->getAttributeId($attributeCode);
    //    $OptionIds = $this->resourceModelColorSwatch->getOptionIds($attributeid);
    //     if ( !empty($OptionIds) ) {

    //         foreach ($OptionIds as $key => $OptionId) {

    //            $Optionvalues = $this->resourceModelColorSwatch->getOptionValues($OptionId['option_id']);
    //            $optionvalues=[];
    //            foreach ($Optionvalues as $key => $Optionvalue) {

    //                  $optionvalues[]  = $Optionvalue['value'];
    //            }

    //           if() {

    //                }




    //         }
    //     }

    //   return false;
    // }
}
