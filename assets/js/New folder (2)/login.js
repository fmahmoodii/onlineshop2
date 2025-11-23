$(document).ready(function () {
	$("#register2").hide();
	$("#forget").hide();
	$('#btn1').hide();
});
	$('#password').keyup(function () {

		var password= $('#password').val();

		if (password.length>=6){
			$('#error_1').hide();
			$('#accept_1').show();
		}else {
			$('#error_1').show();
			$('#accept_1').hide();
		}
	});
	$('#re_password').keyup(function () {

		var password= $('#password').val();
		var re_password= $('#re_password').val();
		if (password.length>=6){
		if(re_password==password){

			$('#error_2').hide();
			$('#accept_2').show();

			$('#btn_accept_reg').show();

		}else {

			$('#error_2').show();
			$('#accept_2').hide();

			$('#btn_accept_reg').hide();
		}}

	});

    $('#register_btn').click(function(){
    	$('#login').hide();
    	$("#register2").show();
        $("#forget").hide();
    });
    $('#login_btn').click(function(){
        $('#login').show();
        $("#register2").hide();
        $("#forget").hide();
    });
    $('#forget-pass').click(function(){
        $('#login').hide();
        $("#register2").hide();
        $("#forget").show();
    });



	$("#btn").click(function () {
		alert("متن درون تصویر را صحیح وارد کنید");
	});

	$('#insert_capcha').keyup(function(event) {
		var text = $("#insert_capcha").val();
		var pass = $("#capcha").text();

		if(text==pass){
			$("#btn1").show();
			$("#btn").hide();
		}else {
			$("#btn").show();
			$("#btn1").hide();
		}
	});
	$('#refresh').mouseover(function () {
		$(this).css('color','#2c9ed4');
		$(this).css('font-size','25px');
		$(this).css('transform','rotate(5deg)');
		$(this).mouseleave(function () {
			$(this).css('color','#636262');
			$(this).css('font-size','20px');
			$(this).css('transform','rotate(-5deg)');

		});
	});
	$(document).on('click', '#refresh', function(e){
		var r=Math.random().toString(36).slice(7);
		$("#capcha").text(r);
		var myArray = ['#4287f5' ,'#9ec3ff','#f2abff','#b8ffbf','#ffd4b8','#ffada8','#f59ff3','#ff4fa4','#e0e0ff','#defcff'];
		var rand = myArray[Math.floor(Math.random() * myArray.length)];
		document.getElementById("cap_div").style.backgroundColor = rand;

		var element = $("#cap_div"); // global variable
		var getCanvas; // global variable
		html2canvas(element, {
			onrendered: function (canvas) {
				$("#cap_div").hide();
				getCanvas = canvas;
			}
		});
	});
	//-------------------
	var r=Math.random().toString(36).slice(7);
	$("#capcha").text(r);
	var myArray = ['#4287f5' ,'#9ec3ff','#f2abff','#b8ffbf','#ffd4b8','#ffada8','#f59ff3','#ff4fa4','#e0e0ff','#defcff'];
	var rand = myArray[Math.floor(Math.random() * myArray.length)];
	document.getElementById("cap_div").style.backgroundColor = rand;

	var element = $("#cap_div"); // global variable
	var getCanvas; // global variable
	html2canvas(element, {
		onrendered: function (canvas) {
			$("#cap_div").hide();
			getCanvas = canvas;
		}
	});


