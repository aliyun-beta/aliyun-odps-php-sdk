<?php

/**
 * @author Nikolai Kordulla
 */
class PBString extends PBScalar
{
    var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;

    public function ParseFromReader($reader)
    {
        $length = $reader->next();
        $_oldpointer = $reader->get_pointer();
        $reader->add_pointer($length);

        // now add array from _oldpointer to pointer to the chunk array
        $this->value = $reader->get_message_from($_oldpointer);
    }

    public function getBytes()
    {
        return unpack("C*", $this->value);
    }

    /**
     * Serializes type
     */
    public function SerializeToString($rec = -1, $excludeColmns = array())
    {
        $string = '';

        if ($rec > -1) {
            $string .= $this->base128->set_value($rec << 3 | $this->wired_type);
        }

        $string .= $this->base128->set_value(strlen($this->value));
        $string .= $this->value;

        return $string;
    }
}

?>
