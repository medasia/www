<!DOCTYPE html>
<html lang="en">
<head>
	<title>Generate LOA/Verifications</title>
	<script>
$(document).ready(function() {
	$('#dateofbirth').datepicker({format: 'yyyy-mm-dd'});
	$('#diagnosis, #illness').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/diagnosis', //Controller where search is performed
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
	$('#hospital_name').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/verifications_hospclinic', //Controller where search is performed
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
			$('[name=hospital_branch]').val(ui.item ? ui.item.branch : '');
		}
	});
	// $('#hospital_branch').autocomplete({
	// 	minLength: 1,
	// 	source: function(req, add){
	// 		$.ajax({
	// 			url: '<?=base_url()?>utils/autocomplete/from/verifications_hospclinic_branch', //Controller where search is performed
	// 			dataType: 'json',
	// 			type: 'POST',
	// 			data: {
	// 				term : req.term,
	// 				hospital : $('#hospital_name').val()
	// 			},
	// 			success: function(data) {
	// 				if(data.response =='true'){
	// 					add(data.message);
	// 				}
	// 			}
	// 		});
	// 	}
	// });
	$('#addlab').click(function() {
		$('#labset').clone().appendTo('#lab_info');
	});
	var cloneCount = 1;
	$('#addDiagnosis').click(function() {
		cloneCount++;
		$('#diagnosis').clone().attr('id','diagnosis'+cloneCount).attr('class','diagnosis form-control').appendTo('#multi_diagnosis');
			$('#diagnosis'+cloneCount).autocomplete({
			minLength: 1,
			source: function(req, add){
				$.ajax({
					url: '<?=base_url()?>utils/autocomplete/from/diagnosis', //Controller where search is performed
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
	});
	$('#removeDiagnosis').click(function() {
		if(cloneCount > 1)
		{
			$('#diagnosis'+cloneCount).remove();
			cloneCount--;
		}
	});
	$("form").validate({
    			rules: {
    				illness: {
    					required: true
    				},
    				hospital_name: {
    					required: true
    				},
    			    diagnosis: {
    			        required: true
    			    }
    			},
    			messages: {
    				illness: {
    					required: 'This field is required'
    				},
    				hospital_name: {
    					required: 'This field is required'
    				},
    			    diagnosis: {
    			        required: "This field is required"
    			    }
    			}
			});
});
</script>
</head>
<h1>Generate LOA for <?php echo $lastname.', '.$firstname.' '.$middlename; ?></h1>
<?php echo validation_errors(); ?>
<?php
	// var_dump($patient);
	// 
	if($status != 'ACTIVE')
	{
		$availment = form_label("Patient's membership status had expired or not yet ACTIVE.");
		$nextStep = anchor(base_url().'records/members/view/'.$id,'Reactivate Patient',array('class'=>'btn btn-info'));
	}
	else
	{
		if(empty($options))
		{
			echo "<h2>This member doesn't have any registered Benefits!</h2>";
			$availment = form_label("<b>This member doesn't have any registered Benefits.</b>");
			$nextStep = anchor(base_url()."records/benefits", "Register Benefits here!");
		}
		else
		{
			$availment = form_dropdown('availment_type', $options);
			$nextStep = form_submit(array('value'=>'Select Benefit Type','class'=>'btn btn-success btn-sm'));
		}
	}

	if(empty($benefit_name))
	{
		$benefit = form_label("Not yet member of any Benefits.");
		$benefitset_info['benefit_limit_type'] = $benefit;
	}
	else
	{
		$benefit = $benefit_name;
	}
?>

<?php echo form_open('verifications/registerLOA'); ?>
<?php
	if($benefitset_info['benefit_limit_type'] == 'Per Illness')
	{
		$ill_label = form_label('Patient Illness');
		$ill_input = form_input(array('name'=>'illness','id'=>'illness','size'=>'20','placeholder'=>'Enter Illness','class'=>'form-control'));
		echo form_hidden('benefit_limit_type',$benefitset_info['benefit_limit_type']);
	}
	else
	{
		$ill_label = '';
		$ill_input = '';
	}
	
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="diagnosis_set">',
			'table_close' => '</table>'
			);

	$diagnosis = array(
				array(form_input(array('name'=>'diagnosis[]','id'=>'diagnosis','class'=>'form-control','size'=>'50','placeholder'=>'Chief Complaint / Diagnosis')))
				);
	$this->table->set_template($tmpl);
	$addDiagnosis = form_fieldset('<b>Chief Complaint / Diagnosis</b>',array('id'=>'multi_diagnosis'));
	$addDiagnosis.= $this->table->generate($diagnosis);
	$addDiagnosis.= form_fieldset_close();

	$inputs = array(
				array('', ''),
				array(form_label('Patient Name', 'patient_name'), $lastname.', '.$firstname.' '.$middlename),
				array(form_label('Company', 'company_name'), $compins['company']),
				array(form_label('Insurance', 'insurance_name'), $compins['insurance']),
				array(form_label('Company Code', 'company_code'), $company_code['code']),
				array(form_label('Insurance Code', 'insurance_code'), $insurance_code['Code']),
				array(form_label('Company - Insurance Remarks'),$compins['notes']),

				array(form_label('Name of Benefit'), '<b>'.$benefit.'</b>'),
				array(form_label('Benefit Schedule Type'),'<b>'.$benefitset_info['benefit_limit_type']),
				array($ill_label,$ill_input),
				array(form_label('Other Conditions'), '<b>'.$benefitset_info['condition_name']),
				array(form_label('Exclusion'),'<b>'.$benefitset_info['exclusion_name']),
				
				// FIX HOSPITAL BRANCH
				array(form_label('Hospital Name', 'hospital_name'), form_input(array('name'=>'hospital_name', 'id'=>'hospital_name', 'size'=>'50','class'=>'form-control','placeholder'=>'Hospital Name'))),
				array(form_label('Hospital Branch'), form_input(array('name'=>'hospital_branch', 'id'=>'hospital_branch', 'size'=>'50','class'=>'form-control','placeholder'=>'Hospital Branch'))),
				// FIX HOSPITAL BRANCH
				
				array(form_button(array('name'=>'addDiagnosis','id'=>'addDiagnosis','content'=>'Add Diagnosis','class'=>'btn btn-info btn-xs')),$addDiagnosis.'<br>'.
					form_button(array('name'=>'removeDiagnosis','id'=>'removeDiagnosis','content'=>'Remove Diagnosis','class'=>'btn btn-danger btn-xs'))),
				
				// // FIX LAB
				// array(form_button(array('name' => 'addlab', 'id' => 'addlab', 'content' => 'Add lab')), $labs),
				// // FIX LAB
				
				array(form_label('Required Service', 'availment_type'), $availment
																				// form_dropdown('availment_type', @$options
																				// array(
																				// 	'In Patient' => 'In Patient',
																				// 	'Consultation' => 'Consultation',
																				// 	'Laboratory / Diagnostic procedures' => 'Laboratory / Diagnostic procedures',
																				// 	'ER Case' => 'ER Case',
																				// 	'Reimbursement' => 'Reimbursement',
																				// 	'APE' => 'APE',
																				// 	'ECU' => 'ECU',
																				// 	'Dental' => 'Dental',
																				// 	'OP / IP' => 'OP / IP',
																				// 	'OP Medicines' => 'OP Medicines',))
																				),
				array(form_label('Principal name', 'principal_name'), $cardholder),
				// array(form_label('Remarks', 'remarks'), form_textarea(array('name'=>'remarks', 'id'=>'remarks', 'cols'=>'40', 'rows'=>'5'))),
				
				array(form_label('Proceed to the next step'), $nextStep)
				);
	$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);
	echo form_hidden('patient_name', $lastname.', '.$firstname.' '.$middlename);
	echo form_hidden('patient_id', $id);
	echo form_hidden('company_name', $compins['company']);
	echo form_hidden('insurance_name', $compins['insurance']);
	echo form_hidden('notes',$compins['notes']);
	echo form_hidden('compins_id', $compins['id']);
	echo form_hidden('company_code', $company_code['code']);
	echo form_hidden('insurance_code', $insurance_code['Code']);
	echo form_hidden('principal_name', $cardholder);
	echo form_hidden('benefit_set_id', $benefit_set_id);
	echo form_hidden('benefit_name', $benefit);
	echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));

	echo form_hidden('condition_name',$benefitset_info[0]['condition_name']);
	echo form_hidden('exclusion_name',$benefitset_info[0]['exclusion_name']);
?>
<?php echo form_close(); ?>