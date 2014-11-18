<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>View Records of <?php echo $name?></title>
        <link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
    
<script>
$(document).ready(function() {
    $('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
        indicator : 'Saving...',
        cancel    : 'Cancel',
        submit    : 'OK',
        tooltip   : 'Click to edit...',
        onblur    : 'cancel',
        submitdata : {table: 'brokers', key: <?=$id?>}
    });
});
</script>
</head>
<h2>View Records of <?php echo $name?></h2>
<?php
$tmpl = array (
                'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',
                'table_close'         => '</table>'
                );
$this->table->set_template($tmpl);
$back = anchor(base_url()."records/company/", "Back");
$this->table->add_row('Name', '<div class="editable" id="name">'.$name.'</div>');
$this->table->add_row('Address', '<div class="editable" id="address">'.$address.'</div>');
$this->table->add_row('Address', '<div class="editable" id="contact_person">'.$contact_person.'</div>');
$this->table->add_row('Address', '<div class="editable" id="contact_no">'.$contact_no.'</div>');
$this->table->add_row(anchor(base_url()."records/accounts/delete/".$id."/", "Delete Broker", array('onClick'=>"return confirm('Are you sure you want to delete this record?')",'class'=>'btn btn-danger')),'');

echo $this->table->generate();
?>