<?php

interface InterfaceReader
{

    static function getInstance();

    function setOptions($options);

    function getOptions();

    function setCallBack($call_back);

    function setPath($path);

    function getPath();

    function getCallBack();

    function parse();

    function stop();

}
