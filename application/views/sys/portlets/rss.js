/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
FeedGrid = function(viewer, config){
    this.viewer = viewer;
    Ext.apply(this, config);
    
    this.store = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: site_url('tools/rss/feed')
        }),
        
        reader: new Ext.data.XmlReader({
            record: 'item'
        }, ['title', 'author', {
            name: 'pubDate',
            type: 'date'
        }, 'link', 'description', 'content'])
    });
    this.store.setDefaultSort('pubDate', "DESC");
    
    this.columns = [{
        id: 'title',
        header: _s("cTitulo"),
        dataIndex: 'title',
        sortable: true,
        width: 420,
        renderer: this.formatTitle
    }, {
        header: _s("Autor"),
        dataIndex: 'author',
        width: 100,
        hidden: true,
        sortable: true
    }, {
        id: 'last',
        header: _s("Fecha"),
        dataIndex: 'pubDate',
        width: 150,
        renderer: this.formatDate,
        sortable: true
    }];
    
    FeedGrid.superclass.constructor.call(this, {
        region: 'center',
        id: 'topic-grid',
        loadMask: {
            msg: _s('Cargando')
        },
        
        sm: new Ext.grid.RowSelectionModel({
            singleSelect: true
        }),
        
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            showPreview: true,
            getRowClass: this.applyRowClass
        }
    });
    
    this.on('rowcontextmenu', this.onContextClick, this);
};

Ext.extend(FeedGrid, Ext.grid.GridPanel, {

    onContextClick: function(grid, index, e){
        if (!this.menu) { // create context menu on first right click
            this.menu = new Ext.menu.Menu({
                //id: 'grid-ctx',
                items: [{
                    iconCls: 'rss-new-win',
                    text: _s('Ver entrada'),
                    scope: this,
                    handler: function(){
                        Ext.app.addTabUrl({
                            url: this.ctxRecord.data.link,
                            title: this.ctxRecord.data.title,
                            icon: 'rss-new-win-tab'
                        });
                        //window.open(this.ctxRecord.data.link);
                    }
                }, '-', {
                    iconCls: 'rss-refresh-icon',
                    text: _s('Actualizar'),
                    scope: this,
                    handler: function(){
                        this.ctxRow = null;
                        this.store.reload();
                    }
                }]
            });
            this.menu.on('hide', this.onContextHide, this);
        }
        e.stopEvent();
        if (this.ctxRow) {
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
        this.ctxRow = this.view.getRow(index);
        this.ctxRecord = this.store.getAt(index);
        Ext.fly(this.ctxRow).addClass('x-node-ctx');
        this.menu.showAt(e.getXY());
    },
    
    onContextHide: function(){
        if (this.ctxRow) {
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
    },
    
    loadFeed: function(url){
        this.store.baseParams = {
            feed: url
        };
        this.store.load();
    },
    
    // within this function "this" is actually the GridView
    applyRowClass: function(record, rowIndex, p, ds){
        if (this.showPreview) {
            var xf = Ext.util.Format;
            p.body = '<p>' + xf.ellipsis(xf.stripTags(record.data.description), 200) + '</p>';
            return 'x-grid3-row-expanded';
        }
        return 'x-grid3-row-collapsed';
    },
    
    formatDate: function(date){
        if (!date) {
            return '';
        }
        var now = new Date();
        var d = now.clearTime(true);
        var notime = date.clearTime(true).getTime();
        if (notime == d.getTime()) {
            return 'Today ' + date.dateFormat('g:i a');
        }
        d = d.add('d', -6);
        if (d.getTime() <= notime) {
            return date.dateFormat('D g:i a');
        }
        return date.dateFormat('n/j g:i a');
    },
    
    formatTitle: function(value, p, record){
        return String.format('<div class="topic"><b>{0}</b><span class="author">{1}</span></div>', value, record.data.author, record.id, record.data.forumid);
    }
});

function Portlet_rss(){
    return {
        feed: null,
		id: null,
        html: function(params){
            return null;
        },
        
        init: function(params){
			this.id = Ext.app.createId();
            this.feed = new FeedGrid({
                height: 250
            });
            if (params[0] != null) 
                this.feed.loadFeed(params[0]);
            this.panel = {
                xtype: 'panel',
                layout: 'border',
                height: 250,
                items: this.feed
            };
            return this.panel;
        },
        load: function(){
        },
        tools: function(tools){
            var tools1 = [];
            var t = this;
            tools1.push({
                id: 'refresh',
                handler: function(){
                    t.feed.getStore().reload();
                }
            });
            return tools1.concat(tools);
        }
    }
}
