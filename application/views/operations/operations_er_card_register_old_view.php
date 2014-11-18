<script>
$(document).ready(function() {
	$('#divNew').show();
	$('#divOld').hide();
	$('input[name=pick]').change(function() {
		// alert($(this).val());
		if($(this).val()=='New') {
			$('#divNew').show();
			$('#divOld').hide();
		} else {
			$('#divOld').show();
			$('#divNew').hide();
		}
	});
	$('#patient').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/operations_er_register', //Controller where search is performed
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
<?php
$this->table->add_row('Type of patient');
$this->table->add_row(form_radio(array(
											'name'        => 'pick',
											'id'          => 'pick',
											'value'       => 'New',
											'checked'     => TRUE,
											)).'New'
						,form_radio(array(
											'name'        => 'pick',
											'id'          => 'pick',
											'value'       => 'Existing',
											'checked'     => FALSE,
											)).'Existing'
						);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate(); 
?>
<div id="divOld">
<h2>Search</h2>
<?php
$this->table->add_row(form_label('Patient Name', 'patient'), form_input(array('name'=>'patient', 'id'=>'patient', 'size'=>'50')));
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate(); 
?>
<div id="results"></div>
</div>