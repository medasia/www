<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Medriks - Records</title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
	</head>
	<?php
		$links = array();
			foreach($records_links as $text => $url)
			{
				$sub = substr($url, 8);
				array_push($links, anchor($url, $text,array('class'=>$sub.' records_links')));
			}
			$x = array();
			array_push($x, $links);
			// $template = array(
			// 			'table_open'	=> '<table align="center" width="100%">',
			// 			'cell_start'	=> '<td align="center">',
			// 			'table_close'	=> '</table>'
			// 			);
			// $this->table->set_template($template); 
			// echo $this->table->generate($x); 

			echo '<nav id="records_links_nav">
				<ul id="records_links_ul">';
				foreach($x as $key => $value)
				{
					foreach($value as $links)
					{
					// echo '<td align="center">'.$links.'</td>';
					echo '<li class="records_links_li">'.$links.'</li>';
					}
				}
				echo '</ul>
			</nav>';
		?>
</html>