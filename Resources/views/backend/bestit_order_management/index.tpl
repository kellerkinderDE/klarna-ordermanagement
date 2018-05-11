{extends file="parent:backend/bestit_order_management/base/layout.tpl"}
{namespace name="backend/bestit_order_management"}

{block name="content/main"}
    <fieldset>
        <legend>{s name='OrderDetails'}Bestelldetails{/s}</legend>

        <div class="flex-between">
            <div class="items-group amounts">

                <div class="text-left">
                    <div>
                        <span>{s name='TotalAmount'}Total Amount:{/s}</span>
                    </div>
                    <div>
                        <span>{s name='CapturedAmount'}Captured Amount:{/s}</span>
                    </div>
                    <div>
                        <span>{s name='RefundedAmount'}Refunded Amount:{/s}</span>
                    </div>
                    <div>
                        <span>{s name='RemainingAmount'}Remaining Amount:{/s}</span>
                    </div>
                </div>

                <div class="text-right">
                    <div>
                        <span class="text-strong">{$order.order_amount|bestitToMajorUnit|currency}</span>
                    </div>
                    <div>
                        <span class="text-strong">{$order.captured_amount|bestitToMajorUnit|currency}</span>
                    </div>
                    <div>
                        <span class="text-strong">{$order.refunded_amount|bestitToMajorUnit|currency}</span>
                    </div>
                    <div>
                        <span class="text-strong">{$order.remaining_authorized_amount|bestitToMajorUnit|currency}</span>
                    </div>
                </div>

                <div class="text-left p-left">
                    <div class="placeholder">&nbsp;</div>
                    <div>
                        <a class="{if $order.status === 'CANCELLED'}inactiveLink{/if}"
                           data-toggle="modal"
                           href="#capturesModal">{s name='CaptureDetails'}Details{/s}</a>
                    </div>
                    <div class="placeholder">&nbsp;</div>
                    <div>
                        <a class="{if $order.status === 'CANCELLED'}inactiveLink{/if}" data-toggle="modal"
                           href="#releaseModal">{s name='ReleasRemaining'}Release{/s}</a>
                    </div>
                </div>


            </div>

            <div class="items-group">

                <div class="text-left">
                    <div>
                        <span>{s name='KlarnaReference'}Klarna-Reference:{/s}</span>
                    </div>
                    <div>
                        <span>{s name='KlarnaStatus'}Klarna Status:{/s}</span>
                    </div>
                    <div>
                        <span>{s name='FraudStatus'}Fraud Status:{/s}</span>
                    </div>
                    <div>
                        <span>{s name='Expires'}Expires at:{/s}</span>
                    </div>
                </div>

                <div class="text-right">
                    <div>
                        <span class="text-strong">{$order.klarna_reference}</span>
                    </div>
                    <div>
                        <span class="text-strong">{$order.status}</span>
                    </div>
                    <div>
                        <span class="text-strong">{$order.fraud_status}</span>
                    </div>
                    <div>
                        <span class="text-strong">{$order.expires_at|date_format:"%d.%m.%Y-%H:%M"}</span>
                    </div>

                    <div>
                        <a class="{if $order.status === 'CANCELLED'}inactiveLink{/if}"
                           data-toggle="modal"
                           href="#extendAuthTimeModal">{s name='ExtendAuthTime'}Extend Authorization Time{/s}</a>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex-between">
            <div class="buttons-group">
                <button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#captureModal" {if $order.status === 'CANCELLED'} disabled{/if}>
                    {s name='Capture'}Capture{/s}
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal"
                        data-target="#refundModal" {if $order.status === 'CANCELLED'} disabled{/if}>
                    {s name='Refund'}Refund{/s}
                </button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-default" data-toggle="modal"
                        data-target="#cancel-order-Modal" {if $order.captured_amount > 0 || $order.refunded_amount > 0 || $order.status === 'CANCELLED'} disabled{/if}>
                    {s name='Cancel'}Cancel Order{/s}
                </button>
            </div>
        </div>

    </fieldset>

    <fieldset>
        <legend>{s name='KlarnaOrderHistory'}Klarna Bestellhistorie{/s}</legend>

        {include file="backend/bestit_order_management/view/history/history.tpl"}
    </fieldset>
{/block}