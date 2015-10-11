$(document).ready(function(){
	$('.card-expandable').cardExpandable();
	$('.repeater').repeater();

	$('body').on('keyup', '.card-header-field', function() {
		console.log($(this).val());
		$(this).parents('.card').find('.card-header').text($(this).val() + $(this).data('card-suffix'));
	});
});