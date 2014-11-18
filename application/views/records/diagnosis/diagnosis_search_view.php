<h3>Search</h3>
<?php echo validation_errors();?>
<?php echo form_open('records/diagnosis/search');?>
<?php
	$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$inputs = array(
			array('',''),
			array(form_label('Search Diagnosis:'),form_input(array('name'=>'keyword','id'=>'keyword','size'=>'50','class'=>'form-control'))),
			array('', form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-success btn-sm')))
				);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);
?>
<?php echo form_close();?>