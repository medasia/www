<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
session_start();

class Table_export extends CI_Controller {
 
    function __construct() 
    {
        parent::__construct();
 
        // Here you should add some sort of user validation
        // to prevent strangers from pulling your table data
    }
 
    function index($table_name)
    {
        ini_set('display_errors',1); 
        error_reporting(E_ALL);

        $table_name = $_POST['table_name'];
        $start = $_POST['start'];
        $end = $_POST['end'];

        if($table_name == 'patient')
        {
            $date_field = 'declaration_date';
        }
        else
        {
            $date_field = 'date_accredited';
        }
        if(isset($start) || isset($end))
        {
            $query = $this->db->where($date_field.' >=',$start);
            $query = $this->db->where($date_field.' <=',$end);
            $query = $this->db->get($table_name);
        }
        else
        {
            $query = $this->db->get($table_name);
        }
         
        if(!$query)
            return false;
 
        // Starting the PHPExcel library
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
 
        $objPHPExcel->setActiveSheetIndex(0);
 
        // Field names in the first row
        $fields = $query->list_fields();
        $col = 0;
        foreach ($fields as $field)
        {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
            $col++;
        }
 
        // Fetching the table data
        $row = 2;
        foreach($query->result() as $data)
        {
            $col = 0;
            foreach ($fields as $field)
            {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);
                $col++;
            }
 
            $row++;
        }
 
        $objPHPExcel->setActiveSheetIndex(0);
 
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
 
        // Sending headers to force the user to download the file
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$table_name.'#'.mdate('%M %d, %Y', mysql_to_unix($start)).'-'.mdate('%M %d, %Y',mysql_to_unix($end)).'.xls"');
        header('Cache-Control: max-age=0');
 
        ob_end_clean(); // FOR CLEAN DOWNLOAD OF XLS FILE
        $objWriter->save('php://output');
    }
}
?>