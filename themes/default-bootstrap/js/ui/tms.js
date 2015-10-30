$(document).ready(function(){
	$('.dropcms').hide();
	$('.clipcms').hide();
	$('.explodecms').hide();
	$('.clipmagazine').hide();
	$('.notafinal').hide();
	$('.droptitle').hide();
	$('.clipfinca').hide();
	$('.notafinca').hide();
	$('.textodistribuidores').hide();
	
	$('.textodistribuidores').show("slide", 1000).delay(800);

	$(window).scroll(function(){
		if ($(this).scrollTop() > 700) {
			$('.clipcms').show("clip", 1000);
		}
	
		if ($(this).scrollTop() > 900) {
			$('.dropcms').show("drop", 1000);
		}
		
		if ($(this).scrollTop() > 1100) {
			$('.explodecms').show("explode", 1000);
		}
		
		if ($(this).scrollTop() > 200) {
			$('.clipfinca').show("slide", 1000);
		}
		
		if ($(this).scrollTop() > 500) {
			$('.notafinca').show("clip", 1000);
		}
		
		if ($(this).scrollTop() > 300) {
			$('.clipmagazine').show("clip", 1000);
		}
		
		if ($(this).scrollTop() > 500) {
			$('.notafinal').show("clip", 1000);
		}
		
		if ($(this).scrollTop() > 700) {
			$('.droptitle').show("drop", 1000);
		}
	});
});