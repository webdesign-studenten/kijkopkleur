<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Block\Adminhtml\Contact\Edit\Tab;

/**
 * Class Main
 * @package RLTSquare\ColorSwatch\Block\Adminhtml\Contact\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;
    /**
     * @var \RLTSquare\ColorSwatch\Helper\Data
     */
    protected $helper;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ColorSwatch\Source\AttributeValues
     */
    protected $attributeValues;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ColorSwatch\Source\Status
     */
    protected $status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \RLTSquare\ColorSwatch\Model\ColorSwatch\Source\AttributeValues $attributeValues,
        \RLTSquare\ColorSwatch\Model\ColorSwatch\Source\Status $status,
        \Magento\Framework\Data\FormFactory $formFactory,
        \RLTSquare\ColorSwatch\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->attributeValues = $attributeValues;
        $this->status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('rltsquare_colorswatch');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('contact_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Information')]);

        if ($model->getId()) {
            $fieldset->addField('colorswatch_id', 'hidden', ['name' => 'colorswatch_id']);
        }

        $fieldset->addField(
            'brand_id',
            'select',
            [
                'name' => 'brand_id',
                'label' => __('Color Groups'),
                'title' => __('Color Groups'),
                'required' => true,
                'values' => $this->attributeValues->toOptionArray(),
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Enable'),
                'title' => __('Enable'),
                'required' => true,
                'values' => $this->status->toOptionArray(),
                'disabled' => false,
            ]
        );


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Main');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Main');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
