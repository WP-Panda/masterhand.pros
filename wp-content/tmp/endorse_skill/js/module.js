$(document).ready(function(){

	$('.run-search').on('click', mod.setSearch);
	$('.reset-search').on('click', mod.resetSearch);
	$('#search_field').on('keypress', function(e){
		if(e.keyCode == 13){ mod.setSearch() }
	});

	$('#value_skill').on('keypress', function(e){
		if(e.keyCode == 13){ mod.createSkill() }
	});
	$('.create-skill').on('click', mod.createSkill);
	$('.form-create-skill').on('submit', function(){return false});
	$('.form-edit-skill').on('submit', function(){mod.editSkill(this); return false});

	$('#group_skill').select2();
	//$('#searchDoc').select2({
	//	matcher: mod.matchCustom
	//});

});

var mod = {
	skillId : 0,
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
	matchCustom : function (obj, obj2){
		console.log(obj)
		console.log(obj2)

		//return null;
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
	createSkill : function(){
		var targetSkill = document.querySelector('#value_skill');
		var valueGroup = document.querySelector('#group_skill').value;
		var valueSkill = targetSkill.value;
		if(valueSkill.length) {
			targetSkill.readonly = true;
			var data = {'skill' : valueSkill, 'group' : valueGroup};
			data.action = 'createSkill';
			mod.showLoad();
			$.post(mod.url_send, data, function (result) {
				var data = mod.parseJsonString(result);
				if (data.status == 'success') {
					targetSkill.value = '';
					//$('#list_skill').prepend(data.list);
					mod.alert(mod.msg('saved'), 'success');
					mod.resetSearch()
				} else {
					mod.hideLoad();
					mod.alert(data.msg ? data.msg : mod.msg('error'));
				}
				targetSkill.readonly = false;
			}).fail(mod.failRequest)
		}
	},
	openEdit : function(id){
		this.skillId = id;
		mod.showLoad();
		$.post(mod.url_send, 'action=getSkill&id=' + id, function (result) {
			var data = mod.parseJsonString(result);
			mod.hideLoad();
			if (data.status == 'success') {
				$('#edit_title_skill').val(data.item.title)
				$('#edit_group_skill').val(data.item.group_skill)
				$('#edit_skill').modal();
			} else {
				mod.alert(data.msg ? data.msg : mod.msg('error'));
			}
		}).fail(mod.failRequest)
	},
	editSkill : function(fr){
		console.log(fr)
		var self = this;
		var data = new FormData(fr);
		data.append('action', 'editSkill');
		data.append('id', self.skillId);
		mod.showLoad();
		$.ajax({
			url: self.url_send,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			success: function ( result ) {
				if(result.status == 'success') {
					mod.alert(mod.msg('saved'), 'success');
					$('#edit_skill').modal('hide');
					mod.getData();
				} else {
					mod.hideLoad();
					mod.alert(result.msg ? result.msg : mod.msg('error'))
				}
			},
			error: mod.failRequest
		});
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

	resetRating : function(obj){
		//console.log(obj)

		var docId = $(obj).data('id');
		var docTitle = $(obj).data('name');
		$.confirm({
			title: 'Сброс рейтинга для <b>' + docTitle + '</b>',
			content: 'Уверены??? Вместе со сбросом удаляться все отзывы',
			type: 'blue',
			buttons: {
				ok: {
					text: 'Да',
					btnClass: 'btn-primary',
					keys: ['enter'],
					action: function(){
						mod.showLoad()
						$.post(mod.url_send, 'action=resetRating&docId=' + docId, function(result){
							mod.hideLoad()
							var data = mod.parseJsonString(result)
							if(data.status == 'success') {
								mod.alert(mod.msg('success'), 'success')
								mod.resetSearch()
								mod.resetSearchRw()
							} else {
								mod.alert(mod.msg('error'))
							}
							mod.hideLoad()
						}).fail(mod.failRequest)
					}
				},
				cancel: {
					text: 'Нет'
				}
			}
		})
	},
	delSkill : function(obj){
		var id = $(obj).data('id');
		$.confirm({
			title: mod.msg('delete_record') + ' "' + $(obj).data('name') + '"',
			content: mod.msg('sure'),
			type: 'blue',
			buttons: {
				ok: {
					text: mod.msg('yes'),
					btnClass: 'btn-primary',
					keys: [],
					action: function(){
						mod.showLoad()
						$.post(mod.url_send, 'action=deleteSkill&skId=' + id, function(result){
							mod.hideLoad()
							var data = mod.parseJsonString(result)
							if(data.status == 'success') {
								mod.alert(mod.msg('success'), 'success', 2);
								mod.resetSearch()
							} else {
								mod.alert(mod.msg('error'))
							}
						}).fail(mod.failRequest);
					}
				},
				cancel: {
					text: mod.msg('no')
				}
			}
		})
	},
	msg : function(key){
		return langEndoSkill[key]? langEndoSkill[key] : key;
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
	},
}