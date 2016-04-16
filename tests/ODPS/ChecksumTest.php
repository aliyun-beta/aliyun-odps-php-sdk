<?php

use \ODPS\Services\Tunnel\Protobuf\OdpsChecksum;

class ChecksumTest extends \PHPUnit_Framework_TestCase
{
    public function testIntegerChecksum()
    {
        $bytes = unpack("C*", "sample123");
        $checksum = new \ODPS\Services\Tunnel\Protobuf\OdpsChecksum();
        $checksum->update("sample123");
        print $this->debugNum($checksum->getValue()) . "\n";

    }

    private function debugNum($num)
    {
        print  decbin($num) . "\n";
    }
}
