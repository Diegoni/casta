window.CatalogoBuscarView = Backbone.View.extend({

    initialize: function () {
        this.render();
    },

    render: function () {
        $(this.el).html(this.template());
        return this;
    },

    close: function() {
    	this.remove();
    	console.dir(this);
    	this.trigger('closeTab', this.options.tabid);
    },

    getRoute: function() {
    	return 'catalogo/buscar';
    }
});