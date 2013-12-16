<?php

/**
 * Install / Uninstall and updates the modules
 *
 * @category  Data_Export
 * @package   Store_Export
 * @author    David Smith <david.smith@wirewool.com>
 * @copyright 2013 Wirewool Ltd <http://www.wirewool.com>
 * @license   http://www.wirewool.com/license/
 * @version   1.0
 * @link      http://www.wirewool.com/
 * @see       http://www.wirewool.com/ee/store_export
 */

if ( ! defined('STORE_EXPORT_NAME'))
{
    define('STORE_EXPORT_NAME',          'Store Export');
    define('STORE_EXPORT_MODULE_NAME',   'Store_Export');
    define('STORE_EXPORT_VERSION',       '0.9.0');
}

$config['name']         = STORE_EXPORT_NAME;
$config['module_name']  = STORE_EXPORT_MODULE_NAME;
$config['version']      = STORE_EXPORT_VERSION;

//$config['nsm_addon_updater']['versions_xml'] = 'http://www.wirewool.com/ee/store_export/feed';

/* End of file config.php */
/* Location: ./system/expressionengine/third_party/store_export/config.php */