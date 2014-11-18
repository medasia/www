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
	.members_table
	{
		-moz-column-count: 3;
		-moz-column-gap: 20px;
		-moz-row-count:2;
		-webkit-column-count: 3;
		-webkit-column-gap: 20px;
		-webkit-row-count:2;
		row-count:2;
	}
	.three-col {
       -moz-column-count: 2;
       -moz-column-gap: 20px;
       -webkit-column-count: 2;
       -webkit-column-gap: 20px;
}
</style>

<?php
	//DETAILS Table
	$details_table = '
						<table cellpadding="1" cellspacing="0">';
	$details_table.= '
							<tr>
								<td>Date</td>
								<td> : </td>
								<td>November 3, 2014</td>
							</tr>
							<tr>
								<td>To</td>
								<td> : </td>
								<td>Mr. Daniel Padilla</td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td><i>Artista</i></td>
							</tr>
							<tr>
								<td>From</td>
								<td> : </td>
								<td>Medasia Philippines, Inc.</td>
							</tr>
							<tr>
								<td>Account Name</td>
								<td> : </td>
								<td>MEDRIKS</td>
							</tr>
							<tr>
								
							</tr>
					 ';
	$details_table.= '</table>';

	//Members Table
	$count = 1;
	$members_table = '<table cellpadding="1" cellspacing="0" class="members_table">';
							

							// var_dump($patients);
							foreach ($patients as $key => $value)
							{
								$members_table.= '<tr>
									<td>'.$count++.'.'.'</td>
									<td>'.$value[0]['firstname']." ".$value[0]['middlename']." ".$value[0]['lastname'].'</td>
								</tr>';

							}

	$members_table.= '</table>';

	// DISPLAY ALL TABLE
	echo '<table cellpadding="4" cellspacing="0" id="top_table" style="width:100%">
			<tr>
				<td>
					<img width="200px" src="/home/dev/web/operations051513/includes/images/Logo2.jpg">
				</td>
			<tr>
		</table>';

	echo '<table cellpadding="4" cellspacing="0" style="width:100%">
			<tr>
				<td>'.$details_table.'</td>
			</tr>
		</table>';

	echo '<hr>';

	echo '<br><br>

			<table cellpadding="2" cellspacing="0" class="members_table" style="width:100%" align="center">
				<tr>
					<td colspan="100%">'; echo $members_table.'</td>
				</tr>
				
			</table>';
?>