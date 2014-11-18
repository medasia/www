<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Edit Password</title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
<script>
$(document).ready(function() {
	$('#patient').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/patient', //Controller where search is performed
				dataType: 'html',
				type: 'POST',
				data: req,
				success: function(data) {
					$('#results').html(data);
				}
			});
		}
	});
});
</script>
</head>

<?php echo validation_errors(); ?>
<?php echo form_open('records/members/search'); ?>
<?php
// $template = array(
// 			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
// 			'table_close'	=> '</table>'
// 			);
// $this->table->set_template($template);
// $this->table->add_row(form_label('<b> Search Test </b>', 'members'), form_input(array('name'=>'members', 'id'=>'members', 'size'=>'50')), form_label('Limit:', 'limit'), form_dropdown('limit', array('100' => '100', '300' => '300', '500' => '500')), form_submit('submit', 'Search'));
// echo $this->table->generate();
?>
<?php echo form_close();?>

<h2>Search</h2>
LEGEND:</br>
Black: Active</br>
<font color='orange'>Orange: Warning! Will expire within a week!</font></br>
<font color='green'>Green: On Hold</font></br>
<font color='red'>Red: Expired/Deleted</font></br>
<?php echo validation_errors(); ?>
<?php
$this->table->add_row(form_label('<b>Patient Name</b>', 'patient'), form_input(array('name'=>'patient', 'id'=>'patient', 'size'=>'50','class'=>'form-control','placeholder'=>'Patient Name')));
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate(); 
?>
<?php echo form_close(); ?>
<div id="results"></div>