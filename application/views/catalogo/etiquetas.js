(function(){
    var form_id = Ext.app.createId();
    var id = "<?php echo $id;?>";
    var ctxRow = null;
    var reload = function(){
        var f = Ext.getCmp(form_id + "_tree");
        f.root.reload();
    }
    
    var t = Ext.app.combobox({
        url : site_url('catalogo/grupoetiqueta/printer?list=true'),
        name : 'report',
        allowBlank: true,
        anchor : '100%',
        label : _s('Formato')
    });
    //t['forceSelection'] = false;
    var report = new Ext.form.ComboBox(t);

    var add = function(nIdSeccionPadre){
        Ext.Msg.prompt(title, _s('cNombre'), function(ok, v){
            if (ok != 'ok') 
                return;
            
            Ext.app.callRemote({
                url: site_url('generico/seccion/upd'),
                params: {
                    nIdSeccionPadre: nIdSeccionPadre,
                    cNombre: v
                },
                fnok: function(){
                    reload();
                }
            });
        });
    }
    var contextmenu = new Ext.menu.Menu({
        allowOtherMenus: false,
        items: [{
            text: _s('Imprimir'),
            handler: function(){
                if (ctxRow) {
                    var r = report.getValue();
                    if (ctxRow.attributes.children != null) {
                        Ext.app.printLabels(site_url('catalogo/grupoetiqueta/imprimir/' + id + '/' + ctxRow.attributes.id + '/-/' + r ), _s('Imprimir etiquetas'));
                    }
                    else {
                        Ext.app.printLabels(site_url('catalogo/grupoetiqueta/imprimir/' + id + '/-1/' + ctxRow.attributes.id + '/' + r), _s('Imprimir etiquetas'));                        
                    }
                }
            },
            iconCls: 'icon-print'
        }, '-', {
            text: _s('Eliminar'),
            handler: function(){
                if (ctxRow) {
                    if (ctxRow.attributes.children != null) {
                        // imprime una sección
                        var params = {
                            id: id,
                            ids: ctxRow.attributes.id
                        }
                    }
                    else 
                        var params = {
                            id: id,
                            ida: ctxRow.attributes.id
                        }
                    
                    Ext.app.callRemote({
                        url: site_url('catalogo/grupoetiqueta/del_etq'),
                        params: params,
                        fnok: function(){
                            reload();
                        }
                    });
                }
            },
            iconCls: 'icon-delete'
        }]
    });
    
    var grid = new Ext.ux.tree.TreeGrid({
        region: 'center',
        id: form_id + "_tree",
        autoScroll: true,
        useArrows: true,
        loadMask: true,
        rootVisible: false,
        anchor: '100% 80%',
        autoExpandColumn: 'descripcion',
        
        columns: [{
            header: _s('Sección'),
            id: 'descripcion',
            width: Ext.app.TAM_COLUMN_TEXT * 4,
            dataIndex: 'text'
        }, {
            header: _s('nCantidad'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            dataIndex: 'nCantidad'
        }, {
            header: _s('cSimbolo'),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'cSimbolo'
        }, {
            header: _s('fPVP'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            dataIndex: 'fPVP'
        }],
        listeners: {
            contextmenu: function(node, event){
                node.select();
                ctxRow = node;
                contextmenu.showAt(event.xy);
                return;
            }
        },
        sm: new Ext.grid.RowSelectionModel({
            singleSelect: true
        }),
        tbar: [{
            tooltip: _s('cmd-actualizar'),
            iconCls: 'icon-refresh',
            listeners: {
                click: function(){
                    reload();
                }
            }
        }, '-', {
            text: _s('Imprimir todo'),
            tooltip: _s('cmd-print'),
            handler: function(){
                var r = report.getValue();
                Ext.app.printLabels(site_url('catalogo/grupoetiqueta/imprimir/' + id + '/-1/-/' + r), _s('Imprimir etiquetas'));
            },
            iconCls: 'icon-print'
        }, '-', {
            text: _s('Borrar todo'),
            tooltip: _s('cmd-delregistro'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('catalogo/grupoetiqueta/del'),
                    params: {
                        id: id
                    },
                    fnok: function(){
                        reload();
                    }
                });
            },
            iconCls: 'icon-delete'
        }, '-', {
            tooltip: _s('cmd-expandir'),
            iconCls: 'iconoExpandir',
            listeners: {
                click: function(){
                    var f = Ext.getCmp(form_id + "_tree");
                    f.expandAll();
                }
            }
        }, {
            tooltip: _s('cmd-contraer'),
            iconCls: 'iconoContraer',
            listeners: {
                click: function(){
                    var f = Ext.getCmp(form_id + "_tree");
                    f.collapseAll();
                }
            }
        }],
        loader: new Ext.tree.TreeLoader({
            loadMask: true,
            uiProviders: {
                'col': Ext.tree.ColumnNodeUI
            },
            dataUrl: site_url('catalogo/grupoetiqueta/get_tree/' + id)
        }),
        root: new Ext.tree.AsyncTreeNode({
            expanded: true
        })
    });
    
    var controls = [grid, report];
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        autosize: false,
        labelWidth: 200,
        height: 500,
		disableok: true,
        icon: 'icon-etiquetas',
        width: 700,
        title: _s('Imprimir etiquetas'),
        fn_ok: function(){
            var asig = '';
            store.each(function(e){
                console.dir(e);
                asig += e.data.nIdLibro + '##' + e.data.fPrecioAsignado + ';';
            });
            var act = actualizar.getValue();
            Ext.app.callRemote({
                url: site_url('compras/albaranentrada/precios'),
                params: {
                    id: id,
                    precios: asig,
                    etq: act
                },
                fnok: function(obj){
                    form.close();
                }
            });
        }
    });
    
    /*store.baseParams = {
     id: id
     };
     store.load();*/
    report.store.load();
    form.show();
    
    return;
})();
