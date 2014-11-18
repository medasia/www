<h2>Benefit Set: <?php echo $info[0]['benefit_set_name'];?></h2>
<?php echo validation_errors();?>
<?php echo form_open('records/benefits/addOrUpdate');?>
<?php
	
	$template = array(
					'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
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
				$ipDetails = $this->table->add_row(array('',form_label('Total '.$vdetails['details']),form_label($fields[0][$ipField])));
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
				$opDetails = $this->table->add_row(array('',form_label('Total '.$vdetails['details']),form_label($fields[0][$opField])));
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
			$ipopTmpl = $this->table->add_row("<b>".$key."</b>","<b>Details</b>","<b>Value</b>");
			$this->table->set_template($template);

			foreach($value as $kdetails => $vdetails)
			{
				$ipopField = 'IP-OP#'.str_replace(" ","_",$key).'#'.str_replace(" ","_",$vdetails['details']);
				$ipopDetails = $this->table->add_row(array('',form_label('Total '.$vdetails['details']),form_label($fields[0][$ipopField])));
			}
		}
		$ipopForm = form_fieldset('Basic Benefit', array('id'=>'ipop_info'));
		$ipopForm.= $this->table->generate($ipopTmpl);
		$ipopForm.= form_fieldset_close();
	}
	else
	{
		$ipopForm = '<b>No applicable benefit/s</b>';
	}

	$this->table->set_template($template);

	$inputs = array(
					array('',''),
					array(anchor(base_url()."records/benefits/viewMembers/".$info[0]['id']."/", "<h3>View All Members of Benefit ".$info[0]['benefit_set_name']."</h3>", array("target"=>"_blank"))),
					array(form_label('Company Name: ', 'compins'), form_label('<b>'.$compinsname.'</b>')),
					array(form_label('Benefit Schedule Name: ', 'benefit_set'), form_label('<b>'.$info[0]['benefit_set_name'].'</b>')),
					array(form_label('Benefit Plan Type'),form_label($info[0]['plan_type'])),
					array(form_label('Cardholder Type: ', 'cardholder'), form_label('<b>'.$info[0]['cardholder_type'].'</b>')),
					array(form_label('Level/Rank/Position: ', 'level'), form_label('<b>'.$info[0]['level'].'</b>')),
					array(form_label('Maximum Benefit Limit: ', 'mbl'), form_label('<b> Php. '.number_format($info[0]['maximum_benefit_limit'],2).' (base by level)</b>')),
					array(form_label('In - Patient:' , 'ip'), $ipForm),
					array(form_label('Out - Patient: ', 'op'), $opForm),
					array(form_label('In and Out Patient: ', 'ipop'), $ipopForm),
					array('',form_submit(array('name'=>'submit','id'=>'submit','value'=>'Add Members','class'=>'btn btn-sm btn-primary'))),
					array('',form_submit(array('name'=>'submit','id'=>'submit','value'=>'Edit','class'=>'btn btn-sm btn-info')))
					);

	echo $this->table->generate($inputs);

	echo form_hidden('benefitset_id', $info[0]['id']);
	echo form_hidden('compins_id', $info[0]['compins_id']);
	echo form_hidden('cardholder_type', $info[0]['cardholder_type']);
	echo form_hidden('level', $info[0]['level']);
	echo form_hidden('table', $info[0]['benefit_set_name']);
?>
<?php echo form_close(); ?>
<br><br>