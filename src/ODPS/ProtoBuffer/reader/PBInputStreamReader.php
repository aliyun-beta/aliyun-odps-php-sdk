<?php

use \ODPS\Http\RequestCore;

/**
 * Class PBInputStreamReader is the actual stream reader which implemented chunked data reading
 */
class PBInputStreamReader extends PBInputReader
{
    private $socket;
    private $chunkDataLen = 0;
    private $removed = 0;
    private $headers;
    private $lastReadedLine;

    public function __construct($httpSocket)
    {
        parent::__construct(null);
        $this->string = null;
        $this->socket = $httpSocket;
    }

    public function readUInt32()
    {
        return $this->readRawVarint32(false);
    }

    public function readRawVarint32($is_string = false)
    {
        while (true) {
            $currenctStr = $this->_checkCachedReponseBody(1);

            if ($currenctStr === null) {
                return null;
            }

            $string = $this->string[$this->pointer++];
            $this->removed++;
            $this->_reduceCachedReponseBody();

            if ($is_string == true) {
                return ord($string);
            }

            $tmp = unpack("c", $string)[1];
            // $tmp = ord($string);

            if ($tmp >= 0) {
                return $tmp;
            }

            $result = $tmp & 0x7f;
            if (($tmp = $this->readRawByte()) >= 0) {
                $result += $tmp << 7;
            } else {
                $result += ($tmp & 0x7f) << 7;
                if (($tmp = $this->readRawByte()) >= 0) {
                    $result += $tmp << 14;
                } else {
                    $result += ($tmp & 0x7f) << 14;
                    if (($tmp = $this->readRawByte()) >= 0) {
                        $result += $tmp << 21;
                    } else {
                        $result += ($tmp & 0x7f) << 21;
                        $result |= ($tmp = $this->readRawByte()) << 28;
                        if ($tmp < 0) {
                            // Discard upper 32 bits.
                            for ($i = 0; $i < 5; $i++) {
                                if ($this->readRawByte() >= 0) {
                                    return $result;
                                }
                            }

                        }
                    }
                }
            }
            return $result;
        }
    }

    /**
     * Read response body from socket
     *
     * @return string The read result
     */
    public function readMore()
    {
        if (!$this->headers) {
            while ($data = fgets($this->socket)) {
                if (is_array($this->headers)) {
                    $this->_readChunkedData($data);
                    return $data;
                } else {
                    if ($this->_endsWith($this->lastReadedLine, "\r\n") && $this->_startsWith($data, "\r\n")) {
                        $this->headers = RequestCore::parseHttpSocketHeader($this->headers);
                    } else {
                        $this->headers .= $data;
                    }

                    $this->lastReadedLine = $data;
                }
            }
        } else {
            if ($data = fgets($this->socket)) {
                $this->_readChunkedData($data);
            }

            return $data;
        }
    }

    /**
     * get the next
     *
     * @param boolean $is_string - if set to true only one byte is read
     */
    public function readRawByte()
    {
        $this->_checkCachedReponseBody(1);
        $this->setPoint(0);
        $string = $this->string[$this->pointer++];
        $this->removed++;
        $this->_reduceCachedReponseBody();
        return unpack("c", $string)[1];
    }

    public function readSInt64()
    {
        $this->value = '';

        // just extract the string
        $this->setPoint(0);
        $this->add_pointer(8);

        $this->_checkCachedReponseBody(8);

        $str = $this->get_message_from(0);
        $item = array_values(unpack('c*', $str));

        $shift = 0;
        $result = 0;
        $index = 0;

        while ($shift < 64) {
            $b = $item[$index++];
            $v = ($b & 0x7F) << $shift;
            $result |= $v;
            if (($b & 0x80) == 0) {
                break;
            }

            $shift += 7;
        }

        $this->removed += ($index - 8);
        $this->string = substr($str, $index) . $this->string;
        $this->setPoint(0);
        return $this->_unZigZagVal($result);
    }

    public function get_message_from($from)
    {
        $this->_checkCachedReponseBody($this->pointer - $from);
        $val = substr($this->string, $from, $this->pointer - $from);
        $this->removed += ($this->pointer - $from);
        $this->_reduceCachedReponseBody();
        return $val;
    }

    public function next($is_string = false)
    {
        return $this->readRawVarint32($is_string);
    }

    private function _checkCachedReponseBody($len)
    {
        //2 equals the start cheracter of chunk flag "\r\n0\r\n"
        while ((strlen($this->string) - 2) < $len) {
            if (!$this->readMore()) {
                return null;
            }
        }

        return $this->string;
    }

    /**
     * Handle chunk flag string likes: \r\n content length \r\n
     *
     * @param  $data
     * @return mixed
     */
    private function _readChunkedData($data)
    {
        if (isset($this->headers['Transfer-Encoding'])) {
            $chunkPreFlag = ($this->string === null || $this->_endsWith($this->string, "\r\n"));
            if ($chunkPreFlag && ($this->removed + strlen($this->string)) >= $this->chunkDataLen) {
                //If endswith "\r\n"
                if (strlen($this->string) >= 2) {
                    $this->string = rtrim($this->string, "\r\n");
                }

                $len = hexdec(trim($data, "\r\n"));
                $this->chunkDataLen += $len;
                $this->lastReadedLine = $data;
            } else {
                $this->string .= $data;
                $this->lastReadedLine = $data;
                return $data;
            }
        } else {
            $this->string .= $data;
            return $data;
        }
    }

    private function _endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    private function _startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * To save the memory, we should remove the read string
     */
    private function _reduceCachedReponseBody()
    {
        $this->string = substr($this->string, $this->get_pointer());
        $this->setPoint(0);
    }

    private function _unZigZagVal($val)
    {
        return $this->shr($val, 1) ^ -($val & 1);
    }

    private function shr($x, $bits)
    {
        $intLength = PHP_INT_SIZE * 8;

        if ($bits <= 0) {
            return $x;
        }
        if ($bits >= $intLength) {
            return 0;
        }

        $bin = decbin($x);
        $l = mb_strlen($bin);

        if ($l > $intLength) {
            $bin = mb_substr($bin, $l - $intLength, $intLength);
        } elseif ($l < $intLength) {
            $bin = str_pad($bin, $intLength, '0', STR_PAD_LEFT);
        }

        return bindec(str_pad(mb_substr($bin, 0, $intLength - $bits), $intLength, '0', STR_PAD_LEFT));
    }
}
