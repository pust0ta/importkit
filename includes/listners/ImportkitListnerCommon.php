<?php

class ImportkitListnerCommon extends ImportkitListner implements InterfaceObserver
{
    public $weight = 0;

    public $operation = '';

    public function batch($argument, &$context)
    {
    }

    public function settings_form($form_state = array(), $submit = false)
    {
        global $user;

        $form = array();

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => t('Default settings'),
          //'#description' => t('Default settings'),
          '#collapsible' => true,
          '#collapsed' => false,
        );

        $form[__CLASS__]['importkit_switch_maintenance_mode'] = array(
          '#type' => 'checkbox',
          '#title' => t('Use switch maintenance mode when import start'),
          '#default_value' => variable_get('importkit_switch_maintenance_mode', true),
        );

        $form[__CLASS__]['importkit_data_path'] = array(
          '#type' => 'textfield',
          '#size' => 50,
          '#title' => t('Full path to exchange folder'),
          '#description' => t('Path must be without /import_files'),
          '#weight' => 10,
          '#default_value' => variable_get('importkit_data_path', ''),
        );

        // Use module Ubercart or Commerce
        $options = array('ubercart' => 'Ubercart 3', 'commerce' => 'Commerce');
        foreach (array('uc_product', 'commerce') as $module) {
            if (!module_exists($module)) {
                switch ($module) {
                    case 'uc_product':
                        unset($options['ubercart']);
                        break;
                    case 'commerce':
                        unset($options['commerce']);
                        break;
                }
            }
        }
        $form[__CLASS__]['importkit_content_module_use'] = array(
          '#type' => 'select',
          '#title' => t('Module use'),
          '#options' => $options,
          '#default_value' => variable_get('importkit_content_module_use', 'ubercart'),
          '#weight' => 20,
          '#description' => t('Select module for import set'),
        );

        $formats = filter_formats($user);
        $options = array();
        foreach ($formats as $format) {
            $options[$format->format] = $format->name;
        }

        $form[__CLASS__]['importkit_common_product_format'] = array(
          '#type' => 'select',
          '#title' => t('Text format'),
          '#options' => $options,
          '#default_value' => variable_get('importkit_common_product_format', 'plain'),
          '#weight' => 30,
          '#description' => t('The body format of product'),
        );

        $form[__CLASS__]['importkit_image_settings'] = array(
          '#type' => 'select',
          '#title' => t('Image settings'),
          '#description' => t('Image settings'),
          '#weight' => 40,
          '#default_value' => variable_get('importkit_image_settings', 1),
          '#options' => array(t('Move'), t('Copy')),
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
