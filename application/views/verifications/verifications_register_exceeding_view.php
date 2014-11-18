<script>
$(document).ready(function(){
	$('#old_loa').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/verifications_special_loa', //Controller where search is performed
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
			$('[name=hospital_name]').val(ui.item ? ui.item.hospital_name : '');
			$('[name=hospital_branch]').val(ui.item ? ui.item.hospital_branch : '');
			$('[name=physician]').val(ui.item ? ui.item.physician : '');
			$('[name=availment_type]').val(ui.item ? ui.item.availment_type : '');
		}
	});
	$("form").validate({
    			rules: {
    				old_loa: {
    					required: true
    				},
    				amount: {
    					required: true,
    					number: true
    				},
    			},
    			messages: {
    				old_loa: {
    					required: 'This field is required'
    				},
    				amount: {
    					required: 'This field is required',
    					number: 'Enter valid amount only'
    				},
    			}
			});
});
</script>
<title>Exceeding LOA/Availments</title>
<?php
	echo form_open('verifications/registerSpecial');
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$inputs = array(
			array('',''),
			array(form_label('Previous LOA/Availment Code'),form_input(array('name'=>'old_loa','id'=>'old_loa','size'=>'20','class'=>'form-control','placeholder'=>'Previous LOA'))),
			array(form_label('Patient Name', 'patient_name'), $lastname.', '.$firstname.' '.$middlename),
			array(form_label('Company', 'company_name'), $compins['company']),
			array(form_label('Insurance', 'insurance_name'), $compins['insurance']),
			array(form_label('Company Code', 'company_code'), $company_code['code']),
			array(form_label('Insurance Code', 'insurance_code'), $insurance_code['Code']),
			array(form_label('Company - Insurance Remarks'),$compins['notes']),

			array(form_label('Name of Benefit'), '<b>'.$benefit_name.'</b>'),
			array(form_label('Benefit Schedule Type'),'<b>'.$benefitset_info['benefit_limit_type']),

			array(form_label('Other Conditions'), '<b>'.$benefitset_info['condition_name']),
			array(form_label('Exclusion'),'<b>'.$benefitset_info['exclusion_name']),

			array(form_label('Hospital Name'), form_input(array('name'=>'hospital_name', 'id'=>'hospital_name', 'size'=>'20','class'=>'form-control','placeholder'=>'Hospital Name'))),
			array(form_label('Hospital Branch'), form_input(array('name'=>'hospital_branch', 'id'=>'hospital_branch', 'size'=>'20','class'=>'form-control','placeholder'=>'Hospital Branch'))),
			array(form_label('Physician'), form_input(array('name'=>'physician', 'id'=>'physician', 'size'=>'20','class'=>'form-control','placeholder'=>'Physician'))),
			array(form_label('Availment Type'), form_input(array('name'=>'availment_type', 'id'=>'availment_type', 'size'=>'20','class'=>'form-control','placeholder'=>'Availment Type'))),
			array(form_label('Exceeding Amount'),form_input(array('name'=>'amount','id'=>'amount','size'=>'20','class'=>'form-control','placeholder'=>'Amount'))),
			array(form_label('Remarks', 'remarks'), form_textarea(array('name'=>'remarks', 'id'=>'remarks', 'cols'=>'50', 'rows'=>'10','class'=>'form-control','placeholder'=>'Remarks'))),
			array('',form_submit(array('name'=>'submit','value'=>'Register Exceeding Amount','class'=>'btn btn-success')))
			);
	$this->table->set_template($tmpl);
	$exceeding = form_fieldset('<b>Exceeding LOA/Availments</b>');
	$exceeding.= $this->table->generate($inputs);
	$exceeding.= form_fieldset_close();

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
	echo form_hidden('benefit_name', $benefit_name);
	echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));

	echo form_hidden('condition_name',$benefitset_info['condition_name']);
	echo form_hidden('exclusion_name',$benefitset_info['exclusion_name']);

	$form = array(array($exceeding));
	echo $this->table->generate($form);
	echo form_close();
?>