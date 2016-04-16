<?php

namespace ODPS\Services\Tunnel;

use ODPS\Core\OdpsBase;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsException;
use ODPS\Core\OdpsClient;
use ODPS\Services\Tunnel\Protobuf\TableRecord;

/**
 * Class UploadSession
 *
 * @package ODPS\Services\Tunnel
 */
class UploadSession extends OdpsBase
{
    private $uploadSessionJsonObj;
    private $tableName;
    private $partition;


    public function __construct($odpsClient, $partition, $uploadSessionJsonObj, $tableName, $tunnelServer)
    {
        parent::__construct($odpsClient);
        $this->uploadSessionJsonObj = $uploadSessionJsonObj;
        $this->tableName = $tableName;
        $this->partition = $partition;
        $this->setBaseUrl($tunnelServer);
    }

    public function getSessionDetail()
    {
        return $this->uploadSessionJsonObj;
    }

    public function getStatus()
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $this->tableName);
        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => array(
                "partition" => $this->partition,
                "uploadid" => $this->uploadSessionJsonObj->UploadID)
        );

        return $this->call($options);
    }

    public function commit()
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $this->tableName);

        $options = array(
            OdpsClient::ODPS_METHOD => "POST",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => array(
                "partition" => $this->partition,
                "uploadid" => $this->uploadSessionJsonObj->UploadID),
            OdpsClient::ODPS_HEADERS => array(
                "x-odps-tunnel-version" => constant("XOdpsTunnelVersion")
            )
        );

        $this->setCurrProject($this->getDefaultProjectName());
        return $this->call($options);
    }

    public function getColumns()
    {
        if ($this->uploadSessionJsonObj == null) {
            throw new OdpsException("Invalid upload session");
        }
        $cols = $this->uploadSessionJsonObj->Schema->columns;
        return $cols;
    }

    public function newRecord()
    {
        return new TableRecord();
    }

    public function openRecordWriter($blockId)
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $this->tableName);

        $options = array(
            OdpsClient::ODPS_METHOD => "PUT",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => array(
                "partition" => $this->partition,
                "uploadid" => $this->uploadSessionJsonObj->UploadID,
                "blockid" => $blockId),
            OdpsClient::ODPS_HEADERS => array(
                "Transfer-Encoding" => "chunked",
                "x-odps-tunnel-version" => constant("XOdpsTunnelVersion"),
                "Content-Type" => "application/octet-stream"
            ),
            "JustOpenSocket" => true
        );

        $this->setCurrProject($this->getDefaultProjectName());
        return new RecordWriter($this->call($options));
    }
}
