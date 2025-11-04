<!--space-->
<div class="container-fluid" style=" height: 50px;"></div>

<!--footer-->
<div class="container-fluid" style="background-color: #5e5e5e;">
	<div class="row" style="padding: 3%;
    display: inline-flex
;">
		<div style="border-left: 1px solid #fff;
    width: 33%;
    padding-left: 2%;">
			<b style=" color: #e5adad;
    font-size: 18px;">درباره ما:</b><br>
			<a href="#">
				<p style="display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;

    color: #fff;"><?php foreach ($call_us as $call){echo $call->text;}?></p>
			</a>
			<br>
			<b style=" color: #e5adad">آدرس:</b><br>
			<span  style=" color: #fff;overflow-wrap: normal; text-align: justify"><?php foreach ($call_us as $call){echo $call->address;}?></span>
			<br><br>
			<b style="color: #e5adad">تلفن:</b><br>
			<span  style=" color: #fff"><?php foreach ($call_us as $call){echo $call->phone;}?></span>
			<br>
			<br>
			<div>
					<?php foreach ($socials as $s){?>
<!--						<a href="--><?php //echo $s->facebook;?><!--"><img src="--><?php //base_url()?><!--../assets/images/facebook.png" style="height: 28px; width: 28px"></a>-->
						<a href="<?php echo $s->facebook;?>"><img src="assets/images/facebook.png" style="height: 28px; width: 28px"></a>
						<a href="<?php echo $s->whatsapp;?>"><img src="assets/images/whatsapp.png" style="height: 28px; width: 28px"></a>
						<a href="<?php echo $s->telegram;?>"><img src="assets/images/telegram.png" style="height: 28px; width: 28px"></a>
						<a href="<?php echo $s->instagram;?>"><img src="assets/images/instagram.png" style="height: 28px; width: 28px"></a>
					<?php }?>
				</div>
		</div>

		<div style="
    border-left: 1px solid #fff;
    width: 33%;
    padding-right: 2%;
    padding-left: 0;">
			<b style="font-size: 18px;font-weight: bold; color: #fff">لینک های پر بازدید:</b>
			<br><br>
			<table>
				<tr>
				<td style="width: 50%;"><?php foreach ($links as $l){?>
						<a href="<?php echo base_url()?>home/rules" style="font-size: 16px;color: #e5adad;">قوانین سایت</a><br>
						<a href="<?php echo base_url()?>home/call_us" style="font-size: 16px;color: #e5adad;">ارتباط با ما</a><br>
						<a href="<?php echo site_url($l->link1)?>" style="font-size: 16px;color: #e5adad;">لینک 1</a><br>
						<a href="<?php echo site_url($l->link2)?>" style="font-size: 16px;color: #e5adad;">لینک 2</a><br>
						<a href="<?php echo site_url($l->link3)?>" style="font-size: 16px;color: #e5adad;">لینک 3</a><br>
					<?php }?>
				</td>
				<td style="width:24%;"><?php foreach ($links as $l){?>
						<a href="<?php echo site_url($l->link4)?>" style="font-size: 16px;color: #e5adad;">لینک 4</a><br>
						<a href="<?php echo site_url($l->link5)?>" style="font-size: 16px;color: #e5adad;">لینک 5</a><br>
						<a href="<?php echo site_url($l->link6)?>" style="font-size: 16px;color: #e5adad;">لینک 6</a><br>
						<a href="<?php echo site_url($l->link7)?>" style="font-size: 16px;color: #e5adad;">لینک 7</a><br>
						<a href="<?php echo site_url($l->link8)?>" style="font-size: 16px;color: #e5adad;">لینک 8</a><br>
					<?php }?>
				</td>
			</tr>
			</table>
		</div>

		<div style="/* height: 300px; *//* margin: 45px 0 0 0; */width: 33%;padding-right: 2%;padding-left: 0;">
			<span style="font-size: 18px;font-weight: bold;color: #fff">عضویت:</span>
			<br>
			<div>
				<input type="text" style="padding: 8px;border-radius: 25px;height: 12%;outline: unset;border: 1px solid #ccc;box-shadow: 0 0 3px #ccc;width: 80%;margin: 13px 0 0 0;" placeholder="ایمیل">


				<i style="
    color: #d23030;
    font-size: 17px;
    line-height: 27px;
    text-align: center;
    background-color: white;
    height: 29px;
    width: 29px;
    border-radius: 50%;
    border: 1px solid;
    /* margin: 1px 0 0 0; */
    /* margin: -8.3% 87% 0 0; */
    position: relative;
    top: 2px;
    right: -37px;
    " class="fa fa-envelope" aria-hidden="true"></i>
			</div>
			<br>
			<button class="btn btn-default" style="
    padding: 8px;
    line-height: 1;
    font-weight: bold;
    font-size: 12px;
    border-radius: 25px;
    height: 29px;
    outline: unset;
    width: 88px;
    /* margin: 20px 0 0 0; */
    color: #ffffff;
    background-color: #d23030;
    ">عضویت</button>
		</div>

		</div>
	</div>
</div>
