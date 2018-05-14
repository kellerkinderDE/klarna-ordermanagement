//{namespace name=backend/bestit_order_management/order_overview}
//{block name="backend/order/view/detail/overview"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.BestitExtendOrder.view.detail.Overview', {
    override: 'Shopware.apps.Order.view.detail.Overview',

    createEditElements: function () {
        var me = this, paymentId = me.record.get('paymentId'), result = me.callParent();

        if (BestitKlarna.controller.Order.isKlarnaPaymentId(paymentId)) {
            Ext.Array.forEach(result, function (record) {
                var name = record['name'];

                if (name === 'invoiceShipping' || name === 'invoiceShippingNet') {
                    record['disabled'] = true;
                    record['supportText'] = '{s name=ShippingCostsSupportText}The shipping costs cannot be changed for orders that have been paid through Klarna.{/s}';
                }
            });
        }

        return result;
    }
});
//{/block}
