{namespace name="backend/bestit_order_management/capture"}

{block name="submit_capture/main"}
    <!-- Modal -->
    <div class="modal fade" id="submit-capture-Modal" tabindex="-1" role="dialog"
         aria-labelledby="submit-capture-ModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                            aria-label="Close">&times;
                    </button>
                </div>
                <div class="modal-body">
                    {s name='submitCaptureText'} Are you sure that you want to capture an amount that is different from the actual positions?{/s}
                </div>
                <div class="confirm-box">
                    <a data-url="{url controller="BestitOrderManagement" action="createCapture"}"
                       data-order="{$order|json_encode|escape}"
                       href="#"
                       data-action="capture"
                       class="btn btn-primary js--submit-orderAction">
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