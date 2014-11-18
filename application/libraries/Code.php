<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Code
{
	protected $CI;
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function getCode($code = '')
	{
		// $this->CI->db->select('code');
		$this->CI->db->where('code', $code);
		$res = $this->CI->db->get('code_test')->result_array();
		var_dump($res);
		if($res)
		{
			return $res;
		}
		else
		{
			return $this->setCode();
		}
	}

	public function setCode()
	{
		$code = '';
		$uniqueCode = FALSE;

		$year = date("y");
		$alpha = array('A','B','C','E','L','M','O','R','T','U','X','Y');
		$numeric = array('1','2','3','4','5','6','7','8','9');
		
		while($uniqueCode == FALSE)
		{
			for($i=0;$i<2;$i++)
			{
				$code.=($alpha[rand()%count($alpha)]);
			}
			$code.=$year;
			for($i=0;$i<4;$i++)
			{
				$code.=($numeric[rand()%count($numeric)]);
			}

			$data = array('code' => $code, 'date' => mdate('%Y-%m-%d', now()));
			$register = $this->insertCode($data);
			
			if($register)
			{
				$uniqueCode = TRUE;
			}
		}
		return $code;
	}

	public function insertCode($data)
	{
		$this->CI->db->set($data);
		$this->CI->db->insert('code_test');
		return $this->CI->db->insert_id();
	}
}