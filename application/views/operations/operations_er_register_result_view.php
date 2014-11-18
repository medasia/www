<?php
if(empty($patients) OR is_null($patients))
{
	echo "<h2>No Record/s Found!!!</h2>";
}
else
{
	echo '<div class="table_scroll">';
	$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',

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
	$this->table->set_heading('', '','Name', 'Company', 'Insurance', 'Date of birth', 'Age', 'Level/Position', 'Declaration date', 'Start', 'End', 'Membership Status', 'Cardholder Type', 'Cardholder', 'Beneficiary', 'Remarks', 'Benefits');
	$count=1;
	foreach ($patients as $value => $key)
	{
		if(strtolower($key['status']) == "active")
		{
			$newdate = strtotime('-7 day', strtotime($key['end']));
			$newdate = date('Y-m-d',$newdate);
			$expires = (strtotime($key['end']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24);

			if($expirese > 1)
			{
				$day = " days";
			}
			else
			{
				$day = " day";
			}

			if($expires < 0)
			{
				$id = $key['id'];
				$field = 'status';
				$data = "EXPIRED";
				$key['status'] = status_update('patient',$field,$data,$id);
			}

			if($newdate <= $currentDate)
			{
				$color = 'orange';
				$key['status'] = $key['status']." - will expire in".$expire.$day.".";
			}
			else
			{
				$color = 'black';
			}
		}
		elseif(strtolower($key['status']) == "expired" || strtolower($key['status']) == 'deleted')
		{
			$color = 'red';
		}
		else
		{
			$color = 'green';
		}

		// Adding a new table row.
		$this->table->add_row("<font color=".$color.">".$count++.".", "<font color=".$color.">".$key['id'].form_radio(array(
											'name'        => 'pick',
											'id'          => 'pick',
											'value'       => $key['id']))
		,"<font color=".$color.">".$key['lastname'].", ".$key['firstname']." ".$key['middlename'], "<font color=".$color.">".$key['compins'][0]['company'], "<font color=".$color.">".$key['compins'][0]['insurance'], "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['dateofbirth'])), "<font color=".$color.">".computeAge($key['dateofbirth']), 
		"<font color=".$color.">".$key['level'], "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['declaration_date'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['start'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['end'])), "<font color=".$color.">".$key['status'], 
		"<font color=".$color.">".$key['cardholder_type'], "<font color=".$color.">".$key['cardholder'], "", "<font color=".$color.">".$key['remarks'], "");
	}
	echo $this->table->generate();
	echo '</div>';
}
?>