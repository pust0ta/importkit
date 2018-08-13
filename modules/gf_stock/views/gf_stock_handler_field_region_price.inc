<?php

/**
 * @uses views_handler_field_numeric
 */
class gf_stock_handler_field_region_price extends views_handler_field_numeric{

  use gf_stock_region_price_trait; 


  public function admin_summary() {
    return strtoupper($this->gf_stock_get_region_name());
  }


  /**
   * {@inheritdoc}
   */
  public function query() {
    parent::query();
    $table = $this->ensure_my_table();
    $region = $this->options['gf_stock_region_price'];
    $this->query
        ->add_where($this->options['group'], db_or()
        ->condition($table . '.price_id', $region));
  }
}
