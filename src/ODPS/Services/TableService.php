<?php

namespace ODPS\Services;

use ODPS\Core\OdpsBase;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsClient;

/**
 * Class Table provides all operations for odps Table
 *
 * @package ODPS\Services
 */
class TableService extends \ODPS\Core\OdpsBase
{
    public function getExtendedTable($tableName)
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $tableName);
        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_SUB_RESOURCE => "extended"
        );

        return $this->call($options);
    }

    public function getTable($tableName)
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $tableName);
        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource
        );

        return $this->call($options);
    }

    public function getTablePartitions($tableName)
    {
        $resource = ResourceBuilder::buildTableUrl($this->getDefaultProjectName(), $tableName);
        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_SUB_RESOURCE => "partitions"
        );

        return $this->call($options);
    }

    public function getTablesInternal($paras)
    {
        $resource = ResourceBuilder::buildTablesUrl($this->getDefaultProjectName());
        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => $paras
        );

        $rst = $this->call($options);
        return $rst;
    }

    public function getTables($tableName = null, $owner = null)
    {
        return new OdpsIterator(
            array(
                "obj" => $this,
                "func" => "getTablesInternal",
                "funcParams" => array(
                    "expectmarker" => "true",
                    "name" => $tableName,
                    "owner" => $owner
                ),
                "itemName" => "Table"
            )
        );
    }
}
