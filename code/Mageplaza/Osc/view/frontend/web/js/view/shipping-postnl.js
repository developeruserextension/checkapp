define([
    'jquery',
    'TIG_PostNL/js/Helper/State',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Mageplaza_Osc/js/action/payment-total-information'
], function (
    $,
    State,
    quote,
    priceUtils,
    getPaymentTotalInformation
) {
    return function (Shipping) {
        return Shipping.extend({
            initialize: function () {
                this._super();
                $(document).on('compatible_postnl_deliveryoptions_save_done', function (event, data) {
                    getPaymentTotalInformation();
                });
            },
            PostNLFee: State.fee,
            isEnableModulePostNL: window.checkoutConfig.oscConfig.compatible.isEnableModulePostNL,
            canUseDeliveryOption: function () {
                var deliveryOptionsActive = window.checkoutConfig.shipping.postnl.shippingoptions_active == 1;
                var deliveryDaysActive = window.checkoutConfig.shipping.postnl.is_deliverydays_active;
                var pakjegemakActive = window.checkoutConfig.shipping.postnl.pakjegemak_active == '1';

                return deliveryOptionsActive && (deliveryDaysActive || pakjegemakActive);
            },

            isPostNLDeliveryMethod: function (method) {
                return method.carrier_code == 'tig_postnl';
            },

            canUsePostnlDeliveryOptions: function (method) {
                if (!this.canUseDeliveryOption()) {
                    return false;
                }

                var result = this.isPostNLDeliveryMethod(method);

                if (result) {
                    State.method(method);
                }

                return result;
            },

            formatPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            }
        });
    }
});