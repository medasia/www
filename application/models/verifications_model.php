<?php
Class Verifications_model extends CI_Model 
{
	function register($table, $data)
	{
		$query = $this->db->set($data);
		$query = $this->db->ignore();
		$query = $this->db->insert($table);
		return $query = $this->db->insert_id();
	}

	function getAllRecords($table, $limit = '100', $offset = NULL)
	{
		// $query = $this->db->limit('100','0');
		// $query = $this->db->query('SELECT * FROM availments_test');
		// // return $query->result();
		$query = $this->db->get($table,$limit,$offset);

		if($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function getDoctors($table)
	{
		$query = $this->db->order_by('firstname','asc');
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

	function getRecordsByDate($table,$date)
	{
		$query = $this->db->limit('100','0');
		$query = $this->db->where('date_encoded',$date);
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

	function getRecordByField($table, $field, $key, $select = '')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field, $key);
		$query = $this->db->get($table);

		// var_dump($this->db->queries);
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

		// SOME SPECIAL CONDITIONS PER TABLE
		if($table == 'patient_illness' || $table == 'benefits_overall_mbl')
		{
			$query = $this->db->order_by('date','DESC');
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

	function getRecordsByTwoFields($table, $field1, $field2, $key1, $key2, $select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field1, $key1);
		$query = $this->db->where($field2, $key2);
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

	function getRecordsByTwoFieldsOrderByDesc($table, $field1, $field2, $key1, $key2, $select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field1, $key1);
		$query = $this->db->where($field2, $key2);
		$query = $this->db->order_by('id','DESC');
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

	function update($table,$field,$id,$data)
	{
		$query = $this->db->where($field,$id);
		$query = $this->db->set($data);
		return $query = $this->db->update($table);
	}
	
	function delete($table,$field, $id)
	{
		// $field == ''? $field = 'id' : $field = $field;
		$query = $this->db->where($field, $id);
		return $query = $this->db->delete($table);
	}
}
