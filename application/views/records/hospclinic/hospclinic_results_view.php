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
<h2>Hospitals and Clinics</h2>
<?php echo validation_errors(); ?>
<?php echo form_open('records/hospclinic/multiSelect'); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);
$csv = anchor(base_url().'table_export/index/hospital','Download All Records',array('class'=>'btn btn-danger btn-xs'));
$multi = $this->table->add_row(form_submit(array('name'=>'submit','value'=>'Delete','class'=>'btn btn-danger')),form_submit(array('name'=>'submit','value'=>'Update Status','class'=>'btn btn-warning')),form_dropdown('status', array('ACCREDITED'=>'ACCREDITED','DIS-ACCREDITED'=>'DIS-ACCREDITED', 'DO NOT PROMOTE'=>'DO NOT PROMOTE')),
	form_checkbox(array('name'=>'selectAll','id'=>'selectAll', 'class'=>'selectAll')),form_label('Select All'),$csv
	// form_submit(array('name'=>'submit','value'=>'VAT AND TERMS'))
	);
echo $this->table->generate($multi);

if(isset($links))
{
	@$page = "<b>Page Results: </b>".@$links;
}
	echo @$page;

if(empty($hospclinic))
{
	echo "<h2>No Record/s Found!!!</h2>";
}
else
{
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
	$this->table->set_heading('', 'Hospital/Clinic name','Classification', 'Type', 'Branch', 'Address', 'Contact Person - Contact Number', 'Medical Coordinator', 'Medical Coordinator 2', 'Fax Number','E-mail Address', 'Category', 'Date Accredited', 'Status', 'Remarks');
	$count=1;
	foreach ($hospclinic as $value => $key)
	{
		//ATTRIBUTES FOR NEW WINDOW
		// $atts = array(
 	//              'width'      => '800',
 	//              'height'     => '600',
 	//              'scrollbars' => 'yes',
 	//              'status'     => 'yes',
 	//              'resizable'  => 'yes',
 	//              'screenx'    => '0',
 	//              'screeny'    => '0'
 	//            );
		
		// Build the custom actions links.
		// $actions = anchor(base_url()."records/hospclinic/delete/".$key['id']."/", "Delete");
		$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key['id']));
		// Adding a new table row.
		$this->table->add_row($count++.".".$selMulti, anchor(base_url()."records/hospclinic/view/".$key['id']."/", $key['name'], array('target'=>'_blank')), $key['classification'], $key['type'], $key['branch'],
		$key['street_address'].' '.$key['subdivision_village'].' '.$key['barangay'].' '.$key['city'].' '.$key['province'].' '.$key['region'], $key['contact_person'].' - '.$key['contact_number'], 
		$key['med_coor_name']."<br>Room #: ".$key['room']."<br>Schedule: ".$key['schedule']."<br>Contact No.: ".$key['contact_no']."<br>E-mail : ".$key['med_coor_email'],
		$key['med_coor_name_2']."<br>Room #: ".$key['room_2']."<br>Schedule: ".$key['schedule_2']."<br>Contact No.: ".$key['contact_no_2']."<br>E-mail : ".$key['med_coor_email_2'],
		$key['fax_number'], $key['email'], $key['category'], mdate('%M %d, %Y', mysql_to_unix($key['date_accredited'])), $key['status'], $key['remarks']);
	}
	echo $this->table->generate();
	echo form_hidden('location', 'records/hospclinic');
	echo '</div>';
	echo $page;
}
?>