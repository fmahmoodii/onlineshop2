<div class="container" style="padding-top: 50px">
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon">جستجو</span>
			<input autocomplete="off" type="text" name="search_text" id="search_text" placeholder="جستجو.." class="form-control" />
		</div>
	</div>
	<br />
	<div id="result"></div>
</div>

<script>
	$(document).ready(function(){
		load_data();

		function load_data(query)
		{
			$.ajax({
				url:"<?php echo base_url(); ?>home/search_name",
				method:"POST",
				data:{query:query,},
				success:function(data){
					$('#result').html(data);
				}
			});
		}

		$('#search_text').keyup(function(){
			var search = $(this).val();
			if(search != '')
			{
				load_data(search);
			}
			else
			{
				load_data();
			}
		});
		//-----------------------------

	});
</script>
