<div class="container-fluid">
	<div class="row" style="height: 130px">

	</div>
</div>

<!--rules-->
<div class="container-fluid" style="margin: 0 auto">
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6" style="border-radius:11px; padding: 30px;box-shadow: 5px 5px 5px #c9ccc7;
                border: 1px solid #ccc;">
			<h3 style="font-weight: bold">قوانین سایت: </h3><br><br>
			<span style="line-break: anywhere;">
				<?php foreach ($rules as $r){echo $r->text;}?>
			</span>

		</div>
		<div class="col-md-3"></div>
	</div>
</div>
