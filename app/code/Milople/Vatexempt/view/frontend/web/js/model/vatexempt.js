/**

 * Copyright © 2015 Magento. All rights reserved.

 * See COPYING.txt for license details.

 */

define(

    ['ko'],

    function (ko) {

        'use strict';

        var vatStatus = window.vatexemptConfig.vatexemptStatus;

        var vatApplyTo = window.vatexemptConfig.vatexemptApplyTo;

        var vatShowLink = window.vatexemptConfig.vatexemptShowLink;

        var vatLinkText = window.vatexemptConfig.vatexemptLinkText;

        var vatTermsandconditions = window.vatexemptConfig.vatexemptTermsandconditions;

	 	var vatProductList = window.vatexemptConfig.vatexemptProductList;

	 	var vatURL = window.vatexemptConfig.vatexemptURL;

	 	var vatFile =  window.vatexemptConfig.vatexemptFile;

	 	var selectedVatStatus = 1;

	 	var applientName = null;

	 	var selectedReason = null;

         var agreeTermsandconditions = false;
         
        var selectFile = null;


        return {

            setSelectedVatStatus: function(value) {

                this.selectedVatStatus = value;

            },

            setApplientName: function(value) {

                this.applientName = value;

            },

            setSelectedReason: function(value) {

                this.selectedReason = value;

            },

            setAgreeTermsandconditions: function(value) {

                this.agreeTermsandconditions = value;

            },




            

            getSelectedVatStatus: function() {

                return this.selectedVatStatus;

            },

            getApplientName: function() {

                return this.applientName;

            },

            getSelectedReason: function() {

                return this.selectedReason;

            },

            getAgreeTermsandconditions: function() {

                return this.agreeTermsandconditions;

            },

            getURL:function() {

            	return vatURL;

            },

            getFile:function() {

            	return vatFile;

            }

        };

    }

);

