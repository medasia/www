<script>
$(document).ready(function() {
	$('#topsheet_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#topsheet_date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#start, #end').datepicker({format: 'yyyy-mm-dd'});
	$('#search_id, #download_id, #topsheet_id').hide();
	$('#download').click(function() {
		$('#download_id').slideToggle('fast',function(){});
		$('#search_id').hide();
		$('#topsheet_id').hide();
	});
	$('#search').click(function() {
		$('#search_id').slideToggle('fast',function(){});
		$('#download_id').hide();
		$('#topsheet_id').hide();
	});
	$('#topsheet').click(function() {
		$('#topsheet_id').slideToggle('fast',function(){});
		$('#download_id').hide();
		$('#search_id').hide();
	});
	$('#hospital_name').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/verifications_hospclinic',
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data) {
					if(data.response == 'true'){
						add(data.message);
						$('#hospital_branch').val(data.message2);
					}
				}
			});
		}
	});

	$('#insurance_topsheet').autocomplete({
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
						$('test').val(data.message2);
					}
				}
			});
		}
	});

	$("form#download_form").validate({
    			rules: {
    				start: {
    					required: true
    				},
    			    end: {
    			        required: true
    			    }
    			},
    			messages: {
    				start: {
    					required: 'This field is required'
    				},
    			    end: {
    			        required: "This field is required"
    			    }
    			}
			});
	$("form#topsheet_form").validate({
    			rules: {
    				topsheet_date_from: {
    					required: true
    				},
    			    topsheet_date_to: {
    			        required: true
    			    }
    			},
    			messages: {
    				topsheet_date_from: {
    					required: 'This field is required'
    				},
    			    topsheet_date_to: {
    			        required: "This field is required"
    			    }
    			}
			});
});
</script>
<html>
<head>
	<title>Medriks - Summary</title>
</head>

<h1>Summary</h1>
<?php echo validation_errors(); ?>
<?php echo form_open('summary/search');?>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);

	$search = array(
			array(form_button(array('name'=>'search','id'=>'search','content'=>'Search Summary','class'=>'btn btn-info')),
			form_button(array('name'=>'download','id'=>'download','content'=>'Download Record','class'=>'btn btn-info')),
			form_button(array('name'=>'topsheet','id'=>'topsheet','content'=>'Search Topsheet','class'=>'btn btn-info'))),
			);
	$this->table->set_template($template);
	echo $this->table->generate($search);

	$inputs = array(
			array(form_label('Approval Code:'),form_input(array('name'=>'code','id'=>'code','size'=>'20','class'=>'form-control','placeholder'=>'Approval Code'))),
			array(form_label('Patient Name:'),form_input(array('name'=>'patient_name','id'=>'patient_name','size'=>'20','class'=>'form-control','placeholder'=>'Patient Name'))),
			array(form_label('Company:'),form_input(array('name'=>'company_name','id'=>'company_name','size'=>'20','class'=>'form-control','placeholder'=>'Company Name'))),
			array(form_label('Insurance:'),form_input(array('name'=>'insurance_name','id'=>'insurance_name','size'=>'20','class'=>'form-control','placeholder'=>'Insurance Name'))),
			array(form_label('Hospital:'),form_input(array('name'=>'hospital_name','id'=>'hospital_name','size'=>'20','class'=>'form-control','placeholder'=>'Hospital Name'))),
			array(form_label('Chief Complaint/Diagnosis:'),form_input(array('name'=>'chief_complaint','id'=>'chief_complaint','size'=>'20','class'=>'form-control','placeholder'=>'Chief Complaint'))),
			array(form_label('Availment Type:'),form_dropdown('availment_type',array(''=>'','In-Patient'=>'In-Patient','Out-Patient'=>'Out-Patient','In and Out Patient'=>'In and Out Patient'))),
			array(form_label('Date Start:'),form_input(array('name'=>'start','id'=>'start','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
			array(form_label('Date End:'),form_input(array('name'=>'end','id'=>'end','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
			array(form_label('Claims Status:'),form_dropdown('claims_status',array(''=>'','BILLED'=>'Billed'))),
			array(form_label('User:'),form_input(array('name'=>'user','id'=>'user','size'=>'20','class'=>'form-control','placeholder'=>'User'))),
			array(form_label('Sort By:'),form_dropdown('sort_by',array('patient_name'=>'Patient Name',
																	'company_name'=>'Company',
																	'insurance_name'=>'Insurance',
																	'hospital_name'=>'Hospital',
																	'chief_complaint'=>'Chief Complaint',
																	'availment_type'=>'Availment Type',
																	'date_start'=>'Date Start',
																	'date_end'=>'Date End',
																	'user'=>'User'))
										.' '.form_radio('sort','ASC','TRUE').' Ascending '.form_radio('sort','DESC').' Descending '),
			array('',form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
			);
	$this->table->set_template($template);
	$search_fieldset = form_fieldset('<b>Search Summary</b>',array('id'=>'search_id'));
	$search_fieldset.= $this->table->generate($inputs);
	$search_fieldset.= form_fieldset_close();

	$input = array(array($search_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>

<?php echo validation_errors();?>
<?php echo form_open('table_export/index',array('id'=>'download_form'));?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);
	$input = array(
			array(form_label('Select Record:'),form_dropdown('table_name',array(
																				''=>'',
																				'patient'=>'Patients',
																				'hospital'=>'Hospital',
																				'dentistsanddoctors'=>'Dentists and Doctors'))),
			array(form_label('Date From:'),form_input(array('name'=>'start','id'=>'start','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
			array(form_label('Date End:'),form_input(array('name'=>'end','id'=>'end','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
			array('',form_submit(array('name'=>'submit','value'=>'Download','class'=>'btn btn-success')))
				);
	$download_template = form_fieldset('<b>Download Records</b>',array('id'=>'download_id'));
	$download_template.= $this->table->generate($input);
	$download_template.= form_fieldset_close();

	$inputs = array(array($download_template));
	echo $this->table->generate($inputs);
?>
<?php echo form_close();?>

<?php echo validation_errors(); ?>
<?php echo form_open('summary/searchTopsheet',array('id'=>'topsheet_form')); ?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellspacing="0" cellpadding="4">',
				'table_close' => '</table>'
				);
	$op_top_sheet = array(
				array(form_label('Enter Insurance Name: '),form_input(array('name'=>'insurance_topsheet','id'=>'insurance_topsheet','size'=>'20','class'=>'form-control'))),
				array(form_label('Date Received: ')),
				array(form_label('Date From: '),form_input(array('name'=>'topsheet_date_from','id'=>'topsheet_date_from','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: '),form_input(array('name'=>'topsheet_date_to','id'=>'topsheet_date_to','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);

	$op_top_sheet_fieldset = form_fieldset('<b>Topsheet Summary</b>',array('id'=>'topsheet_id'));
	$op_top_sheet_fieldset.= $this->table->generate($op_top_sheet);
	$op_top_sheet_fieldset.= form_fieldset_close();

	$input = array(array($op_top_sheet_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>
