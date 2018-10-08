<?php

/**
 * @uses views_handler_field_numeric
 */
class gf_stock_handler_filter_stock extends views_handler_filter_numeric{

  use gf_stock_handler_trait; 

  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['gf_stock_region'] = [
      'default' => $this->gf_stock_regions[GF_STOCK_REGION_RU],
    ];
    $options['gf_stock_region_from_session'] = [
      'default' => FALSE,
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function options_form(&$form, &$form_state) {
    $regions = 
    $form['gf_stock_region'] = array(
      '#type' => 'radios',
      '#title' => t('Stock Region'),
      '#description' => t('Select the region to display stock.'),
      '#options' => $this->gf_stock_regions,
      '#default_value' => $this->options['gf_stock_region'],
    );
    $form['gf_stock_region_from_session'] = array(
      '#type' => 'checkbox',
      '#title' => t('Stock Region from User Session'),
      '#description' => t('Take a region from the user\'s session (if any).'),
      '#default_value' => $this->options['gf_stock_region_from_session'],
    );
    parent::options_form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function admin_summary() {
    if ($this
      ->is_a_group()) {
      return t('grouped');
    }
    if (!empty($this->options['exposed'])) {
      return t('exposed');
    }
    $output = strtoupper($this->options['gf_stock_region']) . ' ';
    if ($this->options['gf_stock_region_from_session']) {
      $output .= strtoupper(t('Or From Session')) . ' ';
    }

    $options = $this
      ->operator_options('short');
    $output .= check_plain($options[$this->operator]);
    if (in_array($this->operator, $this
      ->operator_values(2))) {
      $output .= ' ' . t('@min and @max', array(
        '@min' => $this->value['min'],
        '@max' => $this->value['max'],
      ));
    }
    elseif (in_array($this->operator, $this
      ->operator_values(1))) {
      $output .= ' ' . check_plain($this->value['value']);
    }
    return $output;
  }


  public function _gf_stock_get_region() {
    if ($this->options['gf_stock_region_from_session']) {
      $current_region = gf_stock_get_current_region();
      if (in_array($current_region, array_keys($this->gf_stock_regions))) {
        return $current_region;
      }
    }
    return $this->options['gf_stock_region'];
  }

  public function query() {
    $this->ensure_my_table();
    $nid = 'node.nid';
    $region = $this->_gf_stock_get_region();
    $expression = "(SELECT SUM(stock) FROM {$this->table_alias} 
      WHERE nid = {$nid} AND region = '$region')";

    $info = $this->operators();
    if (!empty($info[$this->operator]['method'])) {
      $this->{$info[$this->operator]['method']}($expression);
    }
  }


  /**
   * Сравнение по вхождение(или невхождение) в диапазон значений.
   */
  function op_between($expression) {
    $op = ($this->operator == 'between') ? 'BETWEEN' : 'NOT BETWEEN';
    $this->query->add_where_expression($this->options['group'], 
      "$expression $op :min AND :max",
      [
        ':min' => $this->value['min'],
        ':max' => $this->value['max']
      ]);
  }

  /**
   * Простое сравнение (типа больше/меньше).
   */
  function op_simple($expression) {
    $this->query->add_where_expression($this->options['group'], 
      "$expression $this->operator :value",
      [':value' => $this->value["value"]]);                              
  }

  /**
   * Пустое значение.
   */
  function op_empty($expression) {
    $op = ($this->operator == 'empty') ? 'IS NULL' : 'IS NOT NULL';

    $this->query->add_where_expression($this->options['group'], 
      "$expression $op");
  }

  /**
   * Соответствие по регулярке.
   */
  function op_regex($expression) {
    $this->query->add_where_expression($this->options['group'], 
      "$expression RLIKE :value", [':value' => $this->value["value"]]);
  }
}
