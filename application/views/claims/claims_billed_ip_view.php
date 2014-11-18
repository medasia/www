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

<?php echo validation_errors(); ?>
<?php echo form_open('claims/summarized',array('target'=>'_blank'));?>
<?php
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$this->table->set_template($tmpl);
	$this->table->add_row(form_submit(array('name'=>'submit','value'=>'Summarized','class'=>'btn btn-success')),form_checkbox(array('name'=>'select_all','id'=>'select_all','class'=>'select_all')),form_label('Select All'));
	echo $this->table->generate();

	if($billed)
	{
		foreach($availments as $lkey => $lvalue)
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
	$this->table->set_heading('No.','Billing Code','Date Billed','Approval Code','Patient Name','Availment Type','Date of Availment','Date of Received','Chief Complaint/Diagnosis','Diagnosis','Amount','Actual Amount',
		'Variance','Company Name','Insurance Name','Hospital','Doctor','Dentist','Dental Clinic','Account Name','Availment Type','Laboratory','Remarks','Remarks Claims','User','');
	$count = 1;

	foreach($billed as $value => $key)
	{
		$variance[$value] = 0.00;
		$variance[$value] = $totalAll[$value] - $availments[$value]['availments'][0]['claims_amount'];

		if($variance[$value] <= 0.00)
		{
			$var_color = "red";
		}
		else
		{
			$var_color = "blue";
		}

		if(($availments[$value]['availments'][0]['claims_status'] == 'SUMMARIZED') OR ($availments[$value]['availments'][0]['availment_type'] == 'Out-Patient'))
		{
			unset($key);
		}
		else
		{
			$sel_multi = form_checkbox(array('name'=>'sel_multi[]','id'=>'sel_multi','class'=>'sel_multi','value'=>$key['id']));
		
			$this->table->add_row($count++.'.'.$sel_multi,$key['claims_code'],mdate('%M %d, %Y',mysql_to_unix($key['print_date'])),$availments[$value]['availments'][0]['code'],$key['patient_name'],$availments[$value]['availments'][0]['availment_type'],'<font color="blue">'.mdate('%M %d, %Y %h:%i %a',mysql_to_unix($availments[$value]['availments'][0]['claims_dateofavailment'])),'<font color="blue">'.mdate('%M %d, %Y %h:%i %a',mysql_to_unix($availments[$value]['availments'][0]['claims_dateofrecieve'])),
				$diagnosis[$value],'<font color="blue">'.$availments[$value]['availments'][0]['claims_diagnosis'],number_format($totalAll[$value],2),'<font color="blue">'.number_format($availments[$value]['availments'][0]['claims_amount'],2),'<font color="'.$var_color.'">'.number_format($variance[$value],2),$availments[$value]['availments'][0]['company_name'],$availments[$value]['availments'][0]['insurance_name'],$availments[$value]['availments'][0]['hospital_name'],'<font color="blue">'.$availments[$value]['availments'][0]['claims_doctor'],
				'<font color="blue">'.$availments[$value]['availments'][0]['claims_dentist'],'<font color="blue">'.$availments[$value]['availments'][0]['claims_dentalclinic'],'<font color="blue">'.$availments[$value]['availments'][0]['account_name'],$benefit[$value],$lab[$value],$availments[$value]['availments'][0]['remarks'],'<font color="blue">'.$availments[$value]['availments'][0]['claims_remarks'],$availments[$value]['availments'][0]['user'],'<font color="blue">'.$availments[$value]['availments'][0]['claims_status']);
		}
	}
	echo $this->table->generate();
	echo '</div>';
?>
<?php echo form_close();?> 
</html>