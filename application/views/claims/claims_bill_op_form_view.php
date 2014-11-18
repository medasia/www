<script>
$(document).ready(function()
	{
		$('#date').datepicker({format:'yyyy-mm-dd'});
		$("form").validate({
    			rules: {
    				prepared_by: {
    					required: true
    				},
    			    checked_by: {
    			        required: true
    			    },
    			    approved_by: {
    			    	required: true
    			    }
    			},
    			messages: {
    				prepared_by: {
    					required: 'This field is required'
    				},
    			    checked_by: {
    			        required: "This field is required"
    			    },
    			   	approved_by: {
    					required: 'This field is required'
    				}
    			}
			});
	});
</script>
<h3>Please fill up this form...</h3>
<?php echo validation_errors();?>
<?php echo form_open('claims/printOP');?>
<?php
	// FORM DETAILS
	date_default_timezone_set('Asia/Manila');
	$date = date('Y-m-d');
	$tmpl = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($tmpl);

	$inputs = array(
			array('',''),
			array(form_label('Date:'),form_label(mdate('%M %d, %Y',mysql_to_unix($date)))),
			array(form_label('Prepared By: '),form_input(array('name'=>'prepared_by','id'=>'prepared_by','size'=>'20','placeholder'=>'Prepared By','class'=>'form-control'))),
			array(form_label('Checked By: '),form_input(array('name'=>'checked_by','id'=>'checked_by','size'=>'20','placeholder'=>'Checked By','class'=>'form-control'))),
			array(form_label('Approved By: '),form_input(array('name'=>'approved_by','id'=>'approved_by','size'=>'20','placeholder'=>'Approved By','class'=>'form-control'))),
			array('',form_submit(array('name'=>'submit','value'=>'Print','class'=>'btn btn-success btn-sm')))
				);
	echo $this->table->generate($inputs);

	echo form_hidden('date',$date);
	echo form_hidden('sel_multi',$sel_multi);
	echo form_hidden('total_amount',$total_amount);

	echo '<h2>Preview sample details to be print below</h2>';
	echo '<hr>';

	// INSURANCE TABLE
	$insu_tmpl = array(
				'table_open' => '<table border="0" cellpadding="1" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($insu_tmpl);

	$insu = array(
			array($this->table->add_row('<b>Account Name: ','<b>'.$insurance[0]['name'].'</b>')),
			array($this->table->add_row('',$insurance[0]['Address'])),
			array($this->table->add_row('&nbsp;')),
			array($this->table->add_row('')),
			array($this->table->add_row('<b>ATTN: ','<b>'.$insurance[0]['Attention_Name'].'</b>')),
			array($this->table->add_row('',$insurance[0]['Attention_Pos']))
				);
	$insu_table = $this->table->generate($insu);

	// BILLING TABLE
	$this->table->set_template($tmpl);
	$bill = array(
			array($this->table->add_row('<b>'.mdate('%M %d, %y',mysql_to_unix($date)).'</b>')),
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
	$this->table->set_heading('No.','LOA Code','Patient Name','Date of Availment','Company Name','Hospital','Diagnosis','Amount');
	$count = 1;

	foreach($availments as $value => $key)
	{
		$avail_table = $this->table->add_row($count++.'.',$key[0]['code'],$key[0]['patient_name'],mdate('%M %d, %Y',mysql_to_unix($key[0]['date_encoded'])),$key[0]['company_name'],$key[0]['hospital_name'],$diagnosis[$value],'<b>PHP </b>'.number_format($key['amount'],2));
	}	
	$availments_table =  $this->table->generate($avail_table);

	//OVER ALL TABLE
	echo '<table cellpadding="5" cellspacing="0" style="width:100%">
			<tr>
				<td>'.$insu_table.'</td>
				<td>'.$bill_table.'</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
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
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td>Prepared By: ________________________</td>
				<td>Checked By: ________________________</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td>Noted By: ________________________</td>
				<td><p>Received By:<br><br><br>
						________________________<br>
						Signature over printed name</p></td>
			</tr>
		</table>';
?>
<?php echo form_close();?>
