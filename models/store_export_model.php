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
    private $_se_emails                 = 'se_emails';
    private $_se_emails_recipients      = 'se_recipients';
    private $_se_settings               = 'se_settings';

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

    public function get_settings_values()
    {
        $query = $this->EE->db->get($this->_se_settings);
        return $query->result_array();
    }

    public function update_settings_values($settings_data)
    {
        $this->EE->db->update_batch($this->_se_settings, $settings_data, 'key');

        return ($this->EE->db->affected_rows() > 0) ? true : false;
    }

}

/* End of file store_export_model.php */
/* Location: ./system/expressionengine/third_party/tdl_export/model/store_export_model.php */