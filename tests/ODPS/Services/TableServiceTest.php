<?php

class TableServiceTest extends TestBase
{
    public function testGetTables()
    {
        $rst = $this->table->getTables();

        foreach ($rst as $table) {
            $this->assertNotNull($table);
        }
    }

    public function testGetTableTablePartitions()
    {
        $rst = $this->table->getTablePartitions("t2");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/tables/t2?partitions",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetTable()
    {
        $rst = $this->table->getTable("t1");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/tables/t1",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetExtendTables()
    {
        $rst = $this->table->getExtendedTable("t1");
        $this->assertEquals(
            "http://service.odps.aliyun.com/api/projects/xioxu_project/tables/t1?extended",
            $rst->debugInfo["requestUrl"]
        );
    }
}
