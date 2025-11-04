<?php if($this->session->userdata('id')){ ?>

<div class="container" id="content" style="padding: 50px 0 50px 0">
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon">جستجو</span>
			<input autocomplete="off" type="text" name="search_text" id="search_text" placeholder="جستجو.." class="form-control">
		</div>
	</div>
	<br>
	<br>
	<div id="result"></div>
</div>
<br>
<br>

	<div class="modal fade" id="show_edit" role="dialog">
		<div class="modal-dialog modal-md" >
			<!-- Modal content-->
			<div class="modal-content " style="background-color: #fff">
				<div class="modal-header text-center" >
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">ویرایش نظرات</h4>
				</div>
				<div class="modal-body">
					<div>
						<input autocomplete="off" type="hidden" id="id_modal">
						<input autocomplete="off" id="name_modal" type="text" class="form-control"><br>
						<!--                    <textarea class="ckeditor" rows="30" cols="70" type="text" name="text_modal"></textarea><br><br>-->
						<textarea class="form-control" id="text_modal" cols="75" rows="10" style="padding: 10px"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="btn_edit" class="btn btn-success" data-dismiss="modal" >ثبت ویرایش</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal" >لغو</button>

				</div>
			</div>
		</div>
	</div>
<?php } ?>

	<script>
		$(document).ready(function(){

			load_data();

			function load_data(query)
			{
				$.ajax({
					url:"<?php echo base_url(); ?>admin/comments_list",
					method:"POST",
					data:{query:query},
					success:function(data){
						$('#result').html(data);
					}
				})
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
		});

		$(document).on('click', '#accept', function(e){
			id=$(this).attr("id_cmnt");
			var m = confirm('آیا از تایید این کامنت اطمینان دارید؟')
			if (m == true) {
				$.post('<?php echo base_url();?>admin/accept_comment',{'id':id,},
					function (data) {
						if (data==1){
							window.location.reload();
						}
					}
				);
			}

		});
		$(document).on('click', '#reject', function(e){
			id=$(this).attr("id_cmnt");
			var m = confirm('آیا از رد این کامنت اطمینان دارید؟')
			if (m == true) {
				$.post('<?php echo base_url();?>admin/reject_comment',{'id':id,},
					function (data) {
						if (data==1){
							window.location.reload();
						}
					}
				);
			}

		});
		$(document).on('click', '#delete', function(e){
			id=$(this).attr("id_cmnt");
			var m = confirm('آیا از حذف این کامنت اطمینان دارید؟')
			if (m == true) {
				$.post('<?php echo base_url();?>admin/delete_comment',{'id':id,},
					function (data) {
						if (data==1){
							$("#cmnt_"+id).hide();
							alert('حذف با موفقیت انجام گردید')
						}
					}
				);
			}
			else if (m == false){
				return
			}


		});

		//------نمایش مودال---------
		$(document).on('click', '#edit_1', function(e){
			id=$(this).attr("id_cmnt");
			var name=$("#name_"+id).text();
			var text=$("#text_"+id).text();
			$("#id_modal").val(id);
			$("#name_modal").val(name);
			$("#text_modal").val(text);
			$("#show_edit").modal();
		});
		//---------عملیات ویرایش-----------
		$(document).on('click', '#btn_edit', function(e){
			var id=$("#id_modal").val();
			var name=$("#name_modal").val();
			var text=$("#text_modal").val();

			$.post('<?php echo base_url();?>admin/edit_cm',{'id':id,'name':name , 'text':text},
				function (data) {
					if (data==1){
						$("#name_"+id).text(name);
						$("#text_"+id).text(text);
						alert('ویرایش با موفقیت انجام گردید')
					}
				}
			);
		});

	</script>

