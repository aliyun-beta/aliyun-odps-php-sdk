<?php

/*
 * Handle serialize and deserialize for ODPS data type bigint
 */

class PBBigint extends PBScalar
{
    var $wired_type = PBMessage::WIRED_VARINT;

    public function ParseFromReader($reader)
    {
        $this->value = $reader->readSInt64();
    }

    public function getBytes()
    {
        return $this->get64bitsArray($this->value);
    }

    public function get64bitsArray($longVal)
    {
        $highMap = 0xffffffff00000000;
        $lowMap = 0x00000000ffffffff;
        $higher = ($longVal & $highMap) >> 32;
        $lower = $longVal & $lowMap;
        $packed = pack('N2', $higher, $lower);

        return array_reverse(unpack('C*', $packed));
    }

    /**
     * Serializes type
     */
    public function SerializeToString($rec = -1, $excludeColmns = array())
    {
        // first byte is length byte
        $string = '';

        if ($rec > -1) {
            $string .= $this->base128->set_value($rec << 3 | $this->wired_type);
        }

        $value = $this->base128->set_value($this->_zigZagVal($this->value));
        $string .= $value;

        return $string;
    }

    private function _zigZagVal($val)
    {
        return ($val << 1) ^ ($val >> 63);
    }
}
