<?php

abstract class ImportkitListner
{
    public $weight = 0;

    public $index = 0;

    protected $_settings;

    protected $_state;

    protected $_params = array();

    // Применяется для вызова методов листнеров
    protected $_jobs = array();

    protected $_parser = 'XML';

    // Применяется для вызова процедур из модулей Drupal
    protected $_call_backs = array();

    protected $_variables = array();

    public function __construct($call_backs = array())
    {
        foreach ($call_backs as $name => $call_back) {
            $this->setCallBack($name, $call_back);
        }
    }

    /**
     * Установка функции обработчика
     *
     * @param mixed $name
     * @param mixed $call_back
     */
    protected function setCallBack($name, $call_back)
    {
        $this->_call_backs[$name] = $call_back;
    }

    public function getJobs()
    {
        return $this->_jobs;
    }

    public function clearJobs()
    {
        $this->_jobs = array();
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function setParams($params = array())
    {
        $this->_params = $params;
    }

    public function setVariables($variables = array())
    {
        $this->_variables = $variables;
    }

    protected function setJob($name = 'parse', $arguments = array())
    {
        $this->_jobs[$name] = $arguments;
    }

    protected function getParam($name)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : null;
    }

    protected function setParam($name, $value = array())
    {
        $this->_params[$name] = $value;
    }

    /**
     * Проверка на наличие зарегистрированных функций модуля-обработчика
     *
     * @param mixed $names
     */
    protected function callbackExists($names = array())
    {
        $exists = array();
        foreach ($names as $name) {
            $return = (bool) $this->getCallBack($name);
            if ($return) {
                $exists[] = $return;
            }
        }
        if ($exists) {
            return true;
        }

        return false;
    }

    /**
     * Получение имени функции обработчика
     *
     * @param mixed $name
     */
    protected function getCallBack($name)
    {
        if (isset($this->_call_backs[$name])) {
            return $this->_call_backs[$name];
        }

        return false;
    }

    /**
     * Вызов функции модуля-обработчика
     *
     * @param mixed $name
     * @param mixed $args
     */
    protected function exeCallBack($name, $args = array())
    {
        $call_back = $this->getCallBack($name);

        $call_backs = array();

        if ($call_back && is_array($call_back)) {
            $call_backs = $call_back;
        } else {
            $call_backs[] = $call_back;
        }

        $return = array();

        foreach ($call_backs as $call_back) {
            if (function_exists($call_back)) {

                $_args = array();

                if (!is_array($args)) {
                    $_args[] = $args;
                } else {
                    $_args = $args;
                }

                try {
                    if ($r = call_user_func_array($call_back, $_args)) {
                        $return[$call_back] = $r;
                    }
                } catch (Exception $e) {
                    $sql = '-';
                    if (isset($e->query_string)) {
                        $sql = str_replace(array_keys($e->args), array_values($e->args), $e->query_string);
                    }
                    throw new Exception(t('
                    An error has occurred in class @class->@method().<br>
                    When you call a function @function.<br>
                    Error: @error<br>
                    SQL: @query', array(
                                  '@class' => isset($_args['options']['class']) ? $_args['options']['class'] : '-',
                                  '@method' => isset($_args['options']['method']) ? $_args['options']['method'] : '-',
                                  '@function' => $call_back,
                                  '@error' => $e->getMessage(),
                                  '@query' => $sql,
                                )));
                }
            }
        }

        if ($c = count($return)) {
            return $c == 1 ? array_shift($return) : $return;
        }
    }

    /*protected function getVariable($name, $default = '')
    {
        if (isset($this->_variables[$name])) {
            return $this->_variables[$name];
        } else {
            return variable_get($name, $default);
        }
    }*/
}
