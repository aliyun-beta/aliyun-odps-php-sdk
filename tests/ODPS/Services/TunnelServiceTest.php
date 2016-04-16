<?php
use ODPS\Services\Tunnel\Protobuf\TableRecord;

class TunnelServiceTest extends TestBase
{
    public function testCreateUploadSession()
    {
        $rst = $this->tunnel->createUploadSession("t1");
        $this->assertNotNull($rst);
    }

    public function testGetUploadSessionStatus()
    {
        $session = $this->tunnel->createUploadSession("t2", "c3=a");
        $rst = $session->getStatus();

        $this->assertStringStartsWith(
            "http://dt.odps.aliyun.com/projects/xioxu_project/tables/t2",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testGetDownloadSessionStatus()
    {
        $session = $this->tunnel->createDownloadSession("t2", "c3=a");
        $rst = $session->getStatus();

        $this->assertStringStartsWith(
            "http://dt.odps.aliyun.com/projects/xioxu_project/tables/t2",
            $rst->debugInfo["requestUrl"]
        );
    }

    public function testDwonloadWithStream()
    {
        $this->downloadWithStream("t4", "c6=a");
        //print xdebug_memory_usage();
    }

    public function testUploadWithStream()
    {
        // $this->upload("t4", "c6=a");
        $this->uploadWithStream("t4", "c6=a");
    }

    public function testByColumntype()
    {
        $tblNames = ["a1", "a2", "a3", "a4"];
        foreach ($tblNames as $idx => $v) {
            $this->uploadWithStream($v, "c2=a");
            $this->downloadWithStream($v, "c2=a");
        }
    }

    private function downloadWithStream($tableName, $partion)
    {
        $this->tunnel->setCurrProject("test1");
        $downloadSession = $this->tunnel->createDownloadSession($tableName, $partion);
        $recordCount = $downloadSession->getRecordCount();

        print "\r\nTotal record count:" . $recordCount . "\r\n";

        $recordReader = $downloadSession->openRecordReader(0, $recordCount);
        $index = 1;

        while ($record = $recordReader->read()) {
            print $index++ . "\n Record Start: " . "\n";

            foreach ($record->getValues() as $v) {
                if ($v instanceof DateTime) {
                    print $v->format("Y-m-d\TH:i:s\Z");
                } else {
                    print $v;
                }

                print ", ";
            }

            print "\n Record Done: " . "\n";
        }

        print "download done";
    }

    private function uploadWithStream($tableName, $partion)
    {
        $uploadSerssion = $this->tunnel->createUploadSession($tableName, $partion);
        $record = new TableRecord();
        $recordWriter = $uploadSerssion->openRecordWriter(0);

        $cols = $uploadSerssion->getColumns();
        for ($i = 0; $i < sizeof($cols); $i++) {
            $col = $cols[$i];

            $pbIndex = $i + 1;

            switch ($col->type) {
                case "string":
                    $record->setColumnValue($pbIndex, $col->type, "sample122222223");
                    break;
                case "bigint":
                    $record->setColumnValue($pbIndex, $col->type, pow(2, 33));
                    break;
                case "boolean":
                    $record->setColumnValue($pbIndex, $col->type, true);
                    break;
                case "double":
                    $record->setColumnValue($pbIndex, $col->type, -0.3);
                    break;
                case "datetime":
                    $record->setColumnValue($pbIndex, $col->type, (new DateTime()));
                    break;
                default:
                    throw new Exception("Unknown column type:" . $col->type);
            }
        }

        for ($i = 0; $i < 100; $i++) {
            $recordWriter->write($record);
        }
        $ret = $recordWriter->close();
        $uploadSerssion->commit();

        print "Upload done";
    }
}
