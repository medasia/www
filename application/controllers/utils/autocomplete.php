<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Autocomplete extends CI_Controller
{
	/**
	 * Constructor
	 * 
	 * Loads MODELS, LIBS and HELPERS needed for class
	 */
	function __construct()
	{
		parent::__construct();
		$models = array(
					    'records_model' => '',
					    'autocomplete_model' => ''
						);
		$this->load->model($models,'',TRUE);
	}
	/**
	 * Function from
	 * 
	 * @access 	public
	 * @param 	String 	$table 		name of table on database to fetch data
	 * @return 	boolean|array|json 	data fetched from database
	 */
	function from($table) {

		$session_data = $this->session->userdata('logged_in');
		$sess_data = $session_data;
		$keyword = $this->input->post('term'); //From jQuery's AJAX autocomplete
		$data['response'] = 'false'; //Set default response
		switch($table) {
			case 'cardholder': //if fetching data for cardholder set table to patient
			case 'operations_patient':
			case 'operations_er_register':
			case 'accounts-members':
				$result = $this->autocomplete_model->getRecord('patient', $keyword);
				break;
			case 'compins-comp': //if fetching data for company for company insurance set table to company
				$result = $this->autocomplete_model->getRecord('company', $keyword);
				break;
			case 'compins_insurance': //if fetching data for insurance for company insurance set table to insurance
				$result = $this->autocomplete_model->getRecord('insurance', $keyword);
				break;
			case 'compins-brokers': //if fetching data for insurance for company insurance set table to insurance
				$result = $this->autocomplete_model->getRecord('brokers', $keyword);
				break;
			case 'compins':
				$result = $this->autocomplete_model->getRecord('company_insurance', $keyword);
				break;
			case 'verifications_hospclinic':
			case 'hospclinic':
			case 'operations_asp':
				$result = $this->autocomplete_model->getRecord('hospital', $keyword);
				break;
			case 'verifications_physician':
			case 'dentistsdoctors':
			case 'operations_dnd':
				$result = $this->autocomplete_model->getRecord('dentistsanddoctors', $keyword);
				break;
			case 'emerroom':
			case 'operations_er_card':
				$result = $this->autocomplete_model->getRecord('emergency_room', $keyword);
				break;
			case 'hospaccnt':
				$result = $this->autocomplete_model->getRecord('hospital_account', $keyword);
				break;
			case 'verifications_hospclinic_branch':
				$result = $this->autocomplete_model->getRecordByField('hospital', 'branch', $keyword, 'branch');
				break;
			case 'operations_admission':
				$result = $this->autocomplete_model->getRecord('admission_report_test',$keyword);
				break;
			case 'operations_monitoring':
				$result = $this->autocomplete_model->getRecord('monitoring',$keyword);
				break;
			case 'operations_verifications':
				$result = $this->autocomplete_model->getRecord('availments_test',$keyword);
				break;
			case 'diagnosis':
				$result = $this->autocomplete_model->getRecord('diagnosis',$keyword);
				break;
			case 'benefit_set_condition':
				$result = $this->autocomplete_model->getRecord('benefits.benefit_set_condition',$keyword);
				break;
			case 'benefit_set_exclusion':
				$result = $this->autocomplete_model->getRecord('benefits.benefit_set_exclusion',$keyword);
				break;
			default: //if not, set table to whatever the URI tells
				$result = $this->autocomplete_model->getRecord($table, $keyword); //Model DB search
				break;
		}
		if($result > 0) {
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			$data['message2'] = '';
			foreach($result as $key => $value) {
				switch($table) {
					case 'company_insurance': //returns <company name> - <insurance name> (start date - end date) *Id is hidden, to be used on hidden textbox once user selects a suggestion*
						$data['message'][] = array('label'=>$value['company']."-".$value['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($value['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($value['end'])).")", 'value'=>$value['company']."-".$value['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($value['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($value['end'])).")", 
							'compins_id'=>$value['id']);
						break;
					case 'compins-comp': //returns <company name>
						$data['message'][] = array('label'=>$value['name'], 'value'=>$value['name'], 'company_id' => $value['id']);
						break;
					case 'compins-brokers':
						$data['message'][] = array('label'=>$value['name'], 'value'=>$value['name'], 'broker_id' => $value['id']);
							break;
					case 'compins_insurance': //returns <insurance name>
						$data['message'][] = array('label'=>$value['name'], 'value'=>$value['name'], 'insurance_id' => $value['id']);
						break;
					case 'accounts-members': //returns <members name>
						$data['message'][] = array('label'=>$value['lastname'].', '.$value['firstname'].' '.$value['middlename'], 'value'=>$value['lastname'].', '.$value['firstname'].' '.$value['middlename'], 'patient_id' => $value['id']);
						break;
					case 'verifications_hospclinic':
						$data['message'][] = array('label'=>$value['name'], 'value'=>$value['name'],'branch'=>$value['branch']);
						// $data['message2'] = $value['branch'];
						break;
					case 'verifications_hospclinic_branch':
						$data['message'][] = array('label'=>$value['branch'], 'value'=>$value['branch']);
						break;
					case 'verifications_physician':
						$data['message'][] = array('label'=>$value['type'].'. '.$value['firstname'].' '.$value['middlename'].' '.$value['lastname'], 'value'=>$value['type'].'. '.$value['firstname'].' '.$value['middlename'].' '.$value['lastname']);
						break;
					case 'verifications_special_loa':
						$data['message'][] = array('label'=>$value['code'], 'value'=>$value['code'],'hospital_name'=>$value['hospital_name'],'hospital_branch'=>$value['hospital_branch'],'physician'=>$value['physician'],'availment_type'=>$value['availment_type']);
						break;
					case 'diagnosis':
						$data['message'][] = array('label'=>$value['diagnosis'], 'value'=>$value['diagnosis']);
						break;
					case 'benefit_set_condition':
						$data['message'][] = array('label'=>$value['condition_name'], 'value'=>$value['condition_name']);
						break;
					case 'benefit_set_exclusion':
						$data['message'][] = array('label'=>$value['exclusion_name'], 'value'=>$value['exclusion_name']);
						break;
					case 'compins':
						$memberCount = $this->records_model->getCountByField('patient_company_insurance', 'company_insurance_id', $value['id']);
						$result[$key]['membercount'] = $memberCount;
						$data['compins'] = $result;
						// $data['compins']['members'] = "VALUE";
						break;
					case 'cardholder': //returns <lastname>, <firstname> <middlename>
						$data['message'][] = array('label'=>$value['lastname'].", ".$value['firstname']." ".$value['middlename'], 'value'=>$value['lastname'].", ".$value['firstname']." ".$value['middlename'],'birthdate'=>$value['dateofbirth'],'Id'=>$value['id']);
						break;
					case 'patient':
					case 'operations_patient':
					case 'operations_er_register':
						$patientCompIns = $this->records_model->getRecordByField('patient_company_insurance', 'patient_id', $value['id']);
						foreach($patientCompIns as $pci)
						{
							$compIns = $this->records_model->getRecordById('company_insurance', $pci['company_insurance_id']);
							$result[$key]['compins'] = $compIns;

							// $benefit_name = $this->records_model->getRecordByMultiField('benefits.benefitset_info',
							// 														array(
							// 															'compins_id' => $pci['company_insurance_id'],
							// 															'level' => $value['level']
							// 															));
							// foreach($benefit_name as $bnkey => $bndetails)
							// {
							// 	if(($bndetails['cardholder_type'] == ucfirst(strtolower($value['cardholder_type'])) ) || $bndetails['cardholder_type'] == 'Principal and Dependent')
							// 	{
							// 		$result[$key]['benefit_name'] = $benefit_name;
							// 	}
							// }

							// $mbl = $this->records_model->getRecordByField('benefits_overall_mbl','patient_id',$value['id']);
							// foreach($mbl as $blkey => $blval)
							// {
							// 	$result[$key]['overall_mbl'] = $blval['remaining_overall_mbl'];
							// }
							// $result[$key]['benefit_name'] = $benefit_name;
						}
						$data['patients'] = $result;
						// echo '<pre>';
						// var_dump($data);
						break;
					case 'operations_admission':
						// @$specialist = $this->records_model->getRecordsByTwoFields('admission_specialist','code','time_generated',$value['code'],$value['time_generated']);
						$specialist = $this->records_model->getRecordByMultiField('admission_specialist',
																			array(
																				'code' => $value['code'],
																				'time_generated' => $value['time_generated']
																				));
						$diagnosis = $this->records_model->getRecordByField('availments_diagnosis','code',$value['code']);
						foreach($diagnosis as $dgnsis)
						{
							$result[$key]['diagnosis'][] = $dgnsis;
						}
						foreach(@$specialist as $splst)
						{
							if(@$splst)
							{
								$result[$key]['specialist'][] = $splst;
							}
							else
							{
								$result[$key]['specialist'] = '';
							}
						}
						$data['admission'] = $result;
						break;
					case 'operations_monitoring':
						$result[$key]['diagnosis'] = $this->records_model->getRecordByField('availments_diagnosis','code',$value['code']);
						$data['monitoring'] = $result;
						break;
					case 'operations_special_verifications':
						$data['result'] = $result;
						break;
					case 'operations_verifications':
						$labs = $this->records_model->getRecordByField('lab_test_test','availments_id',$value['id']);
						@$labInfo = array('lab_test','amount');

						if($labs)
						{
							foreach($labs as $lkey => $lval)
							{
								foreach($labInfo as $field)
								{
									@$result[$key]['lab_test_test'][$lkey][$field] = $lval[$field];
								}
							}
						}
						else
						{
							@$result[$key]['lab_test_test'] = NULL;
						}
						@$result[$key]['benefits_in-out_patient'] = $this->records_model->getRecordByField('benefits_in-out_patient','availment_id',$value['id']);
						@$result[$key]['benefits_others'] = $this->records_model->getRecordByField('benefits_others','availment_id',$value['id']);
						@$result[$key]['benefits_others_as_charged'] = $this->records_model->getRecordByField('benefits_others_as_charged','availment_id',$value['id']);
						$result[$key]['diagnosis'] = $this->records_model->getRecordByField('availments_diagnosis','code',$value['code']);
						$result[$key]['specialist'] = $this->records_model->getRecordByField('admission_specialist','code',$value['code']);
						// @$result[$key]['compins_notes'] = $this->records_model->getRecordsByTwoFields('company_insurance','company','insurance',$value['company_name'],$value['insurance_name']);
						$result[$key]['compins_notes'] = $this->records_model->getRecordByMultiField('company_insurance',
																								array(
																									'company' => $value['company_name'],
																									'insurance' => $value['insurance_name']
																									));
						$result[$key]['illness'] = $this->records_model->getRecordByField('patient_illness','loa_code',$value['code']);
						@$data['result'] = $result;
						break;
					case 'dentistsdoctors':
					case 'operations_dnd':
						// $clinics = $this->records_model->getRecordByField('clinics', 'dentistsanddoctors_id', $value['id']);
						// $clinic_info = array(
						// 					'clinic_name', 'hospital_name',
						// 					'street_address', 'subdivision_village',
						// 					'barangay', 'city',
						// 					'province','region','clinic_sched'
						// 					);
						// if($clinics) {
						// 	foreach($clinics as $ckey => $cval) {
						// 		foreach($clinic_info as $field) {
						// 			$result[$key]['clinics'][$ckey][$field] = $cval[$field];
						// 			// $result[$key]['clinics'] .= $cval[$field];
						// 		}
						// 	}
						// } else {
						// 	$result[$key]['clinics'] = NULL;
						// }
						
						$data['dentistsdoctors'] = $result;
						break;
					case 'company':
						$comp_ins = $this->records_model->getRecordByField('company_insurance','company',$value['name']);
						foreach($comp_ins as $ci_key => $ci_value)
						{
							$memberCount = $this->records_model->getCountByField('patient_company_insurance', 'company_insurance_id', $ci_value['id']);
							$comp_ins[$ci_key]['membercount'] = $memberCount;
						}
						$result[$key]['comp_ins'] = $comp_ins;
						$data[$table] = $result;
						break;
					case 'hospclinic':
					case 'emerroom':
					case 'operations_er_card':
						$patient = $this->records_model->getRecordById('patient',$value['patient_id']);
						foreach($patient as $pkey => $ptnt)
						{
							if($ptnt)
							{
								$result[$key]['patient_name'] = $ptnt['lastname'].', '.$ptnt['firstname'].' '.$ptnt['firstname'];
							}
						}
						$data[$table] = $result;
						break;
					case 'insurance':
					case 'hospaccnt':
					case 'operations_asp':
					case 'brokers':
						$data[$table] = $result;
						break;
				}
			}
		}
		switch($table) {
			case 'company_insurance': 
			case 'cardholder':
			case 'compins-comp':
			case 'compins-brokers':
			case 'compins_insurance':
			case 'verifications_hospclinic':
			case 'verifications_hospclinic_branch':
			case 'verifications_physician':
			case 'diagnosis':
			case 'benefit_set_condition':
			case 'benefit_set_exclusion':
			case 'verifications_special_loa':
			case 'accounts-members':
				echo json_encode($data); //returns encoded json array to be parsed by jQuery and displayed as SELECT on autocomplete
				break;
			case 'patient':
				if( ! $result)
				{
					return false;
				}
				// $session_data = $this->session->userdata('logged_in');
				// $sess_data = $session_data;
				// if($sess_data['usertype'] == 'admin_assoc')
				// {
				// 	$this->load->view('records/members/members_results_assoc_view', $data); //loads VIEW with fetched data from DB
				// }
				// else
				// {
				if($sess_data['usertype'] == 'accounting')
				{
					$this->load->view('records/members/members_results_view_view', $data);
				}
				else
				{
					$this->load->view('records/members/members_results_view', $data); //loads VIEW with fetched data from DB
				}
				// }
				break;
			case 'operations_patient':
			case 'operations_er_card':
				if( ! $result)
				{
					return false;
				}
				if($sess_data['usertype'] == 'admin_assoc')
				{
					$this->load->view('operations/'.$table.'_results_assoc_view', $data); //loads VIEW with fetched data from DB
				}
				elseif($sess_data['usertype'] == 'ops')
				{
					$this->load->view('operations/'.$table.'_results_ops_view', $data); //loads VIEW with fetched data from DB
				}
				else
				{
					$this->load->view('operations/'.$table.'_results_view', $data);
				}
				break;
			case 'operations_admission':
				if(!$result)
				{
					return FALSE;
				}
				else
				{
					$this->load->view('operations/'.$table.'_report_view',$data);
				}
				break;
			case 'operations_monitoring':
				if(!$result)
				{
					return FALSE;
				}
				else
				{
					$this->load->view('operations/'.$table.'_report_view',$data);
				}
				break;
			case 'operations_verifications':
				if(!$result)
				{
					return FALSE;
				}
				else
				{
					$this->load->view('verifications/verifications_members_view',$data);
				}
				break;
			case 'operations_special_verifications':
				if(!$result)
				{
					return FALSE;
				}
				else
				{
					$this->load->view('verifications/special_verifications_members_view',$data);
				}
				break;
			case 'operations_asp':
			case 'operations_dnd':
				// if( ! $result)
				// {
				// 	return false;
				// }
				// $session_data = $this->session->userdata('logged_in');
				// $sess_data = $session_data;
				// if($sess_data['usertype'] == 'admin_assoc')
				// {
				// 	$this->load->view('operations/'.$table.'_results_assoc_view', $data); //loads VIEW with fetched data from DB
				// }
				// else
				// {
					$this->load->view('operations/'.$table.'_results_view', $data);
				// }
				break;
			case 'company':
			case 'insurance':
			case 'compins':
			case 'hospclinic':
			case 'dentistsdoctors':
			case 'emerroom':
			case 'hospaccnt':
				if( ! $result) {
					return false;
				}
				$this->load->view('records/'.$table.'/'.$table.'_results_view', $data);
				break;
			case 'brokers':
				if( ! $result)
				{
					return false;
				}
				$this->load->view('records/accounts/accounts_'.$table.'_results_view', $data);
				break;
			case 'operations_er_register':
				if( ! $result) {
					return false;
				}
				$this->load->view('operations/operations_er_register_result_view', $data); //loads VIEW with fetched data from DB
				break;
		}
	}
}
?>