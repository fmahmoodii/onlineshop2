<div class="container-fluid">
	<div class="row" style="margin-top: 100px">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<table class="table table-responsive table-striped table-bordered">
				<thead>
				<tr>
					<td>

					</td>
					<td>جزئیات</td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>
						<?php if (isset($merchant[0])){ foreach ($merchant as $mer){ ?>
							<?php echo $mer->value;?>
						<?php }}?>
					</td>
					<td>
					<?php if (isset($merchant[0])){ foreach ($merchant as $mer){ ?>
					<input  id="id" type="hidden" value="<?php  echo $mer->id;?>">
					<button data-toggle="modal" data-target="#edit-modal" class="btn btn-success">ویرایش</button>
					<?php }}?>
					<button id="delete" class="btn btn-danger">حذف</button>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="col-md-3"></div>
	</div>
</div>
<div class="modal fade" id="edit-modal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button class="close" data-dismiss="modal" style="float: right">
					<i class="fa fa-close"></i>
				</button>
				<h4>ویرایش</h4>
			</div>
			<div class="modal-body text-center">
                <input type="text" class="form-control" value="
<?php if (isset($merchant[0])){ foreach ($merchant as $mer){ echo $mer->value;}}?>">
			</div>
			<div class="modal-footer">
				<button id="edit" class="btn btn-success" data-dismiss="modal">ذخیره</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).on('click', '#edit', function(e){
		var id=$("#id").val();
		var value=("#value").val();
		$.post('<?php echo base_url();?>admin/edit_merchant',{'id':id,'value':value},
				function (data) {
					if (data==1){
						$("#text").text(text);
						window.location.href='<?php echo base_url()?>admin/merchantid'
					}
				}
		);
	});
</script>
