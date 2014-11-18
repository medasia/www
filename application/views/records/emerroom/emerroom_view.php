<script>
$(document).ready(function() {
	$('#emerroom').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/emerroom', //Controller where search is performed
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
$this->table->add_row(form_label('ER Card #', 'emerroom'), form_input(array('name'=>'emerroom', 'id'=>'emerroom', 'size'=>'50','class'=>'form-control','placeholder'=>'Enter ER Number')));
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate(); 
?>
<div id="results"></div>