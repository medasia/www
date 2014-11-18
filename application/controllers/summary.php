<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
session_start();

class Summary extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('summary_model','',TRUE);
		$this->load->library(array('table','form_validation','code'));
		$this->load->helper(array('benefit_helper','benefit_remarks_helper','html'));

		if($this->session->userdata('logged_in'))
		{
			$this->header_links = $this->session->userdata('logged_in');

			$session_data = $this->session->userdata('logged_in');
			switch($session_data['usertype'])
			{
				case 'sysad':
				case 'ops':
				case 'claims':
				case 'accre':
				case 'accounting':
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
			$data = $session_data;

			switch($data['usertype'])
			{
				case 'sysad':
				case 'ops':
				case 'claims':
				case 'accre':
				case 'accounting':
					$loadedViews = array(
									'summary/summary_search_view' => NULL
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
		$this->form_validation->set_rules('code','Approval Code','trim|xss_clean');
		$this->form_validation->set_rules('patient_name','Patient Name','trim|xss_clean');
		$this->form_validation->set_rules('company_name','Company Name','trim|xss_clean');
		$this->form_validation->set_rules('insurance_name','Insurance Name','trim|xss_clean');
		$this->form_validation->set_rules('hospital_name','Hospital Name','trim|xss_clean');
		$this->form_validation->set_rules('chief_complaint','Chief Complaint','trim|xss_clean');
		$this->form_validation->set_rules('availment_type','Availment Type','trim|xss_clean');
		$this->form_validation->set_rules('start','Date Start','trim|xss_clean');
		$this->form_validation->set_rules('claims_status','trim|xss_clean');
		$this->form_validation->set_rules('end','Date End','trim|xss_clean');
		$this->form_validation->set_rules('user','User','trim|xss_clean');
		$this->form_validation->set_rules('sort_by','Sort By','trim|xss_clean');
		$this->form_validation->set_rules('sort','Sort','trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->flashdata('result',validation_errors());
			redirect('summary','refresh');
		}
		else
		{
			// echo '<pre>';
			// var_dump($_POST);
			$data = $_POST;
			// var_dump($data);

			$search = $this->summary_model->getRecordsByLikes('availments_test',$data);
			// var_dump($search);

			foreach($search as $key => $value)
			{
				$labs = $this->summary_model->getRecordsByField('lab_test_test','availments_id',$value['id']);
				$labInfo = array('lab_test','amount');

				if($labs)
				{
					foreach($labs as $lkey => $lval)
					{
						foreach($labInfo as $field)
						{
							$search[$key]['lab_test_test'][$lkey][$field] = $lval[$field];
						}
					}
				}
				else
				{
					$search[$key]['lab_test_test'] = NULL;
				}
				$search[$key]['benefits_in-out_patient'] = $this->summary_model->getRecordsByField('benefits_in-out_patient','availment_id',$value['id']);
				$search[$key]['benefits_others'] = $this->summary_model->getRecordsByField('benefits_others','availment_id',$value['id']);
				$search[$key]['benefits_others_as_charged'] = $this->summary_model->getRecordsByField('benefits_others_as_charged','availment_id',$value['id']);
			}
			$data['summary'] = $search;
			// var_dump($data);

			if($search == FALSE)
			{
				$this->session->set_flashdata('result','Search result not found');
			}

			$loadedViews = array(
							'summary/summary_search_view' => NULL,
							'summary/summary_result_view' => $data
							);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function reprintBilled($code)
	{
		$availments = $this->summary_model->getRecordsByField('availments_test','code',$code);
		// echo '<pre>';
		$data['availments'] = $availments;

		$claims_code = $this->summary_model->getRecordsByField('billings_details','loa_code',$code);
		$reprint_details = $this->summary_model->getRecordsByField('billings_reprint','claims_code',$claims_code[0]['claims_code']);
		$data['details'] = $reprint_details;
		// var_dump($claims_code);
	
		$physician = $this->summary_model->getRecordsByField('admission_report_test','code',$code);
		$data['doctors'] = $physician[0]['physician'];

		$diagnosis = $this->summary_model->getRecordsByField('availments_diagnosis','code',$code);
		foreach($diagnosis as $key => $value)
		{
			$data['diagnosis'][] = $value['diagnosis'];
		}

		$benefits['benefits_in-out_patient'] = $this->summary_model->getRecordsByField('benefits_in-out_patient','availment_id',$availments[0]['id']);
		$benefits['benefits_others'] = $this->summary_model->getRecordsByField('benefits_others','availment_id',$availments[0]['id']);
		$benefits['benefits_others_as_charged'] = $this->summary_model->getRecordsByField('benefits_others_as_charged','availment_id',$availments[0]['id']);

		foreach($benefits as $key => $value)
		{
			if($value == FALSE)
			{
				unset($value);
			}
			else
			{
				// PLAN / BENEFIT SET NAME
				$benefit_set_name = $this->summary_model->getRecordById('benefits.benefitset_info',$value[0]['benefit_set_id']);
				foreach($benefit_set_name as $bkey => $bvalue)
				{
					$data['plan'] = $bvalue['benefit_set_name'];
				}

				// For Total Amount of Availments
				foreach($value as $akey => $avalue)
				{
					if($avalue['availed_amount'] != 0.00)
					{
						$data['hospital_bills'][] = $avalue['availed_amount'];
					}
					if($avalue['availed_as-charged'] != 0.00)
					{
						$data['hospital_bills'][] = $avalue['availed_as-charged'];
					}
				}
			}
		}
		//INSURANCE DETAILS
		$data['insurance'] = $this->summary_model->getRecordsByField('insurance','name',$availments[0]['insurance_name']);
		// var_dump($data);

		// var_dump($availments);
		$this->load->view('summary/summary_bill_reprint_view',$data);

		//PRINT TO PDF
		$html = $this->output->get_output();
		
		// Load library
		$this->load->library('dompdf_gen');
		
		// Convert to PDF
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$this->dompdf->stream($claims_code[0]['claims_code'].".pdf",array('Attachment'=>0)); //convert 
	}

	function searchTopsheet()
	{
		$this->form_validation->set_rules('insurance_topsheet','Insurance Name','required|trim|xss_clean');
		$this->form_validation->set_rules('topsheet_date_from','Date From','required|trim|xss_clean');
		$this->form_validation->set_rules('topsheet_date_to','Date To','required|trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('result',validation_errors());
			redirect('summary','refresh');
		}
		else
		{
			$insurance = $_POST['insurance_topsheet'];
			$start = $_POST['topsheet_date_from'];
			$end = $_POST['topsheet_date_to'];

			$topsheet = $this->summary_model->getSummarized('billings_topsheet',$insurance,'insurance_name',$start,$end);

			if($topsheet)
			{
				foreach($topsheet as $key => $value)
				{
					$topsheet_details = $this->summary_model->getRecordsByField('billings_topsheet_details','invoice_number',$value['invoice_number']);
				}
			}

			$data['topsheet'] = $topsheet;
			$data['topsheet_details'] = $topsheet_details;

			$loadedViews = array(
							'summary/summary_search_view' => NULL,
							'summary/summary_topsheet_view' => $data
							);
			$this->load->template($loadedViews,$this->header_links);
		}
	}

	function reprintSummary($invoice_number)
	{
		$topsheet = $this->summary_model->getRecordsByField('billings_topsheet','invoice_number',$invoice_number);
		$insurance = $this->summary_model->getRecordsByField('insurance','name',$topsheet[0]['insurance_name']);

		foreach($topsheet as $key => $value)
		{
			$topsheet_details = $this->summary_model->getRecordsByField('billings_topsheet_details','invoice_number',$value['invoice_number']);
		}

		$data['insurance'] = $insurance;
		$data['topsheet'] = $topsheet;
		$data['topsheet_details'] = $topsheet_details;

		// echo '<pre>';
		// var_dump($data);

		$this->load->view('summary/summary_summary_reprint_view',$data);

		//PRINT TO PDF
		$html = $this->output->get_output();

		//Load Library
		$this->load->library('dompdf_gen');

		//Convert to PDF
		$pdf = "Invoice-".$invoice_number;
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$this->dompdf->stream($pdf.".pdf",array('Attachment'=>0));
	}
}
?>