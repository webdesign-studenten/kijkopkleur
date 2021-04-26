<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_VATExempt
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento-extensions/vat-exempt-m2.html
*
**/
namespace Milople\Vatexempt\Model;class Details extends \Magento\Framework\Model\AbstractModel{	/**     * Initialize resource model     *     * @return void     */	protected $STATUS;	/**     * CMS page cache tag.    */    const CACHE_TAG = 'vatexempt_medicalcondition';    /**     * @var string    */    protected $_cacheTag = 'vatexempt_medicalcondition';    /**     * Prefix of model events names.     *     * @var string    */    protected $_eventPrefix = 'vatexempt_medicalcondition';
    /**    * Initialize resource model    *	* @return void	*/	protected function _construct()	{		$this->_init('Milople\Vatexempt\Model\ResourceModel\Details');
    }
	
}
