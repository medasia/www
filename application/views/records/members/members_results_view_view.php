<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Members Results</title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
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
</head>

<?php echo validation_errors(); ?>
<?php echo form_open('records/members/multiSelect'); ?>
<?php

if(empty($patients))
{
	echo "<h2>No record/s found!!!</h2>";
}

$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 

date_default_timezone_set("Asia/Manila");
$date = date_default_timezone_get();
echo '<div class="table_scroll">';
$tmpl = array (
				'table_open'          => '<table border="0" cellpadding="4" cellspacing="0" class="table table-hover table-bordered" id="example">',

				'heading_row_start'   => '<tr>',
				'heading_row_end'     => '</tr>',
				'heading_cell_start'  => '<th>',
				'heading_cell_end'    => '</th>',

				'row_start'           => '<tr>',
				'row_end'             => '</tr>',
				'cell_start'          => '<td>',
				'cell_end'            => '</td>',

				// 'row_alt_start'       => '<tr>',
				// 'row_alt_end'         => '</tr>',
				// 'cell_alt_start'      => '<td>',
				// 'cell_alt_end'        => '</td>',

				'table_close'         => '</table>'
				);
$this->table->set_template($tmpl);
$this->table->set_heading('', 'Name', 'Company - Insurance', 'Date of Birth', 'Age', 'Level/Position', 'Declaration Date', 'Start', 'End', 'Membership Status', 'Cardholder Type', 'Cardholder', 'Beneficiary', 'Remarks', 'Benefit Set Name');

$count=1;
$currentDate = date('Y-m-d');
foreach ($patients as $value => $key)
{
	if(strtolower($key['status']) == "active")
	{
		$newdate = strtotime('-7 day', strtotime($key['end']));
		$newdate = date('Y-m-d', $newdate);
		$expires = (strtotime($key['end']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24);

		if($expires > 1)
		{
			$day = " days";
		}
		else
		{
			$day = " day";
		}

		if($expires < 0)
		{
			$id = $key['id'];
			$field = 'status';
			$data = "EXPIRED";
			$key['status'] = status_update('patient',$field,$data,$id);
		}

		if($newdate <= $currentDate)
		{ // WARNING
			$color = 'orange';
			$key['status'] = $key['status']." - will expire in ".$expires.$day.".";
		}
		else
		{ // ACTIVE
			$color = 'black';
		}
	}
	elseif (strtolower($key['status']) == "expired" || strtolower($key['status']) == "deleted")
	{ // EXPIRED/DELETED
		$color = 'red';
	}
	else
	{ //ON HOLD OR LACK OF INFO
		$color = 'green';
	}

	if($key['compins'][0]['company'] == "" || $key['compins'][0]['insurance'] == "" || is_null($key['compins'][0]))
	{
		$compinsCI = "<font color=".$color.">"." No existing Company - Insurance ";
	}
	else
	{
		$compinsCI = $key['compins'][0]['company']." - ".$key['compins'][0]['insurance'];
	}

	// Build the custom actions links.
	// $actions = anchor(base_url()."records/members/delete/".$key['id']."/", "Delete");
	// Adding a new table row.
	$this->table->add_row("<font color=".$color.">".$count++.".", "<font color=".$color.">".$key['lastname'].", ".$key['firstname']." ".$key['middlename'],$compinsCI, "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['dateofbirth'])), "<font color=".$color.">".computeAge($key['dateofbirth']), 
		"<font color=".$color.">".$key['level'], "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['declaration_date'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['start'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['end'])), "<font color=".$color.">".$key['status'], 
		"<font color=".$color.">".$key['cardholder_type'], "<font color=".$color.">".$key['cardholder'], "", "<font color=".$color.">".$key['remarks'], "<font color=".$color."><b>".$key['benefit_name'][0]['benefit_set_name'].'</b><br>Remaining Overall MBL: <b>PHP. '.$key['overall_mbl'].'</b>');
}
echo $this->table->generate();
echo '</div>';
echo form_hidden('location', 'records/members');
?>