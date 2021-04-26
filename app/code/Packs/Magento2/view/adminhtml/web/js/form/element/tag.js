define([

    <span style="font-weight: 400;">  </span>,

'Magento_Ui/js/form/element/select',

    'ko'

], function(Select, ko) {
    'use strict';
    return Select.extend({
        optionsAfterRender: function (option, item) {
            if(item != undefined) {
                if(item.value == 'remove') {
                    ko.applyBindingsToNode(option, {style: {background: '#FFFFFF'}}, item);
                }else {
                    ko.applyBindingsToNode(option, {style: {background: item.value}}, item);
                }
            }
        },
       setSelectColor: function (item, event) {

            if(event.target.value != undefined)

                event.target.style='background-color:'+event.target.value;

        }

    });

});