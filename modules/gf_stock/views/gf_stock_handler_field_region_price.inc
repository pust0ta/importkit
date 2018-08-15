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
    $this->ensure_my_table();
    $nid = 'node.nid';
    $price_id = $this->options['gf_stock_region_price'];
    $region = strtolower($this->gf_stock_get_region_name());
    // Значение регионального ценника выводим подзапросом. 
    // Ибо сраный вьюс так и не научился работать с составными
    // первичными ключами. Или я в нем это тупо не нашел(что врядли).
    $expression = "(SELECT price FROM {$this->table_alias} 
      WHERE nid = {$nid} AND price_id = '$price_id')";
    $this->field_alias = $this->query->add_field(NULL, 
      $expression, $this->table_alias . '_region_' . $region);
    $this->add_additional_fields();
  }
}
