<script>
	$(document).ready(function(){
		$('#others_id').hide();
		$('#others').click(function(){
			$('#others_id').slideToggle('fast', function(){});
		});
	});
</script>
<br>
<?php echo validation_errors();?>
<?php
	$tmpl = array(
				'table_open' => '<table border="0" cellpadding="0" cellspacing="0">',
				'table_close' => '</table>'
				);
	$tmpl2 = array(
				'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
				'table_close' => '</table>'
				);
	$input = array(
			array(form_button(array('name'=>'others','id'=>'others','content'=>'Add Other Conditions/Exclusions','class'=>'btn btn-default')))
				);
	$this->table->set_template($tmpl);
	echo $this->table->generate($input);

	echo '<div id="others_id">';

	$condition_pdf = form_open_multipart('utils/fileuploader/upload_pdf/benefit_set_condition',array('id'=>'condition'));
	$condition_pdf_input = array(
						array(form_label('Upload PDF for Other Condition: '),form_upload(array('name'=>'file','id'=>'pdf_up','class'=>'form-group')),
						form_submit(array('name'=>'submit','value'=>'Upload','class'=>'btn btn-success')))
						);
	$this->table->set_template($tmpl2);
	$condition_pdf.= $this->table->generate($condition_pdf_input);
	$condition_pdf.= form_close();

	$exclusion_pdf = form_open_multipart('utils/fileuploader/upload_pdf/benefit_set_exclusion',array('id'=>'exclusion'));
	$exclusion_pdf_input = array(
						array(form_label('Upload PDF for Exclusion: '),form_upload(array('name'=>'file','id'=>'pdf_up','class'=>'form-group')),
						form_submit(array('name'=>'submit','value'=>'Upload','class'=>'btn btn-success')))
						);
	$this->table->set_template($tmpl2);
	$exclusion_pdf.= $this->table->generate($exclusion_pdf_input);
	$exclusion_pdf.= form_close();

	$uploads = array(
				array($condition_pdf),
				array($exclusion_pdf)
				);
	$this->table->set_template($tmpl2);
	$uploads_field = form_fieldset('Upload PDF File');
	$uploads_field.=$this->table->generate($uploads);
	$uploads_field.= form_fieldset_close();
	$uploads_input = array(array($uploads_field));
	echo $this->table->generate($uploads_input);

	echo form_open('records/benefits/addOthers');
	$this->table->set_template($tmpl2);
	$others = array(
			array(form_label('Other Conditions'),''),
			array(form_label('Other Conditions Name:'),form_input(array('name'=>'condition_name','id'=>'condition_name','size'=>'20','class'=>'form-control'))),
			array(form_label('Other Conditions Details:'),form_textarea(array('name'=>'condition_details','id'=>'condition_details','cols'=>'50','rows'=>'10','class'=>'form-control'))),
			array(form_label('Exclusions'),''),
			array(form_label('Exclusion Name:'),form_input(array('name'=>'exclusion_name','id'=>'exclusion_name','size'=>'20','class'=>'form-control'))),
			array(form_label('Exclusion Details:'),form_textarea(array('name'=>'exclusion_details','id'=>'exclusion_details','cols'=>'50','rows'=>'10','class'=>'form-control'))),
			array('',form_submit(array('name'=>'submit','value'=>'Register Conditions / Exclusions','class'=>'btn btn-success')))
				);
	$others_fieldset = form_fieldset('<b>Other Conditions / Exclusions</b>');
	$others_fieldset.= $this->table->generate($others);
	$others_fieldset.= form_fieldset_close();

	$others_input = array(array($others_fieldset));
	echo $this->table->generate($others_input);

	$template = array(
				'table_open'			=> '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

				'heading_row_start' 	=> '<tr>',
				'heading_row_end'		=> '</tr>',
				'heading_cell_start'	=> '<th>',
				'heading_cell_end'		=> '</th>',

				'row_start'				=> '<tr>',
				'row_end'				=> '</tr>',
				'cell_start'			=> '<td>',
				'cell_end'				=> '</td>',

				'row_alt_start'			=> '<tr>',
				'row_alt_end'			=> '</tr>',
				'cell_alt_start'		=> '<td>',
				'cell_alt_end'			=> '</td>',

				'table_close'			=> '</table>'
				);
	$this->table->set_template($template);
	$count1 = 1;
	$count2 = 1;

	// CONDITIONS TABLE
	$this->table->set_heading('','Condition Name','Condition Details','User','');

	foreach($condition as $value => $key)
	{
		if($key['filename'] != '')
		{
			$view_pdf = anchor(base_url().'records/uphist/view_pdf/benefit_set_condition/'.$key['filename'].'/','View attached PDF',array('class'=>'btn btn-xs btn-info','target'=>'_blank'));
		}
		else
		{
			$view_pdf = '';
		}
		$edit_condition = anchor(base_url()."records/benefits/editCondition/".$key['id']."/","Edit",array('class'=>'btn btn-warning btn-xs'));
		$delete_condition = anchor(base_url()."records/benefits/deleteCondition/".$key['id']."/","Delete",array('class'=>'btn btn-danger btn-xs'));
		$condition_details = $this->table->add_row($count1++.".",$key['condition_name'],'<pre>'.$key['condition_details'].'</pre>'.$view_pdf,$key['user'],$edit_condition.$delete_condition);
	}
	$condition_table = $this->table->generate($condition_details);

	//EXCLUSION TABLE
	$this->table->set_heading('','Exclusion Name','Exclusion Details','User','');

	foreach($exclusion as $value => $key)
	{
		if($key['filename'] != '')
		{
			$view_pdf = anchor(base_url().'records/uphist/view_pdf/benefit_set_exclusion/'.$key['filename'].'/','View attached PDF',array('class'=>'btn btn-info btn-xs','target'=>'_blank'));
		}
		else
		{
			$view_pdf = '';
		}
		$edit_exclusion = anchor(base_url()."records/benefits/editExclusion/".$key['id']."/","Edit",array('class'=>'btn btn-warning btn-xs'));
		$delete_exclusion = anchor(base_url()."records/benefits/deleteExclusion/".$key['id']."/","Delete",array('class'=>'btn btn-danger btn-xs'));
		$exclusion_details = $this->table->add_row($count2++.".",$key['exclusion_name'],'<pre>'.$key['exclusion_details'].'</pre>'.$view_pdf,$key['user'],$edit_exclusion.$delete_exclusion);
	}
	$exclusion_table = $this->table->generate($exclusion_details);

	//GENERATE ALL TABLES
	echo "<table cellpadding='4' cellspacing='0' border='0' id='benefit_others_tbl'>
			<tr>
				<th class='half_width'>CONDITIONS</th>
				<th class='half_width'>EXCLUSIONS</th>
			</tr>
			<tr>
				<td class='half_width'><div class='table_scroll'>".$condition_table."</div></td>
				<td class='half_width'><div class='table_scroll'>".$exclusion_table."</div></td>
			</tr>
		</table>
		</div>";
?>
<?php echo form_close();?>
