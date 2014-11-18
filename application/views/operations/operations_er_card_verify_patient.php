<title>Medriks - Operations</title>
<?php
	echo validation_errors();
	echo form_open('operations/availments');
	$tmpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close' => '</table>'
			);
	$this->table->set_template($tmpl);

	// IF ACTIVE AVAIL BUTTON APPEARS
	$status === 'ACTIVE' ? $avail = form_submit(array('name'=>'submit','value'=>'Verified / Avail','class'=>'btn btn-success')):$avail='';

	$beneficiary_form = array(
					array('',''),
					array(form_label('Name:'),$beneficiary_firstname),
					array(form_label('Middlename:'),$beneficiary_middlename),
					array(form_label('Lastname:'),$beneficiary_lastname),
					array(form_label('Relationship:'),$relationship)
						);
	$beneficiary = form_fieldset('Beneficiary Details');
	$beneficiary.= $this->table->generate($beneficiary_form);
	$beneficiary.= form_fieldset_close();

	$input = array(
			array('',''),
			array(form_label('Card Number'),$card_number),
			array(form_label('Pin Number'), $pin_number),
			array(form_label('Amount'),'<b>PHP.</b> '.$amount),
			array(form_label('Patient Name'),$patient_name),
			array(form_label('Birthdate'),mdate('%M %d, %Y',mysql_to_unix($birth_date))),
			array(form_label('Age'),computeAge($birth_date).' years old'),
			array(form_label('Occupation'),$occupation),
			array(form_label('Address'),$address),
			array(form_label('Landline Number'),$landline_number),
			array(form_label('Mobile Number'),$mobile_number),
			array(form_label('Beneficiary Details'),$beneficiary),
			array(form_label('Card Status'),$status),
			array('',$avail)
			);
	$er = form_fieldset('<h2>Verify Details of Card # '.$card_number.'</h2>');
	$er.= $this->table->generate($input);
	$er.= form_fieldset_close();

	$inputs = array(array($er));
	echo $this->table->generate($inputs);

	echo form_hidden('id',$id);
	echo form_hidden('patient_id',$patient_id);
	echo form_close();
?>