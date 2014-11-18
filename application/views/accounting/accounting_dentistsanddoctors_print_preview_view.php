<style type="text/css">
.overline
{
	text-decoration: overline;
}
.underline
{
	text-decoration: underline;
}
.details_id, .details_hd
{
	border:1px solid black;
}
</style>
<?php
	$date = date('Y-m-d');
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$inputs = array(
			array('',''),
			array(form_label('Check Number:'),form_input(array('name'=>'check_number','id'=>'check_number','size'=>'20','placeholder'=>'Enter Check Number'))),
			array('',form_submit(array('name'=>'submit','value'=>'Print')))
			);
	$this->table->set_template($tmpl);
	echo $this->table->generate($inputs);

	echo "<hr><table cellpadding='4' cellspacing='0' style='width:100%'>
			<tr>
				<td><b>MEDASIA HEALTHCARE SYSTEM PHILIPPINES INC.</b></td>
				<td class='details_id' align='center'>ACKNOWLEDGEMENT</td>
			</tr>
		</table><br>";

	echo "<table cellpadding='4' cellspacing='0' style='width:100%' class='details_id'>
			<tr>
				<td colspan='2' class='details_hd'>PAYEE:</td>
				<td class='details_hd'>CHECK NUMBER:</td>
			</tr>
			<tr>
				<td class='details_hd'>AMOUNT:</td>
				<td class='details_hd'>REFERENCE:</td>
				<td class='details_hd'>CHECK DATE:</td>
			</tr>
		</table><br>";

	echo "<table cellpadding='4' cellspacing='0' style='width:100%'>
			<tr>
				<td class='overline details_hd'>SIGNATURE OVER PRINTED NAME</td>
				<td class='overline details_hd'>DATE</td>
				<td class='overline details_hd'>O.R NUMBER</td>
			</tr>
		</table><hr>";

	echo "<table cellpadding='4' cellspacing='0' style='width:100%'>
			<tr>
				<td><h3>MEDASIA HEALTHCARE SYSTEM PHILIPPINES INC.</h3></td>
				<td class='details_hd' align='center'><h4>PAYMENT DETAILS</h4></td>
			</tr>
		</table><br>";

	echo "<table cellpadding='4' cellspacing='0' style='width:100%'>
			<tr>
				<td colspan='2' class='details_hd'>PAYEE</td>
				<td class='details_hd'>CHECK NUMBER</td>
			</tr>
			<tr>
				<td class='details_hd'>AMOUNT:</td>
				<td class='details_hd'>REFERENCE:</td>
				<td class='details_hd'>CHECK DATE:</td>
			</tr>
		</table><br>";

	echo "<table cellpadding='4' cellspacing='0' style='width:100%'>
			<tr>
				<td class='underline'>Invoice Date</td>
				<td class='underline'>Approval Code</td>
				<td class='underline'>Amount</td>
				<td class='underline'>Invoice Description</td>
				<td class='underline'>Trans Date</td>
			</tr>
		</table>";
?>