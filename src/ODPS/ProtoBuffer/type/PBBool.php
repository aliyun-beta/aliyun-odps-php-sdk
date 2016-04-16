<?php


/**
 * Handle serialize and deserialize for ODPS data type bool
 */
class PBBool extends PBScalar
{
    var $wired_type = PBMessage::WIRED_VARINT;

    public function getBytes()
    {
        return $this->value ? [1] : [0];
    }

    public function ParseFromReader($reader)
    {
        $this->value = $reader->next();
        $this->value = ($this->value != 0) ? true : false;
    }

    public function SerializeToString($rec = -1, $excludeColmns = array())
    {
        // first byte is length byte
        $string = '';

        if ($rec > -1) {
            $string .= $this->base128->set_value($rec << 3 | $this->wired_type);
        }

        $value = $this->base128->set_value($this->value ? 1 : 0);
        $string .= $value;

        return $string;
    }
}

?>
