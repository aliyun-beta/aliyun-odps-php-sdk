<?php

class ResourceServiceTest extends TestBase
{
    public function testDelResource()
    {
        $rst = $this->resource->delResource("res2");

        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/resources/res2",
            $rst->debugInfo["requestUrl"]
        );

        $this->assertEquals("DELETE", $rst->debugInfo["requestMethod"]);
    }

    public function testGetResources()
    {
        $rst = $this->resource->getResources();

        foreach ($rst as $resource) {
            $this->assertNotNull($resource);
        }
    }

    public function testGetResource()
    {
        $rst = $this->resource->getResource("Extract_Name.py");

        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/resources/Extract_Name.py",
            $rst->debugInfo["requestUrl"]
        );

        $this->assertEquals("GET", $rst->debugInfo["requestMethod"]);
    }

    public function testPostResourceException()
    {
        $this->setExpectedException("ODPS\Core\OdpsException");
        $rst = $this->resource->postResource(null);
    }

    public function testPostResource()
    {
        $rst = $this->resource->postResource(
            array(
                "x-odps-resource-type" => "py",
                "x-odps-resource-name" => "Extract_Name.py"
            ), "print 1"
        );

        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/resources",
            $rst->debugInfo["requestUrl"]
        );

        $this->assertEquals("POST", $rst->debugInfo["requestMethod"]);
    }

    public function testPutResource()
    {
        $rst = $this->resource->putResource(
            "Extract_Name.py", array(
            "x-odps-resource-type" => "py",
            "x-odps-resource-name" => "Extract_Name.py"
        ), "print 1"
        );

        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/resources/Extract_Name.py",
            $rst->debugInfo["requestUrl"]
        );

        $this->assertEquals("PUT", $rst->debugInfo["requestMethod"]);
    }
}
