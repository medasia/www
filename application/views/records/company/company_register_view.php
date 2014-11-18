<script>
$(document).ready(function() {
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});
	$("form#company_form").validate({
    			rules: {
    				name: {
    					required: true
    				},
    			    code: {
    			        required: true
    			    }
    			},
    			messages: {
    				name: {
    					required: 'This field is required'
    				},
    			    code: {
    			        required: "This field is required"
    			    }
    			}
			});
});
</script>
<h1>Company</h1>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}
?>
<br>
<button id='toggleSlide' class="btn btn-default">Add New Company</button>
<div id='formAdd'>
<?php 
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/3');?>
<?php
$inputs = array(
				array(form_label('Download Template for Company', 'multiup'), form_submit(array('value'=>'Download','class'=>'btn btn-warning')))
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
<?php echo form_open_multipart('utils/fileuploader/upto/company');?>
<?php
$inputs = array(
				array(form_label('Upload multiple Companies', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup','class'=>'form-group')), form_submit(array('value'=>'Upload','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/company/register',array('id'=>'company_form')); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Company', 'name'), form_input(array('name'=>'name', 'id'=>'name', 'size'=>'20','class'=>'form-control','placeholder'=>'Company'))),
				array(form_label('Code', 'code'), form_input(array('name'=>'code', 'id'=>'code', 'size'=>'20','class'=>'form-control','placeholder'=>'Code'))),
				array('', form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
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