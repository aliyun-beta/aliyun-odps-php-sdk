<?php

namespace ODPS\Services;

use ODPS\Core\OdpsBase;
use ODPS\Core\OdpsException;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsClient;

/**
 * Class Resource provides all operations for odps Resource
 *
 * @package ODPS\Services
 */
class ResourceService extends OdpsBase
{
    public function delResource($resourceName)
    {
        $resource = ResourceBuilder::buildResourceUrl($this->getDefaultProjectName(), $resourceName);

        $options = array(
            OdpsClient::ODPS_METHOD => "DELETE",
            OdpsClient::ODPS_RESOURCE => $resource
        );

        return $this->call($options);
    }

    public function getResourcesInternal($paras)
    {
        $resource = ResourceBuilder::buildResourcesUrl($this->getDefaultProjectName());

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => $paras
        );

        return $this->call($options);
    }

    public function getResources($resourceName = null, $owner = null)
    {
        return new OdpsIterator(
            array(
                "obj" => $this,
                "func" => "getResourcesInternal",
                "funcParams" => array(
                    "name" => $resourceName,
                    "owner" => $owner
                ),
                "itemName" => "Resource"
            )
        );
    }

    public function getResource($resourceName)
    {
        $resource = ResourceBuilder::buildResourceUrl($this->getDefaultProjectName(), $resourceName);

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource
        );

        return $this->call($options);
    }

    public function headResource($resourceName)
    {
        $resource = ResourceBuilder::buildResourceUrl($this->getDefaultProjectName(), $resourceName);

        $options = array(
            OdpsClient::ODPS_METHOD => "HEAD",
            OdpsClient::ODPS_RESOURCE => $resource
        );

        return $this->call($options);
    }

    /**
     * Post resource, the parameter $resourceHeaders must be an array which contains 'x-odps-resource-type'£¬'x-odps-resource-name'...
     *
     * @param  array $resourceHeaders
     * @return mixed
     * @throws OdpsException
     */
    public function postResource($resourceHeaders, $content = null)
    {
        return $this->_updateResource($resourceHeaders, $content, "POST");
    }

    public function putResource($resourceName, $resourceHeaders, $content = null)
    {
        if ($resourceHeaders == null || !is_array($resourceHeaders)) {
            throw new OdpsException('Parameter "$resourceHeaders" must be an array. ');
        }

        $resource = ResourceBuilder::buildResourceUrl($this->getDefaultProjectName(), $resourceName);

        $options = array(
            OdpsClient::ODPS_METHOD => "PUT",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_HEADERS => $resourceHeaders,
            OdpsClient::ODPS_CONTENT => $content
        );

        return $this->call($options);
    }

    private function _updateResource($resourceHeaders, $content = null, $action)
    {
        if ($resourceHeaders == null || !is_array($resourceHeaders)) {
            throw new OdpsException('Parameter "$resourceHeaders" must be an array. ');
        }

        $resource = ResourceBuilder::buildResourcesUrl($this->getDefaultProjectName());

        $options = array(
            OdpsClient::ODPS_METHOD => $action,
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_HEADERS => $resourceHeaders,
            OdpsClient::ODPS_CONTENT => $content
        );

        return $this->call($options);
    }
}
