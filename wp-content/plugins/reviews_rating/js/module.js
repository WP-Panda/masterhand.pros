$(document).ready(function(){
	$('.run-search').on('click', mod.setSearch);

	$('#search_field').on('keypress', function(e){
		if(e.keyCode == 13){ mod.setSearch() }
	});
	$('.reset-search').on('click', mod.resetSearch);

	$('.run-search-rw').on('click', mod.setSearchRw);

	$('#search_fieldRw').on('keypress', function(e){
		if(e.keyCode == 13){ mod.setSearchRw() }
	});
	$('.reset-search-rw').on('click', mod.resetSearchRw);

	$('.review-star-vote').mouseover(function(){
		var currClass = this.className
		var run = true
		$.each($('.review-star-vote'), function(it, elm){
			if(run) {
				if (elm.className == currClass) {
					run = false
				} else {
					$(elm).addClass('review-star-over')
				}
			}
		})
	}).mouseout(function() {
		$(this).removeClass('review-star-over')
	});
	$('.review-select-vote').mouseout(function(){
		$.each($('.review-star-vote'), function(){
			$(this).removeClass('review-star-over')
		})
	});
	$('.review-vote').on('change', function(){
		var vote = this.value;
		$.each($('.review-vote'), function(it, elm){
			if(elm.value < vote) {
				$(this).addClass('vote-checked')
			} else {
				$(this).removeClass('vote-checked')
			}
		})
	});

	$('.form-create-review').on('submit', mod.createReview)
	//$('.form-edit-review').on('submit', mod.editReview)
	$('.form-edit-review').on('submit', function(){mod.editReview(this); return false})

	//$('#searchDoc').select2({
	//	matcher: mod.matchCustom
	//});

})

var mod = {
	userId : 0,
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

		var wrap
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
	createReview : function(){
		var isEmpty = false;
		//console.log(this)
		//console.log($(this).serialize())
		var docId = $('#searchDoc').val();
		var username = $('.content-review-username').val();
		var title = $('.content-review-title').val();
		var comment = $('.content-review-comment').val();
		var objStatus = $('.content-review-status');
		var status = $(objStatus).val() !== undefined ? $(objStatus).val() : '';

		var isVote = document.querySelector('.review-vote:checked')? document.querySelector('.review-vote:checked') : document.querySelector('.review-vote')
		var vote = isVote.checked ? isVote.value : 0;

		if(title.replace(/ |\r|\n/g, '')){
			$('.content-review-title').parent().removeClass(mod.classFieldRequired)
		} else {
			isEmpty = true;
			$('.content-review-title').parent().addClass(mod.classFieldRequired)
		}
		if(username.replace(/ |\r|\n/g, '')){
			$('.content-review-username').parent().removeClass(mod.classFieldRequired)
		} else {
			isEmpty = true;
			$('.content-review-username').parent().addClass(mod.classFieldRequired)
		}

		if(comment.replace(/ |\r|\n/g, '')){
			$('.content-review-comment').parent().removeClass(mod.classFieldRequired)
		} else {
			isEmpty = true;
			$('.content-review-comment').parent().addClass(mod.classFieldRequired)
		}

		if(isEmpty == true) return false;

		var form = 'docId=' + encodeURIComponent(docId)
				+ '&username=' + encodeURIComponent(username)
				+ '&title=' + encodeURIComponent(title)
				+ '&comment=' + encodeURIComponent(comment)
				+ '&vote=' + encodeURIComponent(vote)
				+ '&status=' + encodeURIComponent(status);

		mod.showLoad()
		$('.review-submit').attr('disabled', 'disabled');
		$.post(mod.urlAction('createReview'), form, function(result){
			mod.hideLoad();
			var data = mod.parseJsonString(result);
			if(data.status == 'success'){
				mod.alert(data.msg, 'success');

				if(data.reload){ mod.reload(2) }
			} else {
				mod.alert(data.msg)
			}

		}).fail(mod.failRequest).always(function(){$('.review-submit').removeAttr('disabled');})

		return false;
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
		if($(elm).parent().data('tb') == 'reviews') {
			this.getDataRw();
		} else {
			this.getDataRt();
		}
	},
	setSearch : function(){
		var search = $('#search_field').val()
		if(search.length){
			mod.searchStr = search
			mod.page = mod.basepage
			mod.getDataRt()
		}
	},
	resetSearch : function(){
		mod.searchStr = ''
		mod.page = mod.basepage
		mod.field = ''
		$('#search_field').val('')
		$('#ratings thead').find('i').removeClass('fa-long-arrow-alt-down').removeClass('fa-long-arrow-alt-up')
		mod.getDataRt()
	},
	setSearchRw : function(){
		var search = $('#search_fieldRw').val()
		if(search.length){
			mod.searchStrRw = search
			mod.page = mod.basepage
			mod.getDataRw()
		} else{
			mod.resetSearchRw()
		}
	},
	resetSearchRw : function(){
		mod.searchStrRw = ''
		mod.page = mod.basepage
		mod.field = ''
		$('#search_fieldRw').val('')
		$('#reviews thead').find('i').removeClass('fa-long-arrow-alt-down').removeClass('fa-long-arrow-alt-up')
		mod.getDataRw()
	},
	getDataRt : function (pg){
		if(this.page != pg && pg != undefined)
			this.page = pg;

		var data = 'action=getRtList&page=' + this.page;

		if(this.field && this.direction) {
			data += '&orderBy=' + encodeURIComponent(this.field + ',' + this.direction);
		}

		if(this.searchStr.length > 0) {
			data += '&search=' + encodeURIComponent(this.searchStr);
		}

		mod.showLoad()
		$.post(mod.url_send, data, function(result){
			var data = mod.parseJsonString(result)
			mod.hideLoad()
			if(data.status == 'success') {
				$('#ratings tbody').html(data.list);
				$('.pagination-rating').html(data.pagination);
			} else {
				mod.alert(data.msg? data.msg : 'Error!')
			}
		}).fail(mod.failRequest)
	},
	getDataRw : function (pg){
		if(this.page != pg && pg != undefined)
			this.page = pg;

		var data = 'action=getRwList&page=' + this.page;

		if(this.field && this.direction) {
			data += '&orderBy=' + encodeURIComponent(this.field + ',' + this.direction);
		}

		if(this.searchStrRw.length > 0) {
			data += '&search=' + encodeURIComponent(this.searchStrRw);
		}

		mod.showLoad()
		$.post(mod.url_send, data, function(result){
			var data = mod.parseJsonString(result)
			mod.hideLoad()
			if(data.status == 'success') {
				$('#reviews tbody').html(data.list);
				$('.pagination-reviews').html(data.pagination);
			} else {
				mod.alert(data.msg? data.msg : 'Error!')
			}
		}).fail(mod.failRequest)
	},
	editReview : function(fr){
		console.log(fr)
		var self = this;
		var data = new FormData(fr);
		//data.append('action', 'editReview')
		data.append('action', 'changeStatus')
		$.ajax({
			url: self.url_send,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			success: function ( result ) {
				if(result.status == 'success') {
					mod.alert('Saved', 'success')
					mod.reload(2)
				} else {
					mod.alert(result.msg ? result.msg : 'Error!')
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
			if(r.status == 'success'){ mod.alert('Saved', 'success'); mod.reload(2) } else { mod.alert(r.msg) }
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
					text: 'Нет'
				}
			}
		})
	},
	delReview : function(id){
		$.confirm({
			title: 'Удаление отзыва',
			content: 'Уверены?',
			type: 'blue',
			buttons: {
				ok: {
					text: 'Да',
					btnClass: 'btn-primary',
					keys: [],
					action: function(){
						mod.showLoad()
						$.post(mod.url_send, 'action=deleteReview&rwId=' + id, function(result){
							mod.hideLoad()
							var data = mod.parseJsonString(result)
							if(data.status == 'success') {
								mod.alert('Успех', 'success', 2);
								mod.resetSearchRw()
							} else {
								mod.alert('Error')
							}
						}).fail(mod.failRequest);
					}
				},
				cancel: {
					text: 'Нет'
				}
			}
		})
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
		//console.log(this)
		//console.log(r)
		mod.hideLoad()
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
	},
}