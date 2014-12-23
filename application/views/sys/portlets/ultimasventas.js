var Portlet_ultimasventas_this = null;

function Portlet_ultimasventas_timer(){
    //console.log('En timer..' + Ext.app.FACTURAREFRESH);
    if (Portlet_ultimasventas_this != null) 
        Portlet_ultimasventas_this.load();
}

function Portlet_ultimasventas(){
    return {
        grid: null,
        store: null,
        rt: null,
        html: function(params){
            return null;
        },
        
        init: function(params){
        
            var columns = [{
                header: _s('Id'),
                width: Ext.app.TAM_COLUMN_ID,
                sortable: true,
                dataIndex: 'nIdFactura'
            }, {
                header: "Fecha",
                width: Ext.app.TAM_COLUMN_DATE,
                dateFormat: 'timestamp',
                sortable: true,
                renderer: Ext.app.renderDate,
                dataIndex: 'dFecha'
            }, {
                header: _s('fImporte'),
                align: 'right',
                width: Ext.app.TAM_COLUMN_NUMBER,
                dataIndex: '_fTotal',
                renderer: Ext.app.numberFormatter,
                sortable: true
            }, {
                header: _s('Factura'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cNumero',
                id: 'descripcion',
                sortable: true
            }, {
                header: _s('Cliente'),
                width: Ext.app.TAM_COLUMN_TEXT,
                sortable: true,
                dataIndex: 'cCliente'
            }, {
                header: _s('cCUser'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cCUser',
                sortable: true
            }, {
                header: _s('dCreacion'),
                width: Ext.app.TAM_COLUMN_DATE,
                dateFormat: 'timestamp',
                renderer: Ext.app.renderDate,
                dataIndex: 'dCreacion',
                sortable: true
            }];
            
            // Store de datos
            var storefields = {
                id: 'id',
                model: [{
                    name: 'nIdFactura'
                }, {
                    name: 'cNumero'
                }, {
                    name: 'cCliente'
                }, {
                    name: 'fTotal'
                }, {
                    name: 'dFecha'
                }, {
                    name: 'cCUser'
                }, {
                    name: 'dCreacion'
                }, {
                    name: 'cAUser'
                }, {
                    name: 'dAct'
                }]
            };
            
            this.rt = Ext.data.Record.create(storefields);
            limit = (params != null && params[0] != null) ? params[0] : Ext.app.AUTOCOMPLETELISTSIZE;
            
            this.store = new Ext.data.Store({
                url: site_url('ventas/factura/get_last'),
                baseParams: {
                    sort: 'dCreacion',
                    dir: 'desc',
                    start: 0,
                    limit: limit,
                    //where: 'nIdEstado != 1'
                },
                reader: new Ext.data.ArrayReader({
                    idIndex: 0
                }, this.rt)
            });
            
            this.grid = new Ext.grid.GridPanel({
                store: this.store,
                columns: columns,
                loadMask: {
                    msg: _s('Cargando')
                },
                stripeRows: true,
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                //autoExpandColumn: 'id',
                height: 250,
                width: 600
            });
            
            var t = this;
            function add_item(data){
                t.store.insert(0, new t.rt({
                    'nIdFactura': data.id,
                    'cCliente': data.data.cCliente,
                    'cNumero': data.numero,
                    '_fTotal': data.importe,
                    'dFecha': data.data.dFecha,
                    'cCUser': data.data.cCUser,
                    'cAUser': data.data.cAUser,
                    'dCreacion': data.data.dCreacion,
                    'dAct': data.data.dAct
                }));
            }
            Ext.app.eventos.observe('factura.close', add_item);
            
            this.grid.on('dblclick', function(e){
                var sm = t.grid.getSelectionModel();
                if (sm.hasSelection()) {
                    var sel = sm.getSelected();
                    Ext.app.execCmd({
                        url: site_url('ventas/tpv/index/' + sel.data.nIdFactura)
                    });
                }
            });
            //console.log()
            try {
                Portlet_ultimasventas_this = this;
                setInterval(function(){
					Portlet_ultimasventas_timer();
				}, Ext.app.FACTURAREFRESH);
                
                Portlet_ultimasventas_timer();
            } 
            catch (e) {
                console.dir(e);
            }
            return this.grid;
        },
        
        load: function(){
            var t = this;
            var el = t.grid.getEl();
            if (el != null) 
                el.mask();
            t.grid.disable();
            t.store.removeAll();
            Ext.app.callRemote({
                url: site_url('ventas/factura/get_last'),
                params: {
                    sort: 'dCreacion',
                    dir: 'desc',
                    start: 0,
                    limit: limit
                    //where: 'nIdEstado != 1'
                },
                nomsg: true,
                fnok: function(res){
                    Ext.each(res.value_data, function(res){
                        t.store.add(new t.rt({
                            'nIdFactura': res.nIdFactura,
                            'cNumero': res.cNumero,
                            'cCliente': res.cCliente,
                            '_fTotal': res._fTotal,
                            'dFecha': res.dFecha,
                            'cCUser': res.cCUser,
                            'cAUser': res.cAUser,
                            'dCreacion': res.dCreacion,
                            'dAct': res.dAct
                        }));
                    });
                    if (el != null) 
                        el.unmask();
                    t.grid.enable();
                },
                fnnok: function(){
                    if (el != null) 
                        el.unmask();
                    t.grid.enable();
                }
            });
        },
        tools: function(tools){
            var tools1 = [];
            var t = this;
            tools1.push({
                id: 'refresh',
                handler: function(){
                    t.load();
                }
            });
            return tools1.concat(tools);
        }
    }
}
