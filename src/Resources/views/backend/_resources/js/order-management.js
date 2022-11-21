$(function () {

    /**
     * Will be called when a checkbox changed
     */
    var $checkbox = $('.js--orderLine-checkbox');
    $checkbox.change(function (e) {
        calculateSum(e);
    });

    /**
     * Will be called when a quantity changed
     */
    var $quantity = $('.js--orderLine-quantity');
    $quantity.on('change mouseup keyup input', function (e) {
        calculateSum(e);
    });

    /**
     * Update the sum for the capture/refund amount and disable submit-btn if amount is <= 0.
     * @param e
     */
    var $sum = $('.js--sum');
    $sum.each(function(i, e) {
        var action = e.dataset.action;
        var actionId = '#' + action + '-btn';

        if (parseInt(e.value, 10) <= 0) {
            $(actionId).prop('disabled', true);
        }

        e.addEventListener('change', function() {
            if (parseInt(e.value, 10) > parseInt(e.max, 10)) {
                e.value = e.max;
            }

            if (e.value <= 0) {
                $(actionId).prop('disabled', true);
            }

            if (e.value > 0) {
                $(actionId).prop('disabled', false);
            }
        });
    });

    /**
     * Recalculate the sum for the capture/refund Amount.
     * @param e
     */
    function calculateSum(e) {
        var $target = $(e.currentTarget);
        var totalPrice = 0;
        var action = $target.attr('data-action');
        var actionClass = '.js--sum.' + action;
        var orderLinesName = getOrderLinesName(action);

        $('input[name^=' + orderLinesName + ']').each(function (i, e) {
            var $el = $(e);
            if ($el.is(':checked')) {
                var $parant = $el.closest('tr');
                var $quantity = $parant.find('.js--orderLine-quantity');
                totalPrice = Math.round((totalPrice + ($el.attr('data-price') * $quantity.val())) * 100) / 100;
            }
        });
        
        var maxTotalPrice = getMaxTotalPrice(action); 

        if (maxTotalPrice && totalPrice > maxTotalPrice) {
            totalPrice = maxTotalPrice;
        }

        $(actionClass).val(totalPrice);
        /* trigger change event manually so that eventListener can change the max amount */
        document.querySelector(actionClass).dispatchEvent(new Event('change'));
    }

    /**
     * Calculates the maximum total price for a refund/capture.
     */
    function getMaxTotalPrice(action) {
        var actionClass = '.js--sum.' + action;
        var input = document.querySelector(actionClass);

        if (input.max) {
            return input.max;
        }

        return null;
    }

    /**
     * Select all checkboxes or deselect them, and recalculate Sum.
     */
    var $selectAll = $('.js--select-all-checkboxes');
    $selectAll.change(function (e) {
        var $target = $(e.currentTarget);
        var action = $target.attr('data-action');
        var orderLinesName = getOrderLinesName(action);

        $('input[name^=' + orderLinesName + ']').each(function (i, e) {
            var $el = $(e);
            $el.prop('checked', $target.is(':checked'));
        });

        calculateSum(e);
    });

    function getOrderLinesName(action) {
        var orderLinesName;
        if (action === 'refund') {
            orderLinesName = 'refund_order_line';
        } else {
            orderLinesName = 'capture_order_line';
        }

        return orderLinesName
    }

    /**
     * Checks if the sum is different from the actual positions and shows the suitable text
     *
     */
    var $submitBtn = $('.js-confirmation-btn');
    $submitBtn.click(function (e) {
        var $target = $(e.currentTarget);
        var order = $target.data('order');
        var action = $target.attr('data-action');
        var amountClass = '.js--sum.' + action;
        var confirmationNormalClass = '.' + action + '-confirmation-normal';
        var confirmationDiffersClass = '.' + action + '-confirmation-differs';
        var orderLineName = action + '_order_line';
        var amount = $(amountClass).val();

        var selectedLines = [];
        $('input[name^=' + orderLineName + ']').each(function (i, e) {
            var $el = $(e);
            if (!$el.is(':checked')) {
                return;
            }

            var $quantity = $el.closest('tr').find('.js--orderLine-quantity');
            order.order_lines[i].quantity = parseInt($quantity.val(), 10);
            selectedLines.push(order.order_lines[i]);
        });

        var sum = selectedLines.reduce(function (sum, orderline) {
            return sum + (orderline.quantity * orderline.unit_price);
        }, 0);

        amount = Math.round(amount * 100);
        sum = Math.round(sum);

        if (amount === sum) {
            $(confirmationNormalClass).show();
            $(confirmationDiffersClass).hide();
        } else {
            $(confirmationDiffersClass).show();
            $(confirmationNormalClass).hide();
        }
    });

    /**
     * Filter selected lines
     * Get captured/refund amount
     * Get description
     * Send all data to the backend in order to make capture/refund call
     */
    var $submitOrderActionBtn = $('.js--submit-orderAction');
    $submitOrderActionBtn.click(function (e) {
        var $target = $(e.currentTarget);
        var order = JSON.parse($target.attr('data-order'));
        var url = $target.attr('data-url');
        var action = $target.attr('data-action');
        var amountClass = '.js--sum.' + action;
        var descriptionClass = '.js--comment.' + action;
        var orderLineName = action + '_order_line';

        var amount = $(amountClass).val();
        var description = $(descriptionClass).val();

        var selectedLines = [];
        $('input[name^=' + orderLineName + ']').each(function (i, e) {
            var $el = $(e);
            if (!$el.is(':checked')) {
                return;
            }

            var $quantity = $el.closest('tr').find('.js--orderLine-quantity');
            order.order_lines[i].quantity = parseInt($quantity.val(), 10);
            selectedLines.push(order.order_lines[i]);
        });

        $.post(url, {
                order_id: order.order_id,
                amount: amount,
                remainingAmount: order.remaining_authorized_amount,
                description: description,
                selectedLines: JSON.stringify(selectedLines)
            }
        ).done(function (data) {
            $('#submit-' + action + '-Modal').modal('hide');
            $('#' + action + 'Modal').modal('hide');
            location.reload();
            createGrowlMessage(data);
        });

    });


    /**
     * Trigger resend of customer communication
     */
    var $resendCommunicationBtn = $('.js--resend-communication');
    $resendCommunicationBtn.click(function (e) {
        var $target = $(e.currentTarget);
        var url = $target.attr('data-url');
        var orderId = $target.attr('data-orderId');
        var captureId = $target.attr('data-captureId');
        var modal = $target.attr('data-modal');

        $.post(url, {
                order_id: orderId,
                capture_id: captureId
            }
        ).done(function (data) {
            $('#' + modal).modal('hide');
            $('#capturesModal').modal('hide');
            location.reload();
            createGrowlMessage(data);
        });
    });

    /**
     * Call api
     */
    var $callApiBtn = $('.js--call-api');
    $callApiBtn.click(function (e) {
        var $target = $(e.currentTarget);
        var url = $target.attr('data-url');
        var orderId = $target.attr('data-orderId');
        var amount = $target.attr('data-amount');

        $.post(url, {
                order_id: orderId,
                amount: amount
            }
        ).done(function (data) {
            $('#extend-auth-time-Modal').modal('hide');
            location.reload();
            createGrowlMessage(data);
        });
    });

    /**
     * Show Grow message
     * @param data
     */
    function createGrowlMessage(data) {
        var opt = {
            text: data.success ? data.message : data.errorMessage,
            title: data.success ? 'Success alert' : 'Danger alert'
        };
        parent.Shopware.Notification.createStickyGrowlMessage(opt);
    }
});
