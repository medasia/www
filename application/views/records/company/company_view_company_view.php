<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Records of <?php echo $name?></title>
		<link href="<?php echo base_url();?>bootstrap/css/bootstrap.css" rel="stylesheet">
	
		<script>
		$(document).ready(function() {
			$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
				indicator : 'Saving...',
				cancel    : 'Cancel',
				submit    : 'OK',
				tooltip   : 'Click to edit...',
				onblur    : 'cancel',
				submitdata : {table: 'company', key: <?=$id?>}
			});
		});
		</script>
	</head>
	<body class="main-bg">

		<div class="profile_container">
			<div class="profile_name">Records of <?php echo $name?></div>
			<?php
				$tmpl = array (
								'table_open'          => '<table cellpadding="4" cellspacing="0" class="profile_table">',
								'table_close'         => '</table>'
								);
				$this->table->set_template($tmpl);
				$back = anchor(base_url()."records/company/", "Back");
				$this->table->add_row('Company Name', '<div class="editable" id="name">'.$name.'</div>');
				$this->table->add_row('Code', '<div class="editable" id="code">'.$code.'</div>');
				$this->table->add_row(anchor(base_url()."records/company/delete/".$id."/", "Delete Company", array('onClick'=>"return confirm('Are you sure you want to delete this record?')",'class'=>'btn btn-danger')),'');

				echo $this->table->generate();
			?>
		</div>
	</body>
</html>