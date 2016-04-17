#aliyun-odps-php-sdk
![PHP 64bit](https://img.shields.io/badge/php-64bit-green.svg)

##CI
[![Build Status](https://travis-ci.org/aliyun-beta/aliyun-odps-php-sdk.svg?branch=master)](https://travis-ci.org/aliyun-beta/aliyun-odps-php-sdk)

##Preconditoins
1. Since some data types(such as Bigint, Datetime) in ODPS depends on 64bit integer (long in java), and php32 build doesn't include this type, so we have to use php 64bit version for running odps project.
    
    ----由于odps中的数据类型Bigint、Datetime的传输依赖于64位的长整型，而php默认只有64位版本才支持64位的整型，因此当前odps php sdk必须运行于64位的php版本。
2. You must use the ODPS php SDK upon any php 64bit versoin of Linux/mac-os or the php 7+ 64bit on windows since [a bug](https://bugs.php.net/bug.php?id=64863)
   
   ----由于[php的一个问题](https://bugs.php.net/bug.php?id=64863)，如果我们使用windows的情况下，请务必使用php7+ 64位版本, Linux/MacOs则直接使用64位版本即可。
3. To execute the unit test case, you need to install PHPunit in your enviroment.
   ----请在执行单元测试前确认您的环境安装有PHPunit

## Features
1. Provides full features support of aliyun ODPS api, more api detail in [official api document](http://repo.aliyun.com/api-doc/index.html)
2. Wide variety debuging supports.


## Services
Odps-php-sdk includes the following services to access RestFul api:

1. \ODPS\Services\TableService
2. \ODPS\Services\FunctionService
2. \ODPS\Services\InstanceService
2. \ODPS\Services\ProjectService
2. \ODPS\Services\ResourceService
2. \ODPS\Services\TunnelService

## How to run unit test of this project
1. Install composer in your enviroment
2. Navigate into project folder and issue 'composer install' command
3. Issue 'phpunit' in command line or phpStorm.

## Sample Usage
```
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

```

Please refer to unit test case to get more detail usage of each service.

##Common Options

### Set Current Project
$service = new ResourceService($odps);
$service->setCurrProject($currProject);

### Debug mode
```
$odps = new Odps(...)
$odps->setDebugMode(true);
... do service request...
$tableService = new TableService($odps);
$result = $tableService.getTable($tblName);

print $result->debugInfo;

```

### Enable http proxy
To enable an http proxy for catching the http traffic or any other reasons, please use the folloing line in config.php:
```
define("SetOpensearchServiceCallHttpProxy", "localhost:8888");
```

##Known issues
1. Since php doesn't supports milliseconds in Date object, so the milliseconds of ODPS table data will be missed.

##Dependencies
1. This project include the source code of [pb4php--Apache License 2.0](https://code.google.com/archive/p/pb4php/)
2. PHPunit

