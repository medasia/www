<script>
$(document).ready(function() {
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'diagnosis', key: <?=$id?>}
	});
});
</script>

<h2>Update <?php echo $diagnosis; ?></h2>
<?php
	$tmpl = array(
			'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table-bordered">',
			'table_close' => '</table>'
			);
	$this->table->set_template($tmpl);
	$this->table->add_row('<b>Chief Complaint / Diagnosis</b>','<div class="editable" id="diagnosis">'.$diagnosis.'</div>');
	echo $this->table->generate();
?>