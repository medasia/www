<script>
$(document).ready(function()
{
	$('#formAdddd').hide();
	$('#toggleSlideee').click(function()
	{
		$('#formAdddd').slideToggle('fast', function() {});
	});
});
</script>

<!-- VIEW SET OF BENEFITS CREATED -->
<br>
<button id='toggleSlideee' class="btn btn-default">View Schedule of Benefits</button>
<div id='formAdddd'>
<h2>Registered Schedule of Benefits</h2>
<?php
	echo '<div class="table_scroll">';
	$template = array(
				'table_open'			=> '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">',

				'heading_row_start'		=> '<tr>',
				'heading_row_end'		=> '</tr>',
				'heading_cell_start'	=> '<th>',
				'heading_cell_end'		=> '</th>',

				'row_start'				=> '<tr>',
				'row_end'				=> '</tr>',
				'cell_start'			=> '<td>',
				'cell_end'				=> '</td>',

				'row_alt_start'			=> '<tr>',
				'row_alt_end'			=> '</tr>',
				'cell_alt_start'		=> '<td>',
				'cell_alt_end'			=> '</td>',

				'table_close'			=> '</table>'
				);
	$this->table->set_template($template);
	$this->table->set_heading('', 'Benefit Schedule Name','Benefit Plan Type','Benefit Limit Type' ,'Company - Insurance', 'Cardholder Type','Level','Other Conditions','Exclusions','Members','Date Created','User','');
	$count=1;

	foreach($info as $key => $value)
	{
		$delete = anchor(base_url()."records/benefits/deleteBenefitSet/".$value['id']."/".$value['benefit_set_name']."/", "Delete Benefit Set", array('class'=>'btn btn-danger btn-xs'));
		$members = anchor(base_url()."records/benefits/viewMembers/".$value['id']."/", "View <b>".$memberCount[$key]."</b> Members");
		$this->table->add_row($count++.".", anchor(base_url()."records/benefits/view/".$value['id']."/", $value['benefit_set_name']),$value['plan_type'],$value['benefit_limit_type'] ,$compinsname[$key], $value['cardholder_type'], $value['level'],
								'<b>'.$value['condition_name'].'</b>','<b>'.$value['exclusion_name'].'</b>',$members, mdate('%M %d, %Y - %h:%i %a', mysql_to_unix($value['date_created'])),$value['user'], $delete);
	}
	echo $this->table->generate();
	echo '</div>';
?>
<br><br>