<?php
echo "<b>LEGEND:</b></br>";
echo "Black: Active</br>";
echo "<font color='orange'>Orange: Warning! Will expire within a week!</font></br>";
echo "<font color='green'>Green: On Hold</font></br>";
echo "<font color='red'>Red: Expired/Deleted</font></br>";
echo "<div class='table_scroll'>";
date_default_timezone_set("Asia/Manila");
$date = date_default_timezone_get();
$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="0" cellspacing="0" class="table table-hover table-bordered" id="example">',

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
$this->table->set_heading('','Patient Name','Membership Status','Company - Insurance','Date of Birth','Age','Level / Plan','Declaration Date','Validity Start - End',
					'Cardholder Type','Cardholder','Company - Insurance Notes','Remarks','Benefit Set Name');

$count = 1;
$currentDate = date('Y-m-d');
foreach($patients as $value => $key)
{
	if(strtolower($key['status']) == 'active')
	{
		$newdate = strtotime('-7 day',strtotime($key['end']));
		$newdate = date('Y-m-d',$newdate);
		$expires = (strtotime($key['end']) - strtotime(date('Y-m-d'))) / (60*60*24);

		if($expires > 1)
		{
			$day = ' days';
		}
		else
		{
			$day = ' day';
		}

		if($expires < 0)
		{
			$id = $key['id'];
			$field = 'status';
			$data = 'EXPIRED';
			$key['status'] = status_update('patient',$field,$data,$id);
		}

		if($newdate <= $currentDate)
		{
			$color = 'orange';
			$key['status'] = $key['status'].' will expire in '.$expires.$day.'.';
		}
		else
		{
			$color = 'black';
		}
	}
	elseif(strtolower($key['status']) == 'expired' || strtolower($key['status']) == 'deleted')
	{
		$color = 'red';
	}
	else
	{
		$color = 'green';
	}

	if($key['compins'][0]['company'] == "" || $key['compins'][0]['insurance'] == "" || is_null($key['compins'][0]))
	{
		$compinsCI = "<font color=".$color.">"." No existing Company - Insurance ";
	}
	else
	{
		$compinsCI = "<font color=".$color.">".$key['compins'][0]['company']." - ".$key['compins'][0]['insurance'];
	}

	if(isset($key['benefit_name'][0]['id']))
	{
		$benefit_link = '<font color='.$color.'><b>'.$key['benefit_name'][0]['benefit_set_name'].'</b><br>Remaining Overall MBL: <b>PHP. '.$key['overall_mbl'].'</b>'.'<br>Condition Name:<br>'.$key['benefit_name'][0]['condition_name'].'<br>Exclusion Name:<br>'.$key['benefit_name'][0]['exclusion_name'];
	}
	else
	{
		$benefit_link = '';
	}

	$this->table->add_row("<font color=".$color.">".$count++.".","<font color=".$color.">".$key['lastname'].", ".$key['firstname']." ".$key['middlename'],"<font color=".$color.">".$key['status'],$compinsCI, "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['dateofbirth'])), "<font color=".$color.">".computeAge($key['dateofbirth']), 
		"<font color=".$color.">".$key['level'], "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['declaration_date'])), "<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['start'])).' - '."<font color=".$color.">".mdate('%M %d, %Y', mysql_to_unix($key['end'])),
		"<font color=".$color.">".$key['cardholder_type'], "<font color=".$color.">".$key['cardholder'],"<font color=".$color.">".$key['compins'][0]['notes'], "<font color=".$color.">".$key['remarks'], $benefit_link);
}
echo $this->table->generate();
echo '</div>';
?>