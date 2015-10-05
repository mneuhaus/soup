$(document).ready(function(){
	$('.card-expandable').cardExpandable();

	$('.card .form-group').each(function(){
		$(this).find('label, .col-sm-9').matchHeight();
	});
});