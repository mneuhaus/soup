$.widget("custom.cardExpandable", {
    // Default options.
    options: {
        value: 0
    },
    _create: function() {
        console.log(this, this.options, this.element);
        var ref = this;

        this.element.on('click', '.js-action-edit', function(e) {
            e.preventDefault();
            ref.toggle();
        });
    },

    toggle: function() {
        this.element.toggleClass('open');
    }
});