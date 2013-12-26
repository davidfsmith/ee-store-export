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
                ee()->lang->line('settings')     => $this->_module_link.AMP.'method=settings'
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
        $vars['download_url']   = $this->_module_link.AMP.'method=create_csv';
        $vars['settings_link']  = $this->_module_link.AMP.'method=settings';

        $vars['log_entries'] = ee()->store_export->get_log_entries();

        return ee()->load->view('index', $vars, true);
    }

    public function download_csv()
    {

    }

    public function cron_task()
    {
        if (ee()->store_export->get_orders_count() > 0)
        {
            $this->write_csv();
        }
    }

    public function write_csv($update = true)
    {
        ee()->load->helper('file');

        $file_counter = ee()->store_export->get_setting_value('file_counter');

        $file_name = ee()->store_export->get_setting_value('file_prefix').sprintf("%0".ee()->store_export->get_setting_value('file_counter_length')."d", $file_counter).".csv";
        $csv_data = $this->_create_csv(true);

        if (!write_file(ee()->store_export->get_setting_value('ftp_backup_file_location').'/'.$file_name, $csv_data))
        {
            ee()->store_export->update_log('Backup file export failed');
            ee()->session->set_flashdata('message_failure', ee()->lang->line('backup_file_export_fail'));
        } else {
            ee()->store_export->update_log('Backup file export completed');
            ee()->session->set_flashdata('message_success', ee()->lang->line('backup_file_export_success'));
        }

        if (!write_file(ee()->store_export->get_setting_value('ftp_file_location').'/'.$file_name, $csv_data))
        {
            ee()->store_export->update_log('Main file export failed');
            ee()->session->set_flashdata('message_failure', ee()->lang->line('file_export_fail'));
        } else {
            $file_counter++;
            ee()->store_export->update_setting_value('file_counter', $file_counter);
            ee()->store_export->update_log('Main file export completed');
            ee()->session->set_flashdata('message_success', ee()->lang->line('file_export_success'));

            if (ee()->store_export->get_setting_value('confirmation_email_active') == 1)
            {
                $this->_send_confirmation_email();
            }
        }

        ee()->functions->redirect($this->_module_link);
    }

    public function print_csv($update = true)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="orders.csv"');
        print $this->_create_csv($update);
        exit;
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

    /**
     * create_csv
     *     Called to manually create the CSV
     * @param  boolean $update Update the orders as exported or not
     * @return [type]          [description]
     */
    private function _create_csv($update = true)
    {
        $vars['orders'] = ee()->store_export->get_orders();

        $fields = array('shipping_name', 'shipping_address1', 'shipping_address2', 'shipping_address3', 'shipping_postcode', 'order_custom1', 'billing_name', 'billing_address1', 'billing_address2', 'billing_address3', 'billing_postcode', 'shipping_method_name');

        // Update the payment status for each order and remove "special" chars
        foreach ($vars['orders'] as $order) {
            // Update order status
            if ($update) {
                ee()->store_export->set_order_status($order->order_id);
            }

            // Remove "special" chars
            foreach ($fields as $field) {
                $order->$field = preg_replace('/[,\n]/', ' ', $order->$field);
            }
        }

        // Build the CSV
        $orders = ee()->load->view('csv_export', $vars, true);

        return $orders;
    }

    private function _send_confirmation_email()
    {
        ee()->load->library('email');
        ee()->email->from(ee()->store_export->get_setting_value('confirmation_email_sender'));
        ee()->email->to(ee()->store_export->get_setting_value('confirmation_email_recipient'));
        ee()->email->subject(ee()->store_export->get_setting_value('confirmation_email_subject'));
        ee()->email->message(ee()->store_export->get_setting_value('confirmation_email_message'));

        if (ee()->email->Send(true)) {
            ee()->store_export->update_log('Email sent successfully');
        } else {
            ee()->store_export->update_log('There was a problem sending the email');
        }

        return;
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