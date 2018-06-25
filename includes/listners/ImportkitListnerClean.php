<?php

class ImportkitListnerClean extends ImportkitListner implements InterfaceObserver
{
    public $weight = 1000;

    public $operation = '';

    public function batch($argument, &$context)
    {
    }

    public function settings_form($form_state = array(), $submit = false)
    {
        global $user;

        $form = array();

        return $form;
    }

    public function settings_form_submit($form, &$form_state = array())
    {
        $form = array();

        return $form;
    }

    public function form($form_state = array(), $submit = false)
    {
        $form = array();

        return $form;
    }

    public function form_submit($form, &$form_state = array())
    {
        $form = array();

        return $form;
    }

    public function parse($reader, $path, $ver, $created)
    {
    }

    public function finished($success, $results, $operations)
    {
        // Выполняем очистку кешей
        importkit_clear_cache();
    }

    public function __toString()
    {
        return sprintf("class %s execute", __CLASS__);
    }
}
