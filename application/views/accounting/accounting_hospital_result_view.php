<script>
$(document).ready(function(){
	$('.select_all').click(function()
	{
		var checked_status = this.checked;
		$('.sel_multi').each(function()
		{
			this.checked = checked_status;
		});
	});
});
</script>
<?php echo validation_errors();?>
<?php echo form_open('accounting/printVoucherByHospital');?>
<h2>Accounts Payable</h2>
<?php
	if(empty($patients))
	{
		echo '<h2>No Record/s Found!</h2>';
	}
	else
	{
		echo '<div class="table_scroll">';
		$tmpl = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
					);
		$this->table->set_template($tmpl);
		$this->table->add_row(form_submit(array('name'=>'submit','value'=>'Print Voucher','class'=>'btn btn-success')),form_checkbox(array('name'=>'select_all','id'=>'select_all','class'=>'select_all')),form_label('Select All'));
		echo $this->table->generate();

		$template = array(
					'table_open' => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

					'heading_row_start' => '<tr>',
					'heading_row_end' => '</tr>',
					'heading_cell_start' => '<th>',
					'heading_cell_end' => '</th>',

					'row_start' => '<tr>',
					'row_end' => '</tr>',
					'cell_start' => '<td>',
					'cell_end' => '</td>',

					'row_alt_start' => '<tr>',
					'row_alt_end' => '</tr>',
					'cell_alt_start' => '<td>',
					'cell_alt_end' => '</td>',

					'table_close' => '</table>'
					);
		$this->table->set_template($template);
		$this->table->set_heading('','','Vendor Account #','Account Name','Receive Date','Released Date','Cleared Date','LOA Patients Name','Availment Date','Due Date','Amount','VAT');
		$count = 1;

		foreach($accounts as $key => $value)
		{
			foreach($patients[$key] as $pkey => $pvalue)
			{
				if($pvalue['claims_status'] == 'RECEIVED' || $pvalue['claims_status'] == '')
				{
					unset($pkey);
				}
				else
				{
					$due_date = $this->dateoperations->sum($pvalue['claims_dateofrecieve'],'day',$value['terms']);
					$sel_multi = form_checkbox(array('name'=>'sel_multi[]','id'=>'sel_multi','class'=>'sel_multi','value'=>$pvalue['code']));
					$this->table->add_row($count++.'.'.$sel_multi,$value['name'],$value['vendor_account'],$pvalue['code'],mdate('%M %d, %Y',mysql_to_unix($pvalue['claims_dateofrecieve'])),'','',$pvalue['patient_name'],
						mdate('%M %d, %Y',mysql_to_unix($pvalue['date_encoded'])),mdate('%M %d, %Y', mysql_to_unix($due_date)),'<b>PHP </b>'.number_format($pvalue['amount'],2),$pvalue['vat']);
	
				}
			}
		}
		echo $this->table->generate();
		echo '</div>';
	}
?>
<?php echo form_close();?>