<script>
$(document).ready(function() {
	$('#input_id').hide();
	$('#register').click(function(){
		$('#input_id').slideToggle('fast',function(){});
	});
	$("form#diagnosis_form").validate({
    			rules: {
    				diagnosis: {
    					required: true
    				}
    			},
    			messages: {
    				diagnosis: {
    					required: 'This field is required'
    				}
    			}
			});
});
</script>

<h1>Diagnosis</h1>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}
?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/20');?>
<?php
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$button = array(
			array(form_button(array('name'=>'register','id'=>'register','content'=>'Register Diagnosis','class'=>'btn btn-default')))
			);
	$this->table->set_template($tmpl);
	echo $this->table->generate($button);

	echo '<div id="input_id">';
	$inputs = array(
				array(form_label('Download Template for Diagnosis'),form_submit(array('name'=>'submit','value'=>'Download','class'=>'btn btn-warning')))
				);
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);
?>
<?php echo form_close();?>

<?php echo validation_errors();?>
<?php echo form_open_multipart('utils/fileuploader/upto/diagnosis');?>
<?php
	$inputs = array(
			array(form_label('Upload multiple Diagnosis'),form_upload(array('name'=>'file','id'=>'multiup','class'=>'form-group')), form_submit(array('name'=>'submit','value'=>'Upload','class'=>'btn btn-success')))
			);
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);
?>
<?php echo form_close();?>

<?php echo validation_errors();?>
<?php echo form_open('records/diagnosis/register',array('id'=>'diagnosis_form'));?>
<?php
	$input = array(
			array(form_label('Chief Complaint / Diagnosis: '),form_input(array('name'=>'diagnosis','id'=>'diagnosis','size'=>'50','placeholder'=>'Chief Complaint','class'=>'form-control'))),
			array('',form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
			);
	$this->table->set_template($tmpl);
	echo $this->table->generate($input);
?>
<?php echo form_close();?>
<?php echo '</div>';?>