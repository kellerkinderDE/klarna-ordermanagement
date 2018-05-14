/**
 * Checks if the order payment is Klarna Payment.
 */
Ext.define('BestitKlarna.controller.Order', {
    singleton: true,
    resultCache: {},

    isKlarnaPaymentId: function (paymentId) {
        if (typeof this.resultCache[paymentId] !== 'undefined') {
            return this.resultCache[paymentId];
        }

        var result = false;

        Ext.Ajax.request({
            url: '{url controller="BestitOrderManagement" action="isKlarnaOrder"}',
            async: false,
            method: 'POST',
            params: {
                paymentId: paymentId
            },
            success: function (responseEncoded) {
                var response = Ext.JSON.decode(responseEncoded.responseText);

                if (response.success) {
                    result = true;
                }
            }
        });

        return this.resultCache[paymentId] = result;
    }
});
