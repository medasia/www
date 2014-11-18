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

<?php echo validation_errors(); ?>
<?php echo form_open('records/emerroom/multiSelect'); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
$multi = $this->table->add_row(form_submit(array('name'=>'submit','value'=>'Delete','class'=>'btn btn-sm btn-danger')),form_checkbox(array('name'=>'selectAll','id'=>'selectAll', 'class'=>'selectAll')),form_label('Select All'));
echo $this->table->generate($multi);

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
$this->table->set_heading('', 'Card #', 'Pin #' ,'Benefit / Card Amount', 'Cardholder', 'Date of Birth', 'Beneficiary', 'Beneficiary - Relationship', 'Landline #', 'Mobile #',
	'Address', 'Occupation', 'Registration Date/Time', 'Date of Activation', 'Date Validity', 'Date Expiration', 'Remarks');
$count=1;
$currentDate = date('Y-m-d');
foreach ($operations_er_card as $value => $key)
{
	// IF A ER DOESNT HAVE A REGISTERED PATIENT YET
	if($key['patient_id'] == '0')
	{	
		if($key['status'] != 'EXPIRED')
		{
			// COUNTING BEFORE UNREGISTERED ER EXPIRE
			$newdate = strtotime('-7 day',strtotime($key['dateexpiration']));
			$newdate = date('Y-m-d',$newdate);
			$expires = (strtotime($key['dateexpiration']) - strtotime(date('Y-m-d'))) / (60*60*24);

			if($expires > 1)
			{
				$day = " days";
			}
			else
			{
				$day = ' day';
			}

			if($expires < 0)
			{
				$key['status'] = status_update('emergency_room','status','EXPIRED',$key['id']);
			}
			
			if($newdate <= $currentDate)
			{
				$key['status'] = $key['status']." - will expire in ".$expires.$day.'.';
			}
			$expire_status = $key['status'];
		}
		else
		{
			$expire_status[$value] = ' - '.$key['status'];
		}
	}
	else
	{
		$action = '';

		if($key['status'] != 'ACTIVE')
		{
			$newdate = strtotime('-7 day',strtotime($key['dateofactivation']));
			$newdate = date('Y-m-d',$newdate);
			$active = (strtotime($key['dateofactivation']) - strtotime(date('Y-m-d'))) / (60*60*24);

			if($active > 1)
			{
				$day = ' days';
			}
			else
			{
				$day = ' day';
			}

			if($active < 0)
			{
				$key['status'] = status_update('emergency_room','status','ACTIVE',$key['id']);
			}

			if($newdate <= $currentDate)
			{
				$key['status'] = $key['status'].' - will activate in '.$active.$day.'.';
			}

			$active_status[$value] = $key['status'];
		}
		else
		{
			$newdate = strtotime('-7 day',strtotime($key['datevalid']));
			$newdate = date('Y-m-d',$newdate);
			$expires = (strtotime($key['datevalid']) - strtotime(date('Y-m-d'))) / (60*60*24);

			if($expires > 1)
			{
				$day = ' days';
			}
			else
			{
				$day = ' day';
			}

			if($expires < 1)
			{
				$key['status'] = status_update('emergency_room','status','EXPIRED',$key['id']);
			}

			if($newdate <= $currentDate)
			{
				$key['status'] = $key['status'].' - will reach validity limit and will expire in '.$expires.$day.'.';
			}
			$active_status[$value] = $key['status'];
		}
	}
	
	$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key['id']));
	// Adding a new table row.
	$this->table->add_row($count++.'.'.$selMulti,$key['card_number'],$key['pin_number'],$key['amount'], $key['patient_name'], mdate('%M %d, %Y', mysql_to_unix($key['birth_date'])),
					$key['beneficiary_lastname'].', '.$key['beneficiary_firstname'].' '.$key['beneficiary_middlename'],$key['relationship'],$key['landline_number'],$key['mobile_number'],
					$key['address'],$key['occupation'],mdate('%M %d, %Y %h:%i %a',mysql_to_unix($key['registration_datetime'])),mdate('%M %d, %Y', mysql_to_unix($key['dateofactivation'])).' '.$active_status[$value],
					mdate('%M %d, %Y', mysql_to_unix($key['datevalid'])),mdate('%M %d, %Y', mysql_to_unix($key['dateexpiration'])).$expire_status[$value],$key['remarks']);
}
echo $this->table->generate();
echo form_hidden('location', 'records/emerroom');
?>
</div>