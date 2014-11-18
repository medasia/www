<script>
$(document).ready(function() {
	$('#date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#upload_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#upload_date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#ip_billing_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#ip_billing_date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#ip_topsheet_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#ip_topsheet_date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#op_billing_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#op_billing_date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#op_topsheet_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#op_topsheet_date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#bill_report_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#bill_report_date_to').datepicker({format: 'yyyy-mm-dd'});
	$('#billing_date_from').datepicker({format: 'yyyy-mm-dd'});
	$('#billing_date_to').datepicker({format: 'yyyy-mm-dd'});
	
	$('#received_id, #upload_id, #ip_billing_id, #ip_top_sheet_id, #op_billing_id, #op_top_sheet_id, #bill_report_id, #billing_id').hide();
	
	$('#receive').click(function()
	{
		$('#received_id').slideToggle('fast', function(){});
		$('#billing_id').hide();
		$('#upload_id').hide();
		$('#ip_billing_id').hide();
		$('#ip_top_sheet_id').hide();
		$('#op_billing_id').hide();
		$('#op_top_sheet_id').hide();
		$('#bill_report_id').hide();
	});

	$('#billing').click(function()
	{
		$('#billing_id').slideToggle('fast', function(){});
		$('#received_id').hide();
		$('#upload_id').hide();
		$('#ip_billing_id').hide();
		$('#ip_top_sheet_id').hide();
		$('#op_billing_id').hide();
		$('#op_top_sheet_id').hide();
		$('#bill_report_id').hide();
	});

	$('#upload').click(function()
	{
		$('#upload_id').slideToggle('fast', function(){});
		$('#received_id').hide();
		$('#billing_id').hide();
		$('#ip_billing_id').hide();
		$('#ip_top_sheet_id').hide();
		$('#op_billing_id').hide();
		$('#op_top_sheet_id').hide();
		$('#bill_report_id').hide();
	});
	$('#ip_billing').click(function()
	{
		$('#ip_billing_id').slideToggle('fast', function(){});
		$('#received_id').hide();
		$('#billing_id').hide();
		$('#upload_id').hide();
		$('#ip_top_sheet_id').hide();
		$('#op_billing_id').hide();
		$('#op_top_sheet_id').hide();
		$('#bill_report_id').hide();
	});
	$('#ip_top_sheet').click(function()
	{
		$('#ip_top_sheet_id').slideToggle('fast',function(){});
		$('#ip_billing_id').hide();
		$('#received_id').hide();
		$('#billing_id').hide();
		$('#upload_id').hide();
		$('#op_billing_id').hide();
		$('#op_top_sheet_id').hide();
		$('#bill_report_id').hide();
	});
	$('#op_billing').click(function()
	{
		$('#op_billing_id').slideToggle('fast', function(){});
		$('#ip_top_sheet_id').hide();
		$('#ip_billing_id').hide();
		$('#received_id').hide();
		$('#billing_id').hide();
		$('#upload_id').hide();
		$('#op_top_sheet_id').hide();
		$('#bill_report_id').hide();
	});
	$('#op_top_sheet').click(function()
	{
		$('#op_top_sheet_id').slideToggle('fast', function(){});
		$('#op_billing_id').hide();
		$('#ip_top_sheet_id').hide();
		$('#ip_billing_id').hide();
		$('#received_id').hide();
		$('#billing_id').hide();
		$('#upload_id').hide();
		$('#bill_report_id').hide();
	});
	$('#bill_report').click(function()
	{
		$('#bill_report_id').slideToggle('fast', function(){});
		$('#op_top_sheet_id').hide();
		$('#op_billing_id').hide();
		$('#ip_top_sheet_id').hide();
		$('#ip_billing_id').hide();
		$('#received_id').hide();
		$('#billing_id').hide();
		$('#upload_id').hide();
	});

	$('#insurance_billing, #insurance_ip_billing, #insurance_ip_topsheet, #insurance_op_billing, #insurance_op_topsheet').autocomplete({
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
	$("form#receiving_form").validate({
    	rules: {
    		date_from: {
    			required: true
    			},
    		date_to: {
    			required: true
    		}
    	},
    	messages: {
    		date_from: {
    			required: 'This field is required'
    		},
    		date_to: {
    			required: 'This field is required'
    		}
    	}
	});
	$("form#billing_form").validate({
    	rules: {
    		insurance_billing: {
    			required: true
    		},
    		billing_date_from: {
    			required: true
    		},
    		billing_date_to: {
    			required: true
    		}
    	},
    	messages: {
    		insurance_billing: {
    			required: 'This field is required'
    		},
    		billing_date_from: {
    			required: 'This field is required'
    		},
    		billing_date_to: {
    			required: 'This field is required'
    		}
    	}
	});
	$("form#ipBilling_form").validate({
    	rules: {
    		insurance_ip_billing: {
    			required: true
    		},
    		ip_billing_date_from: {
    			required: true
    		},
    		ip_billing_date_to: {
    			required: true
    		}
    	},
    	messages: {
    		insurance_ip_billing: {
    			required: 'This field is required'
    		},
    		ip_billing_date_from: {
    			required: 'This field is required'
    		},
    		ip_billing_date_to: {
    			required: 'This field is required'
    		}
    	}
	});
	$("form#ipTopsheet_form").validate({
    	rules: {
    		insurance_ip_topsheet: {
    			required: true
    		},
    		ip_topsheet_date_from: {
    			required: true
    		},
    		ip_topsheet_date_to: {
    			required: true
    		}
    	},
    	messages: {
    		insurance_ip_topsheet: {
    			required: 'This field is required'
    		},
    		ip_topsheet_date_from: {
    			required: 'This field is required'
    		},
    		ip_topsheet_date_to: {
    			required: 'This field is required'
    		}
    	}
	});
	$("form#opBilling_form").validate({
    	rules: {
    		insurance_op_billing: {
    			required: true
    		},
    		op_billing_date_from: {
    			required: true
    		},
    		op_billing_date_to: {
    			required: true
    		}
    	},
    	messages: {
    		insurance_op_billing: {
    			required: 'This field is required'
    		},
    		op_billing_date_from: {
    			required: 'This field is required'
    		},
    		op_billing_date_to: {
    			required: 'This field is required'
    		}
    	}
	});
	$("form#opTopsheet_form").validate({
    	rules: {
    		insurance_op_topsheet: {
    			required: true
    		},
    		op_topsheet_date_from: {
    			required: true
    		},
    		op_topsheet_date_to: {
    			required: true
    		}
    	},
    	messages: {
    		insurance_op_topsheet: {
    			required: 'This field is required'
    		},
    		op_topsheet_date_from: {
    			required: 'This field is required'
    		},
    		op_topsheet_date_to: {
    			required: 'This field is required'
    		}
    	}
	});
});
</script>
<html>
<head>
	<title>Medriks - Claims</title>
</head>

<h1>Claims</h1>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}
?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);

	$input = array(
			array(form_button(array('name'=>'receive','id'=>'receive','content'=>'Receive','class'=>'btn btn-info')),
				form_button(array('name'=>'billing','id'=>'billing','content'=>'Billing','class'=>'btn btn-info')),
				form_button(array('name'=>'ip_billing','id'=>'ip_billing','content'=>'IP Billing','class'=>'btn btn-info')),
				form_button(array('name'=>'ip_top_sheet','id'=>'ip_top_sheet','content'=>'IP Top Sheet','class'=>'btn btn-info')),
				form_button(array('name'=>'op_billing','id'=>'op_billing','content'=>'OP Billing','class'=>'btn btn-info')),
				form_button(array('name'=>'op_top_sheet','id'=>'op_top_sheet','content'=>'OP Top Sheet','class'=>'btn btn-info')))
				// form_button(array('name'=>'upload','id'=>'upload','content'=>'Upload','class'=>'btn btn-info')),
				// form_button(array('name'=>'bill_report','id'=>'bill_report','content'=>'Bill Report','class'=>'btn btn-info')))
			);
	$this->table->set_template($template);
	echo $this->table->generate($input);
?>
<?php echo validation_errors(); ?>

<?php echo form_open('claims/receiving',array('id'=>'receiving_form'));?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);

	$received = array(
				array(form_label('Enter Claims Identity: ').' '.form_input(array('name'=>'keyword','size'=>'20','class'=>'form-control','placeholder'=>'Enter Keyword Based On Identity Type'))),
				array(form_radio('identity','code','TRUE').' Approval Code '.form_radio('identity','patient_name').' Patient Name '.form_radio('identity','hospital_name').' Hospital Name'),
				array(form_label('Date From: ').' '.form_input(array('name'=>'date_from','id'=>'date_from','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: ').' '.form_input(array('name'=>'date_to','id'=>'date_to','size'=>'20','placeholder'=>'YYYY-MM-DD', 'class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);

	$this->table->set_template($template);
	$received_fieldset = form_fieldset('<b>Received</b>', array('id'=>'received_id'));
	$received_fieldset.=  $this->table->generate($received);
	$received_fieldset.= form_fieldset_close();

	$input = array(array($received_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>

<?php echo validation_errors(); ?>
<?php echo form_open('claims/billing',array('id'=>'billing_form')); ?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$billing = array(
				array(form_label('Insurance: ').' '.form_input(array('name'=>'insurance_billing','id'=>'insurance_billing','placeholder'=>'Insurance Name','class'=>'form-control'))),
				array(form_label('Date From: ').' '.form_input(array('name'=>'billing_date_from','id'=>'billing_date_from','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: ').' '.form_input(array('name'=>'billing_date_to','id'=>'billing_date_to','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Availment Type: ').' '.form_dropdown('availment_type',array('In-Patient'=>'In-Patient / In and Out Patient','Out-Patient'=>'Out-Patient'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')),'')
				);
	$this->table->set_template($template);
	$billing_fieldset = form_fieldset('<b>Billing</b>', array('id'=>'billing_id'));
	$billing_fieldset.= $this->table->generate($billing);
	$billing_fieldset.= form_fieldset_close();

	$input = array(array($billing_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close(); ?>

<?php echo validation_errors();?>
<?php echo form_open('claims/ipBilling',array('id'=>'ipBilling_form'));?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);

	$ip_billing = array(
				array(form_label('Insurance Name IP: '),form_input(array('name'=>'insurance_ip_billing','id'=>'insurance_ip_billing','size'=>'20','class'=>'form-control','placeholder'=>'Insurance Name'))),
				array(form_label('Date Received')),
				array(form_label('Date From: '),form_input(array('name'=>'ip_billing_date_from','id'=>'ip_billing_date_from','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: '),form_input(array('name'=>'ip_billing_date_to','id'=>'ip_billing_date_to','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);
	$ip_billing_fieldset = form_fieldset('<b>Claims Billing</b>',array('id'=>'ip_billing_id'));
	$ip_billing_fieldset.= $this->table->generate($ip_billing);
	$ip_billing_fieldset.= form_fieldset_close();

	$input = array(array($ip_billing_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>

<?php echo validation_errors();?>
<?php echo form_open('claims/ipTopsheet',array('id'=>'ipTopsheet_form'));?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);

	$ip_top_sheet = array(
				array(form_label('Enter Insurance Name IN: '),form_input(array('name'=>'insurance_ip_topsheet','size'=>'20','id'=>'insurance_ip_topsheet','class'=>'form-control','placeholder'=>'Insurance Name'))),
				array(form_label('Date Received')),
				array(form_label('Date From: '),form_input(array('name'=>'ip_topsheet_date_from','id'=>'ip_topsheet_date_from','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: '),form_input(array('name'=>'ip_topsheet_date_to','id'=>'ip_topsheet_date_to','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);
	$ip_top_sheet_fieldset = form_fieldset('<b>Claims Billing</b>',array('id'=>'ip_top_sheet_id'));
	$ip_top_sheet_fieldset.= $this->table->generate($ip_top_sheet);
	$ip_top_sheet_fieldset.= form_fieldset_close();

	$input = array(array($ip_top_sheet_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>

<?php echo validation_errors(); ?>
<?php echo form_open('claims/opBilling',array('id'=>'opBilling_form')); ?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellspacing="0" cellpadding="4">',
				'table_close' => '</table>'
				);

	$op_billing = array(
				array(form_label('Enter Insurance Name OP: '),form_input(array('name'=>'insurance_op_billing','size'=>'20','id'=>'insurance_op_billing','class'=>'form-control','placeholder'=>'Insurance Name'))),
				array(form_label('Date Received')),
				array(form_label('Date From: '),form_input(array('name'=>'op_billing_date_from','size'=>'20','id'=>'op_billing_date_from','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: '),form_input(array('name'=>'op_billing_date_to','size'=>'20','id'=>'op_billing_date_to','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);
	$op_billing_fieldset = form_fieldset('<b>Claims Billing</b>',array('id'=>'op_billing_id'));
	$op_billing_fieldset.= $this->table->generate($op_billing);
	$op_billing_fieldset.= form_fieldset_close();

	$input = array(array($op_billing_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>

<?php echo validation_errors(); ?>
<?php echo form_open('claims/opTopsheet',array('id'=>'opTopsheet_form')); ?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellspacing="0" cellpadding="4">',
				'table_close' => '</table>'
				);
	$op_top_sheet = array(
				array(form_label('Enter Insurance Name OUT: '),form_input(array('name'=>'insurance_op_topsheet','id'=>'insurance_op_topsheet','size'=>'20','class'=>'form-control','placeholder'=>'Insurance Name'))),
				array(form_label('Date Received: ')),
				array(form_label('Date From: '),form_input(array('name'=>'op_topsheet_date_from','id'=>'op_topsheet_date_from','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: '),form_input(array('name'=>'op_topsheet_date_to','id'=>'op_topsheet_date_to','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);

	$op_top_sheet_fieldset = form_fieldset('<b>Claims Billing</b>',array('id'=>'op_top_sheet_id'));
	$op_top_sheet_fieldset.= $this->table->generate($op_top_sheet);
	$op_top_sheet_fieldset.= form_fieldset_close();

	$input = array(array($op_top_sheet_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>

<?php echo validation_errors();?>
<?php echo form_open('');?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$upload = array(
				array(form_label('Date Received'),''),
				array(form_label('Date From: '),form_input(array('name'=>'upload_date_from','id'=>'upload_date_from','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To: '),form_input(array('name'=>'upload_date_to','id'=>'upload_date_to','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);

	$this->table->set_template($template);
	$upload_field = form_fieldset('<b>Upload</b>',array('id'=>'upload_id'));
	$upload_field.= $this->table->generate($upload);
	$upload_field.= form_fieldset_close();

	$input = array(array($upload_field));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>

<?php echo validation_errors(); ?>
<?php echo form_open(''); ?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellspacing="0" cellpadding="4">',
				'table_close' => '</table>'
				);

	$bill_report = array(
				array(form_label('Enter Insurance Name OUT: '),form_input(array('name'=>'insurance_op','id'=>'insurance_op','size'=>'20','class'=>'form-control','placeholder'=>'Insurance Name'))),
				array(form_label('Date Received')),
				array(form_label('Date From'),form_input(array('name'=>'bill_report_date_from','id'=>'bill_report_date_from','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date To'),form_input(array('name'=>'bill_report_date_to','id'=>'bill_report_date_to','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);

	$bill_report_fieldset = form_fieldset('<b>Bill Report</b>',array('id'=>'bill_report_id'));
	$bill_report_fieldset.= $this->table->generate($bill_report);
	$bill_report_fieldset.= form_fieldset_close();

	$input = array(array($bill_report_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>