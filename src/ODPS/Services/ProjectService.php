<?php
namespace ODPS\Services;

use ODPS\Core\OdpsBase;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsClient;

/**
 * Class Project provides all operations for odps Project
 *
 * @package ODPS\Services
 */
class ProjectService extends OdpsBase
{
    public function getProject($projectName)
    {
        $resource = ResourceBuilder::buildProjectUrl($projectName);

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource
        );

        $response = $this->call($options);
        return $response;
    }

    public function getExtendedProject($projectName)
    {
        $resource = ResourceBuilder::buildProjectUrl($projectName);

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_SUB_RESOURCE => "extended"
        );

        $response = $this->call($options);
        return $response;
    }

    public function putProject($projectName, $comment = null)
    {
        $content = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Project >
    <Name>$projectName</Name>
    <Comment>$comment</Comment>
</Project>
EOF;

        $resource = ResourceBuilder::buildProjectsUrl();
        $resource .= "/" . $projectName;

        $options = array(
            OdpsClient::ODPS_CONTENT => $content,
            OdpsClient::ODPS_METHOD => "PUT",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_CONTENT_TYPE => OdpsClient::XmlContentType
        );

        $response = $this->call($options);
        return $response;
    }
}
