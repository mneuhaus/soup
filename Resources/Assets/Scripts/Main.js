$(document).ready(function(){
	$('.card-expandable').cardExpandable();
	$('.repeater').repeater();

	$('body').on('keyup', '.card-header-field', function() {
		$(this).parents('.card').find('.card-header-text').text($(this).val() + $(this).data('card-suffix'));
	});
});