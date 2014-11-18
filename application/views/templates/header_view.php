<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<link href="<?php echo base_url();?>includes/main-style.css" rel="stylesheet"> <!-- MAIN CSS -->
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
		<link href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/><!-- script for jQuery's CSS -->
		<script src="http://code.jquery.com/jquery.js"></script><!-- script for jQuery Core -->
		<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script><!-- script for jQuery UI -->
		<script src="<?php echo base_url();?>includes/js/jquery.js"></script><!-- script for jQuery Core -->
		<script src="<?php echo base_url();?>includes/js/jquery-ui.js"></script><!-- script for jQuery UI -->
		<script src="http://www.appelsiini.net/download/jquery.jeditable.js"></script><!-- script for jEditable -->
		<script src="<?php echo base_url();?>includes/js/jquery.validate.js"></script> <!--jQuery Form Validator-->
		<script src="<?php echo base_url();?>includes/js/jquery.limit.js"></script> <!-- jQuery character limit and counter -->
		<script src="https://raw.github.com/qertoip/jeditable-datepicker/master/src/jquery.jeditable.datepicker.js"></script><!--script for jEditable + jQuery's Datepicker -->
		
		<!-- new datepicker -->
		<link href="<?php echo base_url();?>datepicker/css/datepicker.css" rel="stylesheet">
		<script src="<?php echo base_url();?>datepicker/js/bootstrap-datepicker.js"></script>

		<!-- CHOSEN JS AND CSS -->
		<script src="<?php echo base_url();?>chosen/chosen.jquery.js"></script>
		<link href="<?php echo base_url();?>chosen/chosen.css" rel="stylesheet">

		<script>
		$(document).ready(function(){
			//FOR FLEXIBLE SCREEN WIDTH
			var screen_width = screen.width;
			$('body').css({
				'min-width' : screen_width,
			});

			//KEEPS THE NAVIGATION LINKS ACTIVE
			var main_nav = window.location.pathname.split('/')[3];
			var sub_nav = window.location.pathname.split('/')[4];
			$('.'+main_nav).addClass('active');
			$('.'+sub_nav).addClass('active');

			//KEEPS THE NAVIGATION LINKS ACTIVE BY URL
			// var current_location = window.location.href.substr(window.location.href.lastIndexOf("/")+1); GET LAST URL INDEX
			// var current_url = window.location.href;
			// var record_exist = window.location.pathname.split('/')[3];
			// $("nav ul li a").each(function(){
			// 	if($(this).attr("href") == current_url)
			// 	{
			// 		if(record_exist == 'records')
			// 		{
			// 			$('.records').addClass('active');
			// 		}
			// 		$(this).addClass("active");
			// 		alert($(this).attr('href'));
			// 	}
			// });
		});
		</script>
	</head>
	<body>
	<?php
		$links = array();
		array_push($links, '<div id="logo_header"><img src="'.base_url().'/includes/images/Logo2-shrink.png"><span>Ver. 1.1.3</span></div>');
		if(isset($header_links))
		{
			foreach($header_links as $text => $url)
			{
				if($url == "records")
				{
					array_push($links, anchor($url,$text,array('class'=>$url.' dropdown-toggle', 'data-toggle'=>'dropdown')));
				}
				else
				{
					array_push($links, anchor($url,$text,array('class'=>$url)));
				}
			}
			array_push($links, anchor('logout','<span class="glyphicon glyphicon-off"></span> Logout'));
		}

		$session_data = $this->session->userdata('logged_in');
		$data = $session_data;

		array_push($links,"<center><div class='navbar-brand'>User: <span class='glyphicon glyphicon-user'><b> ".$data['name']."</b></span></div></center>");
		$x = array();
		array_push($x, $links);

		// $template = array(
		// 			'table_open'	=> '<table align="center" width="100%" class="table table-bordered">',
		// 			'cell_start'	=> '<td align="center">',
		// 			'table_close'	=> '</table>'
		// 			);
		// $this->table->set_template($template);
		// echo $this->table->generate($x);

		echo '<nav>
		<ul>';
			foreach($x as $key => $value)
			{
				foreach($value as $links)
				{
					// echo '<td align="center">'.$links.'</td>';
					echo '<li>'.$links.'</li>';
				}
			}
		echo '</ul>
		</nav>';
	?>
	</body>
</html>