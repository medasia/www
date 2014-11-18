<script>
$(document).ready(function(){
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace',{
		type		: 'textarea',
		rows		: '10',
		cols		: '50',
		indicator	: 'Saving...',
		cancel		: 'Cancel',
		submit		: 'OK',
		tooltip		: 'Click to edit...',
		onblur		: 'cancel',
		submitdata	: {table: 'benefits.benefit_set_exclusion', key: <?=$id?>}
	});
});
</script>
<h2>Edit Exclusion <?php echo $exclusion_name;?></h2>
<?php
	$tmpl = array(
		'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table-bordered">',
		'table_close' => '</table>'
			);
	$this->table->set_template($tmpl);

	$this->table->add_row('Exclusion Name: ','<div class="editable" id="exclusion_name">'.$exclusion_name.'</div>');
	$this->table->add_row('Exclusion Details: ','<div class="editable" id="exclusion_details">'.$exclusion_details.'</div>');

	echo $this->table->generate();
?>