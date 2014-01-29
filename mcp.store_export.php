<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
if (file_exists(PATH_THIRD.'store_export/config.php') === true) require PATH_THIRD.'store_export/config.php';
else require dirname(dirname(__FILE__)).'/store_export/config.php';

/**
 * Store Export - Control Panel Elements
 * ----------------------------------------------------------------------------------------------
 * Utility to export completed orders
 *
 * ----------------------------------------------------------------------------------------------
 *
 * @category  Data_Export
 * @package   store_export
 * @author    David Smith <david.smith@wirewool.com>
 * @copyright 2013 Wirewool Ltd <http://www.wirewool.com>
 * @license   http://www.wirewool.com/license/
 * @version   1.0
 * @link      http://www.wirewool.com/
 * @see       http://www.wirewool.com/ee/store_export
 */
class store_export_mcp
{

    /**
     * Module constructor
     *
     * @access public
     * @return
     */
    public function __construct()
    {

        // Load the libraries and helpers
        ee()->load->library('javascript');
        ee()->load->library('table');
        ee()->load->helper('form');

        // Load the model
        ee()->load->model('store_export_model', 'store_export');

        // Set the module_link
        $this->_module_link = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.STORE_EXPORT_MODULE_NAME;

        // Set the right navigation
        ee()->cp->set_right_nav(
            array(
                ee()->lang->line('settings')     => $this->_module_link.AMP.'method=settings',
                ee()->lang->line('cron_link')    => 'index.php?ACT='.ee()->cp->fetch_action_id(STORE_EXPORT_MODULE_NAME, 'cron_task')
            )
        );
    }

    /**
     * Module index
     *
     * @access public
     * @return boolean
     */
    public function index()
    {
        $this->_set_page_title('store_export_module_name');

        $vars = array();
        $vars['form_hidden'] = null;
        $vars['files'] = array();

        $vars['unprocessed_order_count'] = ee()->store_export->get_orders_count();
        $vars['download_url']   = 'index.php?ACT='.ee()->cp->fetch_action_id(STORE_EXPORT_MODULE_NAME, 'download_csv');
        $vars['settings_link']  = $this->_module_link.AMP.'method=settings';

        $vars['log_entries'] = ee()->store_export->get_log_entries();

        return ee()->load->view('index', $vars, true);
    }

    public function settings() {
        // Set the title
        $this->_set_page_title('store_export_module_name');

        $vars = array();
        $vars['settings']       = ee()->store_export->get_settings_values();
        $vars['action_url']     = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.STORE_EXPORT_MODULE_NAME.AMP.'method=update_settings';
        $vars['form_hidden']    = array();

        return ee()->load->view('settings', $vars, true);
    }

    public function update_settings() {
        $settings = ee()->store_export->get_settings_values();

        $updated_settings = array();
        foreach ($settings as $setting) {
            $updated_settings[] = array(
                'key'   => $setting['key'],
                'value' => ee()->input->post($setting['key'], true)
            );
        }

        if (ee()->store_export->update_settings_values($updated_settings)) {
            ee()->store_export->update_log('Settings updated');
            ee()->session->set_flashdata('message_success', ee()->lang->line('settings_updated_success'));
        } else {
            ee()->store_export->update_log('Settings update failed');
            ee()->session->set_flashdata('message_failure', ee()->lang->line('settings_updated_fail'));
        }

        ee()->functions->redirect($this->_module_link);
    }

    private function _set_page_title($title)
    {
        // $this->EE->cp->set_variable was deprecated in 2.6
        if (version_compare(APP_VER, '2.6', '>=')) {
            ee()->view->cp_page_title = ee()->lang->line($title);
        } else {
            ee()->cp->set_variable('cp_page_title', ee()->lang->line($title));
        }
    }
}

/* End of file mcp.store_export.php */
/* Location: ./system/expressionengine/third_party/store_export/mcp.store_export.php */