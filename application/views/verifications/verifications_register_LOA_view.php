<script>
$(document).ready(function(){
	var cloneCount = 1;
	$('#addbenefit').click(function(){
		cloneCount++;
		$('#benefitset').clone().attr('id','benefitset'+cloneCount).appendTo('#multi_benefit');
	});
	$('#removeBenefit').click(function() {
		if(cloneCount > 1)
		{
			$('#benefitset'+cloneCount).remove();
			cloneCount--;
		}
	});
});
</script>
<html>
<head>
	<title>Generate LOA/Verifications</title>
</head>
<?php
if($this->session->flashdata('result') != '')
{
	echo $this->session->flashdata('result');
}
?>

<h1>Generate LOA for <?php echo $patient_name;?> </h1>
<h3>Select a Benefit Type</h3>
<?php
// var_dump($options);
	$benefitTMPL = array(
					array(''),
					array(form_dropdown('benefit_name[]',$options))
					);
	$tmpl = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="benefitset">',
				'table_close' => '</table>'
				);
	$this->table->set_template($tmpl);

	if($availment_type == 'In-Patient' || $availment_type == 'In and Out Patient')
	{
		$benefitDropdown = form_fieldset('<b>Select Benefits</b> - Duplicated Benefits will be remove in later process..',array('id'=>'multi_benefit'));
		$benefitDropdown.= $this->table->generate($benefitTMPL);
		$benefitDropdown.= form_fieldset_close();
		$selectBenefit = form_button(array('name'=>'addbenefit', 'id'=>'addbenefit', 'content'=>'Add Benefit','class'=>'btn btn-info btn-sm'));
		$removeBenefit = form_button(array('name'=>'removeBenefit','id'=>'removeBenefit','content'=>'Remove Benefit','class'=>'btn btn-danger btn-xs'));
	}
	else
	{
		$selectBenefit = form_label('Select Benefit');
		$benefitDropdown = form_dropdown('benefit_name', $options);
		$removeBenefit = form_label('');
	}
?>

<?php echo form_open('verifications/fillDetails');?>
<?php echo validation_errors(); ?>
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

	foreach($diagnosis as $key => $value)
	{
		$diagnosisSet = $this->table->add_row($value);
	}
	$this->table->set_template($template);
	$diagnosis_fieldset = form_fieldset('Chief Complaint / Diagnosis');
	$diagnosis_fieldset.= $this->table->generate($diagnosisSet);
	$diagnosis_fieldset.= form_fieldset_close();

	$inputs = array(
					array('',''),
					array(form_label('<b>Patient Details:</b>'),''),
					array(form_label('Patient Name'), $patient_name),
					array(form_label('Company'), $company_name),
					array(form_label('Insurance'), $insurance_name),
					array(form_label('Company Code'), $company_code),
					array(form_label('Insurance Code'), $insurance_code),

					array('',''),
					array(form_label('<b>Medical Details:</b>'),''),
					array(form_label('Hospital Name'), $hospital_name),
					array(form_label('Hospital Branch'), $hospital_branch),
					array(form_label('Chief Complaint/Diagnosis'), $diagnosis_fieldset),

					array('',''),
					array(form_label('<b>Benefit Details:</b>'),''),
					array(form_label('Name of Benefit'), $benefit_name),
					array($ill_label,$illness),
					array(form_label('Other Condition'), $condition_name),
					array(form_label('Exclusion'),$exclusion_name),
					array(form_label('Required Service'), $availment_type),

					array('',''),
					// array(form_label('Select Benefit'),form_dropdown('benefit_name', $options)),
					array($selectBenefit, $benefitDropdown.'<br>'.$removeBenefit),
					array(form_label('Click for the next step'), form_submit(array('value'=>'Proceed to the next step','class'=>'btn btn-success btn-sm')))
					);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);

	echo form_hidden('patient_name', $patient_name);
	echo form_hidden('patient_id', $patient_id);
	echo form_hidden('benefit_set_id', $benefit_set_id);

	echo form_hidden('compins_id',$compins_id);
	echo form_hidden('company_name', $company_name);
	echo form_hidden('insurance_name', $insurance_name);
	echo form_hidden('company_code', $company_code);
	echo form_hidden('insurance_code', $insurance_code);

	echo form_hidden('hospital_name', $hospital_name);
	echo form_hidden('hospital_branch', $hospital_branch);
	echo form_hidden('diagnosis',$diagnosis);
	echo form_hidden('availment_type', $availment_type);

	if(isset($code))
	{
		echo form_hidden('code',$code);
	}

	echo form_hidden('condition_name',$condition_name);
	echo form_hidden('exclusion_name',$exclusion_name);

	if(isset($physician))
	{
		echo form_hidden('physician',$physician);
	}

	if(isset($physician_fee))
	{
		echo form_hidden('physician_fee',$physician_fee);
	}
?>
<?php echo form_close(); ?>