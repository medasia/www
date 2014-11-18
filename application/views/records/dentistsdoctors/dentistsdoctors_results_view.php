<script>
$(document).ready(function() {
	$(".selectAll").click(function() {
		var checked_status = this.checked;
		$(".selMulti").each(function() {
			this.checked = checked_status;
		});
	});
});
</script>
<h2>Dentists and Doctors</h2>
<?php echo validation_errors(); ?>
<?php echo form_open('records/dentistsdoctors/multiSelect'); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);
$multi = $this->table->add_row(form_submit(array('name'=>'submit','value'=>'Delete','class'=>'btn btn-danger')),form_submit(array('name'=>'submit','value'=>'Update Status','class'=>'btn btn-warning')),form_dropdown('status', array('ACCREDITED'=>'ACCREDITED','DIS-ACCREDITED'=>'DIS-ACCREDITED')),
	form_checkbox(array('name'=>'selectAll','id'=>'selectAll', 'class'=>'selectAll')),form_label('Select All')
	// form_submit(array('name'=>'submit','value'=>'VAT and TERMS'))
	);
echo $this->table->generate($multi);


if(isset($links))
{
	$page = "<b>Page Results: </b>".@$links;
}
echo $page;
echo "<div class='table_scroll'>";
foreach($dentistsdoctors as $cvalue => $ckey)
{
	$template = array(
				'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-hover table-bordered">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);
	$this->table->set_heading('Clinic Address', 'Clinic Address', 'Clinic Address', 'Clinic Address', 'Clinic Address');
	$this->table->add_row($ckey['clinic1'], $ckey['clinic2'], $ckey['clinic3'],$ckey['clinic4'],$ckey['clinic5']);
	$clinics[$cvalue] = $this->table->generate();
}

if(empty($dentistsdoctors))
{
	echo "<h2>No Record/s Found!!!</h2>";
}
else
{
	$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-hover table-bordered">',

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
	$this->table->set_heading('', 'Type', 'Name (LN, FN MN)', 'Specialization', 'Clinic/s', 'Mobile #', 'Contact #', 'Fax #','E-mail Address', 'Date Accredited', 'Status', 'Remarks');
	$count=1;
	foreach($dentistsdoctors as $value => $key)
	{
		$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key['id']));
		// Adding a new table row.
		$this->table->add_row($count++.".".$selMulti, $key['type'],
			anchor(base_url()."records/dentistsdoctors/view/".$key['id']."/",$key['lastname'].', '.$key['firstname'].' '.$key['middlename'], array('target'=>'_blank')), $key['specialization'], $clinics[$value],
			$key['mobile_number'], $key['contact_number'], $key['fax_number'], $key['email'], mdate('%M %d, %Y', mysql_to_unix($key['date_accredited'])),
			$key['status'], $key['remarks']);
	}
	echo $this->table->generate();
	echo form_hidden('location', 'records/dentistsdoctors');
	echo '</div>';
	echo $page;
}
?>