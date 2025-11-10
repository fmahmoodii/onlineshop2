$('#list a li').mouseover(function () {
	$(this).css({'background-color':'#a2a1a1'});
}).mouseleave(function () {
	$(this).css({'background-color':'#3d3939'});
});
$('#nav').show();
$('#1').hide();
$('#btn2').show();
$('#btn').hide();
	$(document).ready(function () {

	$('#content').css({'padding-right':'150px'});

});
	$('#btn').click(function(){
	$('#nav').show();
	$('#1').hide();
	$('#btn2').show();
	$('#btn').hide();
	$('#content').css({'padding-right':'150px'});

});
	$('#btn2').click(function(){
	$('#nav').hide();
	$('#1').show();
	$('#btn2').hide();
	$('#btn').show();
	$('#content').css({'padding-right':'0px'});


});


