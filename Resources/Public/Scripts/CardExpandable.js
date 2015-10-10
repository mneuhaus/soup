$.widget("custom.cardExpandable", {
    // Default options.
    options: {
        value: 0
    },
    _create: function() {
        var ref = this;

        this.element.on('click', '.js-action-edit', function(e) {
            e.preventDefault();
            ref.toggle();
        });
    },

    toggle: function() {
        this.element.find('.card-body').slideToggle();
    }
});