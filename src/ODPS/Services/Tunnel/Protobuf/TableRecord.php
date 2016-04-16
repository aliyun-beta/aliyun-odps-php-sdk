<?php

namespace ODPS\Services\Tunnel\Protobuf;

use \PBMessage;
use \PBTypes;

/**
 * Class TableRecord
 *
 * @package ODPS\Services\Tunnel\Protobuf
 */
class TableRecord extends PBMessage
{
    const COLUMN_BIGINT = "BIGINT";
    const COLUMN_BOOLEAN = "BOOLEAN";
    const COLUMN_DATETIME = "DATETIME";
    const COLUMN_DOUBLE = "DOUBLE";
    const COLUMN_STRING = "STRING";
    var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    private $checkSum;

    public function __construct($reader = null, $otherParams = null)
    {
        parent::__construct($reader);
        $this->fields[strval(PBMessage::TUNNEL_END_RECORD)] = PBTypes::PBInt;

        if ($otherParams !== null) {
            $this->setColumns($otherParams);
        }
    }

    public function setColumns($cols)
    {
        for ($i = 0; $i < sizeof($cols); $i++) {
            $pbIndex = $i + 1;
            $this->addColumn($pbIndex, $cols[$i]->type);
        }
    }

    /**
     * @param $index
     * @param $columnType
     * @throws \ODPS\Core\OdpsException
     */
    public function addColumn($index, $columnType)
    {
        switch (strtoupper($columnType)) {
            case static::COLUMN_BIGINT:
                $this->fields["$index"] = PBTypes::PBBigint;
                break;
            case static::COLUMN_BOOLEAN:
                $this->fields["$index"] = PBTypes::PBBool;
                break;
            case static::COLUMN_DATETIME:
                $this->fields["$index"] = PBTypes::PBDate;
                break;
            case static::COLUMN_DOUBLE:
                $this->fields["$index"] = PBTypes::PBDouble;
                break;
            case static::COLUMN_STRING:
                $this->fields["$index"] = PBTypes::PBString;
                break;
            default:
                throw new \ODPS\Core\OdpsException("Unknown column type:" . $columnType);
        }

        $this->values["$index"] = "";
    }

    /**
     * Return the values according to current table schema, which sorting by columns order.
     *
     * @return array
     */
    public function getValues()
    {
        uksort($this->fields, "strnatcasecmp");
        $retArr = array();
        foreach ($this->fields as $index => $field) {
            if (strval($index) !== PBMessage::TUNNEL_END_RECORD) {
                $retArr[] = $this->values[$index]->value;
            }
        }

        return $retArr;
    }


    /**
     * Set an column value
     *
     * @param  $index
     * @param  $columnType
     * @param  $value
     * @throws \ODPS\Core\OdpsException
     */
    public function setColumnValue($index, $columnType, $value)
    {
        $this->addColumn($index, $columnType);
        $this->_set_value("$index", $value);
    }

    /**
     * Used in intenal, don't call it directlly.
     *
     * @return string
     */
    public function getContent()
    {
        $this->_updateCheckSum();
        return $this->SerializeToString();
    }

    /**
     * Used in intenal, don't call it directlly.
     *
     * @return int
     */
    public function _updateCheckSum()
    {
        $this->checkSum = new OdpsChecksum();

        foreach ($this->fields as $index => $field) {
            if (strval($index) !== strval(PBMessage::TUNNEL_END_RECORD)) {

                $this->checkSum->update($index);

                $bytes = $this->values[$index]->getBytes();
                $this->checkSum->updateBytes($bytes, 0, count($bytes));

            }
        }

        $this->_set_value(strval(PBMessage::TUNNEL_END_RECORD), $this->checkSum->getValue());
        return $this->checkSum->getValue();
    }
}
