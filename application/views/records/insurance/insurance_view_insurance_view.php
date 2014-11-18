<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Records of <?php echo $name?></title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
		<script>
		$(document).ready(function() {
			$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
				indicator : 'Saving...',
				cancel    : 'Cancel',
				submit    : 'OK',
				tooltip   : 'Click to edit...',
				onblur    : 'cancel',
				width	  : '100%',
				submitdata : {table: 'insurance', key: <?=$id?>}
			});

			$('.table_list').hide();

			$('#toggleSlideTable').click(function()
			{
				$('.table_list').slideToggle('fast', function() {});
			});
		});
		</script>
	</head>

	<body class="main-bg">
		<div class="profile_container">
			<div class="profile_name">Records of <?php echo $name?></div>
			<?php
				$tmpl = array(
								'table_open' => '<table cellpadding="4" cellspacing="0" class="profile_table">',
								'table_close'=> '</table>'
							 );
				$this->table->set_template($tmpl);
				$back = anchor(base_url()."records/insurance/", "Back");
				$this->table->add_row('Insurance Name', '<div class="editable" id="name">'.$name.'</div>');
				$this->table->add_row('Attention Name', '<div class="editable" id="attention_name">'.$attention_name.'</div>');
				$this->table->add_row('Attention Position', '<div class="editable" id="attention_pos">'.$attention_position.'</div>');
				$this->table->add_row('Address', '<div class="editable" id="address">'.$address.'</div>');
				$this->table->add_row('Code', '<div class="editable" id="code">'.$code.'</div>');
				$this->table->add_row('Billing Code', '<div class="editable" id="billing_code">'.$billing_code.'</div>');
				// $this->table->add_row(anchor(base_url()."records/insurance/delete/".$id."/", "Delete Insurance", array('onClick'=>"return confirm('Are you sure you want to delete this record?')",'class'=>'btn btn-danger')),'');

				echo $this->table->generate();
				
				echo '</div>';

				echo '<br>';
				echo '<button id="toggleSlideTable" class="btn btn-default" style="background-color:white">Companies</button>';

				// echo '<button id="toggleSlideTable" class="btn btn-default" style="background-color:white">Companies</button>';

				echo validation_errors();
				echo form_open('records/accounts/verifyPassword');
				$template = array(
							'table_open'	=> '<table border="1" cellpadding="4" cellspacing="0">',
							'table_close'	=> '</table>'
							);
				$this->table->set_template($template); 
				// $multi = $this->table->add_row(form_submit(array('name'=>'submit','value'=>'Delete','class'=>'btn btn-danger')),form_checkbox(array('name'=>'selectAll','id'=>'selectAll', 'class'=>'selectAll')),form_label('Select All'));
				// echo $this->table->generate($multi);
				
				echo '<div class="table_list">';
				$tmpl = array (
								'table_open'          => '<table border="1" frame="void" rules="all" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

								//class="table table-bordered table-hover">',

								'heading_row_start'   => '<tr>',
								'heading_row_end'     => '</tr>',
								'heading_cell_start'  => '<th>',
								'heading_cell_end'    => '</th>',

								'row_start'           => '<tr>',
								'row_end'             => '</tr>',
								'cell_start'          => '<td>',
								'cell_end'            => '</td>',

								'row_alt_start'       => '<tr>',
								'row_alt_end'         => '</tr>',
								'cell_alt_start'      => '<td>',
								'cell_alt_end'        => '</td>',

								'table_close'         => '</table>'
								);

				$this->table->set_template($tmpl);
				$this->table->set_heading('', 'Company Name', 'Code','Members Count','Broker Name','Notes','Effective Date','Validity Date');
				$count=1;
				foreach ($company as $value => $key) {
					// Build the custom actions links.
					// $actions = anchor(base_url()."records/company/delete/".$key['id']."/", "Delete");
					$members = anchor(base_url()."records/compins/members/".$key['id'],"Members(".$key['membercount'].")",array('target'=>'_blank'));
					$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key['id']));
					// Adding a new table row.
					$this->table->add_row($count++.".".$selMulti, anchor(base_url()."records/accounts/view/company/".$key['id']."/", $key['company'], array('target'=>'__blank')), $key['code'],
										$members,$key['broker_name'],$key['notes'], mdate('%M %d, %Y', mysql_to_unix($key['start'])), mdate('%M %d, %Y', mysql_to_unix($key['end'])));
				}
				echo $this->table->generate();
				echo '</div>';
				echo form_hidden('location', 'records/company');
				
			?>
		</div>
	</body>
</html>