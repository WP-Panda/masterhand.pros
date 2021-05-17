jQuery(document).ready(function ($) {

	"use strict"

	var form = $('form.mailster-form').eq(0);

	form.on('submit', function () {
		return false;
	});

});