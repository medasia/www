<script>
$(document).ready(function() {
	$('#date').datepicker({format: 'yyyy-mm-dd'});
});
</script>
<html>
<head>
	<title>Update Monitoring</title>
</head>
<h2>Update Monitoring for <?php echo $patient_name;?></h2>
<?php echo validation_errors();?>
<?php echo form_open('verifications/updateMonitoring');?>
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
					'table_open' => '<table border="0" cellspacing="0" cellpadding="4">',
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
				array(form_label('<b>Patient Details</b>'),''),
				array(form_label('Patient Name:'), $patient_name),
				array(form_label('Company: '), $company_name),
				array(form_label('Insurance: '), $insurance_name),

				array('',''),
				array(form_label('<b>Medical Details</b>'),''),
				array(form_label('Hospital Name: '), $hospital_name),
				array(form_label('Hospital Branch: '), $hospital_branch),
				array(form_label('Chief Complaint/Diagnosis: '), $chief_complaint),

				array('',''),
				array(form_label('<b>Monitoring Report</b>'),''),
				array(form_label('Date: '),form_input(array('name'=>'date','id'=>'date','size'=>'20','value'=>$date,'class'=>'form-control'))),
				array(form_label('Monitoring Time: '),form_input(array('name'=>'monitoring_time','id'=>'monitoring_time','size'=>'20','value'=>$monitoring_time,'class'=>'form-control'))),
				array(form_label('Running Bill:'),form_input(array('name'=>'running_bill','id'=>'running_bill','size'=>'20','value'=>$running_bill,'class'=>'form-control'))),
				array(form_label('Caller Name:'),form_input(array('name'=>'caller_name','id'=>'caller_name','size'=>'20','value'=>$caller_name,'class'=>'form-control'))),
				array(form_label('Status:'),form_input(array('name'=>'status','id'=>'status','size'=>'20','value'=>$status,'class'=>'form-control'))),
				array(form_label('History: '),form_textarea(array('name'=>'history','id'=>'history','size'=>'50','rows'=>'5','value'=>$history,'class'=>'form-control'))),

				array('',''),
				array(form_submit(array('name'=>'submit','value'=>'Update Monitoring','class'=>'btn btn-success')),form_submit(array('name'=>'submit','value'=>'Discharge Patient','class'=>'btn btn-danger')))			
				);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);

	echo form_hidden('patient_id', $patient_id);
	echo form_hidden('compins_id', $compins_id);
	echo form_hidden('benefit_set_id',$benefit_set_id);
	echo form_hidden('code',$code);

	echo form_hidden('patient_name', $patient_name);
	echo form_hidden('company_name', $company_name);
	echo form_hidden('insurance_name', $insurance_name);
	echo form_hidden('company_code', $company_code);
	echo form_hidden('insurance_code', $insurance_code);
	echo form_hidden('hospital_name', $hospital_name);
	echo form_hidden('hospital_branch', $hospital_branch);
	echo form_hidden('chief_complaint', $chief_complaint);
?>
<?php echo form_close(); ?>