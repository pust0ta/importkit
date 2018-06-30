<?php

/**
 * Implements hook_views_data().
 */
function gf_stock_views_data(){
  $data['importkit_cml_stock']['table']['group'] = t('GF Stock');
  $data['importkit_cml_stock']['table']['base'] = [
    'field' => 'nid',
    'title' => t('Product nid'),
  ];
    $data['importkit_cml_stock']['table']['join'] = array(
    'node' => array(
      'left_field' => 'nid',
      'field' => 'nid',
    ),
  );
  $data['importkit_cml_stock']['nid'] = [
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
  $data['importkit_cml_stock']['stock'] = [
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

  return $data;
}
