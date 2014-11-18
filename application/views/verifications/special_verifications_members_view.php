<?php
	echo "<div class='table_scroll'>";
	$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

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
		$this->table->set_heading('','Old LOA Code','Approval Code','Name','Company','Insurance','Hospital Name', 'Hospital Branch','Attending Physician','Availment Type','Benefit Name','Exceeding Amount','Remarks','User','');
		$count=1;

		foreach(@$result as $value => $key)
		{
			$this->table->add_row($count++.'.',$key['old_loa'],$key['code'],$key['patient_name'],$key['company_name'],$key['insurance_name'],$key['hospital_name'],$key['hospital_branch'],$key['physician'],$key['availment_type'],
				$key['benefit_name'],$key['amount'],$key['remarks'],$key['user'],'');
		}
		echo $this->table->generate();
		echo '</div>';
?>