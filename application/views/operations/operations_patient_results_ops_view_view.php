<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Member Record of <?php echo $lastname.', '.$firstname.' '.$middlename;?></title>
	</head>
</html>

<h2>View record of <?php echo $lastname.', '.$firstname.' '.$middlename; ?></h2>
<?php
$template = array(
			'table_open' => '<table border="0" cellpadding="4" cellpspacing="0">',
			'table_close' => '</table>'
			);
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
$verifications = anchor_popup(base_url()."verifications/newLOA/".$id."/","Add Verification",array('class'=>'btn btn-success btn-xs'));

//PATIENT DETAILS
if($compins['company'] == '' || $compins['insurance'] == '' || is_null($compins))
{
	$compinsCI = " No assigned Company - Insurance ";
}
else
{
	$compinsCI = $compins['company']." - ".$compins['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compins['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compins['end'])).")";
}
$patient = array(
			array('',''),
			array(form_label('Last Name'),$lastname),
			array(form_label('First Name'),$firstname),
			array(form_label('Middlename'),$middlename),
			array(form_label('Company - Insurance'),$compinsCI),
			array(form_label('Date of Birth'),mdate('%M %d, %Y',mysql_to_unix($dateofbirth))),
			array(form_label('Age'),computeAge($dateofbirth)),
			array(form_label('Level/Position'),$level),
			array(form_label('Declaration Date'),mdate('%M %d, %Y',mysql_to_unix($declaration_date))),
			array(form_label('Start Date'),mdate('%M %d, %Y',mysql_to_unix($start))),
			array(form_label('End Date'),mdate('%M %d, %Y',mysql_to_unix($end))),
			array(form_label('Status'),$status),
			array(form_label('Cardholder Type'),$cardholder_type),
			array(form_label('Cardholder'),$cardholder),
			array(form_label('Remarks'),$remarks)
			);
$this->table->set_template($template);
$patient_field = form_fieldset('<b>Patients Details</b>');
$patient_field.= $this->table->generate($patient);
$patient_field.= form_fieldset_close();
$patient_input = array(array($patient_field));
echo $this->table->generate($patient_input);

//BENEFIT DETAILS
if(isset($benefit_info))
{
	$this->table->set_template($template);
	$benefit = array(
			array('',''),
			array(form_label('Company - Insurance'),$compinsCI),
			array(form_label('Benefit Set Name'),$benefit_info['benefit_set_name']),
			array(form_label('Cardholder Type'),$benefit_info['cardholder_type']),
			array(form_label('Level'),$benefit_info['level']),
			array(form_label('Maximum Benefit Limit'),'PHP. '.$benefit_info['maximum_benefit_limit']),
			array(form_label('Benefit Limit Type'),$benefit_info['benefit_limit_type']),
			array(form_label('Patient Remaining Overall MBL Balance'),'PHP. <b>'.$remaining_overall_mbl.'</b>'),
			array(form_label('Other Condition'), $benefit_info['condition_name'].'<br>'.$condition_details),
			array(form_label('Exclusion'), $benefit_info['exclusion_name'].'<br>'.$exclusion_details),
			array('',$verifications)
			);
	$benefit_field = form_fieldset('<b>Benefit Set: '.$benefit_info['benefit_set_name'].'</b>');
	$benefit_field.= $this->table->generate($benefit);
	$benefit_field.= form_fieldset_close();
	$benefit_input = array(array($benefit_field));
	echo $this->table->generate($benefit_input);
}

//AVAILMENTS
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
	$this->table->set_template($tmpl);
	$this->table->set_heading('','Approval Code','Name','Company','Insurance','Hospital Name','Hospital Branch','Chief Complaint / Diagnosis','Availment Type','Benefit Name',
					'Laboratory','Total Amount Availed','Remaining Balance From Benefits','Principal Name','Remarks','User');
	$count=1;

	foreach(@$availments as $value => $key)
	{
		$this->table->add_row($count++.".",$key['code'],$key['patient_name'],$key['company_name'],$key['insurance_name'],$key['hospital_name'],$key['branch'],$key['chief_complaint'],
			$key['availment_type'],$benefit[$value],$lab[$value],$amount[$value],$balance[$value],$key['principal_name'],$key['remarks'],$key['user']);
	}
	echo $this->table->generate();

	$totalAll = @$totalOthers + $totalAvailed;
	echo '</div>';
	echo "<table width=0px><tr><th>Overall Availed Amount: PHP</th><td align=right>".number_format($totalAll,2)."</td></tr></table>";
	echo $verifications;
}

//ADMISSION REPORT
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
			$this->table->set_template($template);
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
	$this->table->set_template($tmpl);
	$this->table->set_heading('','LOA Code','Patient Name','Company - Insurance','Hospital Name','Hospital Branch','Chief Complaint / Diagnosis',
				'Date Admitted','Physician','Specialist','Insurance Approval','History','Remarks','User','Date Updated');
	$count = 1;
	foreach($admission as $value => $key)
	{
		// $viewRecords = anchor(base_url().'verifications/viewAdmissionRecords/'.$key['code'].'/',$key['code'],array('target'=>'_blank'));
		$this->table->add_row($count++.'.',$key['code'],$key['patient_name'],$key['company_name'].' - '.$key['insurance_name'],$key['hospital_name'],
				$key['hospital_branch'], $key['chief_complaint'], mdate('%M %d, %Y',mysql_to_unix($key['date_admitted'])),$key['physician'], $spclst[$value], $insurance[$value],$key['history'],$key['remarks'], $key['user'], mdate('%M %d, %Y %h:%i %A',mysql_to_unix($key['time_generated'])));
	}
	echo $this->table->generate();
	echo '</div>';
}

if(isset($monitoring))
{
	echo '<h3>Monitoring Report</h3>';
	echo '<div class="table_scroll">';
	$this->table->set_template($template);
	$this->table->set_heading('','LOA Code','Patient Name','Company - Insurance','Hospital Name','Hospital Branch','Chief Complaint / Diagnosis',
					'Date','Time','Running Bill','Caller Name','Status','History','Time Generated/Updated','User');
	$count = 1;
	foreach($monitoring as $value => $key)
	{
		$this->table->add_row($count++.'.',$key['code'],$key['patient_name'],$key['company_name'].' - '.$key['insurance_name'],$key['hospital_name'],$key['hospital_branch'],$key['chief_complaint'],
			mdate('%M %d, %Y',mysql_to_unix($key['date'])),$key['monitoring_time'],$key['running_bill'],$key['caller_name'],$key['status'],$key['history'],mdate('%M %d, %Y %H:%i:%s',mysql_to_unix($key['time_generated'])),$key['user']);
	}
	echo $this->table->generate();
	echo '</div>';
}
?>
