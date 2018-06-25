<?php

interface InterfaceSubject
{
    function getSettings();

    function attach(InterfaceObserver $o);

    function detach(InterfaceObserver $o);

    function notify($options);

    function getErrors();

    function getJobs();

    function getErrorsByListner($listner);
}
