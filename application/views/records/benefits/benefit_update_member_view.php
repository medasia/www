<script>
function listbox_selectall(listID, isSelect)
{
	var listbox = document.getElementById(listID);
	for(var count=0; count < listbox.options.length; count++)
	{
		listbox.options[count].selected = isSelect;
	}
}
</script>

<h2>Update Details of Benefit: <?php echo $benefitset_info[0]['benefit_set_name'];?></h2>
<?php
	if($this->session->flashdata('result')!='')
	{
		echo $this->session->flashdata('result');
	}
?>
<?php echo validation_errors();?>
<?php echo form_open('records/benefits/update');?>
<table>
	<tr>
		<h3><?php echo anchor(base_url()."records/benefits/viewMembers/".$benefitset_info[0]['id']."/", "View All Members of Benefit ".$benefitset_info[0]['benefit_set_name'], array("target"=>"_blank"));?></h3>
		<td>Company Name: </td>
		<td>
			<?php echo '<b>'.$compins_name.'</b>';?>
		</td>
	</tr>
	<tr>
		<td>Cardholder Type: </td>
		<td>
			<?php echo '<b>'.$benefitset_info[0]['cardholder_type'].'</b>';?>
		</td>
	</tr>
	<tr>
		<td>Level: </td>
		<td>
			<?php echo '<b>'.$benefitset_info[0]['level'].'</b>';?>
		</td>
	</tr>
	<tr>
		<td>
	<tr>
		<td>Maximum Benefit Limit: </td>
		<td>
			<?php echo '<b>Php. '.number_format($benefitset_info[0]['maximum_benefit_limit'],2).'</b>';?>
		</td>
	</tr>
</table>

<table>
	<tr><h3>Update/Edit the information below.</h3>
		<b>Current Members: </b>
		<td>
			<select multiple size="10" name="currentMembers[]" id="currentMembers" style="width:300px">
				<?php foreach($members as $key => $value)
				{
					$id = $value['patient_id'];
					$label = $value['label'];
					echo "<option value='$id'>$label</option>";
				}
				?>
			</select>
			<br>
			Select <b>ALL</b> current members first, then update the changes below.
			<br>
			<input type="button" onclick="listbox_selectall('currentMembers', true);" value="Select All" class="btn btn-info btn-sm">
			<input type="button" onclick="listbox_selectall('currentMembers', false);" value="Select None" class="btn btn-sm btn-warning">
			<br>
		</td>
	</tr>
</table>
<?php
	$template = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
					'table_close' => '</table>'
					);

	if(@$ip != 0)
	{
		foreach($ip as $key => $value)
		{
			// var_dump($key);
			$this->table->set_heading($key);

			foreach($value as $kdetails => $vdetails)
			{
				// var_dump($vdetails);
				$ipField = 'IP#'.str_replace(" ","_",$key).'#'.str_replace(" ","_", $vdetails['details']);
				// var_dump(str_replace(" ", "_",$ipField));
				if($vdetails['details'] == 'AS CHARGED' || $vdetails['details'] == 'PER ILLNESS')
				{
					$details = form_label('Base on Maximum Benefit Limit: <b>PHP '.number_format($benefitset_info[0]['maximum_benefit_limit'],2));
					echo form_hidden($ipField,$benefitset_info[0]['maximum_benefit_limit']);
				}
				else
				{
					$details = form_input(array('name'=>$ipField,'size'=>'20','value'=>$fields[$ipField]));
				}
				$ipDetails = $this->table->add_row(array(form_label('Total '.$vdetails['details']),$details));
				$this->table->set_template($template);
			}
			echo form_fieldset('In-Patient Benefit Schedule');
			echo $this->table->generate($ipDetails);
			echo form_fieldset_close();
		}
	}

	if(@$op != 0)
	{
		foreach($op as $key => $value)
		{
			$this->table->set_heading($key);

			foreach($value as $kdetails => $vdetails)
			{
				$opField = 'OP#'.str_replace(" ","_",$key).'#'.str_replace(" ","_",$vdetails['details']);

				if($vdetails['details'] == 'AS CHARGED' || $vdetails['details'] == 'PER ILLNESS')
				{
					$details = form_label('Base on Maximum Benefit Limit: <b>PHP '.number_format($benefitset_info[0]['maximum_benefit_limit']));
					echo form_hidden($opField, $benefitset_info[0]['maximum_benefit_limit']);
				}
				else
				{
					$details = form_input(array('name'=>$opField,'size'=>'20','value'=>$fields[$opField]));
				}

				$opDetails = $this->table->add_row(array(form_label('Total '.$vdetails['details']),$details));
			}
			echo form_fieldset('Out-Patient Benefit Schedule');
			echo $this->table->generate($opDetails);
			echo form_fieldset_close();
		}
	}

	if(@$ipop != 0)
	{
		foreach($ipop as $key => $value)
		{
			$this->table->set_heading($key);

			foreach($value as $kdetails => $vdetails)
			{
				$ipopField = 'IP-OP#'.str_replace(" ","_",$key).'#'.str_replace(" ","_",$vdetails['details']);

				if($vdetails['details'] == 'AS CHARGED' || $vdetails['details'] == 'PER ILLNESS')
				{
					$details = form_label('Base on Maximum Benefit Limit: <b>PHP '.number_format($benefitset_info[0]['maximum_benefit_limit'],2));
					echo form_hidden($ipopField, $benefitset_info[0]['maximum_benefit_limit']);
				}
				else
				{
					$details = form_input(array('name'=>$ipopField,'size'=>'20','value'=>$fields[$ipopField]));
				}

				$ipopDetails = $this->table->add_row(array(form_label('Total '.$vdetails['details']), $details));
			}
			echo form_fieldset('In and Out Patient Benefit Schedule');
			echo $this->table->generate($ipopDetails);
			echo form_fieldset_close();
		}
	}

	$input = array(
				array(''),
				array(form_submit(array('name'=>'submit','id'=>'submit','value'=>'Save','class'=>'btn btn-sm btn-success')))
				);
	$this->table->set_template($template);
	echo $this->table->generate($input);

	echo form_hidden('level', $benefitset_info[0]['level']);
	echo form_hidden('cardholder_type', $benefitset_info[0]['cardholder_type']);
	echo form_hidden('maximum_benefit_limit', $benefitset_info[0]['maximum_benefit_limit']);
	echo form_hidden('table', $benefitset_info[0]['benefit_set_name']);
	echo form_hidden('benefitset_id', $benefitset_info[0]['id']);
	echo form_hidden('compins_id', $benefitset_info[0]['compins_id']);
?>
<?php echo form_close(); ?>
<br><br>