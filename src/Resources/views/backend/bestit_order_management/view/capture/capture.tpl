{namespace name="backend/bestit_order_management/capture"}

{block name="capture/main"}
    <!-- Modal -->
    <div class="modal capture" id="captureModal" tabindex="-1" role="dialog" aria-labelledby="captureModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                            aria-label="Close">&times;
                    </button>
                    <span id="captureModalLabel">{s name='Capture'}Capture{/s}</span>
                </div>

                <div class="modal-body">

                    <div class="capture-table">
                        <table class="table table-bordered table-condensed">
                            <thead>
                            <tr>
                                <th><input class="js--select-all-checkboxes" type="checkbox" data-action="capture"></th>
                                <th>{s name='Article'}Article{/s}</th>
                                <th>{s name='Quantity'}Quantity{/s}</th>
                                <th>{s name='Price'}Price{/s}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $order.order_lines as $i => $line}
                                <tr>
                                    <td>
                                        <input class="js--orderLine-checkbox" type="checkbox"
                                               name="capture_order_line[]"
                                               data-action="capture"
                                               data-price="{$line.unit_price|bestitToMajorUnit}">
                                    </td>
                                    <td>{$line.name}</td>
                                    <td>
                                        <input type="number" value="{$line.quantity}"
                                               data-action="capture"
                                               class="js--orderLine-quantity"/>
                                    </td>
                                    <td>{$line.unit_price|bestitToMajorUnit|currency}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>

                    <div class="capture-lines">

                        <div class="flex-between w-50 float-right text-strong">
                            <span>{s name='captureAmount'}Capture Amount:{/s}</span>
                            <input type="number" name="sum" class="js--sum capture capture-sum" value="0" min="0" max={$order.remaining_authorized_amount|bestitToMajorUnit} data-action="capture">
                        </div>

                        <div class="flex-between w-50 float-right">
                            <span>{s name='captureAuthAmount'}Total authorized Klarna amount:{/s}</span>
                            <span>{$order.order_amount|bestitToMajorUnit|currency}</span>
                        </div>

                        <div class="flex-between w-50 float-right">
                            <span>{s name='captureAlreadyCapturedAmount'}Already captured amount:{/s}</span>
                            <span>{$order.captured_amount|bestitToMajorUnit|currency}</span>
                        </div>

                        <div class="flex-between w-50 float-right">
                            <span>{s name='captureRemainingAuthAmount'}Remaining authorized amount:{/s}</span>
                            <span>{$order.remaining_authorized_amount|bestitToMajorUnit|currency}</span>
                        </div>

                    </div>

                    <div class="capture-comment">
                        <h6>{s name='captureComment'} Capture-Comment:{/s}</h6>
                        <textarea rows="5" cols="65" class="js--comment capture" name="comment"></textarea>
                    </div>

                    <div>
                        <button type="button" class="btn btn-primary float-right js-confirmation-btn" id="capture-btn" data-order="{$order|json_encode|escape}" data-action="capture" data-toggle="modal"
                                data-target="#submit-capture-Modal">{s name='Capture'}Capture{/s}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
{/block}