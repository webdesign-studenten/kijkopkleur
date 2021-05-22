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
        @media only screen and (max-width: 767px){
        .header-left-link li:nth-child(3) {padding-left: 0px !important;}
        .dropdown-link{display:none}
    .nav-sections .header.links{background: #ebdac6;}
    .nav-sections .header.links .header-left .header-left-link li {
    font-size: 0;
    border-bottom: 1px solid #d1d1d1;
    float: left;
    width: 100%;
}
.nav-sections .header.links .header-left {
    float: left;
    width: 100%;
    display: block;
}
.nav-sections .header.links li.customer-welcome {
    float: left;
    width: 100% !important;
    border-bottom: 1px solid #d1d1d1;
    margin-top: 0 !important;
}
.nav-sections .header.links li.customer-welcome .customer-menu li.authorization-link {
    display: none;
}
.nav-sections .header.links li.greet.welcome {
    float: left;
    width: 100% !important;
    padding-left: 8px !important;
    border-bottom: 1px solid #d1d1d1;
    padding-bottom: 10px;
}
.nav-sections .header.links li > a {
    border-top: 0;
}
.nav-sections .header.links {
    border-bottom: 0;
}
.nav-sections .header.links li.greet.welcome {
    border-top: 0;
}
.nav-sections .header.links li a {
    font-size: 14px;
    padding: 7px 0px 7px 7px;
    text-transform: uppercase;
}
.header.links li.authorization-link {
    position: relative;
    float: left;
    width: 100% !important;
    border-top: 1px solid #d1d1d1;
    margin-top: 0 !important;
}
.nav-sections .header.links .header-left .header-left-link li:nth-child(3) {
    border-bottom: 1px solid #d1d1d1;
}
.nav-sections .header.links li:nth-child(3) {
    font-size: 14px;
    padding: 0;
    color: #858585;
    font-weight: 700;
}
.nav-sections .header.links li {
    margin: 0;
    float: left;
    width: 26%;
}
.page-header .panel.wrapper {
    margin-top: 0;
}
.nav-sections .header.links .btw-incl {
    display: inline-flex;
}
.nav-sections .header.links {
    background: transparent;
    float: left;
    width: 100%;
}
.nav-sections .header.links .btw-incl li:nth-child(2) {
    margin-right: 0;
}
.nav-sections .header.links .btw-incl a {
    padding: 12px 12px 0px 21px;
    font-size: 12px;
}
.nav-sections .header.links .btw-incl li:nth-child(3) a {
    padding: 0;
}
.nav-sections .nav-sections-item-content {
    background-color: transparent;
}
}
        </style>
        
        <li style="display: inline-block;margin-right: 10px;" id="textstatus"><a>Incl. BTW</a></li>
        <li style="margin-top: 2px !important;margin-left: 5px;"><a>
            <label class="switch">
                <input class="vatswitcher" type="checkbox" id="switch" name="switch" '.$selected.'>
                <span class="slider round"></span>
            </label></a>
        </li>
        <li style="display: inline-block;margin-right: 10px;" id="textstatus"><a>Excl. BTW</li></a>'; 
    }
}
?>