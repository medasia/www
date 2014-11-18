<?php
if($this->session->flashdata('result') != '')
{
	echo $this->session->flashdata('result');
}

//DISPLAY UNREGISTERED BENEFITS
$insufficient_benefits = $this->session->flashdata('insufficient_benefits');
if(!empty($insufficient_benefits))
{
	echo "<br>But some Benefits are not successfully registered.<br>";
	echo "The user might reached/entered the for ";

	foreach($this->session->flashdata('insufficient_benefits') as $key => $value)
	{
		echo '<b>'.$value.',</b> ';
	}
	echo "Benefit/s.";
}
?>
<html>
<head>
	<title>Verifications</title>
</head>

<h1>Verifications</h1>
<?php
	if(empty($result))
	{
		echo "<h2>No record to display or created for today.</h2>";
	}
	if(@$result)
	{
		echo "<div class='table_scroll'>";
		$totalAll = 0.0;
		foreach(@$result as $lkey => $lvalue)
		{
			$template = array(
						'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">',
						'table_close' => '</table>'
						);
			$this->table->set_template($template);
			$doctors_fee = 0.00;
			$physician = $this->table->add_row($lvalue['physician'],'<b>PHP.</b> '.$lvalue['physician_fee']);
			$doctors_fee+=$lvalue['physician_fee'];
			if($lvalue['specialist'] != NULL)
			{
				foreach($lvalue['specialist'] as $key => $row)
				{
					$physician.= $this->table->add_row($row['specialist_name'],'<b>PHP.</b> '.$row['specialist_fee']);
					$doctors_fee+=$row['specialist_fee'];
				}
			}
			$physician.= $this->table->add_row('<b>Total Doctors Fee</b>','<b>PHP. '.number_format($doctors_fee,2).'</b>');

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
			$physician_table[$lkey] = $this->table->generate($physician);

			$this->table->set_template($template);
			foreach($lvalue['diagnosis'] as $dkey => $dvalue)
			{
				$diagnosis_names = $this->table->add_row($dvalue['diagnosis']);
			}
			$diagnosis[$lkey] = $this->table->generate($diagnosis_names);

			if($lvalue['benefits_in-out_patient'])
			{
				$this->table->set_template($template);

				foreach($lvalue['benefits_in-out_patient'] as $key => $row)
				{
					$benefit_name = $this->table->add_row($row['benefit_name']);
				}
				$benefit[$lkey] = $this->table->generate($benefit_name);

				foreach($lvalue['benefits_in-out_patient'] as $key => $row)
				{
					if($row['availed_amount'] != 0)
					{
						$totalAvailed += $row['availed_amount'];
						@$availed[$lkey] += $row['availed_amount'];
						$benefit_amount = $this->table->add_row('<b>PHP </b>'.number_format($row['availed_amount'],2));
					}
					if($row['availed_as-charged'] != 0)
					{
						$totalAvailed += $row['availed_as-charged'];
						@$availed[$lkey] += $row['availed_as-charged'];
						$bnefit_amount = $this->table->add_row('<b>PHP </b>'.number_format($row['availed_as-charged'],2));
					}
				}
				@$totalAmount[$lkey] += $availed[$lkey];
				$benefit_amounts = $this->table->add_row('<b>Total Amount PHP</b> '.number_format($totalAmount[$lkey],2));
				$amount[$lkey] = $this->table->generate($benefit_amounts);

				foreach($lvalue['benefits_in-out_patient'] as $key => $row)
				{
					$last_amount = $row['availed_amount'] > 0 && $row['remaining_amount'] == 0;
					$last_asCharged = $row['availed_as-charged'] > 0 && $row['remaining_as-charged'] == 0;
					$last_day = $row['availed_days'] > 0 && $row['remaining_days'] == 0;

					//AMOUNT VALUE
					if($last_amount || $row['remaining_amount'] != 0)
					{
						if($last_amount || $last_day)
						{
							$reached_limit = "<br><b>(Benefit reached the limit)";
						}
						else
						{
							$reached_limit = '';
						}
						$benefit_balance = $this->table->add_row('<b>Days: </b>'.$row['remaining_days'].$reached_limit,'<b>PHP </b>'.number_format($row['remaining_amount'],2).$reached_limit);
					}
					if($last_asCharged || $row['remaining_as-charged'] != 0)
					{
						if($last_asCharged || $last_day)
						{
							$reached_limit = "<br><b>(Benefit reached the limit)";
						}
						else
						{
							$reached_limit = '';
						}
						$benefit_balance = $this->table->add_row('<b>Days:</b> '.$row['remaining_days'].$reached_limit,'<b>PHP </b>'.number_format($row['remaining_as-charged'],2).$reached_limit);
					}
				}
				$balance[$lkey] = $this->table->generate($benefit_balance);
			}
			else
			{
				//BENEFIT NAME
				$benefit[$lkey] = $lvalue['benefit_name'];

				//BENEFIT AMOUNT
				if(isset($total[$lkey]))
				{
					$amount[$lkey] = "<b>PHP</b> ".number_format($total[$lkey],2);
				}
				if(isset($lvalue['benefits_others'][0]['availed_amount']))
				{
					$amount[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_others'][0]['availed_amount'],2);
					$totalOthers += $lvalue['benefits_others'][0]['availed_amount'];
				}
				if(isset($lvalue['benefits_others_as_charged'][0]['availed_amount']))
				{
					$amount[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_others_as_charged'][0]['availed_amount'],2);
					$totalOthers += $lvalue['benefits_others_as_charged'][0]['availed_amount'];
				}

				//BENEFIT REMAINING BALANCE
				if(isset($lvalue['benefits_laboratory'][0]['remaining_balance']))
				{
					$balance[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_laboratory'][0]['remaining_balance'],2);
				}
				elseif(isset($lvalue['benefits_others'][0]['remaining_amount']))
				{
					$balance[$lkey] = "<b>Days: </b>".$lvalue['benefits_others'][0]['remaining_days']."<b> , PHP </b>".number_format($lvalue['benefits_others'][0]['remaining_amount']);
				}
				elseif(isset($lvalue['benefits_others_as_charged'][0]['remaining_mbl_balance']))
				{
					$balance[$lkey] = "<b>PHP </b>".number_format($lvalue['benefits_others_as_charged'][0]['remaining_mbl_balance'],2);
				}
			}
		}

		$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

				'heading_row_start'   => '<tr>',
				'heading_row_end'     => '</tr>',
				'heading_cell_start'  => '<th>',
				'heading_cell_end'    => '</th>',

				'row_start'           => '<tr>',
				'row_end'             => '</tr>',
				'cell_start'          => '<td>',
				'cell_end'            => '</td>',

				'row_alt_start'       => '<tr>',
				'row_alt_end'         => '</tr>',
				'cell_alt_start'      => '<td>',
				'cell_alt_end'        => '</td>',

				'table_close'         => '</table>'
				);

		$this->table->set_template($tmpl);
		$this->table->set_heading('','Approval Code','Name','Company','Insurance','Hospital Name', 'Hospital Branch','Attending Physician - Physician Fee', 'Chief Complaint/Diagnosis','Availment Type','Benefit Name','Illness (if Benefit is "Per Illness" type)','Laboratory','Total Amount Availed','Remaining Balance from Benefits', 'Principal Name','Company - Insurance Notes','Remarks','User','');
		$count=1;

		foreach(@$result as $value => $key)
		{
			// $id = $key['id'];
			// $edit = anchor(base_url()."verifications/edit/".$key['id']."/","Edit");

			$delete = anchor(base_url()."verifications/delete/".$key['id']."/","Delete",array('class'=>'btn btn-danger btn-xs'));
			$print = anchor(base_url().'verifications/printLOA/'.$key['code'].'/','Print',array('class'=>'btn btn-danger btn-xs','target'=>'_blank'));
			$this->table->add_row($count++.".".$print,$key['code'],$key['patient_name'],$key['company_name'],$key['insurance_name'],$key['hospital_name'],$key['hospital_branch'],$physician_table[$value],$diagnosis[$value],
			$key['availment_type'],$benefit[$value],$key['illness'][0]['illness'],$lab[$value],$amount[$value],$balance[$value],$key['principal_name'],$key['compins_notes'][0]['notes'],$key['remarks'],$key['user'],/**$edit."-".**/$print);
		}
		echo $this->table->generate();

		$totalAll = $totalOthers + $totalAvailed;
		echo "<table width=0px><tr><th>Overall Availed Amount: PHP</th><td align=right>".number_format($totalAll,2)."</td></tr></table>";
		echo '</div>';
	}	
?>