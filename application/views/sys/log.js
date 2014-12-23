(function(){

    try {
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Visor de logs');
        if (icon == '') 
            icon = 'iconoLogViewTab';
        
        var detailsText = _s('log-select-file');
        
        var border = new Ext.Panel({
            title: title,
            id: form_id,
            region: 'center',
            closable: true,
            iconCls: icon,
            layout: 'border',
            items: [{
                region: 'center',
                xtype: 'treepanel',
                id: form_id + 'tree',
                loadMask: true,
                iconCls: 'iconoMenu',
                //width: 225,
                autoScroll: true,
                split: true,
                useArrows: true,
                animate: true,
                rootVisible: false,
                listeners: {
                    'render': function(tp){
                        tp.getSelectionModel().on('selectionchange', function(tree, node){
                            var el = Ext.getCmp(form_id + 'details-panel').body;
                            if (node && node.leaf) {
                                Ext.app.callRemote({
                                    url: site_url('sys/logview/get_log'),
                                    params: {
                                        id: node.id
                                    },
                                    nomsg: true,
                                    fnok: function(obj){
                                        if (obj.success) {
                                            el.update(obj.message);
                                        }
                                    }
                                });
                            }
                            else {
                                el.update(detailsText);
                            }
                        })
                    }
                },
                tbar: [{
                    tooltip: _s('cmd-actualizar'),
                    iconCls: 'icon-refresh',
                    listeners: {
                        click: function(){
                            var x = Ext.getCmp(form_id + 'tree');
							//console.dir(x.getLoader());
							x.root.reload();
                            //x.expandAll();
                        }
                    }
                }, '-', {
                    tooltip: _s('cmd-expandir'),
                    iconCls: 'iconoExpandir',
                    listeners: {
                        click: function(){
                            var x = Ext.getCmp(form_id + 'tree');
                            x.expandAll();
                        }
                    }
                }, {
                    tooltip: _s('cmd-contraer'),
                    iconCls: 'iconoContraer',
                    listeners: {
                        click: function(){
                            var x = Ext.getCmp(form_id + 'tree');
                            x.collapseAll();
                        }
                    }
                }],
                loader: new Ext.tree.TreeLoader({
                    dataUrl: site_url('sys/logview/get_list')
                }),
                root: new Ext.tree.AsyncTreeNode({
                    expanded: true
                })
            }, {
                region: 'south',
                title: _s('Contenido log'),
                id: form_id + 'details-panel',
                autoScroll: true,
                collapsible: true,
                split: true,
                margins: '0 2 2 2',
                cmargins: '2 2 2 2',
                height: 220,
                html: detailsText
            }]
        });
        
        return border;
    } 
    catch (e) {
        console.dir(e);
    }
    
})();
