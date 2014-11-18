<?php
class Autocomplete_model extends CI_Model {
	/**
	 * Function getRecord
	 * 
	 * Fetches a single(or multiple, depending on user input) row/s of record
	 * 
	 * @access public
	 * @param String $table 	table to be used
	 * @param String $keyword 	string to be on LIKE clause
	 * @return boolean|array
	 */
	function getRecord($table, $keyword) {
		switch($table) {
			case 'company_insurance':
				$query = $this->db->like("company", $keyword);
				$query = $this->db->or_like("insurance", $keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'hospital':
				$query = $this->db->like("name", $keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'patient':
			case 'dentistsanddoctors':
				$query = $this->db->like("firstname", $keyword, 'both');
				$query = $this->db->or_like("middlename", $keyword, 'both');
				$query = $this->db->or_like("lastname", $keyword, 'both');
				$query = $this->db->or_like('CONCAT(firstname," ",lastname)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(firstname," ",middlename)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(lastname," ",firstname)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(lastname," ",middlename)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(middlename," ",firstname)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(middlename," ",lastname)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(firstname," ",middlename," ",lastname)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(firstname," ",lastname," ",middlename)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(lastname," ",middlename," ",firstname)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(lastname," ",firstname," ",middlename)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(middlename," ",firstname," ",lastname)', $keyword, 'both');
				$query = $this->db->or_like('CONCAT(middlename," ",lastname," ",firstname)', $keyword, 'both');
				$query = $this->db->limit('100','0');
				break;
			case 'company':
			case 'insurance':
				$query = $this->db->like("name", $keyword);
				$query = $this->db->or_like("code", $keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'brokers':
				$query = $this->db->like("name", $keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'emergency_room':
				$query = $this->db->like("card_number", $keyword);
				$query = $this->db->limit('100','0');
				break; 
			case 'hospital_account':
				$query = $this->db->like("account_name", $keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'admission_report_test':
				$query = $this->db->like("patient_name", $keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'monitoring':
				$query = $this->db->like("patient_name",$keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'availments_test':
				$query = $this->db->like("patient_name",$keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'diagnosis':
				$query = $this->db->like("diagnosis",$keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'benefits.benefit_set_condition':
				$query = $this->db->like("condition_name",$keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'benefits.benefit_set_exclusion':
				$query = $this->db->like("exclusion_name",$keyword);
				$query = $this->db->limit('100','0');
				break;
			case 'verifications_special_loa':
				$query = $this->db->like('code',$keyword);
				$query = $this->db->limit('100','0');
				$table = 'availments_test';
				break;
			case 'operations_special_verifications':
				$query = $this->db->like("code",$keyword);
				$query = $this->db->limit('100','0');
				$table = 'availments_special';
				break;
		}
		$query = $this->db->get($table);
		if($query->num_rows()) {
			// return var_dump($this->db->queries);
			return $query->result_array();
		} else {
			return false;
		}
	}
	function getRecordByField($table, $field, $key, $select = '') {
		$query = $this->db->select($select);
		$query = $this->db->where($field, $key);
		$query = $this->db->get($table);

		// var_dump($this->db->queries);
		if($query->num_rows()) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getAllRecords($table,$field)
	{
		$query = $this->db->order_by($field,'asc');
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return null;
		}
	}
}
?>