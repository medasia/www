<script>
$(document).ready(function(){
	$('#datestart, #dateend, #date_requested, #multiple_declaration, #multiple_release, #multiple_effectivity, #multiple_validity').datepicker({format: 'yyyy-mm-dd'});
	$('#formAddInsurance, #formAddBroker, #formAddCompany, #formAddBilling, #formAddBillingType, #formAddBillingCompany, #formAddBillingPatient, #formAddReceiving').hide();
	$('#toggleSlideInsurance').click(function()
	{
		$('#formAddInsurance').slideToggle('fast', function() {});
		$('#formAddBroker').hide();
		$('#formAddCompany').hide();
		$('#formAddBillingType').hide();
		$('#formAddBillingCompany').hide();
		$('#formAddBillingPatient').hide();
		$('#formAddReceiving').hide();
	});

	$('#toggleSlideBroker').click(function()
	{
		$('#formAddBroker').slideToggle('fast', function() {});
		$('#formAddInsurance').hide();
		$('#formAddCompany').hide();
		$('#formAddBillingType').hide();
		$('#formAddBillingCompany').hide();
		$('#formAddBillingPatient').hide();
		$('#formAddReceiving').hide();
	});

	$('#toggleSlideCompany').click(function()
	{
		$('#formAddCompany').slideToggle('fast', function() {});
		$('#formAddInsurance').hide();
		$('#formAddBroker').hide();
		$('#formAddBillingType').hide();
		$('#formAddBillingCompany').hide();
		$('#formAddBillingPatient').hide();
		$('#formAddReceiving').hide();
	});

	$('#toggleSlideBilling').click(function()
	{
		$('#formAddBillingType').slideToggle('fast',function() {});
		$('#formAddCompany').hide();
		$('#formAddInsurance').hide();
		$('#formAddBroker').hide();
		$('#formAddBillingCompany').hide();
		$('#formAddBillingPatient').hide();
		$('#formAddReceiving').hide();
	});

	$('#toggleSlideReceiving').click(function()
	{
		$('#formAddReceiving').slideToggle('fast',function() {});
		$('#formAddCompany').hide();
		$('#formAddInsurance').hide();
		$('#formAddBroker').hide();
		$('#formAddBillingCompany').hide();
		$('#formAddBillingPatient').hide();
		$('#formAddBillingType').hide();
	});

	$('#billing_for').change(function() {
		var selected = $(this).val();
		if(selected == 'Company')
		{
			$('#formAddBillingCompany').slideToggle('fast',function() {});
			$('#formAddBillingPatient').hide();
			$('[name=type]').val(selected);
		}
		else if(selected == 'Members')
		{
			$('#formAddBillingPatient').slideToggle('fast',function() {});
			$('#formAddBillingCompany').hide();
			$('[name=type]').val(selected);
		}
		else
		{
			$('#formAddBillingCompany').hide();
			$('#formAddBillingPatient').hide();
		}
	});

	$('#compins-ins, #compins-insurance').autocomplete({
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
		},
		select: function(event, ui)
		{
			$('[name=insurance_id]').val(ui.item ? ui.item.insurance_id : '');
		}
	});

	$('#compins-broker').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/compins-brokers', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data) {
					if(data.response =='true'){
						add(data.message);
					}
				}
			});
		},
		select: function(event, ui)
		{
			$('[name=broker_id]').val(ui.item ? ui.item.broker_id : '');
		}
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

	$("form#company_form").validate({
    			rules: {
    				name: {
    					required: true
    				},
    			    code: {
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
    				name: {
    					required: 'This field is required'
    				},
    			    code: {
    			        required: "This field is required"
    			    },insurance: {
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

	$('#multiple_company, #company').autocomplete({
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
		},
		select: function(event, ui)
		{
			$('[name=company_id]').val(ui.item ? ui.item.company_id : '');
		}
	});

	var cloneCount = 1;
	$('#removeCompany').hide();
	$('#addCompany').click(function()
	{
		$('#removeCompany').show();
		cloneCount++;
		$("#multiple_company_table").clone().appendTo('#multiple_company_fieldset').find("*[id]").andSelf().each(function()
		{
			$(this).attr("id", $(this).attr("id")+cloneCount);
			$('#removeCompany').hide();
		});

		$('#multiple_declaration'+cloneCount).datepicker({format: 'yyyy-mm-dd'});
		$('#multiple_release'+cloneCount).datepicker({format: 'yyyy-mm-dd'});
		$('#multiple_effectivity'+cloneCount).datepicker({format: 'yyyy-mm-dd'});
		$('#multiple_validity'+cloneCount).datepicker({format: 'yyyy-mm-dd'});

		$('#removeCompany'+cloneCount).click(function()
		{
			if(cloneCount > 1)
			{
				$('#multiple_company_table'+cloneCount).remove();
				cloneCount--;
			}
		});

		$('#multiple_company'+cloneCount).autocomplete(
		{
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
			},
		});
	});

	$('#billing_attention_name, #multiple_patient').autocomplete(
	{
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/accounts-members', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data) {
					if(data.response =='true'){
						add(data.message);
					}
				}
			});
		},
	});

	$('#removePatient').hide();
	$('#addPatient').click(function()
	{
		$('#removePatient').show();
		cloneCount++;
		$('#removePatient').show();
		cloneCount++;
		$("#multiple_patient_table").clone().appendTo('#multiple_patient_fieldset').find("*[id]").andSelf().each(function()
		{
			$(this).attr("id", $(this).attr("id")+cloneCount);
			$('#removePatient').hide();
		});

		$('#multiple_declaration'+cloneCount).datepicker({format: 'yyyy-mm-dd'});
		$('#multiple_effectivity'+cloneCount).datepicker({format: 'yyyy-mm-dd'});
		$('#multiple_validity'+cloneCount).datepicker({format: 'yyyy-mm-dd'});

		$('#removePatient'+cloneCount).click(function()
		{
			if(cloneCount > 1)
			{
				$('#multiple_patient_table'+cloneCount).remove();
				cloneCount--;
			}
		});

		$('#multiple_patient'+cloneCount).autocomplete(
		{
			minLength: 1,
			source: function(req, add){
				$.ajax({
					url: '<?=base_url()?>utils/autocomplete/from/accounts-members', //Controller where search is performed
					dataType: 'json',
					type: 'POST',
					data: req,
					success: function(data) {
						if(data.response =='true'){
							add(data.message);
						}
					}
				});
			},
		});
	});
});
</script>

<h1>Accounts</h1>
<?php 
if ($this->session->flashdata('result') != '')
{
	echo '<b>'.$this->session->flashdata('result')."</b><br>";
}
?>
<button id='toggleSlideInsurance' class="btn btn-default">Add New Insurance</button>
<button id='toggleSlideBroker' class="btn btn-default">Add New Broker</button>
<button id='toggleSlideCompany' class="btn btn-default">Add New Company</button>
<button id='toggleSlideBilling' class="btn btn-default">Request For Billing</button>
<button id='toggleSlideReceiving' class="btn btn-default">Receiving Copy</button>

<div id='formAddInsurance'>
	<h2>Insurance</h2>
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
	
	<?php echo validation_errors(); ?>
	<?php echo form_open('records/accounts/register',array('id'=>'insurance_form')); ?>
		<?php
			$inputs = array(
							array('', ''),
							array(form_label('Insurance', 'name'), form_input(array('name'=>'name', 'id'=>'name', 'size'=>'20','placeholder'=>'Insurance Name','class'=>'form-control'))),
							array(form_label('Attention Name', 'attention_name'), form_input(array('name'=>'attention_name', 'id'=>'attention_name', 'size'=>'20','placeholder'=>'Attention Name','class'=>'form-control'))),
							array(form_label('Attention Position', 'attention_position'), form_input(array('name'=>'attention_position', 'id'=>'attention_position', 'size'=>'20','placeholder'=>'Attention Position','class'=>'form-control'))),
							array(form_label('Address', 'address'), form_input(array('name'=>'address', 'id'=>'address', 'size'=>'20','placeholder'=>'address','class'=>'form-control'))),
							array(form_label('Code', 'code'), form_input(array('name'=>'code', 'id'=>'code', 'size'=>'20','placeholder'=>'code','class'=>'form-control'))),
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
			echo form_hidden('count', 0);
			echo form_hidden('count_Op', 0);
			echo form_hidden('table','insurance');
		?>
	<?php echo form_close(); ?>
</div>

<div id='formAddBroker'>
	<h2>Broker</h2>
	<?php echo validation_errors(); ?>
	<?php echo form_open('records/accounts/register',array('id'=>'broker_form')); ?>
		<?php
			$template = array(
							'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
							'table_close'	=> '</table>'
						);

			$inputs = array(
							array('', ''),
							array(form_label('Broker', 'broker_name'),form_input(array('name'=>'name', 'id'=>'broker_name', 'size'=>'50','class'=>'form-control','placeholder'=>'Name'))),
							array(form_label('Address', 'address'),form_input(array('name'=>'address', 'id'=>'address', 'size'=>'50','class'=>'form-control','placeholder'=>'Address'))),
							array(form_label('Contact Person', 'contact_person'),form_input(array('name'=>'contact_person', 'id'=>'contact_person', 'size'=>'50','class'=>'form-control','placeholder'=>'Contact Person'))),
							array(form_label('Contact No.', 'contact_no'),form_input(array('name'=>'contact_no', 'id'=>'contact_no', 'size'=>'50','class'=>'form-control','placeholder'=>'Contact No.'))),
							array('', form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
							);
			$this->table->set_template($template); 
			echo $this->table->generate($inputs);
			echo form_hidden('table','brokers');
		?>
	<?php echo form_close(); ?>
</div>

<div id='formAddCompany'>
	<h2>Company</h2>
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

	<?php echo validation_errors(); ?>
	<?php echo form_open('records/accounts/register',array('id'=>'company_form')); ?>
		<?php
			$template = array(
							'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
							'table_close'	=> '</table>'
						);

			// TAGGING OF INSURANCE
			$this->table->set_template($template);
			$insurance_tmpl = array(
							array('',''),
							array(form_label('Insurance'),form_input(array('name'=>'insurance', 'id'=>'compins-ins', 'size'=>'50','class'=>'form-control','placeholder'=>'Insurance'))),
							array(form_label('Broker'),form_input(array('name'=>'broker_name', 'id'=>'compins-broker', 'size'=>'50','class'=>'form-control','placeholder'=>'Broker Name'))),
							array(form_label('Effective Date'),form_input(array('name'=>'start','id'=>'datestart','placeholder'=>'YYYY-MM-DD','class'=>'form-control','size'=>'20'))),
							array(form_label('Validity Date'),form_input(array('name'=>'end', 'id'=>'dateend','placeholder'=>'YYYY-MM-DD','class'=>'form-control','size'=>'20'))),
							array(form_label('Remarks'), form_textarea(array('name'=>'notes','id'=>'notes','cols'=>'50','rows'=>'10','class'=>'form-control'))),
							);
			$insurance = form_fieldset('Tag Insurance');
			$insurance.= $this->table->generate($insurance_tmpl);
			$insurance.= form_fieldset_close();

			$inputs = array(
							array('', ''),
							array(form_label('Company', 'name'), form_input(array('name'=>'name', 'id'=>'name', 'size'=>'20','class'=>'form-control','placeholder'=>'Company'))),
							array(form_label('Code', 'code'), form_input(array('name'=>'code', 'id'=>'code', 'size'=>'20','class'=>'form-control','placeholder'=>'Code'))),
							array(form_label(''),$insurance),
							array('', form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
							);
			$this->table->set_template($template); 
			echo $this->table->generate($inputs);
			echo form_hidden('table','company');
			echo form_hidden('broker_id');
			echo form_hidden('insurance_id');
		?>
	<?php echo form_close(); ?>
</div>

<?php
$members = anchor(base_url()."records/compins/members/1","Team Pirates",array('target'=>'_blank'));
?>

<div id="formAddBillingType">
	<?php
		// $members = anchor(base_url()."records/compins/members/1","Team Pirates")",array('target'=>'_blank'));
		$tmpl = array(
				'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0">',
				'table_close'	=>	'</table>'
				);
		$type = array(
				array('',''),
				array('Billing For: ',form_dropdown('billing_for', array(''=>'', 'Company'=>'Company','Members'=>'Members'),'','id="billing_for"')),
				array('Display Members',$members)
				);
		$this->table->set_template($tmpl);
		echo $this->table->generate($type);
	?>
</div>

<div id="formAddBillingCompany">
	<?php echo validation_errors(); ?>
	<?php echo form_open_multipart('records/accounts/printBilling'); ?>
	<?php
		$template = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0" id="multiple_company_table">',
			'table_close'	=>	'</table>'
			);
		$this->table->set_template($template);

		$multipleCompany = array(
						array(form_label('Company Name'),form_input(array('name'=>'multiple_company[]','id'=>'multiple_company','size'=>'20','class'=>'form-control','placeholder'=>'Company Name'))),
						array(form_label('IP & OP'), form_input(array('name'=>'multiple_ipop[]','id'=>'multiple_ipop','size'=>'20','class'=>'form-control','placeholder'=>'IP & OP Count'))),
						array(form_label('IP'),form_input(array('name'=>'multiple_ip[]','id'=>'multiple_ip','size'=>'20','class'=>'form-control','placeholder'=>'IP Count'))),
						array(form_label('ER'),form_input(array('name'=>'multiple_er[]','id'=>'multiple_er','size'=>'20','class'=>'form-control','placeholder'=>'ER Count'))),
						array(form_label('Dental'), form_input(array('name'=>'multiple_dental[]','id'=>'multiple_dental','size'=>'20','class'=>'form-control','placeholder'=>'Dental Count'))),
						array(form_label('APE'), form_input(array('name'=>'multiple_ape[]','id'=>'multiple_ape','size'=>'20','class'=>'form-control','placeholder'=>'APE Count'))),
						array(form_label('Date of Declaration'), form_input(array('name'=>'multiple_declaration[]','id'=>'multiple_declaration','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Date of Release'), form_input(array('name'=>'multiple_release[]','id'=>'multiple_release','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Coverage Date'),''), // COVERAGE PERIOD (start and end FIELD IN patient TABLE, effectivity_date and validity_date HERE);
						array(form_label('Effectivity Date'),form_input(array('name'=>'multiple_effectivity[]','id'=>'multiple_effectivity','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Validity Date'),form_input(array('name'=>'multiple_validity[]','id'=>'multiple_validity','class'=>'form-control','placeholder'=>'YYYY-MM-DD','size'=>'20'))),
						array(form_label('Remarks'),form_textarea(array('name'=>'multiple_remarks[]','id'=>'multiple_remarks','cols'=>'30','rows'=>'5','class'=>'form-control','placeholder'=>'Remarks'))),
						array(form_button(array('name'=>'removeCompany[]','id'=>'removeCompany','content'=>'Remove Company','class'=>'btn btn-xs btn-danger')),''),
						array('','')
						);
		$addMultipleCompany = form_fieldset('Company',array('id'=>'multiple_company_fieldset'));
		$addMultipleCompany.= $this->table->generate($multipleCompany);
		$addMultipleCompany. form_fieldset_close();

		$tmpl = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=>	'</table>'
			);

		$type = array(
				array('',''),
				array('Billing For: ',form_dropdown('type', array(''=>'', 'Company'=>'Company','Members'=>'Members')))
				);
		$inputs = array(
				array('',''),
				array(form_label('Insurance'),form_input(array('name'=>'insurance', 'id'=>'compins-insurance', 'size'=>'50','class'=>'form-control','placeholder'=>'Insurance'))),
				array(form_button(array('name'=>'addCompany','id'=>'addCompany','content'=>'Add Company','class'=>'btn btn-info btn-xs')),$addMultipleCompany),
				array(form_label('Date Requested'),form_input(array('name'=>'date_requested','id'=>'date_requested','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
				array(form_label('Billing Request Number'),form_input(array('name'=>'billing_request_number','id'=>'billing_request_number','size'=>'20','class'=>'form-control','placeholder'=>'Billing Request Number'))),
				array(form_label('Reference Number'),form_input(array('name'=>'reference_number','id'=>'reference_number','size'=>'20','class'=>'form-control','placeholder'=>'Reference Number'))),
				array(form_label('Prepared By'),form_input(array('name'=>'prepared_by','id'=>'prepared_by','size'=>'20','placeholder'=>'Prepared By','class'=>'form-control'))),
				array(form_label('Position'),form_input(array('name'=>'prepared_by_position','id'=>'prepared_position','size'=>'20','placeholder'=>'Prepared By Position','class'=>'form-control'))),
				array(form_label('Received By'),form_input(array('name'=>'received_by','id'=>'received_by','size'=>'20','placeholder'=>'Received By','class'=>'form-control'))),
				array(form_label('Position'),form_input(array('name'=>'received_by_position','id'=>'received_position','size'=>'20','placeholder'=>'Received By Position','class'=>'form-control'))),
				array(form_label('Attach Receiving Copy'),form_upload(array('name'=>'file','id'=>'file','class'=>'form-group'))),
				array('',form_submit(array('name'=>'submit','id'=>'submit','class'=>'btn btn-success','value'=>'Print')))
				);
		$this->table->set_template($tmpl);
		echo $this->table->generate($inputs);
		echo form_hidden('insurance_id');
		echo form_hidden('type');
	?>
	<?php echo form_close();?>
</div>

<div id="formAddBillingPatient">
	<?php echo validation_errors(); ?>
	<?php echo form_open('records/accounts/printBilling'); ?>
	<?php
		$template = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0" id="multiple_patient_table">',
			'table_close'	=>	'</table>'
			);
		$this->table->set_template($template);

		$multiplePatient = array(
						array(form_label('Patient Name'),form_input(array('name'=>'multiple_patient[]','id'=>'multiple_patient','size'=>'20','class'=>'form-control','placeholder'=>'Patient Name'))),
						array(form_label('Medical Plan'), form_input(array('name'=>'multiple_medical_plan[]','id'=>'multiple_medical_plan','size'=>'20','class'=>'form-control','placeholder'=>'IP & OP Count'))),
						array(form_label('Amount'),form_input(array('name'=>'multiple_amount[]','id'=>'multiple_amount','size'=>'20','class'=>'form-control','placeholder'=>'IP Count'))),
						array(form_label('Date of Declaration'), form_input(array('name'=>'multiple_declaration[]','id'=>'multiple_declaration','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Coverage Date'),''), // COVERAGE PERIOD (start and end FIELD IN patient TABLE, effectivity_date and validity_date HERE);
						array(form_label('Effectivity Date'),form_input(array('name'=>'multiple_effectivity[]','id'=>'multiple_effectivity','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Validity Date'),form_input(array('name'=>'multiple_validity[]','id'=>'multiple_validity','class'=>'form-control','placeholder'=>'YYYY-MM-DD','size'=>'20'))),
						array(form_label('Remarks'),form_textarea(array('name'=>'multiple_remarks[]','id'=>'multiple_remarks','cols'=>'30','rows'=>'5','class'=>'form-control','placeholder'=>'Remarks'))),
						array(form_button(array('name'=>'removePatient[]','id'=>'removePatient','content'=>'Remove Patient','class'=>'btn btn-xs btn-danger')),''),
						array('','')
						);
		$addMultiplePatient = form_fieldset('Patient',array('id'=>'multiple_patient_fieldset'));
		$addMultiplePatient.= $this->table->generate($multiplePatient);
		$addMultiplePatient. form_fieldset_close();

		$tmpl = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=>	'</table>'
			);
		$inputs = array(
				array('',''),
				array(form_label('Insurance'),form_input(array('name'=>'insurance', 'id'=>'compins-insurance', 'size'=>'50','class'=>'form-control','placeholder'=>'Insurance'))),
				array(form_label('Company Name'),form_input(array('name'=>'company','id'=>'company','size'=>'20','class'=>'form-control','placeholder'=>'Company Name'))),
				array(form_label('Attention To'), form_input(array('name'=>'billing_attention_name','id'=>'billing_attention_name','size'=>'20','class'=>'form-control','placeholder'=>'Attention to'))),
				array(form_button(array('name'=>'addPatient','id'=>'addPatient','content'=>'Add Patient','class'=>'btn btn-info btn-xs')),$addMultiplePatient),
				array(form_label('Date Requested'),form_input(array('name'=>'date_requested','id'=>'date_requested','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
				array(form_label('Billing Request Number'),form_input(array('name'=>'billing_request_number','id'=>'billing_request_number','size'=>'20','class'=>'form-control','placeholder'=>'Billing Request Number'))),
				array(form_label('Reference Number'),form_input(array('name'=>'reference_number','id'=>'reference_number','size'=>'20','class'=>'form-control','placeholder'=>'Reference Number'))),
				array(form_label('Prepared By'),form_input(array('name'=>'prepared_by','id'=>'prepared_by','size'=>'20','placeholder'=>'Prepared By','class'=>'form-control'))),
				array(form_label('Position'),form_input(array('name'=>'prepared_by_position','id'=>'prepared_position','size'=>'20','placeholder'=>'Prepared By Position','class'=>'form-control'))),
				array(form_label('Received By'),form_input(array('name'=>'received_by','id'=>'received_by','size'=>'20','placeholder'=>'Received By','class'=>'form-control'))),
				array(form_label('Position'),form_input(array('name'=>'received_by_position','id'=>'received_position','size'=>'20','placeholder'=>'Received By Position','class'=>'form-control'))),
				array('',form_submit(array('name'=>'submit','id'=>'submit','class'=>'btn btn-success','value'=>'Submit')))
				);
		$this->table->set_template($tmpl);
		echo $this->table->generate($inputs);
		echo form_hidden('insurance_id');
		echo form_hidden('company_id');
		echo form_hidden('type');
	?>
	<?php echo form_close();?>
</div>

<div id="formAddReceiving">
	<?php echo validation_errors(); ?>
	<?php echo form_open('records/accounts/printReceiving'); ?>
	<?php
		$template = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0" id="multiple_receiving_table">',
			'table_close'	=>	'</table>'
			);
		$this->table->set_template($template);

		$multiplePatient = array(
						array(form_label('Patient Name'),form_input(array('name'=>'multiple_patient[]','id'=>'multiple_patient','size'=>'20','class'=>'form-control','placeholder'=>'Patient Name'))),
						array(form_label('Medical Plan'), form_input(array('name'=>'multiple_medical_plan[]','id'=>'multiple_medical_plan','size'=>'20','class'=>'form-control','placeholder'=>'IP & OP Count'))),
						array(form_label('Amount'),form_input(array('name'=>'multiple_amount[]','id'=>'multiple_amount','size'=>'20','class'=>'form-control','placeholder'=>'IP Count'))),
						array(form_label('Date of Declaration'), form_input(array('name'=>'multiple_declaration[]','id'=>'multiple_declaration','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Coverage Date'),''), // COVERAGE PERIOD (start and end FIELD IN patient TABLE, effectivity_date and validity_date HERE);
						array(form_label('Effectivity Date'),form_input(array('name'=>'multiple_effectivity[]','id'=>'multiple_effectivity','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Validity Date'),form_input(array('name'=>'multiple_validity[]','id'=>'multiple_validity','class'=>'form-control','placeholder'=>'YYYY-MM-DD','size'=>'20'))),
						array(form_label('Remarks'),form_textarea(array('name'=>'multiple_remarks[]','id'=>'multiple_remarks','cols'=>'30','rows'=>'5','class'=>'form-control','placeholder'=>'Remarks'))),
						array(form_button(array('name'=>'removePatient[]','id'=>'removePatient','content'=>'Remove Patient','class'=>'btn btn-xs btn-danger')),''),
						array('','')
						);
		$addMultiplePatient = form_fieldset('Patient',array('id'=>'multiple_receiving_fieldset'));
		$addMultiplePatient.= $this->table->generate($multiplePatient);
		$addMultiplePatient. form_fieldset_close();

		$tmpl = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=>	'</table>'
			);
		$inputs = array(
				array('',''),
				array(form_label('Reference Number'),form_input(array('name'=>'reference_number', 'id'=>'reference_number', 'size'=>'50','class'=>'form-control','placeholder'=>'Reference Number'))),
				array(form_label('Insurance'),form_input(array('name'=>'insurance', 'id'=>'compins-insurance', 'size'=>'50','class'=>'form-control','placeholder'=>'Insurance'))),
				array(form_label('Account Name'),form_input(array('name'=>'company','id'=>'company','size'=>'20','class'=>'form-control','placeholder'=>'Company Name'))),
				array(form_label('Attention To'), form_input(array('name'=>'billing_attention_name','id'=>'billing_attention_name','size'=>'20','class'=>'form-control','placeholder'=>'Attention to'))),
				array(form_label('Coverage Date'),''), // COVERAGE PERIOD (start and end FIELD IN patient TABLE, effectivity_date and validity_date HERE);
				array(form_label('Effectivity Date'),form_input(array('name'=>'effectivity_date','id'=>'effectivity_date','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
				array(form_label('Validity Date'),form_input(array('name'=>'validity_date','id'=>'validity_date','class'=>'form-control','placeholder'=>'YYYY-MM-DD','size'=>'20'))),		
				array(form_button(array('name'=>'addPatient','id'=>'addPatient','content'=>'Add Patient','class'=>'btn btn-info btn-xs')),$addMultiplePatient),
				array(form_label('Date Requested'),form_input(array('name'=>'date_requested','id'=>'date_requested','size'=>'20','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
				array(form_label('Reference Number'),form_input(array('name'=>'reference_number','id'=>'reference_number','size'=>'20','class'=>'form-control','placeholder'=>'Reference Number'))),
				array(form_label('RF #'),form_input(array('name'=>'rf','id'=>'rf','size'=>'20','class'=>'form-control','placeholder'=>'RF #'))),
				array(form_label('Prepared By'),form_input(array('name'=>'prepared_by','id'=>'prepared_by','size'=>'20','placeholder'=>'Prepared By','class'=>'form-control'))),
				array(form_label('Position'),form_input(array('name'=>'prepared_by_position','id'=>'prepared_position','size'=>'20','placeholder'=>'Prepared By Position','class'=>'form-control'))),
				array(form_label('Received By'),form_input(array('name'=>'received_by','id'=>'received_by','size'=>'20','placeholder'=>'Received By','class'=>'form-control'))),
				array(form_label('Position'),form_input(array('name'=>'received_by_position','id'=>'received_position','size'=>'20','placeholder'=>'Received By Position','class'=>'form-control'))),
				array('',form_submit(array('name'=>'submit','id'=>'submit','class'=>'btn btn-success','value'=>'Submit')))
				);
		$this->table->set_template($tmpl);
		echo $this->table->generate($inputs);
		echo form_hidden('insurance_id');
		echo form_hidden('company_id');
		echo form_hidden('type');
	?>
	<?php echo form_close();?>
</div>