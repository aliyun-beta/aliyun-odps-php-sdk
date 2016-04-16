<?php

namespace ODPS\Services;

use ODPS\Core\OdpsBase;
use ODPS\Core\ResourceBuilder;
use ODPS\Core\OdpsClient;

/**
 * Class Instance provides all operations for odps Instance
 *
 * @package ODPS\Services
 */
class InstanceService extends \ODPS\Core\OdpsBase
{
    public function getInstance($instancename)
    {
        $resource = ResourceBuilder::buildInstanceUrl($this->getDefaultProjectName(), $instancename);

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource
        );

        return $this->call($options);
    }

    public function getInstanceDetail($instancename, $taskName)
    {
        return $this->_getInstanceAction($instancename, $taskName, "instancedetail");
    }

    public function getInstanceProgress($instancename, $taskName)
    {
        return $this->_getInstanceAction($instancename, $taskName, "instanceprogress");
    }

    public function getInstanceSummary($instancename, $taskName)
    {
        return $this->_getInstanceAction($instancename, $taskName, "instancesummary");
    }

    public function getInstancesInternal($paras)
    {
        $resource = ResourceBuilder::buildInstancesUrl($this->getDefaultProjectName());

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_PARAMS => $paras
        );

        return $this->call($options);
    }

    public function getInstances($daterange = null, $status = null, $jobname = null, $onlyowner = null)
    {
        return new OdpsIterator(
            array(
                "obj" => $this,
                "func" => "getInstancesInternal",
                "funcParams" => array(
                    "daterange" => $daterange,
                    "status" => $status,
                    "jobname" => $jobname,
                    "onlyowner" => $onlyowner
                ),
                "itemName" => "Instance"
            )
        );
    }

    public function getInstanceTask($instancename)
    {
        $resource = ResourceBuilder::buildInstanceUrl($this->getDefaultProjectName(), $instancename);

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_SUB_RESOURCE => "taskstatus"
        );

        return $this->call($options);
    }

    /**
     * @param $taskName
     * @param $comment
     * @param $query
     * @return return InstanceName if success,otherwise return original result
     */
    public function postSqlTaskInstance($taskName, $comment, $query)
    {
        $content = <<<EOT
<?xml version="1.0" ?>
<Instance><Job><Priority>9</Priority><Tasks>
<SQL>
    <Name>$taskName</Name>
    <Comment>$comment</Comment>
    <Config>
        <Property>
            <Name />
            <Value />
        </Property>
    </Config>
    <Query><![CDATA[$query]]></Query>
</SQL>
</Tasks></Job></Instance>
EOT;
        $resource = ResourceBuilder::buildInstancesUrl($this->getDefaultProjectName());

        $options = array(
            OdpsClient::ODPS_METHOD => "POST",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_CONTENT => $content,
            OdpsClient::ODPS_CONTENT_TYPE => OdpsClient::XmlContentType
        );

        return $this->_getInstanceName($this->call($options));
    }

    /**
     * @param $taskName
     * @param $comment
     * @param $properties
     * @param $query
     * @return return InstanceName if success,otherwise return original result
     */
    public function postSqlPlanTaskInstance($taskName, $comment, $properties, $query)
    {
        $propertiesXml = "";

        if (is_array($properties) && !empty($properties)) {
            $propertiesXml = "<Config>";
            foreach ($properties as $k => $v) {
                $propertiesXml .= "<Property>";
                $propertiesXml .= "<Name>" . $k . "</Name>";
                $propertiesXml .= "<Value>" . $v . "</Value>";
                $propertiesXml .= "</Property>";
            }

            $propertiesXml .= "</Config>";
        }

        $content = <<<EOT
<?xml version="1.0" ?>
<Instance><Job><Priority>9</Priority><Tasks>
<SQLPlan>
    <Name>$taskName</Name>
    <Comment>$comment</Comment>
    $propertiesXml
    <Query><![CDATA[$query]]></Query>
</SQLPlan>
</Tasks></Job></Instance>
EOT;
        $resource = ResourceBuilder::buildInstancesUrl($this->getDefaultProjectName());

        $options = array(
            OdpsClient::ODPS_METHOD => "POST",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_CONTENT => $content,
            OdpsClient::ODPS_CONTENT_TYPE => OdpsClient::XmlContentType
        );

        return $this->_getInstanceName($this->call($options));
    }

    public function terminateInstance($instanceName)
    {
        $resource = ResourceBuilder::buildInstanceUrl($this->getDefaultProjectName(), $instanceName);

        $content = <<<EOT
<?xml version="1.0" ?>
<Instance>
    <Status>Terminated</Status>
</Instance>
EOT;

        $options = array(
            OdpsClient::ODPS_METHOD => "PUT",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_CONTENT => $content,
            OdpsClient::ODPS_CONTENT_TYPE => OdpsClient::XmlContentType
        );

        return $this->call($options);

    }

    private function _getInstanceAction($instancename, $taskName, $action)
    {
        $resource = ResourceBuilder::buildInstanceUrl($this->getDefaultProjectName(), $instancename);

        $options = array(
            OdpsClient::ODPS_METHOD => "GET",
            OdpsClient::ODPS_RESOURCE => $resource,
            OdpsClient::ODPS_SUB_RESOURCE => $action,
            OdpsClient::ODPS_PARAMS => array(
                "taskname" => $taskName
            )
        );

        return $this->call($options);
    }

    private function _getInstanceName($rst)
    {

        if (isset($rst->header["location"])) {
            $location = $rst->header["location"];
            $locationUrl = parse_url($location);
            $pathArr = explode("/", $locationUrl["path"]);
            return $pathArr[count($pathArr) - 1];
        } else {
            return $rst;
        }
    }
}
