<h2>VERIFY YOUR PASSWORD TO CONTINUE...</h2>
<?php echo validation_errors(); ?>
<?php echo form_open('login/verify_pass');?>
<?php
	$inputs = array(
				array('',''),
				array(form_label('Password'),form_password(array('name'=>'password','size'=>'20','placeholder'=>'Password','class'=>'form-control'))),
				array('', form_submit(array('name'=>'sumbit','value'=>'Verify','class'=>'btn btn-success btn-sm')))
				);
	$template = array(
				'table_open' => '<table border="0" cellspacing="0" cellpadding="4">',
				'table_close' => '</table>'
				);
	$this->table->set_template($template);
	echo $this->table->generate($inputs);

	echo form_hidden('id',$id);
	echo form_hidden('field',$field);
	echo form_hidden('location',$location);
?>