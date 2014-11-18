<h2>Uploads History</h2>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('utils/fileuploader/upto/templates');?>
<?php
// $inputs = array(
// 				array(form_label('Upload Templates', 'templates'), form_upload(array('name'=>'file', 'id'=>'templates')), form_submit('upload', 'Upload'))
// 				);
// $template = array(
// 			'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
// 			'table_close'	=> '</table>'
// 			);
// $this->table->set_template($template); 
// echo $this->table->generate($inputs);
?>

<?php
echo '<div class="table_scroll">';
$template = array(
			'table_open'	=> '<table border="1" cellpadding="4" cellspacing="0" class="table table-bordered table-hover">',
			'table_close'	=> '</table>'
			);
$this->table->set_heading(array('', 'Uploaded to', 'Uploader', 'Date Uploaded', 'Hash (sha1(md5(filename+timestamp)))', 'Client filename', 'DIR Path', 'Size (kB)', ''));
$this->table->set_template($template); 
echo $this->table->generate($files); 
echo '</div>';
?>