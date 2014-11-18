<html>
	<head>
		<title>Medriks - Hospital Record of <?php echo $name;?></title>
<script>
$(document).ready(function() {
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'hospital', key: <?=$id?>}
	});

	$('.editable2').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/hospital_type/<?=$type?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'hospital', key: <?=$id?>}
	});

	$('.editable3').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/hospital_category/<?=$category?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'hospital', key: <?=$id?>}
	});

	$('.editable4').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type      : 'datepicker',
		datepicker: {
			format: 'yyyy-mm-dd'
		},
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		placeholder      : 'YYYY-MM-DD',
		submitdata : {table: 'hospital', key: <?=$id?>},
	});
	
	$('.editable5').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/accred/<?=$status?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'hospital', key: <?=$id?>}
	});

	$('.editable6').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/classification/<?=$classification?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'hospital', key: <?=$id?>}
	});
});
</script>
</head>
<h2>View Records of <?php echo $name?></h2>
<?php
$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',
				'table_close'         => '</table>'
				);
$this->table->set_template($tmpl);
$back = anchor(base_url()."records/hospclinic/", "Back");

$this->table->add_row('Hospital/Clinic name', '<div class="editable" id="name">'.$name.'</div>');
$this->table->add_row('Classification', '<div class="editable6" id="classification">'.$classification.'</div>');
$this->table->add_row('Type', '<div class="editable2" id="type">'.$type.'</div>'); //DROPDOWN
$this->table->add_row('Branch', '<div class="editable" id="branch">'.$branch.'</div>');

$this->table->add_row('Street Address', '<div class="editable" id="street_address">'.$street_address.'</div>');
$this->table->add_row('Subdivision/Village', '<div class="editable" id="subdivision_village">'.$subdivision_village.'</div>');
$this->table->add_row('Barangay', '<div class="editable" id="barangay">'.$barangay.'</div>');
$this->table->add_row('City', '<div class="editable" id="city">'.$city.'</div>');
$this->table->add_row('Province', '<div class="editable" id="province">'.$province.'</div>');
$this->table->add_row('Region', '<div class="editable" id="region">'.$region.'</div>');

$this->table->add_row('Contact Person', '<div class="editable" id="contact_person">'.$contact_person.'</div>');
$this->table->add_row('Contact Number', '<div class="editable" id="contact_number">'.$contact_number.'</div>');
$this->table->add_row('Fax Number', '<div class="editable" id="fax_number">'.$fax_number.'</div>');
$this->table->add_row('E-mail Address', '<div class="editable" id="email">'.$email.'</div>');

$this->table->add_row('Medical Coordinator Name', '<div class="editable" id="med_coor_name">'.$med_coor_name.'</div>');
$this->table->add_row('Room','<div class="editable" id="room">'.$room.'</div>');
$this->table->add_row('Schedule','<div class="editable" id="schedule">'.$schedule.'</div>');
$this->table->add_row('Contact Number','<div class="editable" id="contact_no">'.$contact_no.'</div>');
$this->table->add_row('E-mail','<div class="editable" id="med_coor_email">'.$med_coor_email.'</div>');

$this->table->add_row('Medical Coordinator Name 2', '<div class="editable" id="med_coor_name_2">'.$med_coor_name_2.'</div>');
$this->table->add_row('Room 2', '<div class="editable" id="room_2">'.$room_2.'</div>');
$this->table->add_row('Schedule 2', '<div class="editable" id="schedule_2">'.$schedule_2.'</div>');
$this->table->add_row('Contact Number 2','<div class="editable" id="contact_no_2">'.$contact_no_2.'</div>');
$this->table->add_row('E-mail 2','<div class="editable" id="med_coor_email_2">'.$med_coor_email_2.'</div');

$this->table->add_row('Category', '<div class="editable3" id="category">'.$category.'</div>'); //DROPDOWN
$this->table->add_row('Date Accredited', '<div class="editable4" id="date_accredited">'.mdate('%M %d, %Y', mysql_to_unix($date_accredited)).'</div>');
$this->table->add_row('Status', '<div class="editable5" id="status">'.$status.'</div>');
$this->table->add_row('Remarks', '<div class="editable" id="remarks">'.$remarks.'</div>');

// $this->table->add_row(anchor(base_url()."records/hospclinic/delete/".$id."/", "Delete Hospital/Clinic"),($back));

echo $this->table->generate(); 
?>
</html>