<?php

trait gf_stock_handler_trait {

  public function construct() {
    $this->gf_stock_regions = gf_stock_get_regions();
    parent::construct();
  }

  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['gf_stock_region'] = [
      'default' => $this->gf_stock_regions[GF_STOCK_REGION_RU],
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function options_form(&$form, &$form_state) {
    $form['gf_stock_region'] = array(
      '#type' => 'radios',
      '#title' => t('Stock Region'),
      '#description' => t('Select the region to display stock.'),
      '#options' => $this->gf_stock_regions,
      '#default_value' => $this->options['gf_stock_region'],
    );
    parent::options_form($form, $form_state);
  }
}
