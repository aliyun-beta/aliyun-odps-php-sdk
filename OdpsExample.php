<?php

require_once __DIR__ . "/autoload.php";

use \ODPS\Core\OdpsClient;
use \ODPS\Services\TableService;

$odps = new OdpsClient("TR2QyWfDusb0Tgce", "ZPJZBMEr2pcMP2fsGeHH36PzZeNYHW",
    "http://service.odps.aliyun.com/api", "xioxu_project");

$tableService = new TableService($odps);
$tables = $tableService->getTables();

foreach ($tables as $table) {
    print  $table->Name . "\r\n";
}
