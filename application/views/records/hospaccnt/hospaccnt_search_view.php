<h2>Search Hospital Accounts</h2>
<?php echo validation_errors();?>
<?php echo form_open('records/hospaccnt/search');?>
<?php
	if($this->session->flashdata('result') != '')
	{
		echo $this->session->flashdata('result');
	}
	
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
				);
	$inputs = array(
			array(form_label('Search: '), form_input(array('name'=>'keyword','id'=>'keyword','class'=>'form-control','placeholder'=>'Enter Keyword','size'=>'20'))),
			array(form_label('Limit').' '.form_dropdown('limit', array('100'=>'100', '300'=>'300', '500'=>'500','500000'=>'All')),form_submit(array('name'=>'submit','value'=>'Search Hospital','class'=>'btn btn-success')).' '.form_submit(array('name'=>'submit','value'=>'Search Doctors','class'=>'btn btn-success')))
				);
	$this->table->set_template($tmpl);
	echo $this->table->generate($inputs);
?>
<?php echo form_close();?>