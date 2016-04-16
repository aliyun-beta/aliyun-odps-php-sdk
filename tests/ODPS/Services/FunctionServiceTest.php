<?php

class FunctionServiceTest extends TestBase
{
    public function testPutFunction()
    {
        $rst = $this->functions->putFunction("func1", "classType", null);
        $this->assertEquals("PUT", $rst->debugInfo["requestMethod"]);
        $this->assertEquals("/api/projects/xioxu_project/registration/functions/func1", $rst->debugInfo["requestPath"]);
    }

    public function testPostFunction()
    {
        $rst = $this->functions->postFunction("test_lower2", "org.alidata.odps.udf.examples.Lower", ["my_lower.jar"]);
        $this->assertEquals("POST", $rst->debugInfo["requestMethod"]);
        $this->assertEquals("/api/projects/xioxu_project/registration/functions", $rst->debugInfo["requestPath"]);
    }

    public function testGetFunctions()
    {
        $rst = $this->functions->getFunctions();

        foreach ($rst as $function) {
            $this->assertNotNull($function);
        }
    }

    public function testDelFunctions()
    {
        $rst = $this->functions->deleteFunctions("func1");
        $this->assertEquals("DELETE", $rst->debugInfo["requestMethod"]);
    }
}
