<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
	</head>
	<h2>Account Details</h2>
<?php
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
echo "<div class='table_scroll'>";
$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">',

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
$this->table->set_heading('', 'Username', 'Name', 'Access', 'Department', '');
$count=1;
foreach ($users_new as $value => $key) {
	// Build the custom actions links.
	$actions = anchor(base_url()."useraccounts/edit/".$key['id']."/", "Edit",array('class'=>'btn btn-warning')) . " " . anchor(base_url()."useraccounts/delete/".$key['id']."/", "Delete",array('class'=>'btn btn-danger'));
	switch($key['usertype']) {
		case 'ops':
			$key['usertype'] = "Operations";
			break;
		case 'claims':
			$key['usertype'] = "Claims";
			break;
		case 'admin_assoc':
			$key['usertype'] = "Admin Associate";
			break;
		case 'accre':
			$key['usertype'] = "Accreditation";
			break;
		case 'sysad':
			$key['usertype'] = "System Admin";
			break;
	}
	// Adding a new table row.
	$this->table->add_row($count++.".", $key['username'], $key['name'], $key['access'], $key['usertype'], $actions);
}
echo $this->table->generate();
echo "</div>";
?>
</html>