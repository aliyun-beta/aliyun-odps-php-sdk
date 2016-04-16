<?php

use ODPS\Core\OdpsUtil;

/**
 * @author Nikolai Kordulla
 */
class PBZigZag extends PBScalar
{
    var $wired_type = PBMessage::WIRED_VARINT;

    public function ParseFromReader($reader)
    {
        $this->value = ODPSUtil::unZigZagVal($reader->next());
    }

    public function getBytes()
    {
        return unpack("C*", pack("L", $this->value));
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

        $value = $this->base128->set_value(ODPSUtil::zigZagVal($this->value));
        $string .= $value;

        return $string;
    }
}

