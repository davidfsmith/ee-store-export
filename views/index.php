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
<?php if (sizeof($log_entries) > 0): ?>
<?php
    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('log_text'),
        lang('log_created_at')
    );

    foreach ($log_entries as $entry)
    {
        $this->table->add_row(
            $entry['log_text'],
            $entry['created_at']
        );
    }
?>

<?=$this->table->generate();?>

<?php else: ?>
<h3><?=lang('no_log_entries')?></h3>
<?php endif; ?>
