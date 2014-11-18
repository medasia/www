<script>
$(document).ready(function() {
	$('.editable').editable('<?=base_url()?>utils/ajaxeditinplace', {
		indicator : 'Saving...',
		cancel    : 'Cancel',
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		onblur    : 'cancel',
		submitdata : {table: 'hospital_account', key: <?=$id?>}
	});
});
</script>
<h2>View Records of <?php echo $account_name?></h2>
<?php
$tmpl = array (
				'table_open'          => '<table border="1" cellpadding="4" cellspacing="0">',
				'table_close'         => '</table>'
				);
$this->table->set_template($tmpl);
$back = anchor(base_url()."records/hospaccnt/", "Back");

$this->table->add_row('Account Name', '<div class="editable" id="account_name">'.$account_name.'</div>');
$this->table->add_row('Vendor Account', '<div class="editable" id="vendor_account">'.$vendor_account.'</div>');
$this->table->add_row('Type', '<div class="editable" id="type">'.$type.'</div>');
$this->table->add_row('Terms', '<div class="editable" id="terms">'.$terms.'</div>');
$this->table->add_row('Vat', '<div class="editable" id="vat">'.$vat.'</div>');
$this->table->add_row('Days', '<div class="editable" id="days">'.$days.'</div>');
$this->table->add_row('Clinic/Hospital', '<div class="editable" id="clinic_hospital">'.$clinic_hospital.'</div>');
$this->table->add_row(anchor(base_url()."records/hospaccnt/delete/".$id."/", "Delete Hospital Account"),($back));

echo $this->table->generate(); 
?>