<script>
$(document).ready(function() {
	$('#dateofbirth').datepicker({format: 'yyyy-mm-dd'});
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
		}
	});
	$('#hospital_branch').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/verifications_hospclinic_branch', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				data: {
					term : req.term,
					hospital : $('#hospital_name').val()
				},
				success: function(data) {
					if(data.response =='true'){
						add(data.message);
					}
				}
			});
		}
	});
	$('#addlab').click(function() {
		$('#labset').clone().appendTo('#lab_info');
	});
});
</script>
<h1>Edit LOA for <?=$patient_name ?></h1>
<?php echo validation_errors(); ?>
<?php echo form_open('verifications/update/'.$id); ?>
<?php
var_dump($lab_test_test);

	foreach($lab_test_test as $key => $value)
	{
		var_dump($lab_test_test[$key]['id']);
		echo form_hidden('lab_id[]', $lab_test_test[$key]['id']);

		$labTMPL =  array(
				array('', ''),
				array('Lab', 'Amount'),
				array(form_input(array('name'=>'lab_test[]','id'=>'lab_test', 'value'=>$lab_test_test[$key]['lab_test'],'size'=>'20')), form_input(array('name'=>'amount[]','id'=>'amount','value'=>$lab_test_test[$key]['amount'],'size'=>'20'))),
				);
	}
		$template = array(
				'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0" id="labset">',
				'table_close'	=> '</table>'
				);
		$this->table->set_template($template); 
		$labs = form_fieldset('Laboratory', array('id'=>'lab_info'));
		$labs.= $this->table->generate($labTMPL);
		$labs.= form_fieldset_close();

	$inputs = array(
				array('', ''),
				array(form_label('Patient Name', 'patient_name'), $patient_name),
				array(form_label('Company', 'company_name'), $company_name),
				array(form_label('Insurance', 'insurance_name'), $company_name),
				array(form_label('Company code', 'company_code'), $company_code),
				array(form_label('Insurance code', 'insurance_code'), $insurance_code),
				
				// FIX HOSPITAL BRANCH
				array(form_label('Hospital Name', 'hospital_name'), form_input(array('name'=>'hospital_name', 'value'=> $hospital_name,'id'=>'hospital_name', 'size'=>'50'))),
				array(form_label('Hospital Branch', 'hospital_branch'), form_input(array('name'=>'hospital_branch', 'value'=> $hospital_branch,'id'=>'hospital_branch', 'size'=>'50'))),
				// FIX HOSPITAL BRANCH
				
				array(form_label('Chief Complaint/Diagnosis', 'chief_complaint'), form_input(array('name'=>'chief_complaint', 'value'=>$chief_complaint,'id'=>'chief_complaint', 'size'=>'50'))),
				
				// FIX LAB
				array(form_button(array('name' => 'addlab', 'id' => 'addlab', 'content' => 'Add lab')), $labs),
				// FIX LAB
				
				array(form_label('Availment Type', 'availment_type'), form_dropdown('availment_type', 
																				array(
																					'In Patient' => 'In Patient',
																					'Consultation' => 'Consultation',
																					'Laboratory / Diagnostic procedures' => 'Laboratory / Diagnostic procedures',
																					'ER Case' => 'ER Case',
																					'Reimbursement' => 'Reimbursement',
																					'APE' => 'APE',
																					'ECU' => 'ECU',
																					'Dental' => 'Dental',
																					'OP / IP' => 'OP / IP',
																					'OP Medicines' => 'OP Medicines',
																					),$availment_type)),
				array(form_label('Principal name', 'principal_name'), $principal_name),
				array(form_label('Remarks', 'remarks'), form_textarea(array('name'=>'remarks','value'=>$remarks,'id'=>'remarks', 'cols'=>'40', 'rows'=>'5'))),
				
				array('', form_submit('submit', 'Update'))
				);

	$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);

	$this->table->set_template($template);
	echo $this->table->generate($inputs);
	
	echo form_hidden('patient_name', $patient_name);
	echo form_hidden('patient_id', $patient_id);
	echo form_hidden('company_name', $company_name);
	echo form_hidden('insurance_name', $insurance_name);
	echo form_hidden('company_code', $company_code);
	echo form_hidden('insurance_code', $insurance_code);
	echo form_hidden('principal_name', $principal_name);
	echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));
?>
<?php echo form_close(); ?>