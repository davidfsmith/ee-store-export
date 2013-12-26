<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Store Export - Language File - English
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
$lang = array(

// Required for MODULES page

'store_export_module_name'          => 'Store Export Tool',
'store_export_module_description'   => 'Utility to export completed orders for the Expresso Store plugin',

//----------------------------------------

// Additional Key => Value pairs go here
'settings'                          => 'Module Settings',
'unprocessed_order_count'           => 'Total number of unprocessed orders',
'download_orders'                   => 'Download Orders CSV',
'no_orders_to_export'               => 'There are no orders to export.',
'no_log_entries'                    => 'There are no log entries to show.',

'file_number'                       => 'Order file number',
'file_created_at'                   => 'Created at',
'no_files_have_been_created'        => 'No files have been created.',

'ftp_file_location'                 => 'FTP File Location (no trailing / required)',
'ftp_backup_file_location'          => 'FTP Backup File Location (no trailing / required)',
'file_prefix'                       => 'Filename prefix',
'file_counter_length'               => 'File counter length',
'file_counter'                      => 'File counter',

'file_export_fail'                  => 'Main file export failed to write',
'file_export_success'               => 'Main file export completed successfully',
'backup_file_export_fail'           => 'Backup file export failed to write',
'backup_file_export_success'        => 'Backup file export completed successfully',

'confirmation_email_active'         => 'Send export email confirmation (1 == Yes)',
'confirmation_email_sender'         => 'Email confirmation sender address',
'confirmation_email_recipient'      => 'Email confirmation recipient address',
'confirmation_email_subject'        => 'Email confirmation subject',
'confirmation_email_message'        => 'Email confirmation message',

'submit'                            => 'Submit',
'settings_updated_success'          => 'Settings successfully updated',
'settings_updated_fail'             => 'There was a problem updating the settings'
// END

);

/* End of file store_export_lang.php */
/* Location: ./system/expressionengine/third_party/store_export/language/english/store_export_lang.php */