<?php
Class Claims_model extends CI_Model
{
	function sDate($ID,$Target,$Date,$default)
	{

	}

	function register($table, $data)
	{
		$query = $this->db->set($data);
		$query = $this->db->ignore();
		$query = $this->db->insert($table);
		return $query = $this->db->insert_id();
	}

	function getAllRecords($table)
	{
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

	function getReceived($table,$keyword,$field,$start,$end, $select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('date_encoded >=',$start);
		$query = $this->db->where('date_encoded <=',$end);
		$query = $this->db->like($field,$keyword);
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

	function getRecordById($table,$id,$select ='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('id',$id);
		$query = $this->db->get($table);

		if($query->num_rows() == 1)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function getRecordByField($table,$field,$key,$select = '')
	{
		$query = $this->db->select($select);
		$query = $this->db->where($field,$key);
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

	function getBilling($table,$key1,$key2,$key3,$key4,$key5)
	{
		if($key2 == 'In-Patient')
		{
			$query = $this->db->where('availment_type !=', 'Out-Patient');
		}
		else
		{
			$query = $this->db->where('availment_type',$key2);
		}
		
		$query = $this->db->where('claims_status', $key3);
		$query = $this->db->where('date_encoded >=', $key4);
		$query = $this->db->where('date_encoded <=', $key5);
		$query = $this->db->like('insurance_name', $key1);
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

	function getBilled($table, $keyword,$field,$start,$end,$select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('print_date >=',$start);
		$query = $this->db->where('print_date <=',$end);
		$query = $this->db->like($field,$keyword);
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

	function getSummarized($table, $keyword,$field,$start,$end,$select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('date >=',$start);
		$query = $this->db->where('date <=',$end);
		$query = $this->db->like($field,$keyword);
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

	function update($table,$field,$key,$data)
	{
		$query = $this->db->where($field,$key);
		$query = $this->db->set($data);
		$query = $this->db->update($table);

		if($table == 'billings_compins')
		{
			return $this->getRecordById($table,$key);
		}
		else
		{
			return $query;
		}
	}
}
?>
