{namespace name="backend/bestit_order_management"}

{block name="extend_auth_time/main"}
    <!-- Modal -->
    <div class="modal" id="extendAuthTimeModal" tabindex="-1" role="dialog" aria-labelledby="extendAuthTimeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                            aria-label="Close">&times;
                    </button>
                </div>
                <div class="modal-body">
                    {s name='ExtendAuthTimeText'} Are you sure that you want to extend authorization Time?{/s}
                </div>
                <div class="confirm-box">
                    <a data-url="{url controller="BestitOrderManagement" action="extendAuthTime"}"
                       data-orderId="{$order.order_id}"
                       href="#"
                       class="btn btn-primary js--call-api">
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