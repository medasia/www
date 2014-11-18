<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Operations</title>
		<script src="http://code.jquery.com/jquery.js"></script><!-- script for jQuery Core -->
		<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script><!-- script for jQuery UI -->
		<script src="<?php echo base_url();?>includes/js/jquery.validate.js"></script> <!--jQuery Form Validator-->
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
		<link href="<?php echo base_url();?>includes/main-style.css" rel="stylesheet">

		<script>
		$(document).ready(function()
		{
			$("form").validate({
    				rules: {
    				username: {
    					required: true
    				},
    				password: {
    					required: true
    				}
    			},
    			messages: {
    				username: {
    					required: 'Username is required'
    				},
    				password: {
    					required: 'Password is required'
    				}
    			}
			});
		});
		</script>
	</head>

	<body>
		<div align="center" class="form-group" id="login_container">
			<div id="logo-image">
				<img width="500px" src="<?php echo base_url();?>includes/images/Logo.png">
			</div>
			<h4>Availment System * New</h4>
			<?php echo validation_errors(); ?>
			<?php echo form_open('login'); ?>
			<?php
				echo '<div name="login_form">';
					$inputs = array(
						array(form_input(array('name'=>'username', 'id'=>'username', 'size'=>'20', 'placeholder'=>'Enter Username','class'=>'form-control')),''),
						array(form_password(array('name'=>'password', 'id'=>'password', 'size'=>'20', 'placeholder'=>'Enter Password','class'=>'form-control','data-msg-required'=>"The RequiredDateDemo field is required." )),''),
						// array('<center>'.form_submit(array('value'=>'Login','class'=>'btn btn-success')).'</center>'),
						array('<center><button class="btn btn-success" type="submit" value="Login" name="submit"></input><span class="glyphicon glyphicon-user"></span> Login</button></center'),
					 	);
					echo $this->table->generate($inputs);
				echo '</div>';
			?>
			<?php echo form_close(); ?>
		</div>
	</body>
</html>