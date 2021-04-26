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

namespace Milople\Vatexempt\Api\Data;



interface VatexemptInformationInterface

{

    /**#@+

     * Constants defined for keys of array for VAT Exempt

     */

    const SELECTED_STATUS = 'selected_status';



    const APPLIENT_NAME = 'applient_name';



    const SELECTED_REASON = 'selected_reason';



    const AGREE_TERMSANDCONDITIONS = 'agree_termsandconditions';

    const SELECTED_FILES = 'agree_termsandconditions';



    /**#@-*/



		public function getSelectedStatus();



		public function setSelectedStatus($value);



		public function getApplientName();



		public function setApplientName($name);



		public function getSelectedReason();



		public function setSelectedReason($reason);



		public function getAgreeTermsandconditions();


		public function setAgreeTermsandconditions($conditions);

    public function setSelectedFiles($files);

    public function getSelectedFiles();
}

