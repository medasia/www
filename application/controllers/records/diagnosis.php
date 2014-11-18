<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
session_start();

class Diagnosis extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('table','form_validation','code'));
		$this->load->model('records_model','',TRUE);
		$this->load->helper(array('html'));
		$this->load->library('pagination');

		if($this->session->userdata('logged_in'))
		{
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'ops':
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

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'ops':
						// $data['diagnosis'] = $this->records_model->getAllRecords('diagnosis');

						//PAGINATION
						$config['base_url'] = site_url().'records/diagnosis';
						$config['total_rows'] = $this->records_model->countRecord('diagnosis');
						$config['per_page'] = 100;
						$config['uri_segment'] = 3;
						$choice = $config['total_rows'] / $config['per_page'];
						$config['num_links'] = 10;
						$config['prev_link'] = '<< Previous';
						$config['next_link'] = 'Next >>';
						$this->pagination->initialize($config);
						$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
						$data['links'] = $this->pagination->create_links();

						$data['diagnosis'] = $this->records_model->getAllRecordsPage('diagnosis',$config['per_page'],$page);

						$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/diagnosis/diagnosis_register_view' => NULL,
							'records/diagnosis/diagnosis_search_view' => NULL,
							'records/diagnosis/diagnosis_result_view' => $data
							);
						$this->load->template($loadedViews, $this->header_links);
				break;

				default:
					redirect('','refresh');
			}
		}	
	}

	function register()
	{
		$this->form_validation->set_rules('diagnosis','Diagnosis','trim|xss_clean|is_unique[diagnosis.diagnosis]');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('records/diagnosis','refresh');
		}
		else
		{
			$data['diagnosis'] = $_POST['diagnosis'];

			$register = $this->records_model->register('diagnosis',$data);

			if($register)
			{
				$this->session->set_flashdata('result','<b> Successfully Registered Chief Complaint/Diagnosis');
				redirect('records/diagnosis','refresh');
			}
			else
			{
				$this->session->set_flashdata('result','<b>Registration Failed, something went wrong.');
				redirect('records/diagnosis','refresh');
			}
		}
	}

	function search()
	{
		// var_dump($_POST);
		$this->form_validation->set_rules('keyword','Keyword','trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result', validation_errors());
			redirect('records/diagnosis','refresh');
		}
		else
		{
			$keyword = $_POST['keyword'];

			$session_diagnosis = array(
								'table' => 'diagnosis',
								'keyword' => $keyword,
								'field' => 'diagnosis',
								'limit' => '100'
								);
			$this->session->set_userdata('session_diagnosis',$session_diagnosis);

			$config['base_url'] = base_url().'records/diagnosis/searchDiagnosis';
			$config['total_rows'] = $this->records_model->countSearchResult('diagnosis',$keyword,'diagnosis','100');
			$config['per_page'] = 100;
			$config['uri_segment'] = 4;
			$config['prev_link'] = '<< Previous';
			$config['next_link'] = 'Next >>';
			$choice = $config['total_rows'] / $config['per_page'];
			$config['num_links'] = 10;
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['links'] = $this->pagination->create_links();

			$result = $this->records_model->getRecord('diagnosis',$keyword,'100');

			if($result)
			{
				$data['diagnosis'] = $result;

				$loadedViews = array(
						'records/records_header_view' => $this->header_links,
						'records/diagnosis/diagnosis_register_view' => NULL,
						'records/diagnosis/diagnosis_search_view' => NULL,
						'records/diagnosis/diagnosis_result_view' => $data
						);
				$this->load->template($loadedViews, $this->header_links);
			}
			else
			{
				$data['diagnosis'] = NULL;
				$this->session->set_flashdata('result','No result/s found');
				redirect('records/diagnosis','refresh');
			}
		}
	}

	function searchDiagnosis()
	{
		if($this->session->userdata('session_diagnosis'))
		{
			$session_diagnosis = $this->session->userdata('session_diagnosis');

			$table = $session_diagnosis['table'];
			$keyword = $session_diagnosis['keyword'];
			$field = $session_diagnosis['field'];
			$limit = $session_diagnosis['limit'];

			$config['base_url'] = base_url().'records/diagnosis/searchDiagnosis';
			$config['total_rows'] = $this->records_model->countSearchResult($table,$keyword,$field,$limit);
			$config['per_page'] = $limit;
			$config['uri_segment'] = 4;
			$choice = $config['total_rows'] / $config['per_page'];
			$config['num_links'] = 10;
			$config['prev_link'] = '<< Previous';
			$config['next_link'] = 'Next >>';
			$this->pagination->initialize($config);

			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['links'] = $this->pagination->create_links();

			$data['diagnosis'] = $this->records_model->getRecord($table,$keyword,$limit,$page);

			$loadedViews = array(
							'records/records_header_view' => $this->header_links,
							'records/diagnosis/diagnosis_register_view' => NULL,
							'records/diagnosis/diagnosis_search_view' => NULL,
							'records/diagnosis/diagnosis_result_view' => $data
							);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function edit($id)
	{
		$result = $this->records_model->getRecordById('diagnosis',$id);

		if($result)
		{
			foreach($result as $row)
			{
				$loadedViews = array(
								'records/diagnosis/diagnosis_view_view' => $row
								);
				$this->load->template($loadedViews,$this->header_links);
			}
		}
	}

	function delete($id)
	{
		$delete = $this->records_model->delete('diagnosis',$id);

		if($delete)
		{
			$this->session->set_flashdata('result','Delete Chief Complaint / Diagnosis');
			redirect('records/diagnosis','refresh');
		}
	}
}
?>