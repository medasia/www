<script>
$(document).ready(function() {
		$('#formAddd').hide();
		$('#toggleSlidee').click(function() {
		$('#formAddd').slideToggle('fast', function() {});
	});

	$('#level, #level_label').hide();
	$('#compins').change(function()
	{
		var compins_id = $('#compins').val();
		if(compins_id != "")
		{
			var post_url = "<?=base_url()?>records/benefits/getLevel/"+compins_id;
			$.ajax({
				type: "POST", 
				url: post_url,
				success: function(levels)
				{
					$('#level').empty();
					$('#level, #level_label').show();
					$.each(levels, function(level)
					{
						var opt = $('<option />');
							opt.val(level);
							opt.text(level);
							$('#level').append(opt);
					});
				}
			});
		}
		else
		{
			$('#level').empty();
			$('#level, #level_label').hide();
		}
	});

	// AUTOCOMPLETE
	$('#condition_name_id').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/benefit_set_condition', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				success: function(data){
					if(data.response == 'true')
					{
						add(data.message);
					}
				}
			});
		}
	});
	$('#exclusion_name_id').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/benefit_set_exclusion', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				success: function(data){
					if(data.response == 'true')
					{
						add(data.message);
					}
				}
			});
		}
	});
	$('#company_insurance').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/company_insurance', //Controller where search is performed
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data) {
					if(data.response =='true'){
						add(data.message);
					}
				}
			});
		},
		select: function(event, ui) {
				$('[name=compins_id]').val(ui.item ? ui.item.Id : '');
		}
	});

	// FORM VALIDATION
	jQuery.validator.addMethod("specialChars", function( value, element ) {
        var regex = new RegExp("^[a-zA-Z0-9- ]+$");
        var key = value;

        if (!regex.test(key)) {
           return false;
        }
        return true;
    });
	$("form#benefit_id").validate({
    			rules: {
    				company_insurance: {
    					required: true,
    				},
    				benefit_name: {
    					required: true,
    					maxlength: 50,
    					specialChars: true
    				},
    				cardholder_type: {
    					required: true
    				}
    			},
    			messages: {
    				company_insurance: {
    					required: 'This field is required'
    				},
    				benefit_name: {
    					required: 'This field is required',
    					maxlength: "You've reached the limit for this field",
    					specialChars: "*PLEASE USE THIS ALLOWED CHARACTERS ONLY: a-z A-Z 0-9 - "
    				},
    				cardholder_type: {
    					required: 'This field is required'
    				}
    			}
			});

	// CHOSEN SELECTION
	$("#chosen_rightIP, #chosen_rightOP, #chosen_rightIPOP").chosen({
	    	no_results_text: "Benefit not found!!",
	    	search_contains: 'TRUE',
	    	width: "100%",
	    	placeholder_text_multiple: " "
		});

	// CHARACTER COUNTER
	$('#benefit_schedule').limit({
			limit: 50,
			id_result: 'schedule_limit'
		});

	$('#chosen_rightIP').change(function(){
		var count = 0;
		$('#chosen_rightIP option:selected').each(function(){
			count += $(this).length;
		});
		$('#count_ip').html(count+' selected');
	});

	$('#chosen_rightOP').change(function(){
		var count = 0;
		$('#chosen_rightOP option:selected').each(function(){
			count += $(this).length;
		});
		$('#count_op').text(count+' selected');
	});

	$('#chosen_rightIPOP').change(function(){
		var count = 0;
		$('#chosen_rightIPOP option:selected').each(function(){
			count += $(this).length;
		});
		$('#count_ipop').text(count+' selected');
	});
	});
</script>

<button id='toggleSlidee' class='btn btn-default'>Create Schedule of Benefits</button>
<div id='formAddd'>
<?php echo validation_errors(); ?>
<?php echo form_open('records/benefits/create',array('id'=>'benefit_id')); ?>
	<table cellpadding="4">
		<tr>
			<th>COMPANY-INSURANCE:</th>
			<td>
				<input type="text" id="company_insurance" name="company_insurance" size="165" class="form-control">
			</td>
		</tr>
			<th>CARDHOLDER TYPE:</th>
			<td class=cls>
			<select name="cardholder_type">
					<option value=''></option>
					<option value="Principal">Principal</option>
					<option value="Dependent">Dependent</option>
					<option value="Principal and Dependent">Principal and Dependent</option>
			</select>
			</td>
		</tr>
		<tr>
			<th>In-Patient <div id='count_ip'></div></th>
			<td>
				<select multiple style="width:100%" name="rightIP[]" id="chosen_rightIP" class="chosen-select">
					<?php foreach($ip as $row)
					{
						$data = $row->benefit_name;
						$remarks = benefit_remarks($data);

						$benefit_details = benefit_details($data);
						foreach($benefit_details as $key => $value)
						{
							$details.= $value['details'];
							if($key != count($benefit_details)-1)
							{
								$details.= ', ';
							}
						}

						if($remarks != NULL)
						{
							echo "<option value='$data'>".$data.' - ('.$details.') - '.$remarks."</option>";
							unset($details);
						}
						else
						{
							echo "<option value='$data'>$data - ($details)</option>";
							unset($details);
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Out-Patient <div id='count_op'></div></th>
			<td>
				<select multiple style="width:100%" name="rightOP[]" id="chosen_rightOP" class="chosen-select">
					<?php foreach($op as $row)
					{
						$data = $row->benefit_name;
						$remarks = benefit_remarks($data);

						$benefit_details = benefit_details($data);
						foreach($benefit_details as $key => $value)
						{
							$details.= $value['details'];
							if($key != count($benefit_details)-1)
							{
								$details.= ', ';
							}
						}
						if($remarks != NULL)
						{
							echo "<option value='$data'>".$data.' - ('.$details.') - '.$remarks."</option>";
							unset($details);
						}
						else
						{
							echo "<option value='$data'>$data - ($details)</option>";
							unset($details);
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>In and Out Patient <div id='count_ipop'></div></th>
			<td>
				<select multiple style="width:100%" name="rightIP-OP[]" id="chosen_rightIPOP" class="chosen-select">
					<?php foreach($ipop as $row)
					{
						$data = $row->benefit_name;
						$remarks = benefit_remarks($data);

						$benefit_details = benefit_details($data);
						foreach($benefit_details as $key => $value)
						{
							$details.= $value['details'];
							if($key != count($benefit_details)-1)
							{
								$details.= ', ';
							}
						}

						if($remarks != NULL)
						{
							echo "<option value='$data'>".$data.' - ('.$details.') - '.$remarks."</option>";
							unset($details);
						}
						else
						{
							echo "<option value='$data'>$data - ($details)</option>";
							unset($details);
						}
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	<table cellpadding="4">
		<tr>
			<th>Other Conditions/Exclusions</th>
			<td><b>Conditions<br></b>
				<input type="text" name="condition_name" id="condition_name_id" class="form-control" size="75">
			</td>
			<td>&nbsp;</td>
			<td><b>Exclusions<br></b>
				<input type="text" name="exclusion_name" id="exclusion_name_id" class="form-control" size="75">
			</td>
		</tr>
		<tr>
			<th>Benefit Schedule Name:</th>
			<td><input type="text" name="benefit_name" id="benefit_schedule" class="form-control"></td>
			<td>&nbsp;</td>
			<td><span id='schedule_limit'></span><input type="submit" value="Save" class="btn btn-success"></td>
		</tr>
	</table>
	<?php echo form_hidden('compins_id');?>
	<?php echo form_close();?>
</div>