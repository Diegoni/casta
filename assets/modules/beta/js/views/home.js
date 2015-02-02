window.HomeView = Backbone.View.extend({

    initialize:function () {
        this.render();
    },

    render:function () {
        $(this.el).html(this.template());
        return this;
    },

    close: function() {
    	this.remove();
    	this.trigger('closeTab', this.attributes.tabid);
    },

    getRoute: function() {
    	return '';
    }

});