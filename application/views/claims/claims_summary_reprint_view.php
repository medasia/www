<style type="text/css">
.invoice, .invoice_td
{
	border:1px solid black;
}
html
{
	font-size: 12px;
	font-family: sans-serif;
}
#availments_table
{
	font-size: 10px;
	font-family: Sans-serif;
}
</style>
<?php
	//INSURANCE table
	$insu_tmpl = array(
				'table_open' => '<table border="0" cellpadding="2" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($insu_tmpl);
	$insurance = array(
				array($this->table->add_row('Insurance Name: ','<b>'.$insurance[0]['name'].'</b>')),
				array($this->table->add_row('Insurance Address: ',$insurance[0]['Address']))
				);
	$insu_table = $this->table->generate($insurance);

	//INVOICE table
	$invoice_table = '<table cellpadding="2" cellspacing="0" align="right">';
	$invoice_table.=	'<tr>
							<td align="center" class="invoice_td" colspan="50%">Date</td>
							<td align="center" class="invoice_td" colspan="50%">Invoice Number</td>
						</tr>';
	$invoice_table.=	'<tr>
							<td align="center" class="invoice_td" colspan="50%"><b>'.mdate('%M %d, %Y',mysql_to_unix($topsheet[0]['date'])).'</b></td>
							<td align="center" class="invoice_td" colspan="50%"><b>'.$topsheet[0]['invoice_number'].'</b></td>
						</tr>';
	$invoice_table.=	'<tr>
							<td colspan="100%" align="center" class="invoice_td">Please pay on the date or before</td>
						</tr>';
	$invoice_table.=	'<tr>
							<td colspan="100%" align="center" class="invoice_td"><b>'.mdate('%M %d, %Y',mysql_to_unix($topsheet[0]['due_date'])).'</td>
						</tr>';
	$invoice_table.='</table>';

	//AVAILMENTS table
	$count = 1;
	$availments_table = '<table border="1" cellpadding="2" cellspacing="0" style="width:100%" id="availments_table">
							<tr>
								<th>No.</th>
								<th>Date Recorded</th>
								<th>Bill #</th>
								<th>Company</th>
								<th>Patient</th>
								<th>Hospital</th>
								<th>Total Amount</th>
							</tr>';
							foreach($topsheet_details as $value => $key)
							{
								$availments_table.= '<tr>
									<td class="invoice_td">'.$count++.'</td>
									<td class="invoice_td">'.mdate('%M %d, %Y', mysql_to_unix($key['print_date'])).'</td>
									<td class="invoice_td">'.$key['claims_code'].'</td>
									<td class="invoice_td">'.$key['company_name'].'</td>
									<td class="invoice_td">'.$key['patient_name'].'</td>
									<td class="invoice_td">'.$key['hospital_name'].'</td>
									<td class="invoice_td"><b>PHP '.number_format($key['total_amount'],2).'</td>
								</tr>';
							}
							$availments_table.= '</table>';

	// DISPLAY ALL TABLE
	echo '<center><div><img width="200px" src="/home/dev/web/operations051513/includes/images/Logo2.jpg"><br>
			<span style="font-size:11px">7/F The Linden Suites, #37 San Miguel Ave.,<br>Ortigas Center, Pasig City 1600 Philippines</span></div></center><br><br>';
	echo '<table cellpadding="4" cellspacing="0" id="top_table" style="width:100%">
			<tr>
				<td colspan="50%">'.$insu_table.'</td>
				<td colspan="50%" align="right">'.$invoice_table.'</td>
			<tr>
		</table>';

	echo '<br><br><br><br><br>

			<table cellpadding="2" cellspacing="0" id="overall" style="width:100%" align="center">
				<tr>
					<td colspan="100%" align="center"><b>SUMMARY OF BILLINGS</b></td>
				</tr>
				<tr>
					<td colspan="100%" align="center">'; echo $availments_table.'</td>
				</tr>
				<tr>
					<td colspan="100%" align="right"><b>GRAND TOTAL: PHP '.number_format($topsheet[0]['grand_total'],2).'</b></td>
				</tr>
				<tr>
					<td colspan="100%">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="100%">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" colspan="50%">Prepared By: <u>'.$topsheet[0]['prepared_by'].'</u></td>
					<td align="center" colspan="50%">Noted By: <u>'.$topsheet[0]['noted_by'].'</u></td>
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