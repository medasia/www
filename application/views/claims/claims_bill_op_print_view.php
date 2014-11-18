<?php
	// FORM DETAILS
	date_default_timezone_set('Asia/Manila');
	$date = date('Y-m-d');
	$tmpl = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0" align="center">',
				'table_close' => '</table>'
				);	

	// INSURANCE TABLE
	$insu_tmpl = array(
				'table_open' => '<table border="0" cellpadding="1" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($insu_tmpl);

	$insu = array(
			array($this->table->add_row('<b>Account Name: ','<b>'.$insurance[0]['name'].'</b>')),
			array($this->table->add_row('',$insurance[0]['Address'])),
			array($this->table->add_row('<b>ATTN: ','<b>'.$insurance[0]['Attention_Name'].'</b>')),
			array($this->table->add_row('',$insurance[0]['Attention_Pos']))
				);
	$insu_table = $this->table->generate($insu);

	// BILLING TABLE
	$this->table->set_template($tmpl);
	$bill = array(
			array($this->table->add_row('<b>'.mdate('%M %d, %Y',mysql_to_unix($date)).'</b>')),
			array($this->table->add_row('<b>Billing # </b>'))
				);
	$bill_table = $this->table->generate($bill);

	// AVAILMENTS TABLE
	foreach($availments as $key => $value)
	{
		$this->table->set_template($tmpl);
		foreach($value['diagnosis'] as $dvalue => $dkey)
		{
			$diagnosis_name = $this->table->add_row($dkey['diagnosis']);
		}
		$diagnosis[$key] = $this->table->generate($diagnosis_name);
	}

	//AVAILMENTS table
	$availments_tmpl = array(
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
	$this->table->set_template($availments_tmpl);
	$this->table->set_heading('No.','LOA Code','Patient Name','Date of Availment','Company','Hospital','Diagnosis','Amount');
	$count = 1;

	foreach($availments as $value => $key)
	{
		$avail_table = $this->table->add_row($count++.'.',$key[0]['code'],$key[0]['patient_name'],mdate('%M %d, %Y',mysql_to_unix($key[0]['date_encoded'])),$key[0]['company_name'],$key[0]['hospital_name'],$diagnosis[$value],'<b>PHP </b>'.number_format($key['amount'],2));
	}	
	$availments_table =  $this->table->generate($avail_table);

	//OVER ALL TABLE
	echo '<table cellpadding="5" cellspacing="0" style="width:100%">
			<tr>
				<td colspan="100%" align="center"><b>STATEMENT OF ACCOUNT</b></td>
			</tr>
			<tr>
				<td colspan="50%" align="center">'.$insu_table.'</td>
				<td colspan="50%" align="center">'.$bill_table.'</td>
			</tr>
			<tr>
				<td colspan="100%" align="center">'.$availments_table.'</td>
			</tr>
			<tr>
				<td colspan="100%" align="right"><b>GRAND TOTAL: PHP '.number_format($total_amount,2).'</b></td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="50%">Prepared By: <u>'.$prepared_by.'</u></td>
				<td colspan="50%" align="center">Checked By: <u>'.$checked_by.'</u></td>
			</tr>
			<tr>
				<td colspan="50%">Approved By: <u>'.$approved_by.'</u></td>
				<td colspan="50%" align="center"><p>Received By:<br><br>
						________________________<br>
						Signature over printed name</p></td>
			</tr>
		</table>';
?>