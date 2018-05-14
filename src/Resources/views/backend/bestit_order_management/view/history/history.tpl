{namespace name="backend/bestit_order_management/history"}

<table class="table table-bordered table-condensed">
    <thead>
    <tr>
        <td>#</td>
        <td>{s name='Time'}Zeitpunkt{/s}</td>
        <td>{s name='Message'}Meldung{/s}</td>
        <td>{s name='Details'}Details{/s}</td>
    </tr>
    </thead>
    <tbody>

    {foreach $logs as $i => $log}
        <tr>
            <td><span class="glyphicon {if $log->isSuccessful()} glyphicon-ok klarna-action-success {else} glyphicon-remove klarna-action-fail {/if}"></span></td>
            <td>{$log->getCreatedAt()->format('d.m.Y H:i:s')}</td>
            <td>{$log->getAction()|snippet:$log->getAction():'backend/bestit_order_management/history'}</td>
            <td><a class="link" data-toggle="modal" href="#history-Modal{$i}">Details</a></td>
        </tr>

        {include file="backend/bestit_order_management/view/history/details.tpl" i=$i}
    {/foreach}
    </tbody>
</table>