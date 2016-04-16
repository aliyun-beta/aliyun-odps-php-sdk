<?php
/**
 * Including of all files needed to parse messages
 *
 * @author Nikolai Kordulla
 */
require_once dirname(__FILE__) . '/' . 'encoding/Base128Varint.php';
require_once dirname(__FILE__) . '/' . 'type/PBScalar.php';
require_once dirname(__FILE__) . '/' . 'type/PBString.php';
require_once dirname(__FILE__) . '/' . 'type/PBBigint.php';
require_once dirname(__FILE__) . '/' . 'type/PBDate.php';
require_once dirname(__FILE__) . '/' . 'type/PBZigZag.php';
require_once dirname(__FILE__) . '/' . 'type/PBInt.php';
require_once dirname(__FILE__) . '/' . 'type/PBBool.php';
require_once dirname(__FILE__) . '/' . 'type/PBDouble.php';
require_once dirname(__FILE__) . '/' . 'reader/PBInputReader.php';
require_once dirname(__FILE__) . '/' . 'reader/PBInputStreamReader.php';

/**
 * Abstract Message class
 *
 * @author Nikolai Kordulla
 */
abstract class PBMessage
{
    const TUNNEL_END_RECORD = "33553408";
    const TUNNEL_META_COUNT = "33554430";
    const TUNNEL_META_CHECKSUM = "33554431";

    const WIRED_VARINT = 0;
    const WIRED_64BIT = 1;
    const WIRED_LENGTH_DELIMITED = 2;
    const WIRED_START_GROUP = 3;
    const WIRED_END_GROUP = 4;
    const WIRED_32BIT = 5;
    const MODUS = 1;
    var $serializedMsg;

    // here are the field types
    var $base128;
    // the values for the fields
    var $fields = array();

    // type of the class
    var $values = array();

    // the value of a class
    var $wired_type = 2;

    // modus byte or string parse (byte for productive string for better reading and debuging)
    // 1 = byte, 2 = String
    var $value = null;

    // now use pointer for speed improvement
    // pointer to begin
    var $chunk = '';

    // chunk which the class not understands
    var $_d_string = '';

    // variable for Send method
    var $otherParams;
    protected $reader;

    /**
     * Constructor - initialize base128 class
     */
    public function __construct($reader = null, $otherParams = null)
    {
        $this->otherParams = $otherParams;
        $this->reader = $reader;
        $this->value = $this;
        $this->base128 = new Base128Varint(PBMessage::MODUS);
    }

    public function setPoint($pos)
    {
        if ($this->reader) {
            $this->reader->setPoint($pos);
        }
    }

    /**
     * Serializes the chunk
     *
     * @param String $stringinner - String where to append the chunk
     */
    public function _serialize_chunk(&$stringinner)
    {
        $stringinner .= $this->chunk;
    }

    /**
     * Decodes a Message and Built its things
     *
     * @param message as stream of hex example '1a 03 08 96 01'
     */
    public function ParseFromString($message)
    {
        $this->reader = new PBInputStringReader($message);
        $this->_ParseFromArray();
    }

    /**
     * Get the wired_type and field_type
     *
     * @param  $number as decimal
     * @return array wired_type, field_type
     */
    public function get_types($number)
    {
        $binstring = decbin($number);
        $types = array();
        $low = substr($binstring, strlen($binstring) - 3, strlen($binstring));
        $high = substr($binstring, 0, strlen($binstring) - 3) . '0000';
        $types['wired'] = bindec($low);
        //$types['field'] = bindec($binstring) >> 3;
        $types['field'] = $this->shr32(bindec($binstring), 3);
        return $types;
    }

    /**
     * Internal function
     */
    public function ParseFromArray()
    {
        $this->chunk = '';
        // read the length byte
        $length = $this->reader->next();
        // just take the splice from this array
        $this->_ParseFromArray($length);
    }

    /**
     * Sends the message via post request ['message'] to the url
     *
     * @param the url
     * @param the PBMessage class where the request should be encoded
     *
     * @return String - the return string from the request to the url
     */
    public function Send($url, &$class = null)
    {
        $ch = curl_init();
        $this->_d_string = '';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, '_save_string'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($this->SerializeToString()));
        $result = curl_exec($ch);

        if ($class != null) {
            $class->parseFromString($this->_d_string);
        }
        return $this->_d_string;
    }

    /**
     * Encodes a Message
     *
     * @return string the encoded message
     */
    public function SerializeToString($rec = -1, $excludeColmns = array())
    {
        $string = '';
        $stringinner = '';

        uksort($this->fields, "strnatcasecmp");
        foreach ($this->fields as $index => $field) {
            if (in_array($index, $excludeColmns)) {
                continue;
            }

            if (is_array($this->values[$index]) && count($this->values[$index]) > 0) {
                // make serialization for every array
                foreach ($this->values[$index] as $array) {
                    $newstring = '';
                    $newstring .= $array->SerializeToString($index);

                    $stringinner .= $newstring;
                }
            } else if ($this->values[$index] != null) {
                // wired and type
                $newstring = '';
                $newstring .= $this->values[$index]->SerializeToString($index);

                $stringinner .= $newstring;
            }
        }

        return $string . $stringinner;
    }

    /**
     * Fix Memory Leaks with Objects in PHP 5
     * http://paul-m-jones.com/?p=262
     *
     * thanks to cheton
     * http://code.google.com/p/pb4php/issues/detail?id=3&can=1
     */
    public function __destruct()
    {
        if (isset($this->reader)) {
            unset($this->reader);
        }
        if (isset($this->value)) {
            unset($this->value);
        }
        // base128
        if (isset($this->base128)) {
            unset($this->base128);
        }
        // fields
        if (isset($this->fields)) {
            foreach ($this->fields as $name => $value) {
                unset($this->$name);
            }
            unset($this->fields);
        }
        // values
        if (isset($this->values)) {
            foreach ($this->values as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $name2 => $value2) {
                        if (is_object($value2) AND method_exists($value2, '__destruct')) {
                            $value2->__destruct();
                        }
                        unset($value2);
                    }
                    if (isset($name2)) {
                        unset($value->$name2);
                    }
                } else {
                    if (is_object($value) AND method_exists($value, '__destruct')) {
                        $value->__destruct();
                    }
                    unset($value);
                }
                unset($this->values->$name);
            }
            unset($this->values);
        }
    }

    /**
     * Internal function
     */
    private function _ParseFromArray($length = 99999999, $lastWiredType = null)
    {
        $_begin = $this->reader->get_pointer();
        while ($this->reader->get_pointer() - $_begin < $length) {
            $next = $this->reader->next();
            if ($next === false) {
                break;
            }

            // now get the message type
            $messtypes = $this->get_types($next);


            if ($messtypes['wired'] == PBMessage::WIRED_LENGTH_DELIMITED || $messtypes['wired'] == PBMessage::WIRED_VARINT) {
                if (!isset($this->fields[$messtypes['field']])) {
                    throw new Exception('I dont understand this field number:' . $messtypes['field']);
                }


                if (is_array($this->values[$messtypes['field']])) {
                    $this->values[$messtypes['field']][] = new $this->fields[$messtypes['field']]($this->reader, $this->otherParams);
                    $index = count($this->values[$messtypes['field']]) - 1;
                    if ($messtypes['wired'] != $this->values[$messtypes['field']][$index]->wired_type) {
                        throw new Exception('Expected type:' . $messtypes['wired'] . ' but had ' . $this->fields[$messtypes['field']]->wired_type);
                    }
                    $this->values[$messtypes['field']][$index]->ParseFromArray();
                } else {
                    $this->values[$messtypes['field']] = new $this->fields[$messtypes['field']]($this->reader, $this->otherParams);
                    if ($messtypes['wired'] != $this->values[$messtypes['field']]->wired_type) {
                        throw new Exception('Expected type:' . $messtypes['wired'] . ' but had ' . $this->fields[$messtypes['field']]->wired_type);
                    }
                    $this->values[$messtypes['field']]->ParseFromArray();
                }
            } else {

                if ($this->lastWiredType === PBMessage::WIRED_LENGTH_DELIMITED) {
                    $consume = new PBString($this->reader);
                } else if ($this->lastWiredType === PBMessage::WIRED_VARINT) {
                    $consume = new PBInt($this->reader);
                } else {
                    throw new Exception('I dont understand this wired code:' . $this->lastWiredType);
                }
                $_oldpointer = $this->reader->get_pointer();
                $consume->ParseFromArray();
                // now add array from _oldpointer to pointer to the chunk array
                $this->chunk .= $this->reader->get_message_from($_oldpointer);

            }
        }
    }

    /**
     * Like java >>>
     *
     * @param  $x
     * @param  $bits
     * @return int|number
     */
    protected function shr32($x, $bits)
    {
        if ($bits <= 0) {
            return $x;
        }
        if ($bits >= 32) {
            return 0;
        }

        $bin = decbin($x);
        $l = mb_strlen($bin);

        if ($l > 32) {
            $bin = mb_substr($bin, $l - 32, 32);
        } elseif ($l < 32) {
            $bin = str_pad($bin, 32, '0', STR_PAD_LEFT);
        }

        return bindec(str_pad(mb_substr($bin, 0, 32 - $bits), 32, '0', STR_PAD_LEFT));
    }

    /**
     * Add an array value
     *
     * @param int - index of the field
     */
    protected function _add_arr_value($index)
    {
        return $this->values[$index][] = new $this->fields[$index]();
    }

    /**
     * Set an array value - @TODO failure check
     *
     * @param int - index of the field
     * @param int - index of the array
     * @param object - the value
     */
    protected function _set_arr_value($index, $index_arr, $value)
    {
        $this->values[$index][$index_arr] = $value;
    }

    /**
     * Remove the last array value
     *
     * @param int - index of the field
     */
    protected function _remove_last_arr_value($index)
    {
        array_pop($this->values[$index]);
    }

    /**
     * Get a value
     *
     * @param id of the field
     */
    protected function _get_value($index)
    {
        if ($this->values[$index] == null) {
            return null;
        }
        return $this->values[$index]->value;
    }

    /**
     * Set an value
     *
     * @param int - index of the field
     * @param Mixed value
     */
    public function _set_value($index, $value)
    {
        $this->values[$index] = new $this->fields[$index]();
        $this->values[$index]->value = $value;
    }

    /**
     * Get array value
     *
     * @param id of the field
     * @param value
     */
    protected function _get_arr_value($index, $value)
    {
        return $this->values[$index][$value];
    }

    /**
     * Get array size
     *
     * @param id of the field
     */
    protected function _get_arr_size($index)
    {
        return count($this->values[$index]);
    }

    /**
     * Helper method for send string
     */
    protected function _save_string($ch, $string)
    {
        $this->_d_string .= $string;
        $content_length = strlen($this->_d_string);
        return strlen($string);
    }

}

?>
