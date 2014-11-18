<h1>Benefits</h1>
<?php foreach($query as $row)
{
	$benefit_type = $row->benefit_type;
	$benefit_name = $row->benefit_name;
	$details = $row->details;
	$id = $row->id;
	var_dump($id);
	var_dump($details);
}
	var_dump($details);

echo "<h2>Edit ".$benefit_name."</h2>"
?>

<?php echo validation_errors(); ?>
<?php echo form_open('records/benefits'); ?>
<?php

$otherDet = array(
				array('',''),
				array(form_label('Details', 'otherdetails'), form_input(array('name'=>'otherDetails[]','id'=>'otherDetails','size'=>'20'))),
				array('','')
				);
$template = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0" id="detailsSet">',
			'table_close'	=>	'</table>'
			);
$this->table->set_template($template);
$addDetails = form_fieldset('Other Details', array('id'=>'other_details'));
$addDetails.= $this->table->generate($otherDet);
$addDetails.= form_fieldset_close();

$inputs = array(
				array('',''),
				array(form_label('Benefit Type', 'benefit_type'), form_dropdown('benefit_type', array('IP'=>'IP','OP'=>'OP','IP-OP' =>'IP-OP'),$benefit_type)),
				array(form_label('Benefit Name', 'benefit_name'), form_input(array('name'=>'benefit_name', 'id'=>'benefit_name', 'size'=>'20'),$benefit_name)),
				array(form_label('Details','details'),
					form_checkbox(array('name'=>'days','id'=>'days','value'=>'days'),'',$days).'Days'.'<br>'.
					form_checkbox(array('name'=>'amount','id'=>'amount','value'=>'amount'),'',$amount).'Amount'.'<br>'.
					form_checkbox(array('name'=>'as_charged','id'=>'as_charged', 'value'=>'as charged'),'',$as_charged).'As Charged'),

				array(form_button(array('name'=>'addDetails','id'=>'addDetails', 'content'=>'Add Details')),$addDetails),

				array('', form_submit('submit','Update'))
				);

$template = array(
				'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0">',
				'table_close'	=> '</table>'
				);

$this->table->set_template($template);
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>