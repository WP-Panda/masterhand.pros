/* 
	User Submitted Posts - Plugin Settings
	@ https://perishablepress.com/user-submitted-posts/
*/

jQuery(document).ready(function($){
	
	// toggle panels
	$('.default-hidden').hide();
	$('#mm-panel-toggle a').on('click', function(){
		$('.toggle').slideToggle(300);
		return false;
	});
	$('h2').on('click', function(){
		$(this).next().slideToggle(300);
		return false;
	});
	
	// jump toggle panels
	$('#mm-panel-primary-link').on('click', function(){
		$('.toggle').hide();
		$('#mm-panel-primary .toggle').slideToggle(300);
		return true;
	});
	$('#mm-panel-secondary-link').on('click', function(){
		$('.toggle').hide();
		$('#mm-panel-secondary .toggle').slideToggle(300);
		return true;
	});
	
	// toggle form info
	$('.usp-custom-form').on('click', function(){
		$('.usp-custom-form-info').slideDown(300);
	});
	$('.usp-form').on('click', function(){
		$('.usp-custom-form-info').slideUp(300);
	});
	
	// toggle categories
	$('.usp-cat-toggle-link').on('click', function(){
		$('.usp-cat-toggle-div').toggle(300);
		return false;
	});
	
});
