<?php

/**
 * @author Nikolai Kordulla
 */
class PBInt extends PBScalar
{
    var $wired_type = PBMessage::WIRED_VARINT;

    public function ParseFromReader($reader)
    {
        $this->value = $reader->readUInt32();
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

        $value = $this->base128->set_value($this->value);
        $string .= $value;

        return $string;
    }
}

?>
