$.widget("custom.repeater", {
    // Default options.
    options: {},
    _create: function() {
        var repeater = this;
        this.itemsContainer = this.element.find('.repeater-items');
        this.template = this.element.find('.repeater-template');

        $(this.element).on("keyup", ".repeater-unused input", function() {
            if ($(this).val().length > 0) {
                $(this).parents('.repeater-unused').removeClass('repeater-unused');
                repeater.addNewUnused();
            }
        });

        repeater.addNewUnused();

        $(this.element).on("click", ".repeater-item-remove", function(e) {
            e.preventDefault();
            var item = $(this).parents('.repeater-item');
            repeater.element.append('<input type="hidden" name="' + item.find('input').attr('name') + '[_remove]' + '" value=1>');
            item.find('.repeater-item-remove').remove();
            item.slideUp(function(){
                item.remove();
            });
        });

        $('form').submit(function() {
            $('.repeater-unused, .repeater-template').remove();
        });
    },

    addNewUnused: function() {
        var e = this.template.clone();
        e.removeClass('repeater-template');
        e.addClass('repeater-unused');
        e.html(e.html().replace(/--id--/g, Math.random().toString(36).slice(2)));
        this.itemsContainer.append(e);
    }
});