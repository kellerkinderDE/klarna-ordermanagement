{namespace name="backend/bestit_order_management/customer_communication"}

{block name="customer_communication/main"}
    <!-- Modal -->
    <div class="modal fade" id="resend-communication-Modal{$i}" tabindex="-1" role="dialog"
         aria-labelledby="resend-communication-ModalLabel{$i}"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                            aria-label="Close">&times;
                    </button>
                </div>
                <div class="modal-body">
                    {s name='ResendCommunicationText'} Are you sure that you want to resend the Klarna invoice to your customer?{/s}
                </div>
                <div class="confirm-box">
                    <a data-url="{url controller="BestitOrderManagement" action="resendCustomerCommunication"}"
                       data-orderId="{$orderId}"
                       data-captureId="{$captureId}"
                       href="#"
                       data-modal="resend-communication-Modal{$i}"
                       class="btn btn-primary js--resend-communication">
                        {s name='Proceed'}Proceed{/s}
                    </a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                        {s name='Abort'}Abort{/s}
                    </button>
                </div>
            </div>
        </div>
    </div>
{/block}