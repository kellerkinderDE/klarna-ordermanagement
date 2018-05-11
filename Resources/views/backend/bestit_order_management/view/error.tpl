{namespace name="backend/bestit_order_management/error"}

{block name="error/main"}
    <div class="flex-column text-center">

        <div class="">
            <span class="p-bottom">{s name='ErrorText'}Unfortunately, the order could not be intercepted from Klarna{/s}</span>
            <span class="p-bottom">{s name='TryAgain'}please try again in few minutes{/s}</span>
            <div class="p-bottom">
                <a href="#" class="btn btn-secondary">
                    {s name='refresh'}Refresh{/s}
                </a>
            </div>
        </div>

        <div class="p-bottom"><span class="text-strong">{s name='klarnaOrderId'}Klarna Order Id: {/s}</span>{$klarna_id}</div>
        <div class="p-bottom"><span class="text-strong">{s name='klarnaErrorCode'}Klarna Error Code: {/s}</span>{$error->errorCode}</div>

        <div class="p-bottom">
            <span class="text-strong">{s name='klarnaErrorMessages'}Error Messages:{/s}</span>
            <ul class="error-messages">
                {foreach $error->errorMessages as $errorMessage}
                    <li><span class="error-message">{$errorMessage}</span></li>
                {/foreach}
            </ul>
        </div>

        <div class="p-bottom">
            <span class="text-strong">{s name='klarnaCorrelationId'}Klarna Correlation Id: {/s}</span>
            {$error->correlationId}
        </div>

        <span class="p-bottom">
            {s name='Support1'}Please contact the
                <a target="_blank" href="https://www.klarna.com/de/verkaeufer/haendlersupport/">Klarna support</a>
                if the problem still exists{/s}
        </span>
    </div>
{/block}