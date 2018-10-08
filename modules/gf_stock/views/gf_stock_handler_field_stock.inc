<?php

/**
 * @uses views_handler_field_numeric
 */
class gf_stock_handler_field_stock extends views_handler_field_numeric{

  use gf_stock_handler_trait; 


  public function admin_summary() {
    return strtoupper($this->options['gf_stock_region']);
  }


  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensure_my_table();
    $nid = 'node.nid';
    $region = $this->options['gf_stock_region'];
    $expression = "(SELECT SUM(stock) FROM {$this->table_alias} 
      WHERE nid = {$nid} AND region = '$region')";
    $this->field_alias = $this->query->add_field(NULL, 
      $expression, $this->table_alias . '_region_stock_' . $region);
    $this->add_additional_fields();
  }
}
