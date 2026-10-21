<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" style="padding: 0 0 100px 0">

		<div style="margin-bottom: 15px;">
			<a href="<?= base_url('admin/inventory') ?>">لیست انبارها</a>
			<span> / آمار موجودی انبارها</span>
		</div>

		<?php if (empty($stats)): ?>
			<p class="text-muted">هیچ انباری ثبت نشده است.</p>
		<?php else: ?>
			<table class="table table-bordered table-striped">
				<thead>
				<tr>
					<th>نام انبار</th>
					<th width="15%">وضعیت</th>
					<th width="18%">تعداد کالای متمایز</th>
					<th width="18%">مجموع موجودی</th>
					<th width="12%">جزئیات</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($stats as $inv): ?>
					<tr>
						<td><?= htmlspecialchars($inv->name) ?></td>
						<td>
							<?= $inv->isActive
								? '<span class="label label-success">فعال</span>'
								: '<span class="label label-default">غیرفعال</span>'; ?>
						</td>
						<td><?= number_format($inv->product_count) ?></td>
						<td>
							<?php
							$qty_style = $inv->total_qty <= 0 ? 'color:red;font-weight:bold' : '';
							?>
							<span style="<?= $qty_style ?>"><?= number_format($inv->total_qty) ?></span>
						</td>
						<td>
							<a href="<?= base_url('admin/inventory_stock/' . $inv->id) ?>">
								<button class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> مشاهده</button>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr style="font-weight:bold;">
					<td colspan="2">جمع کل</td>
					<td><?= number_format(array_sum(array_column($stats, 'product_count'))) ?></td>
					<td><?= number_format(array_sum(array_column($stats, 'total_qty'))) ?></td>
					<td></td>
				</tr>
				</tfoot>
			</table>
		<?php endif; ?>

	</div>

<?php } ?>
