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

	$('select[multiple=1]').multiSelect({ selectableOptgroup: true });

	$('[item-source="controllerActionCombinations"]').each(function(){
		var select = $(this);
		if (select.attr('data-value')) {
			var selected = select.attr('data-value').split(',');
		} else {
			var selected = [];
		}
		$('#repeater-Typo3-Ingredients-Controller .controller-name').each(function(){
			var controllerName = $(this).val();
			var group = $('<optgroup label="' + controllerName + 'Controller" />');
			$(this).parents('.card').find('.controller-action').each(function(){
				var value = controllerName + ':' + $(this).val();
				console.log(value, selected.indexOf(value), selected);
				if (selected.indexOf(value) > -1) {
					group.append('<option value="' + value + '" selected="1">' + $(this).val() + 'Action</option>');
				} else {
					group.append('<option value="' + value + '">' + $(this).val() + 'Action</option>');
				}
			});
			select.append(group);
			if (select.attr('multiple')) {
				select.multiSelect('refresh');
			}
		});
	});
});