<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Operations extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('operations_model','',TRUE);
		$this->load->library(array('code'));
		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');
			$this->session_data = $this->session->userdata('logged_in');

			switch($this->session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
				case 'ops':
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		} else {
			//If no session, redirect to login page
			redirect('../', 'refresh');
		}
	}
	function index() {

		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$data = $session_data;
			
			if($data['usertype'] == 'sysad')
			{
				$loadedViews = array(
							'operations/operations_view' => NULL
							);
				$this->load->template($loadedViews, $this->header_links);
			}
			elseif($data['usertype'] == 'admin_assoc' || $data['usertype'] == 'ops')
			{
				$loadedViews = array(
							'operations/operations_adminassoc_view' => NULL
							);
				$this->load->template($loadedViews, $this->header_links);
			}
			else
			{
				echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
				redirect('','refresh');
			}
		}
	}

	function regcard($id)
	{
		$data = $this->operations_model->getRecordById('emergency_room',$id);
		foreach($data as $key => $value)
		{
			$loadedViews = array(
							// 'operations/operations_er_card_register_old_view' => NULL,
							'operations/operations_er_card_register_view' => $value
							);
			$this->load->template($loadedViews, $this->header_links);
		}		
	}

	function register()
	{
		unset($_POST['submit'],$_POST['patient']);
		$this->form_validation->set_rules('birth_date','Date of Birth','trim|required|xss_clean');
		$this->form_validation->set_rules('beneficiary_firstname','Beneficiary Firstname','trim|required|xss_clean');
		$this->form_validation->set_rules('beneficiary_lastname','Beneficiary Lastname','trim|required|xss_clean');
		$this->form_validation->set_rules('beneficiary_middlename','Beneficiary Middlename','trim|xss_clean');
		$this->form_validation->set_rules('occupation','Occupation','trim|xss_clean');
		$this->form_validation->set_rules('relationship','Relationship','trim|required|xss_clean');
		$this->form_validation->set_rules('landline_number','Landline Number','trim|xss_clean');
		$this->form_validation->set_rules('mobile_number','Mobile Number','trim|xss_clean');
		$this->form_validation->set_rules('address','Address','trim|xss_clean');
		$this->form_validation->set_rules('remarks','remarks','trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('operations','refresh');
		}
		else
		{
			$key = array('id'=>$_POST['id']);
			$data = $_POST;
			unset($data['id']);
			$current_date = date('Y-m-d');
			$data['registration_datetime'] = date('Y-m-d H:i:s');
			$activation_date = strtotime('+7 day',strtotime($current_date));
			$data['dateofactivation'] = date('Y-m-d',$activation_date);
			$validity_date = strtotime('+1 year',strtotime($current_date));
			$data['datevalid'] = date('Y-m-d',$validity_date);
			$data['status'] = 'INACTIVE';
			// echo '<pre>';
			// var_dump($data,$key);

			$update = $this->operations_model->update('emergency_room',$data,$key);

			if($update)
			{
				$this->session->set_flashdata('result','Patient successfully registered ER Card.');
				redirect('operations','refresh');
			}
			else
			{
				$this->session->set_flashdata('result','Something went wrong');
				redirect('operations','refresh');
			}
		}
	}

	function memberssearch()
	{
		// echo '<pre>';
		// var_dump($_POST);
		$this->form_validation->set_rules('firstname','First Name','trim|xss_clean');
		$this->form_validation->set_rules('middlename','Middle Name','trim|xss_clean');
		$this->form_validation->set_rules('lastname','Last Name','trim|xss_clean');
		$this->form_validation->set_rules('company','Company Name','trim|xss_clean');
		$this->form_validation->set_rules('insurance','Insurance Name','trim|xss_clean');
		$this->form_validation->set_rules('cardholder_type','Cardholder Type','trim|xss_clean');
		$this->form_validation->set_rules('status','Status','trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('operations','refresh');
		}
		else
		{
			$data['firstname'] = $_POST['firstname'];
			$data['middlename'] = $_POST['middlename'];
			$data['lastname'] = $_POST['lastname'];
			$data['cardholder_type'] = $_POST['cardholder_type'];
			$data['status'] = $_POST['status'];

			$company = $_POST['company'];
			$insurance = $_POST['insurance'];

			$result = $this->operations_model->searchByMultiField('patient',$data);

			// var_dump($result);

			foreach($result as $key => $value)
			{
				$patientCompIns = $this->operations_model->getRecordByField('patient_company_insurance','patient_id',$value['id']);

				foreach($patientCompIns as $pci)
				{
					$compIns = $this->operations_model->getRecordById('company_insurance',$pci['company_insurance_id']);
					$result[$key]['compins'] = $compIns;

					$benefit_name = $this->operations_model->getRecordsByTwoFields('benefits.benefitset_info','compins_id','level',$pci['company_insurance_id'],$value['level']);
					$result[$key]['benefit_name'] = $benefit_name;
				}
			}

			// var_dump($result);
			// var_dump($company,$insurance);
			if(strlen($company) || strlen($insurance))
			{
				foreach($result as $key => $value)
				{
					if(strlen($company))
					{
						$company_true = stripos($value['compins'][0]['company'],$company);
					}
					
					if(strlen($insurance))
					{
						$insurance_true = stripos($value['compins'][0]['insurance'],$insurance);
					}

					// var_dump($company_true, $insurance_true);
					if($company_true === FALSE OR $insurance_true === FALSE)
					{
						unset($result[$key]);
					}
				}
			}
			// var_dump($result);
			$search_result['patients'] = $result;

			$session_data = $this->session->userdata('logged_in');
			$sess_data = $session_data;
			
			if($sess_data['usertype'] == 'sysad')
			{
				$loadedViews = array(
							'operations/operations_view' => NULL,
							'operations/operations_patient_results_view' => $search_result
							);
				$this->load->template($loadedViews, $this->header_links);
			}
			else
			{
				if($sess_data['usertype'] == 'admin_assoc')
				{
					$view = 'operations/operations_patient_results_assoc_view';
				}
				elseif($sess_data['usertype'] == 'ops')
				{
					$view = 'operations/operations_patient_results_ops_view';
				}

				$loadedViews = array(
							'operations/operations_adminassoc_view' => NULL,
							$view => $search_result
							);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}

	function searchER()
	{
		$this->form_validation->set_rules('firstname','Firstname','xss_clean|trim');
		$this->form_validation->set_rules('middlename','Middlename','xss_clean|trim');
		$this->form_validation->set_rules('lastname','Lastname','xss_clean|trim');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('operations','refresh');
		}
		else
		{
			$data['firstname'] = $_POST['firstname'];
			$data['middlename'] = $_POST['middlename'];
			$data['lastname'] = $_POST['lastname'];

			$patients = $this->operations_model->searchByMultiField('patient',$data);

			if($patients)
			{
				foreach($patients as $key => $value)
				{
					$er[$key] = $this->operations_model->getRecordByField('emergency_room','patient_id',$value['id']);

					foreach($er[$key] as $er_key => $er_value)
					{
						$er[$key][$er_key]['patient_name'] = $value['lastname'].', '.$value['firstname'].' '.$value['middlename'];
					}

					if($er[$key] == FALSE)
					{
						unset($er[$key]);
					}
				}
			}

			// RETURNS RESULT TO SINGLE ARRAY
			foreach($er as $key => $value)
			{
				foreach($value as $er_key => $er_value)
				{
					$result['operations_er_card'][] = $er_value;
				}
			}
			
			if($result)
			{
				switch($this->session_data['usertype'])
				{
					case 'admin_assoc':
						$loadedViews = array(
								'operations/operations_view' => NULL,
								'operations/operations_er_card_results_assoc_view' => $result
								);
						$this->load->template($loadedViews,$this->header_links);
					break;
					case 'ops':
						$loadedViews = array(
								'operations/operations_view' => NULL,
								'operations/operations_er_card_results_ops_view' => $result
								);
						$this->load->template($loadedViews,$this->header_links);
					break;
					default:
						$loadedViews = array(
								'operations/operations_view' => NULL,
								'operations/operations_er_card_results_view' => $result
								);
						$this->load->template($loadedViews, $this->header_links);
					break;
				}
			}
			else
			{
				$this->session->set_flashdata('result','No Records Found!!!');
				redirect('operations','refresh');
			}
		}
	}

	function avail($id)
	{
		$er = $this->operations_model->getRecordById('emergency_room',$id);
		$patient = $this->operations_model->getRecordById('patient',$er[0]['patient_id']);
		$er[0]['patient_name'] = $patient[0]['lastname'].', '.$patient[0]['firstname'].' '.$patient[0]['middlename'];

		foreach($er as $row)
		{
			$loadedViews = array(
						'operations/operations_er_card_verify_patient' => $row
						);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function availments()
	{
		$er = $this->operations_model->getRecordById('emergency_room',$_POST['id']);
		$patient = $this->operations_model->getRecordById('patient',$_POST['patient_id']);
		$er[0]['patient_name'] = $patient[0]['lastname'].', '.$patient[0]['firstname'].' '.$patient[0]['middlename'];

		foreach($er as $row)
		{
			$loadedViews = array(
						'operations/operations_er_card_availments' => $row
						);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function registerAvailments()
	{
		unset($_POST['submit']);
		$this->form_validation->set_rules('hospital_name','Hospital Name','trim|required|xss_clean');
		$this->form_validation->set_rules('hospital_branch','Hospital Branch','trim|required|xss_clean');
		$this->form_validation->set_rules('diagnosis','Diagnosis','trim|required|xss_clean');
		$this->form_validation->set_rules('physician','Physician','trim|xss_clean');
		$this->form_validation->set_rules('procedure','Procedure','trim|required|xss_clean');
		$this->form_validation->set_rules('availed_amount','Total Amount','trim|required|xss_clean');
		$this->form_validation->set_rules('remarks','Remars','trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('operations','refresh');
		}
		else
		{
			echo '<pre>';
			var_dump($_POST);

			$er = $this->operations_model->getRecordById('emergency_room',$_POST['id']);
			$patient = $this->operations_model->getRecordById('patient',$_POST['patient_id']);

			// COMPANY - INSURANCE DETAILS
			$pci = $this->operations_model->getRecordByField('patient_company_insurance','patient_id',$_POST['patient_id']);
			$company_insurance = $this->operations_model->getRecordById('company_insurance',$pci[0]['company_insurance_id']);
			$company = $this->operations_model->getRecordByField('company','name',$company_insurance[0]['company']);
			$insurance = $this->operations_model->getRecordByField('insurance','name',$company_insurance[0]['insurance']);

			var_dump($er,$patient,$pci,$company_insurance,$company,$insurance);

			$data = $_POST;
			unset($data['availed_amount'],$data['id'],
				$data['patient_id'],$data['procedure']);

			// FOR availments (VERIFICATIONS)
			$data['date'] = date('Y-m-d H:i:s');
			$data['availment_type'] = 'Out-Patient';
			$data['patient_id'] = $_POST['patient_id'];
			$data['patient_name'] = $patient[0]['lastname'].', '.$patient[0]['firstname'].' '.$patient[0]['middlename'];
			$data['company_name'] = $company_insurance[0]['company'];
			$data['insurance_name'] = $company_insurance[0]['insurance'];
			$data['company_code'] = $company[0]['code'];
			$data['insurance_code'] = $insurance[0]['Code'];
			$data['code'] = $this->code->setCode();

			// for USED ER
			$er_used['card_id'] = $_POST['id'];
			$er_used['patient_id'] = $_POST['patient_id'];
			$er_used['code'] = $data['code'];
			$er_used['benefit_name'] = $_POST['procedure'];
			$er_used['availed_amount'] = $_POST['availed_amount'];
			$er_used['date'] = $data['date'];

			var_dump($data, $er_used);
		}
	}

	function memberView($id)
	{
		//PATIENT, COMPANY - INSURANCE DETAILS
		$result = $this->operations_model->getRecordById('patient',$id);
		$patientCompIns = $this->operations_model->getRecordByField('patient_company_insurance','patient_id',$id);
		$compins = $this->operations_model->getRecordById('company_insurance',$patientCompIns[0]['patient_id']);
		if($compins)
		{
			$result[0]['compins'] = $compins[0];
		}

		//BENEFIT DETAILS
		$benefit_info = $this->operations_model->getRecordByMultiField('benefits.benefitset_info',
																array('compins_id'=>$patientCompIns[0]['company_insurance_id'],
																	'level'=>$result[0]['level'],
																	'cardholder_type'=>$result[0]['cardholder_type']));

		if($benefit_info == FALSE)
		{
			$benefit_info = $this->operations_model->getRecordByFieldsAndLike('benefits.benefitset_info',
																array('compins_id'=>$patientCompIns[0]['company_insurance_id'],
																	'level'=>$result[0]['level'],
																	'cardholder_type'=>$result[0]['cardholder_type']));
		}


		foreach($benefit_info as $key => $value)
		{
			if($value['cardholder_type'] == ucfirst(strtolower($result[0]['cardholder_type'])) || $value['cardholder_type'] == 'Principal and Dependent')
			{
				$result[0]['benefit_info'] = $benefit_info[$key];
			}
			$condition = $this->operations_model->getRecordByField('benefits.benefit_set_condition','condition_name',$value['condition_name']);
			if($condition)
			{
				$result[0]['condition_details'] = $condition[0]['condition_details'];
			}
			$exclusion = $this->operations_model->getRecordByField('benefits.benefit_set_exclusion','exclusion_name',$value['exclusion_name']);
			if($exclusion)
			{
				$result[0]['exclusion_details'] = $exclusion[0]['exclusion_details'];
			}
		}

		//BENEFIT OVERALL MBL
		$overall_mbl = $this->operations_model->getRecordByMultiField('benefits_overall_mbl',array('patient_id'=>$id));
		if($overall_mbl)
		{
			$result[0]['remaining_overall_mbl'] = $overall_mbl[0]['remaining_overall_mbl'];
		}

		//PATIENT AVAILMENT
		$patientLOA = $this->operations_model->getRecordByField('availments_test','patient_id',$id);
		if($patientLOA)
		{
			foreach($patientLOA as $key => $value)
			{
				// var_dump($value);
				@$labs = $this->operations_model->getRecordByField('lab_test_test', 'availments_id', $value['id']);
				@$labInfo = array(
						'lab_test','amount'
						);
				if($labs)
				{
					foreach($labs as $lkey => $lval)
					{
						foreach($labInfo as $field)
						{
							$patientLOA[$key]['lab_test_test'][$lkey][$field] = $lval[$field];
						}
					}
				}
				else
				{
					@$patientLOA[$key]['lab_test_test'] = NULL;
				}
				$patientLOA[$key]['benefits_in-out_patient'] = $this->operations_model->getRecordByField('benefits_in-out_patient','availment_id',$value['id']);
				$patientLOA[$key]['benefits_others'] = $this->operations_model->getRecordByField('benefits_others','availment_id',$value['id']);
				$patientLOA[$key]['benefits_others_as_charged'] = $this->operations_model->getRecordByField('benefits_others_as_charged','availment_id',$value['id']);
			}
			$result[0]['availments'] = $patientLOA;
		}
		// PATIENT ADMISSION AND MONITORING
		$admission = $this->operations_model->getRecordByField('admission_report_test','patient_id',$id);
		if($admission)
		{
			foreach($admission as $key => $value)
			{
				$specialist = $this->operations_model->getRecordByMultiField('admission_specialist',
																		array(
																		'code' => $value['code'],
																		'time_generated' => $value['time_generated']));
				foreach($specialist as $splst)
				{
					if($splst)
					{
						$admission[$key]['specialist'][] = $splst;
					}
					else
					{
						$admission[$key]['specialist'] = '';
					}
				}
			}
			$result[0]['admission'] = $admission;
		}
		$monitoring = $this->operations_model->getRecordByField('monitoring','patient_id',$id);
		if($monitoring)
		{
			$result[0]['monitoring'] = $monitoring;
		}

		foreach($result as $row)
		{
			// echo '<pre>';
			// var_dump($row);
			$loadedViews = array(
							'operations/operations_patient_results_ops_view_view' => $row
							);
			$this->load->template($loadedViews);
		}
	}
}
?>