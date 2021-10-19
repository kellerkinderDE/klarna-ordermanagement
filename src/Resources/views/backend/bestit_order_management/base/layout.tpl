<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{link file="backend/_resources/styles/indexom.css"}"/>
    <script src="{link file="backend/_resources/lib/jquery-3.3.1.min.js"}"></script>
    <link rel="stylesheet" href="{link file="backend/_resources/lib/bootstrap/css/bootstrap.css"}"/>
    <link rel="stylesheet" type="text/css" href="{link file="backend/_resources/styles/bootstrap-theme-shopware.css"}"/>
    <script src="{link file="backend/_resources/lib/bootstrap/js/bootstrap.js"}"></script>
</head>

<body class="order-management" role="document">

{if $success}
        <div class="theme-showcase" role="main">
            {block name="content/main"}{/block}
            {include file="backend/bestit_order_management/view/capture/capture.tpl"}
            {include file="backend/bestit_order_management/view/confirmation-popups/cancel-order.tpl"}
            {include file="backend/bestit_order_management/view/confirmation-popups/extend-auth-time.tpl"}
            {include file="backend/bestit_order_management/view/confirmation-popups/release.tpl"}
            {include file="backend/bestit_order_management/view/capture/submit-capture.tpl"}
            {include file="backend/bestit_order_management/view/refund/refund.tpl"}
            {include file="backend/bestit_order_management/view/refund/submit-refund.tpl"}
            {include file="backend/bestit_order_management/view/capture/captures.tpl"}
        </div>
{else}
    {include file="backend/bestit_order_management/view/error.tpl"}
{/if}
</body>

<script type="text/javascript" src="{link file="backend/_resources/js/order-management.js"}"></script>

</html>
