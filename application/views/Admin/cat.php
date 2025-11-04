<div class="container" style="width:900px;">
	<h2 align="center">How to Add New Node in Dynamic Treeview using PHP Mysql Ajax</h2>
	<br /><br />
	<div class="row">
		<div class="col-md-6">
			<h3 align="center"><u>Add New Category</u></h3>
			<br />
			<form method="post" id="treeview_form">
				<div class="form-group">
					<label>Select Parent Category</label>
					<select name="parent_category" id="parent_category" class="form-control">

					</select>
				</div>
				<div class="form-group">
					<label>Enter Category Name</label>
					<input type="text" name="category_name" id="category_name" class="form-control">
				</div>
				<div class="form-group">
					<input type="submit" name="action" id="action" value="Add" class="btn btn-info" />
				</div>
			</form>
		</div>
		<div class="col-md-6">
			<h3 align="center"><u>Category Tree</u></h3>
			<br />
			<div id="treeview"></div>
		</div>
	</div>
</div>


<script>
	$(document).ready(function(){

		fill_parent_category();

		fill_treeview();

		function fill_treeview()
		{
			$.ajax({
				url:"fetch.php",
				dataType:"json",
				success:function(data){
					$('#treeview').treeview({
						data:data
					});
				}
			})
		}

		function fill_parent_category()
		{
			$.ajax({
				url:'fill_parent_category.php',
				success:function(data){
					$('#parent_category').html(data);
				}
			});

		}

		$('#treeview_form').on('submit', function(event){
			event.preventDefault();
			$.ajax({
				url:"add.php",
				method:"POST",
				data:$(this).serialize(),
				success:function(data){
					fill_treeview();
					fill_parent_category();
					$('#treeview_form')[0].reset();
					alert(data);
				}
			})
		});
	});
</script>
