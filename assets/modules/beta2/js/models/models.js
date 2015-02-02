window.Wine = Backbone.Model.extend({

    urlRoot: "/wines",

    idAttribute: "_id",

    initialize: function () {
        this.validators = {};

        this.validators.name = function (value) {
            return value.length > 0 ? {isValid: true} : {isValid: false, message: "You must enter a name"};
        };

        this.validators.grapes = function (value) {
            return value.length > 0 ? {isValid: true} : {isValid: false, message: "You must enter a grape variety"};
        };

        this.validators.country = function (value) {
            return value.length > 0 ? {isValid: true} : {isValid: false, message: "You must enter a country"};
        };
    },

    validateItem: function (key) {
        return (this.validators[key]) ? this.validators[key](this.get(key)) : {isValid: true};
    },

    // TODO: Implement Backbone's standard validate() method instead.
    validateAll: function () {

        var messages = {};

        for (var key in this.validators) {
            if(this.validators.hasOwnProperty(key)) {
                var check = this.validators[key](this.get(key));
                if (check.isValid === false) {
                    messages[key] = check.message;
                }
            }
        }

        return _.size(messages) > 0 ? {isValid: false, messages: messages} : {isValid: true};
    },

    defaults: {
        _id: null,
        name: "",
        grapes: "",
        country: "USA",
        region: "California",
        year: "",
        description: "",
        picture: null
    }
});

window.WineCollection = Backbone.Collection.extend({

    model: Wine,

    url: "/wines"

});

var App = App || {};
App.Models = App.Models || {};

//model
App.Models.SearchResult = Backbone.Model.extend({
    term: '',
    sort: null,
    defaults: {
        fPVP: 0
    },

    initialize: function() {
        return this;
    },

    url: function() {
        var uri = site_url('catalogo/articulo/get_list'),
        //var uri = 'http://localhost:3000/',
            params = '',
            term = this.term, 
            sort = this.sort;

        // build the url            
        if (term) {
            uri = uri + '?limit=50&query=' + encodeURIComponent(term);
          }
        return uri;
    },

    fetchData: function( term, sort, callback ) {

        this.term = term;
        this.sort = sort;

        this.fetch({
            success: function() { callback(); },
            error: function() { callback(); }
        });     
    }   
});

App.Models.SearchResultItem = Backbone.Model.extend({
    view: null,
    initialize: function() {
        this.view = new App.Views.SearchResultItemView({model: this});
        return this;
    }
});
