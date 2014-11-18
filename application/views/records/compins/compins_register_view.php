<script>
$(document).ready(function() {
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});
	$('#datestart').datepicker({format: 'yyyy-mm-dd'});
	$('#dateend').datepicker({format: 'yyyy-mm-dd'});
$(".dp").datepicker({
    format: 'dd.mm.yyyy',
    startDate: '01.01.2012',
    endDate: ''
  });
	$('#compins-comp').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/compins-comp', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data) {
					if(data.response =='true'){
						add(data.message);
					}
				}
			});
		}
	});
	$('#compins-ins').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/compins_insurance', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data) {
					if(data.response =='true'){
						add(data.message);
					}
				}
			});
		}
	});
	$("form#compins_form").validate({
    			rules: {
    				company: {
    					required: true
    				},
    			    insurance: {
    			        required: true
    			    },
    			    start: {
    			    	required: true
    			    },
    			    end: {
    			    	required: true
    			    }
    			},
    			messages: {
    				company: {
    					required: 'This field is required'
    				},
    			    insurance: {
    			        required: "This field is required"
    			    },
    			    start: {
    					required: 'This field is required'
    				},
    				end: {
    					required: 'This field is required'
    				}
    			}
			});
});
</script>
<h1>Company - Insurance</h1>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}
?>
<br>
<button id='toggleSlide' class='btn btn-default'>Add New Company - Insurance</button>
<div id='formAdd'>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/10');?>
<?php
$inputs = array(
				array(form_label('Download Template for Company and Insurance', 'multiup'), form_submit(array('value'=>'Download','class'=>'btn btn-warning')))
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
<?php echo form_open_multipart('utils/fileuploader/upto/company_insurance');?>
<?php
$inputs = array(
				array(form_label('Upload multiple Company - Insurance', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup','class'=>'form-group')), form_submit(array('value'=>'Upload','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/compins/register',array('id'=>'compins_form')); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Company', 'compins-comp'), form_input(array('name'=>'company', 'id'=>'compins-comp', 'size'=>'50','class'=>'form-control'))),
				array(form_label('Insurance', 'compins-ins'), form_input(array('name'=>'insurance', 'id'=>'compins-ins', 'size'=>'50','class'=>'form-control'))),
				array(form_label('Start Date', 'datestart'), form_input(array('name'=>'start', 'id'=>'datestart','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('End Date', 'dateend'), form_input(array('name'=>'end', 'id'=>'dateend','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Notes/Remarks'), form_textarea(array('name'=>'notes','id'=>'notes','cols'=>'50','rows'=>'10','class'=>'form-control'))),
				array('', form_submit(array('value'=>'Register','class'=>'btn btn-success')))
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