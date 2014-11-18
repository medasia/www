<h2>Summary Result</h2>
<?php
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	if($summary)
	{
		foreach($summary as $lkey => $lvalue)
		{
			$tmpl = array(
						'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
						'table_close' => '</table>'
						);
			if($lvalue['lab_test_test'] != NULL)
			{
				foreach($lvalue['lab_test_test'] as $lrow)
				{
					@$totalLab[$lkey] += $lrow['amount'];

					$this->table->set_template($tmpl);
					$this->table->set_heading('Lab','Amount');
					$lab[$lkey] = $this->table->generate($lvalue['lab_test_test']);
				}
			}
			else
			{
				@$totalLab[$lkey] = 0.00;
				$lab[$lkey] = '';
			}

			if($lvalue['benefits_in-out_patient'])
			{
				$this->table->set_template($tmpl);

				foreach($lvalue['benefits_in-out_patient'] as $key => $row)
				{
					$benefit_names = $this->table->add_row($row['benefit_name']);
				}
				$benefit[$lkey] = $this->table->generate($benefit_names);

				foreach($lvalue['benefits_in-out_patient'] as $key => $row)
				{
					if($row['availed_amount'] != 0)
					{
						@$totalAvailed[$lkey] += $row['availed_amount'];
					}
					if($row['availed_as-charged'] != 0)
					{
						@$totalAvailed[$lkey] += $row['availed_as-charged'];
					}
				}
			}
			else
			{
				$benefit[$lkey] = $lvalue['benefit_name'];
				@$totalAvailed[$lkey] = 0.00;

				if($lvalue['benefits_others'])
				{
					@$totalOthers[$lkey] += $lvalue['benefits_others'][0]['availed_amount'];
				}
				if($lvalue['benefits_others_as_charged'])
				{
					@$totalOthers[$lkey] += $lvalue['benefits_others_as_charged'][0]['availed_amount'];
				}
			}
			@$totalAll[$lkey] = $totalLab[$lkey] + $totalAvailed[$lkey] + $totalOthers[$lkey];
		}
	}

	echo '<div class="table_scroll">';
	$template = array(
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
	$this->table->set_template($template);
	$this->table->set_heading('No.','Date','Approval Code','Patient Name','Date of Availment','Date of Received','Chief Complaint/Diagnosis','Diagnosis','Amount','Actual Amount',
		'Variance','Company Name','Insurance Name','Hospital','Doctor','Dentist','Dental Clinic','Account Name','Availment Type','Laboratory','Remarks','Remarks Claims','User','Claims Status');
	$count = 1;

	foreach($summary as $value => $key)
	{
		$variance[$value] = 0.00;
		$variance[$value] = $totalAll[$value] - $key['claims_amount'];

		if($variance[$value] <= 0.00)
		{
			$var_color = "red";
		}
		else
		{
			$var_color = "blue";
		}

		if($key['claims_status'] == 'RECEIVED')
		{
			unset($key);
		}
		else
		{
			if($key['claims_status'] == 'BILLED')
			{
				$reprint = anchor(base_url().'summary/reprintBilled/'.$key['code'].'/','Reprint Bill',array('class'=>'btn btn-danger btn-xs','target'=>'_blank'));
			}
			else
			{
				$reprint = $key['claims_status'];
			}
			$this->table->add_row($count++.'.',mdate('%M %d, %Y',mysql_to_unix($key['date_encoded'])),$key['code'],$key['patient_name'],'<font color="blue">'.mdate('%M %d, %Y %h:%i %a',mysql_to_unix($key['claims_dateofavailment'])),'<font color="blue">'.mdate('%M %d, %Y %h:%i %a',mysql_to_unix($key['claims_dateofrecieve'])),$key['chief_complaint'],'<font color="blue">'.$key['claims_diagnosis'],
					number_format($totalAll[$value],2),'<font color="blue">'.number_format($key['claims_amount'],2),'<font color="'.$var_color.'">'.number_format($variance[$value],2),$key['company_name'],$key['insurance_name'],$key['hospital_name'],'<font color="blue">'.$key['claims_doctor'],'<font color="blue">'.$key['claims_dentist'],'<font color="blue">'.$key['claims_dentalclinic'],'<font color="blue">'.$key['account_name'],$benefit[$value],$lab[$value],$key['remarks'],'<font color="blue">'.$key['claims_remarks'],$key['user'],'<font color="blue">'.$reprint);
		}
	}
	echo $this->table->generate();
	echo '</div>';
?>
<?php echo form_close();?> 