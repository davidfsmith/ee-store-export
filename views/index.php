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
<?php if (sizeof($files) > 0): ?>
<?php

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('file_number'),
        lang('file_created_at')
    );

    if (sizeof($files > 0)):
        foreach ($filess as $file)
        {
            $this->table->add_row(
                $email['file_number'],
                $email['file_created_at']
            );
        }
    endif;
?>

<?=$this->table->generate();?>

<?php else: ?>
<h3><?=lang('no_files_have_been_created')?></h3>
<?php endif; ?>