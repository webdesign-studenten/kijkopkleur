define([

    'Magento_Ui/js/grid/editing/record'

], function (Record) {

    'use strict';

    return Record.extend({

        defaults: {
            templates: {
                fields: {
                    // Add tag element
                    tag: {
                        component: 'Packs_Magento2/js/form/element/tag',
                        template: 'Packs_Magento2/form/element/tag',
                        options: '${ JSON.stringify($.$data.column.options) }'
                    }
                }
            },
        },
    });
});
