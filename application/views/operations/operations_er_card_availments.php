<script>
$(document).ready(function(){
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
						$('#hospital_branch').val(data.message2);
					}
				}
			});
		}
	});
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

	jQuery.validator.addMethod('compare',function(value, element){
		var amount = $('#amount').val();
		var total_amount = value;
		var result = amount - total_amount;

		if(result < 0)
		{
			return false;
		}
		return true;
	});
	$('form').validate({
		rules: {
			hospital_name:{
				required: true
			},
			hospital_branch:{
				required: true
			},
			diagnosis:{
				required: true
			},
			availed_amount:{
				required: true,
				number: true,
				compare: true
			}
		},
		messages:{
			hospital_name:{
				required: "This field is required"
			},
			hospital_branch:{
				required: "This field is required"
			},
			diagnosis:{
				required: "This field is required"
			},
			availed_amount:{
				required: "This field is required",
				number: "Number only",
				compare: 'Card has insufficient amount'
			}
		}
	});
});
</script>
<title>Medriks - Operations</title>
<?php
	echo validation_errors();
	echo form_open('operations/registerAvailments');
	$tmpl = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($tmpl);

	$amount_form = array(
				array('Card Amount','Total Amount'),
				array(form_input(array('name'=>'amount','id'=>'amount','class'=>'form-control','disabled'=>'disabled','size'=>'20','value'=>$amount)),
					form_input(array('name'=>'availed_amount','id'=>'availed_amount','class'=>'form-control','size'=>'20','placeholder'=>'Total Amount')))
				);
	$amount_field = form_fieldset('Amount Details');
	$amount_field.= $this->table->generate($amount_form);
	$amount_field.= form_fieldset_close();

	$availments = array(
				array('',''),
				array(form_label('Patient Name'),$patient_name),
				array(form_label('Hospital'),form_input(array('name'=>'hospital_name','id'=>'hospital_name','size'=>'20','placeholder'=>'Hospital','class'=>'form-control'))),
				array(form_label('Branch'),form_input(array('name'=>'hospital_branch','id'=>'hospital_branch','size'=>'20','placeholder'=>'Branch','class'=>'form-control'))),
				array(form_label('Diagnosis'),form_input(array('name'=>'diagnosis','id'=>'diagnosis','size'=>'20','placeholder'=>'Diagnosis','class'=>'form-control'))),
				array(form_label('Physician'),form_input(array('name'=>'physician','id'=>'physician','size'=>'20','placeholder'=>'Physician','class'=>'form-control'))),
				array(form_label('Procedure'),form_textarea(array('name'=>'procedure','id'=>'procedure','cols'=>'50','rows'=>'3','placeholder'=>'Procedure','class'=>'form-control'))),
				array(form_label('Amount'),$amount_field),
				array(form_label('Remarks'),form_textarea(array('name'=>'remarks','id'=>'remarks','cols'=>'50','rows'=>'10','placeholder'=>'Remarks','class'=>'form-control'))),
				array('',form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
				);
	$availments_field = form_fieldset('<h2>Proceed Availments of Card # '.$card_number.'</h2>');
	$availments_field.= $this->table->generate($availments);
	$availments_field.= form_fieldset_close();

	$inputs = array(array($availments_field));
	echo $this->table->generate($inputs);

	echo form_hidden('id',$id);
	echo form_hidden('patient_id',$patient_id);
	echo form_close();
?>