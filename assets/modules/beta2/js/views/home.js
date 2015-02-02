var App = App || {};
App.Views = App.Views || {};

App.Views.HomeView = Backbone.View.extend({

    initialize:function () {
        this.render();
    },

    render:function () {
        $(this.el).html(this.template());
       return this;
    },

    onRender: function() {
        $('.select_rows').click(function () {
            var tableid = $(this).data('tableid');
            $('#'+tableid).find('input[name=row_sel]').attr('checked', this.checked);
        });
       console.dir($('#dt_gal')); 
       $('#dt_gal').dataTable({
            "sDom": "<'row'<'span6'<'dt_actions'>l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
           "sPaginationType": "bootstrap",
            "aaSorting": [[ 2, "asc" ]],
            "aoColumns": [
                { "bSortable": false },
                { "bSortable": false },
                { "sType": "string" },
                { "sType": "formatted-num" },
                { "sType": "eu_date" },
                { "bSortable": false }
            ]
        });        
    },

    close: function() {
    	this.remove();
    	this.trigger('closeTab', this.attributes.tabid);
    },

    getRoute: function() {
    	return '';
    }

});