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
		font-size: 9px;
		font-family: Sans-serif;
	}
</style>

<?php
	@$total_amount += 0;
	
	//INSURANCE Table
	$insurance_table = '<table cellpadding="1" cellspacing="0">';
	$insurance_table.= 		'<tr>
								<td>'.$insurance[0]['name'].'</td>
							 </tr>';
	$insurance_table.= 		'<tr>
								<td>'.$insurance[0]['address'].'</td>
							 </tr>';
	$insurance_table.= '</table>';


	//ATTENTION Table
	$attention_table = '<table cellpadding="0" cellspacing="0">';
	$attention_table.= 		'<tr>
								<td align="right">Attention To:</td>
								<td><b>'.$billing_attention_name.'</b></td>
							 </tr>';
	$attention_table.= '</table>';


	//INVOICE Table
	$invoice_table = '<table cellpadding="2" cellspacing="0" style="width:100%">';
	$invoice_table.= 		'<tr>
					  			<td align="center" class="invoice_td">DATE REQUESTED</td>
					  		 </tr>';
	$invoice_table.= 		'<tr>
								<td align="center" class="invoice_td">'.mdate('%M %d, %Y', mysql_to_unix($date_requested)).'</td>
					  		 </tr>';
	$invoice_table.= '</table>';


	//AVAILMENTS Table
	$count = 1;
	$availments_table = '<table border="1" cellpadding="2" cellspacing="0" style="width:100%" id="availments_table">
							<tr>
								<th align="center" rowspan="2">COMPANY</th>
								<th align="center" rowspan="2">MEMBERS</th>
								<th align="center" colspan="2">PARTICULARS</th>
								<th align="center" rowspan="2">DATE OF DECLARATION</th>
								<th align="center" rowspan="2">COVERAGE PERIOD</th>
								<th align="center" rowspan="2">REMARKS</th>
							</tr>

							<tr>
								<td align="center">MEDICAL PLAN</td>
								<td align="center">AMOUNT</td>
							</tr>';

							
							foreach($multiple_patient as $key => $value)
							{
								$availments_table.= '<tr>
									<td>'.$company.'</td>
									<td>'.$value.'</td>
									<td align="center">'.$multiple_medical_plan[$key].'</td>
									<td align="center">'.number_format($multiple_amount[$key],2).'</td>
									<td align="center">'.mdate('%M %d, %Y', mysql_to_unix($multiple_declaration[$key])).'</td>
									<td align="center">'.mdate('%M %d, %Y', mysql_to_unix($multiple_effectivity[$key])).' - '.mdate('%M %d, %Y', mysql_to_unix($multiple_validity[$key])).'</td>
									<td align="center">'.$multiple_remarks[$key].'</td>
								</tr>';

								$total_amount += $multiple_amount[$key];
							}
							$availments_table.=
								'<tr>
									<td><b>TOTAL</b></td>
									<td align="center">&nbsp;</td>
									<td align="center">&nbsp;</td>
									<td align="center"><b>'.number_format($total_amount,2).'</b></td>
									<td align="center">&nbsp;</td>
									<td align="center">&nbsp;</td>
									<td align="center">&nbsp;</td>
								</tr>';
	$availments_table.= '</table>';

	// DISPLAY ALL TABLE
	echo '<table cellpadding="4" cellspacing="0" id="top_table" style="width:100%">
			<tr>
				<td width="35%">
					<img width="200px" src="/home/dev/web/operations051513/includes/images/Logo2.jpg">
				</td>
				<td width="30%">
					<center>
					<p><b><span style="font-size:15px">REQUEST FOR BILLING</span></b></p>
					<p><b><span style="font-size:15px">'.$reference_number.'</span></b></p>
					</center>
				</td>
				<td width="35%">
					<center>
						<p>
							<span style="font-size:11px">
								7/F The Linden Suites, #37 San Miguel Ave.,
								<br>Ortigas Center, Pasig City 1600 Philippines
								<br>Tel# 638-1598, Fax# 631-6557
							</span>
						</p>
					</center>
				</td>
			<tr>
		</table>';

	echo '<table cellpadding="4" cellspacing="0" style="width:100%">
			<tr>
				<td width="40%">'.$insurance_table.'</td>
				<td width="25%"></td>
				<td width="35%" align="right">'.$invoice_table.'</td>
			</tr>
			<tr>
				<td width="40%">'.$attention_table.'</td>
			</tr>
		</table>';

	echo '<br><br>

			<table cellpadding="2" cellspacing="0" id="overall" style="width:100%" align="center">
				<tr>
					<td colspan="100%" align="center">'; echo $availments_table.'</td>
				</tr>
				<tr>
					<td colspan="100%">RF# '.$rf.'</td>
				</tr>
				<tr>
					<td colspan="100%">&nbsp;</td>
				</tr>

				<tr>
					<td align="left" colspan="50%">
						<table cellpadding="0" cellspacing="0" >
							<tr>
								<td>Prepared By:</i></td>
							</tr>
							<tr>
								<td>
									<p>
										__________________________________________________
									</p>
								</td>
							</tr>
							<tr>
								<td>'.$prepared_by.'</td>
							</tr>
							<tr>
								<td><i>'.$prepared_by_position.'</i></td>
							</tr>
						</table>
					</td>
					<td align="right" colspan="50%">
							<table cellpadding="0" cellspacing="0" >
							<tr>
								<td>Received By:</i></td>
							</tr>
							<tr>
								<td>
									<p>
										__________________________________________________
									</p>
								</td>
							</tr>
							<tr>
								<td align="center">'.$received_by.'</i></td>
							</tr>
							<tr>
								<td align="center"><i>'.$received_by_position.'</i></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';
?>