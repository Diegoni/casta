/*jslint undef: false, browser: true, devel: false, eqeqeq: false, bitwise: false, white: false, plusplus: false, regexp: false, nomen: false */ 
/*global jQuery,setTimeout,location,setInterval,YT,clearInterval,clearTimeout,pixelentity,$ */

jQuery(document).ready(function () {
	
	var items = $(".item");
	var shiners = [];
	var idx = 0;
	var max = items.length;
	var active = 0;
	
	
	function getShiner() {
		var jthis = $(this);
		jthis.attr("id",idx++);
		shiners.push(jthis.find(".peShiner").peShiner({api:true, paused:true, reverse:true,repeat:1}));
	}
	
	function evHandler(e) {
		var old = active;
		idx = (idx + (e.currentTarget.id == "next" ? +1 : -1)) % max;
		active = idx >= 0 ? idx : idx + max;
		items.removeClass("selected").eq(active).addClass("selected");
		shiners[old].pause();
		shiners[active].resume();
	}
	
	
	$(".next, .prev").click(evHandler);
	
	items.each(getShiner);
	
	idx = active = parseInt(items.filter(".selected").attr("id"),10);
	shiners[idx].resume();
	
});