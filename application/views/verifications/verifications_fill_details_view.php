<script>
$(document).ready(function() {
	$('#physician').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/verifications_physician',
				dataType: 'json',
				type: 'POST',
				success: function(data){
					if(data.response == 'true')
					{
						add(data.message);
					}
				}
			});
		}
	});
	var labCount = 1;
	$('#addlab').click(function() {
		labCount++;
		$('#labset').clone().attr('id','labset'+labCount).appendTo('#lab_info');
	});
	$('#removeLab').click(function() {
		if(labCount > 1)
		{
			$('#labset'+labCount).remove();
			labCount--;
		}
	});

	jQuery.validator.addMethod('compare',function(value,element){
		var mbl_amount = $('input[name="maximum_benefit_limit"]').val();
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
    					required: 'This field is required',
    					number: 'Number only'
    				}
    			}
			});
});
</script>
<html>
<head>
	<title>Generate LOA/Verifications</title>
</head>
<h1>Generate LOA for <?php echo $patient_name; ?> </h1>
<h3>Fill the preceeding details...</h3>
<?php
	$labTMPL = array(
			array('',''),
			array('Lab','Amount'),
			array(form_input(array('name'=>'lab_test[]','id'=>'lab_test','size'=>'20','class'=>'form-control','placeholder'=>'Lab')),
				form_input(array('name'=>'amount[]','id'=>'amount','size'=>'20','class'=>'form-control','placeholder'=>'Amount'))),
			);
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="labset">',
			'table_close' => '</table>'
			);
	$this->table->set_template($tmpl);

	// if($benefit_name == 'LABORATORY')
	if(strpos($benefit_name,'LABORATORY') !== FALSE)
	{
		$labs = form_fieldset('<b>Laboratory</b>', array('id'=>'lab_info'));
		$labs.= $this->table->generate($labTMPL);
		$labs.= form_fieldset_close();
		$labButton = form_button(array('name'=>'addlab','id'=>'addlab','content'=>'Add Lab','class'=>'btn btn-info btn-sm'));
	}
	else
	{
		$labs = form_label('');
		$labButton = form_label('');
	}
?>
<?php echo form_open('verifications/register'); ?>
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
	$fieldDetails = $this->table->add_row(array('','Current Details','Details To Fill-Up'));
	foreach($fieldValue as $key => $value)
	{
		$field = str_replace(" ","_",$value);
		// var_dump($fields_remaining[$field]);
		// var_dump($fields[$field]);
		// var_dump($value);
		$remarks = benefit_remarks($benefit_name);
		if($remarks != NULL)
		{
			$benefitRemarks = '*'.$remarks;
		}
		else
		{
			$benefitRemarks = '';
		}

		// if($benefit_name == 'LABORATORY')
		if(strpos($benefit_name,'LABORATORY') !== FALSE)
		{
			if(@$fields_remaining[$field])
			{
				$field_value = $fields_remaining[$field];
			}
			else
			{
				$field_value = $fields[$field];
			}

			// echo form_hidden('details','LABORATORY');
			$labHidden = form_hidden('lab_value', $field_value);
			$new_value = form_label('Proceed to Laboratory');

			if(strpos($value,'AS CHARGED'))
			{
				echo form_hidden('details','AS CHARGED');

				//OVERALL BENEFIT LIMIT
				$field_value = $fields['maximum_benefit_limit'];
			}
			elseif($value == 'AMOUNT')
			{
				echo form_hidden('details','AMOUNT');
			}
			elseif($value == 'AMOUNT PER DAY')
			{
				echo form_hidden('details','AMOUNT PER DAY');
			}
			elseif($value == 'BY MODALITIES')
			{
				echo form_hidden('details','BY MODALITIES');
			}
		}
		else
		{
			$labHidden = form_hidden('lab_value','');		

		if(strpos($value, 'DAYS') AND $benefit_name != 'LABORATORY')
		// if(strpos($value,'DAYS') AND (strpos($benefit_name, 'LABORATORY')!== FALSE))
		{
			if($fields_remaining[$field])
			{
				$field_value = $fields_remaining[$field];
			}
			else
			{
				$field_value = $fields[$field];
			}
			echo form_hidden('details', 'DAYS');
			if($field_value == '0' || $field_value == '0.00')
			{
				@$new_value = form_label(" You've reached the limit of ".$value." Benefit/s.");
			}
			else
			{
				@$new_value = form_input(array('name'=>'availed_days','size'=>'20','class'=>'form-control'));
			}
			@$hidden_value = form_hidden('registered_days',$field_value);
		}
		
		if((strpos($value,'AMOUNT') || strpos($value,'AMOUNT PER DAY')) || (strpos($value,'BY MODALITIES')) AND $benefit_name != 'LABORATORY')
		// if((strpos($value,'AMOUNT') || strpos($value,'AMOUNT PER DAY')) AND strpos($benefit_name,'LABORATORY') !== FALSE)
		// if(($value == 'AMOUNT' || $value == 'AMOUNT PER DAY') AND $benefit_name != 'LABORATORY')
		{
			if($fields_remaining[$field])
			{
				$field_value = $fields_remaining[$field];
			}
			else
			{
				$field_value = $fields[$field];
			}
			
			if(strpos($value, 'AMOUNT'))
			{
				echo form_hidden('details','AMOUNT');
			}
			elseif(strpos($value,'AMOUNT_PER_DAY'))
			{
				echo form_hidden('details','AMOUNT PER DAY');
			}
			elseif(strpos($value, 'BY MODALITIES'))

			if($field_value == '0' || $field_value == '0.00')
			{
				@$new_value = form_label("You've reached the limit of ".$value." for this Benefit.");
			}
			else
			{
				@$new_value = form_input(array('name'=>'availed_amount','size'=>'20','class'=>'form-control'));
			}
			@$hidden_value = form_hidden('registered_amount', $field_value);
		}

		if(strpos($value, 'AS CHARGED') AND $benefit_name != 'LABORATORY')
		// if(strpos($value, 'AS CHARGED') AND strpos($benefit_name, 'LABORATORY') !== FALSE)
		// if($value == 'AS CHARGED' AND $benefit_name != 'LABORATORY')
		{
			if($fields_remaining[$field])
			{
				$field_value = $fields_remaining[$field];
			}
			else
			{
				$field_value = $fields[$field];
			}

			//OVERALL BENEFIT LIMIT
			$field_value = $fields['maximum_benefit_limit'];

			echo form_hidden('details','AS CHARGED');
			if($field_value == '0' || $field_value == '0.00')
			{
				@$new_value = form_label("You've reached the limit of ".$value." for this Benefit.");
			}
			else
			{
				@$new_value = form_input(array('name'=>'availed_amount','size'=>'20','class'=>'form-control'));
			}
			@$hidden_value = form_hidden('registered_amount',$field_value);
		}
		} 

		$fieldDetails = $this->table->add_row(array(form_label('Total '. $details[$key]),
													form_input(array('name'=>$value, 'size'=>'20', 'value'=>$field_value, 'disabled'=>'disabled','class'=>'form-control')),
													@$new_value,@$hidden_value,@$labHidden
													));
	}
	$this->table->set_template($template);
	$fieldset = form_fieldset("<b>".$benefit_name."</b>");
	$fieldset.= $this->table->generate($fieldDetails);
	$fieldset.= form_fieldset_close();

	foreach($diagnosis as $key => $value)
	{
		$diagnosisSet = $this->table->add_row($value);
	}
	$this->table->set_template($template);
	$diagnosis_fieldset = form_fieldset('Chief Complaint / Diagnosis');
	$diagnosis_fieldset.= $this->table->generate($diagnosisSet);
	$diagnosis_fieldset.= form_fieldset_close();

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
				array(form_label('Other Details'),$condition_name),
				array(form_label('Exclusion'),$exclusion_name),
				array(form_label('Required Service'), $availment_type),
				array(form_label('Attending Physician'),form_input(array('name'=>'physician','id'=>'physician','size'=>'20','placeholder'=>'Attending Physician','class'=>'form-control'))),
				array(form_label("Physician's Fee"),form_input(array('name'=>'physician_fee','id'=>'physician_fee','size'=>'20','placeholder'=>'Physician Fee, enter an amount','class'=>'form-control'))),
				array(form_label('Benefit Name'), '<b>'.$benefit_name.'</b>'),
				array('', $benefitRemarks),
				array(form_label('Benefit Details'), $fieldset),

				array($labButton, $labs.'<br>'.form_button(array('name'=>'removeLab','id'=>'removeLab','content'=>'Remove Love','content'=>'Remove Lab','class'=>'btn btn-danger btn-sm'))),
				// array(form_button(array('name'=>'addLab','id'=>'addlab','content'=>'Add Lab')), $labs),
				array(form_label('Overall Total Amount'),form_input(array('name'=>'overall','id'=>'overall','size'=>'20','class'=>'form-control'))),
				array(form_label('Remarks'),form_textarea(array('name'=>'remarks','id'=>'remarks','cols'=>'50','rows'=>'5','class'=>'form-control'))),
				array('',form_submit(array('value'=>'Register','class'=>'btn btn-success btn-sm')))
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
	echo form_hidden('benefit_set_id', $benefit_set_id);
	echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));
	echo form_hidden('maximum_benefit_limit',$fields['maximum_benefit_limit']);
?>
<?php echo form_close(); ?>