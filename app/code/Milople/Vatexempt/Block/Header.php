<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Vatexempt
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/partial-payment-m2.html
*
**/
namespace Milople\Vatexempt\Block;
class Header extends \Magento\Framework\View\Element\Html\Link
{
    //protected $_template = 'Milople_Vatexempt::switch.phtml';
    protected function _toHtml()
    {
     if (false != $this->getTemplate()) {
     return parent::_toHtml();
     }

    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$checkoutsession = $objectManager->get('Magento\Checkout\Model\Session');
	$selected = '';
    
    if($checkoutsession->getVatStatus() == "1"){
        $selected = 'Checked';
    }
    
     //return '<li><input style="transform: scale(1.7);" type="checkbox" id="switch" name="switch" value="switch"'.$selected.' ><label style="margin-left: 10px;" for="switch">Vatexempt</label></li>';
        return '<style>
        .header li{
            margin-top:9px !important;
        }
        .switch {
          position: relative;
          display: inline-block;
          width: 60px;
          height: 34px;
        }
        
        .switch input { 
          opacity: 0;
          width: 0;
          height: 0;
        }
        
        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        
        .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: #69966f;
          -webkit-transition: .4s;
          transition: .4s;
        }
        
        input:checked + .slider {
          background-color: #ffffff;
        }
        
        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }
        
        input:checked + .slider:before {
          -webkit-transform: translateX(26px);
          -ms-transform: translateX(26px);
          transform: translateX(26px);
        }
        
        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }
        
        .slider.round:before {
          border-radius: 50%;
        }
        </style>
        <li style="display: inline-block;margin-right: 10px;" id="textstatus">Incl. BTW</li>
        <li style="margin-top: 2px !important;margin-left: 0px;">
            <label class="switch">
                <input class="vatswitcher" type="checkbox" id="switch" name="switch" '.$selected.'>
                <span class="slider round"></span>
            </label>
        </li>
        <li style="display: inline-block;margin-right: 10px;" id="textstatus">Excl. BTW</li>';
    }
}
?>