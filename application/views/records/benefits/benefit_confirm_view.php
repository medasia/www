<script>
$(document).ready(function()
{
	$("form").validate({
    			rules: {
    				maximum_benefit_limit: {
    					required: true,
    					number: true
    				},
    				benefit_limit_type: {
    					required: true
    				}
    			},
    			messages: {
    				maximum_benefit_limit: {
    					required: 'This field is required',
    					number: 'Enter a valid amount only (Number only)'
    				},
    				benefit_limit_type: {
    					required: 'This field is required'
    				}
    			}
			});
});
</script>
<h2>Confirm Schedule of Benefits</h2>
<?php echo validation_errors();?>
<?php echo form_open('records/benefits/confirm');?>
<?php

	$template = array(
						'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
						'table_close' => '</table>'
						);

	foreach($levels as $value =>$key)
	{
		$level[$key[0]['level']] = $key[0]['level'];
	}
	// var_dump($level);
	
	if(@$ip != 0)
	{
		$ipForm = '';

		foreach($ip as $key => $value)
		{
			@$remarks = benefit_remarks($value);
			if($remarks != NULL)
			{
				$ipTmpl = $this->table->add_row("<b>".$value."</b> - ".$remarks);
			}
			else
			{
				$ipTmpl = $this->table->add_row("<b>".$value."</b>");
			}
			$this->table->set_template($template);
		}
		$ipForm.= form_fieldset('Basic Benefits', array('id'=>'ip_info'));
		$ipForm.= $this->table->generate($ipTmpl);
		$ipForm.= form_fieldset_close();

		echo form_hidden('ip','In-Patient');
		echo form_hidden('ipDetails', $ip);
	}
	else
	{
		$ipForm = '<b>No applicable benefit/s.</b>';
	}

	if(@$op !=0)
	{
		$opForm = '';

		foreach($op as $key => $value)
		{
			$remarks = benefit_remarks($value);
			if($remarks != NULL)
			{
				$opTmpl = $this->table->add_row("<b>".$value."</b> - ".$remarks);
			}
			else
			{
				$opTmpl = $this->table->add_row("<b>".$value."</b>");
			}
			$this->table->set_template($template);
		}
		$opForm.= form_fieldset('Basic Benefits', array('id'=>'op_info'));
		$opForm.= $this->table->generate($opTmpl);
		$opForm.= form_fieldset_close();

		echo form_hidden('op','Out-Patient');
		echo form_hidden('opDetails', $op);
	}
	else
	{
		$opForm = '<b>No applicable benefit/s.</b>';
	}

	if(@$ipop !=0)
	{
		$ipopForm = '';

		foreach($ipop as $key => $value)
		{
			$remarks = benefit_remarks($value);
			if($remarks != NULL)
			{
				$ipopTmpl = $this->table->add_row("<b>".$value."</b> - ".$remarks);
			}
			else
			{
				$ipopTmpl = $this->table->add_row("<b>".$value."</b>");
			}
			$this->table->set_template($template);
		}

		$ipopForm.= form_fieldset('Basic Benefits', array('id'=>'op_info'));
		$ipopForm.= $this->table->generate($ipopTmpl);
		$ipopForm.= form_fieldset_close();

		echo form_hidden('ipop','In and Out Patient');
		echo form_hidden('ipopDetails', $ipop);
	}
	else
	{
		$ipopForm = '<b>No applicable benefit/s.</b>';
	}
	
	$this->table->set_template($template);

	$inputs = array(
					array('',''),
					array(form_label('Company Name : ', 'compins'), form_label('<b>'.$compins_name.'</b>','compins_name')),
					array(form_label('Benefit Schedule Name : ', 'benefit_set'), form_label('<b>'.$benefit_set_name.'</b>', 'benefit_set_name')),
					array(form_label('Cardholder Type : ', 'cardholder'), form_label('<b>'.$cardholder_type.'</b>','cardholder_type')),
					array(form_label('Other Conditions:'),form_label($condition_name.'<br>'.$condition_details[0]['condition_details'])),
					array(form_label('Exclusions:'),form_label($exclusion_name.'<br>'.$exclusion_details[0]['exclusion_details'])),
					array(form_label('Level/Rank/Position :', 'rank'), form_dropdown('level', array_unique($level))),
					// array(form_label('Maximum Benefit Limit :','mbl'), form_input(array('name'=>'maximum_benefit_limit', 'id'=>'maximum_benefit_limit', 'size'=>'20'))),
					array(form_label('Choose Benefit Schedule Limit Type:'),form_dropdown('benefit_limit_type',array(''=>'','Maximum Benefit Limit'=>'Annual Benefit Limit','Per Illness'=>'Per Illness')).'<div class="col-xs-3">'.
						form_input(array('name'=>'maximum_benefit_limit','id'=>'maximum_benefit_limit','size'=>'20','placeholder'=>'Enter Limit Amount','class'=>'form-control input-sm'))),
					array(form_label('In - Patient : ', 'ip'), $ipForm),
					array(form_label('Out - Patient : ', 'op'), $opForm),
					array(form_label('In and Out - Patient : ', 'ipop'), $ipopForm),
					array('', form_submit(array('name'=>'submit','value'=>'Confirm','class'=>'btn btn-success'))),
					);
	echo $this->table->generate($inputs);

	echo form_hidden('compins_id', $compins_id);
	echo form_hidden('benefit_set_name', $benefit_set_name);
	echo form_hidden('cardholder_type', $cardholder_type);
	echo form_hidden('fields', $fields);
	echo form_hidden('condition_name',$condition_name);
	echo form_hidden('exclusion_name',$exclusion_name);
?>
<?php echo form_close(); ?>
<br><br>