<?php
class Accounting_model extends CI_Model
{
	function searchByLike($table,$keyword,$limit,$select='')
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

	function getRecordsByField($table, $field, $key, $select='')
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

	function getRecordsByFieldAndDate($table,$field,$key,$start,$end,$select='')
	{
		$query = $this->db->select($select);
		$query = $this->db->where('claims_dateofrecieve >=',$start);
		$query = $this->db->where('claims_dateofrecieve <=',$end);
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

	function getRecordByMultiField($table, $data, $select='')
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
}
?>