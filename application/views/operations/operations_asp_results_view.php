<?php
echo '<div class="table_scroll">';
$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

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
$this->table->set_heading('', 'Hospital/Clinic Name','Classification','Type', 'Branch', 'Address', 'Contact Person - Contact Number','Medical Coordinator','Medical Coordinator2','Fax Number','E-Mail Address', 'Category', 'Date Accredited', 'Status', 'Remarks');
$count=1;
foreach ($operations_asp as $value => $key) {
	// Adding a new table row.
	$this->table->add_row($count++.".", $key['name'], $key['classification'],$key['type'], $key['branch'],
	$key['street_address'].' '.$key['subdivision_village'].' '.$key['barangay'].' '.$key['city'].' '.$key['province'], $key['contact_person'].'<br />'.$key['contact_number'],  
	$key['med_coor_name']."<br><b>Room #:</b>".$key['room']."<br><b>Schedule:</b>".$key['schedule']."<br><b>Contact No.:</b>".$key['contact_no'],
	$key['med_coor_name_2']."<br><b>Room #:</b>".$key['room_2']."<br><b>Schedule:</b>".$key['schedule']."<br><b>Contact No.:</b>".$key['contact_no_2'],
	$key['fax_number'], $key['email'], $key['category'], mdate('%M %d, %Y', mysql_to_unix($key['date_accredited'])), $key['status'], $key['remarks']);
}
echo $this->table->generate();
echo "</div>";
?>