<html>
<head>
	<title>Admission Report</title>
</head>
<h2>Admission Report</h2>
<?php
	date_default_timezone_set("Asia/Manila");
	$date = date_default_timezone_get();

	foreach($admission as $ivalue => $ikey)
	{
		//INSURANCE APPROVAL DISPLAY
		if(($ikey['dateofemail'] != '0000-00-00') && !is_null($ikey['timefrom']) && !is_null($ikey['timeto'])
		&& !is_null($ikey['approved']) && !is_null($ikey['declined']))
		{
			$template = array(
					'table_open' 	=> '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">',
					'table_close'	=> '</table>'
					);
			$this->table->set_template($template);
			$this->table->set_heading('Date Of E-mail','Time From','Time To','Approved By','Declined By');
			$this->table->add_row($ikey['dateofemail'],$ikey['timefrom'],$ikey['timeto'],$ikey['approved'],$ikey['declined']);
			$insurance[$ivalue] = $this->table->generate();
		}
		else
		{
			$insurance[$ivalue] = "No Insurance Approval";
		}

		$template = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
					'table_close' => '</table>'
					);
		$this->table->set_template($template);
		foreach($ikey['diagnosis'] as $key => $value)
		{
			$diagnosis_names = $this->table->add_row($value['diagnosis']);
		}
		$diagnosis[$ivalue] = $this->table->generate($diagnosis_names);

		//DISPLAY SPECIALIST by each ADMISSION REPORT
		if(isset($ikey['specialist']))
		{
			$stmpl = array(
						'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="table table-bordered"  style="width:300px">',
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
			$spclst[$ivalue] = "No Specialist Assigned";
		}
	}

	//ADMISSION REPORT
	if(empty($admission))
	{
		echo "<h2>No Record/s Found!!!</h2>";
	}
	else
	{
		echo '<div class="table_scroll">';
		$tmpl = array(
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
		$this->table->set_heading('','LOA Code', 'Patient Name','Company - Insurance','Hospital Name','Hospital Branch','Chief Complaint/Diagnosis',
							'Date Admitted','Physician','Specialist','Insurance Approval','History','Remarks','User','Date Updated','');
		$count = 1;
		foreach(@$admission as $value => $key)
		{
			if($key['discharge_status'] == 'Discharged')
			{
				unset($key);
			}
			else
			{
				$viewRecords = anchor(base_url().'verifications/viewAdmissionRecords/'.$key['code'].'/',$key['code'],array('target'=>'_blank'));
				$edit = anchor(base_url().'verifications/editAdmission/'.$key['code'].'/'.$key['time_generated'],'Edit',array('class'=>'btn btn-xs btn-warning','target'=>'_blank'));
				$this->table->add_row($count++.".",$viewRecords,$key['patient_name'],$key['company_name']." - ".$key['insurance_name'], $key['hospital_name'],
					$key['hospital_branch'], $diagnosis[$value],mdate('%M %d, %Y', mysql_to_unix($key['date_admitted'])),$key['physician'], $spclst[$value], $insurance[$value], $key['history'], $key['remarks'], $key['user'], mdate('%M %d, %Y %H:%i %A',mysql_to_unix($key['time_generated'])),$edit);
			}
		}
		echo $this->table->generate();
	}
?>
