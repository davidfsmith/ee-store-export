<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Store Export Model File
 * ----------------------------------------------------------------------------------------------
 * Utility to export completed orders
 *
 * ----------------------------------------------------------------------------------------------
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
class Store_export_model
{

    // Set the table names (Espresso)
    private $_store_orders              = 'store_orders';
    private $_store_order_items         = 'store_order_items';
    private $_store_payments            = 'store_payments';
    private $_store_shipping_rules      = 'store_shipping_rules';
    private $_store_shipping_methods    = 'store_shipping_methods';

    // TDL Export
    private $_tdle_emails               = 'se_emails';
    private $_tdle_emails_recipients    = 'se_recipients';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->EE =& get_instance();
    }

    public function get_orders_count($status = 'complete')
    {
        return sizeof($this->get_orders($status));
    }

    public function get_orders($status = 'complete')
    {
        // Create a nice big query
        $query = $this->EE->db->select(
            'eso.order_id AS order_id,' .
            'eso.order_completed_date AS order_completed_date,' .
            'esoi.order_item_id  AS order_item_id,' .
            'eso.billing_name AS billing_name, ' .
            'eso.order_email AS order_email, ' .
            'eso.billing_phone AS billing_phone, ' .
            'eso.billing_address1 AS billing_address1, ' .
            'eso.billing_address2 AS billing_address2, ' .
            'eso.billing_address3 AS billing_address3, ' .
            'eso.billing_postcode AS billing_postcode, ' .
            'eso.billing_country AS billing_country, ' .
            'eso.order_total AS order_total, ' .
            'eso.order_custom1 AS order_custom1, ' .
            'eso.shipping_name AS shipping_name,' .
            'eso.shipping_address1 AS shipping_address1, ' .
            'eso.shipping_address2 AS shipping_address2, ' .
            'eso.shipping_address3 AS shipping_address3, ' .
            'eso.shipping_postcode AS shipping_postcode, ' .
            'eso.shipping_country AS shipping_country, ' .
            'esoi.item_qty AS item_qty, ' .
            'esoi.sku AS sku, ' .
            'esoi.title AS title, ' .
            'esoi.price_inc_tax AS price_inc_tax, ' .
            'esoi.item_total AS item_total, ' .
            'essr.base_rate AS base_rate, ' .
            'essm.title AS shipping_method_name'
        )->from(
            $this->_store_orders . ' eso'
        )->join(
            $this->_store_payments . ' esp', 'esp.order_id = eso.order_id'
        )->join(
            $this->_store_order_items . ' esoi', 'esoi.order_id = eso.order_id'
        )->join(
            $this->_store_shipping_rules . ' essr', 'essr.shipping_method_id = eso.shipping_method_id'
        )->join(
            $this->_store_shipping_methods . ' essm', 'essm.shipping_method_id = eso.shipping_method_id '
        )->where(
            'esp.payment_status', $status
        )->order_by(
            'eso.order_id', 'ASC'
        )->get();

        return $query->result();
    }

    public function set_order_status($order_id, $status = 'processed')
    {
        $data = array(
            'payment_status'   => $status,
        );

        $this->EE->db->where('order_id', $order_id);
        $this->EE->db->update($this->_store_payments, $data);

        // Check we have updated some data
        return ($this->EE->db->affected_rows() > 0) ? true : false;

    }

    public function get_emails()
    {
        $query = $this->EE->db->get($this->_tdle_emails);
        return $query->result_array();
    }

    public function get_default_email()
    {
        $query = $this->EE->db->get_where($this->_tdle_emails,  array('email_default' => 1), 1, 0);
        return $query->result_array();
    }

    public function get_email($email_id)
    {
        $query = $this->EE->db->get_where($this->_tdle_emails,  array('email_id' => $email_id), 1, 0);
        return $query->result_array();
    }

    public function update_email($email_data, $email_id = false)
    {
        $email_data['email_default'] = 0;

        if ($email_id)
        {
            $this->EE->db->where('email_id', $email_id);
            $this->EE->db->update($this->_tdle_emails, $email_data);

            return ($this->EE->db->affected_rows() > 0) ? $email_id : false;
        }
        else
        {
            $this->EE->db->insert($this->_tdle_emails, $email_data);
            return ($this->EE->db->affected_rows() > 0) ? $this->EE->db->insert_id() : false;
        }
    }

    public function delete_email($email_id)
    {
        if ($this->count_recipients($email_id) > 0)
        {
            $this->delete_recipients($email_id);
        }
        $this->EE->db->delete($this->_tdle_emails, array('email_id' => $email_id));
        return ($this->EE->db->affected_rows() > 0) ? true : false;
    }

    public function get_recipients($email_id)
    {
        $query = $this->EE->db->get_where($this->_tdle_emails_recipients, array('email_id' => $email_id));
        return $query->result_array();
    }

    public function add_recipients($email_recipients, $email_id)
    {
        if ($this->count_recipients($email_id) > 0)
        {
            $this->delete_recipients($email_id);
        }

        $row_count = 0;
        for ($i = 0; $i < sizeof($email_recipients); $i++) {
            $recipient_data = array(
                'email_id'          => $email_id,
                'recipient_name'    => $email_recipients[$i]['email_recipient_name'],
                'recipient_email'   => $email_recipients[$i]['email_recipient_address']
            );

            $this->EE->db->insert($this->_tdle_emails_recipients, $recipient_data);
            $row_count += $this->EE->db->affected_rows();
        }
        return ($row_count == sizeof($email_recipients)) ? true : false;
    }

    public function delete_recipients($email_id)
    {
        $this->EE->db->delete($this->_tdle_emails_recipients, array('email_id' => $email_id));
        return ($this->EE->db->affected_rows() > 0) ? true : false;
    }

    public function count_recipients($email_id)
    {
        ee()->db->where('email_id', $email_id);
        return ee()->db->count_all_results($this->_tdle_emails_recipients);
    }

}

/* End of file store_export_model.php */
/* Location: ./system/expressionengine/third_party/tdl_export/model/store_export_model.php */