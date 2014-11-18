<h2>Hospital Accounts</h2>
<?php
if(isset($links))
{
	$page = "<b>Page results:</b> ".@$links;
}
	echo $page;

if(empty($result))
{
	echo "<h2>No Record/s Found!!!</h2>";
}
else
{
	echo '<div class="table_scroll">';
	$templates = array(
				'table_open'          => '<table border="0" cellpadding="4" cellspacing="0" class="table table-hover table-bordered">',

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
	$this->table->set_template($templates);
	$this->table->set_heading('','Account Name','Vendor Account','Terms (Days)','VAT','');
	$count = 1;

	foreach($result as $value => $key)
	{
		// RESULTS ARE DENTISTS AND DOCTORS
		if(isset($key['firstname']) || isset($key['middlename']) || isset($key['lastname']))
		{
			if($key['middlename'] == 'N/A')
			{
				$mid = '';
			}
			else
			{
				$mid = $key['middlename'];
			}

			$account_name = $key['type'].'. '.$key['firstname'].' '.$mid.' '.$key['lastname'];
			$edit = anchor(base_url().'records/hospaccnt/view/dentistsanddoctors/'.$key['id'].'/','Edit',array('target'=>'_blank','class'=>'btn btn-warning btn-sm'));
		}

		// RESULTS ARE HOSPITALS OR CLINIC
		if(isset($key['name']))
		{
			$account_name = $key['name'];
			$edit = anchor(base_url().'records/hospaccnt/view/hospital/'.$key['id'].'/','Edit',array('target'=>'_blank','class'=>'btn btn-warning btn-sm'));
		}

		$this->table->add_row($count++.'.',$account_name,$key['vendor_account'],$key['terms'],$key['vat'],$edit);
	}
	echo $this->table->generate();
	echo '</div>';
}
?>