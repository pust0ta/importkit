<?php

class ImportkitListnerContent extends ImportkitListner implements InterfaceObserver
{
    public $weight = 20;

    public $operation = '';

    public function batch($argument, &$context)
    {
        $params = array(
          'argument' => $argument,
          'call_back' => 'create_node',
          'options' => $this->getParams(),
          'context' => &$context
        );

        return $this->exeCallBack('batch', $params);
    }

    public function settings_form($form_state = array(), $submit = false)
    {
        $form = array();

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => t('Content settings (@type)', array(
            '@type' => variable_get('importkit_content_node_type', 'product')
          )),
          '#description' => t('The content configuration settings'),
          '#collapsible' => true,
          '#collapsed' => false,
        );


        $form[__CLASS__]['importkit_filename_wildcards_import'] = array(
          '#type' => 'textfield',
          '#size' => 50,
          '#title' => t('Filename wildcards'),
          '#description' => t('File name and pattern matching by import.xml file.'),
          '#weight' => 1,
          '#default_value' => variable_get('importkit_filename_wildcards_import', 'import*.xml'),
        );

        $options = array();

        // Node API
        $types = node_type_get_types();
        foreach ($types as $type) {
            $options[$type->type] = $type->name;
        }
        $form[__CLASS__]['importkit_content_node_type'] = array(
          '#type' => 'select',
          '#title' => t('Content type'),
          '#options' => $options,
          '#default_value' => variable_get('importkit_content_node_type', 'product'),
          '#weight' => 10,
          '#description' => t('The content type for import set'),
          '#suffix' => t('<sup>Current: @current</sup>', array(
            '@current' => variable_get('importkit_content_node_type', 'product')
          ))
        );

        if (function_exists('mb_substr')) {

            $form[__CLASS__]['importkit_display_title_field_length'] = array(
              '#type' => 'textfield',
              '#size' => 50,
              '#title' => t('Title length'),
              '#description' => t('The length of title'),
              '#weight' => 5,
              '#default_value' => variable_get('importkit_display_title_field_length', 120),
            );

        }

        $form[__CLASS__]['importkit_display_language'] = array(
          '#type' => 'textfield',
          '#size' => 20,
          '#title' => t('The description language'),
          '#description' => t('The description language'),
          '#weight' => 6,
          '#default_value' => variable_get('importkit_display_language', LANGUAGE_NONE),
        );

        // Fields API
        if (module_exists('field')) {
            $all_fields = field_read_fields();
            if ($all_fields) {
                $options = array();
                $properties = array();

                foreach ($all_fields as $name => $field) {
                    if ($name == 'taxonomy_catalog') {
                        $options['taxonomy_catalog'] = 'Каталог (БАЗОВЫЙ)';
                        continue;
                    }
                    $instances = field_read_instances(array('field_id' => $field['id']));
                    $instance = reset($instances);

                    if (isset($instance['bundle']) && in_array($instance['entity_type'], array('node'))) {
                        switch ($field['module']) {
                            case 'taxonomy':
                                $options[$instance['field_name']] = $instance['label'] . " ({$instance['field_name']})";
                                $instance['vocabulary_machine_name'] = $field['settings']['allowed_values'][0]['vocabulary'];
                                $properties[] = $instance;
                                break;
                            case 'number':
                            case 'text':
                                $fields[] = $instance;
                                break;
                        }
                    }
                }

                // Категории
                $form[__CLASS__]['importkit_content_category_field'] = array(
                  '#type' => 'select',
                  '#title' => t('Category field'),
                  '#options' => $options,
                  '#default_value' => variable_get('importkit_content_category_field', 'field_category'),
                  '#weight' => 10,
                  '#description' => t('The content type for import set'),
                );

                // ЗначенияСвойств/ЗначенияСвойства
                if (isset($properties)) {

                    $form[__CLASS__]['importkit_content_properties'] = array(
                      '#type' => 'fieldset',
                      '#title' => t('Properties <ЗначенияСвойств>'),
                      '#description' => t('The product properties settings <ЗначенияСвойств>'),
                      '#collapsible' => true,
                      '#collapsed' => true,
                      '#weight' => 12,
                    );

                    foreach ($properties as $property) {
                        $form[__CLASS__]['importkit_content_properties']
                        ['importkit_content_property_' . $property['field_id']] = array(
                          '#type' => 'textfield',
                          '#size' => 50,
                          '#title' => $property['label'],
                          '#description' => t('The guid of property') . '<br>' . l(t('Add property'), 'admin/structure/taxonomy/' . $property['vocabulary_machine_name'] . '/add'),
                          '#weight' => 30,
                          '#default_value' => variable_get('importkit_content_property_' . $property['field_id'], ''),
                        );
                    }

                }

                // ЗначенияСвойств/ЗначенияСвойства
                if (isset($fields)) {

                    $form[__CLASS__]['importkit_content_fields'] = array(
                      '#type' => 'fieldset',
                      '#title' => t('Fields <ЗначенияСвойств>'),
                      '#description' => t('The product properties settings <ЗначенияСвойств>'),
                      '#collapsible' => true,
                      '#collapsed' => true,
                      '#weight' => 13,
                    );

                    foreach ($fields as $field) {
                        $form[__CLASS__]['importkit_content_fields']
                        ['importkit_content_field_' . $field['field_id']] = array(
                          '#type' => 'textfield',
                          '#size' => 50,
                          '#title' => $field['label'] . ' (' . $all_fields[$field['field_name']]['module'] . ')',
                          '#description' => t('The guid of property'),
                          '#weight' => 30,
                          '#default_value' => variable_get('importkit_content_field_' . $field['field_id'], ''),
                        );
                    }

                }

            }
        }

        // Image
        $bundle = variable_get('importkit_content_node_type', 'product');
        $fields = field_info_instances('node', $bundle);
        $options = array();
        foreach ($fields as $field_name => $field) {
            if (isset($field['label']) && isset($field['settings']['file_extensions']) && preg_match('~(png|gif|jpg|jpeg)~i', $field['settings']['file_extensions'])) {
                $options[$field_name] = $field['label'];
            }
        }
        if ($options) {
            $form[__CLASS__]['importkit_display_image_field'] = array(
              '#type' => 'select',
              '#title' => t('Image field name of display'),
              '#options' => $options,
              '#default_value' => variable_get('importkit_display_image_field', 'image'),
              '#weight' => 12,
              '#description' => t('The machine image field name'),
              '#suffix' => t('<sup>Current: @current</sup>', array(
                '@current' => variable_get('importkit_display_image_field', 'image')
              ))
            );
        }

        // Description
        $form[__CLASS__]['importkit_display_description_field'] = array(
          '#type' => 'select',
          '#title' => t('Description field name of display'),
          '#options' => array(
            'Описание' => 'Описание',
            'ОписаниеВФорматеHTML' => 'ОписаниеВФорматеHTML',
            'Полное наименование' => 'Полное наименование',
          ),
          '#default_value' => variable_get('importkit_display_description_field', 0),
          '#weight' => 14,
          '#description' => t('The xml description node name'),
        );

        $form[__CLASS__]['importkit_comments_status'] = array(
          '#type' => 'select',
          '#title' => t('Comments mode'),
          '#options' => array(
            1 => 'Отключить комментарий',
            2 => 'Выключить комментарий',
          ),
          '#default_value' => variable_get('importkit_comments_status', 1),
          '#weight' => 15,
        );

        if( variable_get('importkit_content_module_use', 'ubercart') == 'commerce' ) {

            $form[__CLASS__]['importkit_create_default_product'] = array(
              '#type' => 'select',
              '#title' => t('Create default product'),
              '#options' => array(
                1 => 'Создавать',
                2 => 'Не создавать',
              ),
              '#default_value' => variable_get('importkit_create_default_product', 2),
              '#weight' => 16,
            );

        }

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
          'Товары',
          'products',
          'imported_products',
          'remove_product',
        ))
        ) {
            return $form;
        }

        $form[__CLASS__] = array(
          '#type' => 'fieldset',
          '#title' => 'Импорт продуктов',
          '#description' => 'Импортирование: наименование, артикул, описание, картинки, св-ва, привязка категорий (терминов), привязка св-в, реквизиты',
          '#collapsible' => true,
          '#collapsed' => false,
          '#weight' => 0,
        );

        $products = (int) $this->exeCallBack('products');
        /*if ($products) {

            $form[__CLASS__]['amount_products'] = array(
              '#type' => 'item',
              '#title' => 'Импортировано продуктов: ' . $products,
              '#description' => '',
            );

        }*/

        if (!$products) {
            if($this->operation == 'import') {
                $form[__CLASS__]['importkit_products'] = array(
                  '#type' => 'checkboxes',
                  '#title' => '',
                  '#description' => '',
                  '#default_value' => array('import' => 'import'),
                  '#options' => array('import' => 'Импортировать продукты'),
                );
            }
        } else {

            $options = array();

            if(in_array($this->operation, array('import','update'))) {
                $options['update'] = 'Обновить продукты: ' . $products;
                $options['skip'] = 'Пропустить';
                $this->operation = 'update';
            }

            if($this->operation == 'remove') {
                $options['remove'] = 'Удалить продукты: ' . $products;
                $options['skip'] = 'Пропустить';
            }

            $form[__CLASS__]['importkit_products'] = array(
              '#type' => 'radios',
              '#title' => '',
              '#description' => '',
              '#default_value' => $this->operation,
              '#options' => $options,
            );
        }

        return $form;
    }

    public function form_submit($form, &$form_state = array())
    {
        if (isset($form_state['values'])) {

            // checkbox
            if (isset($form_state['values']['importkit_products']['import'])) {
                switch (@(string) $form_state['values']['importkit_products']['import']) {
                    case 'import':
                        $this->setJob('parse');
                        $this->setParam('parser', 'ImportkitParserCML');
                        break;
                }
            }

            // radio
            if (isset($form_state['values']['importkit_products'])) {
                switch (@(string) $form_state['values']['importkit_products']) {
                    case 'update':
                        $this->setJob('parse');
                        $this->setParam('parser', 'ImportkitParserCML');
                        break;
                    case 'remove':
                        $imported = $this->exeCallBack('imported_products', array('pid'));
                        $this->setJob('remove_products', $imported);
                        $this->setParam('chunks', 100);
                        break;
                }
            }

        }
    }

    public function parse($reader, $path, $ver, $created)
    {
        if ($reader->name == 'Свойства' && $reader->nodeType == XMLReader::ELEMENT) {
            return $this->exeCallBack($reader->name, array(
              $reader,
              $path,
              $ver,
              $created
            ));
        }
        if ($reader->name == 'Товары' && $reader->nodeType == XMLReader::ELEMENT) {
            return $this->exeCallBack($reader->name, array(
              $reader,
              $path,
              $ver,
              $created
            ));
        }
    }

    public function finished($success, $results, $operations)
    {
        if ($success && isset($results['method']) && $results['method'] == 'parse') {
            $keys = $this->exeCallBack('get_content_products');
            if (isset($keys)) {
                // Регистрируем функцию создания продукта
                $this->setJob('batch', $keys);
                $this->setParam('chunks', 20);

                return true;
            }
        }

        return false;
    }

    public function remove_products($argument)
    {
        return $this->exeCallBack('remove_product', $argument);
    }

    public function __toString()
    {
        return sprintf("class %s execute", __CLASS__);
    }
}
