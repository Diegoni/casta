/**
 * Barra lateral donde se muestran todas las ventanas abiertas
 * @type Backbone.View
 */
window.SidebarView = Backbone.View.extend({

    actual : null,
    tabs : [],

    initialize: function () {
        this.render();
    },

    render: function () {
        $(this.el).html(this.template());
        return this;
    },

    selectTab: function (id) {
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
    }
});