<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */

namespace RLTSquare\ColorSwatch\Model\ResourceModel;

/**
 * Class ColorSwatch
 * @package RLTSquare\ColorSwatch\Model\ResourceModel
 */
class ColorSwatch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('rlt_square_colorswatch', 'colorswatch_id');
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getIdByCode($code)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('catalog_product_link_type'),["link_type_id"])
            ->where('code = ?', $code);
        $data = $this->getConnection()->fetchAll($select);

        return $data['0']['link_type_id'];
    }

    /**
     * @return mixed
     */
    public function getAllBrands()
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('rlt_square_colorswatch'));
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }

    /**
     * @param $productid
     * @param $linktypeid
     * @return array
     */
    public function getAttachedProductIds($productid,$linktypeid)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('catalog_product_link'),["linked_product_id"])
            ->where('product_id = ?', $productid)->where('link_type_id = ?', $linktypeid);
        $data = $this->getConnection()->fetchAll($select);
        $prodids = [];
        if(!empty($data)) {
            foreach ($data as $prodid) {
                $prodids[] = $prodid['linked_product_id'];
            }
        }
        return $prodids;
    }

    /**
     * @param $brandids
     * @return mixed
     */
    public function getRelatedBrands($brandids)
    {

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('rlt_square_colorswatch'),["colorswatch_id"]);
        $brandids = explode(",", $brandids);
        $data = [];
        $select = $select->where('brand_id IN (?)', $brandids);
        $data = $this->getConnection()->fetchAll($select);
        return $data;

    }

    /**
     * @param $colorswatchids
     * @return mixed
     */
    public function getRelatedProducts($colorswatchids)
    {
        $ids = [];
        foreach ($colorswatchids as $key => $value) {
            $ids[] = $value['colorswatch_id'];
        }
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('rltsquare_colorswatch_products'),["product_id"]);
        $select = $select->where('colorswatch_id IN (?)', $ids);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
    /**
     * @param $brandid
     * @return mixed
     */
    public function getColorSwatchId($brandid)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('rlt_square_colorswatch'),["colorswatch_id"])
            ->where('brand_id = ?', $brandid);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
    /**
     * @param $colorswatchid
     * @return mixed
     */
    public function getProductIds($colorswatchid)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('rltsquare_colorswatch_products'),["product_id"])
            ->where('colorswatch_id = ?', $colorswatchid);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
    /**
     * @param $optionid
     * @return false
     */
    public function CreateorCheck($optionid)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('rlt_square_colorswatch'),["colorswatch_id"])
            ->where('brand_id = ?', $optionid);
        $data = $this->getConnection()->fetchAll($select);
        if(empty($data)) {
            $submitdata = [];
            $submitdata['brand_id'] = $optionid;
            $submitdata['status'] = 1;
            $this->getConnection()->insert($this->getTable('rlt_square_colorswatch'), $submitdata);
            return false;
        } else {
            return $data['0']['colorswatch_id'];
        }
    }
    /**
     * @param $prodid
     * @param $colorswatchid
     */
    public function AttachedProductid($prodid,$colorswatchid)
    {
        $submitdata = [];
        $submitdata['product_id'] = $prodid;
        $submitdata['colorswatch_id'] = $colorswatchid;
        $this->getConnection()->insert($this->getTable('rltsquare_colorswatch_products'), $submitdata);
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getAttributeId($code)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('eav_attribute'),["attribute_id"])
            ->where('attribute_code = ?', $code);
        $data = $this->getConnection()->fetchAll($select);
        return $data['0']['attribute_id'];
    }

    /**
     * @param $attributeid
     * @param $prodid
     * @return array
     */
    public function getColorValue($attributeid,$prodid)
    {

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('catalog_product_entity_text'),["value"])
            ->where('attribute_id = ?', $attributeid)->where('entity_id = ?', $prodid);
        $data = $this->getConnection()->fetchAll($select);
        if( !empty($data) ) {

            return $data['0']['value'];

        } else {

            return [];
        }
    }

    /**
     * @param $attributeid
     * @return mixed
     */
    public function getOptionIds($attributeid)
    {

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('eav_attribute_option'),["option_id"])
            ->where('attribute_id = ?', $attributeid);
        $data = $this->getConnection()->fetchAll($select);
        return $data;


    }

    /**
     * @param $optionid
     * @return mixed
     */
    public function getOptionValues($optionid)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('eav_attribute_option_value'),["value"])
            ->where('option_id = ?', $optionid)->where('store_id = ?', 0);


        $data = $this->getConnection()->fetchAll($select);
        return $data;

    }

    /**
     * @param $attributeid
     * @param $prodid
     * @return array
     */
    public function getImageValue($attributeid,$prodid)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('catalog_product_entity_varchar'),["value"])
            ->where('attribute_id = ?', $attributeid)->where('entity_id = ?', $prodid);
        $data = $this->getConnection()->fetchAll($select);

        if( !empty($data) ) {

            return $data['0']['value'];

        } else {

            return [];
        }
    }

    /**
     * @param $attributeid
     * @param $prodid
     * @return array
     */
    public function getBrandValue($attributeid,$prodid)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('catalog_product_entity_text'),["value"])
            ->where('attribute_id = ?', $attributeid)->where('entity_id = ?', $prodid);
        $data = $this->getConnection()->fetchAll($select);
        if( !empty($data) ) {
            return $data['0']['value'];
        } else {
            return [];
        }
    }

    /**
     * @param $brandids
     * @return mixed
     */
    public function getColorSwatchIds($brandids)
    {

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('rlt_square_colorswatch'),["colorswatch_id"])
            ->where('brand_id IN (?)', $brandids);

        $data = $this->getConnection()->fetchAll($select);
        return $data;

    }




}
