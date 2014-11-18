<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Compins extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('records_model','',TRUE);
		$this->load->library('pagination');
		$this->load->helper('url');
		
		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
				case 'claims':
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
		else
		{
			//If no session, redirect to login page
			redirect('../', 'refresh');
		}
	}

	function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'admin_assoc':
				case 'claims':
						$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							// 'records/compins/compins_register_view' => NULL,
							'records/compins/compins_view' => NULL
							);
						$this->load->template($loadedViews, $this->header_links);
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}	
	}

	function members($id)
	{
		//PAGINATION
		$this->session->unset_userdata('sess_compins_members');
		$limit = 100;
		$config['base_url'] = base_url()."records/compins/compinsMembersPage";
		$config['total_rows'] = $this->records_model->countRecordByField('patient_company_insurance','company_insurance_id',$id,$limit);
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		$choice = $config['total_rows'] / $config['per_page'];
		$config['num_links'] = round($choice);
		$config['prev_link'] = '<< Previous';
		$config['next_link'] = 'Next >>';
		$this->pagination->initialize($config);

		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data['links'] = $this->pagination->create_links();

		//SESSION FOR PAGINATION
		$sess_compins_members = array(
							'id' => $id,
							'limit' => $limit
								);
		$this->session->set_userdata('sess_compins_members',$sess_compins_members);

		$compinsname = $this->records_model->getRecordById('company_insurance', $id);
		$data['compins_id'] = $compinsname[0]['id'];
		$data['notes'] = $compinsname[0]['notes'];
		$data['name'] = $compinsname[0]['company']." - ".$compinsname[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['end'])).")";
	
		$result = $this->records_model->getRecordByFieldWithLimit('patient_company_insurance', 'company_insurance_id', $id,$limit,0,'patient_id');
		$resultCount = $this->records_model->countRecordByField('patient_company_insurance','company_insurance_id',$id,$limit);

		//var_dump($compinsname[0]);
		foreach($result as $key => $value)
		{
			@$members[$key] = $this->records_model->getRecordById('patient', $value['patient_id']);

			foreach($members as $pkey => $pvalue)
			{
				// $members[$key]['benefit_name'] = $this->records_model->getRecordsByThreeFields('benefits.benefitset_info','compins_id','level','cardholder_type',$compinsname[0]['id'],$pvalue[0]['level'],$pvalue[0]['cardholder_type']);
				// $benefit_details = $this->records_model->getRecordsByTwoFields('benefits.benefitset_info','compins_id','level',$compinsname[0]['id'],$pvalue[0]['level']);
				$benefit_details = $this->records_model->getRecordByMultiField('benefits.benefitset_info',
																			array(
																				'compins_id' => $compinsname[0]['id'],
																				'level' => $pvalue[0]['level']
																				));
				foreach($benefit_details as $bdkey => $bddetails)
				{
					if(($bddetails['cardholder_type'] == ucfirst(strtolower($pvalue[0]['cardholder_type']))) || $bddetails['cardholder_type'] == 'Principal and Dependent')
					{
						$members[$key]['benefit_name'] = $benefit_details;
					}
				}
			}	
		}
		$data['patients'] = @$members;

		/** 111414 **/
		$company_details = $this->records_model->getRecordById('company',$id);
		if($company_details)
		{
			foreach($company_details as $each_detail)
			{



		$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							// registration for selected company insurance

							'records/company/company_view_company_view' => $each_detail,

							'records/compins/compins_view_compins_view' => $data

							// 'records/accounts/display_compins_members' => $data
							
							// 'records/accounts/accounts_print_compins_members_view' => $data
							);

			}
		}
		$this->load->template($loadedViews, $this->header_links);
	}

	function compinsMembersPage()
	{
		if($this->session->userdata('sess_compins_members'))
		{
			$sess_members = $this->session->userdata('sess_compins_members');

			$id = $sess_members['id'];
			$limit = $sess_members['limit'];

			$config['base_url'] = base_url()."records/compins/compinsMembersPage";
			$config['total_rows'] = $this->records_model->countRecordByField('patient_company_insurance','company_insurance_id',$id,$limit);
			$config['per_page'] = $limit;
			$config['uri_segment'] = 4;
			$choice = $config['total_rows'] / $config['per_page'];
			$config['num_links'] = round($choice);
			$config['prev_link'] = '<< Previous';
			$config['next_link'] = 'Next >>';
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['links'] = $this->pagination->create_links();

			//DATA TO DISPLAY
			$compinsname = $this->records_model->getRecordById('company_insurance',$id);
			$data['compins_id'] = $compinsname[0]['id'];
			$data['name'] = $compinsname[0]['company']." - ".$compinsname[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['start']))." - ".mdate('%M %d, %Y',mysql_to_unix($compinsname[0]['end'])).")";

			$result = $this->records_model->getRecordByFieldWithLimit('patient_company_insurance', 'company_insurance_id', $id,$limit,$page,'patient_id');
		$resultCount = $this->records_model->countRecordByField('patient_company_insurance','company_insurance_id',$id,$limit);

		//var_dump($compinsname[0]);
		foreach($result as $key => $value)
		{
			@$members[$key] = $this->records_model->getRecordById('patient', $value['patient_id']);

			foreach($members as $pkey => $pvalue)
			{
				// $members[$key]['benefit_name'] = $this->records_model->getRecordsByThreeFields('benefits.benefitset_info','compins_id','level','cardholder_type',$compinsname[0]['id'],$pvalue[0]['level'],$pvalue[0]['cardholder_type']);
				$members[$key]['benefit_name'] = $this->records_model->getRecordByMultiField('benefits.benefitset_info',
																						array(
																							'compins_id' => $compinsname[0]['id'],
																							'level' => $pvalue[0]['level'],
																							'cardholder_type' => $pvalue[0]['cardholder_type']
																							));
			}

			// @$members[$key]['benefit_name'] = $this->records_model->getRecordByField('benefits.benefitset_info','compins_id',$compinsname[0]['id']);
		}
		$data['patients'] = @$members;
		$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							// registration for selected company insurance
							'records/compins/compins_view_compins_view' => $data
							);
		$this->load->template($loadedViews, $this->header_links);
		}
	}

	function register()
	{
		//Validates inputs from user, checks for security flaws
		$this->form_validation->set_rules('company', 'Company', 'trim|required|xss_clean');
		$this->form_validation->set_rules('insurance', 'Insurance', 'trim|required|xss_clean');
		$this->form_validation->set_rules('start', 'Start Date', 'trim|required|xss_clean|valid_date');
		$this->form_validation->set_rules('end', 'End Date', 'trim|required|xss_clean|valid_date');
		$this->form_validation->set_rules('notes','Notes/Remarks', 'trim|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{ //if validation had errors reroute to useraccounts with flashdata that contains the said errors
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/compins', 'refresh');
		}
		else
		{
			$data = $_POST;
			unset($data['submit']);
			$register = $this->records_model->register('company_insurance', $data);

			if($register)
			{
				$this->session->set_flashdata('result', '<b>Succesfully registered Company-Insurance.</b>');
				redirect('records/compins', 'refresh');
			}
			else
			{
				$this->session->set_flashdata('result', '<b>Error in registering data, database error or duplicate error may occured.</b>');
				redirect('records/compins', 'refresh');
			}
		}
	}

	function search()
	{
		$this->form_validation->set_rules('members', 'Members', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{ // $data = $_POST;
			// var_dump($data);
			$id = $_POST['compins_id'];
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/compins/members/'.$id, 'refresh');
		}
		else
		{
			unset($_POST['submit']);
			$id = $_POST['compins_id'];
			$keyword = $_POST['members'];

			$compinsname = $this->records_model->getRecordById('company_insurance', $id);
			$data['compins_id'] = $compinsname[0]['id'];
			$data['name'] = $compinsname[0]['company']." - ".$compinsname[0]['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($compinsname[0]['end'])).")";
			
			$result = $this->records_model->getRecordByField('patient_company_insurance', 'company_insurance_id', $id, 'patient_id');
		
			foreach($result as $key => $value)
			{
				@$members[$key] = $this->records_model->getRecordById('patient', $value['patient_id']);

				foreach($members as $pkey => $pvalue)
				{
					// $members[$key]['benefit_name'] = $this->records_model->getRecordsByThreeFields('benefits.benefitset_info','compins_id','level','cardholder_type',$compinsname[0]['id'],$pvalue[0]['level'],$pvalue[0]['cardholder_type']);
					$members[$key]['benefit_name'] = $this->records_model->getRecordByMultiField('benefits.benefitset_info',
																							array(
																								'compins_id' => $compinsname[0]['id'],
																								'level' => $pvalue[0]['level'],
																								'cardholder_type' => $pvalue[0]['cardholder_type']
																								));
				}
			}
			
			foreach($members as $key => $value)
			{
				$firstname = stripos($value[0]['firstname'], $keyword);
				$middlename = stripos($value[0]['middlename'], $keyword);
				$lastname = stripos($value[0]['lastname'], $keyword);

				//CONCATENATE NAMES
				$flname = stripos($value[0]['firstname']." ".$value[0]['lastname'], $keyword);
				$fmname = stripos($value[0]['firstname']." ".$value[0]['middlename'], $keyword);
				$lfname = stripos($value[0]['lastname']." ".$value[0]['firstname'], $keyword);
				$lmname = stripos($value[0]['lastname']." ".$value[0]['middlename'], $keyword);
				$mfname = stripos($value[0]['middlename']." ".$value[0]['firstname'], $keyword);
				$mlname = stripos($value[0]['middlename']." ".$value[0]['lastname'], $keyword);

				$fmlname = stripos($value[0]['firstname']." ".$value[0]['middlename']." ".$value[0]['lastname'], $keyword);
				$flmname = stripos($value[0]['firstname']." ".$value[0]['lastname']." ".$value[0]['middlename'], $keyword);
				$lmfname = stripos($value[0]['lastname']." ".$value[0]['middlename']." ".$value[0]['firstname'], $keyword);
				$lfmname = stripos($value[0]['lastname']." ".$value[0]['firstname']." ".$value[0]['middlename'], $keyword);
				$mflname = stripos($value[0]['middlename']." ".$value[0]['firstname']." ".$value[0]['lastname'], $keyword);
				$mlfname = stripos($value[0]['middlename']." ".$value[0]['lastname']." ".$value[0]['firstname'], $keyword);

				if($firstname !== FALSE OR $middlename !== FALSE OR $lastname !== FALSE
					OR $flname !== FALSE OR $fmname !== FALSE
					OR $lfname !== FALSE OR $lmname !== FALSE
					OR $mfname !== FALSE OR $mlname !== FALSE
					OR $fmlname !== FALSE OR $flmname !== FALSE
					OR $lmfname !== FALSE OR $lfmname !== FALSE
					OR $mflname !== FALSE OR $mlfname !== FALSE
				)
				{
					$data['patients'][] = $value;
				}
			}

			// $data['patients'] = $members;
			// var_dump($members);
			$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						// registration for selected company insurance
						'records/compins/compins_view_compins_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function multiSelect()
	{
		// var_dump($_POST);
		$this->form_validation->set_rules('selMulti[]', 'Multiple Select', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo "INPUT ERROR: All fields are required!!";
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/compins', 'refresh');
		}
		else
		{
			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/verify_password_view' => NULL
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function multiVerified()
	{
		$data =  $this->session->flashdata('data');
		// var_dump($data);
		if($data['submit'] == 'Delete')
		{
			$count = 0;
			foreach($data['selMulti'] as $id)
			{
				$delete = $this->records_model->delete('company_insurance', $id);

				$members = $this->records_model->getRecordByField('patient_company_insurance','company_insurance_id',$id);
				if($members)
				{
					foreach($members as $key => $value)
					{
						$deleteMembers = $this->records_model->delete('patient',$value['id']);
					}
				}
				
				$delete2 = $this->records_model->deleteByField('patient_company_insurance','company_insurance_id',$id);
				$count++;
			}
			
			if($delete)
			{ //if successfully deleted company-insurance, reroute to compins with flashdata
				$this->session->set_flashdata('result', 'Deleted '.$count.' records of Company - Insurance.');
				redirect('records/compins', 'refresh');
			}
		}
	}

	function compinsSearch()
	{
		$this->form_validation->set_rules('compinsTest', 'Compins Test', 'trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/compins', 'refresh');
		}
		else
		{
			unset($_POST['submit']);
			$keyword = $_POST['compinsTest'];
			$limit = $_POST['limit'];

			$result = $this->records_model->getRecord('company_insurance', $keyword, $limit);
			
			foreach($result as $key => $value)
			{
				$data['message'][] = array('label'=>$value['company']."-".$value['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($value['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($value['end'])).")", 'value'=>$value['company']."-".$value['insurance']." (".mdate('%M %d, %Y', mysql_to_unix($value['start']))." - ".mdate('%M %d, %Y', mysql_to_unix($value['end'])).")", 
							'Id'=>$value['id']);
				$memberCount = $this->records_model->getCountByField('patient_company_insurance', 'company_insurance_id', $value['id']);
				$result[$key]['membercount'] = $memberCount;
			}
			
			$data['compins'] = $result;

			$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/compins/compins_register_view' => NULL,
						'records/compins/compins_view' => NULL,
						'records/compins/compins_results_view' => $data
						);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function delete($id)
	{
		$delete = $this->records_model->delete('company_insurance', $id);
		if($delete)
		{ //if successfully deleted company-insurance, reroute to compins with flashdata
			$this->session->set_flashdata('result', 'Deleted company-insurance.');
			redirect('records/compins', 'refresh');
		}
	}

	function edit($id)
	{
		$compins = $this->records_model->getRecordById('company_insurance',$id);

		if($compins)
		{
			foreach($compins as $row)
			{
				$loadedViews = array(
								'records/compins/compins_view_compins_edit_view' => $row
								);
				$this->load->template($loadedViews);
			}
		}
		else
		{
			$this->session->set_flashdata('result','<b>Record not found!</b>');
			redirect('records/compins','refresh');
		}
	}

	function deleteMember($id,$compins_id)
	{
		$delete = $this->records_model->delete('patient', $id);
		if($delete)
		{
			$delete2 = $this->records_model->delete('patient_company_insurance', $id, 'patient_id');
			if($delete)
			{ //if successfully deleted patient, reroute to Members with flashdata
				$this->session->set_flashdata('result', 'Deleted patient.');
				redirect('records/compins/members/'.$compins_id, 'refresh');
			}
		}
	}
}
?>