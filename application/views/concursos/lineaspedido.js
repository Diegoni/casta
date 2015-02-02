(function(){
    /*-------------------------------------------------------------------------
     * Datos Formulario
     *-------------------------------------------------------------------------
     */
    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = 'iconoLineasPedidoConcursoTab';
    
    if (title == '') 
        title = _s('Líneas de pedido concursos');
    if (form_id == '') 
        form_id = Ext.app.createId();

    // Búsqueda
    var fn_open = function(id) {}

    var grid_search_m = search_lineaspedidoconcurso(form_id, fn_open);

    var concursos = new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('concursos/concurso/search'),
        name : 'concurso',
        anchor : '100%',
        label : _s('nIdConcurso')
    }));

    //console.dir(grid_search_m.addcontrols);
    var searchcontrols = [{
                allowBlank: false,    
                fieldLabel: _s('nIdLibro'),
                name : "nIdLibro",
                xtype: "textfield"
            }];
    Ext.each(grid_search_m.addcontrols, function(i, item) { 
        if (in_array(i.name, ['nIdBiblioteca', 'nIdSala', 
            'nIdEstado', 'cISBN', 'cEAN', 'cAutores', 'cTitulo', 
            'cEditorial1a', 'cCDU' , 'cCDU2']))
            searchcontrols.push(i);
        if (i.name == 'cTitulo') {
            searchcontrols.push({
                allowBlank: false,    
                fieldLabel: _s('Titulo Asig.'),
                name : "cTitulo2",
                xtype: "textfield"
            });
        }
        if (i.name == 'cAutores') {
            searchcontrols.push({
                allowBlank: false,    
                fieldLabel: _s('Autores Asig.'),
                name : "cAutores2",
                xtype: "textfield"
            });
            searchcontrols.push({
                allowBlank: false,    
                fieldLabel: _s('cProveedor'),
                name : "cProveedor",
                xtype: "textfield"
            });
            searchcontrols.push({
                allowBlank: false,    
                fieldLabel: _s('cEditorial'),
                name : "cEditorial",
                xtype: "textfield"
            });
        }
    });

    /**
     * Formulario
     */
    var form = {
        title: title,
        id: form_id,
        region: 'center',
        closable: true,
        iconCls: icon,
        layout: 'border',
        tbar: [concursos, {
            iconCls : "icon-add",
            text : _s('Añadir nuevo artículo'),
            handler : function() {
                crearlibro();
            }
        }],
        items: Ext.app.formSearchForm({
            grid: grid_search_m,
            searchcontrols: searchcontrols,
            audit: false,
            id_grid: form_id + '_g_search_grid',
            fn_pre : function(fields){
                var idc = concursos.getValue();
                try {
                    Ext.app.set_config('bp.concursos.concursodefecto', concursos.getValue(), 'user');
                } catch(e) {}
                if (idc > 0)
                    fields['idc'] = idc.toString();

                return fields;
            }
        })
    };

    var grid = Ext.getCmp(form_id + '_g_search_grid');
    var cm_lineas = fn_contextmenu();
    var contextmenu = Ext.app.addContextMenuEmpty(grid, cm_lineas);
    cm_lineas.setContextMenu(contextmenu)

    var temp = new Ext.form.TextField();
    temp.refresh = function() {
        grid.getEl().unmask();
        grid.store.load();
        grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
    }

    var accion = function(url, field, grid, params, refresh) {
        var codes = Ext.app.gridGetChecked(grid, field);
        if(codes == null) {
            var record = cm_lineas.getItemSelect();
            if(record != null) {
                codes = record.data[field] + ';';
            }
        }
        if(codes == null) {
            Ext.app.msgFly(title, _s('no-libros-marcados'));
            return;
        }

        if (params == null) params = {};

        if (refresh == null) refresh = true;

        params['id'] = codes;
        params['cmpid'] = temp.id;
        if (refresh) grid.getEl().mask();
        Ext.app.callRemote({
            url : url,
            timeout : false,
            wait : true,
            params : params,
            fnok : function() {
                if (refresh) {
                    grid.getEl().unmask();
                    grid.store.load();
                    grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
                }
            },
            fnnok : function() {
                if (refresh) grid.getEl().unmask();
            }
        });
    }

    var Alternativa = function() {
        var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
            allowBlank: false,
            url: site_url('catalogo/articulo/search'),
            label: _s('Artículo'),
            anchor: '90%',
            fnselect:  function(id) {
                Ext.app.callRemote({
                    url: site_url('compras/reposicion/get_datos_venta'),
                    params: {
                        id: id
                    },
                    nomsg: true,
                    fnok: function(obj) {
                        if (obj.success) {
                            var detailEl = Ext.getCmp("_info").body;
                            detailEl.applyStyles({
                                'background-color': '#FFFFFF'
                            });

                            detailEl.hide().update(obj.message).slideIn('l', {
                                stopFx: true,
                                duration: .1
                            });
                        } else {
                            Ext.app.msgError(title, _s('registro_error') + ': ' +
                            obj.message);
                        }
                    }
                });            
            }
        }));


        var controls = [ctl, {
            //xtype: 'iframepanel',
            height: Ext.app.REPOINFOHEIGHT,
            width: Ext.app.PEDIRWIDTH - 35,
            autoScroll: true,
            id: '_info'
        }];
        var form2 = Ext.app.formStandarForm({
            controls: controls,
            width: Ext.app.PEDIRWIDTH,
            icon: 'icon-alternativa',
            title: _s('Alternativa'),
            fn_ok: function() {                
                accion(site_url('concursos/pedidoconcursolinea/alternativa'), 'nIdLineaPedidoConcurso', grid, {nuevo: ctl.getValue()});
            }
        });

        form2.show();
    }


    var cambiararticulo = function() {
        var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
            allowBlank: false,
            url: site_url('catalogo/articulo/search'),
            label: _s('Artículo'),
            anchor: '90%'
        }));
        var controls = [ctl];
        var form2 = Ext.app.formStandarForm({
            controls: controls,
            icon: 'icon-change',
            title: _s('Cambiar artículo'),
            fn_ok: function() {                
                accion(site_url('concursos/pedidoconcursolinea/cambiar'), 'nIdLineaPedidoConcurso', grid, {nuevo: ctl.getValue()});
            }
        });
        form2.show();
    }

    var cambiarISBN = function() {
        var ctl = new Ext.form.TextField();
        var controls = [ctl];
        var form2 = Ext.app.formStandarForm({
            controls: controls,
            icon: 'icon-etiquetas',
            title: _s('Cambiar ISBN'),
            fn_ok: function() {                
                var record = cm_lineas.getItemSelect();
                if(record != null) {
                    Ext.app.callRemote({
                        url : site_url('catalogo/articulo/upd'),
                        params: {
                            id:  record.data.nIdLibro,
                            cISBN: ctl.getValue()
                        },
                        fnok : function() {
                            grid.store.load();
                            grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
                        }
                    });
                }
            }
        });
        form2.show();
    }

    var unificar = function() {
        var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
            allowBlank: false,
            url: site_url('catalogo/articulo/search'),
            label: _s('Artículo'),
            anchor: '90%'
        }));
        var controls = [ctl];
        var form2 = Ext.app.formStandarForm({
            controls: controls,
            icon: 'icon-unificar',
            title: _s('Unificar'),
            fn_ok: function() {                
                var record = cm_lineas.getItemSelect();
                if(record != null) {
                    Ext.app.callRemote({
                        url : site_url('catalogo/articulo/unificar'),
                        params: {
                            id2:  record.data.nIdLibro,
                            id1: ctl.getValue()
                        },
                        fnok : function() {
                            grid.store.load();
                            grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
                        }
                    });
                }
            }
        });
        form2.show();
    }

    var origenes = [];
    var origen2 = Ext.app.createStore({
        url: site_url('catalogo/articuloseccion/get_list'),
        model: [{
            name: 'id'
        }, {
            name: 'nIdLibro'
        }, {
            name: 'nIdSeccion'
        }, {
            name: 'nStock'
        }, {
            name: 'nStockFirme'
        }, {
            name: 'nStockDeposito'
        }, {
            name: 'cNombre'
        }]
    });
    var moverlibro = function() {
        var origen = new Ext.form.ComboBox(Ext.app.combobox({
            id: id + '_origen',
            anchor: '70%',
            label: _s('Origen')
        }));
        
        var destinocrear = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('generico/seccion/search'),
            anchor: '70%',
            id: id + '_crear',
            label: _s('Nueva')
        }));

        var do_accion = function() {
            if (ctl.getValue() < 1) {
                ctl.focus();
                return false;
            }
            if (origen.getValue() < 1) {
                origen.focus();
                return false;
            }
            if (destinocrear.getValue() < 1) {
                destinocrear.focus();
                return false;
            }
            try {
                Ext.app.set_config('bp.concursos.mover.secciondefecto', destinocrear.getValue(), 'user');
            } catch(e) {}
            accion(site_url('concursos/pedidoconcursolinea/mover'), 'nIdLineaPedidoConcurso', grid, {
                    idl: ctl.getValue(),
                    origen: origen.getValue(),
                    destino: destinocrear.getValue()
                });            
        }

        origen.on('keypress', function(f, e){
            if (e.getKey() == e.ENTER) {
                destinocrear.focus();
            }
        });
        
        destinocrear.on('keypress', function(f, e){
            if (e.getKey() == e.ENTER) {
                do_accion();
                form2.close();
            }
        });

        var select = function(id) {
            origen2.load({
                params: {
                    where: 'nIdLibro=' + id
                },
                callback: function(data){
                    var last_origen = origen.getValue();
                    var new_origen = null;
                    origen.store.removeAll();
                    Ext.each(data, function(item){
                        var text = item.data.cNombre;
                        var id = item.data.nIdSeccion;
                        var stk = parseInt(item.data.nStockFirme) + parseInt(item.data.nStockDeposito);
                        if (stk > 0) {
                            if (item.data.id == last_origen) 
                                new_origen = last_origen;
                            if (new_origen != last_origen) {
                                if (in_array(id, origenes)) 
                                    new_origen = id;
                            }
                            if (new_origen == null) 
                                new_origen = id;
                            
                            Ext.app.comboAdd(origen.store, id, text + '(' + stk + ')');
                        }
                    });
                    origen.setValue(new_origen);
                    origen.focus();
                }
            });
        }

        var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
            allowBlank: false,
            url: site_url('catalogo/articulo/search'),
            label: _s('Artículo'),
            anchor: '100%',
            fnselect: select
        }));
        var controls = [ctl, origen, destinocrear];

        destinocrear.store.load({
            callback: function () {
                var d = parseInt(Ext.app.get_config('bp.concursos.mover.secciondefecto'));
                if (d > 0)
                    destinocrear.setValue(parseInt(d));
            }
        });
        var form2 = Ext.app.formStandarForm({
            controls: controls,
            icon: 'icon-change',
            title: _s('Cambiar artículo'),
            fn_ok: function() {                
                do_accion();
            }
        });
        form2.show();
    }

    var crearlibro = function() {
        var origen = new Ext.form.ComboBox(Ext.app.combobox({
            id: id + '_origen',
            anchor: '70%',
            label: _s('Origen')
        }));
        
        var destinocrear = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('generico/seccion/search'),
            anchor: '70%',
            id: id + '_crear',
            label: _s('Nueva')
        }));

        var sala = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('concursos/sala/search'),
            anchor: '70%',
            id: id + '_sala',
            label: _s('Sala')
        }));

        var biblioteca = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('concursos/biblioteca/search'),
            anchor: '70%',
            id: id + '_biblio',
            label: _s('Biblioteca')
        }));

        var do_accion = function() {
            if (ctl.getValue() < 1) {
                ctl.focus();
                return false;
            }
            /*if (origen.getValue() < 1) {
                origen.focus();
                return false;
            }*/
            if (destinocrear.getValue() < 1) {
                destinocrear.focus();
                return false;
            }
            if (biblioteca.getValue() < 1) {
                biblioteca.focus();
                return false;
            }
            if (sala.getValue() < 1) {
                sala.focus();
                return false;
            }
            try {
                Ext.app.set_config('bp.concursos.mover.secciondefecto', destinocrear.getValue(), 'user');
                Ext.app.set_config('bp.concursos.mover.biliotecadefecto', biblioteca.getValue(), 'user');
            } catch(e) {}

            Ext.app.callRemote({
                url : site_url('concursos/pedidoconcursolinea/crear'),
                timeout : false,
                wait : true,
                params : {
                        idl: ctl.getValue(),
                        origen: origen.getValue(),
                        destino: destinocrear.getValue(),
                        sala: sala.getValue(),
                        biblioteca: biblioteca.getValue()
                    },
                fnok : function() {
                    origen.reset();
                    //destinocrear.reset();
                    ctl.focus();
                    ctl.reset();
                }
            });
            return true;
        }

        origen.on('keypress', function(f, e){
            if (e.getKey() == e.ENTER) {
                destinocrear.focus();
            }
        });
        
        destinocrear.on('keypress', function(f, e){
            if (e.getKey() == e.ENTER) {
                biblioteca.focus();
            }
        });
        biblioteca.on('keypress', function(f, e){
            if (e.getKey() == e.ENTER) {
                sala.focus();
            }
        });

        sala.on('keypress', function(f, e){
            if (e.getKey() == e.ENTER) {
                do_accion();
                //form2.close();
            }
        });

        var select = function(id) {
            origen2.load({
                params: {
                    where: 'nIdLibro=' + id
                },
                callback: function(data){
                    var last_origen = origen.getValue();
                    var new_origen = null;
                    origen.store.removeAll();
                    Ext.each(data, function(item){
                        var text = item.data.cNombre;
                        var id = item.data.nIdSeccion;
                        var stk = parseInt(item.data.nStockFirme) + parseInt(item.data.nStockDeposito);
                        if (stk > 0) {
                            if (item.data.id == last_origen) 
                                new_origen = last_origen;
                            if (new_origen != last_origen) {
                                if (in_array(id, origenes)) 
                                    new_origen = id;
                            }
                            if (new_origen == null) 
                                new_origen = id;
                            
                            Ext.app.comboAdd(origen.store, id, text + '(' + stk + ')');
                        }
                    });
                    origen.setValue(new_origen);
                    origen.focus();
                }
            });
        }

        var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
            allowBlank: false,
            url: site_url('catalogo/articulo/search'),
            label: _s('Artículo'),
            anchor: '100%',
            fnselect: select
        }));
        var controls = [ctl, origen, destinocrear, biblioteca, sala];

        sala.store.load();
        destinocrear.store.load({
            callback: function () {
                var d = parseInt(Ext.app.get_config('bp.concursos.mover.secciondefecto'));
                if (d > 0)
                    destinocrear.setValue(parseInt(d));
            }
        });
        biblioteca.store.load({
            callback: function () {
                var d = parseInt(Ext.app.get_config('bp.concursos.mover.biliotecadefecto'));
                if (d > 0)
                    biblioteca.setValue(parseInt(d));
            }
        });
        var form2 = Ext.app.formStandarForm({
            controls: controls,
            icon: 'icon-add',
            title: _s('Añadir nuevo artículo'),
            fn_ok: function(res) {
                do_accion();
                return false;
            }
        });
        form2.show();
    }

    var m_articulo = contextmenu.add({
        text : _s('Ver artículo'),
        handler : function() {
            var record = cm_lineas.getItemSelect();
            if(record != null) {
                Ext.app.execCmd({
                    url : site_url('catalogo/articulo/index/' + record.data.nIdLibro)
                });
            }
        },
        iconCls : 'iconoArticulos'
    });
    var m_eliminar = contextmenu.add({
        iconCls : "icon-ver",
        text : _s('Ver original'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/ver'), 'nIdLineaPedidoConcurso', grid);
        }
    });
    var m_estado = contextmenu.add({
        iconCls : "icon-statistics",
        text : _s('Cambios estado'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/estado'), 'nIdLineaPedidoConcurso', grid);
        }
    });

    contextmenu.add('-');
    var m_isbn = contextmenu.add({
        iconCls : "icon-etiquetas",
        text : _s('Cambiar ISBN'),
        handler : function() {
            cambiarISBN();
        }
    });
    var m_unificar = contextmenu.add({
        iconCls : "icon-unificar",
        text : _s('Unificar'),
        handler : function() {
            unificar();
        }
    });
    var m_isbn = contextmenu.add({
        iconCls : "icon-alternativa",
        text : _s('Alternativa'),
        handler : function() {
            Alternativa();
        }
    });

    contextmenu.add('-');
    var m_pedidoproveedor = contextmenu.add({
        text : _s('Ver pedido proveedor'),
        handler : function() {
            var record = cm_lineas.getItemSelect();
            if(record != null) {
                Ext.app.execCmd({
                    url : site_url('compras/pedidoproveedor/index/' + record.data.nIdPedidoProveedor)
                });
            }
        },
        iconCls : 'iconoPedidoProveedor'
    });

    var m_pedir = contextmenu.add({
        text : _s('Pedir'),
        handler : function() {
            var record = cm_lineas.getItemSelect();
            if(record != null)
                concurso = record.data.nIdConcurso;
            accion(site_url('concursos/concurso/pedirproveedor'), 'nIdLineaPedidoConcurso', grid, {concurso: concurso}, false);
        },
        iconCls : 'icon-pedir'
    });

    var m_albaranentrada = contextmenu.add({
        text : _s('Ver albarán de entrada'),
        handler : function() {
            var record = cm_lineas.getItemSelect();
            if(record != null) {
                Ext.app.execCmd({
                    url : site_url('compras/albaranentrada/index/' + record.data.nIdAlbaranEntrada)
                });
            }
        },
        iconCls : 'iconoAlbaranEntrada'
    });
    var m_albaransalida = contextmenu.add({
        text : _s('Ver albarán de salida'),
        handler : function() {
            var record = cm_lineas.getItemSelect();
            if(record != null) {
                Ext.app.execCmd({
                    url : site_url('ventas/albaransalida/index/' + record.data.nIdAlbaranSalida)
                });
            }
        },
        iconCls : 'iconoAlbaranSalida'
    });

    contextmenu.add('-');

    var m_cancelar = contextmenu.add({
        iconCls : "icon-cancel",
        text : _s('Cancelar'),
        handler : function() {
            accion(site_url('compras/pedidoproveedorlinea/cancelar'), 'nIdLineaPedidoProveedor', grid);
        }
    });

    var m_cancelaravisar = contextmenu.add({
        iconCls : "iconoCancelacionPedidoProveedor",
        text : _s('Cancelar y avisar'),
        handler : function() {
            accion(site_url('compras/cancelacion/crear'), 'nIdLineaPedidoProveedor', grid);
        }
    });

    var m_eliminarpedido = contextmenu.add({
        iconCls : "icon-delete",
        text : _s('Eliminar del pedido'),
        handler : function() {
            accion(site_url('compras/pedidoproveedorlinea/del'), 'nIdLineaPedidoProveedor', grid);
        }
    });

    contextmenu.add('-');
    var m_cambiar = contextmenu.add({
        iconCls : "icon-change",
        text : _s('Cambiar artículo'),
        handler : function() {
            cambiararticulo();
        }
    });

    var m_duplicar = contextmenu.add({
        iconCls : "icon-duplicate",
        text : _s('Duplicar'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/duplicar'), 'nIdLineaPedidoConcurso', grid);
        }
    });

    var m_asignarseccion = contextmenu.add({
        iconCls : "iconoSeccionMover",
        text : _s('Mover libro sección'),
        handler : function() {
            moverlibro();
        }
    });
    contextmenu.add('-');
    var m_agotar = contextmenu.add({
        iconCls : "icon-status-1",
        text : _s('Agotado'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/agotado'), 'nIdLineaPedidoConcurso', grid);
        }
    });
    var m_descatalogar = contextmenu.add({
        iconCls : "icon-status-3",
        text : _s('Descatalogado'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/descatalogado'), 'nIdLineaPedidoConcurso', grid);
        }
    });
    var m_reimpresion = contextmenu.add({
        iconCls : "icon-status-2",
        text : _s('En reimpresión'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/reimpresion'), 'nIdLineaPedidoConcurso', grid);
        }
    });
    var m_enproceso = contextmenu.add({
        iconCls : "icon-tarea-1",
        text : _s('En proceso'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/enproceso'), 'nIdLineaPedidoConcurso', grid);
        }
    });
    var m_descartar = contextmenu.add({
        iconCls : "icon-descartar",
        text : _s('Descartar'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/descartar'), 'nIdLineaPedidoConcurso', grid);
        }
    });
    contextmenu.add('-');
    var m_desvincular = contextmenu.add({
        iconCls : "icon-unlink",
        text : _s('Desvincular'),
        handler : function() {
            accion(site_url('concursos/pedidoconcursolinea/desvincular'), 'nIdLineaPedidoConcurso', grid);
        }
    });
    contextmenu.add('-');
    var m_eliminar = contextmenu.add({
        iconCls : "icon-delete",
        text : _s('Eliminar'),
        handler : function() {
            Ext.Msg.show({
                title : _s('Eliminar'),
                buttons : Ext.MessageBox.YESNOCANCEL,
                icon : Ext.Msg.QUESTION,
                msg : _s('elm-registro'),
                fn : function(btn, text) {
                    if(btn == 'yes') {
                        accion(site_url('concursos/pedidoconcursolinea/del'), 'nIdLineaPedidoConcurso', grid);
                    }
                }
            });            
        }
    });

    /*var m_unificar = contextmenu.add({
        iconCls : "iconoUnficarArticulo",
        text : _s('Unificar'),
        handler : function() {
            accion(site_url('concursos/lineapedido/unificar'), 'nIdLibro', grid);
        }
    });*/

    var fn_check_menu = function(item) {
        (item.data.nIdPedidoProveedor > 0) ? m_pedidoproveedor.enable() : m_pedidoproveedor.disable();
        (item.data.nIdAlbaranEntrada > 0) ? m_albaranentrada.enable() : m_albaranentrada.disable();
        (item.data.nIdAlbaranSalida > 0) ? m_albaransalida.enable() : m_albaransalida.disable();
        (item.data.nIdLibro > 0) ? m_articulo.enable() : m_articulo.disable();
        (item.data.nIdLibro > 0) ? m_unificar.enable() : m_unificar.disable();
        (item.data.nIdLibro > 0) ? m_isbn.enable() : m_isbn.disable();
        (item.data.nIdLibro > 0) ? m_estado.enable() : m_estado.disable();
        (item.data.nIdLineaPedidoProveedor > 0 && item.data.nIdEstado==5) ? m_cancelar.enable() : m_cancelar.disable();
        (item.data.nIdLineaPedidoProveedor > 0 && item.data.nIdEstado==5) ? m_cancelaravisar.enable() : m_cancelaravisar.disable();
        (item.data.nIdLineaPedidoProveedor > 0 && item.data.nIdEstado==22) ? m_eliminarpedido.enable() : m_eliminarpedido.disable();
        (item.data.nIdEstado == 1) ? m_pedir.enable() : m_pedir.disable();
        (item.data.nIdLibro > 0 /*&& item.data.nIdEstado == 1*/) ? m_cambiar.enable() : m_cambiar.disable();
        (item.data.nIdLibro > 0 && (item.data.nIdEstado == 1 || item.data.nIdEstado == 22 || item.data.nIdEstado == 22)) ? m_asignarseccion.enable() : m_asignarseccion.disable();
        (item.data.nIdLibro > 0 && item.data.nIdEstado == 2) ? m_desvincular.enable() : m_desvincular.disable();
        (item.data.nIdLibro > 0 && (item.data.nIdEstado == 1)) ? m_eliminar.enable() : m_eliminar.disable();
        (item.data.nIdEstado == 1 || item.data.nIdEstado == 22 || item.data.nIdEstado == 5) ? m_agotar.enable() : m_agotar.disable();
        (item.data.nIdEstado == 1 || item.data.nIdEstado == 22 || item.data.nIdEstado == 5) ? m_descatalogar.enable() : m_descatalogar.disable();
        (item.data.nIdEstado == 1 || item.data.nIdEstado == 22 || item.data.nIdEstado == 5) ? m_reimpresion.enable() : m_reimpresion.disable();
        (item.data.nIdEstado == 11 || item.data.nIdEstado == 6 || item.data.nIdEstado == 23) ? m_enproceso.enable() : m_enproceso.disable();
        (item.data.nIdEstado == 1 || item.data.nIdEstado == 22 || item.data.nIdEstado == 5) ? m_descartar.enable() : m_descartar.disable();
        //(item.data.nIdLibro > 0) ? m_unificar.enable() : m_unificar.disable();
    }
    cm_lineas.setCheckMenu(fn_check_menu);

    var col = grid.getColumnModel();
    try {
        //console.dir(col);
        col.setHidden(5, true);
        col.setHidden(6, true);
        col.setHidden(7, true);
        col.setHidden(8, true);
        col.setHidden(9, true);
        col.setHidden(10, true);
        col.setHidden(11, true);
        col.setHidden(12, true);
        col.setHidden(13, true);
        col.setHidden(14, true);
        col.setHidden(15, true);
        col.setHidden(19, true);
        col.setHidden(23, true);
        col.setHidden(24, true);
        //col.setHidden(25, true);
        col.setHidden(26, true);
        col.setHidden(27, true);
    } catch (e) {
        console.dir(e);
    }
    col.setColumnWidth(1, Ext.app.TAM_COLUMN_TEXT*2);
    col.setColumnWidth(2, Ext.app.TAM_COLUMN_TEXT);
    col.setColumnWidth(4, Ext.app.TAM_COLUMN_TEXT);

    var renderTitulo = function(r) {
        var t ='';
        t += '<span style="color: green;font-weight: bold;">' + r.data.cTitulo + '</span><br/>'; 
        var d = [];
        if (r.data.tTitolVolum != null && r.data.tTitolVolum != '') d.push(r.data.tTitolVolum); 
        if (r.data.tTitolUniforme != null && r.data.tTitolUniforme != '') d.push(r.data.tTitolUniforme); 
        if (r.data.cAutores != null && r.data.cAutores != '') d.push(r.data.cAutores); 
        if (d.length > 0)
            t += '<span style="color: grey;font-style: italic;">' + d.join(' | ') + '</span><br/>'; 

        d = [];
        if (r.data.cISBN != null && r.data.cISBN != '') d.push(r.data.cISBN); 
        if (r.data.cEAN != null && r.data.cEAN != '') d.push(r.data.cEAN); 
        if (r.data.cEdicion != null && r.data.cEdicion != '') d.push(r.data.cEdicion);
        t += d.join(' | ') +'<br/>';

        t += '<span style="color: blue;font-weight: bold;">' + r.data.cTitulo2 + '</span><br/>'; 
        if (r.data.cAutores2 != null && r.data.cAutores2 != '') 
            t += '<span style="color: grey;font-style: italic;">' + r.data.cAutores2 + '</span><br/>'; 
        d = [];
        d.push(r.data.nIdLibro);
        if (r.data.cISBN2 != null && r.data.cISBN2 != '') d.push(r.data.cISBN2); 
        if (r.data.nEAN != null && r.data.nEAN != '') d.push(r.data.nEAN); 
        if (r.data.cEditorial != null && r.data.cEditorial != '') d.push(r.data.cEditorial); 
        if (r.data.cCUser2 != null && r.data.cCUser2 != '') d.push(r.data.cCUser2); 
        if (r.data.dCreacion2 != null && r.data.dCreacion2 != '') d.push(Ext.app.renderDate(r.data.dCreacion2)); 
        if (r.data.nIdAlternativa != null ) d.push('<span style="color: #ff5200;font-weight: bold;">' + sprintf(_s('concurso-alternativa'), r.data.nIdAlternativa) + '</span>'); 
        if (r.data.nIdCambioLibro != null ) d.push('<span style="color: #ff5200;font-weight: bold;">' + sprintf(_s('concurso-cambio-otro'), r.data.nIdCambioLibro) + '</span>'); 
        if (r.data.nIdAlbaranSalida > 0 ) d.push('<span style="color:  #000715;font-weight: bold;">' + _s('AS:') + r.data.nIdAlbaranSalida + '</span>'); 
        if (r.data.nIdAlbaranEntrada > 0 ) d.push('<span style="color: #000715;font-weight: bold;">' + _s('AE:') + r.data.nIdAlbaranEntrada + '</span>'); 
        if (r.data.nIdPedidoProveedor > 0 ) d.push('<span style="color: #000715 ;font-weight: bold;">' + _s('PP:') + r.data.nIdPedidoProveedor + '</span>'); 
        t += d.join(' | ');
        return t;
    }
    grid.viewConfig = {
            enableRowBody : true,
            showPreview : true,
            getRowClass : function(r, rowIndex, rowParams, store) {
                if(this.showPreview && r != null) {
                    //console.dir(r.data);                    
                    rowParams.body = renderTitulo(r);
                }
                if (r.data.nIdEstado==1 && r.data.nIdLibro==null) return 'cell-concurso-linea_nolibro';
                return 'cell-concurso-linea_' + r.data.nIdEstado;
            }
        }
    grid.preview = renderTitulo;
    concursos.store.load({
        callback: function () {
            var d = parseInt(Ext.app.get_config('bp.concursos.concursodefecto'));
            if (d > 0)
                concursos.setValue(parseInt(d));
        }
    });

    return form;
})();

