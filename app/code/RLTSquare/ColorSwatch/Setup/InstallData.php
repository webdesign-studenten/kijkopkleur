<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */

namespace RLTSquare\ColorSwatch\Setup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Model\Product\Attribute\Frontend\Image as ImageFrontendModel;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;

/**
 * Class InstallData
 * @package RLTSquare\ColorSwatch\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var ResourceProduct
     */
    protected $_resourceProduct;
    /**
     * @var AttributeSet
     */
    protected $_attributeSet;

    /**
     * InstallData constructor.
     * @param AttributeSet $attributeSet
     * @param ResourceProduct $resourceProduct
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        AttributeSet $attributeSet,
        ResourceProduct $resourceProduct,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->_resourceProduct = $resourceProduct;
        $this->_attributeSet    = $attributeSet;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute (
            \Magento\Catalog\Model\Product::ENTITY,
            'colors',
            [
                'group' => 'Color Groups',
                'label' => 'Colors',
                'type'  => 'text',
                'input' => 'select',
                'source' => 'RLTSquare\ColorSwatch\Model\ColorSwatch\Source\Colors',
                'required' => false,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'visible_on_front' => false
            ]
        );
        $eavSetup->addAttribute (
            \Magento\Catalog\Model\Product::ENTITY,
            'brands',
            [
                'group' => 'Color Groups',
                'label' => 'Color Groups',
                'type'  => 'text',
                'input' => 'multiselect',
                'source' => 'RLTSquare\ColorSwatch\Model\ColorSwatch\Source\Companiess',
                'required' => false,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'visible_on_front' => false
            ]
        );

        $eavSetup->addAttribute (
            \Magento\Catalog\Model\Product::ENTITY,
            'color_swatch_image',
            [
                'type' => 'varchar',
                'label' => 'ColorSwatch Image',
                'input' => 'media_image',
                'frontend' => ImageFrontendModel::class,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'group' => 'General',
            ]
        );

        $eavSetup->addAttribute (
            \Magento\Catalog\Model\Product::ENTITY,
            'company_shades',
            [
                'type' => 'int',
                'label' => 'ColorSwatch Groups',
                'input' => 'select',
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => true,
                'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_VIRTUAL]),
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
            ]
        );

        $eavSetup->addAttribute (
            \Magento\Catalog\Model\Product::ENTITY,
            'background_color',
            [
                'type'     => 'text',
                'label'    => 'Backgroud Color',
                'input'    => 'textarea',
                'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible'  => true,
                'default'  => "",
                'required' => false,
                'group' => "Color Groups",
                'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ]
        );
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'enable_Colorswatch',
            [
                'type'     => 'int',
                'label'    => 'Enable Color Swatch',
                'note' => 'This Attribute Color Swatch Only Works On Configurable Products',
                'input'    => 'boolean',
                'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible'  => true,
                'default'  => 0,
                'required' => false,
                'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ]
        );

        $setup->endSetup();
    }
}
