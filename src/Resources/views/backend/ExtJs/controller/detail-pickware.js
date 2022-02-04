//{block name="backend/order/controller/detail"}
//{$smarty.block.parent}
//{namespace name="backend/bestit_order_management/order_detail"}
/**
 * This is a Copy of detail.js but without the function onSavePosition due to the conflict with PickWare.
 *
 * We defined this variable in order to be able to call the parent function when viewing the confirm callback.
 *
 * @see https://bestit.atlassian.net/browse/KSP-117
 */
var onSaveDetailsOrg = Ext.ClassManager.get('Shopware.apps.Order.controller.Detail').prototype.onSaveDetails;

Ext.define('Shopware.apps.BestitExtendOrder.controller.Detail', {
    override: 'Shopware.apps.Order.controller.Detail',

    onUpdateDetailPage: function (order, window) {
        var me = this;

        me.callParent(arguments);

        me.refreshKlarnaTab();
    },

    refreshKlarnaTab: function () {
        var componentEl = Ext.getCmp('bestitKlarnaOrderIFrame-' + this.record.get('id')).getEl();

        if (typeof componentEl === 'undefined') {
            // The iFrame hasn't been loaded yet.
            return;
        }

        var componentDom = componentEl.dom;
        var iFrameSrc = componentDom.src;

        if(!iFrameSrc){
            return;
        }
        /**
         * We just append a random parameter to the iFrame URL, so that the browser
         * re-renders it. There is no way to reload the tab/iFrame through some
         * ExtJS method, unfortunately.
         */
        componentDom.src = iFrameSrc + '&random=' + Math.random();
    },

    onSaveDetails: function (record, options) {
        var me = this;
        var oldPaymentId = record.raw.paymentId;
        var newPaymentId = record.get('paymentId');
        var parentArguments = arguments;
        var title = '{s name="messagebox_change_payment/title"}Change Payment{/s}';

        if (!BestitKlarna.controller.Order.isKlarnaPaymentId(oldPaymentId) && BestitKlarna.controller.Order.isKlarnaPaymentId(newPaymentId)) {
            Ext.MessageBox.alert(
                title,
                '{s name="messagebox_change_payment/to_klarna_payment_method_not_possible"}Changing the payment method from a non Klarna payment method to a Klarna payment method is not possible.{/s}'
            );

            return;
        }

        if (oldPaymentId === newPaymentId || !BestitKlarna.controller.Order.isKlarnaPaymentId(oldPaymentId)) {
            me.callParent(arguments);
            // We need to do this because shopware doesn't update that value but we rely on it on subsequent calls.
            record.raw.paymentId = newPaymentId;
            return;
        }

        var message = '{s name="messagebox_change_payment/change_confirmation_message"}Do you really want to change the payment method? Changing the payment method will cancel this order towards Klarna.{/s}';

        Ext.MessageBox.confirm(title, message, function (response) {
            if (response !== 'yes') {
                return false;
            }

            onSaveDetailsOrg.apply(me, parentArguments);
            // We need to do this because shopware doesn't update that value but we rely on it on subsequent calls.
            record.raw.paymentId = newPaymentId;
        });
    }
});
//{/block}
