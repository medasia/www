<style type="text/css">
.invoice, .invoice_td
{
	border:1px solid black;
}
html
{
	font-size: 12px;
}
#availments_table
{
	font-size: 10px;
}
</style>

<?php
	date_default_timezone_set('Asia/Manila');
	$date = date('Y-m-d');

	// INSURANCE table
	$insu_tmpl = array(
				'table_open' => '<table border="0" cellpadding="1" cellspacing="0" id="insurance">',
				'table_close' => '</table>'
				);
	$this->table->set_template($insu_tmpl);
	$insurance = array(
			array($this->table->add_row('Insurance Name: ','<b>'.$insurance_details[0]['name'].'</b>')),
			array($this->table->add_row('Insurance Address: ',$insurance_details[0]['Address'])),
				);
	$insu_table = $this->table->generate($insurance);

	//INVOICE table
	$invoice_table = '<table cellpadding="2" cellspacing="0" align="right">';
	$invoice_table.=	'<tr>
							<td align="center" class="invoice_td" colspan="50%">Date</td>
							<td align="center" class="invoice_td" colspan="50%">Invoice Number</td>
						</tr>';
	$invoice_table.=	'<tr>
							<td align="center" class="invoice_td" colspan="50%"><b>'.mdate('%M %d, %Y',mysql_to_unix($date)).'</b></td>
							<td align="center" class="invoice_td" colspan="50%"><b>'.$invoice.'</b></td>
						</tr>';
	$invoice_table.=	'<tr>
							<td colspan="100%" align="center" class="invoice_td">Please pay on the date or before</td>
						</tr>';
	$invoice_table.=	'<tr>
							<td colspan="100%" align="center" class="invoice_td"><b>'.mdate('%M %d, %Y',mysql_to_unix($due_date)).'</td>
						</tr>';
	$invoice_table.='</table>';

	//AVAILMENTS table CODEIGNITER
	// $availments_template = array(
	// 			'table_open' => '<table border="1" cellpadding="4" cellspacing="0" id="availments">',

	// 			'heading_row_start' => '<tr align="center" class="invoice_td">',
	// 			'heading_row_end' => '</tr>',
	// 			'heading_cell_start' => '<th align="center" class="invoice_td">',
	// 			'heading_cell_end' => '</th>',

	// 			'row_start' => '<tr>',
	// 			'row_end' => '</tr>',
	// 			'cell_start' => '<td class="invoice_td">',
	// 			'cell_end' => '</td>',

	// 			'row_alt_start' => '<tr>',
	// 			'row_alt_end' => '</tr>',
	// 			'cell_alt_start' => '<td class="invoice_td">',
	// 			'cell_alt_end' => '</td>',

	// 			'table_close' => '</table>'
	// 			);
	// $this->table->set_template($availments_template);
	// $avail = $this->table->set_heading('No.','Date Recorded','Bill #','Company','Patient','Hospital','Total Amount');
	// $count = 1;

	// foreach($insurance_billing as $hvalue => $hkey)
	// {
	// 	foreach($hkey as $lvalue => $lkey)
	// 	{
	// 		$avail.= $this->table->add_row(array('data'=>$count++.'.','class'=>'invoice_td'),mdate('%M %d, %Y',mysql_to_unix($lkey['print_date'])),$lkey['claims_code'],$lkey['company_name'],
	// 			$lkey['patient_name'],$availments[$hvalue][$lvalue]['hospital_name'],'<b>PHP</b> '.number_format($total_amount[$hvalue],2));
	// 	}
	// }
	// $availments_table =  $this->table->generate($avail);

	//GRAND TOTAL table
	foreach($total_amount as $key => $value)
	{
		@$grand_total += $value;
	}

	//AVAILMENTS table HTML
	$count = 1;
	$availments_table = '<table cellpadding="2" cellspacing="0" style="width:100%" border="1" id="availments_table">
							<tr>
								<th>No.</th>
								<th>Date Recorded</th>
								<th>Bill #</th>
								<th>Company</th>
								<th>Patient</th>
								<th>Hospital</th>
								<th>Total Amount</th>
							</tr>';
							foreach($insurance_billing as $hvalue => $hkey)
							{
								foreach($hkey as $lvalue => $lkey)
								{
									$availments_table.= '<tr>
										<td class="invoice_td">'.$count++.'.</td>
										<td class="invoice_td">'.mdate('%M %d, %Y',mysql_to_unix($lkey['print_date'])).'</td>
										<td class="invoice_td">'.$lkey['claims_code'].'</td>
										<td class="invoice_td">'.$lkey['company_name'].'</td>
										<td class="invoice_td">'.$lkey['patient_name'].'</td>
										<td class="invoice_td">'.$availments[$hvalue][$lvalue]['hospital_name'].'</td>
										<td class="invoice_td"><b>PHP</b> '.number_format($total_amount[$hvalue],2).'</td>
									</tr>';
								}
							}
	$availments_table.= '</table>';

	// DISPLAY ALL TABLE
	echo '<center><div><img width="200px" src="/home/dev/web/operations051513/includes/images/Logo2.jpg"><br>
			<span style="font-size:11px">7/F The Linden Suites, #37 San Miguel Ave.,<br>Ortigas Center, Pasig City 1600 Philippines</span></div></center><br><br>';
	echo '<table cellpadding="4" cellspacing="0" id="top_table" style="width:100%">
			<tr>
				<td colspan="50%">'.$insu_table.'</td>
				<td colspan="50%" align="right">'.$invoice_table.'</td>
			</tr>
		</table>';

	echo '<br><br><br><br><br>
			<br>
			<table cellpadding="4" cellspacing="0" id="overall" style="width:100%" align="center">
			<tr>
				<td colspan="100%" align="center"><b>SUMMARY OF BILLINGS FOR IN - PATIENT '.date('Y').'</b></td>
			</tr>
			<tr>
				<td colspan="100%" align="center">'; echo $availments_table.'</td>
			</tr>
			<tr>
				<td colspan="100%" align="right"><b>GRAND TOTAL: PHP '.number_format($grand_total,2).'</b></td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td align="left" colspan="50%">Prepared By: <u>'.$prepared_by.'</u></td>
				<td align="center" colspan="50%">Noted By: <u>'.$noted_by.'</u></td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%" align="right"><p>Received By:<br><br><br>
						________________________<br>
						Signature over printed name</p></td>
			</tr>
		</table>';	
?>