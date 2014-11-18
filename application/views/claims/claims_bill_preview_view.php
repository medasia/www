<style type="text/css">
.details_id, .details_th
{
	border:1px solid black;
}
</style>

<center><h2>Statement of Account</h2></center><br>

<?php
	// CLAIMS table
	date_default_timezone_set('Asia/Manila');
	$date = date('Y-m-d');
	$claims_tmpl = array(
				'table_open' => '<table border="0" cellpadding="1" cellspacing="0" id="claims">',
				'table_close' => '</table>'
				);
	$this->table->set_template($claims_tmpl);
	$claims = array(
			array($this->table->add_row('Claims No.:','')),
			array($this->table->add_row('','')),
			array($this->table->add_row('DATE:',mdate('%M %d, %Y',mysql_to_unix($date))))
				);
	$claims_table = $this->table->generate($claims);

	// INSURANCE table
	$insu_tmpl = array(
				'table_open' => '<table border="0" cellpadding="1" cellspacing="0" id="insurance">',
				'table_close' => '</table>'
				);
	$this->table->set_template($insu_tmpl);
	foreach($insurance as $row)
	{
		$insu = array(
			array($this->table->add_row('TO:', '<b>'.$row['name'].'</b>')),
			array($this->table->add_row('',$row['Address'])),
			array($this->table->add_row('<br>')),
			array($this->table->add_row('ATTN:', '<u>'.$row['Attention_Name'].'</u>')),
			array($this->table->add_row('',$row['Attention_Pos']))
			);
	}
	$insu_table = $this->table->generate($insu);

	//PATIENT DETAILS table
	$patient_tmpl = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="patient">',
				'table_close' => '</table>'
					);
	$this->table->set_template($patient_tmpl);
	foreach($availments as $key => $value)
	{
		$patient_det = array(
						array($this->table->add_row('Patient Name:','<b>'.$value['patient_name'].'</b>')),
						array($this->table->add_row('Approval Code No:','<b>'.$value['code'].'</b>')),
						array($this->table->add_row('Hospital Name: ','<b>'.$value['hospital_name'].'</b>')),
						array($this->table->add_row('Date of Availments:','<b>'.mdate('%M %d, %Y',mysql_to_unix($value['date_encoded'])))),
						array($this->table->add_row('Plan:','<b>'.$plan.'</b>'))
						);
	}
	// $patient_details = form_fieldset('This is to bill you for the following case',array('id'=>'patient_id'));
	$patient_details = $this->table->generate($patient_det);
	// $patient_details.= form_fieldset_close();

	//AMOUNT table
	$amount_tmpl = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="amount">',
					'table_close' => '</table>'
					);
	$this->table->set_template($amount_tmpl);
	$total = 0.00;
	foreach($hospital_bills as $key => $value)
	{
		$total += $value;
	}
		$amount = array(
					array($this->table->add_row('Hospital Bill:','PHP <b>'.number_format($total,2))),
					array($this->table->add_row($availments[0]['physician'],'')),
					array($this->table->add_row('')),
					array($this->table->add_row('')),
					array($this->table->add_row('Total Bills:','PHP <b>'.number_format($total,2)))
					);
	// $amount_details = form_fieldset('Amount',array('id'=>'amount_id'));
	$amount_details = $this->table->generate($amount);
	// $amount_details.= form_fieldset_close();

	//DIAGNOSIS table
	$diagnosis_tmpl = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0" id="diagnosis">',
					'table_close' => '</table>'
					);
	foreach($diagnosis as $key => $value)
	{
		@$dgnsis.= $value.', ';
	}
	$diagnosis_details = $this->table->add_row(array('DIAGNOSIS: ','<b>'.$dgnsis.'</b>'));
	$diagnosis_table = $this->table->generate($diagnosis_details);

	// INSURACE AND CLAIMS DISPLAY table
	echo "<table border='0' cellpadding='4' cellspacing='0' id='insurance_id' style='width:100%'>
			<tr>
				<td>".$insu_table."</td>
				<td>".$claims_table."</td>
			</tr>
		</table>";

	// BILLING table
	echo "<table  cellpadding='4' cellspacing='0' class='details_id' style='width:100%'>
			<tr>
				<th class='details_th' colspan='25%'>REM/REF</th>
				<th class='details_th' colspan='25%'>FAX GTEE</th>
				<th class='details_th' colspan='25%'>".strtoupper($availments[0]['availment_type'])."</th>
				<th class='details_th' colspan='25%'>".$availments[0]['company_name']."</th>
			</tr>
			<tr>
				<td colspan='75%' align='center' class='details_th'>DESCRIPTION</td>
				<td align='center' class='details_th' colspan='25%'>AMOUNT</td>
			</tr>
			<tr>
				<td colspan='75%' class='details_th'>".$patient_details."
				<p>Please find the attachment of the following:<br>
						<br></p></td>
				<td align='center' class='details_th' colspan='25%'>".$amount_details."</td>
			</tr>
			<tr>
				<td colspan='100%' class='details_th'>".$diagnosis_table."</td>
			</tr>
			<tr>
				<td colspan='100%' class='details_th'>Operations: </td>
			</tr>
		</table>";

		$this->table->add_row('<br><br>');
		$this->table->add_row('Prepared By: _______________________________________');
		$this->table->add_row('<br>');
		$this->table->add_row('Checked By:  _______________________________________');
		echo $this->table->generate();
?>