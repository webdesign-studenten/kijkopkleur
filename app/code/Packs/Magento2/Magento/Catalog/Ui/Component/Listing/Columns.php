<?php

namespace Packs\Magento2\Magento\Catalog\Ui\Component\Listing;

class Columns extends \Magento\Catalog\Ui\Component\Listing\Columns {
    // Array of attributes not included in
    //  \vendor\magento\module-catalog\view\adminhtml\ui_component\product_listing.xml
//   }
    /**
     * {@inheritdoc}
     */
    public function prepare() {
        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        foreach ($this->attributeRepository->getList() as $attribute) {
            $attr_code = $attribute->getAttributeCode();
            $config = [];
            if (!isset($this->components[$attr_code]) || in_array($attr_code,
                    $this->additional_fields)) {
                $config['sortOrder'] = ++$columnSortOrder;
                if ($attribute->getIsFilterableInGrid()) {
                    $config['filter'] = $this->getFilterType($attribute->getFrontendInput());
                }
                // Copy editor configuration for additional attributes
                if (isset($this->components[$attr_code]->_data['config']['editor'])) {
                    $config['editor'] = $this->components[$attr_code]->_data['config']['editor'];
                }
                $column = $this->columnFactory->create($attribute,
                    $this->getContext(), $config);
                $column->prepare();
                $this->addComponent($attribute->getAttributeCode(), $column);
            }
        }
        parent::prepare();
    }
}