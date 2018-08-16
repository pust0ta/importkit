<?php

/**
 * @uses views_handler_field_numeric
 */
class gf_stock_handler_field_region_price extends views_handler_field_numeric{

  public function construct() {
    parent::construct();
    // $this->additional_fields['currency']['field'] = 'currency';
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

  public function gf_stock_get_currency() {
    // Хелпер. Получаем название валюты текущего региона.
    // На данный момент используется для преобразования значения поля
    // к значению в дефолтной валюте сайта.
    switch ($this->gf_stock_get_region_name()) {
    case GF_STOCK_REGION_RU:
      return GF_STOCK_CURRENCY_RU;
    case GF_STOCK_REGION_CN:
      return GF_STOCK_CURRENCY_CN;
    }
    return FALSE;
  }

  public function get_value($values, $field = NULL) {
    $value = parent::get_value($values);
    if ($currency = $this->gf_stock_get_currency()) {
      // Приводим значение, полученное из базы, к отображению
      // в дефолтной валюте(выбранной в настройках модуля).
      return _gf_stock_convert_to_default_currency($value, $currency);
    }
    return $value;
  }


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
