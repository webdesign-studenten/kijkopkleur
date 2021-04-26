define(

    [

        'ko',

        'uiComponent',

        'underscore',

        'Milople_Vatexempt/js/fancybox/jquery.fancybox',

        'Milople_Vatexempt/js/model/vatexempt',

        'Magento_Checkout/js/model/step-navigator',

        'jquery',

        'mage/url',

        'Magento_Checkout/js/model/url-builder',

        'mage/storage',

        'Magento_Checkout/js/model/full-screen-loader',

        'Magento_Checkout/js/model/error-processor',

        "Magento_Checkout/js/model/quote",

        'Magento_Checkout/js/model/resource-url-manager',



    ],

    function (

        ko,

        Component,

        _,

        fancybox,

        vatexempt,

        stepNavigator,

        $,

        mageUrl1,

        urlBuilder,

        storage,

        fullScreenLoader,

        errorProcessor,

        quote,

        resourceUrlManager

    ) {

        'use strict';

        return Component.extend({

            defaults: {

                template: 'Milople_Vatexempt/vatexempt'

            },

            vatStatus: window.vatexemptConfig.vatexemptStatus,
            vatFile: window.vatexemptConfig.vatexemptFile,

            vatApplyTo: window.vatexemptConfig.vatexemptApplyTo,

            vatShowLink: window.vatexemptConfig.vatexemptShowLink,

            vatLinkText: window.vatexemptConfig.vatexemptLinkText,

            vatTermsandconditions: window.vatexemptConfig.vatexemptTermsandconditions,

            vatProductList: window.vatexemptConfig.vatexemptProductList,

            vatMedicalConditions: window.vatexemptConfig.vatexemptConditions,




            VatStatus: [{

                value: 1,

                text: 'Yes'

            },

            {

                value: 0,

                text: 'No'

            }

            ],

            selectedVatStatus: ko.observable('Please Select'),

            applientName: ko.observable(null),

            selectedReason: ko.observable('Please Select'),

            selectedFile: ko.observable(null),

            agreeTermsandconditions: ko.observable(null),



            //add here your logic to display step,

            isVisible: ko.observable(false),

            upd: function () {
                console.log("Vatexampt js file called");
                var file = [];
                file = $("#file")[0].files;
                var formData = new FormData();
                var filesLength = $("#file")[0].files.length;
                for (var i = 0; i < filesLength; i++) {
                    formData.append("files[]", file[i]);
                }


                //var file = $("#file")[0].files;
                //var fd = new FormData();
                //fd.append('theFile', file);

                // var data = new FormData();

                // $.each($("#file")[0].files, function(i, file) {
                //     data.append('file', file);
                // });
                // console.log(formData);

                // var serviceUrl = urlBuilder.createUrl('/vatexempt/doc/save', {});
                // var contentType = 'multipart/form-data';
                $.ajax({
                    url: mageUrl1.build('vatexempt/doc/save'),
                    type: 'POST',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (data, status, jqxhr) {
                        console.log('suc');
                    },
                    error: function (jqxhr, status, msg) {
                        //error code
                        console.log('fail');
                    }//JSON.stringify(payload),
                    // global: global,
                    // contentType: contentType
                });
            },
            /**

             *

             * @returns {*}

             */

            initialize: function () {

                this._super();

                // register your step

                if (window.vatexemptConfig.vatexemptProductList.length > 0) {

                    stepNavigator.registerStep(

                        //step code will be used as step content id in the component template

                        'vat_exempt',

                        //step alias

                        null,

                        //step title value

                        'VAT Exempt',

                        //observable property with logic when display step or hide step

                        this.isVisible,



                        _.bind(this.navigate, this),



                        /**

                         * sort order value

                         * 'sort order value' < 10: step displays before shipping step;

                         * 10 < 'sort order value' < 20 : step displays between shipping and payment step

                         * 'sort order value' > 20 : step displays after payment step

                         */

                        15

                    );

                }

                this.selectedVatStatus.subscribe(function (value) {

                    vatexempt.setSelectedVatStatus(value);

                });



                this.applientName.subscribe(function (value) {

                    vatexempt.setApplientName(value);

                });


                //  this.selectedFile.subscribe(function(value) {

                //     vatexempt.setSelectedFile(value);

                // }); 



                this.selectedReason.subscribe(function (value) {

                    vatexempt.setSelectedReason(value);

                });




                this.agreeTermsandconditions.subscribe(function (value) {

                    vatexempt.setAgreeTermsandconditions(value);

                });

                return this;

            },

            showFormPopUp: function () {

                $.fancybox({

                    'type': 'inline',

                    'href': '#dialogContent'

                });

            },

            hideOptions: function () {

                if (vatexempt.getSelectedVatStatus() == '1') {

                    if (vatexempt.getFile() == '0') {
                        jQuery('.label').hide();
                        jQuery('#file').hide();
                    }

                    $('#applientNameId').show();

                    $('#applientBox').prop('required', true);

                    $('#applientFile').show();

                    $('#file').prop('required', true);

                    $('#vatTermsandconditionsId').show();

                    $('#termCheck').prop('required', true);

                    $("#vatMedicalConditionsId").show();

                    $('#medicalSelect').prop('required', true);

                } else {

                    if (vatexempt.getFile() == '0') {
                        jQuery('.label').hide();
                        jQuery('#file').hide();
                    }

                    $('#applientNameId').hide();
                    $('#applientFile').hide();

                    $('#applientBox').prop('required', false);


                    $('#applientBox').val('');

                    $("#vatMedicalConditionsId").hide();

                    $('#termCheck').prop('required', false);

                    $('#termCheck').val('');

                    $('#vatTermsandconditionsId').hide();

                    $('#termCheck').attr('checked', false);

                    $('#medicalSelect').prop('required', false);



                }

            },

            /**

             * The navigate() method is responsible for navigation between checkout step

             * during checkout. You can add custom logic, for example some conditions

             * for switching to your custom step 

             */

            navigate: function () {

                var self = this;

                self.isVisible(true);



            },



            /**

             * @returns void

             */

            navigateToNextStep: function () {

                var requestUrl = vatexempt.getURL();

                var selectedStatus = vatexempt.getSelectedVatStatus();

                var applientName = (vatexempt.getApplientName()) ? vatexempt.getApplientName() : null;

                var selectedReason = vatexempt.getSelectedReason();
                if (jQuery("#file").val() == "") {
                    var selectedFile = null;
                } else if(window.vatexemptConfig.vatexemptFile != '0'){
                    if (jQuery("#file")[0].files.length == '1') {
                        var selectedFile = jQuery("#file")[0].files[0].name;
                    } else {

                        var selectedFile = "";
                        var len = jQuery("#file")[0].files.length;
                        for (var i = 0; i < len; i++) {
                            var fname = jQuery("#file")[0].files[i].name;
                            if (i > 0) {
                                selectedFile += ',';
                            }
                            selectedFile += fname;
                        }
                    }
                }else{
                    var selectedFile = null;
                }
                console.log(selectedFile);
                var agreeTermsandconditions = vatexempt.getAgreeTermsandconditions();

                var serviceUrl = urlBuilder.createUrl('/vatexempt/setdata', {});
                // var global = undefined;
                var contentType = 'application/json; charset=utf-8';

                var payload = {

                    vatexempt: {

                        selectedStatus: selectedStatus,

                        applientName: applientName,

                        selectedReason: selectedReason,

                        selectedFile: selectedFile,

                        agreeTermsandconditions: agreeTermsandconditions,

                    }

                };

                fullScreenLoader.startLoader();



                return storage.post(

                    serviceUrl, JSON.stringify(payload) //, global, contentType

                )
                    .done(

                        function (response) {

                            console.log(response);

                            storage.get(resourceUrlManager.getUrlForCartTotals(quote), false)

                                .done(

                                    function (response) {

                                        quote.setTotals(response);

                                        fullScreenLoader.stopLoader();

                                        stepNavigator.next();

                                    })

                                .fail(

                                    function (response) {

                                        //do your error handling

                                    });

                        }

                    ).fail(

                        function (response) {

                            fullScreenLoader.stopLoader();

                            errorProcessor.process(response);

                        }

                    );
            }

        });

    }

);