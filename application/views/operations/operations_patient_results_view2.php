<?php
echo '<div class="table_scroll">';
$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',

				'heading_row_start'   => '<tr>',
				'heading_row_end'     => '</tr>',
				'heading_cell_start'  => '<th>',
				'heading_cell_end'    => '</th>',

				'row_start'           => '<tr>',
				'row_end'             => '</tr>',
				'cell_start'          => '<td>',
				'cell_end'            => '</td>',

				'row_alt_start'       => '<tr>',
				'row_alt_end'         => '</tr>',
				'cell_alt_start'      => '<td>',
				'cell_alt_end'        => '</td>',

				'table_close'         => '</table>'
				);

$this->table->set_template($tmpl);
$this->table->set_heading('', 'Name', 'Company', 'Insurance', 'Date of birth', 'Age', 'Level/Position', 'Declaration date', 'Start', 'End', 'Membership Status', 'Cardholder Type', 'Cardholder', 'Beneficiary', 'Remarks', 'Benefit Set Name');
$count=1;
foreach ($patients as $value => $key) {
	// Build the custom actions links.
	// $actions = anchor_popup(base_url()."verifications/newLOA/".$key['id']."/", "Add verification");
	// Adding a new table row.
	$this->table->add_row($count++.".", $key['lastname'].", ".$key['firstname']." ".$key['middlename'], $key['compins'][0]['company'], $key['compins'][0]['insurance'], mdate('%M %d, %Y', mysql_to_unix($key['dateofbirth'])), computeAge($key['dateofbirth']), 
		$key['level'], mdate('%M %d, %Y', mysql_to_unix($key['dateofdeclaration'])), mdate('%M %d, %Y', mysql_to_unix($key['datestart'])), mdate('%M %d, %Y', mysql_to_unix($key['dateend'])), $key['status'], 
		$key['cardholder_type'], $key['cardholder'], "", $key['remarks'], $key['benefit_name'][0]['benefit_set_name']);
}
echo $this->table->generate();
echo '</div>';
?>