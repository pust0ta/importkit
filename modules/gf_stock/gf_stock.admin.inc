<?php 

/**
 * Форма наcтроек модуля.
 */
function gf_stock_settings() {
  $rate_array = array(variable_get('gf_stock_yuan_exchange_rate', NULL), 
  variable_get('gf_stock_yuan_rate_last_update', NULL));
  if(in_array(NULL, $rate_array)) {
    gf_stock_update_yuan_exchange_rate(); 
  }

  $form['gf_stock_currency'] = array(
    '#type' => 'fieldset',
    '#title' => t('Basic Currency Settings')
  );
  // Информация о текущем курсе юаня к рублю.
  $form['gf_stock_currency']['gf_stock_current_yuan_exchange_rate'] = array(
    '#markup' => t('The current exchange rate of the yuan to the 
    ruble is @rate rubles per 1 yuan. Date of last update: @date',
array(
  '@rate' => variable_get('gf_stock_yuan_exchange_rate'),
  '@date' => variable_get('gf_stock_yuan_rate_last_update')
)),
  );

// Выбор валюты по-умолчанию.
$form['gf_stock_currency']['gf_stock_region_price_currency'] = array(
  '#type' => 'select',
  '#title' => t('Default currency'),
  '#description' => t('The currency in which the imported prices will be stored.'),
  '#options' => array(
    GF_STOCK_CURRENCY_CN => t('Yuan'),
    GF_STOCK_CURRENCY_RU => t('Ruble')
  ),
  '#default_value' => variable_get('gf_stock_region_price_currency', GF_STOCK_CURRENCY_CN),
);

$form['gf_stock_region_prices'] = array(
  '#type' => 'fieldset',
  '#title' => t('Prices by region'),
);

$form['gf_stock_region_prices']['gf_stock_region_price_cny_as_rub'] = array(
  '#type' => 'checkbox',
  '#title' => t('Convert a сhinese prices of a products from CNY to RUB'),
  '#description' => t('If checked, then the price for a Chinese warehouse will be displayed in rubles in the product object.'),
  '#default_value' => variable_get('gf_stock_region_price_cny_as_rub', FALSE),
);

$options = [];
$options[GF_STOCK_REGION_ALL] = t('All');

// Получаем список ID для цен регионов.
$query_region_ids = db_select('gf_stock_prices', 'p')
  ->fields('p', array('price_id', 'currency'))
  ->distinct('p.price_id' )
  ->execute()
  ->fetchAll();

foreach($query_region_ids as $name=>$region_id) {
  $region_ids[$region_id->price_id] = $region_id->price_id;
}

foreach(gf_stock_get_regions() as $name => $region){
  $options[$name] = $region;
  // Привязки имеющихся ID цен регионов к конкретным регионам.
  $form['gf_stock_region_prices']['gf_stock_region_price_'. $name] = array(
    '#type' => 'select',
    '#title' => t('Price ID for Region @region', array('@region' => drupal_strtoupper($name))),
    '#description' => t('The stock region that will be selected if the user did`t select their region.'),
    '#options' => $region_ids,
    '#default_value' => variable_get('gf_stock_region_price_' . $name),
  );
}

// Регион по-умолчанию. Когда пол-ль не выбирал никаких регионов.
$form['gf_stock_default_region'] = array(
  '#type' => 'select',
  '#title' => t('Default Region'),
  '#description' => t('The stock region that will be selected if the user did`t select their region.'),
  '#options' => $options,
  '#default_value' => variable_get('gf_stock_default_region', GF_STOCK_REGION_ALL),
  '#weight' => -1,
);

return system_settings_form($form);
}
