<?php

use ODPS\Core\OdpsClient;
use ODPS\Services\FunctionService;
use ODPS\Services\Udf;
use ODPS\Services\ProjectService;
use ODPS\Services\TunnelService;
use ODPS\Services\InstanceService;
use ODPS\Services\TableService;

abstract class TestBase extends \PHPUnit_Framework_TestCase
{
    protected $odps;
    protected $functions;
    protected $tunnel;
    protected $instance;
    protected $project;
    protected $resource;
    protected $table;

    public function __construct()
    {
        $this->odps = new OdpsClient(
            "TR2QyWfDusb0Tgce", "ZPJZBMEr2pcMP2fsGeHH36PzZeNYHW",
            "http://service.odps.aliyun.com/api", "xioxu_project", true
        );

        $this->functions = new FunctionService($this->odps);
        $this->tunnel = new TunnelService($this->odps);
        $this->instance = new InstanceService($this->odps);
        $this->project = new ProjectService($this->odps);
        $this->resource = new \ODPS\Services\ResourceService($this->odps);
        $this->table = new TableService($this->odps);
    }
}
