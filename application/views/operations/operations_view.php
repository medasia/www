<script>
$(document).ready(function() {
	// $('#filter_on').hide();
	$('#filtered_field').hide();
	$('#er_filter').hide();
	$('#keyword').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/operations_'+$('#search_for').val(), //Controller where search is performed
				dataType: 'html',
				type: 'POST',
				data: req,
				success: function(data) {
					$('#results').html(data);
				}
			});
		}
	});
	$('#search_for').change(function() {
		$('#keyword').val('');
		$('#results').empty();

		if($('#search_for').val() == 'patient')
		{
			$('#filter_on').show();
			$('#er_filter').hide();
			$('#keyword').attr('placeholder','Patient Name').placeholder();
		}
		if($('#search_for').val() == 'er_card')
		{
			$('#filter_on').show();
			$('#filtered_field').hide();
			$('#keyword').attr('placeholder','Card Number').placeholder();
		}
		if($('#search_for').val() == 'asp')
		{
			$('#filter_on').hide();
			$('#filtered_field').hide();
			$('#er_filter').hide();
			$('#keyword').attr('placeholder','Hospital/Clinic Name').placeholder();
		}
		if($('#search_for').val() == 'dnd')
		{
			$('#filter_on').hide();
			$('#filtered_field').hide();
			$('#er_filter').hide();
			$('#keyword').attr('placeholder','Dentists/Doctors Name').placeholder();
		}
		if($('#search_for').val() == 'special_verifications')
		{
			$('#filter_on').hide();
			$('#filtered_field').hide();
			$('#er_filter').hide();
			$('#keyword').attr('placeholder','Special LOA Code').placeholder();
		}
		else
		{
			$('#filter_on').hide();
			$('#filtered_field').hide();
			$('#er_filter').hide();
			$('#keyword').attr('placeholder','Patient Name').placeholder();
		}
	});
	$('#filter_on').click(function()
	{
		if($('#search_for').val() == 'patient')
		{
			$('#filtered_field').slideToggle('fast', function() {});
			$('#er_filter').hide();
		}
		if($('#search_for').val() == 'er_card')
		{
			$('#er_filter').slideToggle('fast',function() {});
			$('#filtered_field').hide();
		}
	});
});
</script>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Operations</title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
	</head>
<h2>Search</h2>
<?php
if($this->session->flashdata('result') != '')
{
	echo $this->session->flashdata('result');
}
$this->table->add_row(form_label('Search for', 'search_for'), form_dropdown('search_for', array('patient'=>'Members','er_card'=>'ER Cards','asp'=>'Affiliated Service Providers',
	'dnd'=>'Dentists and Doctors','admission'=>'Patient Admission Report','monitoring'=>'Monitoring Report','verifications'=>'Verifications','special_verifications'=>'Special Verifications'), '','id="search_for"'));
$this->table->add_row(form_label('Keyword', 'keyword'), form_input(array('name'=>'keyword', 'id'=>'keyword', 'size'=>'50','class'=>'form-control','placeholder'=>'Patient Name')),
	form_button(array('name'=>'filter_on','id'=>'filter_on','class'=>'btn btn-info','content'=>'Filtered Search')));
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate();
?>

<?php echo validation_errors();?>
<?php echo form_open('operations/memberssearch');?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$filtered_form = array(
				array(form_label('First Name:'),form_input(array('name'=>'firstname','id'=>'firstname','size'=>'20','placeholder'=>'First Name','class'=>'form-control')),
					form_label('Middle Name:'),form_input(array('name'=>'middlename','id'=>'middlename','size'=>'20','placeholder'=>'Middle Name','class'=>'form-control')),
					form_label('Last Name:'),form_input(array('name'=>'lastname','id'=>'lastname','size'=>'20','placeholder'=>'Last Name','class'=>'form-control'))),
				array(form_label('Company:'),form_input(array('name'=>'company','id'=>'company','size'=>'20','placeholder'=>'Company Name','class'=>'form-control')),
					form_label('Insurance:'),form_input(array('name'=>'insurance','insurance','size'=>'20','placeholder'=>'Insurance Name','class'=>'form-control')),''),
				array(form_label('Cardholder Type:'),form_dropdown('cardholder_type',array(''=>'All','PRINCIPAL'=>'PRINCIPAL','DEPENDENT'=>'DEPENDENT')),
					form_label('Status:'),form_dropdown('status',array(''=>'All','ACTIVE'=>'ACTIVE','EXPIRED'=>'EXPIRED','DELETED'=>'DELETED','ON HOLD'=>'ON HOLD')),
					form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);
	$filtered = form_fieldset('<b>Filtered Search</b>',array('id'=>'filtered_field'));
	$filtered.= $this->table->generate($filtered_form);
	$filtered.= form_fieldset_close();

	$input = array(array($filtered));
	$this->table->set_template($template);
	echo $this->table->generate($input);

?>
<?php echo form_close();?>

<?php echo validation_errors();?>
<?php echo form_open('operations/searchER');?>
<?php
	$template = array(
				'table_open' => '<table cellpadding="4" cellspacing="0" border="0">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);

	$er_filter = array(
				array(form_label('Firstname'),form_input(array('name'=>'firstname','id'=>'firstname','class'=>'form-control','size'=>'20')),
					form_label('Middlename'),form_input(array('name'=>'middlename','id'=>'middlename','class'=>'form-control','size'=>'20')),
					form_label('Lastname'),form_input(array('name'=>'lastname','id'=>'lastname','class'=>'form-control','size'=>'20')),
					form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$filtered = form_fieldset('<b>Search ER Card By Name</b>',array('id'=>'er_filter'));
	$filtered.= $this->table->generate($er_filter);
	$filtered.= form_fieldset_close();

	$input = array(array($filtered));
	$this->table->set_template($template);
	echo $this->table->generate($input);
?>
<?php echo form_close();?>
<div id="results"></div>