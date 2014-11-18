<script>
$(document).ready(function() {
	var cloneCount = 1;
	$('#addlab').click(function() {
		cloneCount++;
		$('#labset').clone().attr('id','labset'+cloneCount).appendTo('#lab_info');
	});
	$('#removeLab').click(function() {
		if(cloneCount > 1)
		{
			$('#labset'+cloneCount).remove();
			cloneCount--;
		}
	});
	$('#physician').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/verifications_physician',
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data){
					if(data.response == 'true')
					{
						add(data.message);
					}
				}
			});
		}
	});

	jQuery.validator.addMethod('compare',function(value, element){
		var mbl_amount = $("input[name='maximum_benefit_limit']").val();
		var overall = value;
		var result = mbl_amount - overall;

		if(result < 0)
		{
			return false;
		}
		return true;
	});
	$("form").validate({
    			rules: {
    				physician: {
    					required: true
    				},
    				overall: {
    					compare: true,
    					number: true,
    					required: true
    				}
    			},
    			messages: {
    				physician: {
    					required: 'This field is required'
    				},
    				overall: {
    					compare: 'You exceed or reached the Maximum Benefit Limit!',
    					number: 'Enter a number only',
    					required: 'This field is required'
    				},
    			}
			});
});
</script>
<html>
	<head>
		<title>Generate LOA/Verifications</title>
	</head>
<h1>Generate LOA for <?php echo $patient_name; ?></h1>
<h3>Fill the preceeding details...</h3>
<?php echo form_open('verifications/register');?>
<?php
	if(isset($benefit_limit_type))
	{
		$ill_label = form_label('Patient Illness');
		echo form_hidden('illness',$illness);
		echo form_hidden('benefit_limit_type',$benefit_limit_type);
	}
	else
	{
		$ill_label = '';	
	}

	$template = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
					'table_close' => '</table>'
					);

	foreach($benefit_name as $key => $value)
	{
		// var_dump($value);
		$remarks = benefit_remarks($value);
		if($remarks != NULL)
		{
			$benefitRemarks = '*'.$remarks;
		}
		else
		{
			$benefitRemarks = '';
		}

		// if($value == 'LABORATORY')
		if(strpos($value,'LABORATORY') !== FALSE)
		{
			$laboratory = $value;
			foreach($details[$key] as $dkey => $dvalue)
			{
				$field = str_replace(" ","_", $fieldValue[$key][$dkey]);

				if(@$fields_remaining[$field])
				{
					$field_value = $fields_remaining[$field];
				}
				else
				{
					$field_value = $fields[$field];
				}

				if($dvalue == 'AMOUNT' || $dvalue == 'AMOUNT PER DAY' || $dvalue == 'BY MODALITIES')
				{
					if($dvalue == 'AMOUNT')
					{
						echo form_hidden('details['.$key.']','AMOUNT');
					}
					elseif($dvalue == 'AMOUNT PER DAY')
					{
						echo form_hidden('details['.$key.']','AMOUNT PER DAY');
					}
					elseif($dvalue == 'BY MODALITIES')
					{
						echo form_hidden('details['.$key.']','BY MODALITIES');
					}
				}
				elseif($dvalue == 'AS CHARGED')
				{
					echo form_hidden('details['.$key.']','AS CHARGED');

					//OVERALL BENEFIT LIMIT
					$field_value = $fields['maximum_benefit_limit'];
				}

				if($field_value == '0' || $field_value == '0.00')
				{
					$lab_label = form_label("You've reach the limit of ".$dvalue." for this Benefit");
					unset($laboratory);
				}
				else
				{
					$lab_label = form_label('Proceed to Laboratory...');
				}
				$ipTmpl = $this->table->add_row('<b>'.$value,'<b>Details','<b>Current Details<br>(Remaining Balance)','<b>Details To Fill-Up');
				$ipDetails = $this->table->add_row(array($benefitRemarks,form_label('Total '.$dvalue),
												form_input(array('name'=>$field,'size'=>'20','value'=>$field_value,'disabled'=>'disabled','class'=>'form-control')),
												$lab_label
												));
				echo form_hidden('lab_value', $field_value);
			}
		}
		else
		{
			$ipTmpl = $this->table->add_row('<b>'.$value,'<b>Details','<b>Current Details','<b>Details To Fill-Up');
			$this->table->set_template($template);

			foreach($details[$key] as $dkey => $dvalue)
			{
				$field = str_replace(" ","_",$fieldValue[$key][$dkey]);

				if(isset($fields_remaining[$field]))
				{
					$field_value = $fields_remaining[$field];
				}
				else
				{
					$field_value = $fields[$field];
				}

				if($dvalue == 'DAYS')
				{
					echo form_hidden('details_days['.$key.']','DAYS');
					if($field_value == '0' || $field_value == '0.00')
					{
						$new_value = form_label("You've reached the limit of ".$dvalue." for this Benefit.");
					}
					else
					{
						$new_value = form_input(array('name'=>'availed_days['.$key.']','size'=>'20','class'=>'form-control'));
					}
					$hidden_value = form_hidden('registered_days['.$key.']',$field_value);
				}

				if($dvalue == 'AMOUNT' || $dvalue == 'AMOUNT PER DAY' || $dvalue == 'BY MODALITIES')
				{
					if($dvalue == 'AMOUNT')
					{
						echo form_hidden('details['.$key.']','AMOUNT');
					}
					elseif($dvalue == 'AMOUNT PER DAY')
					{
						echo form_hidden('details['.$key.']','AMOUNT PER DAY');
					}
					elseif($dvalue == 'BY MODALITIES')
					{
						echo form_hidden('details['.$key.']','BY MODALITIES');
					}
					
					if($field_value == '0' || $field_value == '0.00')
					{
						$new_value = form_label("You've reached the limit of ".$dvalue." for this Benefit.");
					}
					else
					{
						$new_value = form_input(array('name'=>'availed_amount['.$key.']','size'=>'20','class'=>'form-control'));
					}
					$hidden_value = form_hidden('registered_amount['.$key.']', $field_value);
				}

				if($dvalue == 'AS CHARGED')
				{
					echo form_hidden('details['.$key.']','AS CHARGED');
					//OVERALL BENEFIT LIMIT
					$field_value = $fields['maximum_benefit_limit'];
					if(@$field_value == '0' || $field_value == '0.00')
					{
						$new_value = form_label("You've reached the limit of ".$dvalue." for this Benefit.");
					}
					else
					{
						$new_value = form_input(array('name'=>'availed_as-charged['.$key.']','size'=>'20','class'=>'form-control'));
					}
					$hidden_value = form_hidden('registered_as-charged['.$key.']', $field_value);
				}

				$ipDetails = $this->table->add_row(array($benefitRemarks, form_label('Total '.$dvalue),
											form_input(array('name'=>$field, 'size'=> '20','value'=>$field_value,'disabled'=>'disabled','class'=>'form-control')),
											$new_value,$hidden_value
											));
			}
		}
	}
	$fieldset = form_fieldset('<b>Benefit Details</b>');
	$fieldset.= $this->table->generate($ipTmpl);
	$fieldset.= form_fieldset_close();

	//LABORATORY
	$labTMPL = array(
					array('',''),
					array('Lab','Amount'),
					array(form_input(array('name'=>'lab_test[]','id'=>'lab_test', 'size'=>'20','class'=>'form-control','placeholder'=>'Lab')),
						form_input(array('name'=>'amount[]','id'=>'amount','size'=>'20','class'=>'form-control','placeholder'=>'Amount')))
					);
	$labtmpl = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="labset">',
					'table_close' => '</table>'
					);
	if(@$laboratory)
	{
		$this->table->set_template($labtmpl);
		$labs = form_fieldset('<b>Laboratory</b>',array('id'=>'lab_info'));
		$labs.= $this->table->generate($labTMPL);
		$labs.= form_fieldset_close();
		$labButton = form_button(array('name'=>'addlab','id'=>'addlab','content'=>'Add Lab','class'=>'btn btn-info btn-xs'));
	}
	else
	{
		$labs = form_label('');
		$labButton = form_label('');
	}

	foreach($diagnosis as $key => $value)
	{
		$diagnosisSet = $this->table->add_row($value);
	}
	$this->table->set_template($template);
	$diagnosis_fieldset = form_fieldset('Chief Complaint / Diagnosis');
	$diagnosis_fieldset.= $this->table->generate($diagnosisSet);
	$diagnosis_fieldset.= form_fieldset_close();

	// PHYSICIAN FIELD
	if(!isset($physician))
	{
		$physician_field = array(form_label('Attending Physician'),form_input(array('name'=>'physician','id'=>'physician','size'=>'20','placeholder'=>'Attending Physician','class'=>'form-control')));
	}
	else
	{
		$physician_field = array(form_label('Attending Physician'),form_input(array('name'=>'physician','id'=>'physician','size'=>'20','value'=>$physician,'disabled'=>'disabled','class'=>'form-control')));
		echo form_hidden('physician',$physician);
	}

	if(!isset($physician_fee))
	{
		$physician_fee_field = array(form_label('Physician Fee'),form_input(array('name'=>'physician_fee','id'=>'physician_fee','size'=>'20','placeholder'=>'Physician Fee, enter an amount','class'=>'form-control')));
	}
	else
	{
		$physician_fee_field = array(form_label('Physician Fee'),form_input(array('name'=>'physician_fee','id'=>'physician_fee','size'=>'20','value'=>$physician_fee,'disabled'=>'disabled','class'=>'form-control')));	
		echo form_hidden('physician_fee',$physician_fee);
	}
	$inputs = array(
			array(''),
			array(form_label('<b>Patient Details</b>'),''),
			array(form_label('Patient Name'), $patient_name),
			array(form_label('Company'), $company_name),
			array(form_label('Insurance'), $insurance_name),
			array(form_label('Company Code'), $company_code),
			array(form_label('Insurance Code'), $insurance_code),

			array('',''),
			array(form_label('<b>Medical Details</b>'),''),
			array(form_label('Hospital Name'), $hospital_name),
			array(form_label('Hospital Branch'), $hospital_branch),
			array(form_label('Chief Complaint/Diagnosis'), $diagnosis_fieldset),

			array('',''),
			array(form_label('<b>Benefit Details</b>'),''),
			array(form_label('Name of Benefit'), $benefit_set_name),
			array(form_label('Benefit Plan Type'),$plan_type),
			array(form_label('Remaining Overall Benefit Limit'),'<b>PHP. '.$fields['maximum_benefit_limit'].'</b>'),
			array($ill_label,$illness),
			array(form_label('Other Condition'),$condition_name),
			array(form_label('Exclusion'),$exclusion_name),
			array(form_label('Required Service'), $availment_type),
			$physician_field,$physician_fee_field,
			array(form_label('Benefit Details'), $fieldset),
			array($labButton, $labs.'<br>'.form_button(array('name'=>'removeLab','id'=>'removeLab','content'=>'Remove Lab','class'=>'btn btn-danger btn-sm'))),

			// array($labButton, $labs),
			array(form_label('Overall Total Amount'),form_input(array('name'=>'overall','id'=>'overall','size'=>'20','class'=>'form-control','placeholder'=>'Overall Amount'))),
			array(form_label('Remarks'), form_textarea(array('name'=>'remarks','id'=>'remarks','cols'=>'50','rows'=>'5','class'=>'form-control'))),
			array('', form_submit(array('value'=>'Register','class'=>'btn btn-success btn-xs')))
			);
			$this->table->set_template($template);
			echo $this->table->generate($inputs);

	echo form_hidden('patient_name', $patient_name);
	echo form_hidden('patient_id', $patient_id);
	echo form_hidden('company_name', $company_name);
	echo form_hidden('insurance_name', $insurance_name);
	echo form_hidden('company_code', $company_code);
	echo form_hidden('insurance_code', $insurance_code);
	echo form_hidden('compins_id', $compins_id);

	echo form_hidden('hospital_name', $hospital_name);
	echo form_hidden('hospital_branch', $hospital_branch);
	echo form_hidden('diagnosis',$diagnosis);

	echo form_hidden('benefit_set_name', $benefit_set_name);
	echo form_hidden('availment_type', $availment_type);
	echo form_hidden('benefit_name', $benefit_name);
	echo form_hidden('benefit_set_id',$benefit_set_id);
	echo form_hidden('maximum_benefit_limit',$fields['maximum_benefit_limit']);

	if(isset($date_encoded))
	{
		echo form_hidden('date_encoded',$date_encoded);
	}
	else
	{
		echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));
	}

	if(isset($code))
	{
		echo form_hidden('code',$code);
	}
?>