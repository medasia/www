<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Change Password</title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
		<script>
		$(document).ready(function()
		{
			$("form").validate({
    			rules: {
    				password: {
    					required: true
    				},
    				new_pass: {
    					required: true
    				},
    			    ver_pass: {
    			        required: true,
    			        equalTo: '#new_pass'
    			    }
    			},
    			messages: {
    				password: {
    					required: 'Please provide a username'
    				},
    				new_pass: {
    					required: 'Please provide a Name'
    				},
    			    ver_pass: {
    			        required: "Please provide a password",
    			        equalTo: 'Please verify your new password'
    			    }
    			}
			});
		});
		</script>
	</head>
<h1>Edit Password</h1>
<?php echo validation_errors(); ?>
<?php echo form_open('useraccounts/changePass/'.$id); ?>
<?php
	$inputs = array(
		array('', ''),
		array(form_label('Old Password', 'password'), form_password(array('name'=>'password','id'=>'password', 'size'=>'50', 'class'=>'form-control','placeholder'=>'Old Password'))),
		array(form_label('New Password', 'new_pass'), form_password(array('name'=>'new_pass','id'=>'new_pass', 'size'=>'50','class'=>'form-control','placeholder'=>'New Password'))),
		array(form_label('Verify Password', 'ver_pass'), form_password(array('name'=>'ver_pass','id'=>'ver_pass', 'size'=>'50', 'class'=>'form-control','placeholder'=>'Verify Password'))),
		array('', form_submit(array('value'=>'Submit','class'=>'btn btn-success')),)
		);

	$template = array(
				'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close'	=> '</table>'
				);

	$this->table->set_template($template);
	echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
</html>