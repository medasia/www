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

<h2>Members of Benefit "<?=str_replace("_"," ",$name);?>" for <?=$compinsname;?></h2>
<?php
if($this->session->flashdata('result') != '')
{
	echo $this->session->flashdata('result');
}
?>
<br>
<?php echo validation_errors();?>
<?php echo form_open('records/benefits/addOrUpdate');?>
<?php
	$temp = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$add = array(
				array('',''),
				array(form_label('Benefit Details:'),form_submit(array('name'=>'submit','id'=>'submit','value'=>'Add Members','class'=>'btn btn-sm btn-primary')),form_submit(array('name'=>'submit','id'=>'submit','class'=>'btn btn-sm btn-info','value'=>'Edit')))
				);
	$this->table->set_template($temp);
	echo $this->table->generate($add);

	echo form_hidden('table',$info[0]['benefit_set_name']);
	echo form_hidden('benefitset_id', $info[0]['id']);
	echo form_hidden('compins_id', $info[0]['compins_id']);
	echo form_hidden('cardholder_type', $info[0]['cardholder_type']);
	echo form_hidden('level', $info[0]['level']);
?>
<?php echo form_close();?>

<?php echo validation_errors();?>
<?php echo form_open('records/benefits/multiSelect');?>
<?php
$template = array(
				'table_open' => '<table border="0" cellpadding ="4" cellspacing ="0"',
				'table_close' => '</table>'
				);

	if(@$ip != 0)
	{
		$ipForm = '';

		foreach($ip as $key => $value)
		{
			$ipTmpl = $this->table->add_row("<b>".$key."</b>",'<b>Details</b>','<b>Value</b>');
			$this->table->set_template($template);

			foreach($value as $kdetails => $vdetails)
			{
				$ipField = 'IP#'.str_replace(" ","_",$key).'#'.str_replace(" ","_",$vdetails['details']);

				if($vdetails['details'] == 'AMOUNT' || $vdetails['details'] == 'AS CHARGED' || $vdetails['details'] == 'BY MODALITIES')
				{
					if($vdetails['details'] == 'AS CHARGED')
					{
						$based = " (Based on Maximum Benefit Limit)";
					}
					else
					{
						$based = " ";
					}
					$ipFieldDetails = 'Php. '.number_format($fields[0][$ipField],2).$based;
				}
				elseif($vdetails['details'] == 'DAYS')
				{
					$ipFieldDetails = $fields[0][$ipField].' Days';
				}
				else
				{
					$ipFieldDetails = $fields[0][$ipField];
				}

				$ipDetails = $this->table->add_row(array('', form_label('<b>Total '.$vdetails['details'].'</b>'),form_label($ipFieldDetails)));
			}
		}
		$ipForm = form_fieldset('Basic Benefit', array('id'=>'ip_info'));
		$ipForm.= $this->table->generate($ipTmpl);
		$ipForm.= form_fieldset_close();
	}
	else
	{
		$ipForm = '<b>No applicable benefit/s.</b>';
	}

	if(@$op != 0)
	{
		$opForm = '';

		foreach($op as $key => $value)
		{
			$opTmpl = $this->table->add_row("<b>".$key."</b>",'<b>Details</b>','<b>Value</b>');
			$this->table->set_template($template);

			foreach($value as $kdetails => $vdetails)
			{
				$opField = 'OP#'.str_replace(" ","_",$key).'#'.str_replace(" ","_",$vdetails['details']);

				if($vdetails['details'] == 'AMOUNT' || $vdetails['details'] == 'AS CHARGED' || $vdetails['details'] == 'BY MODALITIES')
				{
					if($vdetails['details'] == 'AS CHARGED')
					{
						$based = " (Based on Maximum Benefit Limit)";
					}
					else
					{
						$based = " ";
					}
					$opFieldDetails = 'Php. '.number_format($fields[0][$opField],2).$based;
				}
				elseif($vdetails['details'] == 'DAYS')
				{
					$opFieldDetails = $fields[0][$opField].' Days';
				}
				else
				{
					$opFieldDetails = $fields[0][$opField];
				}

				$opDetails = $this->table->add_row(array('',form_label('<b>Total '.$vdetails['details'].'</b>'),form_label($opFieldDetails)));
			}
		}
		$opForm = form_fieldset('Basic Benefit', array('id'=>'op_info'));
		$opForm.= $this->table->generate($opTmpl);
		$opForm.= form_fieldset_close();
	}
	else
	{
		$opForm = '<b>No applicable benefit/s.</b>';
	}

	if(@$ipop != 0)
	{
		$ipopForm = '';

		foreach($ipop as $key => $value)
		{
			$ipopTmpl = $this->table->add_row("<b>".$key."</b>",'<b>Details</b>','<b>Value</b>');
			$this->table->set_template($template);

			foreach($value as $kdetails => $vdetails)
			{
				$ipopField = 'IP-OP#'.str_replace(" ","_",$key).'#'.str_replace(" ","_",$vdetails['details']);

				if($vdetails['details'] == 'AMOUNT' || $vdetails['details'] == 'AS CHARGED' || $vdetails['details'] == 'BY MODALITIES')
				{
					if($vdetails['details'] == 'AS CHARGED')
					{
						$based = " (Based on Maximum Benefit Limit)";
					}
					else
					{
						$based = " ";
					}
					$ipopFieldDetails = 'Php. '.number_format($fields[0][$ipopField],2).$based;
				}
				elseif($vdetails['details'] == 'DAYS')
				{
					$ipopFieldDetails = $fields[0][$ipopField].' Days';
				}
				else
				{
					$ipopFieldDetails = $fields[0][$ipopField];
				}

				$ipopDetails = $this->table->add_row(array('',form_label('<b>Total '.$vdetails['details'].'</b>'),form_label($ipopFieldDetails)));
			}
		}
		$ipopForm = form_fieldset('Basic Benefit', array('id'=>'ipop_info'));
		$ipopForm.= $this->table->generate($ipopTmpl);
		$ipopForm.= form_fieldset_close();
	}
	else
	{
		$ipopForm = "<b>No applicable benefit/s.</b>";
	}

	$this->table->set_template($template);

	$inputs = array(
					array('',''),
					array('<b>Benefit Details: ',''),
					array(form_label('Benefit Limit Type'),form_label($info[0]['plan_type'])),
					array(form_label('Cardholder Type:'), form_label('<b>'.$info[0]['cardholder_type'].'</b>')),
					array(form_label('Level/Rank/Position'), form_label('<b>'.$info[0]['level'])),
					array(form_label('Maximum Benefit Limit: '), form_label('<b>'.number_format($info[0]['maximum_benefit_limit'],2).' (base by level)</b>')),
					array(form_label('In - Patient:'), $ipForm),
					array(form_label('Out - Patient:'), $opForm),
					array(form_label('In and Out Patient:'), $ipopForm)
					);

	echo $this->table->generate($inputs);

	$this->table->set_template($template);
	$this->table->add_row(form_submit(array('name'=>'submit','id'=>'submit','value'=>'Back','class'=>'btn btn-sm btn-warning')),form_submit(array('name'=>'submit','id'=>'submit','value'=>'Delete','class'=>'btn btn-sm btn-danger')),
						form_checkbox(array('name'=>'selectAll','id'=>'selectAll','class'=>'selectAll')),form_label('Select All'));
	echo $this->table->generate();

	echo '<div class="table_scroll">';
	$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover" id="example">',

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
	$this->table->set_heading('', 'Name', 'Date of Birth', 'Age', 'Level/Position', 'Declaration Date', 'Start', 'End', 'Membership Status', 'Cardholder Type', 'Cardholder', 'Beneficiary', 'Remarks');

	$count=1;
	$currentDate = date('Y-m-d');
	foreach ($patients as $value => $key)
	{
		if(strtolower($key[0]['status']) == "active")
		{
			$newdate = strtotime('-7 day', strtotime($key[0]['end']));
			$newdate = date('Y-m-d', $newdate);
			$expires = (strtotime($key[0]['end']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24);

			if($expires > 1)
			{
				$day = " days";
			}
			else
			{
				$day = " day";
			}

			if($expires < 0)
			{
				$id = $key[0]['id'];
				$field = 'status';
				$data = "EXPIRED";
				$key['status'] = status_update('operations_new.patient',$field,$data,$id);
			}

			if($newdate <= $currentDate)
			{ // WARNING
				$color = 'orange';
				$key[0]['status'] = $key[0]['status']." - will expire in ".$expires.$day.".";
			}
			else
			{ // ACTIVE
				$color = 'black';
			}
		}
		elseif (strtolower($key[0]['status']) == "expired" || strtolower($key[0]['status']) == "deleted")
		{ // EXPIRED/DELETED
			$color = 'red';
		}
		else
		{ //ON HOLD OR LACK OF INFO
			$color = 'green';
		}

		// Build the custom actions links.
		// $actions = anchor(base_url()."records/members/delete/".$key['id']."/", "Delete");
		$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key[0]['id']));
		// Adding a new table row.
		$this->table->add_row("<font color=".$color.">".$count++.".".$selMulti, anchor(base_url()."operations/memberView/".$key[0]['id']."/", "<font color=".$color.">".$key[0]['lastname'].", ".$key[0]['firstname']." ".$key[0]['middlename']), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['dateofbirth'])), "<font color=".$color.">".computeAge($key[0]['dateofbirth']), 
			"<font color=".$color.">".$key[0]['level'], "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['declaration_date'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['start'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['end'])), "<font color=".$color.">".$key[0]['status'], 
			"<font color=".$color.">".$key[0]['cardholder_type'], "<font color=".$color.">".$key[0]['cardholder'], "", "<font color=".$color.">".$key[0]['remarks']);
	}
	echo form_hidden('id', $info[0]['id']);
	echo form_hidden('location', 'records/benefits');
	echo $this->table->generate();
	echo '</div>';
	?>
<?php echo form_close();?>
<br><br>