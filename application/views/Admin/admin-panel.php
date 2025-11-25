<?php include_once('layout/header.php'); ?>
<?php include_once('layout/sidebar.php'); ?>

<div class="container">
	<br/>
	<h2 class="text-center">Codeigniter 3 - Highcharts mysql json example</h2>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
<!--			--><?php //echo password_hash("admin", PASSWORD_BCRYPT); ?>

			<div class="panel panel-default">
				<div class="panel-heading">Dashboard - ItSolutionStuff.com</div>
				<div class="panel-body">
					<div id="container"></div>
				</div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">

	$(function () {

		var data_click = <?php echo $click; ?>;
		var data_viewer = <?php echo $viewer; ?>;

		$('#container').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: 'Yearly Website Ratio'
			},
			xAxis: {
				categories: []
			},
			yAxis: {
				title: {
					text: 'Rate'
				}
			},
			series: [{
				name: 'Click',
				data: data_click
			}, {
				name: 'View',
				data: data_viewer
			}]
		});
	});

</script>



