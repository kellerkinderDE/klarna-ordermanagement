<?php

namespace BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use BestitKlarnaOrderManagement\Components\Factory\Serializer as FactorySerializer;
use BestitKlarnaOrderManagement\Components\Transformer\OrderDetailTransformerInterface;
use BestitKlarnaOrderManagement\Components\Trigger\Action\PartialCapture;
use BestitKlarnaOrderManagement\Tests\BaseTestCase;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Status;

/**
 * Test Class for PartialCapture
 *
 * @package BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;
 */
class PartialCaptureTest extends TestCase
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
     * @param int|null $remainingAuthorizedAmount
     * @param string $transactionId
     * @param int $amountToCapture
     * @param bool $responseIsError
     * @param int|null $expected
     *
     * @dataProvider provideValidOrders
     */
    public function testTrigger($remainingAuthorizedAmount, $transactionId, $amountToCapture, $responseIsError, $expected)
    {
        $swOrder = $this->createMock(SwOrder::class);
        $swOrder
            ->method('getTransactionId')
            ->willReturn($transactionId);

        $calculator = $this->createMock(CalculatorInterface::class);
        $calculator
            ->method('toCents')
            ->withAnyParameters()
            ->willReturn($amountToCapture);

        $klarnaOrder = $this->createMock(KlarnaOrder::class);
        $klarnaOrder->remainingAuthorizedAmount = $remainingAuthorizedAmount;

        $response = $this->createMock(Response::class);
        $response
            ->method('isError')
            ->willReturn($responseIsError);

        $captureFacade = $this->createMock(CaptureFacade::class);
        $captureFacade
            ->method('create')
            ->willReturn($response);

        $detailTransformer = $this->createMock(OrderDetailTransformerInterface::class);
        $detailTransformer
            ->method('createLineItem')
            ->withAnyParameters()
            ->willReturn(new LineItem());

        $serializer = FactorySerializer::create();

        $partialCapture = new PartialCapture($captureFacade, $calculator, $detailTransformer, $serializer);
        $return = $partialCapture->trigger($swOrder, $klarnaOrder, $this->swOrderDetail);

        $this->assertEquals($expected, $return);
    }

    public function provideValidOrders()
    {

        return [
            'remaining_authorized_amount_bigger_amount_to_capture_iserror_true' => [
                '1000',
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                '900',
                true,
                Status::PAYMENT_STATE_REVIEW_NECESSARY
            ],
            'remaining_authorized_amount_bigger_amount_to_capture_iserror_false' => [
                '1000',
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                '900',
                false,
                Status::PAYMENT_STATE_PARTIALLY_PAID
            ],
            'remaining_authorized_amount_smaller_amount_to_capture_iserror_false' => [
                '900',
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                '1000',
                false,
                null
            ],
            'klarna_order_id_empty' => [
                '1000',
                '',
                '999',
                false,
                null
            ],
        ];
    }

}
