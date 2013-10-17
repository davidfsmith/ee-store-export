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
        $this->_form_link   = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.STORE_EXPORT_MODULE_NAME;

        // Set the right navigation
        ee()->cp->set_right_nav(
            array(
                ee()->lang->line('add_email') => $this->_module_link.AMP.'method=add_email'
                // ee()->lang->line('config')     => $this->_module_link.AMP.'method=config'
            )
        );

        // default global view variables
        ee()->load->vars(array(
            'cp_tdl_table_template_recipients' => array(
                'table_open'        => '<table class="mainTable" id="email_recipients" border="0" cellspacing="0" cellpadding="0">'
            )
        ));
    }

    /**
     * Module index
     *
     * @access public
     * @return boolean
     */
    public function index()
    {
        // Set the title
        $this->_set_page_title('store_export_module_name');

        $vars = array();
        $vars['form_hidden'] = null;
        $vars['files'] = array();

        $vars['unprocessed_order_count'] = ee()->store_export->get_orders_count();
        $vars['download_url'] = $this->_module_link.AMP.'method=create_csv';

        // Display the emails in the system
        $vars['emails'] = ee()->store_export->get_emails();

        $vars['modify_email_link']  = $this->_module_link.AMP.'method=mod_email'.AMP.'email_id=';
        $vars['delete_email_link']  = $this->_module_link.AMP.'method=del_email'.AMP.'email_id=';
        $vars['send_email_link']    = $this->_module_link.AMP.'method=send_email'.AMP.'email_id=';

        return ee()->load->view('index', $vars, true);
    }

    /**
     * add_email
     *     Called to render the add / modify email page
     *
     * @param array $email_values Used to render the page when doing a modify
     *
     * @return html page
     */
    public function add_email($vars = array())
    {
        if (sizeof($vars) == 0) {
            // Set the breadcrumb
            ee()->cp->set_breadcrumb($this->_module_link, ee()->lang->line('store_export_module_name'));
            $this->_set_page_title('add_email');

            $vars = array();
            // Set the title

            $vars['email']['email_name']            = '';
            $vars['email']['email_subject']         = '';
            $vars['email']['email_body_orders']     = '';
            $vars['email']['email_body_no_orders']  = '';

            $vars['email']['email_address'][] = array(
                'id'                => 0,
                'recipient_name'    => '',
                'recipient_email'   => ''
            );
            $vars['text_action'] = 'add_email';
            $vars['form_hidden']    = array(
                'email_recipients_count'  => sizeof($vars['email']['email_address'])
            );
        }

        $vars['action_url']     = $this->_form_link.AMP.'method=update_email';
        $vars['message']        = null;

        ee()->javascript->output(array(
            '$(\'#add_another_recipient\').click(function() {
                var email_recipients_count = parseInt($(\'input[name="email_recipients_count"]\').val()) + 1;
                $(\'input[name="email_recipients_count"]\').val(email_recipients_count);

                var fields_to_clone = new Array("email_recipient_name_", "email_recipient_address_");

                for (i = 0; i < fields_to_clone.length; i++) {
                    field_counter = i + 1;
                    $(\'#email_recipients tbody tr:nth-child(\' + field_counter + \')\').clone().find(\'input\').each(function() {
                        $(this).attr({
                          \'name\': function(_, name) { return fields_to_clone[i] + email_recipients_count },
                          \'value\': \'\'
                        });
                    }).end().appendTo(\'#email_recipients\');
                }

                $(\'input[name="email_recipient_name_\' + email_recipients_count + \'"]\').focus();
                return false;
            });'
        ));

        return ee()->load->view('add_email', $vars, true);
    }

    /**
     * mod_email
     *     Called to modify an email, uses add_email to render the page
     *
     * @return html page via add_email
     */
    public function mod_email()
    {
        // Get the email ID
        $email_id = ee()->input->get('email_id', true);

        if ($email_id)
        {
            // Set the breadcrumb and title
            ee()->cp->set_breadcrumb($this->_module_link, ee()->lang->line('store_export_module_name'));
            $this->_set_page_title('modify_email');

            $vars = array();
            $email = ee()->store_export->get_email($email_id);
            $vars['email'] = $email[0];
            $email_address = ee()->store_export->get_recipients($email_id);

            if (sizeof($email_address) > 0)
            {
                $vars['email']['email_address'] = $email_address;
            }
            else
            {
                $vars['email']['email_address'][] = array(
                    'id'                => 0,
                    'recipient_name'    => '',
                    'recipient_email'   => ''
                );
            }
            $vars['text_action'] = 'modify_email';
            $vars['form_hidden'] = array(
                'email_id' => $email_id,
                'email_recipients_count'  => sizeof($vars['email']['email_address'])
            );

            return $this->add_email($vars);
        }
        else
        {
            // Redirect to the module home page
            ee()->functions->redirect($this->_module_link);
        }
    }

    public function del_email()
    {
        // Get the email ID
        $email_id = ee()->input->get('email_id', true);
        if ($email_id) {
            if (ee()->store_export->delete_email($email_id)) {
                ee()->session->set_flashdata('message_success', ee()->lang->line('email_deleted'));
            } else {
                ee()->session->set_flashdata('message_failure', ee()->lang->line('email_delete_fail'));
            }
        }
        ee()->functions->redirect($this->_module_link);
    }

    /**
     * update_email
     *     Called to update the email data
     * @return boolean true on success otherwise false
     */
    public function update_email()
    {
        // Get the email ID
        $email_id = ee()->input->get_post('email_id', false);

        $email_data = array(
            'email_name'            => ee()->input->post('email_name', true),
            'email_subject'         => ee()->input->post('email_subject', true),
            'email_body_orders'     => ee()->input->post('email_body_orders', true),
            'email_body_no_orders'  => ee()->input->post('email_body_no_orders', true),
        );

        $email_recipients_count = ee()->input->post('email_recipients_count', true);
        for ($i = 1; $i <= $email_recipients_count; $i++) {
            if (strlen(ee()->input->post('email_recipient_address_' . $i)) > 0)
            {
                $email_recipients[$i - 1]['email_recipient_name']       = ee()->input->post('email_recipient_name_' . $i, true);
                $email_recipients[$i - 1]['email_recipient_address']    = ee()->input->post('email_recipient_address_' . $i, true);
            }
        }

        $email_id = ee()->store_export->update_email($email_data, $email_id);
        if ($email_id) {
            // Delete and Create recipients
            ee()->store_export->add_recipients($email_recipients, $email_id);
            if (ee()->input->get('email_id', false)) {
                ee()->session->set_flashdata('message_success', ee()->lang->line('email_modified'));
            } else {
                ee()->session->set_flashdata('message_success', ee()->lang->line('email_added'));
            }
        } else {
            if ($email_id) {
                ee()->session->set_flashdata('message_failure', ee()->lang->line('email_modified_fail'));
            } else {
                ee()->session->set_flashdata('message_failure', ee()->lang->line('email_add_fail'));
            }
        }
        ee()->functions->redirect($this->_module_link);

    }

    public function send_email()
    {
        // Get the email ID
        $email_id = ee()->input->get_post('email_id', false);

        $email = ee()->store_export->get_email($email_id);
        $email_address = ee()->store_export->get_recipients($email_id);

        $email_recipients = array();
        for ($i = 0; $i < sizeof($email_address); $i++) {
            $email_recipients[] = $email_address[$i]['recipient_email'];
        }

        if (ee()->store_export->get_orders_count() > 0)
        {
            $email_message = $email[0]['email_body_orders'];
            $email_filename = $this->write_csv(false);
            if (!$email_filename)
            {
                $email .= '\n\n'.ee()->lang->line('email_attachment_error');
            }
        }
        else
        {
            $email_message = $email[0]['email_body_no_orders'];
        }

        ee()->load->library('email');
        ee()->email->from(ee()->config->item('webmaster_name').' <'.ee()->config->item('webmaster_email').'>');
        ee()->email->to(implode(', ', $email_recipients));
        ee()->email->subject($email[0]['email_subject']);
        ee()->email->message($email_message);

        if ($email_filename)
        {
            ee()->email->attach($email_filename);
        }

        ee()->email->Send(false);
        echo ee()->email->print_debugger(array('headers'));
        exit;
    }

    public function send_default_email()
    {

    }

    public function download_csv()
    {

    }

    public function write_csv($update = true)
    {
        ee()->load->library('file');

        if (!write_file('', $this->create_csv($update)))
        {
            return false;
        }
        else
        {

        }
    }

    /**
     * create_csv
     *     Called to manually create the CSV
     * @param  boolean $update Update the orders as exported or not
     * @return [type]          [description]
     */
    public function create_csv($update = true)
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

        // ee()->output->set_header('Content-Type: application/vnd.ms-excel');   //->output('something,in,here');
        // ee()->output->set_header('Content-Length: ' . strlen($orders));
        // ee()->output->set_header('Content-Disposition: attachment; filename="orders.csv"');

        // Headers are set in the view (EE wasn't behaving)
        print $orders;

        exit;
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