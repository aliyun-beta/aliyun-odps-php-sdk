<?php
namespace ODPS\Core;

/**
 * Class OdpsBase for provider common OdpsService action and store the authentication infomation
 *
 * @package ODPS\Core
 */
abstract class OdpsBase
{
    /**
     * @var
     */
    protected $odpsClient;
    /**
     * @var
     */
    private $curr_project;

    private $baseUrl;

    /**
     * @param \ODPS\Core\OdpsClient $odpsClient
     */
    public function __construct($odpsClient)
    {
        $this->odpsClient = $odpsClient;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param mixed $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param $projectName
     */
    public function SwitchProject($projectName)
    {
        $this->odpsClient->setDefaultProject($projectName);
    }

    /**
     * @param $endPoint
     */
    public function ChangeEndpoint($endPoint)
    {
        $this->odpsClient->setEndpoint($endPoint);
    }

    /**
     * @return mixed
     */
    public function getCurrProject()
    {
        return $this->curr_project;
    }

    /**
     * @param mixed $curr_project
     */
    public function setCurrProject($curr_project)
    {
        $this->curr_project = $curr_project;
    }

    /**
     * @param $options
     * @return mixed
     */
    protected function call($options)
    {
        return $this->odpsClient->call($this->baseUrl, $options, $this->getCurrProject());
    }

    /**
     * @param $options
     * @throws OssException
     */
    protected function precheckOptions(&$options)
    {
        OdpsUtil::validateOptions($options);
        if (!$options) {
            $options = array();
        }
    }

    /**
     * @return mixed
     */
    protected function getDefaultProjectName()
    {
        return $this->odpsClient->getDefaultProject();
    }

}
