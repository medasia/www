<?php
if(empty($patients))
{
    echo "<h2>No Record/s Found!!!</h2>";
}
else
{
    echo '<div class="table_scroll">';
    $tmpl = array (
                    'table_open'          => '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',

                    'heading_row_start'   => '<tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<th>',
                    'heading_cell_end'    => '</th>',

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>',

                    'row_alt_start'       => '<tr>',
                    'row_alt_end'         => '</tr>',
                    'cell_alt_start'      => '<td>',
                    'cell_alt_end'        => '</td>',

                    'table_close'         => '</table>'
                    );

    $this->table->set_template($tmpl);
    $this->table->set_heading('', 'Name');
    $count=1;
    
    $currentDate = date('Y-m-d');
    foreach ($patients as $value => $key)
    {
        if(strtolower($key[0]['status']) == "active")
        {
            $newdate = strtotime('-7 day', strtotime($key[0]['end']));
            $newdate = date('Y-m-d', $newdate);
            $expires = (strtotime($key[0]['end']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24);

            if($expires > 1)
            {
                $day = " days";
            }
            else
            {
                $day = " day";
            }

            if($expires < 0)
            {
                $id = $key[0]['id'];
                $field = 'status';
                $data = "EXPIRED";
                $key['status'] = status_update('patient',$field,$data,$id);
            }

            if($newdate <= $currentDate)
            { // WARNING
                $color = 'orange';
                $key[0]['status'] = $key[0]['status']." - will expire in ".$expires.$day.".";
            }
            else
            { // ACTIVE
                $color = 'black';
            }
        }
        elseif (strtolower($key[0]['status']) == "expired" || strtolower($key[0]['status']) == "deleted")
        { // EXPIRED/DELETED
            $color = 'red';
        }
        else
        { //ON HOLD OR LACK OF INFO
            $color = 'green';
        }

        // Build the custom actions links.
        //var_dump($key);
        // Build the custom actions links.
        // $actions = anchor(base_url()."records/compins/deleteMember/".$key[0]['id']."/".$id."/", "Delete");
        $selMulti = form_checkbox(array('name'=>'selMulti[]','id'=>'selMulti','class'=>'selMulti','value'=>$key[0]['id']));
        // Adding a new table row.
        $this->table->add_row("<font color=".$color.">".$count++.".".$selMulti, anchor(base_url()."records/members/view/".$key[0]['id']."/", "<font color=".$color.">".$key[0]['lastname'].", ".$key[0]['firstname']." ".$key[0]['middlename'], array('target'=>'_blank')));

        $this->table->add_row(
                                $count++,
                                anchor(base_url()."records/members/view/".$key[0]['id']."/",
                                "<font color=".$color.">".$key[0]['lastname'].", ".$key[0]['firstname']." ".$key[0]['middlename'],
                                array('target'=>'_blank')));
    }
    echo $this->table->generate();
    
    echo form_hidden('compins_id', $compins_id);
    echo form_hidden('location', 'records/members');
    if(isset($links))
    {
        $page = "<b>Page results: </b>". @$links;
    }
    echo '</div>';
    echo $page;
}
?>