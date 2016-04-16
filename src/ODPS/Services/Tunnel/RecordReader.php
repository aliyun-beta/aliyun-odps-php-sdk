<?php

namespace ODPS\Services\Tunnel;

use \ODPS\Services\Tunnel\Protobuf\TableRecord;
use \ODPS\Http\RequestCore;
use \PBInputStreamReader;

/**
 * A TableRecord reader form the response stream, used in internal
 *
 * @package ODPS\Services\Tunnel
 */
class RecordReader extends \PBMessage
{
    private $socket;
    private $columns;
    private $streamReader;

    /**
     * @param resource $httpSocket
     * @param Array $columns is the table schema
     */
    public function __construct($httpSocket, $columns)
    {
        $this->socket = $httpSocket;
        $this->columns = $columns;
    }

    /**
     * Return the next TableRecord form the stream
     *
     * @return TableRecord
     */
    public function read()
    {
        if (!$this->streamReader) {
            $this->streamReader = new  \PBInputStreamReader($this->socket);
        }

        $tblRecord = new TableRecord(null, $this->columns);

        $next = $this->streamReader->next();
        while ($next) {
            $messtypes = $this->get_types($next);
            $wired = $messtypes['wired'];
            $fieldNum = strval($messtypes["field"]);

            if ($fieldNum === static::TUNNEL_END_RECORD) {
                $checkSum = $this->streamReader->readUInt32();
                // $this->addRecord($tblRecord);
                return $tblRecord;
                $tblRecord = new TableRecord(null, $this->otherParams);
            } else if ($fieldNum === static::TUNNEL_META_COUNT) {
                $cnt = $this->streamReader->readSInt64();
                $messtypes = $this->get_types($this->streamReader->next());

                if ($messtypes["field"] != static::TUNNEL_META_CHECKSUM) {
                    throw new OdpsException("Expect meta checksum but not");
                } else {
                    $checkSum = $this->streamReader->readUInt32();
                }
            } else {
                if (isset($tblRecord->fields[$messtypes['field']])) {
                    $fieldType = $tblRecord->fields[$messtypes['field']];

                    $consume = new $fieldType();
                    $consume->ParseFromReader($this->streamReader);
                    //      $size = $tblRecord->_get_arr_size($messtypes["field"]);
                    $tblRecord->_set_value($messtypes["field"], $consume->value);
                }
            }

            $next = $this->streamReader->next();
        }

        $this->_close();
        return null;
    }

    private function _close()
    {
        fclose($this->socket);
    }
}
