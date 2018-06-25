<?php


/**
 * @file
 * Drush commands for import files.
 */

/**
 * Implementation of hook_drush_command().
 * Sample: drush importer-import
 */
function importkit_drush_command()
{

    $items['importkit-import'] = array(
      'callback' => 'importkit_drush_import',
      'description' => 'Import products from CommerceML2 format.',
      /*'arguments' => array(
        'silent' => 'Silent mode on/off.',
      ),*/
      'options' => array(
        'silent' => 'Silent mode on/off.',
      ),
    );

    $items['importkit-update-all'] = array(
      'callback' => 'importkit_drush_update_all',
      'description' => 'Update all products from CommerceML2 format.',
      'options' => array(
        'silent' => 'Silent mode on/off.',
      ),
    );

    $items['importkit-update-products'] = array(
      'callback' => 'importkit_drush_update_products',
      'description' => 'Update only products from CommerceML2 format.',
      'options' => array(
        'silent' => 'Silent mode on/off.',
      ),
    );

    $items['importkit-update-offers'] = array(
      'callback' => 'importkit_drush_update_offers',
      'description' => 'Update only offers from CommerceML2 format.',
      'options' => array(
        'silent' => 'Silent mode on/off.',
      ),
    );

    $items['importkit-remove'] = array(
      'callback' => 'importkit_drush_remove',
      'description' => 'Remove all imported products.',
      'options' => array(
        'silent' => 'Silent mode on/off.',
      ),
    );

    return $items;
}

/**
 * Implementation of hook_drush_help().
 */
function importkit_drush_help($section)
{
    switch ($section) {
        case 'drush:importkit-import':
            return t("Import or update products from CommerceML2 format using default settings.");

        case 'drush:importkit-remove':
            return t("Remove all imported products.");

        case 'drush:importkit-update-all':
            return t("Update all products from CommerceML2 format.");

        case 'drush:importkit-update-products':
            return t("Update only products from CommerceML2 format.");

        case 'drush:importkit-update-offers':
            return t("Update only offers from CommerceML2 format.");
    }
}

/**
 * Import 1C files.
 * import or update data from 1c export files
 */
function importkit_drush_import()
{
    // Полный импорт
    $values = array(
      'importkit_terms' => array('import' => 'import'),
      'importkit_products' => array('import' => 'import'),
      'importkit_offers' => array('import' => 'import'),
      'importkit_prices' => array('import' => 'import'),
      'importkit_stock' => array('import' => 'import'),
    );

    variable_set('imporkit_settings_ImportkitListnerCategory', 1);
    variable_set('imporkit_settings_ImportkitListnerContent', 2);
    variable_set('imporkit_settings_ImportkitListnerOffers', 3);
    variable_set('imporkit_settings_ImportkitListnerPrice', 4);
    variable_set('imporkit_settings_ImportkitListnerStock', 5);

    importkit_initialize('import');

    return importkit_drush_run($values);
}

/**
 * Import 1C files.
 * import or update data from 1c export files
 */
function importkit_drush_update_all()
{
    // Полный импорт
    $values = array(
      'importkit_terms' => 'update',
      'importkit_products' => 'update',
      'importkit_offers' => 'update',
      'importkit_prices' => 'update',
      'importkit_stock' => 'update',
    );

    variable_set('imporkit_settings_ImportkitListnerCategory', 1);
    variable_set('imporkit_settings_ImportkitListnerContent', 2);
    variable_set('imporkit_settings_ImportkitListnerOffers', 3);
    variable_set('imporkit_settings_ImportkitListnerPrice', 4);
    variable_set('imporkit_settings_ImportkitListnerStock', 5);

    importkit_initialize('update');

    return importkit_drush_run($values);
}

/**
 * Import 1C files.
 * import or update data from 1c export files
 */
function importkit_drush_update_products()
{
    // Полный импорт
    $values = array(
      'importkit_terms' => false,
      'importkit_products' => 'update',
      'importkit_offers' => false,
      'importkit_prices' => false,
      'importkit_stock' => false,
    );

    variable_set('imporkit_settings_ImportkitListnerCategory', 1);
    variable_set('imporkit_settings_ImportkitListnerContent', 2);
    variable_set('imporkit_settings_ImportkitListnerOffers', 3);
    variable_set('imporkit_settings_ImportkitListnerPrice', 4);
    variable_set('imporkit_settings_ImportkitListnerStock', 5);

    importkit_initialize('update');

    return importkit_drush_run($values);
}

/**
 * Import 1C files.
 * import or update data from 1c export files
 */
function importkit_drush_update_offers()
{
    // Полный импорт
    $values = array(
      'importkit_terms' => false,
      'importkit_products' => false,
      'importkit_offers' => false,
      'importkit_prices' => 'update',
      'importkit_stock' => 'update',
    );

    variable_set('imporkit_settings_ImportkitListnerCategory', 1);
    variable_set('imporkit_settings_ImportkitListnerContent', 2);
    variable_set('imporkit_settings_ImportkitListnerOffers', 3);
    variable_set('imporkit_settings_ImportkitListnerPrice', 4);
    variable_set('imporkit_settings_ImportkitListnerStock', 5);

    importkit_initialize('update');

    return importkit_drush_run($values);
}

/**
 * Importkit tools of clear iported products.
 */
function importkit_drush_remove()
{
    // Удаление импортированных продуктов
    $values = array(
      'importkit_terms' => 'remove',
      'importkit_prices' => 'remove',
      'importkit_stock' => 'remove',
      'importkit_offers' => 'remove',
      'importkit_products' => 'remove',
    );

    variable_set('imporkit_settings_ImportkitListnerCategory', 1);
    variable_set('imporkit_settings_ImportkitListnerPrice', 2);
    variable_set('imporkit_settings_ImportkitListnerStock', 3);
    variable_set('imporkit_settings_ImportkitListnerOffers', 4);
    variable_set('imporkit_settings_ImportkitListnerContent', 5);

    importkit_initialize('remove');

    return importkit_drush_run($values);
}

function importkit_drush_run($values)
{
    $t = get_t();

    if (!variable_get('importkit_content_module_use', '')) {
        importkit_verbose( $t("Please go to 'importkit settings' and save it") );

        return false;
    }

    $form_state = array(
      'values' => $values,
      'submit_handlers' => array('importkit_form_submit'),
    );

    importkit_form_submit(null, $form_state, true);

    cache_set('importkit_progressive', true);

    cache_clear_all('importkit_silent', 'cache');

    if (drush_get_option('silent')) {
        cache_set('importkit_silent', true);
    }

    importkit_product_batch_set();

    // Переводим в рабочий режим
    variable_set('maintenance_mode', 0);

    return true;
}