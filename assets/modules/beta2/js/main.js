var app = app || {};

var AppRouter = Backbone.Router.extend({

    routes: {
        ""                  : "home",
        "home"				: "home",
        "catalogo/buscar"   : "catalogoBuscar",
        
        "wines"	: "list",
        "wines/page/:page"	: "list",
        "wines/add"         : "addWine",
        "wines/:id"         : "wineDetails",
        "about"             : "about"
    },

    initialize: function () {
        this.headerView = new App.Views.HeaderView();
        $('#app_header').html(this.headerView.el);
        this.sidebarView = new App.Views.SidebarView();
        $('#app_sidebar').html(this.sidebarView.el);
        this.home();
    },

    home: function (id) {
        /*
        http://ianstormtaylor.com/rendering-views-in-backbonejs-isnt-always-simple/
         
        render : function () {
            this.$el.html(this.template());

            this.assign(this.subview,        '.subview');
            this.assign(this.anotherSubview, '.another-subview');
            return this;
           }
          assign : function (view, selector) {
                view.setElement(this.$(selector)).render();
            }
         */
        if (!this.homeView) {
            this.homeView = new App.Views.HomeView();
            this.sidebarView.addTab(null, null, 'home',  this.homeView, 'icon-home', 'Portal', false);
        }
        this.sidebarView.selectTab('home');
    },
    count : 0,
    catalogoBuscar: function (text){
        var id = 'cat_buscar' + this.count;
        this.count++;
        var t = this;
        var view = new App.Views.CatalogoBuscarView({tabid: id});
        this.sidebarView.addTab('catalogo_buscar', 'Buscar', id, view, 'icon-search', 'Buscar');
        return;

        var controller = function(id) {
            delete t.catalogoBuscarViews[id];
        }
        if (!this.catalogoBuscarViews || !this.catalogoBuscarViews[id]) {
            this.catalogoBuscarViews = [];
            var view = new CatalogoBuscarView({tabid: id});
            this.catalogoBuscarViews[id] = view;
            view.on('closeTab', function(id) {
                delete t.catalogoBuscarViews[id];
            });

            this.sidebarView.addTab('catalogo_buscar', 'Buscar', id, view, 'icon-th-list', 'Buscar');
        }
        this.sidebarView.selectTab(id);
    },

	list: function(page) {
        var p = page ? parseInt(page, 10) : 1;
        var wineList = new WineCollection();
        wineList.fetch({success: function(){
            $("#content").html(new WineListView({model: wineList, page: p}).el);
        }});
        this.headerView.selectMenuItem('home-menu');
    },

    wineDetails: function (id) {
        var wine = new Wine({_id: id});
        wine.fetch({success: function(){
            $("#content").html(new WineView({model: wine}).el);
        }});
        this.headerView.selectMenuItem();
    },

	addWine: function() {
        var wine = new Wine();
        $('#content').html(new WineView({model: wine}).el);
        this.headerView.selectMenuItem('add-menu');
	},

    about: function () {
        if (!this.aboutView) {
            this.aboutView = new AboutView();
        }
        $('#content').html(this.aboutView.el);
        this.headerView.selectMenuItem('about-menu');
    }

});

$(document).ready(function(){
    Ext.app.config_load(function() {
        // Carga las plantillas
        utils.loadTemplate([
            ['main' ,'HomeView'], 
            ['main', 'HeaderView'], 
            ['main', 'SidebarView'], 
            ['catalogo', 'CatalogoBuscarView'], 
            ['catalogo' ,'SearchResultItemView']
            ], function() {
            // Carga la aplicación
            var app = new AppRouter();
            // Router
            Backbone.history.start();

            // Estado
            Ext.status.init();

            // No salir sin preguntar
            //http://msdn.microsoft.com/en-us/library/ms536907(VS.85).aspx
            $(window).bind('beforeunload', function() {
                //if(Ext.app.askexit)
                    //return _s('reload');
                //else
                    return null;
            });
            //window.onbeforeunload = closeIt;
        });        
    });
});

