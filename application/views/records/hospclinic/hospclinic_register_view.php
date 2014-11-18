<script>
$(document).ready(function() {
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});
	$('#date_accredited').datepicker({format: 'yyyy-mm-dd'});
});
</script>
<h1>Hospitals and Clinics</h1>
<button id='toggleSlide'>Add new hospital/clinic</button>
<div id='formAdd'>
<?php 
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/14');?>
<?php
$inputs = array(
				array(form_label('Download Template for Hospital and Clinics', 'multiup'), form_submit('download', 'Download'))
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
<?php echo form_open_multipart('utils/fileuploader/upto/hospclinic');?>
<?php
$inputs = array(
				array(form_label('Upload multiple hospital/clinic', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup')), form_submit('upload', 'Upload'))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/hospclinic/register'); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);

$addressTMPL =  array(
					array('', ''),
					array(form_label('Street Address', 'street_address'), form_input(array('name'=>'street_address', 'id'=>'street_address', 'size'=>'20'))),
					array(form_label('Subdivision/Village', 'subdivision_village'), form_input(array('name'=>'subdivision_village', 'id'=>'subdivision_village', 'size'=>'20'))),
					array(form_label('Barangay', 'barangay'), form_input(array('name'=>'barangay', 'id'=>'barangay', 'size'=>'20'))),
					array(form_label('City', 'city'), form_input(array('name'=>'city', 'id'=>'city', 'size'=>'20'))),
					array(form_label('Province', 'province'), form_input(array('name'=>'province', 'id'=>'province', 'size'=>'20'))),
					array(form_label('Region', 'region'), form_input(array('name'=>'region', 'id'=>'region', 'size'=>'20')))
				);
$this->table->set_template($template); 
$address = form_fieldset('Address Information');
$address.= $this->table->generate($addressTMPL);
$address.= form_fieldset_close();

$med_coor_tmpl = array(
					array('',''),
					array(form_label('Medical Coordinator Name: '), form_input(array('name'=>'med_coor_name', 'id'=>'med_coor_name', 'size'=>'20'))),
					array(form_label('Room: '), form_input(array('name'=>'room', 'id'=>'room', 'size'=>'20'))),
					array(form_label('Schedule: '), form_input(array('name'=>'schedule', 'id'=>'schedule', 'size'=>'20'))),
					array(form_label('Contact Number: '), form_input(array('name'=>'contact_no','id'=>'contact_no', 'size'=>'20')))
				);
$this->table->set_template($template);
$medical_coordinator = form_fieldset('Medical Coordinator');
$medical_coordinator.= $this->table->generate($med_coor_tmpl);
$medical_coordinator.= form_fieldset_close();

$inputs = array(
				array('', ''),
				array(form_label('Hospital/Clinic name', 'name'), form_input(array('name'=>'name', 'id'=>'name', 'size'=>'20'))),
				array(form_label('Type', 'type'), form_dropdown('type', array('Regular' => 'Regular', 'Blanket' => 'Blanket', 'Maximum' => 'Maximum'))),
				array(form_label('Classification'), form_dropdown('clasification', array('')))
				array(form_label('Branch', 'branch'), form_input(array('name'=>'branch', 'id'=>'branch', 'size'=>'20'))),
				array('', $address),
				array(form_label('Contact Person', 'contact_person'), form_input(array('name'=>'contact_person', 'id'=>'contact_person', 'size'=>'20'))),
				array(form_label('Contact Number', 'contact_number'), form_input(array('name'=>'contact_number', 'id'=>'contact_number', 'size'=>'20'))),
				array(form_label('Fax Number', 'fax_number'), form_input(array('name'=>'fax_number', 'id'=>'fax_number', 'size'=>'20'))),
				array('', $medical_coordinator),
				array(form_label('Category', 'category'), form_dropdown('category', array('Level 1' => 'Level 1', 'Level 2' => 'Level 2', 'Level 3' => 'Level 3', 'Level 4' => 'Level 4'))),
				array(form_label('Date Accredited', 'date_accredited'), form_input(array('name'=>'date_accredited', 'id'=>'date_accredited', 'value'=>'YYYY-MM-DD'))),
				array(form_label('Status', 'status'), form_dropdown('status', array('ACCREDITED' => 'ACCREDITED', 'DIS-ACCREDITED' => 'DIS-ACCREDITED', 'DO NOT PROMOTE'=>'DO NOT PROMOTE'))),
				array(form_label('Remarks', 'remarks'), form_textarea(array('name'=>'remarks', 'id'=>'remarks', 'cols'=>'50', 'rows'=>'10'))),
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