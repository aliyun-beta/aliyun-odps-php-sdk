<?php

namespace ODPS\Services;

use ODPS\Core\OdpsBase;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsClient;
use ODPS\Services\Tunnel\DownloadSession;
use ODPS\Services\Tunnel\UploadSession;

/**
 * Class Tunnel provides all operations for odps Tunnel
 *
 * @package ODPS\Services
 */
class TunnelService extends \ODPS\Core\OdpsBase
{
    const HEADER_ODPS_REQUEST_ID = "x-odps-request-id";
    const HEADER_ODPS_TUNNEL_VERSION = "x-odps-tunnel-version";
    const HEADER_STREAM_VERSION = "x-odps-tunnel-stream-version";
    const HEADER_ODPS_CURRENT_PACKID = "x-odps-current-packid";
    const HEADER_ODPS_PACK_TIMESTAMP = "x-odps-pack-timestamp";
    const HEADER_ODPS_NEXT_PACKID = "x-odps-next-packid";
    const HEADER_ODPS_PACK_NUM = "x-odps-pack-num";
    const VERSION = 4;
    const RES_PARTITION = "partition";
    const RECORD_COUNT = "recordcount";
    const PACK_ID = "packid";
    const PACK_NUM = "packnum";
    const PACK_FETCHMODE = "packfetchmode";
    const ITERATE_MODE = "iteratemode";
    const ITER_MODE_AT_PACKID = "AT_PACKID";
    const ITER_MODE_AFTER_PACKID = "AFTER_PACKID";
    const ITER_MODE_FIRST_PACK = "FIRST_PACK";
    const ITER_MODE_LAST_PACK = "LAST_PACK";
    const SHARD_NUMBER = "shardnumber";
    const SHARD_STATUS = "shardstatus";
    const LOCAL_ERROR_CODE = "Local Error";
    const SEEK_TIME = "timestamp";
    const NORMAL_TABLE_TYPE = "normal_type";
    const HUB_TABLE_TYPE = "hub_type";
    const META_ONLY = "metaonly";
    private $tunnelServer;

    /**
     * Create an serssion for uploading
     *
     * @param  string $tableName
     * @param  string $partition
     * @return UploadSession
     */
    public function createUploadSession($tableName, $partition = null)
    {
        $body = $this->_createSession("uploads", $tableName, $partition);
        return new UploadSession($this->odpsClient, $partition, json_decode($body), $tableName, $this->_getTunnelServer());
    }

    public function createDownloadSession($tableName, $partition = null)
    {
        $body = $this->_createSession("downloads", $tableName, $partition);
        $downloadSession = new DownloadSession($this->odpsClient, $partition, json_decode($body), $tableName, $this->_getTunnelServer());
        $downloadSession->setCurrProject($this->getCurrProject());
        return $downloadSession;
    }

    private function _createSession($subResource, $tableName, $partition = null)
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $tableName);

        $options = array(
            OdpsClient::ODPS_METHOD => "POST",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_SUB_RESOURCE => $subResource,
            OdpsClient::ODPS_PARAMS => array(
                "partition" => $partition
            ),
            OdpsClient::ODPS_HEADERS => array(
                "x-odps-tunnel-version" => static::VERSION
            ),
            OdpsClient::ODPS_ENDPOINT => $this->_getTunnelServer()
        );

        return $this->call($options)->body;
    }

    private function _getTunnelServer()
    {
        if (empty($this->tunnelServer)) {
            $resource = ResourceBuilder::buildTunnelUrl($this->getDefaultProjectName());

            $options = array(
                OdpsClient::ODPS_METHOD => "GET",
                OdpsClient::ODPS_RESOURCE => $resource,
                OdpsClient::ODPS_SUB_RESOURCE => "service"
            );

            $rst = $this->call($options);

            $this->tunnelServer = parse_url($this->odpsClient->getEndpoint())["scheme"] . "://" . $rst->body;

        }
        return $this->tunnelServer;

    }
}
