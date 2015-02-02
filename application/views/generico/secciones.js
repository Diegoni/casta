(function(){
    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    if (title == '') 
        title = _s('Secciones');
    if (icon == '') 
        icon = 'iconoSeccionTab';
    var ctxRow = null;
    var reload = function(){
        var f = Ext.getCmp(form_id + "_tree");
        f.root.reload();
    }
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
            text: _s('Cambiar nombre'),
            handler: function(){
                if (ctxRow) {
                    Ext.Msg.prompt(title, _s('Nuevo nombre'), function(ok, v){
                        if (ok != 'ok') 
                            return;
                        Ext.app.callRemote({
                            url: site_url('generico/seccion/upd'),
                            params: {
                                id: ctxRow.attributes.nIdSeccion,
                                cNombre: v
                            },
                            fnok: function(){
                                reload();
                            }
                        });
                    }, null, null, ctxRow.attributes.cNombre);
                }
            },
            iconCls: 'icon-edit'
        }, '-', {
            text: _s('Bloquear'),
            id: form_id + '_lock',
            handler: function(){
                if (ctxRow) {
                    Ext.app.callRemote({
                        url: site_url('generico/seccion/upd'),
                        params: {
                            id: ctxRow.attributes.nIdSeccion,
                            bBloqueada: !ctxRow.attributes.bBloqueada
                        },
                        fnok: function(){
                            reload();
                        }
                    });
                }
            },
            iconCls: 'icon-lock'
        }, {
            text: _s('Desbloquear'),
            id: form_id + '_unlock',
            handler: function(){
                if (ctxRow) {
                    Ext.app.callRemote({
                        url: site_url('generico/seccion/upd'),
                        params: {
                            id: ctxRow.attributes.nIdSeccion,
                            bBloqueada: !ctxRow.attributes.bBloqueada
                        },
                        fnok: function(){
                            reload();
                        }
                    });
                }
            },
            iconCls: 'icon-unlock'
        }, {
            text: _s('bMostrarWeb'),
            id: form_id + '_mostrar',
            handler: function(){
                if (ctxRow) {
                    Ext.app.callRemote({
                        url: site_url('generico/seccion/upd'),
                        params: {
                            id: ctxRow.attributes.nIdSeccion,
                            bWeb: !ctxRow.attributes.bWeb
                        },
                        fnok: function(){
                            reload();
                        }
                    });
                }
            },
            iconCls: 'icon-mostrar-si'
        }, {
            text: _s('No Mostrar Web'),
            id: form_id + '_nomostrar',
            handler: function(){
                if (ctxRow) {
                    Ext.app.callRemote({
                        url: site_url('generico/seccion/upd'),
                        params: {
                            id: ctxRow.attributes.nIdSeccion,
                            bWeb: !ctxRow.attributes.bWeb
                        },
                        fnok: function(){
                            reload();
                        }
                    });
                }
            },
            iconCls: 'icon-mostrar-no'
        }, '-', {
            text: _s('Añadir'),
            handler: function(){
                add((ctxRow) ? ctxRow.attributes.nIdSeccion : null);
            },
            iconCls: 'icon-add'
        }, '-', {
            text: _s('Eliminar'),
            handler: function(){
                if (ctxRow) {
                    Ext.app.callRemoteAsk({
                        url: site_url('generico/seccion/del/'),
                        title: title,
                        askmessage: _s('elm-registro'),
                        params: {
                            id: ctxRow.attributes.nIdSeccion
                        },
                        fnok: function(){
                            reload();
                        }
                    });
                }
            },
            iconCls: 'icon-delete'
        }]
    });
    
    var secciones = new Ext.Panel({
        layout: 'border',
        title: title,
        id: form_id,
        iconCls: icon,
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        items: [new Ext.ux.tree.TreeGrid({
            region: 'center',
            id: form_id + "_tree",
            autoScroll: true,
            useArrows: true,
            loadMask: true,
            rootVisible: false,
            columns: [{
                header: _s('Sección'),
                width: Ext.app.TAM_COLUMN_TEXT * 2,
                dataIndex: 'text'
            }, {
                header: _s('Id'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'nIdSeccion'
            }, {
                header: _s('bBloqueada'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'bBloqueada',
                tpl: new Ext.XTemplate('{bBloqueada:this.format}', {
                    format: function(v){
                        return (v == 0) ? '' : '<span class="icon-lock" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                    }
                })
            }, {
                header: _s('bWeb'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'bWeb',
                tpl: new Ext.XTemplate('{bWeb:this.format}', {
                    format: function(v){
                        return '<span class="' + ((v == 0) ? 'icon-mostrar-no' : 'icon-mostrar-si') + '" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                    }
                })
            }, {
                header: _s('cCodigo'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cCodigo'
            }, {
                header: _s('nHijos'),
                width: Ext.app.TAM_COLUMN_NUMBER,
                dataIndex: 'nHijos'
            }, {
                header: _s('cCUser'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cCUser'
            }, {
                header: _s('dCreacion'),
                width: Ext.app.TAM_COLUMN_DATE,
                dateFormat: 'timestamp',
                renderer: Ext.app.renderDate,
                sortable: true,
                dataIndex: 'dCreacion'
            }, {
                header: _s('cAUser'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cAUser'
            }, {
                header: _s('dAct'),
                width: Ext.app.TAM_COLUMN_DATE,
                dataIndex: 'dAct'
            }],
            listeners: {
                contextmenu: function(node, event){
                    node.select();
                    ctxRow = node;
                    var m = Ext.getCmp(form_id + '_lock');
                    var m2 = Ext.getCmp(form_id + '_unlock');
                    if (ctxRow.attributes.bBloqueada) {
                        m.setVisible(false);
                        m2.setVisible(true);
                    }
                    else {
                        m2.setVisible(false);
                        m.setVisible(true);
                    }
                    m = Ext.getCmp(form_id + '_mostrar');
                    m2 = Ext.getCmp(form_id + '_nomostrar');
                    if (ctxRow.attributes.bWeb) {
                        m.setVisible(false);
                        m2.setVisible(true);
                    }
                    else {
                        m2.setVisible(false);
                        m.setVisible(true);
                    }
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
                text: _s('Añadir'),
                tooltip: _s('cmd-addregistro'),
                handler: function(){
                    add(null);
                },
                iconCls: 'icon-add'
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
                dataUrl: site_url('generico/seccion/get_tree')
            }),
            root: new Ext.tree.AsyncTreeNode({
                expanded: true
            })
        })],
    
    });
    
    return secciones;
})();
