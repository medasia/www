<script>
$(document).ready(function(){
	$('#doctors_form, #clinic_form').hide();
	$('#hospital').click(function()
	{
		$('#clinic_form').slideToggle('fast', function() {});
		$('#doctors_form').hide();
	});

	$('#doctors').click(function()
	{
		$('#doctors_form').slideToggle('fast', function() {});
		$('#clinic_form').hide();
	});
});
</script>

<h2>Search</h2>
<button id='hospital' class="btn btn-default">Search Hospitals and Clinics</button>
<button id='doctors' class="btn btn-default">Search Dentists and Doctors</button>

<div id="clinic_form">
	<?php echo validation_errors(); ?>
	<?php echo form_open('records/affiliatedserviceprovider/searchHospital'); ?>
		<?php
		$template = array(
						'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
						'table_close' => '</table>'
						);

		//HOSPITALS AND CLINICS SEARCH GROUP
		$clinicTmpl = array(
						array('',''),
						array(form_label('Name: '), form_input(array('name'=>'keyword', 'id'=>'keyword', 'size'=>'20', 'value'=>set_value('keyword'),'class'=>'form-control'))),
						array(form_label('Branch: '), form_input(array('name'=>'branch', 'id'=>'branch', 'size'=>'20', 'value'=>set_value('branch'),'class'=>'form-control'))),
						array(form_label('Address: '), form_input(array('name'=>'address', 'id'=>'address', 'size'=>'20', 'value'=>set_value('address'),'class'=>'form-control'))),
						array(form_label('Province: '), form_input(array('name'=>'province', 'id'=>'province', 'size'=>'20', 'value'=>set_value('province'),'class'=>'form-control'))),
						array(form_label('Region: '), form_input(array('name'=>'region', 'id'=>'region', 'size'=>'20', 'value'=>set_value('region'),'class'=>'form-control'))),
						array(form_label('Limit: '), form_dropdown('limit', array('100'=>'100','300'=>'300','500'=>'500','1000'=>'1000','500000'=>'All'))),
						array(form_hidden('table', 'hospital'), form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-primary')))
						);
		$this->table->set_template($template);
		$clinic = form_fieldset('<b>Search Hospitals and Clinics</b>');
		$clinic.= $this->table->generate($clinicTmpl);
		$clinic.= form_fieldset_close();

		$input = array(array($clinic));
		$this->table->set_template($template);
		echo $this->table->generate($input);
		?>
	<?php echo form_close();?>
</div>

<div id="doctors_form">
	<?php echo validation_errors(); ?>
		<?php echo form_open('records/affiliatedserviceprovider/searchDentists');?>
		<?php
		$template = array(
						'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
						'table_close' => '</table>'
						);

		//DENTISTS AND DOCTORS SEARCH GROUP
		$doctorsTmpl = array(
						array('',''),
						// array(form_label('Name: '), form_input(array('name'=>'keyword','id'=>'keyword', 'size'=>'20', 'value'=>set_value('keyword'),'class'=>'form-control'))),
						array(form_label('First Name: '), form_input(array('name'=>'firstname','id'=>'firstname','size'=>'20','value'=>set_value('firstname'),'class'=>'form-control'))),
						array(form_label('Middle Name: '), form_input(array('name'=>'middlename','id'=>'middlename','size'=>'20','value'=>set_value('middlename'),'class'=>'form-control'))),
						array(form_label('Last Name: '), form_input(array('name'=>'lastname','id'=>'lastname','size'=>'20','value'=>set_value('lastname'),'class'=>'form-control'))),
						array(form_label('Specialization: '), form_input(array('name'=>'specialization','id'=>'specialization', 'size'=>'20', 'value'=>set_value('specialization'),'class'=>'form-control'))),
						array(form_label('Address: '), form_input(array('name'=>'address','id'=>'address','size'=>'20', 'value'=>set_value('address'),'class'=>'form-control'))),
						// array(form_label('City: '), form_input(array('name'=>'city', 'id'=>'city', 'size'=>'20', 'value'=>set_value('city')))),
						// array(form_label('Province: '), form_input(array('name'=>'province', 'id'=>'province', 'size'=>'20', 'value'=>set_value('province')))),
						// array(form_label('Region: '), form_input(array('name'=>'region','id'=>'region', 'size'=>'20', 'value'=>set_value('region')))),
						array(form_label('Limit: '), form_dropdown('limit', array('100'=>'100','300'=>'300','500'=>'500','1000'=>'1000','500000'=>'All'))),
						array(form_hidden('table','dentistsanddoctors'), form_submit(array('name'=>'submit','value'=>'Search','class'=>'btn btn-primary')))
						);
		$this->table->set_template($template);
		$doctors = form_fieldset('<b>Search Dentists and Doctors</b>');
		$doctors.= $this->table->generate($doctorsTmpl);
		$doctors.= form_fieldset_close();

		$input = array(array($doctors));
		$this->table->set_template($template);
		echo $this->table->generate($input);
		?>
	<?php echo form_close();?>
</div>
<div id="results"></div>


<?php
// $template = array(
// 				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
// 				'table_close' => '</table>'
// 				);
// $this->table->set_template($template);
// $this->table->add_row(form_label('<b>Search:</b>'), form_dropdown('table', array('hospital'=>' Hospital and Clinic Name', 'dentistsanddoctors'=>'Dentists and Doctors Name')),
// 					form_input(array('name'=>'keyword', 'id'=>'keyword', 'size'=>'50')), form_label('<b>Limit: </b>'),
// 					form_dropdown('limit', array('100'=>'100', '300'=>'300', '500'=>'500')), form_submit('submit','Search'));
// echo $this->table->generate();
?>