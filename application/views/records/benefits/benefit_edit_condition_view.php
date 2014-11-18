<script>
$(document).ready(function() {
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type	: 'textarea',
		cols 	: '50',
		rows	: '10',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'benefits.benefit_set_condition', key: <?=$id?>}
	});
});
</script>
<h2>Edit Condition <?php echo $condition_name;?></h2>
<?php
	$tmpl = array(
			'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table-bordered">',
			'table_close' => '</table>'
			);
	$this->table->set_template($tmpl);

	$this->table->add_row('Condition Name: ','<div class="editable" id="condition_name">'.$condition_name.'</div>');
	$this->table->add_row('Condition Details: ','<div class="editable" id="condition_details">'.$condition_details.'</div>');

	echo $this->table->generate();
?>