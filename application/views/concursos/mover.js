(function(){
    try {
	var id = Ext.app.createId();
    var title = _s('Mover Libros');
    var icon = "iconoSeccionMoverTab";
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

    var concurso = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/concurso/search'),
        label: _s('Concursos'),
        name: 'concurso',
        anchor: '90%'
    }));

    concurso.store.load({
            callback: function() {
                var v = Ext.app.get_config('bp.albaranentrada.concurso.default', 'user');
                if (v != null && v != '')
                    concurso.setValue(parseInt(v));
            }
        });

    origen.on('keypress', function(f, e){
        if (e.getKey() == e.ENTER) {
            destinocrear.focus();
        }
    });
    
    destinocrear.on('keypress', function(f, e){
        if (e.getKey() == e.ENTER) {
            accion();
        }
    });
    
    var select = function(id) {
        //var detailEl = Ext.getCmp(id + '_html').body;                        
        //detailEl.update('');
        if (id == undefined)
            id = art.getValue();
        origen2.load({
            params: {
                where: 'nIdLibro=' + id
            },
            callback: function(data) {
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
                        if (!in_array(new_origen, origenes)) origenes.push(new_origen);
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

    var ctl = new Ext.form.TextField({
        enableKeyEvents: true,
        fieldLabel: _s('Id')
    });
    
    ctl.on('keypress', function(t, e){
        if (e.getKey() === e.ENTER) {
            var tx = t.getValue();
            art.store.load({
                params: {
                    query: tx,
                    start: 0,
                    limit: Ext.app.AUTOCOMPLETELISTSIZE
                },
                callback: function(c){
                    t.setValue();
                    if (c.length == 1) {
                        art.setValue(c[0].id);
                        select();
                    }
                    else {
                        art.setValue(tx);                            
                    }
                }
            });
        }
    });
    
    var art = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/articulo/search'),
        label: _s('Artículo'),
        anchor: '100%',
        fnselect: select
    }));

    var model = [{
        name: 'id'
    }, {
        name: 'titulo'
    }, {
        name: 'destino'
    }];
    
    var store = new Ext.data.ArrayStore({
        fields: model
    });

    var grid = new Ext.grid.GridPanel({
        region: 'center',
        autoExpandColumn: "descripcion",
        loadMask: true,
        stripeRows: true,
        store: store,
        /*height: 300,*/
        id: id + "_grid",
        columns: [{
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true,
            hidden: true
        }, {
            header: _s('cTitulo'),
            width: Ext.app.TAM_COLUMN_TEXT*2,
            dataIndex: 'titulo',
            sortable: true
        }, {
            header: _s('Destino'),
            width: Ext.app.TAM_COLUMN_TEXT*2,
            id: 'descripcion',
            dataIndex: 'destino',
            sortable: true
        }]
    
    });


    var controls = [concurso, ctl, art, origen, destinocrear, {
        xtype: 'iframepanel',
        id: id + '_html',
        height: 60,
        /*region: 'center'*/
    }/*, grid*/];

    destinocrear.store.load({
        callback: function () {
            var d = parseInt(Ext.app.get_config('bp.concursos.mover.secciondefecto'));
            if (d > 0)
                destinocrear.setValue(parseInt(d));
        }
    });

    var accion = function() {                
        if (concurso.getValue() < 1) {
            concurso.focus();
            return false;
        }
        if (art.getValue() < 1) {
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
        Ext.app.callRemote({
            url: site_url('concursos/pedidoconcursolinea/mover'), 
            params: {
                idl: art.getValue(),
                origen: origen.getValue(),
                destino: destinocrear.getValue(),
                concurso: concurso.getValue(),
                id: -2
            },
            fnok: function (res) {
                var detailEl = Ext.getCmp(id + '_html').body;                        
                detailEl.applyStyles({
                    'background-color': '#FFFFFF'
                });
                var text = '<div style="font-size: 200%;color:green;align:center;">' + res.biblioteca + '</div>';
                detailEl.update(text);
                var reg = {
                    id: art.getValue(),
                    titulo: art.getRawValue(),
                    destino: res.biblioteca
                }
                store.insert(0, new ComboRecord(reg));
                ctl.reset();
                art.reset();
                //origen.reset();
                //destinocrear.reset();
                ctl.focus();
            }
        });
        return false;
    }
    /*var form2 = Ext.app.formStandarForm({
        controls: controls,
        height: 500,
        width: 500,
        icon: 'icon-change',
        title: _s('Cambiar artículo'),
        fn_ok: accion
    });
    form2.show();*/

    var form = new Ext.FormPanel({
        region: 'north',
        baseCls: 'form-movimiento',
        height: 200,
        labelWidth: 100,
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        items: controls
    });


    var panel = new Ext.Panel({
        layout: 'border',
        title: title,
        id: id,
        iconCls: icon,
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        listeners: {
            afterrender: function(p){
                ctl.focus();
            },
            afterlayout : function() {
                ctl.focus();
            }
        },
        
        items: [form, grid]
    });

    return panel;
} catch (e) {
    console.dir(e);
}
    return;
})();
