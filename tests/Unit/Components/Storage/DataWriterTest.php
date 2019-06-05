<?php

namespace BestitKlarnaOrderManagement\Tests\Unit\Components\Storage;

use BestitKlarnaOrderManagement\Components\Storage\DataWriter;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

/**
 * Test Class for DataWriter
 *
 * @package BestitKlarnaOrderManagement\Tests\Unit\Components\Storage;
 */
class DataWriterTest extends TestCase
{
    public function testUpdatePaymentStatus()
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->method('update')
            ->willReturn('1');

        $dataWriter = new DataWriter($connection);

        $return = $dataWriter->updatePaymentStatus('', '');
        $this->assertEquals(0, $return);

        $return = $dataWriter->updatePaymentStatus('a7a1855f-330f-7bda-a1e7-70771df09673', '');
        $this->assertEquals(1, $return);
    }
}
