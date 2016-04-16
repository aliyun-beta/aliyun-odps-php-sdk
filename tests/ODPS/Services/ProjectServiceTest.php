<?php

use \ODPS\Core\OdpsClient;

class ProjectServiceTest extends TestBase
{

    public function testGetProject()
    {
        $rst = $this->project->getProject("xioxu_project");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetExtendedProject()
    {
        $rst = $this->project->getExtendedProject("xioxu_project");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project?extended",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testPutProject()
    {
        $rst = $this->project->putProject("xioxu_project");
        $this->assertNotNull($rst);
    }

}
