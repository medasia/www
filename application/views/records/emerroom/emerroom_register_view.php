<script>
$(document).ready(function() {
	$('#dateexpiration').datepicker({format: 'yyyy-mm-dd'});
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});
	$('form#emerroom_form').validate({
		rules:{
			card_number: {
				required: true,
				number: true,
				remote: {
    				url: '<?=base_url();?>useraccounts/check_exist/emergency_room/card_number',
    				type: 'POST',
    				data: {
    					term: function()
    					{
    						return $('#card_number').val();
    					}
    				}
    			}
			},
			pin_number: {
				required:true,
				number: true,
				remote: {
    				url: '<?=base_url();?>useraccounts/check_exist/emergency_room/pin_number',
    				type: 'POST',
    				data: {
    					term: function()
    					{
    						return $('#pin_number').val();
    					}
    				}
    			}
			},
			amount: {
				required: true,
				number: true
			},
			dateexpiration: {
				required: true,
				date: true
			}
		},
		messages:{
			card_number: {
				required: 'This field is required',
				number: 'Enter a valid amount only (Number only)',
				remote: 'Card Number already exist'
			},
			pin_number: {
				required: 'This field is required',
				number: 'Enter a valid amount only (Number only)',
				remote: 'Pin Number already exist'
			},
			amount: {
				required: 'This field is required',
				number: 'Enter a valid amount only (Number only)'
			},
			dateexpiration: {
				required: 'This field is required',
				date: 'Enter a valid date only'
			}
		}
	});
});
</script>
<h1>Emergency Room</h1>
<button id='toggleSlide' class="btn btn-default">Add new ER Card</button>
<div id='formAdd'>
<?php 
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/8');?>
<?php
$inputs = array(
				array(form_label('Download Template for Emergency Room', 'multiup'), form_submit(array('name'=>'download','value'=>'Download','class'=>'btn btn-warning')))
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
<?php echo form_open_multipart('utils/fileuploader/upto/emerroom');?>
<?php
$inputs = array(
				array(form_label('Upload multiple ER Cards', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup','class'=>'form-group')), form_submit(array('name'=>'upload','value'=>'Upload','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/emerroom/register',array('id'=>'emerroom_form')); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('ER Card #', 'card_number'), form_input(array('name'=>'card_number', 'id'=>'card_number', 'size'=>'20','placeholder'=>'Card Number','class'=>'form-control'))),
				array(form_label('ER Pin #', 'pin_number'), form_input(array('name'=>'pin_number', 'id'=>'pin_number', 'size'=>'20','placeholder'=>'Pin Number','class'=>'form-control'))),
				array(form_label('Benefit Amount'),form_input(array('name'=>'amount','id'=>'amount','size'=>'20','placeholder'=>'Amount','class'=>'form-control'))),
				array(form_label('Card Expiration'),form_input(array('name'=>'dateexpiration','id'=>'dateexpiration','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array('', form_submit(array('name'=>'submit','value'=>'Submit','class'=>'btn btn-success')))
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