<script>
$(document).ready(function() {
	$('#dentistsdoctors').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/dentistsdoctors', //Controller where search is performed
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

<?php echo validation_errors(); ?>
<?php echo form_open('records/dentistsdoctors/search'); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);
$this->table->add_row(form_label('<b> Search Test </b>', 'dentanddoc'), form_input(array('name'=>'dentanddoc', 'id'=>'dentanddoc', 'size'=>'50')), form_label('Limit:', 'limit'), form_dropdown('limit', array('100' => '100', '300' => '300', '500' => '500')), form_submit('submit', 'Search'));
echo $this->table->generate();
?>
<?php echo form_close();?>

<h2>Search</h2>
<?php echo validation_errors(); ?>
<?php echo form_open('records/dentistsdoctors/search'); ?>
<?php
$this->table->add_row(form_label('Dentist/Doctor Name', 'dentistsdoctors'), form_input(array('name'=>'dentistsdoctors', 'id'=>'dentistsdoctors', 'size'=>'50')));
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);
echo $this->table->generate();
?>
<?php echo form_close(); ?>
<div id="results"></div>