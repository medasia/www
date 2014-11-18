<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class AffiliatedServiceProvider extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('records_model','',TRUE);
		$this->load->helper('url');
		$this->load->helper('csv');
		$this->load->library('pagination');

		if($this->session->userdata('logged_in'))
		{
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'accre':
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
		else
		{
			redirect('../','refresh');
		}
	}

	function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$valid = $session_data;

			switch($valid['usertype'])
			{
				case 'sysad':
				case 'accre':
						//SESSION DESTROY
						$this->session->unset_userdata('sess_search_hospital');
						$this->session->unset_userdata('sess_search_doctors');
						//PAGINATION
						$config['base_url'] = site_url()."records/affiliatedserviceprovider";
						$config['total_rows'] = $this->records_model->countRecord('hospital');
						$config['per_page'] = 100;
						$config['uri_segment'] = 3;
						$choice = $config['total_rows'] / $config['per_page'];
						$config['num_links'] = 10;
						$config['prev_link'] = '<< Previous';
						$config['next_link'] = 'Next >>';
						$this->pagination->initialize($config);
						$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
						$data['links'] = $this->pagination->create_links();

						$data['hospclinic'] = $this->records_model->getAllRecordsPage('hospital',$config['per_page'], $page);

						$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/affiliatedprovider/asp_register_view' => NULL,
							'records/affiliatedprovider/asp_search_view' => NULL,
							// 'records/hospclinic/hospclinic_register_view' => NULL,
							// 'records/dentistsdoctors/dentistsdoctors_register_view' => NULL,
							'records/hospclinic/hospclinic_results_view' => $data
							);
						$this->load->template($loadedViews, $this->header_links);
				break;

				default:
					echo '<script>alert("You are not allowed to access this portion of the site!");</script>';
					redirect('','refresh');
			}
		}
	}

	function search()
	{
		$this->form_validation->set_rules('table', 'Table', 'trim|required|xss_clean');
		$this->form_validation->set_rules('keyword', 'Keyword', 'trim|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
		else
		{
			// var_dump($_POST);
			$table = $_POST['table'];
			$keyword = $_POST['keyword'];
			$limit = $_POST['limit'];

			$result = $this->records_model->getRecord($table,$keyword,$limit);

			if($table == 'hospital')
			{
				$data['hospclinic'] = $result;

				$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/affiliatedprovider/asp_register_view' => NULL,
							'records/affiliatedprovider/asp_search_view' => NULL,
							'records/hospclinic/hospclinic_results_view' => $data
							);
				
				$this->load->template($loadedViews, $this->header_links);
			}
			elseif($table == 'dentistsanddoctors')
			{
				foreach($result as $key => $value)
				{
					$clinics = $this->records_model->getRecordByField('clinics', 'dentistsanddoctors_id', $value['id']);

					$clinic_info = array(
										'clinic_name', 'hospital_name',
										'street_address', 'subdivision_village',
										'barangay', 'city', 'province',
										'region', 'clinic_sched'
										);
					if($clinics)
					{
						foreach($clinics as $ckey => $cval)
						{
							foreach($clinic_info as $field)
							{
								$result[$key]['clinics'][$ckey][$field] = $cval[$field];
							}
						}
					}
					else
					{
						$result[$key]['clinics'] = NULL;
					}
					$data['dentistsdoctors'] = $result;
				}

				$loadedViews = array(
								'records/records_header_view' => $this->header_links,
								'records/affiliatedprovider/asp_register_view' => NULL,
								'records/affiliatedprovider/asp_search_view' => NULL,
								'records/dentistsdoctors/dentistsdoctors_results_view' => $data
								);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}

	function searchHospital()
	{
		// var_dump($this->session->all_userdata());
		$this->session->unset_userdata('sess_search_hospital');
		$this->form_validation->set_rules('table', 'Table', 'trim|required|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|required|xss_clean');
		$this->form_validation->set_rules('keyword', 'Keyword', 'trim|xss_clean');
		$this->form_validation->set_rules('branch', 'Branch', 'trim|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'trim|xss_clean');
		$this->form_validation->set_rules('province', 'Province', 'trim|xss_clean');
		$this->form_validation->set_rules('region', 'Region', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider','refresh');
		}
		else
		{
			// var_dump($_POST);
			$table = $this->input->post('table');
			$keyword = $this->input->post('keyword');
			$limit = $this->input->post('limit');
			$branch = $this->input->post('branch');
			$address = $this->input->post('address');
			$province = $this->input->post('province');
			$region = $this->input->post('region');

			// SESSION SEARCH
			$sess_search_hospital = array(
						'table' => $table,
						'keyword' => $keyword,
						'limit' => $limit,
						'branch' => $branch,
						'address' => $address,
						'province' => $province,
						'region' => $region
						);
			$this->session->set_userdata('sess_search_hospital', $sess_search_hospital);

			//PAGINATION
			if($limit < '500000')
			{
				$config['base_url'] = base_url()."records/affiliatedserviceprovider/searchHospitalsPage";
				$config['total_rows'] = $this->records_model->countHospitalResults($table,$keyword,$branch,$address,$province,$region,$limit);
				$config['per_page'] = $limit;
				$config['uri_segment'] = 4;
				$choice = $config['total_rows'] / $config['per_page'];
				$config['num_links'] = 10;
				$config['prev_link'] = '<< Previous';
				$config['next_link'] = 'Next >>';
				$this->pagination->initialize($config);

				$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
				$data['links'] = $this->pagination->create_links();
			}
			else
			{
				$config = NULL;
				$page = 0;
			}

			$result = $this->records_model->getHospitalByField($table,$keyword,$branch,$address,$province,$region,$limit,$page);
			$data['hospclinic'] = $result;

			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/affiliatedprovider/asp_register_view' => NULL,
							'records/affiliatedprovider/asp_search_view' => NULL,
							'records/hospclinic/hospclinic_results_view' => $data
							);
				
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function searchHospitalsPage()
	{
		if($this->session->userdata('sess_search_hospital'))
		{
			$sess_hospital = $this->session->userdata('sess_search_hospital');

			$table = $sess_hospital['table'];
			$keyword = $sess_hospital['keyword'];
			$limit = $sess_hospital['limit'];
			$branch = $sess_hospital['branch'];
			$address = $sess_hospital['address'];
			$province = $sess_hospital['province'];
			$region = $sess_hospital['region'];

			$config['base_url'] = base_url()."records/affiliatedserviceprovider/searchHospitalsPage";
			$config['total_rows'] = $this->records_model->countHospitalResults($table,$keyword,$branch,$address,$province,$region,$limit);
			$config['per_page'] = $limit;
			$config['uri_segment'] = 4;
			$choice = $config['total_rows'] / $config['per_page'];
			$config['num_links'] = 10;
			$config['prev_link'] = '<< Previous';
			$config['next_link'] = 'Next >>';
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) :0;
			$data['links'] = $this->pagination->create_links();


			$result = $this->records_model->getHospitalByField($table,$keyword,$branch,$address,$province,$region,$limit,$page);
			$data['hospclinic'] = $result;

			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/affiliatedprovider/asp_register_view' => NULL,
							'records/affiliatedprovider/asp_search_view' => NULL,
							'records/hospclinic/hospclinic_results_view' => $data
							);	
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function searchDentists()
	{
		$this->session->unset_userdata('sess_search_doctors');
		$this->form_validation->set_rules('table', 'Table', 'trim|required|xss_clean');
		$this->form_validation->set_rules('limit', 'Limit', 'trim|xss_clean');
		// $this->form_validation->set_rules('keyword', 'Keyword', 'trim|xss_clean');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|xss_clean');
		$this->form_validation->set_rules('middlename', 'Middle Name', 'trim|xss_clean');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|xss_clean');
		$this->form_validation->set_rules('specialization', 'Specialization', 'trim|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'trim|xss_clean');
		// $this->form_validation->set_rules('city', 'City', 'trim|xss_clean');
		// $this->form_validation->set_rules('province', 'Province', 'trim|xss_clean');
		// $this->form_validation->set_rules('region', 'Region', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/affiliatedserviceprovider', 'refresh');
		}
		else
		{
			// var_dump($_POST);
			// DENTIST AND DOCTORS by name***
			$table = $this->input->post('table');
			$keyword = $this->input->post('keyword');
			$firstname = $this->input->post('firstname');
			$middlename = $this->input->post('middlename');
			$lastname = $this->input->post('lastname');
			$limit = $this->input->post('limit');
			$specialization = $this->input->post('specialization');

			$address = $this->input->post('address');
			// $city = $_POST['city'];
			// $province = $_POST['province'];
			// $region = $_POST['region'];
			
			$sess_search_doctors = array(
						'table' => $table,
						'keyword' => $keyword,
						'firstname' => $firstname,
						'middlename' => $middlename,
						'lastname' => $lastname,
						'limit' => $limit,
						'specialization' => $specialization,
						'address' => $address,
						);
			$this->session->set_userdata('sess_search_doctors', $sess_search_doctors);

			if($limit < '500000')
			{
				$config['base_url'] = base_url()."records/affiliatedserviceprovider/searchDoctorsPage";
				$config['total_rows'] = $this->records_model->countDoctorsResults($table,$firstname,$middlename,$lastname,$specialization,$address,$limit);
				$config['per_page'] = $limit;
				$config['uri_segment'] = 4;
				$config['prev_link'] = '<< Previous';
				$config['next_link'] = 'Next >>';
				$choice = $config['total_rows'] / $config['per_page'];
				$config['num_links'] = 10;
				$this->pagination->initialize($config);

				$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
				$data['links'] = $this->pagination->create_links();
			}
			else
			{
				$config = NULL;
				$page = 0;
			}

			$result = $this->records_model->getDoctorsByField($table,$firstname,$middlename,$lastname,$specialization, $address, $limit,$page);

			// foreach($result as $key => $value)
			// {
			// 	$clinics = $this->records_model->getRecordByField('clinics', 'dentistsanddoctors_id', $value['id']);

			// 	$clinic_info = array(
			// 						'clinic_name', 'hospital_name',
			// 						'street_address', 'subdivision_village',
			// 						'barangay', 'city', 'province',
			// 						'region', 'clinic_sched'
			// 						);
			// 	if($clinics)
			// 	{
			// 		foreach($clinics as $ckey => $cval)
			// 		{
			// 			foreach($clinic_info as $field)
			// 			{
			// 				$result[$key]['clinics'][$ckey][$field] = $cval[$field];
			// 			}
			// 		}
			// 	}
			// 	else
			// 	{
			// 		$result[$key]['clinics'] = NULL;
			// 	}

			// 	if(strlen($address))
			// 	{
			// 		@$valstreet_address = strpos($clinics[$key]['street_address'], $address);
			// 		@$valsubdivision_village = strpos($clinics[$key]['subdivision_village'], $address);
			// 		@$valbarangay = strpos($clinics[$key]['barangay'], $address);
			// 	}

			// 	if(strlen($city))
			// 	{
			// 		@$valcity = strpos($clinics[$key]['city'], $city);
			// 	}

			// 	if(strlen($province))
			// 	{
			// 		@$valprovince = strpos($clinics[$key]['province'], $province);
			// 	}

			// 	if(strlen($region))
			// 	{
			// 		@$valregion = strpos($clinics[$key]['region'], $region);
			// 	}
				
			// 	if(@$valstreet_address===FALSE OR @$valsubdivision_village===FALSE OR @$valbarangay===FALSE OR @$valcity===FALSE OR @$valprovince===FALSE OR @$valregion===FALSE)
			// 	{
			// 		unset($result[$key]);
			// 	}

				$data['dentistsdoctors'] = $result;
			// }

			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/affiliatedprovider/asp_register_view' => NULL,
							'records/affiliatedprovider/asp_search_view' => NULL,
							'records/dentistsdoctors/dentistsdoctors_results_view' => $data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function searchDoctorsPage()
	{
		if($this->session->userdata('sess_search_doctors'))
		{
			$sess_doctors = $this->session->userdata('sess_search_doctors');

			$table = $sess_doctors['table'];
			$keyword = $sess_doctors['keyword'];
			$firstname = $sess_doctors['firstname'];
			$middlename = $sess_doctors['middlename'];
			$lastname = $sess_doctors['lastname'];
			$limit = $sess_doctors['limit'];
			$address = $sess_doctors['address'];
			$specialization = $sess_doctors['specialization'];

			$config['base_url'] = base_url()."records/affiliatedserviceprovider/searchDoctorsPage";
			$config['total_rows'] = $this->records_model->countDoctorsResults($table,$firstname,$middlename,$lastname,$specialization,$address,$limit);
			$config['per_page'] = $limit;
			$config['uri_segment'] = 4;
			$config['prev_link'] = '<< Previous';
			$config['next_link'] = 'Next >>';
			$choice = $config['total_rows'] / $config['per_page'];
			$config['num_links'] = 10;
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['links'] = $this->pagination->create_links();

			$result = $this->records_model->getDoctorsByField($table,$firstname,$middlename,$lastname,$specialization, $address, $limit,$page);

			$data['dentistsdoctors'] = $result;

			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/affiliatedprovider/asp_register_view' => NULL,
							'records/affiliatedprovider/asp_search_view' => NULL,
							'records/dentistsdoctors/dentistsdoctors_results_view' => @$data
							);
			$this->load->template($loadedViews, $this->header_links);
		}
	}

	function download($table)
	{
		$download = $this->records_model->download($table);
		query_to_csv($download, TRUE, $table.'.csv');
	}
}
?>