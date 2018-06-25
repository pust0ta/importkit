<?php

abstract class ImportkitXMLReader extends XMLReader implements InterfaceReader
{

    static $_instance;

    private $_options = array();

    private $_call_back = array();

    private $_path = '';

    private $_stop = false;

    protected function __construct()
    {
    }

    public static function getInstance()
    {
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function setPath($path)
    {
        $this->_path = $path;

        return $this;
    }

    public function parse()
    {

    }

    public function stop($status = null)
    {
        if (!is_null($status)) {
            $this->_stop = $status;
        } else {
            return $this->_stop;
        }
    }

    protected function __clone()
    {
    }

    protected function setOption($name, $value)
    {
        $this->_options[$name] = $value;

        return $this;
    }

    protected function getOption($name = '')
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        } else {
            return false;
        }
    }

    protected function exeCallBack()
    {
        if (is_callable($this->getCallBack())) {
            return call_user_func_array($this->getCallBack(), array($this->getOptions()));
        }
    }

    public function getCallBack()
    {
        return $this->_call_back;
    }

    public function setCallBack($call_back)
    {
        $this->_call_back = $call_back;

        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setOptions($options)
    {
        $this->_options = $options;

        return $this;
    }

}
