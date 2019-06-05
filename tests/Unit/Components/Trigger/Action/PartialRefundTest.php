<?php

namespace BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Facade\Refund as RefundFacade;
use BestitKlarnaOrderManagement\Components\Factory\Serializer as FactorySerializer;
use BestitKlarnaOrderManagement\Components\Transformer\OrderDetailTransformerInterface;
use BestitKlarnaOrderManagement\Components\Trigger\Action\PartialRefund;
use BestitKlarnaOrderManagement\Tests\BaseTestCase;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Status;

/**
 * Test Class for PartialRefund
 *
 * @package BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;
 */
class PartialRefundTest extends TestCase
{
    /** @var SwOrderDetail */
    protected $swOrderDetail;
    /** @var OrderDetailTransformerInterface */
    protected $detailTransformer;

    protected function setUp()
    {
        $this->swOrderDetail = $this->createMock(SwOrderDetail::class);
        $this->detailTransformer = $this->createMock(OrderDetailTransformerInterface::class);
    }

    /**
     * @param int|null $capturedAmount
     * @param int|null $refundedAmount
     * @param int $amountToRefund
     * @param string $transactionId
     * @param bool $responseIsError
     * @param int|null $expected
     *
     * @dataProvider provideValidOrders
     */
    public function testTrigger($capturedAmount, $refundedAmount, $amountToRefund, $transactionId, $responseIsError, $expected)
    {
        $swOrder = $this->createMock(SwOrder::class);
        $swOrder
            ->method('getTransactionId')
            ->willReturn($transactionId);

        $calculator = $this->createMock(CalculatorInterface::class);
        $calculator
            ->method('toCents')
            ->withAnyParameters()
            ->willReturn($amountToRefund);

        $klarnaOrder = $this->createMock(KlarnaOrder::class);
        $klarnaOrder->capturedAmount = $capturedAmount;
        $klarnaOrder->refundedAmount = $refundedAmount;

        $response = $this->createMock(Response::class);
        $response
            ->method('isError')
            ->willReturn($responseIsError);

        $refundFacade = $this->createMock(RefundFacade::class);
        $refundFacade
            ->method('create')
            ->willReturn($response);

        $detailTransformer = $this->createMock(OrderDetailTransformerInterface::class);
        $detailTransformer
            ->method('createLineItem')
            ->withAnyParameters()
            ->willReturn(new LineItem());

        $serializer = FactorySerializer::create();

        $partialCapture = new PartialRefund($refundFacade, $calculator, $detailTransformer, $serializer);
        $return = $partialCapture->trigger($swOrder, $klarnaOrder, $this->swOrderDetail);

        $this->assertEquals($expected, $return);
    }

    public function provideValidOrders()
    {

        return [
                'refundable_amount_bigger_amount_to_refund_response_iserror_false' => [
                    1000,
                    100,
                    900,
                    'a7a1855f-330f-7bda-a1e7-70771df09673',
                    false,
                    Status::PAYMENT_STATE_RE_CREDITING
                ],
                'refundable_amount_bigger_amount_to_refund_response_iserror_true' => [
                    1000,
                    100,
                    900,
                    'a7a1855f-330f-7bda-a1e7-70771df09673',
                    true,
                    Status::PAYMENT_STATE_REVIEW_NECESSARY
                ],
                'refundable_amount_smaller_amount_to_refund' => [
                    1000,
                    500,
                    900,
                    'a7a1855f-330f-7bda-a1e7-70771df09673',
                    false,
                    null
                ],
                'klarna_order_id_empty' => [
                    1000,
                    100,
                    900,
                    '',
                    false,
                    null
                ],
                'response_iserror_true' => [
                    1000,
                    100,
                    900,
                    'a7a1855f-330f-7bda-a1e7-70771df09673',
                    true,
                    Status::PAYMENT_STATE_REVIEW_NECESSARY
                ],
        ];
    }

}
