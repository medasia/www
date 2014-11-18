<script>
$(document).ready(function() {
	$('#hospclinic').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/hospclinic', //Controller where search is performed
				dataType: 'html',
				type: 'POST',
				data: req,
				success: function(data) {
					$('#results').html(data);
				}
			});
		}
	});
	$('#formAddd').hide();
	$('#toggleSlidee').click(function()
	{
		$('#formAddd').slideToggle('fast', fun                                                                                                                                                                                                                                                                                                                                                                                                     ction() {});
	});
});
</script>

<h2>Search</h2>
<?php echo validation_errors(); ?>
<?php echo form_open('records/hospclinic/search'); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);
$this->table->add_row(form_label('<b>Hospital Name:</b>', 'hospital'), form_input(array('name'=>'hospital', 'id'=>'hospital', 'size'=>'50')), form_label('Limit:', 'limit'), form_dropdown('limit', array('100' => '100', '300' => '300', '500' => '500')), form_submit('submit', 'Search'));
echo $this->table->generate();
?>
<?php echo form_close();?>

<button id='toggleSlidee'>Optional Search</button>
<div id='formAddd'>
<?php echo validation_errors(); ?>
<?php echo form_open('records/hospclinic/optionalSearch'); ?>
<?php
$template = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
$this->table->set_template($template);
$this->table->add_row(form_label('<b>Branch:</b>', 'branch'), form_input(array('name'=>'branch', 'id'=>'branch','size'=>'20')));
$this->table->add_row(form_label('<b>Address:</b>', 'address'), form_input(array('name'=>'address', 'id'=>'address', 'size'=>'20')));
$this->table->add_row(form_label('<b>Province:</b>', 'province'), form_input(array('name'=>'province', 'id'=>'province', 'size'=>'20')));
$this->table->add_row(form_label('<b>Region</b>', 'region'), form_input(array('name'=>'region', 'id'=>'region','size'=>'20')));
$this->table->add_row(form_label('<b>Limit</b>', 'limit'), form_dropdown('limit', array('100'=>'100', '300'=>'300', '500'=>'500')));
$this->table->add_row('', form_submit('submit', 'Search'));

echo $this->table->generate();
?>
<?php echo form_close();?>
</div>

<?php echo validation_errors(); ?>
<?php echo form_open('utils/autocomplete/from/hospclinic'); ?>
<?php
// $this->table->add_row(form_label('Hospital/Clinic Name', 'hospclinic'), form_input(array('name'=>'hospclinic', 'id'=>'hospclinic', 'size'=>'50')));
// $template = array(
// 			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
// 			'table_close'	=> '</table>'
// 			);
// $this->table->set_template($template); 
// echo $this->table->generate(); 
?>
<?php echo form_close(); ?>
<div id="results"></div>