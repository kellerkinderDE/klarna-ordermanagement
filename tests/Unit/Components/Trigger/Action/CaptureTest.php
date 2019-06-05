<?php

namespace BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use BestitKlarnaOrderManagement\Components\Trigger\Action\Capture;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Status;

/**
 * Test Class for Capture
 *
 * @package BestitKlarnaOrderManagement\Tests\Unit\Components\Trigger\Action;
 */
class CaptureTest extends TestCase
{
    /** @var SwOrderDetail */
    protected $swOrderDetail;

    protected function setUp()
    {
        $this->swOrderDetail = $this->createMock(SwOrderDetail::class);
    }

    /**
     * @param int|null $remainingAuthorizedAmount
     * @param string $transactionId
     * @param bool $responseIsError
     * @param int|null $expected
     *
     * @dataProvider provideValidOrders
     */
    public function testTrigger($remainingAuthorizedAmount, $transactionId, $responseIsError, $expected)
    {
        $swOrder = $this->createMock(SwOrder::class);
        $swOrder
            ->method('getTransactionId')
            ->willReturn($transactionId);

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

        $capture = new Capture($captureFacade);
        $return = $capture->trigger($swOrder, $klarnaOrder, $this->swOrderDetail);

        $this->assertEquals($expected, $return);
    }

    public function provideValidOrders()
    {

        return [
            'remaining_authorized_amount_equals_zero' => [
                0,
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                false,
                null
            ],
            'remaining_authorized_amount_smaller_zero' => [
                '-1',
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                false,
                null
            ],
            'klarna_order_id_empty' => [
                100,
                '',
                false,
                null
            ],
            'response_is_error_true' => [
                '100',
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                true,
                Status::PAYMENT_STATE_REVIEW_NECESSARY
            ],
            'response_is_error_false' => [
                100,
                'a7a1855f-330f-7bda-a1e7-70771df09673',
                false,
                Status::PAYMENT_STATE_COMPLETELY_PAID
            ],
        ];
    }

}
