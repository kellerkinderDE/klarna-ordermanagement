//{block name="backend/order/controller/detail"}
//{$smarty.block.parent}
//{namespace name="backend/bestit_order_management/order_detail"}
/**
 * We defined this variable in order to be able to call the parent function when viewing the confirm callback.
 *
 * @see https://bestit.atlassian.net/browse/KSP-117
 */
var onSaveDetailsOrg = Ext.ClassManager.get('Shopware.apps.Order.controller.Detail').prototype.onSaveDetails;

Ext.define('Shopware.apps.BestitExtendOrder.controller.Detail', {
    override: 'Shopware.apps.Order.controller.Detail',

    /**
     * We reject the changes on a failure because shopware just removes an item if saving it failed which
     * is weird and confusing for the customer. So we just put it back.
     */
    onSavePosition: function (editor, e, order, options) {
        var me = this;

        //to convert the float value. Without this the insert value "10,55" would be converted to "1055,00"
        e.record.set('price', e.newValues.price);

        //the article suggest search is not a form field so we have to set the value manually
        e.record.set('articleName', e.newValues.articleName);
        e.record.set('articleNumber', e.newValues.articleNumber);

        //calculate the new total amount.
        if (Ext.isNumeric(e.newValues.price) && Ext.isNumeric(e.newValues.quantity)) {
            e.record.set('total', e.newValues.price * e.newValues.quantity);
            e.newValues.total = e.newValues.price * e.newValues.quantity;
        }

        e.record.save({
            callback: function (data, operation) {
                var records = operation.getRecords(),
                    record = records[0],
                    rawData = record.getProxy().getReader().rawData;

                if (operation.success === true) {
                    Shopware.Notification.createGrowlMessage(me.snippets.successTitle, me.snippets.positions.successMessage, me.snippets.growlMessage);
                    order.set('invoiceAmount', rawData.invoiceAmount);
                    if (options !== Ext.undefined && Ext.isFunction(options.callback)) {
                        options.callback(order);
                    }
                } else {
                    Shopware.Notification.createGrowlMessage(me.snippets.failureTitle, me.snippets.positions.failureMessage + '<br> ' + rawData.message, me.snippets.growlMessage);
                    e.store.rejectChanges(records);
                }
            }
        });
    },

    onUpdateDetailPage: function (order, window) {
        var me = this;

        me.callParent(arguments);

        me.refreshKlarnaTab();
    },

    refreshKlarnaTab: function () {
        var component = Ext.getCmp('bestitKlarnaOrderIFrame-' + this.record.get('id'));
        if (typeof component === 'undefined') {
            // The iFrame hasn't been loaded yet.
            return;
        }

        var componentEl = component.getEl();

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
