window.CatalogoBuscarView = Backbone.View.extend({

    initialize: function () {   
        this.model = new App.Models.SearchResult();
       /*this.collection = new SearchResultCollection();
        this.collection.bind("reset", this.render, this);
        this.collection.bind("change", this.renderList, this);*/

         this.render();
    },

   events: {
        "click #search_button"   : "clickSearch"
    },

    renderList: function() {
        var self = this;
        var coll = self.model.get('value_data');
        if (coll) {
            coll.forEach( function(item) {
                var photo = new App.Models.Photo(item);
                els.push( photo.view.render().el );
            });
    },

    clickSearch: function (e) {
        e.preventDefault();
        var t = $('#search_texto').val();
        console.log(t);
        var self = this
        this.model.fetchData( t, '', function() {
            self.renderList();

            /*var coll = self.model.get('photo');
        if (coll) {

            coll.forEach( function(item) {
                var photo = new App.Models.Photo(item);
                els.push( photo.view.render().el );
            })*/
        });
    
        //this.collection.fetch();
        /*console.log(site_url('catalogo/articulo/search'));
        $('#search_resultado').showLoading();
                console.log(this.collection.toJSON());
        var jqxhr = $.post(site_url('catalogo/articulo/search'), {query: t}, function(data) {
              console.log("success");
              console.dir(data);
            })
            .done(function() { console.log("second success"); })
            .fail(function() { console.log("error"); })
            .always(function() { console.log("finished"); 
                $('#search_resultado').hideLoading()});*/
        return false;
        $('#search_resultado').showLoading();

        return false;
    },
    render: function () {
        $(this.el).html(this.template());
        return this;
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