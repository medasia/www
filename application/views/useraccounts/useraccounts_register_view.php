<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - User Account</title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
		<script>
		$(document).ready(function()
		{
			jQuery.validator.addMethod('regEx', function(value, element){ //REGEX VALIDATION FOR USERNAME alphanumeric keys only
				var regex = new RegExp("^[a-zA-Z0-9]+$");
				var key = value;
				if(!regex.test(key))
				{
					return false;
				}
				return true;
			});
			$("form#register").validate({
    			rules: {
    				username: {
    					required: true,
    					regEx: true,
    					remote: {
    						url: '<?=base_url();?>useraccounts/check_exist/users_new/username',
    						type: 'POST',
    						data: {
    							term: function()
    							{
    								return $('#username').val();
    							}
    						}
    					}
    				},
    				name: {
    					required: true
    				},
    			    password: {
    			        required: true
    			    }
    			},
    			messages: {
    				username: {
    					required: 'Please provide a username',
    					regEx: 'Only alphanumeric keys are allowed',
    					remote: 'Username already exist'
    				},
    				name: {
    					required: 'Please provide a Name'
    				},
    			    password: {
    			        required: "Please provide a password"
    			    }
    			}
			});
		});
		</script>
	</head>
<h1>User Accounts</h1>
<?php echo validation_errors(); ?>
<?php echo form_open('useraccounts/register',array('id'=>'register')); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Username', 'username'), form_input(array('name'=>'username', 'id'=>'username', 'size'=>'20','placeholder'=>'Username','class'=>'form-control'))),
				array(form_label('Name', 'name'), form_input(array('name'=>'name', 'id'=>'name', 'size'=>'20','class'=>'form-control','placeholder'=>'Name'))),
				array(form_label('Password', 'password'), form_password(array('name'=>'password', 'id'=>'password', 'size'=>'20','class'=>'form-control','placeholder'=>'Password'))),
				array(form_label('Access', 'access'), form_dropdown('access', array('user' => 'User', 'admin' => 'Admin',))),
				array(form_label('Usertype', 'usertype'), form_dropdown('usertype', array('ops' => 'Ops', 'claims' => 'Claims', 'admin_assoc' => 'Admin Associate',
					'accre' => 'Accreditation', 'sysad' => 'System Admin','accounting' => 'Accounting'))),
				array('', form_submit(array('value'=>'Register','class'=>'btn btn-success')))
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