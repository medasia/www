<?php
Class Operations_model extends CI_Model {
	function getRecordById($table, $id, $select ='') {
		$query = $this->db->select($select);
		$query = $this->db->where('id', $id);
		$query = $this->db->get($table);

		// var_dump($this->db->queries);
		if($query->num_rows() == 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	function getRecordByField($table, $field, $key, $select = '')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field, $key);
		$query = $this->db->get($table);

		// echo '<pre>';
		// var_dump($this->db->queries);
		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
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

	function searchByMultiField($table,$data,$select='')
	{
		$query = $this->db->select($select)
						->limit('100',0);

		if(strlen($data['firstname']))
		{
			$query->like('firstname',$data['firstname']);
		}

		if(strlen($data['middlename']))
		{
			$query->like('middlename',$data['middlename']);
		}

		if(strlen($data['lastname']))
		{
			$query->like('lastname',$data['lastname']);
		}

		if(strlen($data['cardholder_type']))
		{
			$query->like('cardholder_type',$data['cardholder_type']);
		}

		if(strlen($data['status']))
		{
			$query->like('status',$data['status']);
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

	function getRecordsByTwoFields($table, $field1,$field2, $key1, $key2)
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

	function update($table,$data,$key)
	{
		$query = $this->db->where($key);
		$query = $this->db->set($data);
		return $query = $this->db->update($table);
	}
}
