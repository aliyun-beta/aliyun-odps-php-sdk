<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016-03-30
 * Time: 15:14
 */

namespace ODPS\Services\Tunnel;

use \ODPS\Services\Tunnel\Protobuf\OdpsChecksum;
use \ODPS\Http\RequestCore;
use \PBMessage;
use \PBTypes;

class RecordWriter extends PBMessage
{
    var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    private $socket;
    private $checksum;
    private $rcdCnt;

    public function __construct($httpSocket)
    {
        $this->rcdCnt = 0;
        $this->socket = $httpSocket;
        $this->checksum = new OdpsChecksum();

        $this->fields[strval(static::TUNNEL_META_COUNT)] = PBTypes::PBZigZag;
        $this->values[strval(static::TUNNEL_META_COUNT)] = "";

        $this->fields[strval(static::TUNNEL_META_CHECKSUM)] = PBTypes::PBInt;
        $this->values[strval(static::TUNNEL_META_CHECKSUM)] = "";
    }

    /**
     * @param \ODPS\Services\Tunnel\Protobuf\TableRecord $record
     */
    public function write($record)
    {
        $checksum = $record->_updateCheckSum();
        $this->checksum->update($checksum);

        $content = $record->SerializeToString();
        $content = $this->_buildChunkData($content);

        fwrite($this->socket, $content);
        $this->rcdCnt += 1;
    }

    public function close()
    {
        $this->_set_value(strval(PBMessage::TUNNEL_META_COUNT), $this->rcdCnt);
        $this->_set_value(strval(PBMessage::TUNNEL_META_CHECKSUM), $this->checksum->getValue());
        $content = $this->SerializeToString();
        $content = $this->_buildChunkData($content) . "0" . "\r\n\r\n";
        fwrite($this->socket, $content);

        $response = '';

        while ($data = fgets($this->socket)) {
            $response .= $data;
        }
        fclose($this->socket);
        $reqCore = new RequestCore();
        return $reqCore->parseResponse($response);
    }

    private function _buildChunkData($content)
    {
        return dechex(strlen($content)) . "\r\n" . $content . "\r\n";
    }
}
