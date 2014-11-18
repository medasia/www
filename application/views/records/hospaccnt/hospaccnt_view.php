<script>
$(document).ready(function() {
	$('#hospaccnt').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/hospaccnt', //Controller where search is performed
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
<h2>Search</h2>
<?php
$this->table->add_row(form_label('Account Name', 'hospaccnt'), form_input(array('name'=>'hospaccnt', 'id'=>'hospaccnt', 'size'=>'50')));
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate(); 
?>
<div id="results"></div>