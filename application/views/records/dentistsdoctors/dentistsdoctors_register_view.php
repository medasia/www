<script>
$(document).ready(function() {
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});
	$('#date_accredited').datepicker({format: 'yyyy-mm-dd'});
	$('#addclinic').click(function() {
		// $('#clinicset tr:last').after('<tr><td>CELL ROW</td></tr>');
		$('#clinicset').clone().appendTo('#clinic_info');
	});
});
</script>
<h1>Dentists and Doctors</h1>
<button id='toggleSlide'>Add new Dentist/Doctor</button>
<div id='formAdd'>
<?php 
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/15');?>
<?php
$inputs = array(
				array(form_label('Download Template for Dentist and Doctors', 'multiup'), form_submit('download', 'Download'))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('utils/fileuploader/upto/dentistsdoctors');?>
<?php

$clinicTmpl =  array(
					array('', ''),
					array(form_label('Clinic name', 'clinic_name'), form_input(array('name'=>'clinic_name[]', 'id'=>'clinic_name', 'size'=>'20'))),
					array(form_label('Hospital name', 'hospital_name'), form_input(array('name'=>'hospital_name[]', 'id'=>'hospital_name', 'size'=>'20'))),
					array(form_label('Street Address', 'street_address'), form_input(array('name'=>'street_address[]', 'id'=>'street_address', 'size'=>'20'))),
					array(form_label('Subdivision/Village', 'subdivision_village'), form_input(array('name'=>'subdivision_village[]', 'id'=>'subdivision_village', 'size'=>'20'))),
					array(form_label('Barangay', 'barangay'), form_input(array('name'=>'barangay[]', 'id'=>'barangay', 'size'=>'20'))),
					array(form_label('City', 'city'), form_input(array('name'=>'city[]', 'id'=>'city', 'size'=>'20'))),
					array(form_label('Province', 'province'), form_input(array('name'=>'province[]', 'id'=>'province', 'size'=>'20'))),
					array(form_label('Region', 'region'), form_input(array('name'=>'region[]', 'id'=>'region', 'size'=>'20'))),
					array(form_label('Clinic Sched', 'clinic_sched'), form_input(array('name'=>'clinic_sched[]', 'id'=>'clinic_sched', 'size'=>'20'))),
					array('', '')
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0" id="clinicset">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
$clinic = form_fieldset('Clinic Information', array('id'=>'clinic_info'));
$clinic.= $this->table->generate($clinicTmpl);
$clinic.= form_fieldset_close();

$inputs = array(
				array(form_label('Upload multiple dentist/doctor', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup')), form_submit('upload', 'Upload'))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/dentistsdoctors/register'); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Type', 'type'), form_dropdown('type', array('MD' => 'MD', 'Dentist' => 'Dentist'))),
				array(form_label('First name', 'firstname'), form_input(array('name'=>'firstname', 'id'=>'firstname', 'size'=>'50'))),
				array(form_label('Middle name', 'middlename'), form_input(array('name'=>'middlename', 'id'=>'middlename', 'size'=>'50'))),
				array(form_label('Last name', 'lastname'), form_input(array('name'=>'lastname', 'id'=>'lastname', 'size'=>'50'))),
				array(form_label('Specialization', 'specialization'), form_input(array('name'=>'specialization', 'id'=>'specialization', 'size'=>'50'))),
				
				array(form_button(array('name' => 'addclinic', 'id' => 'addclinic', 'content' => 'Add clinic')), $clinic),
				
				array(form_label('Mobile Number', 'mobile_number'), form_input(array('name'=>'mobile_number', 'id'=>'mobile_number', 'size'=>'50'))),
				array(form_label('Contact Number', 'contact_number'), form_input(array('name'=>'contact_number', 'id'=>'contact_number', 'size'=>'50'))),
				array(form_label('Fax Number', 'fax_number'), form_input(array('name'=>'fax_number', 'id'=>'fax_number', 'size'=>'50'))),
				array(form_label('Date Accredited', 'date_accredited'), form_input(array('name'=>'date_accredited', 'id'=>'date_accredited',  'value'=>'YYYY-MM-DD'))),
				array(form_label('Status', 'status'), form_dropdown('status', array('ACCREDIT' => 'ACCREDIT', 'DIS-ACCREDIT' => 'DIS-ACCREDIT'))),
				array(form_label('Remarks', 'remarks'), form_input(array('name'=>'remarks', 'id'=>'remarks', 'size'=>'50'))),
				
				array('', form_submit('submit', 'Register'))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));
?>
<?php echo form_close(); ?>
</div>