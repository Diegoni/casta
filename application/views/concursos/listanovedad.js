(function(){

try {
	var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
	if (title == '') title = _s('Servicio de Novedades');
	if (icon == '') icon = 'iconoServicioNovedadesTab';
	if (form_id == '') form_id = Ext.app.createId();
    
    var form = Ext.app.formGeneric();
    
    var list_grids = [form_id + '_libros_grid']
    
    // Carga
    var fn_load = function(id){
        Ext.app.formLoadList({
            list: list_grids,
            params: {
                where: 'nIdLista=' + id
            }
        });
    }
    
    // Borrado
    var fn_reset = function(){
        Ext.app.formResetList({
            list: list_grids,
            params: {
                where: 'nIdLista=-1'
            }
        });
    }
    
    var fn_enable_disable = function(){
        var list_buttons = [form.idform + 'btn_mailing', 
				form.idform + 'btn_noportada', 
				form.idform + 'btn_stock', 
				form.idform + 'btn_nov_mat',
				form.idform + 'btn_sin_portada',
				form.idform + 'btn_nov_tema'];
        
        Ext.app.formEnableList({
            list: list_buttons,
            enable: (form.getId() > 0)
        });
		
        Ext.app.formEnableList({
            list: list_grids,
            enable: (form.getId() > 0)
        });
    }
    
    form.init({
        id: id,
        title: title,
        icon: icon,
        url: site_url('concursos/listanovedad'),
        fn_load: fn_load,
        fn_reset: fn_reset,
        fn_enable_disable: fn_enable_disable
    });
    
    var model = [{
        name: 'id',
        column: {
            header: _s("Id"),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true
        }
    }, {
        name: 'nIdLibro',
        column: {
            header: _s("nIdLibro"),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true
        }
    }, {
        name: 'cAutores',
        column: {
            header: _s("cAutores"),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        },
        ro: true
    }, {
        name: 'cTitulo',
        column: {
            header: _s('cTitulo'),
            width: Ext.app.TAM_COLUMN_TEXT,
            id: 'descripcion',
            sortable: true
        },
        ro: true
    }, {
        name: 'cSeccion',
        column: {
            header: _s('cOrigen'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        },
        ro: true
    }, {
        name: 'fPVP',
        column: {
            header: _s("fPVP"),
            width: Ext.app.TAM_COLUMN_PRICE,
            sortable: true
        },
        ro: true
    }];
    
    var origen = new Ext.form.ComboBox(Ext.app.combobox({
        id: id + '_origen',
        anchor: '70%',
        label: _s('Origen'),
        autoload: true
    }));
    
    var origenes = [];
    var count = 0;
    var count_lineas = 0;
    
    var accion = function(){
        var idl = ctl.getValue();
        var ido = origen.getValue();
        Ext.app.callRemote({
            url: site_url('concursos/listanovedadlinea/add'),
            params: {
                'nIdLista': parseInt(form.getId()),
                'nIdLibro': parseInt(idl),
                'nIdSeccion': parseInt(ido)
            },
            fnok: function(){
                origenes[origenes.length] = ido;
                ctl.reset();
                origen.reset();
                origen.store.removeAll();
                ctl.focus();
                Ext.app.formLoadList({
                    list: [form_id + '_libros_grid'],
                    params: {
                        where: 'nIdLista=' + form.getId()
                    }
                });
            }
        });
    }

    origen.on('keypress', function(f, e){
        if (e.getKey() == e.ENTER) {
            accion();
        }
    });

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

    var select = function(id){
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

    var libros = Ext.app.createFormGrid({
        model: model,
        id: form_id + "_libros",
        idfield: 'id',
        show_filter: false,
        urlget: site_url("concursos/listanovedadlinea/get_list"),
        urldel: site_url("concursos/listanovedadlinea/del"),
        rbar: [{
            xtype: 'label',
            html: _s('new-libro')
        }, ctl, '-', origen],
        anchor: '100% 85%',
        load: false
    });
        
    var grid = Ext.getCmp(form_id + '_libros_grid');
    var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro');

    var controls = [{
        xtype: 'textfield',
        id: 'cDescripcion',
        anchor: '90%',
        allowBlank: false,
        fieldLabel: _s('cDescripcion')
        }, libros
    ];

    form.addTab({
        title: _s('General'),
        iconCls: 'icon-general',
        items: {
            xtype: 'panel',
            layout: 'form',
            items: form.addControls(controls)
        }
    });
       
    form.addTabUser();
    
    var fn_open = function(id){
        form.load(id);
        form.selectTab(0);
    }
    
    var grid_search_m = search_listanovedad(form_id, fn_open);
	 
   	form.addTab({
			title: _s('Búsqueda'),
	        iconCls : 'icon-search',
			items : Ext.app.formSearchForm({        
				grid: grid_search_m,
                audit: true,
				id_grid: form_id + '_g_search_grid'
			})
		});
	 
    // Acciones    
    return form.show(open_id);
} catch (e)
{
    console.dir(e);
}
})();
