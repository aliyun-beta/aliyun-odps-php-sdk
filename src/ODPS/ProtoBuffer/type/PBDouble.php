<?php

/**
 * Handle serialize and deserialize for ODPS data type double
 */
class PBDouble extends PBScalar
{
    var $wired_type = PBMessage::WIRED_64BIT;

    public function ParseFromReader($reader)
    {
        $this->value = '';

        // just extract the string
        $pointer = $reader->get_pointer();
        $reader->add_pointer(8);
        $item = unpack('d', $reader->get_message_from($pointer));
        $this->value = $item["1"];
    }

    public function getBytes()
    {
        return unpack("C*", pack("d", $this->value));
    }

    /**
     * Serializes type
     */
    public function SerializeToString($rec = -1, $excludeColmns = array())
    {
        $string = '';
        if ($rec > -1) {
            $string .= $this->base128->set_value(
                $rec << 3 |
                $this->wired_type
            );
        }

        $string .= pack("d", (double)$this->value);

        return $string;
    }
}

?>
