<!DOCTYPE html>
<html lang="eng">
	<head>
		<meta charset="UTF-8">
		<title>Records of <?php echo $name?></title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
		<script>
			$(document).ready(function() {
				$('#start').datepicker({format: 'yyyy-mm-dd'});
				$('#end').datepicker({format: 'yyyy-mm-dd'});
				$(".selectAll").click(function()
				{
					var checked_status = this.checked;
					$(".selMulti").each(function()
					{
						this.checked = checked_status;
					});
				});

				$('.table_list').hide();

				$('#toggleSlideTable').click(function()
				{
					$('.table_list').slideToggle('fast', function() {});
				});
			});
		</script>
	</head>
	<body class="main-bg">
		<br>
		<button id="toggleSlideTable" class="btn btn-default" style="background-color:white">Members</button>

		<div class="table_list">
			<div class="profile_name">Members of <?php echo $name?></div>

			<?php 
				if ($this->session->flashdata('result') != '') {
					echo $this->session->flashdata('result');
				}
			?>

			<?php echo validation_errors(); ?>

			<?php echo form_open_multipart('records/uphist/downloadTemp/11');?>

			<?php
				// $template = array(
				// 			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
				// 			'table_close'	=> '</table>'
				// 			);
				// $this->table->set_template($template); 
				// $this->table->add_row('<b>Notes/Remarks:</b> ',$notes);
				// echo $this->table->generate();
				// $inputs = array(
				// 				array(form_label('Download Template for Members', 'multiup'), form_submit(array('value'=>'Download','class'=>'btn btn-warning')))
				// 				);
				// echo $this->table->generate($inputs);
			?>

			<?php echo form_close(); ?>

			<?php echo form_open_multipart('utils/fileuploader/upto/company_insurance_members');?>

			<?php
				// $template = array(
				// 			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
				// 			'table_close'	=> '</table>'
				// 			);
				// $inputs = array(
				// 				array(form_label('Upload multiple Members', 'multicompany'), form_upload(array('name'=>'file', 'id'=>'multicompany','class'=>'form-group')), form_submit(array('value'=>'Upload','class'=>'btn btn-success')))
				// 				);
				// $this->table->set_template($template);
				// echo $this->table->generate($inputs);
			?>

			<?php echo form_close(); ?>

			LEGEND:</br>
			Black: Active</br>
			<font color='orange'>Orange: Warning! Will expire within a week!</font></br>
			<font color='green'>Green: On Hold</font></br>
			<font color='red'>Red: Expired/Deleted</font></br>

			<?php echo form_open('records/compins/search'); ?>

			<?php
				$viewAll =  anchor(base_url()."records/compins/members/".$compins_id, "View All Records",array('class'=>'btn btn-primary'));
				$this->table->add_row(form_label('<b>Search Member/s</b>', 'members'), form_input(array('name'=>'members', 'id'=>'members', 'size'=>'50','class'=>'form-control','placeholder'=>'Search Member/s')), form_submit(array('value'=>'Search','class'=>'btn btn-primary')), $viewAll);
				$template = array(
							'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
							'table_close'	=> '</table>'
							);
				$this->table->set_template($template); 
				echo $this->table->generate();
				echo form_hidden('compins_id', $compins_id);
			?>

			<?php echo form_close(); ?>

			<?php echo validation_errors(); ?>

			<?php echo form_open('records/members/multiSelect'); ?>

			<?php
				$template = array(
							'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
							'table_close'	=> '</table>'
							);
				$this->table->set_template($template); 
				$multi = $this->table->add_row(form_submit(array('name'=>'submit','value'=>'Delete','class'=>'btn btn-danger')),form_submit(array('name'=>'submit','value'=>'Update Status','class'=>'btn btn-warning')),form_dropdown('status', array('ACTIVE' => 'ACTIVE', 'EXPIRED' => 'EXPIRED', 'DELETED' => 'DELETED', 'ON HOLD' => 'ON HOLD')),form_checkbox(array('name'=>'selectAll','id'=>'selectAll','class'=>'selectAll')).' '.form_label('Select All'));
				$multi.= $this->table->add_row(form_label('Date Start'),form_input(array('name'=>'start','id'=>'start','size'=>'10','class'=>'form-control','placeholder'=>'YYYY-MM-DD')),form_label('Date End'),form_input(array('name'=>'end','id'=>'end','size'=>'10','class'=>'form-control','placeholder'=>'YYYY-MM-DD')));
				echo $this->table->generate($multi);

				if(empty($patients))
				{
					echo "<h3>No Record/s Found!!!</h3>";
				}
				else
				{
					if(isset($links))
					{
						$page = "<h4>Results: </h4>". @$links;
					}
					echo $page;
					date_default_timezone_set("Asia/Manila");
					echo '<div class="table_scroll">';
					$tmpl = array (
									'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover" id="table-bg">',

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
					$this->table->set_heading('', 'Name', 'Date of Birth', 'Age', 'Level/Position', 'Declaration Date', 'Start', 'End', 'Membership Status', 'Cardholder Type', 'Cardholder', 'Beneficiary', 'Remarks', 'Benefit Set Name');
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
								$key['status'] = status_update('patient',$field,$data,$id);
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
						//var_dump($key);
						// Build the custom actions links.
						// $actions = anchor(base_url()."records/compins/deleteMember/".$key[0]['id']."/".$id."/", "Delete");
						$selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key[0]['id']));
						// Adding a new table row.
						$this->table->add_row("<font color=".$color.">".$count++.".".$selMulti, anchor(base_url()."records/members/view/".$key[0]['id']."/", "<font color=".$color.">".$key[0]['lastname'].", ".$key[0]['firstname']." ".$key[0]['middlename'], array('target'=>'_blank')), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['dateofbirth'])), "<font color=".$color.">".computeAge($key[0]['dateofbirth']), 
							"<font color=".$color.">".$key[0]['level'], "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['declaration_date'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['start'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key[0]['end'])), "<font color=".$color.">".$key[0]['status'], 
							"<font color=".$color.">".$key[0]['cardholder_type'], "<font color=".$color.">".$key[0]['cardholder'], "", "<font color=".$color.">".$key[0]['remarks'], "<font color=".$color.">".$key['benefit_name'][0]['benefit_set_name']);
					}
					echo $this->table->generate();
					
					echo form_hidden('compins_id', $compins_id);
					echo form_hidden('location', 'records/members');
					// if(isset($links))
					// {
					// 	$page = "<b>Page results: </b>". @$links;
					// }
					echo '</div>';
					// echo $page;
				}
			?>
		</div>
	</body>
</html>