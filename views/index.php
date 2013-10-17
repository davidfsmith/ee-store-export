<?php if ($unprocessed_order_count > 0): ?>

<?php

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('unprocessed_order_count'),
        $unprocessed_order_count,
        '<a href="'.$download_url.'">'.lang('download_orders').'</a>'
    );

?>

<?=$this->table->generate();?>

<?php else: ?>
<h3><?=lang('no_orders_to_export')?></h3>
<?php endif; ?>
<hr>
<?php if (sizeof($emails) > 0): ?>
<?php

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('email_name'),
        lang('email_last_sent'),
        '',
        '',
        ''
    );

    if (sizeof($emails > 0)):
        foreach ($emails as $email)
        {
            $this->table->add_row(
                $email['email_name'],
                '',
                // $email->email_last_sent,
                '<a href="'.$modify_email_link.$email['email_id'].'">'.lang('modify_email').'</a>',
                '<a href="'.$delete_email_link.$email['email_id'].'">'.lang('delete_email').'</a>',
                '<a href="'.$send_email_link.$email['email_id'].'">'.lang('send_email').'</a>'
            );
        }
    endif;
?>

<?=$this->table->generate();?>

<?php else: ?>
<h3><?=lang('no_emails_have_been_setup')?></h3>
<?php endif; ?>