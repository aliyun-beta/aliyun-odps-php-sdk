<?php
namespace ODPS\Services;

use ODPS\Core\OdpsBase;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsClient;

/**
 * Class FunctionService provides all operations for odps function
 *
 * @package ODPS\Services
 */
class FunctionService extends OdpsBase
{
    public function putFunction($functionName, $classType, $resources = array())
    {
        $funcResources = "";
        if (!empty($resources)) {
            $funcResources = "<ResourceName>" . imlode("</ResourceName><ResourceName>", $resources) . "</ResourceName>";
        }

        if (!empty($funcResources)) {
            $funcResources = "<Resources>" . $funcResources . "</Resources>";
        }

        $content = <<<EOT
<?xml version="1.0" ?>
<Function>
<Alias>$functionName</Alias>
<ClassType>$classType</ClassType>
$funcResources
</Function>
EOT;

        $resource = ResourceBuilder::buildFunctionUrl($this->getDefaultProjectName(), $functionName);

        $options = array(
            OdpsClient::ODPS_CONTENT => $content,
            OdpsClient::ODPS_METHOD => "PUT",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_CONTENT_TYPE => OdpsClient::XmlContentType
        );

        $response = $this->call($options);
        return $response;
    }

    public function postFunction($functionName, $classType, $resources = array())
    {
        $funcResources = "";
        if (!empty($resources)) {
            $funcResources = "<ResourceName>" . implode("</ResourceName><ResourceName>", $resources) . "</ResourceName>";
        }

        if (!empty($funcResources)) {
            $funcResources = "<Resources>" . $funcResources . "</Resources>";
        }

        $content = <<<EOT
<?xml version="1.0" ?>
<Function>
<Alias>$functionName</Alias>
<ClassType>$classType</ClassType>
$funcResources
</Function>
EOT;

        $resource = ResourceBuilder::buildFunctionsUrl($this->getDefaultProjectName());

        $options = array(
            OdpsClient::ODPS_CONTENT => $content,
            OdpsClient::ODPS_METHOD => "POST",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_CONTENT_TYPE => OdpsClient::XmlContentType
        );

        $response = $this->call($options);
        return $response;
    }

    public function getFunctionsInternal($paras)
    {
        $resource = ResourceBuilder::buildFunctionsUrl($this->getDefaultProjectName());
        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => $paras
        );

        return $this->call($options);
    }

    public function getFunctions($functionName = null, $owner = null, $marker = null, $maxItems = null)
    {
        return new OdpsIterator(
            array(
                "obj" => $this,
                "func" => "getFunctionsInternal",
                "funcParams" => array(
                    "expectmarker" => "true",
                    "name" => $functionName,
                    "owner" => $owner
                ),
                "itemName" => "Function"
            )
        );
    }

    public function deleteFunctions($functionName)
    {
        $resource = ResourceBuilder::buildFunctionUrl($this->getDefaultProjectName(), $functionName);
        $options = array(
            OdpsClient::ODPS_METHOD => "DELETE",
            OdpsClient::ODPS_RESOURCE => $resource
        );

        return $this->call($options);
    }
}
