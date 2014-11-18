<?php
	foreach(@$dentistsdoctors as $cvalue => $ckey)
	{
		$template = array(
						'table_open' => '<table border ="1" cellpadding="4" cellspacing="0" class="table-bordered">',
						'table_close' => '</table>'
						);
		$this->table->set_template($template);
		$this->table->set_heading('Clinic Address', 'Clinic Address','Clinic Address','Clinic Address','Clinic Address');
		$this->table->add_row($ckey['clinic1'],$ckey['clinic2'],$ckey['clinic3'],$ckey['clinic4'],$ckey['clinic5']);
		$clinics[$cvalue] = $this->table->generate();
	}

	if(empty($dentistsdoctors))
	{
		echo "<h2>No Records/s Found!!!</h2>";
	}
	else
	{
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
		$this->table->set_heading('','Type','Name (LN, FN MN)', 'Specialization','Clinic/s','Mobile #', 'Contact #', 'Fax #','E-mail Address', 'Date Accredited','Status','Remarks');
		$count = 1;
		foreach($dentistsdoctors as $value => $key)
		{
			$this->table->add_row($count++.".", $key['type'], $key['lastname'].", ".$key['firstname']." ".$key['middlename'], $key['specialization'], $clinics[$value],
				$key['mobile_number'], $key['contact_number'], $key['fax_number'], $key['email'],mdate('%M %d, %Y', mysql_to_unix($key['date_accredited'])),
				$key['status'],$key['remarks']);
		}
		echo $this->table->generate();
		echo '</div>';
	}
?>

<?php
// foreach($dentistsdoctors as $ckey => $cvalue) {
// 	if($cvalue['clinics'] != NULL) { //continue loop
// 		foreach($cvalue['clinics'] as $crow) {
// 			$template = array(
// 				'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
// 				'table_close'	=> '</table>'
// 				);
// 			$this->table->set_template($template);
// 			$this->table->set_heading('Clinic Name', 'Hospital Name', 'Street Address', 'Subdivision/Village', 'Barangay', 'City', 'Province', 'Schedule');
// 			$clinic[$ckey] = $this->table->generate($cvalue['clinics']);
// 		}
// 	} else {
// 		$clinic[$ckey] = 'N/A';
// 	}
// }
// $tmpl = array (
// 				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',

// 				'heading_row_start'   => '<tr>',
// 				'heading_row_end'     => '</tr>',
// 				'heading_cell_start'  => '<th>',
// 				'heading_cell_end'    => '</th>',

// 				'row_start'           => '<tr>',
// 				'row_end'             => '</tr>',
// 				'cell_start'          => '<td>',
// 				'cell_end'            => '</td>',

// 				'row_alt_start'       => '<tr>',
// 				'row_alt_end'         => '</tr>',
// 				'cell_alt_start'      => '<td>',
// 				'cell_alt_end'        => '</td>',

// 				'table_close'         => '</table>'
// 				);

// $this->table->set_template($tmpl);
// $this->table->set_heading('', 'Type', 'Name (LN, FN MN)', 'Specialization', 'Clinic/s', 'Mobile #', 'Contact #', 'Fax #', 'Date Accredited', 'Status', 'Remarks');
// $count=1;
// foreach($dentistsdoctors as $value => $key) {
// 	// Adding a new table row.
// 	$this->table->add_row($count++.".", $key['type'],
// 		$key['lastname'].', '.$key['firstname'].' '.$key['middlename'], $key['specialization'], $clinic[$value],
// 		$key['mobile_number'], $key['contact_number'], $key['fax_number'],  mdate('%M %d, %Y', mysql_to_unix($key['date_accredited'])),
// 		$key['status'], $key['remarks']);
// }
// echo $this->table->generate(); 
?>