(function() {
    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    if (title == '') 
        title = _s('Importar SINLI');
    if (icon == '') 
        icon = 'icon-sinli';
    if (form_id == '') 
        form_id = Ext.app.createId();


	var model = [{
		name : 'id'
	}, {
		name : 'nIdFichero'
	}, {
		name : 'dFecha'
	}, {
		name : 'dFechaAlbaran'
	}, {
		name : 'cAlbaran'
	}, {
		name : 'cProveedor'
	}, {
		name : 'nCantidad'
	}, {
		name : 'fImporte'
	}, {
		name : 'fGastos'
	}];

	var url = site_url("sys/sinli/envios");
	var store = Ext.app.createStore({
		model : model,
		url : url
	});

	var columns = [{
		header : _s("Id"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'nIdFichero',
		sortable : true
	}, {
		id : 'descripcion',
		header : _s("Proveedor"),
		dataIndex : 'cProveedor',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		header : _s("dFecha"),
		dataIndex : 'dFecha',
		width : Ext.app.TAM_COLUMN_DATE,
		renderer : Ext.app.renderDateShort,
		sortable : true
	}, {
		header : _s("Fecha Albaran"),
		dataIndex : 'dFechaAlbaran',
		width : Ext.app.TAM_COLUMN_DATE,
		renderer : Ext.app.renderDateShort,
		sortable : true
	}, {
		header : _s("Albaran"),
		dataIndex : 'cAlbaran',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		header : _s("nCantidad"),
		dataIndex : 'nCantidad',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}, {
		header : _s("fImporte"),
		dataIndex : 'fImporte',
		width : Ext.app.TAM_COLUMN_NUMBER,
		renderer : Ext.app.renderPrecio,
		sortable : true
	}, {
		header : _s("fGastos"),
		dataIndex : 'fGastos',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}];

	var proveedores = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('sys/sinli/proveedores/ENVIO'),
		name : 'pv'
	}));

	proveedores.on('select', function(c, r, i) {
		store.baseParams.id = r.data.id;
		store.load();
	});

	var reload = function() {
		var p = proveedores.getValue();
		if (p > 0) {
			store.baseParams.id = p;
			store.load();
		}
	}

    var tbar = [{
        xtype: 'label',
        html: _s('Proveedor')
    }, proveedores, '-', {
        tooltip: _s('Importar Fichero'),
        text: _s('Importar Fichero'),
        iconCls: 'icon-openfile',
        handler: function(){
			Ext.app.execCmd({
            	url : site_url('sys/sinli/fichero'),
        	});
        }
    }];

	store.baseParams = {
		start : 0,
		limit : Ext.app.PAGESIZE,
		id : -1,
		sort : 'dFecha',
		dir : 'DESC'
	};
	proveedores.store.load();

    var grid = new Ext.grid.EditorGridPanel({
		store : store,
		anchor : '100% 100%',
		//region: 'center',
		region : 'center',
		//height : 400,
		autoExpandColumn : 'descripcion',
		bbar : Ext.app.gridBottom(store),
		sm : new Ext.grid.RowSelectionModel({
			singleSelect : false
		}),
		stripeRows : true,
		loadMask : true,
		bbar : Ext.app.gridBottom(store, true),

		// grid columns
		columns : columns
	});

	grid.on('keydown', function(e) {
		if(e.getKey() == e.DELETE && !grid.editing) {
			console.log('DELETE');
		}
	});

	grid.on('dblclick', function(e) {
		var sm = grid.getSelectionModel();
		if(sm.hasSelection()) {
			var sel = sm.getSelected();
				Ext.app.callRemote({
                	url : site_url('sys/sinli/importarenvio'),
                	params : {
                    	ids : sel.data.id
                	},
                	fnok: function() {
                		store.load();
                	}
            	});			
		}
	});

	var cm_lineas = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenu(grid, 'nIdFichero', cm_lineas, 'sys/sinli/ver', _s('Ver'), 'icon-ver');
	cm_lineas.setContextMenu(contextmenu)
    
    var panel = new Ext.Panel({
        layout: 'border',
        title: title,
        id: id,
        iconCls: icon,
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        tbar: tbar,
        listeners: {
            afterrender: function(p){
                var map = new Ext.KeyMap(p.getEl(), [{
                    key: [10, 13],
                    ctrl: true,
                    stopEvent: true,
                    fn: function(){
                        reload();
                    }
                }]);
            }
        },
        
        items: [grid]
    });
    
    return panel;
})();
