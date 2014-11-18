<script>
$(document).ready(function(){
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'company_insurance', key: <?=$id?>}
	});

	$('.editable2').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type      : 'text',
		// datepicker: {
		// 	dateFormat: 'yy-mm-dd'
		// },
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		data      : 'YYYY-MM-DD',
		submitdata : {table: 'company_insurance', key: <?=$id?>}
	});
	$('.editable3').editable('<?=base_url()?>utils/ajaxeditinplace',{
		type		: 'textarea',
		cols		: '50',
		rows 		: '10',
		indicator	: 'Saving',
		cancel 		: 'Cancel',
		submit 		: 'OK',
		tooltip		: 'Click to edit...',
		onblur		: 'cancel',
		submitdata 	: {table: 'company_insurance', key: <?=$id?>}
	});
});
</script>

<h2>View Record of <?php echo $company.' - '.$insurance; ?></h2>
<?php
	$tmpl = array(
			'table_open' => '<table border="1" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
				);
	$this->table->set_template($tmpl);
	$this->table->add_row('Company','<div id="company">'.$company.'</div>');
	$this->table->add_row('Insurance','<div id="insurance">'.$insurance.'</div>');
	$this->table->add_row('Start','<div class="editable2" id="start">'.mdate('%M %d, %Y', mysql_to_unix($start)).'</div>');
	$this->table->add_row('End','<div class="editable2" id="end">'.mdate('%M %d, %Y', mysql_to_unix($end)).'</div>');
	$this->table->add_row('Notes/Remarks','<div class="editable3" id="notes">'.$notes.'</div>');

	echo $this->table->generate();
?>