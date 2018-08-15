<?php

/**
 * Implements hook_views_data().
 */
function gf_stock_views_data(){
  $data['gf_stock_stocks']['table']['group'] = t('GF Stock');
  $data['gf_stock_stocks']['table']['base'] = [
    'field' => 'nid',
    'title' => t('Product nid'),
  ];
    $data['gf_stock_stocks']['table']['join'] = array(
    'node' => array(
      'left_field' => 'nid',
      'field' => 'nid',
    ),
  );
  $data['gf_stock_stocks']['nid'] = [
    'title' => t('Product Node Nid'),
    'help' => t('Product Node identifier.'),
    'relationship' => [
      'base' => 'node', 
      'base field' => 'nid', 
      'handler' => 'views_handler_relationship',
      'label' => t('Product'),
      'title' => t('Product'),
    ],
  ];
  $data['gf_stock_stocks']['stock'] = [
    'title' => t('Region Stock'),
    'help' => t('Stock data for the Region'),
    'field' => [
      'handler' => 'gf_stock_handler_field_stock',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'handler' => 'gf_stock_handler_filter_stock',
    ],
    'sort' => [
      'handler' => 'gf_stock_handler_sort_stock',
    ],
  ];

  // Цены по регионам.
  $data['gf_stock_prices']['table']['group'] = t('GF Stock');
  $data['gf_stock_prices']['table']['base'] = [
    'field' => 'nid',
    'title' => t('Product nid'),
  ];
    $data['gf_stock_prices']['table']['join'] = array(
    'node' => array(
      'left_field' => 'nid',
      'field' => 'nid',
    ),
  );
  $data['gf_stock_prices']['nid'] = [
    'title' => t('Product Node Nid'),
    'help' => t('Product Node identifier.'),
    'relationship' => [
      'base' => 'node', 
      'base field' => 'nid', 
      'handler' => 'views_handler_relationship',
      'label' => t('Product'),
      'title' => t('Product'),
    ],
  ];
  $data['gf_stock_prices']['price'] = [
    'title' => t('Region Price'),
    'help' => t('Price for a specific region.'),
    'field' => array(
      'handler' => 'gf_stock_handler_field_region_price',
      'click sortable' => TRUE,
    ),
  ];

  return $data;
}
