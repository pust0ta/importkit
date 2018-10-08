<?php

/**
 * @uses views_handler_field_numeric
 */
class gf_stock_handler_sort_stock extends views_handler_sort{

  use gf_stock_handler_trait;

  public function admin_summary() {
    if (!empty($this->options['exposed'])) {
      return t('Exposed');
    }
    $output = strtoupper($this->options['gf_stock_region']);
    switch ($this->options['order']) {
      case 'ASC':
      case 'asc':
      default:
        $output .= t(', asc');
        break;
      case 'DESC':
      case 'desc':
        $output .= t(', desc');
        break;
    }
    return $output;
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

    $this->query->add_orderby(NULL, 
      $expression, $this->options['order'], $this->table_alias . '_region_stock_' . $region);
  }
}
