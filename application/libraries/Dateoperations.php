<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
* Class dateOperations
* Created in July 07, 2009, by Vicente Russo Neto <vicente@vrusso.com.br>
* Modified on November 04, 2010, by Vicente Russo Neto <vicente@vrusso.com.br>
* 
* @param 	integer	$date - the date to be processed
* @param 	string	$what - what piece of date to process. Values: day|month|year|hour|minute|second
* @param 	integer	$value - how much will be increased or decreased
* @param 	string	$return_format - 'mysql' format or 'timestamp' format 
* @author 	Vicente Russo Neto <vicente@vrusso.com.br>
* @return 	string|boolean
* @version 	0.2
* 
* Description: This class can add or subtract days, months, years, hours, minutes, seconds and return the result. Created for
* PHP Framework CodeIgniter (www.codeigniter.com). Tested on 1.7.2.
*
* Usage:
* echo '<br>'.$this->dateoperations->sum('2010-11-04','day',1,FALSE); // Prints: 2010-11-05
* echo '<br>'.$this->dateoperations->sum('2010-11-04 00:00:00','minute',15); // Prints: 2010-11-04 00:15:00
* echo '<br>'.$this->dateoperations->subtract('2010-11-04 00:00:00','second',35); // Prints: 2010-11-03 23:59:25
* echo '<br>'.$this->dateoperations->subtract('2010-11-04 00:00:00','year',1); // Prints: 2009-11-04 00:00:00
* echo '<br>'.$this->dateoperations->subtract('2010-11-04 00:00:00','day',15, FALSE); // Prints: 2010-10-20 
*/

class Dateoperations {
    
	public function sum ($date,$what,$value,$full=TRUE,$return_format='mysql') {
		
		$return = $this->calculate($date,$what,$value,'sum',$full,$return_format);
		return $return;
		
	}
	
	public function subtract ($date,$what,$value,$full=TRUE,$return_format='mysql') {
		
		$return = $this->calculate($date,$what,$value,'subtract',$full,$return_format);
		return $return;
		
	}	
	
	private function calculate($date,$what,$value,$operation,$full,$return_format) {
		
		// checking args
		if($operation!='sum' && $operation!='subtract') return FALSE;
		if ($what!='day' && $what!='month' && $what!='year' && $what!='hour' && $what!='minute' && $what!='second') return FALSE;

		// validating date or datetime
		if (!preg_match('/\\A(?:^((\\d{2}(([02468][048])|([13579][26]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])))))|(\\d{2}(([02468][1235679])|([13579][01345789]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|(1[0-9])|(2[0-8]))))))(\\s(((0?[0-9])|(1[0-9])|(2[0-3]))\\:([0-5][0-9])((\\s)|(\\:([0-5][0-9])))?))?$)\\z/', $date)) {
			return FALSE;
		}
		
		/* From this point I'm sure its a valid date or datetime no matter what */
		
		$only_date = substr($date, 0, 10);
		$only_time = substr($date, 11, 8);
		
		list($year, $month, $day) = explode("-", $only_date);
		
		if ($what=='day') 		$day 	= $operation=='sum' ? intval($day) + intval($value) : intval($day) - intval($value) ;
		if ($what=='month') 	$month 	= $operation=='sum' ? intval($month) + intval($value) : intval($month) - intval($value);
		if ($what=='year') 		$year 	= $operation=='sum' ? intval($year) + intval($value) : intval($year) - intval($value);
	
		$hour = $minute = $second = 0;
	
		if($only_time!='') { // we have time too!
			
			list($hour, $minute, $second) 	= explode(":", $only_time);
			
			if ($what=='hour') 		$hour 	= $operation=='sum' ? intval($hour) + intval($value) : intval($hour) - intval($value);
			if ($what=='minute') 	$minute	= $operation=='sum' ? intval($minute) + intval($value) : intval($minute) - intval($value);
			if ($what=='second') 	$second	= $operation=='sum' ? intval($second) + intval($value) : intval($second) - intval($value);
				
		}

		$date_tmp = mktime($hour, $minute, $second, $month, $day, $year);    

		if ($return_format=='mysql') {
			if($full) $date_tmp = date('Y-m-d H:i:s', $date_tmp); // return date and time
			else $date_tmp = date('Y-m-d', $date_tmp); // return only date
		} elseif (!$return_format=='timestamp') {
			return FALSE;   
		}
		               
		return $date_tmp;	
		
	}

    
}

/* End of file dateOperations.php */
/* Location: ./application/libraries/dateOperations.php */