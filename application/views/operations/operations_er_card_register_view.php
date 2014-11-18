<script>
$(document).ready(function() {
	$('#dateofactivation').datepicker({format: 'yyyy-mm-dd'});
	$('#datevalid').datepicker({format: 'yyyy-mm-dd'});
	$('#dateexpiration').datepicker({format: 'yyyy-mm-dd'});

	$('#patient', this).autocomplete({
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
		},
		select: function(event, ui) {
				$('[name=birth_date]').val(ui.item ? ui.item.birthdate : '');
				$('[name=patient_id]').val(ui.item ? ui.item.Id : '');
		}
	});
});
</script>
<title>Medriks - Emergency Card</title>
<div id="divNew">
<h2>Register/Activate ER Card # <?php echo $card_number; ?></h2>
<?php echo form_open('operations/register'); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Card Number'),$card_number),
				array(form_label('Pin Number'),$pin_number),
				array(form_label('Patient Name'),form_input(array('name'=>'patient','id'=>'patient','size'=>'50','placeholder'=>'Patient Name','class'=>'form-control'))),
				array(form_label('Date of Birth'),form_input(array('name'=>'birth_date','id'=>'birth_date','size'=>'50','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Benficiary First Name'), form_input(array('name'=>'beneficiary_firstname', 'id'=>'firstname', 'size'=>'50','class'=>'form-control'))),
				array(form_label('Beneficiary Middle Name'), form_input(array('name'=>'beneficiary_middlename', 'id'=>'middlename', 'size'=>'50','class'=>'form-control'))),
				array(form_label('Beneficiary Last Name'), form_input(array('name'=>'beneficiary_lastname', 'id'=>'lastname', 'size'=>'50','class'=>'form-control'))),
				array(form_label('Occupation'),form_input(array('name'=>'occupation','id'=>'occupation','size'=>'50','class'=>'form-control'))),
				array(form_label('Relationship'),form_input(array('name'=>'relationship','id'=>'relationship','size'=>'50','class'=>'form-control'))),
				array(form_label('Landine Number'),form_input(array('name'=>'landline_number','id'=>'landline_number','size'=>'50','class'=>'form-control'))),
				array(form_label('Mobile Number'),form_input(array('name'=>'mobile_number','id'=>'mobile_number','size'=>'50','class'=>'form-control'))),
				array(form_label('Address'), form_textarea(array('name'=>'address', 'id'=>'address', 'cols'=>'50', 'rows'=>'3','class'=>'form-control','placeholder'=>'Address'))),
				// array(form_label('Date of Activation'),form_input(array('name'=>'dateofactivation','id'=>'dateofactivation','size'=>'50','class'=>'form-control','placeholder'=>'YYYY-MM-DD'))),
				// array(form_label('Date of Validity'),form_input(array('name'=>'datevalid','id'=>'datevalid','size'=>'50','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Date of Activation'),'Will be activated in seven(7) <b>days</b> upon this registration'),
				array(form_label('Date of Validity'),'This Card will have one(1) <b>year</b> validity upon this registration'),
				array(form_label('Remarks', 'remarks'), form_textarea(array('name'=>'remarks', 'id'=>'remarks', 'cols'=>'50', 'rows'=>'10','class'=>'form-control','placeholder'=>'Remarks'))),
				array(),
				array('', form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);
echo $this->table->generate($inputs);
echo form_hidden('id',$id);
echo form_hidden('patient_id');
?>
<?php echo form_close(); ?>
</div>