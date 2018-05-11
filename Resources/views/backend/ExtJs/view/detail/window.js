//{block name="backend/order/view/detail/window"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.BestitExtendOrder.view.detail.Window', {
    override: 'Shopware.apps.Order.view.detail.Window',

    /**
     * Creates the tab panel for the detail page.
     * @return Ext.tab.Panel
     */
    createTabPanel: function () {
        var me = this, paymentId = me.record.get('paymentId'), result = me.callParent();

        if (BestitKlarna.controller.Order.isKlarnaPaymentId(paymentId)) {
            /**
             * Make sure that our tab is loaded in the background - before the tab is clicked.
             */
            result.layout.deferredRender = false;

            result.insert(
                Ext.create('Shopware.apps.BestitExtendOrder.view.detail.KlarnaTab', {
                    record: me.record,
                    padding: '0'
                })
            );
        }

        return result;
    }
});
//{/block}