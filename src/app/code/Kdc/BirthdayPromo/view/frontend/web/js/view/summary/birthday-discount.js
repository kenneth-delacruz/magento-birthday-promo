define([
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Kdc_BirthdayPromo/checkout/summary/birthday-discount',
            dobMessage: null
        },

        initialize: function () {
            this._super();
            
            // Check if the config has dobMessage
            if (this.hasOwnProperty('config') && this.config.hasOwnProperty('dobMessage')) {
                this.dobMessage = this.config.dobMessage;
            }
        },

        getMessage: function () {
            return this.dobMessage;
        }
    });
});
