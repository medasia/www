<script>
$(document).ready(function() {
	$('#physician, #specialist_name').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/verifications_physician', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data) {
					if(data.response == 'true')
					{
						add(data.message);
					}
				}
			});
		}
	});
	var cloneCount = 1;
	$('#addSpecialist').click(function() {
		cloneCount++;
		$('#specialist_name').clone().attr('id','specialist_name'+cloneCount).appendTo('#multi_specialist');
			$('#specialist_name'+cloneCount).autocomplete({
			minLength: 1,
			source: function(req, add){
				$.ajax({
					url: '<?=base_url()?>utils/autocomplete/from/verifications_physician', //Controller where search is performed
					dataType: 'json',
					type: 'POST',
					data: req,
					success: function(data) {
						if(data.response == 'true')
						{
							add(data.message);
						}
					}
				});
			}
		});
	});
	$('#removeSpecialist').click(function() {
		if(cloneCount > 1)
		{
			$('#specialist_name'+cloneCount).remove();
			cloneCount--;
		}
	});
	$('#dateadmitted').datepicker({format: 'yyyy-mm-dd'});
	$('#dateofemail').datepicker({format: 'yyyy-mm-dd'});
	$("form").validate({
    			rules: {
    				physician: {
    					required: true
    				},
    				date_admitted: {
    					required: true
    				}
    			},
    			messages: {
    				physician: {
    					required: 'This field is required'
    				},
    				date_admitted: {
    					required: 'This field is required'
    				}
    			}
			});
});
</script>
<html>
<head>
	<title>Update Admission Report</title>
</head>
<h1>Update Admission Report (In-Patient)</h1>
<?php echo validation_errors(); ?>
<?php echo form_open('verifications/updateAdmission');?>
<?php
	date_default_timezone_set("Asia/Manila");

	if(isset($specialist_name))
	{
		foreach($specialist_name as $key => $value)
		{
			$otherDet = $this->table->add_row();
			$otherDet.= $this->table->add_row(form_input(array('name'=>'specialist_name[]','id'=>'specialist_name','size'=>'50','value'=>$value,'class'=>'form-control')));
			// $otherDet.= $this->table->add_row(array(form_label('Specialist Name:'),form_dropdown('specialist_name[]',$specialist, $value)));
		}
		$template = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="specialist_set">',
					'table_close' => '</table>'
					);
		$this->table->set_template($template);
		$addDetails = form_fieldset('<b>Specialist Name/s</b>', array('id'=>'multi_specialist'));
		$addDetails.= $this->table->generate($otherDet);
		$addDetails.= form_fieldset_close();
	}
	else
	{
		$otherDet = array(
				array('',''),
				// array(form_label('Specialist Name:'), form_input(array('name'=>'specialist_name[]','id'=>'specialist_name','size'=>'20','class'=>'form-control','placeholder'=>'Specialist'))),
				array(form_label('Specialist Name: '), form_dropdown('specialist_name[]',$specialist)),
				array('','')
				);
		$template = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0" id="specialist_set">',
			'table_close'	=>	'</table>'
			);
		$this->table->set_template($template);
		$addDetails = form_fieldset('<b>Specialist Name/s</b>', array('id'=>'multi_specialist'));
		$addDetails.= $this->table->generate($otherDet);
		$addDetails.= form_fieldset_close();
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

	$ins_approv = array(
					array('',''),
					array(form_label('Date of Email: '), form_input(array('name'=>'dateofemail','size'=>'20','id'=>'dateofemail','value'=>$dateofemail,'class'=>'form-control'))),
					array(form_label('Time From: '), form_input(array('name'=>'timefrom','id'=>'timefrom','size'=>'20','value'=>$timefrom,'class'=>'form-control'))),
					array(form_label('Time To: '), form_input(array('name'=>'timeto','id'=>'timeto','size'=>'20','value'=>$timeto,'class'=>'form-control'))),
					array(form_label('Approved By:'),form_input(array('name'=>'approved','id'=>'approved','size'=>'20','value'=>$approved,'class'=>'form-control'))),
					array(form_label('Declined By: '),form_input(array('name'=>'declined','id'=>'declined','size'=>'20','value'=>$declined,'class'=>'form-control')))
					);
	$this->table->set_template($template);
	$ins_approv_fieldset = form_fieldset('<b>Insurance Approval</b>');
	$ins_approv_fieldset.= $this->table->generate($ins_approv);
	$ins_approv_fieldset.= form_fieldset_close();
	$this->table->set_template($template);
	$update_admission = array(
							array('',''),
							array(form_label('<b>Patient Details</b>'),''),
							array(form_label('Patient Name: '),$patient_name),
							array(form_label('Company: '),$company_name),
							array(form_label('Company Code: '),$company_code),
							array(form_label('Insurance: '),$insurance_name),
							array(form_label('Insurance Code: '), $insurance_code),

							array('',''),
							array(form_label('<b>Medical Details </b>'),''),
							array(form_label('Hospital Name: '), $hospital_name),
							array(form_label('Hospital Branch: '),$hospital_branch),
							array(form_label('Chief Complaint / Diagnosis: '), $diagnosis_fieldset),

							array('',''),
							array(form_label('<b>Admission Report</b>'),''),
							array(form_label('Date Admitted'),form_input(array('name'=>'date_admitted','size'=>'20','id'=>'dateadmitted','value'=>$date_admitted,'class'=>'form-control'))),
							array(form_label('Attending Physician: '),form_input(array('name'=>'physician','size'=>'20','id'=>'physician','value'=>$physician,'class'=>'form-control'))),
							array(form_button(array('name'=>'addSpecialist','id'=>'addSpecialist','content'=>'Add Specialist','class'=>'btn btn-sm btn-info')),$addDetails.'<br>'.
								form_button(array('name'=>'removeSpecialist','id'=>'removeSpecialist','content'=>'Remove Specialist','class'=>'btn btn-sm btn-danger'))),

							array(form_label('History: '),form_textarea(array('name'=>'history','id'=>'history','size'=>'50','rows'=>'5','value'=>$history,'class'=>'form-control'))),
							array(form_label('Remarks: '),form_textarea(array('name'=>'remarks','id'=>'remarks','size'=>'50','rows'=>'5','value'=>$remarks,'class'=>'form-control'))),
							array(form_label('Insurance Approval: '),$ins_approv_fieldset),
							// array(form_label('Attachment: '), form_upload(array('name'=>'file'))),
							array(form_submit(array('name'=>'submit','value'=>'Update Admission Report','class'=>'btn btn-sm btn-success')),form_submit(array('name'=>'submit','value'=>'Discharge Patient','class'=>'btn btn-sm btn-danger')))
							);
	$this->table->set_template($template);
	echo $this->table->generate($update_admission);

	echo form_hidden('patient_id',$patient_id);
	echo form_hidden('compins_id',$compins_id);
	echo form_hidden('benefit_set_id',$benefit_set_id);
	echo form_hidden('code', $code);

	echo form_hidden('patient_name', $patient_name);
	echo form_hidden('company_name', $company_name);
	echo form_hidden('insurance_name', $insurance_name);
	echo form_hidden('company_code', $company_code);
	echo form_hidden('insurance_code', $insurance_code);
	echo form_hidden('hospital_name', $hospital_name);
	echo form_hidden('hospital_branch',$hospital_branch);
	echo form_hidden('chief_complaint',$chief_complaint);
?>
<?php echo form_close(); ?>