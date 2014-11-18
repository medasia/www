<script>
$(document).ready(function() {
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type 	: 'textarea',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'availments_test', key: <?=$id?>}
	});

	$('.editable2').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type      : 'text',
		// datepicker: {
		// 	dateFormat: 'yy-mm-dd'
		// },
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'Ok',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		data      : 'YYYY-MM-DD',
		submitdata : {table: 'availments_test', key: <?=$id?>}
	});

	$('.editable3').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type : 'text',
		indicator : 'Saving...',
		cancel : 'Cancel',
		submit : 'Ok',
		tooltip : 'Click to edit...',
		onblur : 'cancel',
		data : '0.00',
		submitdata : {table: 'availments_test', key: <?=$id?>},
		callback : function(setting) {
			window.location.reload();
		}
	});

	$('.editable4').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/hospital_account/>',
		type      : 'select',
		indicator : 'Saving...',
		cancel : 'Cancel',
		submit : 'Ok',
		tooltip : 'Click to edit...',
		onblur : 'cancel',
		submitdata : {table: 'availments_test', key: <?=$id?>}
	});
});
</script>
<html>
	<head>
		<title>Claims - Received Record</title>
	</head>
<h2>View Received Record of <?php echo $patient_name; ?></h2>
<?php
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$totalAll = 0.00;
	if($lab_test_test)
	{
		foreach($lab_test_test as $lrow)
		{
			@$totalLab += $lrow['amount'];

			$this->table->set_template($tmpl);
			$this->table->set_heading('Lab','Amount');
			$lab = $this->table->generate($lab_test_test);
		}
	}
	else
	{
		$lab = '';
	}

	$this->table->set_template($tmpl);
	$doctor_fee = 0.00;
	$physician = $this->table->add_row($physician,'<b>PHP. </b>'.$physician_fee);
	$doctor_fee+=$physician_fee;
	if($specialists)
	{
		foreach($specialists as $key => $value)
		{
			$physician.= $this->table->add_row($value['specialist_name'],'<b>PHP. </b>'.$value['specialist_fee']);
			$doctor_fee+=$value['specialist_fee'];
		}
	}
	$physician.= $this->table->add_row('<b>Total Doctors Fee:</b>','<b>PHP. '.number_format($doctor_fee,2).'</b>');
	$physician_table = $this->table->generate($physician);

	$this->table->set_template($tmpl);
	foreach($diagnosis as $key => $value)
	{
		$diagnosis_name = $this->table->add_row($value['diagnosis']);
	}
	$dgnsis = $this->table->generate($diagnosis_name);

	if($benefits_in_out_patient)
	{
		$this->table->set_template($tmpl);

		foreach($benefits_in_out_patient as $key => $row)
		{
			$benefit_name = $this->table->add_row($row['benefit_name']);
		}
		$benefit = $this->table->generate($benefit_name);

		foreach($benefits_in_out_patient as $key => $row)
		{
			if($row['availed_amount'] != 0)
			{
				@$totalAvailed += $row['availed_amount'];
			}
			if($row['availed_as-charged'] != 0)
			{
				@$totalAvailed += $row['availed_as-charged'];
			}
		}
	}
	else
	{
		$benefit = $benefit_name;

		if($benefits_others)
		{
			@$totalOthers += $benefits_others[0]['availed_amount'];
		}
		if($benefits_others_as_charged)
		{
			@$totalOthers += $benefits_others_as_charged[0]['availed_amount'];
		}
	}

	$totalAll = @$totalLab + @$totalAvailed + @$totalOthers;

	$variance = 0.00;
	$variance = $totalAll - $claims_amount;
	if($variance <= 0.00)
	{
		$var_color = "red";
	}
	else
	{
		$var_color = "blue";
	}

	$template = array(
			'table_open' => '<table border="1" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$this->table->set_template($template);
	echo validation_errors();
	echo form_open('claims/received');
	$this->table->add_row('<b>Approval Code</b>',$code);
	$this->table->add_row('<b>Date</b>',mdate('%M %d, %Y',mysql_to_unix($date_encoded)));
	$this->table->add_row('<b>Company Name</b>',$company_name);
	$this->table->add_row('<b>Insurance Name</b>',$insurance_name);
	$this->table->add_row('<b>Hospital Name',$hospital_name);
	$this->table->add_row('<b>Chief Complaint / Diagnosis</b>',$dgnsis);
	$this->table->add_row('<b>Physician / Specialists (with Fees)',$physician_table);
	$this->table->add_row('<b>Availment Types</b>',$benefit);
	$this->table->add_row('<b>Laboratory</b>',$lab);
	$this->table->add_row('<b>Total Amount from Availment Types (and Laboratory, if exists)</b>','<b>PHP</b> '.number_format($totalAll,2));
	$this->table->add_row('<b>Remarks</b>',$remarks);
	$this->table->add_row('<b>User</b>',$user);
	$this->table->add_row('<b><font color="blue">Details to be filled/edit by Claims</b>','');
	$this->table->add_row('<b>Date of Availment</b>','<div class="editable2" id="claims_dateofavailment"><font color="blue">'.mdate('%M %d, %Y',mysql_to_unix($claims_dateofavailment)).'</div>');
	$this->table->add_row('<b>Date of Receive</b>','<div class="editable2" id="claims_dateofrecieve"><font color="blue">'.mdate('%M %d, %Y',mysql_to_unix($claims_dateofrecieve)).'</div>');
	$this->table->add_row('<b>Diagnosis</b>','<font color="blue"><div class="editable" id="claims_diagnosis">'.$claims_diagnosis.'</div>');
	$this->table->add_row('<b>Actual Amount</b>','<div class="editable3" id="claims_amount">'.'<b>PHP</b> <font color="blue">'.number_format($claims_amount,2).'</div>');
	$this->table->add_row('<b>Variance</b>','<b>PHP</b> <font color="'.$var_color.'">'.number_format($variance,2));
	$this->table->add_row('<b>Doctor</b>','<font color="blue"><div class="editable" id="claims_doctor">'.$claims_doctor.'</div>');
	$this->table->add_row('<b>Account Name</b>','<font color="blue"><div class="editable4" id="account_name">'.$account_name.'</div');
	$this->table->add_row('<b>Remarks Claims</b>','<font color="blue"><div class="editable" id="claims_remarks">'.$claims_remarks.'</div>');
	$this->table->add_row('',form_submit(array('name'=>'submit','value'=>'Received','class'=>'btn btn-success')));

	echo $this->table->generate();
	echo form_hidden('sel_multi[]',$id);
	echo form_close();
?>
<br><br><br>