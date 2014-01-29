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
class store_export
{

    /**
     * Module constructor
     *
     * @access public
     * @return
     */
    public function __construct()
    {

        // Load the model
        ee()->load->model('store_export_model', 'store_export');
    }

    public function cron_task()
    {
        if (ee()->store_export->get_orders_count() > 0)
        {
            $this->write_csv(true);
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

    public function download_csv($update = true)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="orders.csv"');
        print $this->_create_csv($update);
        exit;
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

}

/* End of file mod.store_export.php */
/* Location: ./system/expressionengine/third_party/store_export/mod.store_export.php */