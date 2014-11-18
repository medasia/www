<script>
$(document).ready(function() {
	var search_by = $('#search_by :selected').val();
	var start_keyword = "";
	$('#keyword').attr("placeholder", "Insurance Name");
	$('#search_by').change(function() {
		search_by = $(this).val();
		$('#keyword').attr("placeholder", $('#search_by :selected').text()+" Name").val("");
		$('#results').empty();
    });
	$('#keyword').autocomplete({
		minLength: 1,
		source: function(req, add){
			$.ajax({
				url: '<?=base_url()?>utils/autocomplete/from/'+search_by, //Controller where search is performed
				dataType: 'html',
				type: 'POST',
				data: req,
				success: function(data) {
					$('#results').html(data);
				}
			});
		}
	});

	function load() {
        $.ajax({ //create an ajax request to load_page.php
            type: "POST",
            url: '<?=base_url()?>utils/autoload_data/from/'+search_by, //Controller where search is performed
            dataType: "html", //expect html to be returned                
            success: function (response) {
                $('#results').html(response);
                alert(response);
            }
        });
    }
    load(); //if you don't want the click
});
</script>

<h2>Search</h2>
<?php echo validation_errors(); ?>
	<?php
		$this->table->add_row(form_label('Search : '),form_dropdown('search', array('insurance' => 'Insurance','brokers' => 'Brokers','company' => 'Company'),'insurance','id="search_by"'), form_input(array('name'=>'keyword', 'id'=>'keyword', 'size'=>'50','class'=>'form-control')));
		$template = array(
					'table_open'	=> '<table border="0" cellpadding="4" cellspacing="0">',
					'table_close'	=> '</table>'
					);
		$this->table->set_template($template); 
		echo $this->table->generate(); 
	?>
<?php echo form_close(); ?>
<div id="results"></div>