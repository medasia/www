<script>
$(document).ready(function()
	{
		$("#due_date").datepicker({format: "yyyy-mm-dd"});
		$("form").validate({
    			rules: {
    				invoice: {
    					required: true
    				},
    			    due_date: {
    			        required: true
    			    },
    			    prepared_by: {
    			    	required: true
    			    },
    			    noted_by: {
    			    	required: true
    			    }
    			},
    			messages: {
    				invoice: {
    					required: 'This field is required'
    				},
    			    due_date: {
    			        required: "This field is required"
    			    },
    			    noted_by: {
    					required: 'This field is required'
    				}
    			}
			});
	});
</script>
<h3>Please fill-up this form</h3>
<?php echo validation_errors();?>
<?php echo form_open('claims/printSummary');?>
<?php
	date_default_timezone_set('Asia/Manila');
	$date = date('Y-m-d');
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);

	$inputs = array(
			array('',''),
			array(form_label('Date:'),form_label(mdate('%M %d, %Y',mysql_to_unix($date)))),
			array(form_label('Invoice Number:'),form_input(array('name'=>'invoice','id'=>'invoice','size'=>'20','placeholder'=>'Invoice Number','class'=>'form-control'))),
			array(form_label('Pay on or before:'),form_input(array('name'=>'due_date','id'=>'due_date','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
			array(form_label('Prepared By:'),form_input(array('name'=>'prepared_by','id'=>'prepared_by','size'=>'20','placeholder'=>'Prepared By','class'=>'form-control'))),
			array(form_label('Noted By:'), form_input(array('name'=>'noted_by','id'=>'noted_by','size'=>'20','placeholder'=>'Noted By','class'=>'form-control'))),
			array('',form_submit(array('name'=>'submit','value'=>'Print','class'=>'btn btn-success')))
				);
	echo $this->table->generate($inputs);

	echo form_hidden('total_amount',$total_amount);
	echo form_hidden('sel_multi',$sel_multi);
	echo form_hidden('date',$date);

	// GRAND TOTAL DETAILS
	foreach($total_amount as $key => $value)
	{
		@$grand_total += $value;
	}
	echo form_hidden('grand_total',$grand_total);

	echo '<h3>Preview of Summary to be printed</h3>';
	echo '<hr>';
?>
<?php echo form_close();?>

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
			array($this->table->add_row('<b>'.$insurance_details[0]['name'].'</b>')),
			array($this->table->add_row($insurance_details[0]['Address'])),
				);
	$insu_table = $this->table->generate($insurance);

	//INVOICE table
	$invoice_table = '<table border="1" cellpadding="4" cellspacing="0" id="invoice">
						<tr>
							<td align="center">Date</td>
							<td align="center">Invoice Number</td>
						</tr>
						<tr>
							<td align="center"><b>'.mdate('%M %d, %Y',mysql_to_unix($date)).'</b></td>
							<td align="center">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="100%" align="center">Please pay on the date or before</td>
						</tr>
						<tr>
							<td colspan="100%" align="center">&nbsp;</td>
						</tr>
					</table>';

	//AVAILMENTS table
	$availments_template = array(
				'table_open' => '<table border="1" cellpadding="4" cellspacing="0" id="availments">',

				'heading_row_start' => '<tr align="center">',
				'heading_row_end' => '</tr>',
				'heading_cell_start' => '<th align="center">',
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
	$this->table->set_template($availments_template);
	$avail = $this->table->set_heading('No.','Date Recorded','Bill #','Company','Patient','Hospital','Total Amount');
	$count = 1;

	foreach($insurance_billing as $hvalue => $hkey)
	{
		foreach($hkey as $lvalue => $lkey)
		{
			$avail.= $this->table->add_row($count++.'.',mdate('%M %d, %Y',mysql_to_unix($lkey['print_date'])),$lkey['claims_code'],$lkey['company_name'],$lkey['patient_name'],$availments[$hvalue][$lvalue]['hospital_name'],'<b>PHP</b> '.number_format($total_amount[$hvalue],2));
		}
	}
	$availments_table =  $this->table->generate($avail);

	echo '<table border="0" cellpadding="4" cellspacing="0" id="overall" style="width:100%">
			<tr>
				<td align="center">'.$insu_table.'</td>
				<td align="center">'.$invoice_table.'</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="100" align="center"><b>SUMMARY OF BILLINGS FOR IN - PATIENT '.date('Y').'</b></td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%" align="center">'.$availments_table.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="100%" align="center"><b>GRAND TOTAL: PHP '.number_format($grand_total,2).'</b></td>
			</tr>

			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td align="center">Prepared By: ________________________</td>
				<td align="center">Checked By: ________________________</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="100%">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td align="center"><p>Received By:<br><br><br>
						________________________<br>
						Signature over printed name</p></td>
			</tr>
		</table>';	
?>