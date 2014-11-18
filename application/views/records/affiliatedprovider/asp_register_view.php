<script>
$(document).ready(function()
{
	//jQuery for Hospitals and Clinics
	$('#hospitalAdd').hide();
	$('#toggleSlideHospital').click(function()
	{
		$('#hospitalAdd').slideToggle('fast', function() {});
		$('#doctorsAdd').hide();
	});
	$('#date_accredited_hospital').datepicker({format: 'yyyy-mm-dd'});

	//jQuery for Dentist and Doctors
	$('#doctorsAdd').hide();
	$('#toggleSlideDoctors').click(function()
	{
		$('#doctorsAdd').slideToggle('fast', function() {});
		$('#hospitalAdd').hide();
	});
	$('#date_accredited_doctors').datepicker({format: 'yyyy-mm-dd'});
	$('#addclinic').click(function() {
		// $('#clinicset tr:last').after('<tr><td>CELL ROW</td></tr>');
		$('#clinicset').clone().appendTo('#clinic_info');
	});
	$("form#hospclinic_form").validate({
    			rules: {
    				name: {
    					required: true
    				},
    			    classification: {
    			        required: true
    			    },
    			    type: {
    			    	required: true
    			    }
    			},
    			messages: {
    				name: {
    					required: 'This field is required'
    				},
    			    classification: {
    			        required: "This field is required"
    			    },
    			    type: {
    					required: 'This field is required'
    				}
    			}
			});
	$("form#dentistsdoctors_form").validate({
    			rules: {
    				firstname: {
    					required: true
    				},
    			    middlename: {
    			        required: true
    			    },
    			    lastname: {
    			    	required: true
    			    },
    			    specialization: {
    			        required: true
    			    },
    			    date_accredited: {
    			        required: true
    			    },
    			    status: {
    			        required: true
    			    }
    			},
    			messages: {
    				firstname: {
    					required: 'This field is required'
    				},
    			    middlename: {
    			        required: 'This field is required'
    			    },
    			    lastname: {
    			    	required: 'This field is required'
    			    },
    			    specialization: {
    			        required: 'This field is required'
    			    },
    			    date_accredited: {
    			        required: 'This field is required'
    			    },
    			    status: {
    			        required: 'This field is required'
    			    }
    			}
			});
});
</script>

<h1>Affiliated Service Provider</h1>
<?php 
if ($this->session->flashdata('result') != '') {
	echo $this->session->flashdata('result');
}
?>
<br>
<button id='toggleSlideHospital' class="btn btn-default">Add new Hospitals/Clinics</button>
<button id='toggleSlideDoctors' class="btn btn-default"> Add new Dentists/Doctors</button>

<div id='hospitalAdd'>
<h2>Hospitals And Clinics</h2>

<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/24');?>
<?php
$inputs = array(
				array(form_label('Download Template for Hospital and Clinics', 'multiup'), form_submit(array('value'=>'Download','class'=>'btn btn-warning')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('utils/fileuploader/upto/hospclinic');?>
<?php
$inputs = array(
				array(form_label('Upload Multiple Hospitals and Clinics', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup','class'=>'form-group')), form_submit(array('value'=>'Upload','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/hospclinic/register',array('id'=>'hospclinic_form')); ?>
<?php
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);

$addressTMPL =  array(
					array('', ''),
					array(form_label('Street Address', 'street_address'), form_input(array('name'=>'street_address', 'id'=>'street_address', 'size'=>'20','placeholder'=>'Street Address','class'=>'form-control'))),
					array(form_label('Subdivision/Village', 'subdivision_village'), form_input(array('name'=>'subdivision_village', 'id'=>'subdivision_village', 'size'=>'20','placeholder'=>'Subdivision Village','class'=>'form-control'))),
					array(form_label('Barangay', 'barangay'), form_input(array('name'=>'barangay', 'id'=>'barangay', 'size'=>'20','placeholder'=>'Barangay','class'=>'form-control'))),
					array(form_label('City', 'city'), form_input(array('name'=>'city', 'id'=>'city', 'size'=>'20','placeholder'=>'City','class'=>'form-control'))),
					array(form_label('Province', 'province'), form_input(array('name'=>'province', 'id'=>'province', 'size'=>'20','placeholder'=>'Province','class'=>'form-control'))),
					array(form_label('Region', 'region'), form_input(array('name'=>'region', 'id'=>'region', 'size'=>'20','placeholder'=>'Region','class'=>'form-control')))
				);
$this->table->set_template($template); 
$address = form_fieldset('Address Information');
$address.= $this->table->generate($addressTMPL);
$address.= form_fieldset_close();

$med_coor_tmpl = array(
					array('',''),
					array(form_label('Medical Coordinator Name: '), form_input(array('name'=>'med_coor_name','id'=>'med_coor_name', 'size'=>'20','placeholder'=>'Medical Coordinator Name','class'=>'form-control'))),
					array(form_label('Room: '), form_input(array('name'=>'room','id'=>'room','size'=>'20','placeholder'=>'Room','class'=>'form-control'))),
					array(form_label('Schedule: '), form_input(array('name'=>'schedule','id'=>'schedule','size'=>'20','placeholder'=>'Schedule','class'=>'form-control'))),
					array(form_label('Contact Number: '), form_input(array('name'=>'contact_no','id'=>'contact_no','size'=>'20','placeholder'=>'Contact Number','class'=>'form-control'))),
					array(form_label('E-mail'), form_input(array('name'=>'med_coor_email','id'=>'med_coor_email','size'=>'20','placeholder'=>'E-mail','class'=>'form-control')))
				);
$this->table->set_template($template);
$medical_coordinator = form_fieldset('Medical Coordinator');
$medical_coordinator.= $this->table->generate($med_coor_tmpl);
$medical_coordinator.= form_fieldset_close();

$med_coor_tmpl2 = array(
					array('',''),
					array(form_label('Medical Coordinator Name:'), form_input(array('name'=>'med_coor_name_2', 'id'=>'med_coor_name_2', 'size'=>'20','placeholder'=>'Medical Coordinator Name','class'=>'form-control'))),
					array(form_label('Room:'), form_input(array('name'=>'room_2', 'id'=>'room_2', 'size'=>'20','placeholder'=>'Room','class'=>'form-control'))),
					array(form_label('Schedule:'), form_input(array('name'=>'schedule_2', 'id'=>'schedule_2', 'size'=>'20','placeholder'=>'Schedule','class'=>'form-control'))),
					array(form_label('Contact Number:'), form_input(array('name'=>'contact_no_2', 'id'=>'contact_no_2', 'size'=>'20','placeholder'=>'Contact Number','class'=>'form-control'))),
					array(form_label('E-mail'),form_input(array('name'=>'med_coor_email_2','id'=>'med_coor_email_2','size'=>'20','placeholder'=>'E-mail','class'=>'form-control')))
				);
$this->table->set_template($template);
$medical_coordinator2 = form_fieldset('Medical Coordinator 2');
$medical_coordinator2.= $this->table->generate($med_coor_tmpl2);
$medical_coordinator2.= form_fieldset_close();

$inputs = array(
				array('', ''),
				array(form_label('Hospital/Clinic name', 'name'), form_input(array('name'=>'name', 'id'=>'name', 'size'=>'20','placeholder'=>'Hospital/Clinic Name','class'=>'form-control'))),
				array(form_label('Classification'), form_dropdown('classification', array('HOSPITAL'=>'HOSPITAL', 'CLINIC'=>'CLINIC','MULTISPECIALTY'=>'MULTISPECIALTY','DIAGNOSTIC'=>'DIAGNOSTIC', 'SPECIALTY CLINIC'=>'SPECIALTY CLINIC'))),
				array(form_label('Type', 'type'), form_dropdown('type', array('Regular' => 'Regular', 'Blanket' => 'Blanket', 'Maximum' => 'Maximum'))),
				array(form_label('Branch', 'branch'), form_input(array('name'=>'branch', 'id'=>'branch', 'size'=>'20','placeholder'=>'Branch','class'=>'form-control'))),
				array('Address', $address),
				array(form_label('Contact Person', 'contact_person'), form_input(array('name'=>'contact_person', 'id'=>'contact_person', 'size'=>'20','placeholder'=>'Contact Person','class'=>'form-control'))),
				array(form_label('Contact Number', 'contact_number'), form_input(array('name'=>'contact_number', 'id'=>'contact_number', 'size'=>'20','placeholder'=>'Contact Number','class'=>'form-control'))),
				array(form_label('Fax Number', 'fax_number'), form_input(array('name'=>'fax_number', 'id'=>'fax_number', 'size'=>'20','placeholder'=>'Fax Number','class'=>'form-control'))),
				array(form_label('E-mail Address'), form_input(array('name'=>'email','id'=>'email','size'=>'20','placeholder'=>'E-mail','class'=>'form-control'))),
				array('Medical Coordinator',$medical_coordinator),
				array('Medical Coordinator 2', $medical_coordinator2),
				array(form_label('Category', 'category'), form_dropdown('category', array('Level 1' => 'Level 1', 'Level 2' => 'Level 2', 'Level 3' => 'Level 3', 'Level 4' => 'Level 4'))),
				array(form_label('Date Accredited', 'date_accredited'), form_input(array('name'=>'date_accredited', 'id'=>'date_accredited_hospital', 'value'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Status', 'status'), form_dropdown('status', array('ACCREDITED' => 'ACCREDITED', 'DIS-ACCREDITED' => 'DIS-ACCREDITED','DO NOT PROMOTE'=>'DO NOT PROMOTE'))),
				array(form_label('Remarks', 'remarks'), form_textarea(array('name'=>'remarks', 'id'=>'remarks', 'cols'=>'50', 'rows'=>'10','placeholder'=>'Remarks','class'=>'form-control'))),
				array('', form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));
?>
<?php echo form_close(); ?>
</div>

<div id='doctorsAdd'>
<h2>Dentist and Doctors</h2>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('records/uphist/downloadTemp/23');?>
<?php
$inputs = array(
				array(form_label('Download Template for Dentist and Doctors', 'multiup'), form_submit(array('value'=>'Download','class'=>'btn btn-warning')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template);
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('utils/fileuploader/upto/dentistsdoctors');?>
<?php

// $clinicTmpl =  array(
// 					array('', ''),
// 					array(form_label('Clinic name', 'clinic_name'), form_input(array('name'=>'clinic_name[]', 'id'=>'clinic_name', 'size'=>'20'))),
// 					array(form_label('Hospital name', 'hospital_name'), form_input(array('name'=>'hospital_name[]', 'id'=>'hospital_name', 'size'=>'20'))),
// 					array(form_label('Street Address', 'street_address'), form_input(array('name'=>'street_address[]', 'id'=>'street_address', 'size'=>'20'))),
// 					array(form_label('Subdivision/Village', 'subdivision_village'), form_input(array('name'=>'subdivision_village[]', 'id'=>'subdivision_village', 'size'=>'20'))),
// 					array(form_label('Barangay', 'barangay'), form_input(array('name'=>'barangay[]', 'id'=>'barangay', 'size'=>'20'))),
// 					array(form_label('City', 'city'), form_input(array('name'=>'city[]', 'id'=>'city', 'size'=>'20'))),
// 					array(form_label('Province', 'province'), form_input(array('name'=>'province[]', 'id'=>'province', 'size'=>'20'))),
// 					array(form_label('Region', 'region'), form_input(array('name'=>'region[]', 'id'=>'region', 'size'=>'20'))),
// 					array(form_label('Clinic Sched', 'clinic_sched'), form_input(array('name'=>'clinic_sched[]', 'id'=>'clinic_sched', 'size'=>'20'))),
// 					array('', '')
// 				);
// $template = array(
// 			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0" id="clinicset">',
// 			'table_close'	=> '</table>'
// 			);
// $this->table->set_template($template); 
// $clinic = form_fieldset('Clinic Information', array('id'=>'clinic_info'));
// $clinic.= $this->table->generate($clinicTmpl);
// $clinic.= form_fieldset_close();

$clinicTmpl2 = array(
					array('',''),
					array(form_label('Address'), form_textarea(array('name'=>'clinic1', 'id'=>'clinic1', 'cols'=>'25', 'rows'=>'5','class'=>'form-control'))),
					array(form_label('Address'), form_textarea(array('name'=>'clinic2', 'id'=>'clinic2', 'cols'=>'25', 'rows'=>'5','class'=>'form-control'))),
					array(form_label('Address'), form_textarea(array('name'=>'clinic3', 'id'=>'clinic3', 'cols'=>'25', 'rows'=>'5','class'=>'form-control'))),
					array(form_label('Address'), form_textarea(array('name'=>'clinic4', 'id'=>'clinic4', 'cols'=>'25', 'rows'=>'5','class'=>'form-control'))),
					array(form_label('Address'), form_textarea(array('name'=>'clinic5', 'id'=>'clinic5', 'cols'=>'25', 'rows'=>'5','class'=>'form-control')))
				);
$template = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
$this->table->set_template($template);
$clinics = form_fieldset('<b>Clinic Information</b>', array('id'=>'clinic_info'));
$clinics.= $this->table->generate($clinicTmpl2);
$clinics.= form_fieldset_close();

$inputs = array(
				array(form_label('Upload multiple Dentists and Doctors', 'multiup'), form_upload(array('name'=>'file', 'id'=>'multiup','class'=>'form-group')), form_submit(array('value'=>'Upload','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>
<?php echo form_open('records/dentistsdoctors/register',array('id'=>'dentistsdoctors_form')); ?>
<?php
$inputs = array(
				array('', ''),
				array(form_label('Type', 'type'), form_dropdown('type', array('MD' => 'MD', 'Dentist' => 'Dentist'))),
				array(form_label('First name', 'firstname'), form_input(array('name'=>'firstname', 'id'=>'firstname', 'size'=>'50','placeholder'=>'First Name','class'=>'form-control'))),
				array(form_label('Middle name', 'middlename'), form_input(array('name'=>'middlename', 'id'=>'middlename', 'size'=>'50','placeholder'=>'Middle Name','class'=>'form-control'))),
				array(form_label('Last name', 'lastname'), form_input(array('name'=>'lastname', 'id'=>'lastname', 'size'=>'50','placeholder'=>'Last Name','class'=>'form-control'))),
				array(form_label('Specialization', 'specialization'), form_input(array('name'=>'specialization', 'id'=>'specialization', 'size'=>'50','placeholder'=>'Specialization','class'=>'form-control'))),
				
				// array(form_button(array('name' => 'addclinic', 'id' => 'addclinic', 'content' => 'Add clinic')), $clinic),

				array(form_label('Clinic/s'), $clinics),
				
				array(form_label('Mobile Number', 'mobile_number'), form_input(array('name'=>'mobile_number', 'id'=>'mobile_number', 'size'=>'50','placeholder'=>'Mobile Number','class'=>'form-control'))),
				array(form_label('Contact Number', 'contact_number'), form_input(array('name'=>'contact_number', 'id'=>'contact_number', 'size'=>'50','placeholder'=>'Contact Number','class'=>'form-control'))),
				array(form_label('Fax Number', 'fax_number'), form_input(array('name'=>'fax_number', 'id'=>'fax_number', 'size'=>'50','placeholder'=>'Fax Number','class'=>'form-control'))),
				array(form_label('E-mail Address'), form_input(array('name'=>'email','id'=>'email','size'=>'50','placeholder'=>'E-mail','class'=>'form-control'))),
				array(form_label('Date Accredited', 'date_accredited'), form_input(array('name'=>'date_accredited', 'id'=>'date_accredited_doctors',  'value'=>'YYYY-MM-DD','class'=>'form-control'))),
				array(form_label('Status', 'status'), form_dropdown('status', array('ACCREDITED' => 'ACCREDITED', 'DIS-ACCREDITED' => 'DIS-ACCREDITED'))),
				array(form_label('Remarks', 'remarks'), form_input(array('name'=>'remarks', 'id'=>'remarks', 'size'=>'50','placeholder'=>'Remarks','class'=>'form-control'))),
				
				array('', form_submit(array('value'=>'Register','class'=>'btn btn-success')))
				);
$template = array(
			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
			'table_close'	=> '</table>'
			);
$this->table->set_template($template); 
echo $this->table->generate($inputs);
echo form_hidden('date_encoded', mdate('%Y-%m-%d', now()));
?>
<?php echo form_close(); ?>
</div>
