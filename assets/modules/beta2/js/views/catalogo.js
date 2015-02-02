var App = App || {};
App.Views = App.Views || {};

App.Views.SearchResultItemView = Backbone.View.extend({

    tagName: 'tr',

    initialize: function() {

    },

    render: function() {
        // render this model using mustache template
        //console.dir((this.model.attributes));
        //console.log(this.template());
        $(this.el).html(this.template(this.model.attributes));
        return this;
    }
});

App.Views.CatalogoBuscarView = Backbone.View.extend({

    initialize: function () {   
        this.model = new App.Models.SearchResult();

        this.render();
    },

   events: {
        "click #search_button"   : "clickSearch"
    },

    renderList: function() {
        var self = this;
        var els = '';
        var coll = self.model.get('value_data');
        $('#search_result_list').empty();
        if (coll) {
            coll.forEach( function(item) {
                var photo = new App.Models.SearchResultItem(item);
                var el = photo.view.render().el;
                els += el.innerHTML;
            });
            $('#search_result_list').html( els );
        }
        
    },

    clickSearch: function (e) {
        e.preventDefault();
        var t = $('#search_texto').val();
        var self = this
        $('#search_resultado').showLoading();
        this.model.fetchData( t, '', function() {
            self.renderList();
            $('#search_resultado').hideLoading();
        });
    
        return false;
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