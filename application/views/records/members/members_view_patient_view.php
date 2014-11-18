<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Member Record of <?php echo $lastname.', '.$firstname.' '.$middlename;?></title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
<script>
$(document).ready(function() {
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'patient', key: <?=$id?>}
	});

	$('.editable2').editable('<?=base_url()?>utils/ajaxeditinplace', {
		type      : 'datepicker',
		datepicker: {
			format: 'yyyy-mm-dd'
		},
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		placeholder: 'YYYY-MM-DD',
		submitdata : {table: 'patient', key: <?=$id?>},
		callback  : function(value, settings) {
			window.location.reload(true);
		}
	});

	$('.editable3').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/status/<?=$status?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'patient', key: <?=$id?>}
	});

	$('.editable4').editable('<?=base_url()?>utils/ajaxeditinplace', {
		loadurl   : '<?=base_url()?>utils/ajaxeditinplace/cardholder_type/<?=$cardholder_type?>',
		type      : 'select',
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'patient', key: <?=$id?>},
		callback  : function(value, settings) {
			alterCardholder(value);
			window.location.reload(true);
		}
	});
	alterCardholder('<?=$cardholder_type?>');

	// JEDITABLE WITH AUTOCOMPLETE
	$.editable.addInputType('autocomplete',
	{
		element : $.editable.types.text.element,
		plugin : function(settings, original)
		{
			$('input', this).autocomplete(
			{
				minLength: 1,
				source: function(req, add)
				{
					$.ajax(
					{
						url: '<?=base_url()?>utils/autocomplete/from/cardholder', //Controller where search is performed
						dataType: 'json',
						type: 'POST',
						data: req,
						success: function(data)
						{
							if(data.response =='true')
							{
								add(data.message);
							}
						}
					});
				}
			});
		}
	});

	// JEDITABLE WITH AUTOCOMPLETE

	function alterCardholder(val) {
		switch(val) {
			case 'PRINCIPAL':
				// hide box
				$('#cardholder').hide();
			break;
			case 'DEPENDENT':
				// show box
				$('#cardholder').show();
			break;
		}
	}
});
</script>
</html>

<h2>View record of <?php echo $lastname.', '.$firstname.' '.$middlename; ?></h2>
<?php
$currentDate = date('Y-m-d');
if($status == 'ACTIVE')
{
	$newdate = strtotime('-7 day',strtotime($end));
	$newdate = date('Y-m-d',$newdate);
	$expires = (strtotime($end) - strtotime(date('Y-m-d'))) / (60*60*24);
	if($expires > 1)
	{
		$day = " days";
	}
	else
	{
		$day = " day";
	}
	if($expires < 0)
	{
		$id = $id;
		$field = 'status';
		$data = "EXPIRED";
		$status = status_update('patient',$field,$data,$id);
	}

	if($newdate <= $currentDate)
	{
		$status = $status.' will expire in '.$expires.$day.'.';
	}
}

$actions = anchor_popup(base_url()."verifications/newLOA/".$id."/","Add Verification",array('class'=>'btn btn-success btn-xs'));
if($compins['company'] == "" || $compins['insurance'] == "" || is_null($compins))
{
	$compinsCI = " No assigned Company - Insurance ";
}
else
{
	$compinsCI = anchor(base_url()."records/compins/members/".$compins['id']."/", $compins['company']." - ".$compins['insurance']
				." (".mdate('%M %d, %Y', mysql_to_unix($compins['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compins['end'])).")");
}

$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',
				'table_close'         => '</table>'
				);
$this->table->set_template($tmpl);
$this->table->add_row('Lastname', '<div class="editable" id="lastname">'.$lastname.'</div>');
$this->table->add_row('Firstname', '<div class="editable" id="firstname">'.$firstname.'</div>');
$this->table->add_row('Middlename', '<div class="editable" id="middlename">'.$middlename.'</div>');
$this->table->add_row('Company - Insurance', $compinsCI);
$this->table->add_row('Date of Birth', '<div class="editable2" id="dateofbirth">'.mdate('%M %d, %Y', mysql_to_unix($dateofbirth)).'</div>');
$this->table->add_row('Age', computeAge($dateofbirth)." (updated by Date of Birth)");
$this->table->add_row('Level/Position', '<div class="editable" id="level">'.$level.'</div>');
$this->table->add_row('Declaration date', '<div class="editable2" id="declaration_date">'.mdate('%M %d, %Y', mysql_to_unix($declaration_date)).'</div>');
$this->table->add_row('Start date', '<div class="editable2" id="start">'.mdate('%M %d, %Y', mysql_to_unix($start)).'</div>');
$this->table->add_row('End date', '<div class="editable2" id="end">'.mdate('%M %d, %Y', mysql_to_unix($end)).'</div>');
$this->table->add_row('Status', '<div class="editable3" id="status">'.$status.'</div>');
$this->table->add_row('Cardholder Type', '<div class="editable4" id="cardholder_type">'.$cardholder_type.'</div>');
$this->table->add_row('Cardholder', '<div class="editable" id="cardholder">'.$cardholder.'</div>');
$this->table->add_row('Remarks', '<div class="editable" id="remarks">'.$remarks.'</div>');
$this->table->add_row(anchor(base_url()."records/members/delete/".$id."/", "Delete Patient", array('onClick'=>"return confirm('Are you sure you want to delete this record?')",'class'=>'btn btn-danger')),'User: '.$user);

echo $this->table->generate();

if(isset($benefit_details))
{
	echo "<h2>Benefit Set: ".$benefit_details['benefit_set_name']."</h2>";
	$ben_tmpl = array(
				'table_open' => '<table border="0" cellspacing="0" cellpadding="4">',
				'table_close' => '</table>',
				);
	$this->table->set_template($ben_tmpl);

	if($condition_details['filename'])
	{
		$condition_file = anchor(base_url().'records/uphist/view_pdf/benefit_set_condition/'.$condition_details['filename'].'/','View attached PDF',array('class'=>'btn btn-info btn-sm'));
	}
	else
	{
		$condition_file = '';
	}

	if($exclusion_details['filename'])
	{
		$exclusion_file = anchor(base_url().'records/uphist/view_pdf/benefit_set_exclusion/'.$exclusion_details['filename'].'/','View attached PDF',array('class'=>'btn btn-sm btn-info'));
	}
	else
	{
		$exclusion_file = '';
	}

	$input = array(
				array('',''),
				array(form_label('Company Name: '), $compinsCI),
				array(form_label('Benefit Set Name: '), $benefit_details['benefit_set_name']),
				array(form_label('Cardholder Type: '), $benefit_details['cardholder_type']),
				array(form_label('Level: '), $benefit_details['level']),
				array(form_label('Maximum Benefit Limit: '),'<b>Php. '.number_format($benefit_details['maximum_benefit_limit'],2)),
				array(form_label('Patient Remaining Overall MBL Limit'),'<b>PHP. '.number_format($remaining_overall_mbl,2)),
				array(form_label('Other Conditions:'), '<b>'.$benefit_details['condition_name'].'</b><br>'.$condition_details['condition_details']),
				array('',$condition_file),
				array(form_label('Exclusion:'),'<b>'.$benefit_details['exclusion_name'].'</b><br>'.$exclusion_details['exclusion_details']),
				array('',$exclusion_file),
				array(form_label(''),$actions)
				);
	echo $this->table->generate($input);
}

if(isset($availments))
{
	echo "<h3>Availed LOA / Benefit Details</h3>";
	$totalAll = 0.0;
	foreach(@$availments as $lkey => $lvalue)
	{
		$template = array(
						'table_open' => '<table border="0" cellpadding="4 cellspacing="0" class="table table-bordered">',
						'table_close' => '</table>'
						);
		
		if($lvalue['lab_test_test'] != NULL)
		{
			$total[] = 0.00;
			foreach($lvalue['lab_test_test'] as $lrow)
			{
				// var_dump($lrow['amount']);
				$total[$lkey] += $lrow['amount'];
				$totalLab += $lrow['amount'];

				$this->table->set_template($template);
				$this->table->set_heading('Lab','Amount');
				$lab[$lkey] = $this->table->generate($lvalue['lab_test_test']);
			}
				// var_dump($lkey);
				// var_dump($lvalue['lab_test_test']);
				// var_dump($totalAll);
		}
		else
		{
			$lab[$lkey] = 'No applicable Laboratory';
		}

		if($lvalue['benefits_in-out_patient'])
		{
			$this->table->set_template($template);

			foreach($lvalue['benefits_in-out_patient'] as $key => $row)
			{
				$benefit_names = $this->table->add_row($row['benefit_name']);
			}
			$benefit[$lkey] = $this->table->generate($benefit_names);

			foreach($lvalue['benefits_in-out_patient'] as $key => $row)
			{
				if($row['availed_amount'] != 0)
				{
					@$totalAvailed += $row['availed_amount'];
					@$availed[$lkey] += $row['availed_amount'];
					$benefit_amounts = $this->table->add_row('<b>PHP</b> '.number_format($row['availed_amount'],2));
				}
				if($row['availed_as-charged'] != 0)
				{
					@$totalAvailed += $row['availed_as-charged'];
					@$availed[$lkey] += $row['availed_as-charged'];
					$benefit_amounts = $this->table->add_row('<b>PHP</b> '.number_format($row['availed_as-charged'],2));
				}
			}
			@$totalAmount[$lkey] += @$availed[$lkey];
			$benefit_amounts = $this->table->add_row('<b>Total Amount PHP</b> '.number_format($totalAmount[$lkey],2));
			$amount[$lkey] = $this->table->generate($benefit_amounts);

			foreach($lvalue['benefits_in-out_patient'] as $key => $row)
			{
				$last_amount = $row['availed_amount'] > 0 && $row['remaining_amount'] == 0;
				$last_asCharged = $row['availed_as-charged'] > 0 && $row['remaining_as-charged'] == 0;

				//AMOUNT VALUE
				if($last_amount || $row['remaining_amount'] != 0)
				{
					if($last_amount)
					{
						$reached_limit = "<br>(Benefit reached the limit)";
					}
					else
					{
						$reached_limit = '';
					}
					$benefit_balance = $this->table->add_row('<b>PHP</b> '.number_format($row['remaining_amount'],2).$reached_limit);
				}
				//AS CHARGED VALUE
				if($last_asCharged || $row['remaining_as-charged'] != 0)
				{
					if($last_asCharged)
					{
						$reached_limit = "<br><b>(Benefit reached the limit)";
					}
					else
					{
						$reached_limit = '';
					}
					$benefit_balance = $this->table->add_row('<b>PHP</b> '.number_format($row['remaining_as-charged'],2).$reached_limit);
				}
			}
			$balance[$lkey] = $this->table->generate($benefit_balance);
			// var_dump($balance[$lkey]);
		}
		else
		{
			//BENEFIT NAME
			$benefit[$lkey] = $lvalue['benefit_name'];

			//BENEFIT AMOUNT
			if(isset($total[$lkey]))
			{
				$amount[$lkey] = "<b>PHP </b>".number_format($total[$lkey],2); 
			}
			if(isset($lvalue['benefits_others'][0]['availed_amount']))
			{
				$amount[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_others'][0]['availed_amount'],2);
				$totalOthers += $lvalue['benefits_others'][0]['availed_amount'];
			}
			if(isset($lvalue['benefits_others_as_charged'][0]['availed_amount']))
			{
				$amount[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_others_as_charged'][0]['availed_amount'],2);
				@$totalOthers += $lvalue['benefits_others_as_charged'][0]['availed_amount'];
			}

			//BENEFIT REMAINING BALANCE
			if(isset($lvalue['benefits_laboratory'][0]['remaining_balance']))
			{
				$balance[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_laboratory'][0]['remaining_balance'],2);
			}
			elseif(isset($lvalue['benefits_others'][0]['remaining_amount']))
			{
				$balance[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_others'][0]['remaining_amount'],2);
			}
			elseif(isset($lvalue['benefits_others_as_charged'][0]['remaining_mbl_balance']))
			{
				$balance[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_others_as_charged'][0]['remaining_mbl_balance'],2);
			}
		}
	}

	echo '<div class="table_scroll">';
	$tmpl = array(
			'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',
			
			'heading_row_start' => '<tr>',
			'heading_row_end' => '</tr>',
			'heading_cell_start' => '<th>',
			'heading_cell_end' => '</th>',

			'row_start' => '<tr>',
			'row_end' => '</tr>',
			'cell_start' => '<td>',
			'cell_end' => '</td>',

			'row_alt_start' => '<tr>',
			'row_alt_end' => '</tr>',
			'cell_alt_start' => '<td>',
			'cell_alt_end' => '</td>',

			'table_close' => '</table>'
				);

	$this->table->set_template($tmpl);
	$this->table->set_heading('','Approval Code','Name','Company','Insurance','Hospital Name','Hospital Branch','Chief Complaint / Diagnosis','Availment Type','Benefit Name',
					'Laboratory','Total Amount Availed','Remaining Balance From Benefits','Principal Name','Remarks','User','');
	$count=1;

	foreach(@$availments as $value => $key)
	{
		$delete = anchor(base_url()."verifications/delete/".$key['id']."/","Delete",array('class'=>'btn btn-danger btn-xs'));
		$this->table->add_row($count++.".",$key['code'],$key['patient_name'],$key['company_name'],$key['insurance_name'],$key['hospital_name'],$key['branch'],$key['chief_complaint'],
			$key['availment_type'],$benefit[$value],$lab[$value],$amount[$value],$balance[$value],$key['principal_name'], $key['user'],$delete);
	}
	echo $this->table->generate();

	$totalAll = @$totalOthers + $totalAvailed;
	echo '</div>';
	echo "<table width=0px><tr><th>Overall Availed Amount: PHP</th><td align=right>".number_format($totalAll,2)."</td></tr></table>";
	echo $actions;
}

if(isset($admission))
{
	echo '<h3>Admission Report</h3>';
	foreach($admission as $ivalue => $ikey)
	{
		if(($ikey['dateofemail'] != '0000-00-00') && !is_null($ikey['timefrom']) && !is_null($ikey['timeto'])
		&& !is_null($ikey['approved']) && !is_null($ikey['declined']))
		{
			$template = array(
						'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">',
						'table_close' => '</table>'
						);
			$this->table->set_template($template);
			$this->table->set_heading('Date of Email','Time From','Time To','Approval By', 'Declined By');
			$this->table->add_row($ikey['dateofemail'],$ikey['timefrom'],$ikey['timeto'],$ikey['approved'],$ikey['declined']);
			$insurance[$ivalue] = $this->table->generate();
		}
		else
		{
			$insurance[$ivalue] = 'No Insurance Approval';
		}

		if(isset($ikey['specialist']))
		{
			$stmpl = array(
						'table_open' => '<table border="0" cellspacing="0" cellpadding="0">',
						'table_close' => '</table>'
						);
			$this->table->set_template($stmpl);
			foreach($ikey['specialist'] as $svalue => $skey)
			{
				$specialist = $this->table->add_row($skey['specialist_name']);
			}
			$spclst[$ivalue] = $this->table->generate($specialist);
		}
		else
		{
			$spclst[$ivalue] = 'No Specialist Assigned';
		}
	}

	echo '<div class="table_scroll">';
	$tmpl = array(
				'table_open'	=> '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

				'heading_row_start' => '<tr>',
				'heading_row_end' => '</tr>',
				'heading_cell_start' => '<th>',
				'heading_cell_end' => '</th>',

				'row_start' => '<tr>',
				'row_end' => '</tr>',
				'cell_start' => '<td>',
				'cell_end' => '</td>',
				
				'row_alt_start' => '<tr>',
				'row_alt_end' => '</tr>',
				'cell_alt_start' => '<td>',
				'cell_alt_end' => '</td>',

				'table_close' => '</table>'
				);
	$this->table->set_template($tmpl);
	$this->table->set_heading('','LOA Code','Patient Name','Company - Insurance','Hospital Name','Hospital Branch','Chief Complaint / Diagnosis',
					'Date Admitted','Physician','Specialist','Insurance Approval','History','Remarks','User','Date Updated');
	$count = 1;
	foreach($admission as $value => $key)
	{
		$viewRecords = anchor(base_url().'verifications/viewAdmissionRecords/'.$key['code'].'/',$key['code'],array('target'=>'_blank'));
		$this->table->add_row($count++.'.',$viewRecords,$key['patient_name'],$key['company_name'].' - '.$key['insurance_name'],$key['hospital_name'],
				$key['hospital_branch'], $key['chief_complaint'], mdate('%M %d, %Y',mysql_to_unix($key['date_admitted'])),$key['physician'], $spclst[$value], $insurance[$value],$key['history'],$key['remarks'], $key['user'], mdate('%M %d, %Y %h:%i %A',mysql_to_unix($key['time_generated'])));
	}
	echo $this->table->generate();
	echo '</div>';
}

if(isset($monitoring))
{
	echo '<h3>Monitoring Report</h3>';
	echo '<div class="table_scroll">';
	$tmpl = array(
				'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

				'heading_row_start' => '<tr>',
				'heading_row_end' => '</tr>',
				'heading_cell_start' => '<th>',
				'heading_cell_end' => '</th>',

				'row_start' => '<tr>',
				'row_end' => '</tr>',
				'cell_start' => '<td>',
				'cell_end' => '</td>',

				'row_alt_start' => '<tr>',
				'row_alt_end' => '</tr>',
				'cell_alt_start' => '<td>',
				'cell_alt_end' => '</td>',

				'table_close' => '</table>'
				);
	$this->table->set_template($tmpl);
	$this->table->set_heading('','LOA Code','Patient Name','Company - Insurance','Hospital Name','Hospital Branch', 'Chief Complaint / Diagnosis',
					'Date','Time','Running Bill','Caller Name','Status','History','Time Generated/Updated','User');
	$count = 1;

	foreach($monitoring as $value => $key)
	{
		$viewRecords = anchor(base_url().'verifications/viewMonitoringRecords/'.$key['code'].'/',$key['code'],array('target'=>'_blank'));
		$this->table->add_row($count++.'.',$viewRecords,$key['patient_name'],$key['company_name'].' - '.$key['insurance_name'],$key['hospital_name'],$key['hospital_branch'],
			$key['chief_complaint'], mdate('%M %d, %Y',mysql_to_unix($key['date'])),$key['monitoring_time'],$key['running_bill'],$key['caller_name'],$key['status'],$key['history'],mdate('%M %d, %Y %H:%i %A', mysql_to_unix($key['time_generated'])),$key['user']);
	}
	echo $this->table->generate();
	echo '</div>';
}
?>