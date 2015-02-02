/**
 * Barra lateral donde se muestran todas las ventanas abiertas
 * @type Backbone.View
 */
var App = App || {};
App.Views = App.Views || {};

App.Views.SidebarView = Backbone.View.extend({

    actual : null,
    tabs : [],
    currentTab : null,
    composeCount : 0,   


    initialize: function () {

        this.render();
    },

    render: function () {
        $(this.el).html(this.template());
        return this;
    },

    selectTab: function (id) {
        this.showTab(id);
        return;

        $('#sidebar li').removeClass('active');
        if (this.last && this.last != id && this.tabs[this.last]) 
            $(this.tabs[this.last].el).hide();
        if (id && this.last != id) {
            $('#' + id).addClass('active');
            $(this.tabs[id].el).show();
            this.last = id;
            Backbone.history.navigate(this.tabs[id].getRoute()); 
        }
    },

    closeTab: function (id) {
        $('#' + id).remove();
        this.tabs[id].close();
        delete this.tabs[id];
        if (this.last == id) {
            var last = null;
            for(var i in this.tabs) {
                last = i;
            }
            if (last) {
                this.selectTab(last);
            }
        }
    },

    addTab: function(group, group_name, id, view, icon, title, close) {

        $('.nav-tabs').append('<li><a href="#' + id + '"><button class="close closeTab" type="button" >×</button>' 
            + '<i class="' + icon + '"></i>&nbsp;' 
            + title + '</a></li>');
        $('.tab-content').append('<div class="tab-pane" id="' + id + '"></div>');

        //this.craeteNewTabAndLoadUrl("", "./SamplePage.html", "#" + id);
        $("#" + id).append(view.el);
        if (view.onRender)
            view.onRender();

        var t = this;
        //when ever any tab is clicked this method will be call
        $("#myTab").on("click", "a", function (e) {
            e.preventDefault();

            $(this).tab('show');
            t.currentTab = $(this);
        });

        $(this).tab('show');
        this.showTab(id);
        this.registerCloseEvent();

        return;

        $(content).hide();
        this.tabs[id] = view;
        $('#content').append(view.el);

        var html  ='<li id="' + id  +'"><a><i class="icon ' + icon + '"></i>' +
        ((close!==false)?'<button class="close">&times;</button>':'') + '<span>' + title + '</span></a></li>';
        $('#sidebar ul').append(html);
        var el = $('#' + id);
        el.addClass('active');
        var t = this;
        if (close!==false) {
            $('#' + id + ' .close').click(function(event) {
                event.preventDefault();
                t.closeTab(id);
            });            
        }
        $('#' + id).click(function(event) {
            event.preventDefault();
            t.selectTab(id);
        });            
    },

    //this method will register event on close icon on the tab..
    registerCloseEvent: function() {

        $(".closeTab").click(function () {
            console.log('Cerrando TAB');

            //there are multiple elements which has .closeTab icon so close the tab whose close icon is clicked
            var tabContentId = $(this).parent().attr("href");
            $(this).parent().parent().remove(); //remove li of tab
            console.dir($('#myTab a:last'));
            $('#myTab a:last').tab('show'); // Select first tab
            $(tabContentId).remove(); //remove respective tab content

        });
    },

    //shows the tab with passed content div id..paramter tabid indicates the div where the content resides
    showTab: function (tabId) {
        $('#myTab a[href="#' + tabId + '"]').tab('show');
    },

    //return current active tab
    getCurrentTab: function() {
        return this.currentTab;
    },

    //This function will create a new tab here and it will load the url content in tab content div.
    craeteNewTabAndLoadUrl: function(parms, url, loadDivSelector) {

        $("" + loadDivSelector).load(url, function (response, status, xhr) {
            if (status == "error") {
                var msg = "Sorry but there was an error getting details ! ";
                $("" + loadDivSelector).html(msg + xhr.status + " " + xhr.statusText);
                $("" + loadDivSelector).html("Load Ajax Content Here...");
            }
        });
    },

    //this will return element from current tab
    //example : if there are two tabs having  textarea with same id or same class name then when $("#someId") whill return both the text area from both tabs
    //to take care this situation we need get the element from current tab.
    getElement: function(selector) {
        var tabContentId = $this.currentTab.attr("href");
        return $("" + tabContentId).find("" + selector);
    },


    removeCurrentTab: function() {
        var tabContentId = $this.currentTab.attr("href");
        $this.currentTab.parent().remove(); //remove li of tab
        $('#myTab a:last').tab('show'); // Select first tab
        $(tabContentId).remove(); //remove respective tab content
    }
});