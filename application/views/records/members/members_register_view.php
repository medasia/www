<script>
$(document).ready(function() {
	$('#formAddSingle').hide();
	$('#formAddMultiple').hide();
	$('#toggleSlideSingle').click(function()
	{
		$('#formAddSingle').slideToggle('fast', function() {});
		$('#formAddMultiple').hide();
	});
	$('#toggleSlideMultiple').click(function()
	{
		$('#formAddMultiple').slideToggle('fast', function() {});
		$('#formAddSingle').hide();
	});
	$('#dateofbirth').datepicker({format: 'yyyy-mm-dd'});
	$('#declaration_date').datepicker({format: 'yyyy-mm-dd'});
	$('#start').datepicker({format: 'yyyy-mm-dd'});
	$('#end').datepicker({format: 'yyyy-mm-dd'});

	$('#dependent_name').hide();
	$('input[name=cardholder_type]').click(function() {
		if($(this).val() == 'principal') {
				$('#dependent_name').hide();
		} else {
				$('#dependent_name').show();
		}
	});
	$('#dependent_name', this).autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/cardholder', //Controller where search is performed
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
	$('#company_insurance,#company_insurance_multi').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/company_insurance', //Controller where search is performed
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
		select: function(event, ui) {
				$('[name=company_insurance_id]').val(ui.item ? ui.item.compins_id : '');
		}
	});
	$("form").validate({
    			rules: {
    				firstname: {
    					required: true
    				},
    			    middlename: {
    			        required: true
    			    },
    			    lastname: {
    			    	required: true
    			    },
    			    dateofbirth: {
    			    	required: true
    			    },
    			    level: {
    			    	required: true
    			    },
    			    declaration_date: {
    			    	required: true
    			    },
    			    start: {
    			    	required: true
    			    },
    			    end: {
    			    	required: true
    			    },
    			    company_insurance: {
    			    	required: true
    			    }
    			},
    			messages: {
    				firstname: {
    					required: 'This field is required'
    				},
    			    middlename: {
    			        required: 'This field is required'
    			    },
    			    lastname: {
    			    	required: 'This field is required'
    			    },
    			    dateofbirth: {
    			    	required: 'This field is required'
    			    },
    			    level: {
    			    	required: 'This field is required'
    			    },
    			    declaration_date: {
    			    	required: 'This field is required'
    			    },
    			    start: {
    			    	required: 'This field is required'
    			    },
    			    end: {
    			    	required: 'This field is required'
    			    },
    			    company_insurance: {
    			    	required: 'This field is required'
    			    }
    			}
			});
});
</script>

<h1>Members</h1>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}
?>
<br>
<button id='toggleSlideSingle' class="btn btn-default">Add New Member</button>
<button id='toggleSlideMultiple' class="btn btn-default">Add Multiple Members</button>
<div id='formAddSingle'>
<?php echo validation_errors(); ?>
	<?php echo form_open('records/members/register'); ?>
	<?php
		$inputs = array(
						array('', ''),
						array(form_label('First Name', 'firstname'), form_input(array('name'=>'firstname', 'id'=>'firstname', 'size'=>'50','class'=>'form-control','placeholder'=>'First Name'))),
						array(form_label('Middle Name', 'middlename'), form_input(array('name'=>'middlename', 'id'=>'middlename', 'size'=>'50','class'=>'form-control','placeholder'=>'Middle Name'))),
						array(form_label('Last Name', 'lastname'), form_input(array('name'=>'lastname', 'id'=>'lastname', 'size'=>'50','class'=>'form-control','placeholder'=>'Last Name'))),
						array(form_label('Date Of Birth', 'dateofbirth'), form_input(array('name'=>'dateofbirth', 'id'=>'dateofbirth', 'placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
						array(form_label('Level/Position', 'level'), form_input(array('name'=>'level', 'id'=>'level', 'size'=>'50','class'=>'form-control','placeholder'=>'Level'))),
						array(form_label('Status', 'status'), form_dropdown('status', array('ACTIVE' => 'ACTIVE', 'EXPIRED' => 'EXPIRED', 'DELETED' => 'DELETED', 'ON HOLD' => 'ON HOLD'))),
						array(form_label('Date Of Declaration', 'declaration_date'), form_input(array('name'=>'declaration_date', 'id'=>'declaration_date', 'placeholder'=>'YYYY-MM-DD','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
						array(form_label('Effectivity Date'), form_input(array('name'=>'start', 'id'=>'start', 'placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
						array(form_label('Validity Date'), form_input(array('name'=>'end', 'id'=>'end', 'placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
						array(form_label('Remarks', 'remarks'), form_textarea(array('name'=>'remarks', 'id'=>'remarks', 'cols'=>'50', 'rows'=>'10','class'=>'form-control','placeholder'=>'Remarks'))),
						array(form_label('Cardholder Type', 'cardholder_type'), 
							form_radio(array('name'=>'cardholder_type', 'id'=>'cardholder_type', 'value'=>'PRINCIPAL'), '', TRUE).'Principal'.
							form_radio(array('name'=>'cardholder_type', 'id'=>'cardholder_type', 'value'=>'DEPENDENT'), '', FALSE).'Dependent'.'&nbsp'.
							form_input(array('name'=>'cardholder', 'id'=>'dependent_name', 'size'=>'50','class'=>'form-control','placeholder'=>'Dependent Name'))),
						array(form_label('Company - Insurance', 'company_insurance_id'), form_input(array('name'=>'company_insurance', 'id'=>'company_insurance', 'size'=>'150','class'=>'form-control','placeholder'=>'Company - Insurance'))),
						array(form_label('PhilHealth Benefit'),form_input(array('name'=>'philhealth','id'=>'philhealth','placeholder'=>'PhilHealth Benefit','size'=>'20','class'=>'form-control'))),
						array(form_label('Pre-Existing Condition'),form_dropdown('pre_existing_condition',array(''=>'','Covered'=>'Covered','Not Covered'=>'Not Covered','Waved'=>'Waved'))),
						array('', form_submit(array('value'=>'Register','class'=>'btn btn-success')))
						);
		$template = array(
					'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
					'table_close'	=> '</table>'
					);
		$this->table->set_template($template);
		echo $this->table->generate($inputs);
		echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));
		echo form_hidden('company_insurance_id');
	?>
<?php echo form_close(); ?>
</div>

<div id='formAddMultiple'>
	<?php echo validation_errors(); ?>
	<?php echo form_open_multipart('records/uphist/downloadTemp/11');?>
		<?php
			$template = array(
						'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
						'table_close'	=> '</table>'
						);
			$inputs = array(
							array(form_label('Download Template for Members', 'multiup'), form_submit(array('value'=>'Download','class'=>'btn btn-warning')))
							);
			echo $this->table->generate($inputs);
		?>
	<?php echo form_close(); ?>

	<?php echo form_open_multipart('utils/fileuploader/upto/company_insurance_members');?>
		<?php
			$template = array(
						'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
						'table_close'	=> '</table>'
						);
			$inputs = array(
							array(form_label('Company - Insurance', 'company_insurance_id'), form_input(array('name'=>'company_insurance_multi', 'id'=>'company_insurance', 'size'=>'150','class'=>'form-control','placeholder'=>'Company - Insurance'))),
							array(form_label('Upload multiple Members', 'multicompany'), form_upload(array('name'=>'file', 'id'=>'multicompany','class'=>'form-group'))),
							array('', form_submit(array('value'=>'Upload','class'=>'btn btn-success')))
							);
			$this->table->set_template($template);
			echo $this->table->generate($inputs);
			echo form_hidden('company_insurance_id');
		?>
	<?php echo form_close(); ?>
</div>