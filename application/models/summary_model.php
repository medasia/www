<?php
Class Summary_model extends CI_Model
{
	function getRecordsByLikes($table, $data,$select='')
	{
		$query = $this->db->select($select);

		if(strlen($data['code']))
		{
			$query->like('code',$data['code']);
		}

		if(strlen($data['patient_name']))
		{
			$query->like('patient_name',$data['patient_name']);
		}

		if(strlen($data['company_name']))
		{
			$query->like('company_name',$data['company_name']);
		}

		if(strlen($data['insurance_name']))
		{
			$query->like('insurance_name',$data['insurance_name']);
		}

		if(strlen($data['hospital_name']))
		{
			$query->like('hospital_name',$data['insurance_name']);
		}

		if(strlen($data['availment_type']))
		{
			$query->like('availment_type',$data['insurance_name']);
		}

		if(strlen($data['user']))
		{
			$query->like('user',$data['user']);
		}

		if(strlen($data['claims_status']))
		{
			$query->like('claims_status', $data['claims_status']); 
		}

		if(strlen($data['start']))
		{
			$query->where('date_encoded >=',$data['start']);
		}

		if(strlen($data['end']))
		{
			$query->where('date_encoded <=', $data['end']);
		}

		$query = $this->db->order_by($data['sort_by'],$data['sort']);
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

	function getRecordsByField($table, $field, $key, $select = '')
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
			return FALSE;
		}
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
}
?>