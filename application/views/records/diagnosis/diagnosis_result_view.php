<h2>Chief Complaint / Diagnosis</h2>
<?php
	if(isset($links))
	{
		@$page = "<b>Page Results: </b>".@$links;
	}
		echo @$page;
	echo '<div class="table_scroll">';
	$template = array(
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table-hover table-bordered">',

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
	$this->table->set_template($template);
	$this->table->set_heading('','Chief Complaint / Diagnosis','');

	$count = 1;
	if($diagnosis)
	{
		foreach($diagnosis as $value => $key)
		{
			$edit = anchor(base_url().'records/diagnosis/edit/'.$key['id'],'Edit', array('target'=>'__blank','class'=>'btn btn-info btn-sm'));
			$delete = anchor(base_url().'records/diagnosis/delete/'.$key['id'],'Delete', array('class'=>'btn btn-danger btn-sm'));
			$this->table->add_row($count++.'.',$key['diagnosis'],$edit.' - '.$delete);
		}
		echo $this->table->generate();
		echo '</div>';
	}
?>