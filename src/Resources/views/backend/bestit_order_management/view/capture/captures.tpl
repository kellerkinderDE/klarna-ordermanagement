{namespace name="backend/bestit_order_management/captures"}

{block name="captures/main"}
    <!-- Modal -->
    <div class="modal capture" id="capturesModal" tabindex="-1" role="dialog" aria-labelledby="capturesModalLabel"
         aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                        aria-label="Close">&times;
                </button>
                <span id="capturesModalLabel">{s name='Captures'}Captures{/s}</span>
            </div>
            <div class="modal-body">

                {foreach $order.captures as $i => $capture}
                    <div class="capture-content">

                        <div class="m-bottom">
                            <span class="text-strong">
                                {s name='Capture'}Capture {/s}{$i+1}:
                            </span>
                            <span>
                                {$capture.captured_at|date_format:"%d.%m.%Y-%H:%M"}
                            </span>
                        </div>

                        <div class="capture-table">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                    <th>{s name='Article'}Article{/s}</th>
                                    <th>{s name='Quantity'}Quantity{/s}</th>
                                    <th>{s name='Price'}Price{/s}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $capture.order_lines as $line}
                                    <tr>
                                        <td>{$line.name}</td>
                                        <td>{$line.quantity}</td>
                                        <td>{$line.total_amount|bestitToMajorUnit|currency}</td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>

                        <div class="text-right text-strong">
                            <span>{s name='captureAmount'}Capture Amount:{/s}</span>
                            <span> {$capture.captured_amount|bestitToMajorUnit|currency} </span>
                        </div>

                        <div class="m-bottom">
                            <button type="button"
                                    class="btn btn-primary m-bottom" data-toggle="modal"
                                    data-target="#resend-communication-Modal{$i}">
                                {s name='ResendComm'}Resend Customer Communication{/s}
                            </button>
                        </div>
                    </div>
                    {include file="backend/bestit_order_management/view/capture/customer-communication.tpl" i=$i orderId=$order.order_id captureId=$capture.capture_id }

                {/foreach}
            </div>
        </div>
    </div>
{/block}