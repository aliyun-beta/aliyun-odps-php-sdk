<?php

namespace ODPS\Services;

class OdpsIterator implements \Iterator
{
    private $cached = array();
    private $lastMarker = null;
    private $maxItems = 10;
    private $callbackFuncArr;

    function __construct($callbackParaArr)
    {
        $this->callbackFuncArr = $callbackParaArr;
        $this->callbackFuncArr["funcParams"]["maxItems"] = $this->maxItems;
    }

    function valid()
    {
        return $this->valid;
    }

    function key()
    {
        throw new \OdpsException("Do not support key accessing");
    }

    function current()
    {
        return current($this->cached);
    }

    /**
     * Return the array "pointer" to the first element
     * PHP's reset() returns false if the array has no elements
     */
    function rewind()
    {
        $this->next();
    }

    function next()
    {
        $hasCacheItem = (false !== next($this->cached));

        if ($hasCacheItem) {
            $this->valid = true;
            return;
        } else {

            if ($this->lastMarker === null || $this->lastMarker) {
                $callback = $this->callbackFuncArr;
                $callback["funcParams"]["marker"] = $this->lastMarker;
                $rst = call_user_func(array($callback["obj"], $callback["func"]), $callback["funcParams"]);

                if (isset($rst->Marker)) {
                    $this->lastMarker = (string)$rst->Marker;

                    if (isset($rst->{$callback["itemName"]})) {
                        $this->cached = $rst->{$callback["itemName"]};
                    }
                    $hasCacheItem = (false !== reset($this->cached));
                } else {
                    $this->lastMarker = "";
                }
            }
        }

        $this->valid = $hasCacheItem;
    }

}
