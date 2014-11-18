<script>
$(document).ready(function()
	{
		$('#date').datepicker({format:'yyyy-mm-dd'});
		$("form").validate({
    			rules: {
    				prepared_by: {
    					required: true
    				},
    			    checked_by: {
    			        required: true
    			    }
    			},
    			messages: {
    				prepared_by: {
    					required: 'This field is required'
    				},
    			    checked_by: {
    			        required: "This field is required"
    			    }
    			}
			});
	});
</script>
<h3>Kindly fill-up this form</h3>
<?php echo validation_errors();?>
<?php echo form_open('claims/printBilling');?>
<?php
	date_default_timezone_set('Asia/Manila');
	$date = date('Y-m-d');
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);

	$inputs = array(
			array('',''),
			array(form_label('Date:'),mdate('%M %d, %Y',mysql_to_unix($date))),
			array(form_label('Attachments:'),form_textarea(array('name'=>'attachments','cols'=>'25','rows'=>'10','id'=>'attachments','class'=>'form-control'))),
			array(form_label('Prepared By:'),form_input(array('name'=>'prepared_by','id'=>'prepared_by','size'=>'20','class'=>'form-control','placeholder'=>'Prepared By'))),
			array(form_label('Checked By:'),form_input(array('name'=>'checked_by','id'=>'checked_by','size'=>'20','class'=>'form-control','placeholder'=>'Checked By'))),
			array('',form_submit(array('name'=>'submit','value'=>'Print','class'=>'btn btn-sm btn-success')))
				);
	echo $this->table->generate($inputs);

	echo form_hidden('availments',$availments);
	echo form_hidden('doctor',$doctor);
	echo form_hidden('plan',$plan);
	echo form_hidden('hospital_bills',$hospital_bills);
	echo form_hidden('insurance',$insurance);
	echo form_hidden('diagnosis',$diagnosis);
	echo form_hidden('date',$date);

	echo '<h2>Preview sample details to be print below</h2>';
	echo '<hr>';
?>