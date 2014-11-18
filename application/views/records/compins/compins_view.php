<script>
$(document).ready(function() {
	$('#compins').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/compins', //Controller where search is performed
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
<?php echo form_open('records/compins/compinsSearch'); ?>
<?php
// $template = array(
// 			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
// 			'table_close'	=> '</table>'
// 			);
// $this->table->set_template($template);
// $this->table->add_row(form_label('<b> Search Test </b>', 'compinsSearch'), form_input(array('name'=>'compinsTest', 'id'=>'compinsTest', 'size'=>'50')), form_label('Limit:', 'limit'), form_dropdown('limit', array('100' => '100', '300' => '300', '500' => '500')), form_submit('submit', 'Search'));
// echo $this->table->generate();
?>
<?php echo form_close();?>

<h2>Search</h2>
<?php
$this->table->add_row(form_label('Company/Insurance', 'compins'), form_input(array('name'=>'compins', 'id'=>'compins', 'size'=>'50','class'=>'form-control','placeholder'=>'Company - Insurance')));
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate(); 
?>
<div id="results"></div>