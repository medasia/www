<h2>IP Topsheet</h2>
<?php
	$tmpl = array(
			'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">',
			'table_close' => '</table>'
				);
	foreach($ip_topsheet as $hkey => $hvalue)
	{
		$this->table->set_template($tmpl);
		foreach($ip_topsheet_details as $lkey => $lvalue)
		{
			$loa = $this->table->add_row($lvalue['claims_code']);
		}
		$loa_code[$hkey] = $this->table->generate($loa);

		foreach($ip_topsheet_details as $lkey => $lvalue)
		{
			$patient = $this->table->add_row($lvalue['patient_name']);
		}
		$patient_name[$hkey] = $this->table->generate($patient);

		foreach($ip_topsheet_details as $lkey => $lvalue)
		{
			$company = $this->table->add_row($lvalue['company_name']);
		}
		$company_name[$hkey] = $this->table->generate($company);

		foreach($ip_topsheet_details as $lkey => $lvalue)
		{
			$hospital = $this->table->add_row($lvalue['hospital_name']);
		}
		$hospital_name[$hkey] = $this->table->generate($hospital);

		foreach($ip_topsheet_details as $lkey => $lvalue)
		{
			$total = $this->table->add_row('<b>PHP </b>'.$lvalue['total_amount']);
		}
		$total_amount[$hkey] = $this->table->generate($total);
	}

	echo '<div class="table_scroll">';
	$template = array(
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
	$this->table->set_template($template);
	$this->table->set_heading('No.','Invoice Number','Insurance Name','LOA Code','Patient Name','Company Name','Hospital Name','Total Amount','Grand Total','Prepared By','Noted By','Due Date','');
	$count=1;
	foreach($ip_topsheet as $value => $key)
	{
		if($key['availment_type'] == 'Out-Patient')
		{
			unset($value);
		}
		else
		{
			$reprint = anchor(base_url().'claims/reprintIPSummary/'.$key['invoice_number'],'Reprint',array('target'=>'_blank','class'=>'btn btn-danger btn-sm'));
			$this->table->add_row($count++.'.',$key['invoice_number'],$key['insurance_name'],$loa_code[$value],$patient_name[$value],$company_name[$value],$hospital_name[$value],$total_amount[$value],'<b>PHP </b>'.$key['grand_total'],$key['prepared_by'],$key['noted_by'],mdate('%M %d, %Y', mysql_to_unix($key['due_date'])),$reprint);
		}
	}
	echo $this->table->generate();
	echo '</div>';
?>