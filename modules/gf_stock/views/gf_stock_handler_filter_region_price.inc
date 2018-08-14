<?php

/**
 * @uses views_handler_field_numeric
 */
class gf_stock_handler_filter_region_price extends views_handler_filter_numeric{

  use gf_stock_region_price_trait;

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
    $output = strtoupper($this->gf_stock_get_region_name()) . ' ';

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
}
