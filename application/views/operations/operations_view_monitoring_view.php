<html>
<head>
	<title>View Monitoring Report</title>
</head>
<h2>View Monitoring Record of <?php echo $codeLOA;?> for<br><?php echo $patient_name;?></h2>
<?php
	foreach($monitoring as $key => $value)
	{
		$tmpl = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
		$this->table->set_template($tmpl);
		foreach($value['diagnosis'] as $dkey => $dvalue)
		{
			$diagnosis_names = $this->table->add_row($dvalue['diagnosis']);
		}
		$diagnosis[$key] = $this->table->generate($diagnosis_names);
	}
	echo '<div class="table_scroll">';
	$template = array(
				'table_open' => '<table border="1" cellpadding="4" cellspacing="0">',

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
	$this->table->set_heading('','LOA Code','Patient Name','Company - Insurance','Hospital Name','Hospital Branch','Chief Complaint/Diagnosis','Date','Time','Running Bill',
		'Caller Name','Status','History','Time Generated/Updated','User','');
	$count = 1;
	foreach($monitoring as $value => $key)
	{
		if($key['discharge_status'] == 'Discharge')
		{
			unset($key);
		}
		else
		{
			$edit = anchor(base_url().'verification/editMonitoring/'.$key['code'].'/'.$key['time_generated'],'Edit',array('class'=>'btn btn-xs btn-warning','target'=>'_blank'));
			$this->table->add_row($count++.'.',$key['code'],$key['patient_name'],$key['company_name'].' - '.$key['insurance_name'],$key['hospital_name'],$key['hospital_branch'],
				$diagnosis[$value],mdate('%M %d, %Y', mysql_to_unix($key['date'])),$key['monitoring_time'],$key['running_bill'],$key['caller_name'],$key['status'],$key['history'],mdate('%M %d, %Y %H:%i %A', mysql_to_unix($key['time_generated'])),$key['user'],$edit);
		}
	}
	echo $this->table->generate();
	echo '</div>';
?>