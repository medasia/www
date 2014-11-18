<?php
Class Records_model extends CI_Model {
	/**
	 * Function getAllRecords
	 * 
	 * Fetches all records on table given
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param Int 		$limit 	limits fetched rows
	 * @param Int 		$offset starting offset from resultset
	 * @return boolean|array
	 */
	// function getAllRecords($table, $limit = '100', $offset = NULL) {
	function getAllRecords($table) {
		if($table == 'uploads')
		{
			$query = $this->db->order_by('date_uploaded','desc');
		}
		$query = $this->db->get($table);
		// $query = $this->db->get($table, $limit, $offset);

		if($query->num_rows()) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	/**
	 * Function getRecordById
	 * 
	 * Fetches a single(or multiple, depending on DB records) row using the column ID
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param Int 		$id 	ID of record to be used
	 * @return boolean|array
	 */
	function getRecordById($table, $id, $select ='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('id', $id);
		$query = $this->db->get($table);

		// var_dump($this->db->queries);
		if($query->num_rows() == 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
	/**
	 * Function getRecordByField
	 * 
	 * Fetches records by using FIELD name given by user
	 * 
	 * @access public
	 * @param String $table table to be used
	 * @param String $field column name to be used
	 * @param String $key 	string to be user on WHERE clause
	 * @return boolean|array
	 */
	function getRecordByField($table, $field, $key, $select = '')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field, $key);
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function getRecordByMultiField($table,$data,$select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($data);
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function getRecordByFieldsAndLike($table,$data,$select='')
	{
		$query = $this->db->select($select);
		if(isset($data['cardholder_type']))
		{
			$cardholder_type = $data['cardholder_type'];
			unset($data['cardholder_type']);
		}
		$query = $this->db->where($data);
		if($table == 'benefits.benefitset_info' && isset($cardholder_type))
		{
			$query = $this->db->like('cardholder_type',$cardholder_type);
		}
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}
	}

	function getRecordByFieldWithLimit($table,$field,$key,$limit,$start,$select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field, $key);
		$query = $this->db->limit($limit,$start);
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function countRecordByField($table,$field,$key,$limit,$select='')
	{
		$query = $this->db->select($select)
						->limit($limit,0);
		$query->where($field,$key);
		return $query = $this->db->count_all_results($table);
	}

	/**
	 * Function delete
	 * 
	 * Deletes a record on DB using ID
	 * 
	 * @access public
	 * @param String 	$table 	table to be used
	 * @param Int 		$id 	ID to be used on WHERE clause
	 */
	function delete($table, $id, $field = '')
	{
		$field == ''? $field = 'id' : $field = $field;
		$query = $this->db->where($field, $id);
		return $query = $this->db->delete($table);
	}

	function deleteByField($table,$field,$id)
	{
		$query = $this->db->where($field,$id);
		return $query = $this->db->delete($table);
	}

 	function register($table, $data)
 	{
		$query = $this->db->set($data);
		$query = $this->db->ignore();
		$query = $this->db->insert($table);
		return $query = $this->db->insert_id();
	}

	function registerBatch($table, $data)
	{
		return $query = $this->db->insert_batch($table, $data);
	}
	
	function getCountByField($table, $field, $key)
	{
		$this->db->where($field, $key);
		$this->db->from($table);
		return $this->db->count_all_results();
	}

	function update($table,$field,$data,$key)
	{
		$query = $this->db->where('id',$key);
		$query = $this->db->set($field,$data);
		return $query = $this->db->update($table);
	}

	function updateMultiField($table,$field,$key,$data)
	{
		$query = $this->db->where($field,$key);
		$query = $this->db->set($data);
		return $query = $this->db->update($table);
	}
	function getField($table,$field,$key)
	{
		$query = $this->db->select($field);
		$query = $this->db->where('id',$key);
		$query = $this->db->get($table);
		return $query->num_rows();
	}
	
	function getRecordByIdAndLike($table, $id, $keyword,$select = '')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('id', $id);
		$query = $this->db->like("firstname", $keyword);
		$query = $this->db->or_like("middlename", $keyword);
		$query = $this->db->or_like("lastname", $keyword);
		$query = $this->db->limit('100','0');
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function getRecord($table, $keyword, $limit,$start)
	{
		if(strlen($start))
		{
			$start = $start;
		}
		else
		{
			$start = '0';
		}

		switch($table)
		{
			case 'company_insurance':
				$query = $this->db->like("company", $keyword);
				$query = $this->db->or_like("insurance", $keyword);
				$query = $this->db->limit($limit,$start);
				break;
			case 'hospital':
				$query = $this->db->like("name", $keyword);
				$query = $this->db->limit($limit,$start);
				break;
			case 'patient':
			case 'dentistsanddoctors':
				$query = $this->db->like("firstname", $keyword);
				$query = $this->db->or_like("middlename", $keyword);
				$query = $this->db->or_like("lastname", $keyword);
				$query = $this->db->limit($limit,$start);
				break;
			case 'company':
			case 'insurance':
				$query = $this->db->like("name", $keyword);
				$query = $this->db->or_like("code", $keyword);
				$query = $this->db->limit($limit,$start);
				break;
			case 'emergency_room':
				$query = $this->db->like("card_number", $keyword);
				$query = $this->db->limit($limit,$start);
				break;
			case 'hospital_account':
				$query = $this->db->like("account_name", $keyword);
				$query = $this->db->limit($limit,$start);
				break;
			case 'diagnosis':
				$query = $this->db->like("diagnosis",$keyword);
				$query = $this->db->limit($limit,$start);
				break;
		}
		$query = $this->db->get($table);
		
		if($query->num_rows())
		{
			// return var_dump($this->db->queries);
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function getHospitalByField($table, $keyword, $branch, $address, $province, $region, $limit,$start, $select='')
	{
		$query = $this->db->select($select)
					// ->from($table)
					->limit($limit,$start);

		if(strlen($keyword))
		{
			$query->like('name',$keyword);
		}

		if(strlen($branch))
		{
			$query->like('branch',$branch);
		}

		if(strlen($address))
		{
			$query->like('street_address', $address);
			$query->or_like('subdivision_village',$address);			
			$query->or_like('barangay', $address);
			$query->or_like('city', $address);	
		}

		if(strlen($province))
		{
			$query->like('province',$province);
		}

		if(strlen($region))
		{
			$query->like('region',$region);
		}

		$query = $this->db->get($table);

		if($query->num_rows())
		{
		// return var_dump($this->db->queries);
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function getDoctorsByField($table,$firstname,$middlename,$lastname,$specialization, $address, $limit,$start, $select = '')
	{
		$query = $this->db->select($select)
					->limit($limit,$start);

		if(strlen($keyword))
		{
			$query->like('firstname', $keyword);
			$query->or_like('middlename', $keyword);
			$query->or_like('lastname', $keyword);
		}

		if(strlen($firstname))
		{
			$query->like('firstname',$firstname);
		}
		if(strlen($middlename))
		{
			$query->like('middlename',$middlename);
		}
		if(strlen($lastname))
		{
			$query->like('lastname',$lastname);
		}

		if(strlen($specialization))
		{
			$query->like('specialization',$specialization);
		}

		if(strlen($address))
		{
			$query->like('clinic1', $address);
			$query->or_like('clinic2', $address);
			$query->or_like('clinic3', $address);
		}

		$query = $this->db->get($table);

		if($query->num_rows())
		{
		// return var_dump($this->db->queries);
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function countHospitalResults($table, $keyword, $branch, $address, $province, $region, $limit, $select='')
	{
		$query = $this->db->select($select)
					// ->from($table)
					->limit($limit,0);

		if(strlen($keyword))
		{
			$query->like('name',$keyword);
		}

		if(strlen($branch))
		{
			$query->like('branch',$branch);
		}

		if(strlen($address))
		{
			$query->like('street_address', $address);
			$query->or_like('subdivision_village',$address);			
			$query->or_like('barangay', $address);
			$query->or_like('city', $address);	
		}

		if(strlen($province))
		{
			$query->like('province',$province);
		}

		if(strlen($region))
		{
			$query->like('region',$region);
		}

		return $query = $this->db->count_all_results($table);
	}

	function countDoctorsResults($table, $firstname,$middlename,$lastname, $specialization, $address, $limit, $select = '')
	{
		$query = $this->db->select($select)
					->limit($limit,0);

		if(strlen($keyword))
		{
			$query->like('firstname', $keyword);
			$query->or_like('middlename', $keyword);
			$query->or_like('lastname', $keyword);
		}

		if(strlen($firstname))
		{
			$query->like('firstname',$firstname);
		}

		if(strlen($middlename))
		{
			$query->like('middlename',$middlename);
		}

		if(strlen($lastname))
		{
			$query->like('lastname',$lastname);
		}

		if(strlen($specialization))
		{
			$query->like('specialization',$specialization);
		}

		if(strlen($address))
		{
			$query->like('clinic1', $address);
			$query->or_like('clinic2', $address);
			$query->or_like('clinic3', $address);
		}

		return $query = $this->db->count_all_results($table);
	}

	function countSearchResult($table,$keyword,$field,$limit)
	{
		$query = $this->db->select($select)
						->limit($limit,0);

		if(strlen($keyword))
		{
			$query->like($field,$keyword);
		}

		return $query = $this->db->count_all_results($table);
	}

	function countRecord($table)
	{
		return $this->db->count_all($table);
	}

	function getAllRecordsPage($table, $limit, $start)
	{
		$query = $this->db->get($table,$limit,$start);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function getRecordsByThreeFields($table,$field1,$field2,$field3,$key1,$key2,$key3)
	{
		$query = $this->db->where($field1,$key1);
		$query = $this->db->where($field2,$key2);
		$query = $this->db->where($field3,$key3);
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function getRecordsByTwoFields($table,$field1,$field2,$key1,$key2)
	{
		$query = $this->db->where($field1,$key1);
		$query = $this->db->where($field2,$key2);
		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function searchByLikes($table,$keyword,$limit,$start,$select='')
	{
		$query = $this->db->select($select)
						->limit($limit,$start);

		if($table == 'hospital')
		{
			$query->like('name',$keyword);
		}

		if($table == 'dentistsanddoctors')
		{
			$query->like('firstname',$keyword);
			$query->or_like('middlename',$keyword);
			$query->or_like('lastname',$keyword);
		}

		$query = $this->db->get($table);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function countHospaccnt($table,$keyword,$limit, $select='')
	{
		$query = $this->db->select($select)
						->limit($limit,0);

		if($table == 'hospital')
		{
			$query->like('name',$keyword);
		}

		if($table == 'dentistsanddoctors')
		{
			$query->like('firstname',$keyword);
			$query->or_like('middlename',$keyword);
			$query->or_like('lastname',$keyword);
		}

		return $query = $this->db->count_all_results($table);
	}

	function download($table)
	{
		return $query = $this->db->get($table);
	}
}
?>