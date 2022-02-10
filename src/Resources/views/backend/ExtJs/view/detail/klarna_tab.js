/**
 * Adds the Klarna Tab view to the edit order section
 * Important: please don't remove this comment, otherwise Exjs will throw Syntax Error.
 */
Ext.define('Shopware.apps.BestitExtendOrder.view.detail.KlarnaTab', {
    extend: 'Ext.container.Container',
    padding: 10,
    title: 'Klarna.',

    initComponent: function () {
        var me = this;
        me.src = '{url controller="BestitOrderManagement" action=index}?orderId=' + me.record.get('id');

        me.items = [Ext.create('Ext.Component', {
            id: 'bestitKlarnaOrderIFrame-' + me.record.get('id'),
            autoEl: {
                tag: 'iframe',
                src: me.src,
                style: {
                    height: '100%',
                    width: '100%'
                }
            }
        })];

        me.callParent(arguments);
    }
});
