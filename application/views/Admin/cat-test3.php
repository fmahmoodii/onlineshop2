
<div class="container-fluid" id="content" style="padding: 20px 0 70px 0">

	<div class="">
		<div class="col-md-1"></div>
		<div style="" class="col-md-10 cats">
			<div style="position: sticky;z-index: 999;background-color: #5478f6;width: 100%;height: 90px;top: 0px;padding: 30px 20px;">
				<!--		<input id="search" type="text" onclick="search(document.getElementById('search').value)">-->
				<!--		<input value="جستجو" type="button" onclick="search(document.getElementById('search').value)"><br><br>-->
				<input id="search" type="text" >
				<input id="srch" value="جستجو" type="button">
				<input id="nxt" value="بعدی" type="button">
				<br><br>
			</div>

			<?php echo $cat;?>

		</div>
		<div class="col-md-1"></div>
	</div>

	<div class="modal fade" id="show_insert" role="dialog">
		<div class="modal-dialog modal-md" >
			<!-- Modal content-->
			<div class="modal-content " style="background-color: #fff">
				<div class="modal-header text-center" >
					<button id="close" style="float: right;" type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">افزودن زیرمجموعه</h4>
				</div>
				<div class="modal-body">
					<div>
						<input autocomplete="off" type="hidden" id="id_modal">
						<label for="name_modal">نام دسته</label>
						<input autocomplete="off" id="name_modal" type="text" class="form-control"><br>
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="btn_insert" class="btn btn-success" data-dismiss="modal" >ثبت</button>
					<button style="float: right;" id="cancel_ins" class="btn btn-danger" data-dismiss="modal" >لغو</button>

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="show_edit" role="dialog">
		<div class="modal-dialog modal-md">
			<!-- Modal content-->
			<div class="modal-content" style="background-color: #fff">
				<div class="modal-header text-center">
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i
								class="fa fa-close"></i></button>
					<h4 class="modal-title">ویرایش</h4>
				</div>
				<div class="modal-body">
					<div>
						<input autocomplete="off" type="hidden" id="id_modal">
						<label class="required">نام دسته:</label><br>
						<input required autocomplete="off" id="name_modal_edit" type="text" class="form-control" placeholder="نام دسته">
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="btn_edit" class="btn btn-success" data-dismiss="modal">ثبت
						ویرایش
					</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>

				</div>
			</div>
		</div>
	</div>

	<script>


		$(document).ready(function(){

			// load_data();
			var offset=[];
			var offset2;
			var t=1;
			var first;
			var first2;
			var h;
			function load_data(query)
			{
				$.ajax({
					url:"<?php echo base_url(); ?>admin/search",
					method:"POST",
					data:{query:query},
					success:function(data){
						var obj = JSON.parse(data);
						offset2 = '';
						console.log(obj);
						h=0;
						$('.cats span').each(function(){

							$(this).css({'background-color':'white'});
							for(var i=0; i<obj.length;i++) {
								console.log(obj.length);

								var x = $(this).attr('id_cat');
								if (obj[i] == x && $('#search').val()!='') {
									h++;
									console.log(h);
									offset[h] = $(this).offset();
									console.log($(this).offset());
									if (h === 1) {
										offset2 = $(this).offset();
										// console.log($(this).offset());
										first = offset2.top;
									}

									$(this).css({'background-color':'red'});
									$('.box2,.box3,.box4').show();
									$('.plus,.plus2,.plus3').hide();
									$('.minus,.minus2,.minus3').show();


								}

							}
						});

						// console.log(offset2.top);
						// window.scrollTo(offset2.top,offset2.left);
					}
				});
			}

			$(document).on('click', '#srch', function(e) {
				$('html, body, #content').animate({
					scrollTop: first-100
				}, 700);
				$('html, body, #content').animate({
					scrollTop: first-120
				}, 700);
			});


			$(document).on('click', '#nxt', function(e) {
				console.log(h);
				console.log(offset[t]);


				if(t==h+1){
					first2 = offset2.top;
					$('html, body, #content').animate({
						scrollTop: first-120
					}, 700);
					t=1;
				}else {
					$('html, body, #content').animate({
						scrollTop: offset[t].top-120
					}, 700);

					first2 = offset[t].top;
					t++;

					return;

				}


			});

			$('#search').keyup(function(){
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

		var parentId;
		// var level;
		var n;
		var boxId;
		var mar;
		// var parent_div;
		$(document).on('click','#add',function(e) {
			var mar1 = $(this).parents().eq(1).css('margin-right');
			// mar = parseInt(mar1,10);
			mar = parseInt(mar1)+20;
			console.log(mar);
			// level = $(this).attr('level');
			// var m = parseInt(level)+1;
			// n = 'box'+m;
			// n = 'box';
			// boxId = n+'box_'+parentId;
			// find 3th parent:
			// parent_div = $(this).parents().eq(2);
			parentId = $(this).attr('id_cat');
			boxId = 'box_'+parentId;
			$("#show_insert").modal();
			console.log(boxId);
		});

		$(document).on('click','#btn_insert',function(e) {
			var name_cat = $('#name_modal').val();
			// var parent = $('#parent').attr('parentId');
			if(name_cat != "") {
				//$.post('<?php //echo base_url();?>//admin/insert_cat', {'name_cat': name_cat,'parentId': parentId,'level': parseInt(level)+1,},
				$.post('<?php echo base_url();?>admin/insert_cat', {'name_cat': name_cat,'parentId': parentId,'mar': mar,},
					function (data) {
						console.log(boxId);
						console.log($('#'+boxId).append(data));
						// $('#'+boxId).show();
						// // $('#'+boxId).css({'margin-right':mar+25});
						// var div = document.getElementById('divID');

					}
				);
			}else{
				alert("برای دسته جدید یک نام وارد کنید");
			}
		});

		$(document).on('click','#cancel_ins',function(e) {
			$("#name_modal").val('');
		});

		$(document).on('click','#close',function(e) {
			$("#name_modal").val('');
		});

		$(document).on('click', '#delete', function(e){
			var id_cat = $(this).attr('id_cat');
			var row = $(this).parents().eq(1);
			var m = confirm('آیا از حذف دسته و زیر دسته ها مطمئن هستید؟');
			if (m == true) {
				$.post('<?php echo base_url();?>admin/delete_category', {'id': id_cat,},
					function (data) {
						//window.location.href="<?php //echo base_url(); ?>//admin/category_test";
						row.hide();
					});
			}
			if (m == false){
				return
			}
		});


		$(document).on('click', '.plus', function(e) {
			var id = $(this).attr('id_cat');
			var arr;
			$.post('<?php echo base_url();?>admin/cat_minus/'+id, {'id': id,},
				function (data) {
					// var obj=JSON.stringify(data);
					console.log(data.split(','));
					arr = data.split(',');
					console.log(arr.length);
					for(let i=0;i<arr.length;i++){
						$('#box_'+arr[i]).show();
						$('#minus_'+id).show();
						$('#plus_'+id).hide();
					}

				});
			// $(this).parent().next().find('.box4,.box3').hide();

		});



		$(document).on('click', '.minus', function(e) {
			var id = $(this).attr('id_cat');
			var arr;
			$.post('<?php echo base_url();?>admin/cat_minus/'+id, {'id': id,},
				function (data) {
				// var obj=JSON.stringify(data);
					console.log(data.split(','));
					arr = data.split(',');
					console.log(arr.length);
					for(let i=0;i<arr.length;i++){
						$('#box_'+arr[i]).hide();
						$('#minus_'+id).hide();
						$('#plus_'+id).show();
					}

				});

		});


		// function search(string){
		// 	window.find(string);
		// }

		//------نمایش مودال---------
		$(document).on('click', '#edit', function (e) {
			var id = $(this).attr("id_cat");
			var name = $("#name_" + id).text();
			$("#id_modal").val(id);
			$("#name_modal").val(name);
			$("#show_edit").modal();
		});
		//---------عملیات ویرایش-----------
		$(document).on('click', '#btn_edit', function (e) {
			var id = $("#id_modal").val();
			var name = $("#name_modal").val();


			$.post('<?php echo base_url()?>admin/edit_cat', {
					'id': id,
					'name_cat': name,
				},
				function (data) {
					if (data == 1) {
						//alert('ویرایش با موفقیت انجام شد')
						$('#category1_data').DataTable().ajax.reload( null, false );

					}
				});
		});



	</script>
