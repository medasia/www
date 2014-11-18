<script>
$(document).ready(function() {
	$('#formAdd').hide();
	$('#toggleSlide').click(function() {
		$('#formAdd').slideToggle('fast', function() {});
	});

	// DETAILS CLONER
	var cloneCount = 1;
	$('#addDetails').click(function() {
		cloneCount++;
		$('#detailsSet').clone().attr('id','detailsSet'+cloneCount).appendTo('#other_details');
	});
	$('#removeDetails').click(function() {
		if(cloneCount > 1)
		{
			$('#detailsSet'+cloneCount).remove();
			cloneCount--;
		}
	});

	// DYNAMIC CHECKBOX
	$('.amount_per_day').click(function(){
		var checked_status = this.checked;
		$('.days').each(function(){
			this.checked = checked_status;
		});
	});

	// FORM VALIDATOR
	jQuery.validator.addMethod("specialChars", function(value, element) {
        var regex = new RegExp("^[a-zA-Z0-9- ]+$");
        var key = value;

        if (!regex.test(key)) {
           return false;
        }
        return true;
    });
	$("form").validate({
    	rules: {
    		benefit_name: {
    		required: true,
    		maxlength: 50,
    		specialChars: true
    		},
    		benefit_type: {
    			required: true
    		}
    	},
    	messages: {
    		benefit_name: {
    		required: 'This field is required',
    		maxlength: "You've reached the character limit for this field",
    		specialChars: "*PLEASE USE THIS ALLOWED CHARACTERS ONLY: a-z A-Z 0-9 - "
    		},
    		benefit_type: {
    			required: 'This field is required'
    		}
    	}
	});

	// CHARACTER COUNTER
	$('#benefit_name').limit({
		limit: 50,
		id_result: 'limit'
	});
});
</script>

<!-- CREATE BENEFIT LIST -->

<h1>Benefits</h1>
<?php
if($this->session->flashdata('result') != '')
{
	echo $this->session->flashdata('result').'<br>';
}
?>
<button id='toggleSlide' class='btn btn-default'>Add New Benefit</button>
<div id='formAdd'>
<?php echo validation_errors(); ?>
<?php echo form_open('records/benefits/register'); ?>
<?php
$otherDet = array(
				array('',''),
				array(form_label('Other Limits:', 'otherdetails'), form_input(array('name'=>'otherDetails[]','id'=>'otherDetails','size'=>'20','class'=>'form-control'))),
				array('','')
				);
$template = array(
			'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0" id="detailsSet">',
			'table_close'	=>	'</table>'
			);
$this->table->set_template($template);
$addDetails = form_fieldset('<b>Other Limits - UNIQUE ENTRY!</b>', array('id'=>'other_details'));
$addDetails.= $this->table->generate($otherDet);
$addDetails.= form_fieldset_close();

$inputs = array(
				array('','',''),
				array(form_label('Benefit Type: ', 'benefit_type'), form_dropdown('benefit_type', array(''=>'','IP'=>'In-Patient','OP'=>'Out-Patient','IP-OP' =>'In and Out Patient'))),
				array(form_label('Benefit Name: ', 'benefit_name'), form_input(array('name'=>'benefit_name', 'id'=>'benefit_name', 'size'=>'20','class'=>'form-control')),'<div id="limit"></div>'),
				array(form_label('Limits: ','details'),
					form_checkbox(array('name'=>'days','id'=>'days','value'=>'days','class'=>'days')).' Days/Limit'.'<br>'.
					form_checkbox(array('name'=>'amount','id'=>'amount','value'=>'amount', 'class'=>'amount')).' Amount'.'<br>'.
					form_checkbox(array('name'=>'amount_per_day','id'=>'amount_per_day','value'=>'amount per day','class'=>'amount_per_day')).' Amount Per Day<br>'.
					form_checkbox(array('name'=>'as_charged','id'=>'as_charged', 'value'=>'as charged','class'=>'as_charged')).' As Charged<br>'.
					form_checkbox(array('name'=>'by_modalities','id'=>'by_modalities','value'=>'by modalities','class'=>'by_modalities')).' By Modalities<br>'),
					// form_checkbox(array('name'=>'per_illness','id'=>'per_illness','value'=>'per illness','class'=>'per_illness')).'Per Illness'),

				array(form_button(array('name'=>'addDetails','id'=>'addDetails', 'content'=>'Add Limits','class'=>'btn btn-info btn-xs')),$addDetails.'<br>'.
					form_button(array('name'=>'removeDetails','id'=>'removeDetails','content'=>'Remove Limits','class'=>'btn btn-danger btn-xs'))),
				array(form_label('Remarks / Notes:'),form_textarea(array('name'=>'remarks','id'=>'remarks','cols'=>'30','rows'=>'5','class'=>'form-control'))),
				array('', form_submit(array('name'=>'submit','value'=>'Register','class'=>'btn btn-success')))
				);
$template = array(
				'table_open'	=>	'<table border="0" cellpadding="4" cellspacing="0">',
				'table_close'	=> '</table>'
				);
$this->table->set_template($template);
echo $this->table->generate($inputs);
?>
<?php echo form_close(); ?>

<!-- /SHOW BENEFIT LIST -->
<br>
	<div class="table_scroll">
	<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered">
		<tr>
			<th> </th>
			<th>Availment Type</th>
			<th>Benefit Name</th>
			<th>Notes / Remarks</th>
			<th>Details</th>
			<th>User</th>
			<th> </th>
		</tr>
			<?php
				$count=1;
				foreach($query as $row)
				{
					if($row->benefit_type == "IP")
					{
						$benefit_type = "IN-PATIENT";
					}
					elseif($row->benefit_type == "OP")
					{
						$benefit_type = "OUT-PATIENT";
					}
					elseif($row->benefit_type == "IP-OP")
					{
						$benefit_type = "IN AND OUT PATIENT";
					}

					$benefit_name = $row->benefit_name;
					$details = benefit_details($benefit_name);

					$remarks = benefit_remarks($benefit_name);
					if($remarks != NULL)
					{
						$benefitRemarks = $remarks;
					}
					else
					{
						$benefitRemarks = '';
					}

					
					echo '<tr><td align="center">'.$count.'.</td>';
					echo '<td align="center">'.$benefit_type.'</td>';
					echo '<td align="center">'.$benefit_name.'</td>';
					echo '<td>'.$benefitRemarks.'</td><td align="center">';

					foreach($details as $value => $key)
					{
						echo '<table>';
						echo '<tr><td align="center">'.$key['details'].'</td></tr>';
						echo '</table>';
					}
					echo '<td align="center">'.$row->user.'</td>';

					// $per_illness = stripos($benefit_name,'PER ILLNESS');
					// if($per_illness !== FALSE)
					// {
					// 	$delete = '';
					// }
					// else
					// {
						$delete = anchor(base_url().'records/benefits/delete/'.$row->benefit_type.'/'.$row->benefit_name.'/','Delete',array('class'=>'btn btn-danger btn-xs'));
					// }

					// $edit = anchor(base_url()."records/benefits/edit/".$row->benefit_name."/", "Edit");
					// $delete = anchor(base_url()."records/benefits/delete/".$row->benefit_type."/".$row->benefit_name."/", "Delete",array('class'=>'btn btn-danger btn-xs'));
					
					echo '</td><td align="center">'.@$edit."".$delete.'</td></tr>';
					$count++;
				}
			?>
	</table>
</div>
</div>