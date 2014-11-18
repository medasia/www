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
	//INSURANCE Table
	$insurance_table = '<table>';
	$insurance_table.= 		'<tr>
								<td>Prudential Guarantee & Assurance, Inc.</td>
							 </tr>';
						//<td align="center" class="invoice_td" colspan="50%"><b>'.mdate('%M %d, %Y',mysql_to_unix($topsheet[0]['date'])).'</b></td>
	$insurance_table.= 		'<tr>
								<td>Coyiuto House - Greenhills,<br>200 EDSA, Brgy. Wak-Wack</td>
							 </tr>';
	$insurance_table.= '</table>';


	//ATTENTION Table
	$attention_table = '<table>';
	$attention_table.= 		'<tr>
								<td align="right">Attention:</td>
								<td>Mr. Rene L. Bay / Daniel Peralta</td>
							 </tr>
							 <tr>
								<td></td>
								<td><i>VP President & Health Department</i></td>
							 </tr>';
	$attention_table.= '</table>';


	//INVOICE Table
	$invoice_table = '<table cellpadding="2" cellspacing="0" align="right">';
	$invoice_table.= 		'<tr>
					  			<td align="center" class="invoice_td" colspan="50%">DATE REQUESTED</td>
					  		 </tr>';
						//<td align="center" class="invoice_td" colspan="50%"><b>'.mdate('%M %d, %Y',mysql_to_unix($topsheet[0]['date'])).'</b></td>
	$invoice_table.= 		'<tr>
								<td align="center" class="invoice_td" colspan="50%">Sample Date</td>
					  		 </tr>';
	$invoice_table.= '</table>';


	//AVAILMENTS Table
	$count = 1;
	$availments_table = '<table border="1" cellpadding="2" cellspacing="0" style="width:100%" id="availments_table">
							<tr>
								<th align="center" rowspan="2">COMPANY</th>
								<th align="center" colspan="5">QUANTITY</th>
								<th align="center" rowspan="2">DATE OF DECLARATION</th>
								<th align="center" rowspan="2">DUE DATE OF RELEASE</th>
								<th align="center" rowspan="2">COVERAGE PERIOD</th>
								<th align="center" rowspan="2">REMARKS</th>
							</tr>

							<tr>
								<td align="center">IP & OP</td>
								<td align="center">IP</td>
								<td align="center">ER</td>
								<td align="center">DENTAL</td>
								<td align="center">APE</td>
							</tr>

							<tr>
								<td>A</td>
								<td align="center">1</td>
								<td align="center">2</td>
								<td align="center">3</td>
								<td align="center">4</td>
								<td align="center">5</td>
								<td align="center">B</td>
								<td align="center">C</td>
								<td align="center">D</td>
								<td align="center">E</td>
							</tr>';
							// foreach($topsheet_details as $value => $key)
							// {
							// 	$availments_table.= '<tr>
							// 		<td class="invoice_td">'.$count++.'</td>
							// 		<td class="invoice_td">'.mdate('%M %d, %Y', mysql_to_unix($key['print_date'])).'</td>
							// 		<td class="invoice_td">'.$key['claims_code'].'</td>
							// 		<td class="invoice_td">'.$key['company_name'].'</td>
							// 		<td class="invoice_td">'.$key['patient_name'].'</td>
							// 		<td class="invoice_td">'.$key['hospital_name'].'</td>
							// 		<td class="invoice_td"><b>PHP '.number_format($key['total_amount'],2).'</td>
							// 	</tr>';
							// }
	$availments_table.= '</table>';

	// DISPLAY ALL TABLE
	echo '<table cellpadding="4" cellspacing="0" id="top_table" style="width:100%">
			<tr>
				<td colspan="25%">
					<img width="200px" src="/home/dev/web/operations051513/includes/images/Logo2.jpg">
				</td>
				<td colspan="50%" align="center">
					<p><b>REQUEST FOR BILLING</b></p>
					<p><b>201411-001</b></p>
				</td>
				<td colspan="25%" align="right">
					<table style="font-size:11px">
						<tr><td align="center"><b>7/F The Linden Suites, #37 San Miguel Ave.,</b></td></tr>
						<tr><td align="center"><b>Ortigas Center, Pasig City 1600 Philippines</b></td></tr>
					</table>
					
				</td>
			<tr>
		</table>';

	echo '<table cellpadding="4" cellspacing="0" id="top_table" style="width:100%">
			<tr>
				<td colspan="50%">'.$insurance_table.'</td>
				<td colspan="50%" align="right">'.$invoice_table.'</td>
			<tr>
			<tr>
				<td>'.$attention_table.'</td>
			</tr>
		</table>';

	echo '<br><br>

			<table cellpadding="2" cellspacing="0" id="overall" style="width:100%" align="center">
				<tr>
					<td colspan="100%" align="center">'; echo $availments_table.'</td>
				</tr>
				<tr>
					<td colspan="100%">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="100%">&nbsp;</td>
				</tr>

				<tr>
					<td align="left" colspan="50%">
						<table>
							<tr>
								<td>Prepared By:</i></td>
							</tr>
							<tr>
								<td>
									<p>
										<br>
										<br>
										<br>
										__________________________________________________
									</p>
								</td>
							</tr>
							<tr>
								<td>CHARLES LONGINES C. LONGINO</td>
							</tr>
							<tr>
								<td><i>Senior Developer</i></td>
							</tr>
						</table>
					</td>
					<td align="right" colspan="50%">
							<table>
							<tr>
								<td>Received By:</i></td>
							</tr>
							<tr>
								<td>
									<p>
										<br>
										<br>
										<br>
										__________________________________________________
										<br>
									</p>
								</td>
							</tr>
							<tr>
								<td align="center">CHARLES LONGINES C. LONGINO</i></td>
							</tr>
							<tr>
								<td align="center"><i>Senior Developer</i></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';
?>