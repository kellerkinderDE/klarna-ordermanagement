{namespace name="backend/bestit_order_management/refund"}

{block name="submit_refund/main"}
    <!-- Modal -->
    <div class="modal fade submit-refund" id="submit-refund-Modal" tabindex="-1" role="dialog"
         aria-labelledby="submit-refund-ModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                            aria-label="Close">&times;
                    </button>
                </div>
                <div class="modal-body">
                    {s name='submitRefundText'} Are you sure that you want to refund this amount?{/s}
                </div>
                <div class="confirm-box">
                    <a data-url="{url controller="BestitOrderManagement" action="createRefund"}"
                       data-order="{$order|json_encode|escape}"
                       href="#"
                       data-action="refund"
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