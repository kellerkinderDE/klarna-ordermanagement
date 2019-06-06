<?php

namespace BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Facade\Refund as RefundFacade;
use BestitKlarnaOrderManagement\Components\Trigger\Action\Refund;
use BestitKlarnaOrderManagement\Tests\BaseTestCase;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Status;

/**
 * Test Class for Refund
 *
 * @package BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;
 */
class RefundTest extends TestCase
{
    /** @var SwOrderDetail */
    protected $swOrderDetail;

    protected function setUp()
    {
        $this->swOrderDetail = $this->createMock(SwOrderDetail::class);
    }

    /**
     * @param string $transactionId
     * @param int|null $capturedAmount
     * @param int|null $refundedAmount
     * @param bool $orderFacadeResponseIsError
     * @param bool $refundFacadeResponseIsError
     * @param int|null $expected
     *
     * @dataProvider provideValidOrders
     */
    public function testTrigger($transactionId, $capturedAmount, $refundedAmount, $orderFacadeResponseIsError, $refundFacadeResponseIsError, $expected)
    {
        $swOrder = $this->createMock(SwOrder::class);
        $swOrder
            ->method('getTransactionId')
            ->willReturn($transactionId);

        $klarnaOrder = $this->createMock(KlarnaOrder::class);
        $klarnaOrder->capturedAmount = $capturedAmount;
        $klarnaOrder->refundedAmount = $refundedAmount;

        $refundFacadeResponse = $this->createMock(Response::class);
        $refundFacadeResponse
            ->method('isError')
            ->willReturn($refundFacadeResponseIsError);

        $refundFacade = $this->createMock(RefundFacade::class);
        $refundFacade
            ->method('create')
            ->willReturn($refundFacadeResponse);

        $orderFacadeResponse = $this->createMock(Response::class);
        $orderFacadeResponse
            ->method('isError')
            ->willReturn($orderFacadeResponseIsError);

        $orderFacade = $this->createMock(OrderFacade::class);
        $orderFacade
            ->method('cancel')
            ->withAnyParameters()
            ->willReturn($orderFacadeResponse);

        $refund = new Refund($orderFacade, $refundFacade);
        $return = $refund->trigger($swOrder, $klarnaOrder, $this->swOrderDetail);

        $this->assertEquals($expected, $return);
    }

    public function provideValidOrders()
    {

        return [
            'klarna_order_id_empty' => [
                '',
                0,
                500,
                true,
                false,
                null
            ],
            'captured_amount_equals_zero_order_facade_response_is_error_true' => [
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                0,
                500,
                true,
                false,
                Status::PAYMENT_STATE_REVIEW_NECESSARY
            ],
            'captured_amount_equals_zero_order_facade_response_is_error_false' => [
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                0,
                500,
                false,
                false,
                Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED
            ],
            'captured_amount_equals_refund_amount' => [
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                500,
                500,
                false,
                false,
                null
            ],
            'captured_amount_bigger_refund_amount_refund_facade_response_is_error_true' => [
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                1000,
                500,
                false,
                true,
                Status::PAYMENT_STATE_REVIEW_NECESSARY
            ],
            'captured_amount_bigger_refund_amount_refund_facade_response_is_error_false' => [
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                1000,
                500,
                false,
                false,
                Status::PAYMENT_STATE_RE_CREDITING
            ],
            'captured_amount_bigger_refund_amount_refund_facade_response_is_error_false_remaining_authorized_amount_equals_zero' => [
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                1000,
                500,
                false,
                false,
                Status::PAYMENT_STATE_RE_CREDITING
            ],
        ];
    }

}
