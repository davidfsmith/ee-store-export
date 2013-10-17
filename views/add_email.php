<?=form_open($action_url, array('id' => 'form_email'), $form_hidden)?>
<?php

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang($text_action),
        ''
    );

    $this->table->add_row(
        lang('email_name'),
        form_input(array(
            'name'      => 'email_name',
            'value'     => $email['email_name'],
            'maxlength' => 100,
            'size'      => '50',
            'style'     => 'width:50%'))." (".lang('frm_email_name_notes').")"
    );

    $this->table->add_row(
        lang('email_subject'),
        form_input(array(
            'name'      => 'email_subject',
            'value'     => $email['email_subject'],
            'maxlength' => 250,
            'size'      => '50',
            'style'     => 'width:50%'))." (".lang('frm_email_subject_notes').")"
    );

    $this->table->add_row(
        lang('email_body_orders'),
        form_input(array(
            'name'      => 'email_body_orders',
            'value'     => $email['email_body_orders'],
            'maxlength' => 250,
            'size'      => '50',
            'style'     => 'width:50%'))." (".lang('frm_email_body_orders').")"
    );

    $this->table->add_row(
        lang('email_body_no_orders'),
        form_input(array(
            'name'      => 'email_body_no_orders',
            'value'     => $email['email_body_no_orders'],
            'maxlength' => 250,
            'size'      => '50',
            'style'     => 'width:50%'))." (".lang('frm_email_body_no_orders').")"
    );

    echo $this->table->generate();
    $this->table->clear();

    $this->table->set_template($cp_tdl_table_template_recipients);
    $this->table->set_heading(
        lang('add_recipients'),
        ''
    );

    foreach ($email['email_address'] as $email_recipient) {
        $c = 0;
        $c++;
        $this->table->add_row(
            lang('email_recipient_name'),
            form_input(array(
                'name'      => 'email_recipient_name_'.$c,
                'value'     => $email_recipient['recipient_name'],
                'maxlength' => 250,
                'size'      => '50',
                'style'     => 'width:50%'))." (".lang('frm_email_recipient_name').")"
        );

        $this->table->add_row(
            lang('email_recipient_address'),
            form_input(array(
                'name'      => 'email_recipient_address_'.$c,
                'value'     => $email_recipient['recipient_email'],
                'maxlength' => 250,
                'size'      => '50',
                'style'     => 'width:50%'))." (".lang('frm_email_recipient_address').")"
        );
    }

    echo $this->table->generate();
?>

<div class="tableFooter">
    <div class="tableSubmit">
        <?=form_submit(array('name' => 'add_recipient', 'value' => lang('add_another_recipient'), 'class' => 'submit', 'id' => 'add_another_recipient'))?>
        <?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?>
    </div>
</div>

<?=form_close()?>
