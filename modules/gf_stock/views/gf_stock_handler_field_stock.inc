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
    $table_alias = $this->ensure_my_table();
    $params = $this->options['group_type'] != 'group' ?
      array('function' => $this->options['group_type']) : array();
    $nid = 'node.nid';
    $region = $this->options['gf_stock_region'];
    $expression = "(SELECT SUM(stock) FROM {importkit_cml_stock} ics
      WHERE ics.nid = {$nid} AND ics.region = '$region')";
    $this->field_alias = $this->query->add_field(NULL, 
      $expression, $table_alias . '_region_stock_' . $region, $params);
    $this->add_additional_fields();
  }
}
