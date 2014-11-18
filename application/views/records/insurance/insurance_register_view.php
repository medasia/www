<script>
$(document).ready(function() {
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});
	$("form#insurance_form").validate({
    			rules: {
    				name: {
    					required: true
    				},
    				Attention_Name: {
    					required: true
    				},
    				Attention_Pos: {
    					required: true
    				},
    				Address: {
    					required: true
    				},
    			    Code: {
    			        required: true
    			    },
    			    billing_code: {
    					required: true
    				}
    			},
    			messages: {
    			   name: {
    					required: 'This field is required'
    				},
    				Attention_Name: {
    					required: 'This field is required'
    				},
    				Attention_Pos: {
    					required: 'This field is required'
    				},
    				Address: {
    					required: 'This field is required'
    				},
    			    Code: {
    			        required: 'This field is required'
    			    },
    			    billing_code: {
    					required: 'This field is required'
    				}
    			}
			});
});
</script>
<h1>Insurance</h1>
<?php 
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<br>
<button id='toggleSlide' class='btn btn-default'>Add new Insurance</button>
<div id='formAdd'>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/13');?>
<?php
$inputs = array(
				array(form_label('Download Template for Insurance', 'multiup'), form_submit(array('value'=>'Download','class'=>'btn btn-warning')))
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
<?php echo form_open_multipart('utils/fileuploader/upto/insurance');?>
<?php
$inputs = array(
				array(form_label('Upload multiple Insurance', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup','class'=>'form-group')), form_submit(array('value'=>'Upload','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/insurance/register',array('id'=>'insurance_form')); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Insurance', 'name'), form_input(array('name'=>'name', 'id'=>'name', 'size'=>'20','placeholder'=>'Insurance Name','class'=>'form-control'))),
				array(form_label('Attention Name', 'Attention_Name'), form_input(array('name'=>'Attention_Name', 'id'=>'Attention_Name', 'size'=>'20','placeholder'=>'Attention Name','class'=>'form-control'))),
				array(form_label('Attention Position', 'Attention_Pos'), form_input(array('name'=>'Attention_Pos', 'id'=>'Attention_Pos', 'size'=>'20','placeholder'=>'Attention Position','class'=>'form-control'))),
				array(form_label('Address', 'Address'), form_input(array('name'=>'Address', 'id'=>'Address', 'size'=>'20','placeholder'=>'Address','class'=>'form-control'))),
				array(form_label('Code', 'Code'), form_input(array('name'=>'Code', 'id'=>'Code', 'size'=>'20','placeholder'=>'Code','class'=>'form-control'))),
				// array(form_label('Vendor Account', 'vendor_account'), form_input(array('name'=>'vendor_account', 'id'=>'vendor_account', 'size'=>'20'))),
				array(form_label('Billing Code', 'billing_code'), form_input(array('name'=>'billing_code', 'id'=>'billing_code', 'size'=>'20','placeholder'=>'Billing Code','class'=>'form-control'))),
				array('', form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
echo form_hidden('Count', 0);
echo form_hidden('Count_Op', 0);
?>
<?php echo form_close(); ?>
</div>