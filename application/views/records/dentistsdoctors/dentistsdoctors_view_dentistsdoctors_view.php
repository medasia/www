<html>
	<head>
		<title>Medriks - Record of Doctor<?php echo $lastname.', '.$firstname.' '.$middlename;?></title>
<script>
$(document).ready(function() {
	$('.editable#type').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/dentistsdoctors_type/<?=$type?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'dentistsanddoctors', key: <?=$id?>}
	});

	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'dentistsanddoctors', key: <?=$id?>}
	});

	$('.editable2').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type      : 'datepicker',
		datepicker: {
			format: 'yyyy-mm-dd'
		},
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		placeholder : 'YYYY-MM-DD',
		submitdata : {table: 'dentistsanddoctors', key: <?=$id?>},
	});

	$('.editable3').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/accred/<?=$type?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'dentistsanddoctors', key: <?=$id?>}
	});

	$('.editableClinic').editable('<?=base_url()?>utils/ajaxeditinplace', {
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : function(value, settings) {
			var column_name = this.id.replace(/\[[0-9]+\]/g, "");
			var str = this.id;
			var pos = str.indexOf("[") + 1;
			var primary_key = str.slice(pos, -1);
			return {table: 'clinics', key: primary_key, id: column_name};
		}
	});

	$('#addclinic').click(function() {
		// alert($(this).attr);
		$('#clinic_infoTMPL').clone().appendTo('#clin');
	});
});
</script>
</head>
<h2>View record of <?php echo $lastname.', '.$firstname.' '.$middlename; ?></h2>
<?php
//NEW CLINIC TEMPLATE
$clinicTmpl =  array(
					array('', ''),
					array(form_label('Clinic name', 'clinic_name'), form_input(array('name'=>'clinic_name[]', 'id'=>'clinic_name', 'size'=>'20'))),
					array(form_label('Hospital name', 'hospital_name'), form_input(array('name'=>'hospital_name[]', 'id'=>'hospital_name', 'size'=>'20'))),
					array(form_label('Street Address', 'street_address'), form_input(array('name'=>'street_address[]', 'id'=>'street_address', 'size'=>'20'))),
					array(form_label('Subdivision/Village', 'subdivision_village'), form_input(array('name'=>'subdivision_village[]', 'id'=>'subdivision_village', 'size'=>'20'))),
					array(form_label('Barangay', 'barangay'), form_input(array('name'=>'barangay[]', 'id'=>'barangay', 'size'=>'20'))),
					array(form_label('City', 'city'), form_input(array('name'=>'city[]', 'id'=>'city', 'size'=>'20'))),
					array(form_label('Province', 'province'), form_input(array('name'=>'province[]', 'id'=>'province', 'size'=>'20'))),
					array(form_label('Clinic Sched', 'clinic_sched'), form_input(array('name'=>'clinic_sched[]', 'id'=>'clinic_sched', 'size'=>'20'))),
					array(form_button('delete[]', 'Delete'),form_submit('add[]', 'Add')),
					array('', '')
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0" id="clinicset">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
$clinic = form_open('records/dentistsdoctors/addClinic', array('id' => 'addClinicForm'));
$clinic.= form_fieldset('Clinic Information', array('id'=>'clinic_infoTMPL'));
$clinic.= $this->table->generate($clinicTmpl);
$clinic.= form_fieldset_close();
$clinic.= form_close();
echo '<div style="display:none">'.$clinic.'</div>';

// if($clinics != 0) {
// 	$clinicForm = '';
// 	foreach($clinics as $key => $value) {
// 		$clinicTmpl = $this->table->add_row('Clinic name', '<div class="editableClinic" id="clinic_name['.$value['id'].']">'.$value['clinic_name'].'</div>');
// 		$clinicTmpl = $this->table->add_row('Hospital name', '<div class="editableClinic" id="hospital_name['.$value['id'].']">'.$value['hospital_name'].'</div>');
// 		$clinicTmpl = $this->table->add_row('Street address', '<div class="editableClinic" id="street_address['.$value['id'].']">'.$value['street_address'].'</div>');
// 		$clinicTmpl = $this->table->add_row('Subdivision/Village', '<div class="editableClinic" id="subdivision_village['.$value['id'].']">'.$value['subdivision_village'].'</div>');
// 		$clinicTmpl = $this->table->add_row('Barangay', '<div class="editableClinic" id="barangay['.$value['id'].']">'.$value['barangay'].'</div>');
// 		$clinicTmpl = $this->table->add_row('City', '<div class="editableClinic" id="city['.$value['id'].']">'.$value['city'].'</div>');
// 		$clinicTmpl = $this->table->add_row('Province', '<div class="editableClinic" id="province['.$value['id'].']">'.$value['province'].'</div>');
// 		$clinicTmpl = $this->table->add_row('Region', '<div class="editableClinic" id="region['.$value['id'].']">'.$value['region'].'</div>');
// 		$clinicTmpl = $this->table->add_row('Clinic Sched', '<div class="editableClinic" id="clinic_sched['.$value['id'].']">'.$value['clinic_sched'].'</div>');
// 		$template = array(
// 					'table_open'	=> '<table border="1" cellpadding="4" cellspacing="0" id="clinicset['.$value['id'].']">',
// 					'table_close'	=> '</table>'
// 					);
// 		$this->table->set_template($template); 
// 		$clinicForm.= form_fieldset('Clinic Information', array('id'=>'clinic_info'));
// 		$clinicForm.= $this->table->generate($clinicTmpl);
// 		$clinicForm.= form_fieldset_close();
// 	}
// } else {
// 	$clinicForm = 'NO CLINICS';

// }

$clinicTmpl2 = $this->table->add_row('Address', '<div class="editable" id="clinic1">'.$clinic1.'</div>');
$clinicTmpl2 = $this->table->add_row('Address', '<div class="editable" id="clinic2">'.$clinic2.'</div>');
$clinicTmpl2 = $this->table->add_row('Address', '<div class="editable" id="clinic3">'.$clinic3.'</div>');
$clinicTmpl2 = $this->table->add_row('Address', '<div class="editable" id="clinic4">'.$clinic4.'</div>');
$clinicTmpl2 = $this->table->add_row('Address', '<div class="editable" id="clinic5">'.$clinic5.'</div>');

$template = array(
				'table_open' => '<table border="1" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
$this->table->set_template($template);
$clinicForm2 = form_fieldset('<b>Clinic Information</b>', array('id'=>'clinic_info'));
$clinicForm2.= $this->table->generate($clinicTmpl2);
$clinicForm2.= form_fieldset_close();

$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',
				'table_close'         => '</table>'
				);
$this->table->set_template($tmpl);
$this->table->add_row('Type', '<div class="editable" id="type">'.$type.'</div>');
$this->table->add_row('Lastname', '<div class="editable" id="lastname">'.$lastname.'</div>');
$this->table->add_row('Firstname', '<div class="editable" id="firstname">'.$firstname.'</div>');
$this->table->add_row('Middlename', '<div class="editable" id="middlename">'.$middlename.'</div>');
$this->table->add_row('Specialization', '<div class="editable" id="specialization">'.$specialization.'</div>');

// if($clinics != 0) { $this->table->add_row('Clinic/s: ', '<div id="clin">'.$clinicForm.'</div>');
// 	// $this->table->add_row(form_button(array('name' => 'addclinic', 'id' => 'addclinics', 'content' => 'Add clinic')), '<div id="clin">'.$clinicForm.'</div>');
// } else {
// 	$this->table->add_row('ADD/REMOVE CLINIC', $clinicForm);
// }

$this->table->add_row('Clinic/s', $clinicForm2);

$this->table->add_row('Mobile #', '<div class="editable" id="mobile_number">'.$mobile_number.'</div>');
$this->table->add_row('Contact #', '<div class="editable" id="contact_number">'.$contact_number.'</div>');
$this->table->add_row('Fax #', '<div class="editable" id="fax_number">'.$fax_number.'</div>');
$this->table->add_row('E-mail Address','<div class="editable" id="email">'.$email.'</div>');
$this->table->add_row('Date Accredited', '<div class="editable2" id="date_accredited">'.mdate('%M %d, %Y', mysql_to_unix($date_accredited)).'</div>');
$this->table->add_row('Status', '<div class="editable3" id="status">'.$status.'</div>');
$this->table->add_row('Remarks', '<div class="editable" id="remarks">'.$remarks.'</div>');

// echo anchor('delete/something', 'Delete', array('onClick' => "return confirm('Are you sure you want to delete?')"));

$this->table->add_row(anchor(base_url()."records/dentistsdoctors/delete/".$id."/", "Delete Dentist/Doctor", array('onClick'=>"return confirm('Are you sure you want to delete this record?')")), ''); //form_submit('submit', 'Update', 'onclick="addClinicForm.submit();"'));

echo $this->table->generate(); 
?>
</html>