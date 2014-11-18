<script>
$(document).ready(function() {
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});
});
</script>
<h1>Hospital Account</h1>
<button id='toggleSlide'>Add new account</button>
<div id='formAdd'>
<?php 
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/7');?>
<?php
$inputs = array(
				array(form_label('Download Template for Hospital Account', 'multiup'), form_submit('download', 'Download'))
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
<?php echo form_open_multipart('utils/fileuploader/upto/hospaccnt');?>
<?php
$inputs = array(
				array(form_label('Upload multiple accounts', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup')), form_submit('upload', 'Upload'))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/hospaccnt/register'); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Account name', 'account_name'), form_input(array('name'=>'account_name', 'id'=>'account_name', 'size'=>'20'))),
				array(form_label('Vendor Account', 'vendor_account'), form_input(array('name'=>'vendor_account', 'id'=>'vendor_account', 'size'=>'20'))),
				array(form_label('Type', 'type'), form_input(array('name'=>'type', 'id'=>'type', 'size'=>'20'))),
				array(form_label('Terms', 'terms'), form_input(array('name'=>'terms', 'id'=>'terms', 'size'=>'20'))),
				array(form_label('Vat', 'vat'), form_input(array('name'=>'vat', 'id'=>'vat', 'size'=>'20'))),
				array(form_label('Days', 'days'), form_input(array('name'=>'days', 'id'=>'days', 'size'=>'20'))),
				array(form_label('Clinic/Hospital', 'clinic_hospital'), form_input(array('name'=>'clinic_hospital', 'id'=>'clinic_hospital', 'size'=>'20'))),
				array('', form_submit('submit', 'Register'))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
</div>