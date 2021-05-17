$(document).ready(function(){
	$('.run-search').on('click', mod.setSearch);

	$('#search_field').on('keypress', function(e){
		if(e.keyCode == 13){ mod.setSearch() }
	});
	$('.reset-search').on('click', mod.resetSearch);
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
	selectedObj : null,
	chartPie : null,
	chartLine : null,
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
		$('.source-table-items thead').find('i').removeClass('fa-long-arrow-alt-down').removeClass('fa-long-arrow-alt-up');
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
				$('#listItems').html(data.list);
				$('.source-pagination-items').html(data.pagination);

				if(mod.chartPie !== null){
					$('.wrap-chartPie').empty().after(function(){
						$('.wrap-chartPie').html('<canvas id="chartPie" style="width: 100%;height: 100%"></canvas>');
					})
				}
			} else {
				mod.alert(data.msg? data.msg : 'Error!')
			}
		}).fail(mod.failRequest)
	},
	moreDetail : function(obj){
		if(this.selectedObj !== null) {
			$(this.selectedObj).removeClass('item-is-selected');
		}
		this.selectedObj = $(obj).parent().parent();
		$(this.selectedObj).addClass('item-is-selected');
		var docId = $(obj).data('id');
		mod.showLoad();
		$.post(mod.url_send, 'action=moreDetail&docId=' + docId, function(result){
			mod.hideLoad()
			var data = mod.parseJsonString(result)
			if(data.status == 'success') {
				var ctxPie = document.getElementById('chartPie').getContext('2d');
				var ctxLine  = document.getElementById('chartLine').getContext('2d');

				var configPie = {
					type: 'pie',
					data: {
						datasets: [{
							data: [
								data.pie.users,
								data.pie.anonymous,
							],
							backgroundColor: [
								'red',
								'blue',
							],
							label: 'Likes'
						}],
						labels: [
							'Likes of Users',
							'Likes of Anonymous',
						]
					},
					options: {
						responsive: true
					}
				};

				if(mod.chartPie !== null){
					//mod.chartPie.destroy();
					//mod.chartPie.config.data = [
					//	data.pie.users,
					//	data.pie.anonymous,
					//];
					//mod.chartPie.update();

					$('.wrap-chartPie').empty().after(function(){
						$('.wrap-chartPie').html('<canvas id="chartPie" style="width: 100%;height: 100%"></canvas>')
						ctxPie = document.getElementById('chartPie').getContext('2d');
					})
				}

				mod.chartPie = new Chart(ctxPie, configPie);

			} else {
				mod.alert('Error')
			}
			mod.hideLoad()
		}).fail(mod.failRequest)
	},
	resetLikes : function(obj){
		//console.log(obj)

		var docId = $(obj).data('id');
		var docTitle = $(obj).data('name');
		$.confirm({
			title: 'Reset likes for <b>' + docTitle + '</b>',
			content: 'Sure???',
			type: 'blue',
			buttons: {
				ok: {
					text: 'Yes',
					btnClass: 'btn-primary',
					keys: ['enter'],
					action: function(){
						mod.showLoad()
						$.post(mod.url_send, 'action=resetLikes&docId=' + docId, function(result){
							mod.hideLoad()
							var data = mod.parseJsonString(result)
							if(data.status == 'success') {
								mod.alert('Успех', 'success')
								mod.resetSearch()
								mod.resetSearchRw()
							} else {
								mod.alert('Error')
							}
							mod.hideLoad()
						}).fail(mod.failRequest)
					}
				},
				cancel: {
					text: 'No'
				}
			}
		})
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