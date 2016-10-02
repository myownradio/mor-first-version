var items, title;

$(document).ready(function(){
	title = document.title;
	var items = -1;
	getLOG(-1);
});

function getLOG(pos) {
	$.post(window.location.href, { f : pos }, function(data) {
		var data = $.parseJSON(data);
		if(data['status'] == 'OK') {
			var newpos = data['size'];
			$(".data-log").append(atob(data['data']));
			$(window).scrollTop($(document).height());
			getLOG(newpos);
			items++;
			document.title = (items>0?"("+items+") ":"") + title;
		} else {
			getLOG(pos);
		}
	}).fail(function(){
		setTimeout(function(){ getLOG(pos); }, 5000);
	});
}