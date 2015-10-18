$(document).ready(function(){
	$('.card-expandable').cardExpandable();
	$('.repeater').repeater();

	$('body').on('keyup', '.card-header-field', function() {
		$(this).parents('.card').find('.card-header .card-header-text').text($(this).val() + $(this).data('card-suffix'));
	});

	$('.dropdown-add .dropdown-menu a').click(function(e){
		e.preventDefault();
		$($(this).attr('href') + ' .repeater-item-add').click();
	});
});