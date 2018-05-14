{namespace name="backend/bestit_order_management/release"}

{block name="release/main"}
    <!-- Modal -->
    <div class="modal release" id="releaseModal" tabindex="-1" role="dialog" aria-labelledby="releaseModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="float-right btn btn-primary btn-xs" data-dismiss="modal"
                            aria-label="Close">&times;
                    </button>
                </div>
                <div class="modal-body">

                    <span>
                        {s name='ReleaseText'}Click on Release, if you want to release the displayed amount of <span class="text-strong">{$order.remaining_authorized_amount|bestitToMajorUnit|currency}</span>{/s}
                    </span>

                    <a data-url="{url controller="BestitOrderManagement" action="release"}"
                       data-orderId="{$order.order_id}"
                       href="#"
                       class="btn btn-primary btn-block btn-release js--call-api">
                        {s name='Release'}Release{/s}
                    </a>
                </div>
            </div>
        </div>
    </div>
{/block}