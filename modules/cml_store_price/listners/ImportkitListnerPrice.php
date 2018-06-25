<?php

class ImportkitListnerPrice extends ImportkitListner implements InterfaceObserver
{
    public $weight = 40;

    public $operation = '';

    public function batch($argument, &$context)
    {
        $params = array(
          'argument' => $argument,
          'options' => $this->getParams(),
          'context' => &$context
        );

        return $this->exeCallBack('price_batch', $params);
    }

    public function settings_form($form_state = array(), $submit = false)
    {
        //global $user;

        $form = array();

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => t('Price settings'),
          '#description' => t('The price configuration settings'),
          '#collapsible' => true,
          '#collapsed' => false,
        );

        $form[__CLASS__]['importkit_product_retail_price'] = array(
          '#type' => 'textfield',
          '#size' => 50,
          '#title' => t('The guid of price retail price'),
          '#description' => t('The guid of retail price'),
          '#weight' => 30,
          '#default_value' => variable_get('importkit_product_retail_price', ''),
        );

        $form[__CLASS__]['importkit_product_retail_price_rate'] = array(
          '#type' => 'textfield',
          '#size' => 50,
          '#title' => t('The rate of the retail price'),
          '#description' => t('The rate of the price'),
          '#weight' => 31,
          '#default_value' => variable_get('importkit_product_retail_price_rate', 1),
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
            'offers',
            'prices',
            'imported_offers',
          ))
        ) {
            return $form;
        }

        $products = (int) $this->exeCallBack('products');
        $offers = (int) $this->exeCallBack('offers');
        if (!($offers || $products)) {

            $form['importkit_prices'] = array(
              '#type' => 'hidden',
              '#value' => array('import' => 'import'),
            );

            return $form;
        }

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => 'Импорт ценовых предложений',
          '#description' => 'Импортирование: ценовые предложения',
          '#collapsible' => true,
          '#collapsed' => false,
        );

        $prices = (int) $this->exeCallBack('prices');
        /*if ($products && $prices) {

            $form[__CLASS__]['amount_prices'] = array(
              '#type' => 'item',
              '#title' => 'Импортировано ценовых предложений: ' . $prices,
              '#description' => '',
            );

        }*/

        if (!$prices) {
            if($this->operation == 'import') {
                $form[__CLASS__]['importkit_prices'] = array(
                  '#type' => 'checkboxes',
                  '#title' => '',
                  '#description' => '',
                  '#default_value' => array('import' => 'import'),
                  '#options' => array('import' => 'Импортировать ценовые предложения'),
                );
            }
        } elseif ($products || $offers) {

            $options = array();

            if(in_array($this->operation, array('import','update'))) {
                $options['update'] = 'Обновить ценовые предложения: ' . $prices;
                $options['skip'] = 'Пропустить';
                $this->operation = 'update';
            }

            if($this->operation == 'remove') {
                $options['remove'] = 'Удалить ценовые предложения: ' . $prices;
                $options['skip'] = 'Пропустить';
            }

            $form[__CLASS__]['importkit_prices'] = array(
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
            if (isset($form_state['values']['importkit_prices'])) {
                switch (@(string) $form_state['values']['importkit_prices']['import']) {
                    case 'import':
                        $this->setJob('parse');
                        $this->setParam('parser', 'ImportkitParserCML');
                        break;
                }
            }

            // radio
            if (isset($form_state['values']['importkit_prices']) && $form_state['values']['importkit_products'] != 'remove') {
                switch (@(string) $form_state['values']['importkit_prices']) {
                    case 'update':
                        $this->setJob('parse');
                        $this->setParam('parser', 'ImportkitParserCML');
                        break;
                    case 'remove':
                        //$imported = $this->exeCallBack('imported_prices', array());
                        //$this->setJob('remove_prices', $imported);
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
