(function () {

	"use strict"

	function _destroy() {
		_off(message, _postMessage);

		delete win[obj];
	}

	function _close(event) {

		if (!currentframe) return;

		currentframe.parentNode.removeChild(currentframe);
		_off(popstate, _close);
		if (event && event.type != popstate) history.back();

		open = false;

	}

	function _do_button(button, i) {

		var data = button.dataset,
			label = button.innerHTML,
			href = button.getAttribute('href'),
			iframe, src;

		data.button = i + 1;
		data.label = label;
		data.origin = _getOrigin(location.href);
		data.referer = location.href;

		src = href + '&' + _buildQuery(data);

		iframe = _getiframe('width:0px;height:24px;');

		iframe.src = src;

		button.innerHTML = '';
		button.parentNode.replaceChild(iframe, button);

		return {
			'element': button,
			'iframe': iframe
		}

	}

	function _postMessage(e) {

		if (typeof e.data != 'string') return;
		var req = e.data.split('|');
		if (req[0] != 'mailster') return;
		switch (req[1]) {
		case 'd':
			var iframe = buttons[req[4] - 1].iframe;
			iframe.style.width = req[2] + 'px';
			iframe.style.height = req[3] + 'px';
			break;
		case 's':
			if (open) break;
			var iframe = _getiframe('left:0px;top:0px;width:100%;height:100%;z-index:999999;position:fixed !important;', function () {
				buttons[req[4] - 1].iframe.contentWindow.postMessage('l', req[2]);
				_on(popstate, _close);
				history.pushState(is_null, is_null, location.href);
			});
			iframe.src = req[2];
			doc.getElementsByTagName('body')[0].appendChild(iframe);
			currentframe = iframe;
			open = true;
			break;
		case 'c':
			_close();
			break;
		}


	}

	function _getiframe(css, callback) {

		var el = doc.createElement("iframe");
		el[setAttribute]("frameBorder", "0");
		el[setAttribute]("allowtransparency", "true");
		el[setAttribute]("scrolling", "no");
		el.style.cssText = iframeCSS + css;
		el.onload = function () {
			el.style.visibility = "visible";
			callback && callback();
		};
		return el;

	}

	function _getOrigin(url) {
		var path = url.split('/');
		return path[0] + '//' + path[2];
	}

	function _buildQuery(obj, num_prefix, temp_key) {

		var output_string = [],
			o;

		Object.keys(obj).forEach(function (val) {

			var key = val;

			num_prefix && !isNaN(key) ? key = num_prefix + key : ''

			var key = encodeURIComponent(key.replace(/[!'()*]/g, escape));
			temp_key ? key = temp_key + '[' + key + ']' : ''

			if (typeof obj[val] === 'object') {
				var query = build_query(obj[val], is_null, key)
				output_string.push(query)

			} else {
				var value = encodeURIComponent(obj[val].replace(/[!'()*]/g, escape));
				if (value == 'true') value = '1';
				if (value == 'false') value = '0';
				(o = val.match(/^user([A-Z].*)/)) ?
				output_string.push('userdata[' + o[1].toLowerCase() + ']=' + value): output_string.push(key + '=' + value)
			}


		})

		return output_string.join('&');

	}

	function _on(type, listener, use_capture) {
		(win.addEventListener) ?
		win.addEventListener(type, listener, !!use_capture): win.attachEvent("on" + type, listener);
	}

	function _off(type, listener) {
		(win.removeEventListener) ?
		win.removeEventListener(type, listener): win.detachEvent("on" + type, listener);
	}


	var win = window,
		doc = document,
		obj = 'MailsterSubscribe',
		is_null = null,
		selector = '.mailster-subscribe-button',
		buttonsObj = doc.querySelectorAll(selector),
		currentframe,
		setAttribute = 'setAttribute',
		message = 'message',
		popstate = 'popstate',
		iframeCSS = "border:0;overflow:hidden;visibility:hidden;display:inline-block;margin:0px !important;padding:0px !important;background:transparent;",
		open = false,
		buttons = [],
		i;

	if (win[obj].loaded) return;

	for (i = buttonsObj.length - 1; i >= 0; i--) {
		buttons[i] = _do_button(buttonsObj[i], i);
	}

	_on(message, _postMessage);

	win[obj] = {
		loaded: true,
		destroy: _destroy,
		close: _close
	};


})();