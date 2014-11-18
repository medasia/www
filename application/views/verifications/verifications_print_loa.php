<style type="text/css">
html
{
	font-size: 11px;
}
#message
{
	border: 1px solid black;
}
</style>
<html>
	<head>
		<title>Medriks - Print LOA</title>
	</head>
<body>
<?php
$date = date('Y-m-d');
$tmpl = array(
			'table_open' => '<table border="0" cellpadding="2" cellspacing="0">',
			'table_close' => '</table>');
$this->table->set_template($tmpl);
foreach($diagnosis as $key => $value)
{
	$dgnsis.= strtoupper($value);
	if($key != count($diagnosis)-1);
	{
		$dgnsis.= ', ';
	}
}

$total = 0.00;
$bnfts = $this->table->set_heading('Benefit Name','Amount');
foreach($benefits as $key => $value)
{
	$total += $value['availed_amount'];
	$bnfts.= $this->table->add_row($value['benefit_name'],'Php. '.number_format($value['availed_amount'],2));
}
$bnfts.= $this->table->add_row('TOTAL AMOUNT: ','<b>Php. '.number_format($total,2).'</b>');
$benefits_table =  $this->table->generate($bnfts);
$url = base_url();

echo "<table border='0' cellpadding ='1' style='width:100%'>
		<tr>
			<td colspan=50%><div><img width='200px' src='/home/dev/web/operations051513/includes/images/Logo2.jpg'><br>
				7/F The Linden Suites, #37 San Miguel Ave.,<br>Ortigas Center, Pasig City 1600 Philippines</div><td>
			<td colspan=50% align='right'>IMPORTANT:<br>PLEASE SECURE APPROVAL CODE FROM OUR 24/7<br>OPERATION CENTER<br>Tel. No: (02) 636-6832 | Fax No: (2) 631-6557
						<br>Toll Free No: 1-800-1-888-0012</td>
		</tr>
		<tr>
			<td colspan=100%><center><b>LETTER OF AUTHORIZATION: <u>".strtoupper($availment_type)."</u></b></center></td>
		</tr>
		<tr>
			<td colspan=100%><center><b>APPROVAL CODE FOR: <u>".$code."</u></center></b></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan=30%>DATE: <b>".mdate('%M %d, %Y',mysql_to_unix($date))."</b></td>
			<td colspan=70%>HOSPITAL/CLINIC: <b>".$hospital_name." - ".$hospital_branch."</b></td>
		</tr>
		<tr>
			<td colspan=100%>NAME OF PATIENT: <b>".strtoupper($patient_name)."</b></td>
		</tr>
		<tr>
			<td colspan=100%>COMPANY: <b>".strtoupper($company_name)."</b></td>
		</tr>
		<tr>
			<td colspan=100%>DIAGNOSIS: <b>".$dgnsis."</b></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan=100%><center><b><u>This Letter of Authorization is valid for Claims processing if with corresponding Approval Code</b></u></center></td>
		</tr>
		<tr>
			<td colspan=45% align='center'>MedAsia Phils. shall cover for:</td>
			<td colspan=55% align='center'>".$benefits_table."</td>
		</tr>
		<tr>
			<td colspan=100%><p>Thank you,<br><br><u>________".$user."________</u><br>Signature over printed name</p></td>
		</tr>
		<tr>
			<td colspan=100%><center><b>Instruction: Please submit LOA within 7 days from date of availment to MedAsia Phils.</b></center></td>
		</tr>
		<tr>
			<td colspan=100%><center>Original Copy: MedAsia Phils.	Duplicate Copy: Hospital/Clinic 	REV: </center></td>
		</tr>
	</table>";
?>
</body>