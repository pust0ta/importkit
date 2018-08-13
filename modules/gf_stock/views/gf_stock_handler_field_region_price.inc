<?php

/**
 * @uses views_handler_field_numeric
 */
class gf_stock_handler_field_region_price extends views_handler_field_numeric{

  use gf_stock_region_price_trait; 


  public function admin_summary() {
    $region = $this->options['gf_stock_region_price'];
    return strtoupper($this->gf_stock_region_prices[$region]);
  }


  /**
   * {@inheritdoc}
   */
  public function query() {
    $table_alias = $this->ensure_my_table();
    $region = $this->options['gf_stock_region_price'];
    $this->query->add_where(0, $table_alias . '.price_id', $region);
  }
}
