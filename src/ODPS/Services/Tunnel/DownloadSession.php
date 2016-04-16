<?php


namespace ODPS\Services\Tunnel;

use ODPS\Core\OdpsBase;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsException;
use ODPS\Core\OdpsClient;
use ODPS\Services\Tunnel\Protobuf\TableRecords;

class DownloadSession extends \ODPS\Core\OdpsBase
{
    private $sessionJsonObj;
    private $tableName;
    private $partition;


    public function __construct($odpsClient, $partition, $sessionJsonObj, $tableName, $tunnelServer)
    {
        parent::__construct($odpsClient);
        $this->sessionJsonObj = $sessionJsonObj;
        $this->tableName = $tableName;
        $this->partition = $partition;
        $this->setBaseUrl($tunnelServer);
    }

    public function getStatus()
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $this->tableName);
        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => array(
                "partition" => $this->partition,
                "downloadid" => $this->sessionJsonObj->DownloadID)
        );

        return $this->call($options);
    }

    public function getRecordCount()
    {
        return $this->sessionJsonObj->RecordCount;
    }

    public function openRecordReader($start, $count, $columns = null)
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $this->tableName);

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_SUB_RESOURCE => "data",
            OdpsClient::ODPS_PARAMS => array(
                "partition" => $this->partition,
                "downloadid" => $this->sessionJsonObj->DownloadID,
                "columns" => $columns,
                "rowrange" => "(" . $start . "," . $count . ")"
            ),
            OdpsClient::ODPS_HEADERS => array(
                "x-odps-tunnel-version" => constant("XOdpsTunnelVersion"),
                "Accept" => "text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2"
            ),
            "JustOpenSocket" => true
        );

        $this->setCurrProject($this->getCurrProject());
        return new RecordReader($this->call($options), $this->getColumns());
    }

    public function getColumns()
    {
        if ($this->sessionJsonObj == null) {
            throw new OdpsException("Invalid upload session");
        }
        $cols = $this->sessionJsonObj->Schema->columns;
        return $cols;
    }
}
