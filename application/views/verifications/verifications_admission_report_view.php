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
		$('#specialist_name').clone().attr('id','specialist_name'+cloneCount).appendTo('#specialist_set td:first-child');
		$('#specialist_fee').clone().attr('id','specialist_fee'+cloneCount).appendTo('#specialist_set td:nth-child(2)');	
		// $('#specialist_name').clone().attr('id','specialist_name'+cloneCount).appendTo('#multi_specialist');
		// $('#specialist_fee').clone().attr('id','specialist_fee'+cloneCount).appendTo('#multi_specialist');
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
			$('#specialist_fee'+cloneCount).remove();
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
	<title>Admission Report</title>
</head>

<h1>Generate Admission Report (In-Patient)</h1>
<?php echo validation_errors(); ?>
<?php echo form_open('verifications/saveAdmission');?>
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
	date_default_timezone_set("Asia/Manila");
	$otherDet = $this->table->add_row(form_input(array('name'=>'specialist_name[]','id'=>'specialist_name','size'=>'50','class'=>'form-control','placeholder'=>'Specialist')),
									form_input(array('name'=>'specialist_fee[]','id'=>'specialist_fee','size'=>'50','class'=>'form-control','placeholder'=>'Specialist Fee, enter an amount')));
				// array(form_label('Specialist Name:'), form_dropdown('specialist_name',$specialist)),
	$tmpl = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0" id="specialist_set">',
			'table_close'	=>	'</table>'
			);
	$this->table->set_template($tmpl);
	$addDetails = form_fieldset('<b>Specialist Name/s</b>', array('id'=>'multi_specialist'));
	$addDetails.= $this->table->generate($otherDet);
	$addDetails.= form_fieldset_close();

	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$ins_approv = array(
					array('',''),
					array(form_label('Date of Email: '),form_input(array('name'=>'dateofemail','id'=>'dateofemail','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
					array(form_label('Time From: '), form_input(array('name'=>'timefrom','size'=>'15','placeholder'=>'HH:MM AM/PM','class'=>'form-control'))),
					array(form_label('Time To: '), form_input(array('name'=>'timeto','size'=>'15','placeholder'=>'HH:MM AM/PM','class'=>'form-control'))),
					array(form_label('Approved By: '), form_input(array('name'=>'approved','size'=>'20','class'=>'form-control'))),
					array(form_label('Declined By: '), form_input(array('name'=>'declined','size'=>'20','class'=>'form-control')))
					);
	$this->table->set_template($template);
	$ins_approv_fieldset = form_fieldset('<b>Insurance Approval</b>');
	$ins_approv_fieldset.= $this->table->generate($ins_approv);
	$ins_approv_fieldset.= form_fieldset_close();

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
					array(form_label('<b>Patient Details: </b>'),''),
					array(form_label('Patient Name: '),$patient_name),
					array(form_label('Company: '), $company_name),
					array(form_label('Company Code: '),$company_code),
					array(form_label('Insurance: '), $insurance_name),
					array(form_label('Insurance Code: '),$insurance_code),

					array('',''),
					array(form_label('<b>Medical Details: </b>'),''),
					array(form_label('Hospital Name: '), $hospital_name),
					array(form_label('Hospital Branch: '), $hospital_branch),
					array(form_label('Chief Complaint/Diagnosis: '), $diagnosis_fieldset),

					array('',''),
					array(form_label('<b>Admission Report:</b>'),''),
					array(form_label('Date Admitted: '), form_input(array('name'=>'date_admitted','size'=>'20','id'=>'dateadmitted','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
					array(form_label('Attending Physician: '), form_input(array('name'=>'physician','id'=>'physician','size'=>'20','placeholder'=>'Physician','class'=>'form-control'))),
					array(form_label('Physician Fee:'),form_input(array('name'=>'physician_fee','id'=>'physician_fee','size'=>'20','placeholder'=>'Physician Fee, enter amount','class'=>'form-control'))),
					array(form_button(array('name'=>'addSpecialist','id'=>'addSpecialist', 'content'=>'Add Specialist','class'=>'btn btn-info btn-sm')),$addDetails.'<br>'.
						form_button(array('name'=>'removeSpecailist','id'=>'removeSpecialist','content'=>'Remove Specialist','class'=>'btn btn-danger btn-xs'))),

					array(form_label('History: '), form_textarea(array('name'=>'history','size'=>'50','cols'=>'50','rows'=>'5','placeholder'=>'History','class'=>'form-control'))),
					array(form_label('Remarks: '), form_textarea(array('name'=>'remarks','size'=>'50','cols'=>'50','rows'=>'5','placeholder'=>'Remarks','class'=>'form-control'))),
					array(form_label('Insurance Approval: '),$ins_approv_fieldset),
					// array(form_label('Attachment: '), form_upload(array('name'=>'file'))),
					array(form_submit(array('name'=>'submit','value'=>'Save Admission Report','class'=>'btn btn-sm btn-success')),form_submit(array('name'=>'submit','value'=>'Discharge Patient','class'=>'btn btn-sm btn-danger')))
				);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);

	echo form_hidden('patient_id', $patient_id);
	echo form_hidden('compins_id', $compins_id);
	echo form_hidden('benefit_set_id', $benefit_set_id);
	echo form_hidden('availment_type', $availment_type);

	echo form_hidden('patient_name',$patient_name);
	echo form_hidden('company_name', $company_name);
	echo form_hidden('insurance_name', $insurance_name);
	echo form_hidden('company_code', $company_code);
	echo form_hidden('insurance_code', $insurance_code);
	echo form_hidden('hospital_name', $hospital_name);
	echo form_hidden('hospital_branch', $hospital_branch);
	echo form_hidden('diagnosis', $diagnosis);
	echo form_hidden('principal_name',$principal_name);
	echo form_hidden('date_encoded', $date_encoded);
	echo form_hidden('benefit_name', $benefit_name);
?>
<?php echo form_close(); ?>