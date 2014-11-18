<script>
$(document).ready(function() {
	$(".select_all").click(function()
	{
		var checked_status = this.checked;
		$(".sel_multi").each(function()
		{
			this.checked = checked_status;
		});
	});
});
</script>
<?php
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);

	if($billing)
	{
		foreach($billing as $lkey => $lvalue)
		{
			$tmpl = array(
						'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">',
						'table_close' => '</table>'
						);

			$this->table->set_template($tmpl);
			$doctors_fee = 0.00;
			$physician = $this->table->add_row($lvalue['physician'],$lvalue['physician_fee']);
			$doctors_fee+= $lvalue['physician_fee'];
			if($lvalue['specialist'] != NULL)
			{
				foreach($lvalue['specialist'] as $key => $row)
				{
					$physician.= $this->table->add_row($row['specialist_name'],$row['specialist_fee']);
					$doctors_fee+=$row['specialist_fee'];
				}
			}
			$physician.= $this->table->add_row('<b>Total Doctors Fee: </b>','<b>PHP. '.number_format($doctors_fee,2).'</b>');
			$physician_table[$lkey] = $this->table->generate($physician);
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

			$this->table->set_template($tmpl);
			foreach($lvalue['diagnosis'] as $key => $value)
			{
				$diagnosis_name = $this->table->add_row($value['diagnosis']);
			}
			$diagnosis[$lkey] = $this->table->generate($diagnosis_name);

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

	if($billing == FALSE)
	{
		echo '<h2>No Records Found!</h2>';
	}
	else
	{
		echo '<div class="table_scroll">';
		$template = array(
				'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-hover table-bordered">',

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
		$this->table->set_heading('No.','Date','Approval Code','Patient Name','Company Name','Insurance Name','Availment Type','Hospital','Doctor','Date of Availment','Date of Received','Chief Complaint/Diagnosis','Diagnosis','Amount','Availed Benefit Names','Laboratory','Actual Amount',
			'Variance','Dentist','Dental Clinic','Account Name','Remarks','Remarks Claims','User','Claims User','');
		$count = 1;

		foreach($billing as $value => $key)
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

			$bill = anchor(base_url().'claims/patientBilling/'.$key['id'].'/','Bill',array('class'=>'btn btn-danger btn-xs','target'=>'_blank'));
			$this->table->add_row($count++.'.'.$bill,mdate('%M %d, %Y',mysql_to_unix($key['date_encoded'])),$key['code'],$key['patient_name'],$key['company_name'],$key['insurance_name'],$key['availment_type'],$key['hospital_name'],$physician_table[$value],'<font color="blue">'.mdate('%M %d, %Y %h:%i %a',mysql_to_unix($key['claims_dateofavailment'])),'<font color="blue">'.mdate('%M %d, %Y %h:%i %a',mysql_to_unix($key['claims_dateofrecieve'])),$diagnosis[$value],'<font color="blue">'.$key['claims_diagnosis'],$benefit[$value],$lab[$value],
					number_format($totalAll[$value],2),'<font color="blue">'.number_format($key['claims_amount'],2),'<font color="'.$var_color.'">'.number_format($variance[$value],2),'<font color="blue">'.$key['claims_dentist'],'<font color="blue">'.$key['claims_dentalclinic'],'<font color="blue">'.$key['account_name'],$key['remarks'],'<font color="blue">'.$key['claims_remarks'],$key['user'],'<font color="blue">'.$key['claims_user'],$bill);
		}
		echo $this->table->generate();
		echo '</div>';
	}
?>
<?php echo form_close();?> 