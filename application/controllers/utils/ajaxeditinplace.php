<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajaxeditinplace extends CI_Controller {
	/**
	 * Constructor
	 * 
	 * Loads MODEL and HELPER needed for class
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('ajaxeditinplace_model','',TRUE);
	}
	/**
	 * Function index
	 */
	function index()
	{
		$table = $this->input->post('table'); //table to use
		$key = $this->input->post('key'); //primary key
		$field = $this->input->post('id'); //column name
		$value = $this->input->post('value'); //value passed
		$result = $this->ajaxeditinplace_model->update($table, $key, $field, $value); //run function update on Ajaxeditinplace Model
		$isDate = strpos($field, 'date'); //checks if $field contains string 'date'

		if($isDate !== FALSE)
		{ //if it contains 'date' return modified data
			strpos($field, 'dateofbirth') !== FALSE ? $note=" Please refresh page to update patient's age.":$note=''; //if $field is dateofbirth, return with additional information
			echo mdate('%M %d, %Y', mysql_to_unix($result)).$note; //modify string to return human readable date (Jan 01, 1970)
		}
		else
		{
			echo $result;
		}
	}
	/**
	 * Function status
	 * 
	 * Returns options for a dropdown input
	 * 
	 * @access public
	 * @param String $selected 	if param is !NULL returns an array with a selected option
	 * @return json 			array encoded to json
	 */
	function status($selected = '')
	{
		$array = array(
						'ACTIVE' 	=>	'ACTIVE',
						'EXPIRED' 	=>	'EXPIRED',
						'DELETED'	=>	'DELETED',
						'ON HOLD'	=>	'ON HOLD'
						);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}
	/**
	 * Function cardholder_type
	 * 
	 * Returns options for a dropdown input
	 * 
	 * @access public
	 * @param String $selected 	if param is !NULL returns an array with a selected option
	 * @return json 			array encoded to json
	 */
	function cardholder_type($selected = '')
	{
		$array = array(
						'PRINCIPAL'	=>	'PRINCIPAL',
						'DEPENDENT'	=>	'DEPENDENT'
						);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function hospital_type($selected ='')
	{
		$array = array(
						'REGULAR'	=>	'REGULAR',
						'BLANKET'	=>	'BLANKET',
						'MAXIMUM'	=>	'MAXIMUM'
						);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function hospital_category($selected ='')
	{
		$array = array(
						'LEVEL 1'	=>	'LEVEL 1',
						'LEVEL 2'	=>	'LEVEL 2',
						'LEVEL 3'	=>	'LEVEL 3',
						'LEVEL 4'	=>	'LEVEL 4'
						);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function dentistsdoctors_type($selected ='')
	{
		$array = array(
						'MD'	=>	'MD',
						'Dentist'	=>	'Dentist'
						);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function accred($selected ='')
	{
		$array = array(
						'ACCREDITED'	=>	'ACCREDITED',
						'DIS-ACCREDITED'	=>	'DIS-ACCREDITED',
						'DO NOT PROMOTE' => 'DO NOT PROMOTE'
						);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function classification($selected='')
	{
		$array = array(
						'HOSPITAL'=>'HOSPITAL',
						'CLINIC'=>'CLINIC',
						'MULTISPECIALTY'=>'MULTISPECIALTY',
						'DIAGNOSTIC'=>'DIAGNOSTIC',
						'SPECIALTY CLINIC'=>'SPECIALTY CLINIC'
						);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function hospital_account($selected='')
	{
		$hospital = $this->ajaxeditinplace_model->getAllRecords('hospital');

		foreach($hospital as $key => $value)
		{
			$array[$value['name']] = $value['name'];
		}
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function accounting_terms($selected='')
	{
		$array = array(
					'15' => '15 Days',
					'30' => '30 Days'
					);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}

	function accounting_vat($selected='')
	{
		$array = array(
					'VAT HOSP' => 'VAT HOSP',
					'NON-VAT HOSP' => 'NON-VAT HOSP',
					'NON-VAT PF' => 'NON-VAT PF'
					);
		if($selected != '') $array['selected'] = $selected;
		echo json_encode($array);
	}
}
?>