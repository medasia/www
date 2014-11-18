<script>
$(document).ready(function() {
	$('#date_start').datepicker({format: 'yyyy-mm-dd'});
	$('#date_end').datepicker({format: 'yyyy-mm-dd'});

	$('#payable_id').hide();

	$('#payable').click(function(){
		$('#payable_id').slideToggle('fast',function(){});
	});
});
</script>
<html>
<head>
	<title>Medriks - Accounting</title>
</head>
<body>
<h1>Accounting</h1>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}

	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$buttons = array(
			array(form_button(array('name'=>'payable','id'=>'payable','content'=>'Accounts Payable','class'=>'btn btn-info')),
			form_button(array('name'=>'print_voucher','id'=>'print_voucher','content'=>'Printed Voucher','class'=>'btn btn-info')),
			form_button(array('name'=>'released_voucher','id'=>'released_voucher','content'=>'Released Voucher','class'=>'btn btn-info')))
			);
	$this->table->set_template($tmpl);
	echo $this->table->generate($buttons);
?>

<?php echo validation_errors();?>
<?php echo form_open('accounting/search');?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$inputs = array(
			array(form_label('Search Account:'),form_dropdown('table',array('hospital'=>'Hospital','dentistsanddoctors'=>'Dentists and Doctors'))),
			array('',form_input(array('name'=>'keyword','id'=>'keyword','size'=>'20','placeholder'=>'Enter Keyword','class'=>'form-control'))),
			array(form_label('Date From:'),form_input(array('name'=>'date_start','id'=>'date_start','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
			array(form_label('Date To:'),form_input(array('name'=>'date_end','id'=>'date_end','size'=>'20','placeholder'=>'YYYY-MM-DD','class'=>'form-control'))),
			array('',form_label('Limit').' '.form_dropdown('limit',array('100'=>'100','300'=>'300','500'=>'500','500000'=>'All')).' '.form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success')))
				);
	$this->table->set_template($template);
	$payable_fieldset = form_fieldset('<b>Accounts Payable</b>', array('id'=>'payable_id'));
	$payable_fieldset.= $this->table->generate($inputs);
	$payable_fieldset.= form_fieldset_close();

	$input = array(array($payable_fieldset));
	echo $this->table->generate($input);
?>
<?php echo form_close();?>
</body>