<?php

use \ODPS\Core\OdpsUtil;

/**
 * Handle serialize and deserialize for ODPS data type datetime
 */
class PBDate extends PBBigint
{
    var $wired_type = PBMessage::WIRED_VARINT;

    public function ParseFromReader($reader)
    {
        $this->value = '';

        $timestamp = $reader->readSInt64();
        $date = new DateTime();
        //$date->setTimezone(new DateTimeZone("PRC"));
        $date->setTimestamp($timestamp / 1000);
        $this->value = $date;
    }

    public function getBytes()
    {
        $value = $this->value->getTimestamp() * 1000;
        return $this->get64bitsArray($value);
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

        $timeStamps = $this->value->getTimestamp() * 1000;
        $value = $this->base128->set_value(OdpsUtil::zigZagVal($timeStamps));
        $string .= $value;

        return $string;
    }

}
