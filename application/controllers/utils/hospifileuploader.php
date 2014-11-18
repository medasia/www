<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class HospiFileuploader extends CI_Controller {
	function __construct() {
		parent::__construct();
		$models = array(
					    'records_model' => '',
					    'fileuploader_model' => ''
						);
		$this->load->model($models,'',TRUE);
	}
	function upto($tableuri) {
		$config['upload_path'] = $_SERVER['DOCUMENT_ROOT'].'files/uploads/'.$tableuri;
		$config['allowed_types'] = 'xls';
		$config['file_name'] = $this->encrypt->sha1(md5($_FILES['file']['name'].now()));
		$this->load->library('upload', $config);

		$ref = $this->input->server('HTTP_REFERER', TRUE); //previous page

		switch($tableuri) {
			case 'hospclinic':
				$table = 'hospital';
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
			$this->session->set_flashdata('result', 'ERROR: File already exists. Please contact IT Dept.');
			redirect($ref, 'location');
		}
		else
		{ //file does not exist. PROCEED.
			if($this->upload->do_upload('file'))
			{ //upload success
				echo $this->upload->display_errors(); //display error(s) IF ANY
				$file = $this->upload->data(); //get details of uploaded file
				$this->excel_reader->read($file['full_path']); //read file using Excel Reader (3rd party) library
				$worksheet = $this->excel_reader->worksheets[0]; //an array for the whole worksheet[0](first sheet)
				foreach($worksheet as $key => $value)
				{
					if($key == '0') $field = $value;
					$data[$key] = array_combine($field, $value);
					unset($data[0]); //remove field names from array
				}
				$result = $this->records_model->registerBatch($table, $data);
				
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
						$this->session->set_flashdata('result', 'Succesfully uploaded file.');
						redirect($ref, 'location');
					} 
					else
					{
						//return false with flash data
						$this->session->set_flashdata('result', 'ERROR: Inserting filedata. Please contact IT Dept.');
						redirect($ref, 'location');
					}
				}
				else
				{ //insert batch failed
					//return false with flash data
					$this->session->set_flashdata('result', 'ERROR: Inserting batch. Please contact IT Dept.');
					redirect($ref, 'location');
				}
			}
			else
			{ //upload failed
				//return false with flash data
				$this->session->set_flashdata('result', 'ERROR: Uploading file.'.$this->upload->display_errors().'Please contact IT Dept.');
				var_dump($file);
				redirect($ref, 'location');
			}
		}
	}
}