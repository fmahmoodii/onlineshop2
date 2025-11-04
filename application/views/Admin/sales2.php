<?php if($this->session->userdata('id')){ ?>

<div class="container-fluid" id="content" style="margin: 40px 0 60px 0">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="padding: 20px;">
            <table class="table table-bordered table-striped text-center">
                <thead>
                <tr>
                    <th>کد پیگیری:</th>
                    <th>جمع پرداختی:</th>
                    <th>وضعیت:</th>
                    <th style="text-align: center">جزئیات:</th>
                </tr>
                </thead>
                <tbody>
				</tbody>
				<?php foreach ($shopping_cart_order as $ord){ foreach ($info_fact as $info){ foreach ($profile as $prof){ if ($info->user_id == $prof->user_id){if ($info->ord_code==$ord->order_code){ ?>
					<tr id="row_<?php echo $ord->id?>">
						<input id="id1" type="hidden" value="<?php echo $info->id?>">
						<input id="id2" type="hidden" value="<?php echo $ord->id?>">
						<td id="order_code"><?php echo $ord->order_code?></td>
						<td><?php echo number_format($ord->payment)?></td>
						<td><?php echo $ord->status?></td>
						<td>
							<a><button id="delete" class="btn btn-danger">حذف X</button></a>
							<a href="<?php echo base_url('admin/user_detail/').$prof->id?>"><button class="btn btn-warning">اطلاعات کاربر</button> </a>
							<a href="<?php echo base_url('admin/sale_datail/'.$info->id.'/'.$ord->order_code)?>"><button class="btn btn-info">جزئیات سفارش</button></a>
						</td>
					</tr>

				<?php }}}}}?>
                </tbody>
            </table>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<?php }?>

<script>
	$(document).on('click', '#delete', function(e){
		id=$('#id').val();
		id2=$('#id2').val();
		order_code=$('#order_code').val();
		var m = confirm('آیا از حذف این ردیف اطمینان دارید؟')
		if (m == true) {
			$.post('<?php echo base_url();?>admin/delete_sale',{'id':id,'order_code':order_code,},
					function (data) {
						if (data==1){
							$("#row_"+id2).hide();
							alert('حذف با موفقیت انجام گردید')
						}
					}
			);
		}
		if (m == false){
			return
		}

	});

</script>
