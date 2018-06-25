<?php

class ImportkitListnerOrder extends ImportkitListner implements InterfaceObserver
{
    public $weight = 100;

    public function batch($argument, &$context)
    {
    }

    public function settings_form($form_state = array(), $submit = false)
    {
        $form = array();

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => 'Order settings',
          //'#description' => t('The price update configuration settings'),
          '#collapsible' => true,
          '#collapsed' => false,
        );

        switch (variable_get('importkit_content_module_use', 'ubercart')) {
            case 'commerce':

                $form[__CLASS__]['Settings']['importkit_order_state'] = array(
                  '#type' => 'select',
                  '#title' => t('Status'),
                  '#title_display' => 'invisible',
                  '#options' => commerce_order_status_options_list(),
                  '#default_value' => variable_get('importkit_order_state', 'completed'),
                );

                break;

            case 'ubercart':

                $form[__CLASS__]['User info'] = array(
                  '#type' => 'fieldset',
                  '#title' => t('User info'),
                  '#collapsible' => true,
                  '#collapsed' => false,
                );

                $form[__CLASS__]['User info']['importkit_order_user_info'] = array(
                  '#title' => t('Order info'),
                  '#description' => t(''),
                  '#type' => 'radios',
                  '#default_value' => variable_get('importkit_order_user_info', 1),
                  '#options' => array(
                    0 => t('Use billing data for order'), // Платежной информации
                    1 => t('Use delivery data for order'), // Информации о доставки
                  ),
                );

                $form[__CLASS__]['Settings'] = array(
                  '#type' => 'fieldset',
                  '#title' => t('Order settengs'),
                  '#collapsible' => true,
                  '#collapsed' => false,
                );

                if (function_exists('uc_order_status_list')) {
                    $options = array();

                    foreach (uc_order_status_list() as $state) {
                        $options[$state['id']] = $state['title'];
                    }

                    $form[__CLASS__]['Settings']['importkit_order_state'] = array(
                      '#type' => 'select',
                      '#title' => t('Status of orders'),
                      '#options' => $options,
                      '#default_value' => variable_get('importkit_order_state', 'pending'),
                      '#weight' => 10,
                      '#description' => t('to create the order file for 1C import'),
                    );
                }

                break;
        }

        /*$form[__CLASS__]['Price info'] = array(
                 '#type' => 'fieldset',
                 '#title' => t('Price info'),
                 '#collapsible' => TRUE,
                 '#collapsed' => FALSE,
        );*/

        $form[__CLASS__]['importkit_order_curency'] = array(
          '#type' => 'textfield',
          '#title' => t(''),
          '#description' => t('The currency 1C code'),
          '#default_value' => variable_get('importkit_order_curency', 'руб.'),
        );

        if (variable_get('importkit_content_module_use', 'ubercart') == 'commerce') {

            $form[__CLASS__]['importkit_order_status'] = array(
              '#type' => 'textfield',
              '#title' => t(''),
              '#description' => t('The order default status'),
              '#default_value' => variable_get('importkit_order_status', 'Готов к отгрузке'),
            );

        }

        $form[__CLASS__]['importkit_order_file_chmod'] = array(
          '#type' => 'textfield',
          '#title' => t(''),
          '#description' => t('File permissions for order file'),
          '#default_value' => variable_get('importkit_order_file_chmod', '0664'),
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
    }

    public function __toString()
    {
        return sprintf("class %s execute", __CLASS__);
    }
}
