<?php

interface InterfaceObserver
{
    function batch($argument, &$context);

    // Settings
    function settings_form($form_state = array(), $submit = false);

    function settings_form_submit($form, &$form_state = array());
    // End Settings

    // Form
    function form($form_state = array(), $submit = false);

    function form_submit($form, &$form_state = array());

    // End Form

    function parse($reader, $path, $ver, $created);

    function finished($success, $results, $operations);

    function __toString();
}
