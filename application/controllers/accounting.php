<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class Accounting extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('accounting_model','',TRUE);
		$this->load->helper('url');
		$this->load->library('dateoperations');
		$this->load->library(array('table','form_validation','code','pagination','dateoperations'));

		if($this->session->userdata('logged_in'))
		{
			//set header links depending on logged in users in userdata session
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');

			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'accounting':
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
			$data = $session_data;
			
			switch($data['usertype'])
			{
				case 'sysad':
				case 'accounting':
					$loadedViews = array(
						'accounting/accounting_search_view' => NULL
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
		$this->form_validation->set_rules('table','Account Type','trim|required|xss_clean');
		$this->form_validation->set_rules('keyword','Keyword','trim|required|xss_clean');
		$this->form_validation->set_rules('limit','Limit','trim|required|xss_clean');
		$this->form_validation->set_rules('date_start','Starting Date','trim|required|xss_clean');
		$this->form_validation->set_rules('date_end','Ending Date','trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('accounting','refresh');
		}
		else
		{
			// echo '<pre>';
			// var_dump($_POST);
			$table = $_POST['table'];
			$keyword = $_POST['keyword'];
			$limit = $_POST['limit'];
			$start = $_POST['date_start'];
			$end = $_POST['date_end'];

			$accounts = $this->accounting_model->searchByLike($table,$keyword,$limit);

			foreach($accounts as $key => $value)
			{
				//VALIDATES IF ACCOUNT IS hospital or dentistsanddoctors
				if($table == 'hospital')
				{
					$loa[$key] = $this->accounting_model->getRecordsByFieldAndDate('availments_test','hospital_name',$value['name'],$start,$end);
				}
				elseif($table == 'dentistsanddoctors')
				{
					$name[$key] = $value['type'].'. '.$value['firstname'].' '.$value['middlename'].' '.$value['lastname'];
					$loa[$key] = $this->accounting_model->getRecordsByFieldAndDate('availments_test','physician',$name[$key],$start,$end);
				}

				// GET PATIENTS DETAILS
				foreach($loa[$key] as $lokey => $loval)
				{
					$benefits[$lokey]['benefits_in-out_patient'] = $this->accounting_model->getRecordsByField('benefits_in-out_patient','availment_id',$loval['id']);
					$benefits[$lokey]['benefits_others'] = $this->accounting_model->getRecordsByField('benefits_others','availment_id',$loval['id']);
					$benefits[$lokey]['benefits_others_as_charged'] = $this->accounting_model->getRecordsByField('benefits_others_as_charged','availment_id',$loval['id']);
		
					// FOR AMOUNT DETAILS
					foreach($benefits as $bkey => $bvalue)
					{
						if($bvalue == FALSE)
						{
							unset($bvalue);
						}
						else
						{
							foreach($bvalue as $akey => $avalue)
							{
								foreach($avalue as $bakey => $bavalue)
								{
									if($bavalue['availed_amount'] != 0.00)
									{
										$loa[$key][$lokey]['amount'] = $bavalue['availed_amount'];
									}
									if($bavalue['availed_as-charged'] != 0.00)
									{
										$loa[$key][$lokey]['amount'] = $bavalue['availed_as-charged'];
									}
								}
							}
						}
					}
				}
			}
			// echo '<pre>';
			// var_dump($accounts);
			// var_dump($loa);

			$data['accounts'] = $accounts;
			$data['patients'] = $loa;

			// DIFFERENT VIEW FOR hospital and dentistsanddoctors
			if($table == 'hospital')
			{
				$loadedViews = array(
								'accounting/accounting_search_view' => NULL,
								'accounting/accounting_hospital_result_view' => $data
								);
				$this->load->template($loadedViews, $this->header_links);
			}
			if($table == 'dentistsanddoctors')
			{
				$loadedViews = array(
								'accounting/accounting_search_view' => NULL,
								'accounting/accounting_dentistsanddoctors_result_view' => $data
								);
				$this->load->template($loadedViews, $this->header_links);
			}
		}
	}

	function printVoucherByDoctor()
	{
		echo '<pre>';
		$this->form_validation->set_rules('sel_multi[]','Select patient/s','trim|xss_clean|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('accounting','refresh');
		}
		else
		{
			var_dump($_POST['sel_multi']);

			foreach($_POST['sel_multi'] as $code)
			{
				$availments = $this->accounting_model->getRecordsByField('availments_test','code',$code);
			}

			foreach($availments as $key => $value)
			{
				$availments['doctors'][] = $value['physician'];
			}

			$loadedViews = array(
							'accounting/accounting_dentistsanddoctors_print_preview_view' => $availments
							);
			$this->load->template($loadedViews);
		}
	}

	function printVoucherByHospital()
	{
		echo '<pre>';
		$this->form_validation->set_rules('sel_multi[]','Select patient/s','trim|xss_clean|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('accounting','refresh');
		}
		else
		{
			foreach($_POST['sel_multi'] as $key => $code)
			{
				$availments[$key] = $this->accounting_model->getRecordsByField('availments_test','code',$code);

				foreach($availments[$key] as $lokey => $loval)
				{
					$benefits[$lokey]['benefits_in-out_patient'] = $this->accounting_model->getRecordsByField('benefits_in-out_patient','availment_id',$loval['id']);
					$benefits[$lokey]['benefits_others'] = $this->accounting_model->getRecordsByField('benefits_others','availment_id',$loval['id']);
					$benefits[$lokey]['benefits_others_as_charged'] = $this->accounting_model->getRecordsByField('benefits_others_as_charged','availment_id',$loval['id']);

					foreach($benefits as $bkey => $bvalue)
					{
						if($bvalue == FALSE)
						{
							unset($bvalue);
						}
						else
						{
							foreach($bvalue as $akey => $avalue)
							{
								foreach($avalue as $bakey => $bavalue)
								{
									if($bavalue['availed_amount'] != 0.00)
									{
										$availments[$key][$lokey]['amount'] = $bavalue['availed_amount'];
									}
									if($bavalue['availed_as-charged'] != 0.00)
									{
										$availments[$key][$lokey]['amount'] = $bavalue['availed_as-charged'];
									}
								}
							}
						}
					}
				}
			}
			var_dump($availments);
			$loadedViews = array(
							'accounting/accounting_hospital_print_preview_view' => $availments
							);
			$this->load->template($loadedViews);
		}
	}
}
?>