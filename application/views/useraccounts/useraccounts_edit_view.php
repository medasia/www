<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Edit User Account</title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
		<script>
		$(document).ready(function()
		{
			jQuery.validator.addMethod('regEx', function(value,element){
				var regex = new RegExp("^[a-zA-Z0-9]+$");
				var key = value;
				if(!regex.test(key))
				{
					return false;
				}
				return true;
			});
			$("form").validate({
    			rules: {
    				username: {
    					required: true,
    					regEx: true
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
    					regEx: 'Only alphanumeric keys are allowed'
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
<h2>Edit <?=$name;?></h2>
<?php
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<?php echo validation_errors(); ?>
<?php echo form_open('useraccounts/update/'.$id); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Username', 'username'), form_input(array('name'=>'username', 'value'=>$username,'id'=>'username', 'size'=>'20','class'=>'form-control'))),
				array(form_label('Name', 'name'), form_input(array('name'=>'name', 'value'=>$name, 'id'=>'name', 'size'=>'20','class'=>'form-control'))),
				array(form_label('Password', 'password'), form_password(array('name'=>'password', 'id'=>'password', 'size'=>'20','class'=>'form-control','placeholder'=>'Password'))),
				array(form_label('Access', 'access'), form_dropdown('access', array('user' => 'User', 'admin' => 'Admin'), $access)),
				array(form_label('Usertype', 'usertype'), form_dropdown('usertype', array('ops' => 'Ops', 'claims' => 'Claims', 'admin_assoc' => 'Admin Associate',
					'accre' => 'Accreditation', 'sysad' => 'System Admin', 'accounting' => 'Accounting'), $usertype)),
				array(anchor('useraccounts', 'Cancel',array('class'=>'btn btn-warning')), form_submit(array('value'=>'Update','class'=>'btn btn-success')))
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