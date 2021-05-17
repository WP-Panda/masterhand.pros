$(document).ready(function(){

});

var mod = {
	ajax: new XMLHttpRequest,
	url_send : window.location.href,
	field : '',
	direction : 'ASC',
	basepage : 1,
	page : 1,
	searchStr : '',
	searchStrRw : '',
	classFieldRequired : 'field-required',
	templateAlertSucc : {
		wrapBegin: '<div class="alert alert-success" role="alert" data-dismiss="alert"><strong>',
		wrapEnd: '</strong><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a></div>'
	},
	templateAlertErr : {
		wrapBegin: '<div class="alert alert-danger" role="alert" data-dismiss="alert"><strong>',
		wrapEnd: '</strong><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a></div>'
	},
	templateAlertWarn : {
		wrapBegin: '<div class="alert alert-warning" role="alert" data-dismiss="alert"><strong>',
		wrapEnd: '</strong><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a></div>'
	},
	alert : function(msg, type){
		if(msg == undefined || msg.length == 0) return;

		var wrap;
		switch(type){
			case 'success': wrap = this.templateAlertSucc; break;
			case 'warning': wrap = this.templateAlertWarn; break;
			case 'error':
			default: wrap = this.templateAlertErr; break;
		}

		var alerts = $('.body-alerts').prepend( wrap.wrapBegin + msg + wrap.wrapEnd)
		$(alerts).find('.alert').first().fadeOut({duration : 7000, always : function(result){$(result.elem).remove()}})
	},
	setSort : function (elm, f){
		var direction = ($(elm).data('sort') != undefined)? $(elm).data('sort') : 'ASC';

		$(elm).parent().find('i').removeClass('fa-long-arrow-alt-down').removeClass('fa-long-arrow-alt-up').after(function(){
			console.log('after')
		})
		if(direction == 'ASC') {
			$(elm).find('i').addClass('fa-long-arrow-alt-down')
			$(elm).data('sort', 'DESC');
		} else {
			$(elm).find('i').addClass('fa-long-arrow-alt-up')
			$(elm).data('sort', 'ASC');
		}
		this.field = f;
		this.direction = direction;

		this.getData();
	},
	setSearch : function(){
		var search = $('#search_field').val();
		if(search.length){
			mod.searchStr = search;
			mod.page = mod.basepage;
			mod.getData()
		}
	},
	resetSearch : function(){
		mod.searchStr = '';
		mod.page = mod.basepage;
		mod.field = '';
		$('#search_field').val('');
		$('.skill-table thead').find('i').removeClass('fa-long-arrow-alt-down').removeClass('fa-long-arrow-alt-up');
		mod.getData()
	},
	getData : function (pg){
		if(this.page != pg && pg != undefined)
			this.page = pg;

		var data = 'action=getList&page=' + this.page;

		if(this.field && this.direction) {
			data += '&orderBy=' + encodeURIComponent(this.field + ',' + this.direction);
		}

		if(this.searchStr.length > 0) {
			data += '&search=' + encodeURIComponent(this.searchStr);
		}

		mod.showLoad();
		$.post(mod.url_send, data, function(result){
			var data = mod.parseJsonString(result);
			mod.hideLoad();
			if(data.status == 'success') {
				$('#list_skill').html(data.list);
				$('.pagination-skills').html(data.pagination);
			} else {
				mod.alert(data.msg? data.msg : 'Error!')
			}
		}).fail(mod.failRequest)
	},
	saveConf : function(fr){
		fr.onsubmit = function(){return false;}

		var data = new FormData(fr);
		data.append('action', 'updConfig');

		mod.showLoad()
		$.post(mod.url_send, 'action=updConfig&' + $(fr).serialize(), function(r){
			mod.hideLoad()
			if(r.status == 'success'){ mod.alert('Сохранено', 'success'); mod.reload(2) } else { mod.alert(r.msg) }
		}).fail(mod.failRequest);
	},
	reload: function(sec){
		sec = typeof parseInt(sec) != 'NaN'? sec : 0;
		setTimeout(function(){
			document.location.reload();
		}, sec * 1000);
	},
	msg : function(key){
		return langActiveRat[key]? langActiveRat[key] : key;
	},
	showLoad : function(){
		$('.blockLoader').show()
	},
	hideLoad : function(){
		$('.blockLoader').hide()
	},
	urlAction : function(actionName){
		return this.url_send.indexOf('?')? this.url_send + '&action=' + actionName : this.url_send + '?action=' + actionName
	},
	failRequest : function(r){
		mod.hideLoad();
		mod.alert('Error! Status code ' + r.status + ' ' + r.statusText
				+ (r.responseText ? '<br>Server Response:<br><br>' + r.responseText : '')
		);
		$('#value_skill').attr('readonly', false)
	},
	isJsonString : function (str) {
		try {
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	},
	parseJsonString : function (str) {
		return this.isJsonString(str) ? JSON.parse(str) : (typeof str === 'object' ? str : {});
	}
}