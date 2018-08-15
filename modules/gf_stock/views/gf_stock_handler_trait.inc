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

trait gf_stock_region_price_trait {

  public function construct() {
    parent::construct();
    $this->additional_fields['currency']['field'] = 'currency';
    $this->gf_stock_region_prices = _gf_stock_get_region_price_map();
  }


  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['gf_stock_region_price'] = [
      'default' => '',
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function options_form(&$form, &$form_state) {
    // Поле выбора региона, цену которого будет отображать
    // данное поле.
    $form['gf_stock_region_price'] = array(
      '#type' => 'radios',
      '#title' => t('Price Region'),
      '#description' => t('Select a price region.'),
      '#options' => $this->gf_stock_region_prices,
      '#default_value' => $this->options['gf_stock_region_price'],
    );
    parent::options_form($form, $form_state);
  }

  public function gf_stock_get_region_name() {
    // Получаем имя выбранного региона (для отображения в настройках views).
    $region = $this->options['gf_stock_region_price'];
    return $this->gf_stock_region_prices[$region];
  }

  public function get_value($values, $field = NULL) {
    $value = parent::get_value($values);
    $currency = $values->{$this->aliases['currency']};
    // Приводим значение, полученное из базы, к отображению
    // в дефолтной валюте(выбранной в настройках модуля).
    return _gf_stock_convert_to_default_currency($value, $currency);
  }
}
