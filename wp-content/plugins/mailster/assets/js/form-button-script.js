(function () {

	"use strict"

	var mailster = 'mailster',
		btn = document.getElementsByClassName('btn')[0],
		widget = document.getElementsByClassName('btn-widget')[0],
		button_id, origin, form_id, loc;

	btn.onclick = function () {
		widget.className = widget.className.replace('btn-widget', 'btn-widget init');
		loc = location.href.replace(/&button=\d+/, '&iframe=1');
		window.parent.postMessage([mailster, 's', loc, form_id, button_id].join('|'), origin);

	};
	window.onload = function () {
		window.parent.postMessage([mailster, 'd', widget.offsetWidth, widget.offsetHeight, button_id].join('|'), origin);
	};
	window.onmessage = function (event) {
		if (event.data == 'l') widget.className = widget.className.replace('btn-widget init', 'btn-widget');
	};

	button_id = location.search.match(/button=(\d+)/)[1] || 0;
	origin = decodeURIComponent(location.search.match(/origin=(.+)&/)[1]);
	form_id = location.search.match(/id=(\d+)/)[1] || 1;

})();