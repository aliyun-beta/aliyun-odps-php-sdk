<?php

abstract class WireType
{
    const WIRETYPE_VARINT = 0;
    const WIRETYPE_FIXED64 = 1;
    const WIRETYPE_LENGTH_DELIMITED = 2;
    const WIRETYPE_START_GROUP = 3;
    const WIRETYPE_END_GROUP = 4;
    const WIRETYPE_FIXED32 = 5;
}