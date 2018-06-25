<?php

class ImportkitListnerStock extends ImportkitListner implements InterfaceObserver
{
    public $weight = 50;

    public $operation = '';

    public function batch($argument, &$context)
    {
        $params = array(
          'argument' => $argument,
          'options' => $this->getParams(),
          'context' => &$context
        );

        return $this->exeCallBack('stock_batch', $params);
    }

    public function settings_form($form_state = array(), $submit = false)
    {
        //global $user;

        $form = array();

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => t('Stock settings'),
          '#description' => t('The stock configuration settings'),
          '#collapsible' => true,
          '#collapsed' => false,
        );

        $form[__CLASS__]['importkit_product_current_stock'] = array(
          '#type' => 'textfield',
          '#size' => 50,
          '#title' => t('The guid of current stock'),
          '#description' => t('The guid of current stock'),
          '#weight' => 31,
          '#default_value' => variable_get('importkit_product_current_stock', ''),
        );

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

        if (!$this->callbackExists(array(
            'stock',
            'offers',
            'products',
            'imported_offers',
          ))
        ) {
            return $form;
        }

        $products = (int) $this->exeCallBack('products');
        $offers = (int) $this->exeCallBack('offers');
        if (!($offers || $products)) {

            $form['importkit_stock'] = array(
              '#type' => 'hidden',
              '#value' => array('import' => 'import'),
            );

            return $form;
        }

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => 'Импорт позиций по остаткам',
          '#description' => 'Импортирование: остатки товарных позиций',
          '#collapsible' => true,
          '#collapsed' => false,
        );

        $stock = (int) $this->exeCallBack('stock');
        /*if ($products && $stock) {

            $form[__CLASS__]['amount_stock'] = array(
              '#type' => 'item',
              '#title' => 'Импортировано позиций по остаткам: ' . $stock,
              '#description' => '',
            );

        }*/

        if (!$stock) {

            if($this->operation == 'import') {
                switch (variable_get('importkit_content_module_use', 'ubercart')) {
                    case 'commerce':

                        if (module_exists('commerce_ss')) {
                            $form[__CLASS__]['importkit_stock'] = array(
                              '#type' => 'checkboxes',
                              '#title' => '',
                              '#description' => '',
                              '#default_value' => array('import' => 'import'),
                              '#options' => array('import' => 'Импортировать информацию по остаткам'),
                            );
                        } else {

                            $form[__CLASS__]['amout_terms'] = array(
                              '#type' => 'item',
                              '#title' => t('Не установлен модуль !module', array(
                                '!module' => l('commerce_ss', 'https://drupal.org/project/commerce_stock', array('absolute' => true))
                              )),
                              '#description' => '',
                            );

                        }

                        break;

                    case 'ubercart':

                        if (module_exists('uc_stock')) {
                            $form[__CLASS__]['importkit_stock'] = array(
                              '#type' => 'checkboxes',
                              '#title' => '',
                              '#description' => '',
                              '#default_value' => array('import' => 'import'),
                              '#options' => array('import' => 'Импортировать информацию по остаткам'),
                            );
                        } else {
                            $form[__CLASS__]['amout_terms'] = array(
                              '#type' => 'item',
                              '#title' => 'Не установлен модуль uc_stock',
                              '#description' => '',
                            );
                        }

                        break;
                }
            }

        } elseif ($products || $offers) {

            $options = array();

            if(in_array($this->operation, array('import','update'))) {
                $options['update'] = 'Обновить информацию по остаткам: ' . $stock;
                $options['skip'] = 'Пропустить';
                $this->operation = 'update';
            }

            if($this->operation == 'remove') {
                $options['remove'] = 'Удалить информацию по остаткам: ' . $stock;
                $options['skip'] = 'Пропустить';
            }

            $form[__CLASS__]['importkit_stock'] = array(
              '#type' => 'radios',
              '#title' => '',
              '#description' => '',
              '#default_value' => $this->operation,
              '#options' => $options,
            );

        }

        return $form;
    }

    public function prepaire_batch()
    {
    }

    public function import()
    {
    }

    public function update()
    {
    }

    public function form_submit($form, &$form_state = array())
    {
        $form = array();

        if (isset($form_state['values'])) {

            // checkbox
            if (isset($form_state['values']['importkit_stock'])) {
                switch (@(string) $form_state['values']['importkit_stock']['import']) {
                    case 'import':
                        $this->setJob('parse');
                        $this->setParam('parser', 'ImportkitParserCML');
                        variable_set('imporkit_settings_' . get_class($this), $this->weight);
                        break;
                }
            }

            // radio
            if (isset($form_state['values']['importkit_stock']) && $form_state['values']['importkit_products'] != 'remove') {
                switch (@(string) $form_state['values']['importkit_stock']) {
                    case 'update':
                        $this->setJob('parse');
                        $this->setParam('parser', 'ImportkitParserCML');
                        break;
                    case 'remove':
                        break;
                }
            }

        }

        return $form;
    }

    public function parse($reader, $path, $ver, $created)
    {
        if ($reader->name == 'Предложения' && $reader->nodeType == XMLReader::ELEMENT) {
            return $this->exeCallBack($reader->name, array($reader, $path, $ver, $created));
        }
    }

    public function finished($success, $results, $operations)
    {
        if ($success && isset($results['method']) && $results['method'] == 'parse') {
            $keys = $this->exeCallBack('get_content_offers');
            if (isset($keys)) {
                // Регистрируем функцию обновления продукта
                $this->setJob('batch', $keys);
                $this->setParam('chunks', 50);
            }
        }

    }

    public function __toString()
    {
        return sprintf("class %s execute", __CLASS__);
    }
}
