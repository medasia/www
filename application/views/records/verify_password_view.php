<h2>VERIFY YOUR PASSWORD TO CONTINUE...</h2>
<?php echo validation_errors(); ?>
<?php echo form_open('login/verify_password'); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Password', 'password'), form_password(array('name'=>'password', 'id'=>'password', 'size'=>'20','class'=>'form-control'))),
				array('', form_submit(array('name'=>'verify','value'=>'Verifiy','class'=>'btn btn-danger')))
			 	);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);  
echo $this->table->generate($inputs);

echo form_hidden('selMulti', $_POST['selMulti']);
echo form_hidden('location', $_POST['location']);
echo form_hidden('submit', $_POST['submit']);

if(isset($_POST['status']))
{
	echo form_hidden('status', $_POST['status']);
}

if(isset($_POST['compins_id']))
{
	echo form_hidden('compins_id', $_POST['compins_id']);

	if(isset($_POST['start']))
	{
		echo form_hidden('start',$_POST['start']);
	}
	if(isset($_POST['end']))
	{
		echo form_hidden('end',$_POST['end']);
	}
}

if(isset($_POST['id']))
{
	echo form_hidden('id', $_POST['id']);
}
// var_dump($_POST);
?>
<?php echo form_close(); ?>