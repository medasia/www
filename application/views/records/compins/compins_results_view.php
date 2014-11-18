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


<?php echo validation_errors(); ?>
<?php echo form_open('records/compins/multiSelect'); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
$multi = $this->table->add_row(form_submit(array('name'=>'submit','value'=>'Delete','class'=>'btn btn-danger')),form_checkbox(array('name'=>'selectAll','id'=>'selectAll', 'class'=>'selectAll')),form_label('Select All'),
						form_label('<b><font color="red">WARNING!!! Deleting of Company - Insurance will proceed on deleting its current members!!!'));
echo $this->table->generate($multi);
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
$this->table->set_heading('', 'Company', 'Insurance','Notes / Remarks', 'Start', 'End', 'Members');
$count=1;
foreach ($compins as $value => $key) {
	// Build the custom actions links.
	// $actions = anchor(base_url()."records/compins/delete/".$key['id']."/", "Delete");
	$members = anchor(base_url()."records/compins/members/".$key['id'], "Members(".$key['membercount'].")");
	$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key['id']));
	$edit = anchor(base_url()."records/compins/edit/".$key['id'], "Edit", array('class'=>'btn btn-xs btn-warning','target'=>'_blank'));
	// Adding a new table row.
	$data = $this->table->add_row($count++.".".$selMulti.' '.$edit, $key['company'], $key['insurance'], $key['notes'], mdate('%M %d, %Y', mysql_to_unix($key['start'])), mdate('%M %d, %Y', mysql_to_unix($key['end'])), 
		$members);
}
echo $this->table->generate();
echo form_hidden('location', 'records/compins');
echo '</div>';
?>