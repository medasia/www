<!DOCTYPE html>
<html lang="en">
	<head>
		<script>
			$(document).ready(function() {
				$(".selectAll").click(function() {
					var checked_status = this.checked;
					$(".selMulti").each(function() {
						this.checked = checked_status;
					});
				});
			});
		</script>
	</head>
	<body>
		<?php echo validation_errors(); ?>
		<?php echo form_open('records/accounts/verifyPassword'); ?>
		<?php
			$template = array(
						'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
						'table_close'	=> '</table>'
						);
			$this->table->set_template($template); 
			$multi = $this->table->add_row(form_submit(array('name'=>'submit','value'=>'Delete','class'=>'btn btn-danger')),form_checkbox(array('name'=>'selectAll','id'=>'selectAll', 'class'=>'selectAll')),form_label('Select All'));
			echo $this->table->generate($multi);
			echo '<div class="table_scroll table_list">';
			$tmpl = array (
							'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-hover table-bordered">',

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
			$this->table->set_heading('', 'Insurance name', 'Attention Name'.'-'.'Attention Position', 'Address', 'Code', 'Billing Code');
			$count=1;
			foreach ($insurance as $value => $key) {
				// Build the custom actions links.
				// $actions = anchor(base_url()."records/insurance/delete/".$key['id']."/", "Delete");
				$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key['id']));
				// Adding a new table row.
				$this->table->add_row($count++.".".$selMulti, anchor(base_url()."records/accounts/view/insurance/".$key['id']."/", $key['name'],array('target'=>'__blank')), $key['attention_name'].' <br />'.$key['attention_position'],
				$key['address'], $key['code'], /**$key['vendor_account'],**/ $key['billing_code']);
			}
			echo $this->table->generate();
			echo '</div>';
			echo form_hidden('location', 'records/insurance');
		?>
	</body>
</html>