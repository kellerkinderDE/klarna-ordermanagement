{namespace name="backend/bestit_order_management/history"}

{block name="history/main"}
    <!-- Modal -->
    <div class="modal fade" id="history-Modal{$i}" tabindex="-1" role="dialog"
         aria-labelledby="history-ModalLabel{$i}"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="btn btn-primary float-right btn-xs" data-dismiss="modal"
                            aria-label="Close">&times;
                    </button>
                    <span id="history-ModalLabel{$i}">{s name='history'}Details{/s}</span>
                </div>

                <div class="modal-body">
                    {if $log->isSuccessful()}
                        {if !$log->getCents()}
                            No further details.
                        {else}
                            <div>
                                <span class="text-strong">Amount: </span>{$log->getCents()}
                            </div>
                        {/if}
                    {else}
                        {if $log->getCents()}
                            <div>
                                <span class="text-strong">Amount: </span>{$log->getCents()}
                            </div>
                        {/if}

                        <div>
                            <span class="text-strong">{s name='klarnaOrderId'}Klarna Order Id: {/s}</span>{$log->getKlarnaOrderId()}
                        </div>
                        <div>
                            <span class="text-strong">{s name='klarnaErrorCode'}Klarna Error Code: {/s}</span>{$log->getErrorCode()}
                        </div>
                        <div>
                            <span class="text-strong">{s name='klarnaErrorMessages'}Error Messages:{/s}</span>
                            <ul class="error-messages">
                                {foreach $log->getErrorMessages() as $errorMessage}
                                    <li class="messages-text"><span>{$errorMessage}</span></li>
                                {/foreach}
                            </ul>
                        </div>
                        <div>
                            <span class="text-strong">{s name='klarnaCorrelationId'}Klarna Correlation Id: {/s}</span>{$log->getCorrelationId()}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/block}