<?php

class InstanceServiceTest extends TestBase
{
    public function testGetInstance()
    {
        $rst = $this->instance->getInstance("a1");
        $this->assertEquals("GET", $rst->debugInfo["requestMethod"]);
        $this->assertEquals("/api/projects/xioxu_project/instances/a1", $rst->debugInfo["requestPath"]);
    }

    public function testGetInstanceDetail()
    {
        $rst = $this->instance->getInstanceDetail("a1", "t1");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/instances/a1?instancedetail&taskname=t1",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetInstanceProgress()
    {
        $rst = $this->instance->getInstanceProgress("a1", "t1");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/instances/a1?instanceprogress&taskname=t1",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetInstanceSummary()
    {
        $rst = $this->instance->getInstanceSummary("a1", "t1");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/instances/a1?instancesummary&taskname=t1",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetInstanceTask()
    {
        $rst = $this->instance->getInstanceTask("a1", "t1");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/instances/a1?taskstatus",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetInstances()
    {
        $rst = $this->instance->getInstances();

        foreach ($rst as $instance) {
            $this->assertNotNull($instance);
        }
    }

    public function testPostSqlTaskInstances()
    {
        $rst = $this->instance->postSqlTaskInstance("task1", null, "select * from t1;");
        $this->assertNotNull($rst);
    }

    public function testPostSqlPlanTaskInstances()
    {
        $rst = $this->instance->postSqlPlanTaskInstance("task2", null, array("a1" => "v1"), "select * from t1;");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/instances",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testTerminateInstances()
    {
        $rst = $this->instance->terminateInstance("ins1");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/instances/ins1",
            $rst->debugInfo["requestUrl"]
        );
    }
}
