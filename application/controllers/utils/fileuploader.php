<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
echo memory_get_usage();

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit','150M');


class Fileuploader extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$models = array(
					    'records_model' => '',
					    'fileuploader_model' => ''
						);
		$this->load->model($models,'',TRUE);
	}
	function upto($tableuri)
	{
		// THESE LINES OF CODES ARE USED FOR UPLOADING TEMPLATES
// 		if($tableuri == 'templates')
// 		{
// 			$config['upload_path'] ='/home/dev/web/operations051513/files/uploads/'.$tableuri;
// 			$config['allowed_types'] = 'xls';
// 			$config['file_name'] = $this->encrypt->sha1(md5($_FILES['file']['name'].now()));
// 			$this->load->library('upload', $config);

// 			$ref = $this->input->server('HTTP_REFERER', TRUE); //previous page

// 			if ( ! file_exists($config['upload_path']))
// 			{
// 				mkdir($config['upload_path'], 0777); //make directory if it doesn't exist
// 			}
// 			if(file_exists($config['upload_path'].$config['file_name'] ))
// 			{
// 				//file exists
// 				//file exists return false with flash data
// 				$this->session->set_flashdata('result', 'ERROR: File already exists. Please contact IT Dept.');
// 				redirect($ref, 'location');
// 			}
// 			else
// 			{ //file does not exist. PROCEED.
// 				if($this->upload->do_upload('file'))
// 				{
// 					//upload success
// 					echo $this->upload->display_errors(); //display error(s) IF ANY
// 					$file = $this->upload->data(); //get details of uploaded file
// 					$this->excel_reader->read($file['full_path']); //read file using Excel Reader (3rd party) library

// 					$user = $this->session->userdata('logged_in');
// 					$filedata = array(
// 								'uploaded_to'	=> $this->uri->segment(4),
// 								'uploader'		=> $user['username'],
// 								'date_uploaded'	=> mdate('%Y-%m-%d', now()),
// 								'hash'			=> $file['raw_name'],
// 								'filename'		=> $file['client_name'],
// 								'path'			=> $file['file_path'],
// 								'size'			=> $file['file_size'].' kB'
// 									);
// 					$resultFileData = $this->fileuploader_model->insert($filedata);
// 					if($resultFileData)
// 					{
// 						/** Error reporting */
// 						error_reporting(E_ALL);
// 						ini_set('display_errors', TRUE);
// 						ini_set('display_startup_errors', TRUE);

// 						//insert filedata success
// 						//return true with flash data
// 						var_dump($config['upload_path']);
// 						$this->session->set_flashdata('result', 'Succesfully uploaded file.');
// 						redirect($ref, 'location');
// 					} 
// 					else
// 					{
// 						/** Error reporting */
// 						error_reporting(E_ALL);
// 						ini_set('display_errors', TRUE);
// 						ini_set('display_startup_errors', TRUE);

// 						//upload failed
// 						//return false with flash data
// 						var_dump($config['upload_path']);
// 						$this->session->set_flashdata('result', 'ERROR: Uploading file.'.$this->upload->display_errors().'Please contact IT Dept.');
// 						//var_dump($file);
// 						redirect($ref, 'location');
// 					}
// 				}
// 			}
// 		}
// 	}
// }

		$config['upload_path'] = '/home/dev/web/operations051513/files/uploads/'.$tableuri;
		$config['allowed_types'] = 'xls|csv';
		$config['file_name'] = $this->encrypt->sha1(md5($_FILES['file']['name'].now()));
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		$ref = $this->input->server('HTTP_REFERER', TRUE); //previous page

		switch($tableuri) {
			case 'company_insurance':
				$table = $tableuri;
				break;
			case 'diagnosis':
				$table = $tableuri;
				break;
			case 'company_insurance_members':
				$table = 'patient';
				break;
			case 'hospclinic':
				$table = 'hospital_test';
				break;
			case 'dentistsdoctors':
				$table = 'dentistsanddoctors';
				break;
			case 'emerroom':
				$table = 'emergency_room';
				break;
			case 'hospaccnt':
				$table = 'hospital_account';
				break;
			case 'new_member':
				$table = 'patient';
				break;
			default:
				$table = $tableuri;
				break;
		}

		if ( ! file_exists($config['upload_path']))
		{
			 mkdir($config['upload_path'], 0777); //make directory if it doesn't exist
		}
		if(file_exists($config['upload_path'].$config['file_name'] ))
		{ //file exists
			//file exists return false with flash data
			$this->session->set_flashdata('result',' <b>ERROR: File already exists. Please contact IT Dept.</b>');
			redirect($ref, 'location');
		}
		else
		{ //file does not exist. PROCEED.
			$date_created = date('Y-m-d');
			if($this->upload->do_upload('file'))
			{ //upload success
				echo $this->upload->display_errors(); //display error(s) IF ANY
				$file = $this->upload->data(); //get details of uploaded file
				$this->excel_reader->read($file['full_path']); //read file using Excel Reader (3rd party) library
				$worksheet = $this->excel_reader->worksheets[0]; //an array for the whole worksheet[0](first sheet)
				foreach($worksheet as $key => $value) {
					if($key == '0') $field = $value;
					$data[$key] = array_combine($field, $value);
					unset($data[0]); //remove field names from array
				}
				if($tableuri == 'company_insurance_members')
				{
					//insert each row ONE BY ONE
					//and get insert_id
					//to be used for patient_company_insurance
					foreach($data as $key => $value)
					{
						//fix cardholder shit
						$user = $this->session->userdata('logged_in');
						$data[$key]['user'] = $user['name'];
						$data[$key]['age'] = computeAge($data[$key]['dateofbirth']);
						$data[$key]['date_encoded'] = $date_created;
						$data[$key]['cardholder_type'] == 'principal'? $data[$key]['cardholder'] = $data[$key]['lastname'].', '.$data[$key]['firstname'].' / '.$data[$key]['firstname'] : $data[$key]['cardholder']=$data[$key]['cardholder'];
						// var_dump($data[$key]);
						$insert_id = $this->records_model->register('patient', $data[$key]);
						
						if($insert_id)
						{
							// add to patient-company-insurance
							$data2 = array(
											'patient_id' => $insert_id,
											'company_insurance_id' => $_POST['company_insurance_id'] // LAST VALUE OF HTTP_REFERER
										);
							$result = $this->records_model->register('patient_company_insurance', $data2);
						}
					}
				}
				else if($tableuri == 'dentistsdoctors')
				{
					//same as members BUT
					//use clinics as second table after getting insert id 
					foreach($data as $key => $value)
					{
						//MULTI UPLOAD NO CLINICS
						$user = $this->session->userdata('logged_in');
						$data[$key]['user'] = $user['name'];
						$result = $this->records_model->register('dentistsanddoctors', $data[$key]);
					}
				}
				// else if($tableuri == 'hospclinic')
				// {
				// 	foreach($data as $key => $value)
				// 	{
				// 		//MULTI UPLOAD NO CLINICS
				// 		$user = $this->session->userdata('logged_in');
				// 		$data[$key]['user'] = $user['name'];
				// 		$result = $this->records_model->register('hospital', $data[$key]);
				// 	}
				// }
				// else
				// {
				// 	$result = $this->records_model->registerBatch($table, $data);
				// }
				else
				{
					foreach($data as $key => $value)
					{
						$data[$key]['date_created'] = $date_created;
						$result = $this->records_model->register($table, $data[$key]);
					}
				}
				if($result)
				{ //insert batch success
					$user = $this->session->userdata('logged_in');
					$filedata = array(
								'uploaded_to'	=> $this->uri->segment(4),
								'uploader'		=> $user['username'],
								'date_uploaded'	=> mdate('%Y-%m-%d', now()),
								'hash'			=> $file['raw_name'],
								'filename'		=> $file['client_name'],
								'path'			=> $file['file_path'],
								'size'			=> $file['file_size'].' kB'
									);
					$resultFileData = $this->fileuploader_model->insert($filedata);
					if($resultFileData)
					{ //insert filedata success
						//return true with flash data
						$this->session->set_flashdata('result', '<b>Succesfully uploaded file.</b>');
						redirect($ref, 'location');
					} 
					else
					{
						//return false with flash data
						$this->session->set_flashdata('result', '<b>ERROR: Inserting filedata. Please contact IT Dept.</b>');
						redirect($ref, 'location');
					}
				}
				else
				{ //insert batch failed
					//return false with flash data
					$this->session->set_flashdata('result', '<b>ERROR: Inserting batch. Database Error. '.$this->upload->display_errors().' Please contact IT Dept.</b>');
					redirect($ref, 'location');
				}
			}
			else
			{ //upload failed
				//return false with flash data
				echo $config['upload_path'];
				$this->session->set_flashdata('result', '<b>ERROR: Uploading file.'.$this->upload->display_errors().'Please contact IT Dept.</b>');
				//var_dump($file);
				redirect($ref, 'location');
			}
		}
	}

	function upload_pdf($table)
	{
		$config['upload_path'] = '/home/dev/web/operations051513/files/uploads/pdf/'.$table;
		$config['allowed_types'] = '*'; // SET TO ALL, SINCE THERE'S AN ERROR IN UPLOADING PDF FILE TYPE, SPECIAL VALIDATION ARE IN THE INNER PROCESS
		$config['file_name'] = $this->encrypt->sha1(md5($_FILES['file']['name'].now()));
		$this->load->library('upload',$config);
		$this->upload->initialize($config);

		$ref = $this->input->server('HTTP_REFERER', TRUE);
		if(!file_exists($config['upload_path']))
		{
			mkdir($config['upload_path'],0777);
		}
		if(file_exists($config['upload_path'].$config['file_name']))
		{
			$this->session->set_flashdata('result','ERROR: File already exists');
			redirect($ref,'location');
		}
		else
		{
			// SPECIAL VALIDATION FOR PDF FILES, ONLY PDF ARE ALLOWED TO BE UPLOADED IN THIS PART
			$pdf = array('application/pdf',
				'application/x-pdf',
				'application/x-download',
				'binary/octet-stream',
				'application/unknown',
				'application/force-download');
			foreach($pdf as $key => $value)
			{
				if($_FILES['file']['type'] == $value)
				{
					$file_allowed = TRUE;
				}
			}

			if($file_allowed == TRUE)
			{
				if($this->upload->do_upload('file'))
				{
					echo $this->upload->display_errors();
					$file = $this->upload->data();
					$user = $this->session->userdata('logged_in');

					switch($table)
					{
						case 'benefit_set_condition':
							$data = array(
									'condition_name' => str_replace('.pdf', '', $file['client_name']),
									'condition_details' => '',
									'filename' => $file['client_name'],
									'user' => $user['name']
									);
							$result = $this->records_model->register('benefits.benefit_set_condition',$data);
							break;

						case 'benefit_set_exclusion':
							$data = array(
									'exclusion_name' => str_replace('.pdf', '', $file['client_name']),
									'exclusion_details' => '',
									'filename' => $file['client_name'],
									'user' => $user['name']
									);
							$result = $this->records_model->register('benefits.benefit_set_exclusion',$data);
							break;

						default:
							break;
					}

					if($result)
					{
						$filedata = array(
								'uploaded_to' => $this->uri->segment(4),
								'uploader' => $user['name'],
								'date_uploaded' => mdate('%Y-%m-%d',now()),
								'hash' => $file['raw_name'],
								'filename' => $file['client_name'],
								'path' => $file['file_path'],
								'size' => $file['file_size'].' kB'
								);
						$resultFileData = $this->fileuploader_model->insert($filedata);

						if($resultFileData)
						{
							$this->session->set_flashdata('result','Succesfully uploaded file.');
							redirect($ref,'location');
						}
						else
						{
							$this->session->set_flashdata('result','ERROR: Inserting filedata error.');
							redirect($ref,'location');
						}
					}
					else
					{
						$this->session->set_flashdata('result','ERROR: Uploading file '.$this->upload->display_errors().'Please contact IT Department');
						redirect($ref,'location');
					}
				}
				else
				{
					$this->session->set_flashdata('result','ERROR: Uploading file '.$this->upload->display_errors().'Please contach IT Department');
					redirect($ref,'location');
				}
			}
			else
			{
				$this->session->set_flashdata('result',"ERROR: Only PDF file is allowed! You are uploading '".$_FILES['file']['type']."' filetype.");
				redirect($ref,'location');
			}
		}
	}
}
?>
