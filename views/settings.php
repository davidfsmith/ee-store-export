<?=form_open($action_url, $form_hidden)?>
<?php

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('setting'),
        lang('value')
    );

    foreach ($settings as $setting) {
        $this->table->add_row(
            lang($setting['key']),
            form_input(array(
                'name'      => $setting['key'],
                'value'     => $setting['value'],
                'maxlength' => 100,
                'size'      => '50',
                'style'     => 'width:50%')
            )
        );
    }

    echo $this->table->generate();
?>

<?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?>

<?=form_close()?>