{namespace name="backend/bestit_order_management/refund"}

{block name="refund/main"}
    <!-- Modal -->
    <div class="modal refund" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                            aria-label="Close">
                        &times;
                    </button>
                    <span id="refundModalLabel">{s name='Refund'}Refund{/s}</span>
                </div>

                <div class="modal-body">
                    <div class="refund-table">
                        <table class="table table-bordered table-condensed">
                            <thead>
                            <tr>
                                <th><input class="js--select-all-checkboxes" type="checkbox" data-action="refund"></th>
                                <th>{s name='Article'}Article{/s}</th>
                                <th>{s name='Quantity'}Quantity{/s}</th>
                                <th>{s name='Price'}Price{/s}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $order.order_lines as $line}
                                <tr>
                                    <td>
                                        <input class="js--orderLine-checkbox" type="checkbox"
                                               name="refund_order_line[]"
                                               data-action="refund"
                                               data-price="{$line.unit_price|bestitToMajorUnit}">
                                    </td>
                                    <td>{$line.name}</td>
                                    <td>
                                        <input type="number" value="{$line.quantity}"
                                               data-action="refund"
                                               class="js--orderLine-quantity"/>
                                    </td>
                                    <td>{$line.unit_price|bestitToMajorUnit|currency}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>

                    <div class="refund-lines">

                        <div class="flex-between w-50 float-right text-strong">
                            <span>{s name='RefundAmount'}Refund amount:{/s}</span>
                            <input type="text" name="summery" class="js--sum refund refund-sum" value="0" min="0" max={$order.captured_amount|bestitToMajorUnit} data-action="refund">
                        </div>

                        <div class="flex-between w-50 float-right">
                            <span>{s name='RefundableAmount'}Maximum refundable amount:{/s}</span>
                            <span>{$order.captured_amount|bestitToMajorUnit|currency}</span>
                        </div>

                        <div class="flex-between w-50 float-right">
                            <span>{s name='RefundAlreadyRefundedAmount'}Already refunded amount:{/s}</span>
                            <span>{$order.refunded_amount|bestitToMajorUnit|currency}</span>
                        </div>

                        <div class="flex-between w-50 float-right">
                            <span>{s name='RefundRemainingAuthAmount'}Remaining refundable amount:{/s}</span>
                            <span>{($order.captured_amount - $order.refunded_amount)|bestitToMajorUnit|currency}</span>
                        </div>

                    </div>

                    <div class="refund-comment">
                        <h6>{s name='refundComment'} Refund-Comment:{/s}</h6>
                        <textarea rows="5" cols="65" class="js--comment refund" name="comment"> </textarea>
                    </div>

                    <div>
                        <button type="button" class="btn btn-primary float-right js-confirmation-btn" id="refund-btn" data-order="{$order|json_encode|escape}" data-action="refund" data-toggle="modal"
                                data-target="#submit-refund-Modal">{s name='Refund'}Refund{/s}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
{/block}
