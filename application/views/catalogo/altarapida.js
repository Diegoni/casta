(function() {

	try {
	var text = '<?php echo $text;?>';
	var form_id = Ext.app.createId();

	// Controles normales
	var tipo = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('catalogo/tipolibro/search'),
		name : 'nIdTipo',
		allowBlank : true
	}));

	var tipoautor = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('catalogo/tipoautor/search'),
		name : 'nIdTipoAutor',
		width : 100,
		allowBlank : true
	}));

	var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('generico/seccion/search'),
		name : 'nIdSeccion',
		anchor : '100%',
		label : _s('Seccion')
	}));

	var idioma = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('generico/idioma/search'),
		name : 'nIdIdioma',
		width: 100,
		allowBlank : true
	}));

	var proveedor = /*new Ext.form.ComboBox*/(Ext.app.autocomplete2({
		url : site_url('proveedores/proveedor/search'),
		name : 'nIdProveedor',
		anchor : '100%',
		id : form_id + '_pv',
		create : true,
		fieldLabel : _s('Proveedor')
	}));

	var autor = new Ext.form.ComboBox(Ext.app.autocomplete2({
		url : site_url('catalogo/autor/search'),
		width : 200,
		load : function(res) {
			Ext.app.callRemote({
				url : site_url('catalogo/autor/search'),
				params : {
					query : res.id
				},
				fnok : function(res) {
					var reg = {
						id : res.value_data[0]['id'],
						text : res.value_data[0]['text'],
						tipo : tipoautor.getRawValue(),
						idtipo : tipoautor.getValue()
					}
					var p = new store2.recordType(reg, reg.id);
					store2.add(p);
					autor.store.removeAll();
					autor.setValue();
				}
			});
		}
	}));

	autor.on('select', function(c, item) {
		var reg = {
			id : item.data.id,
			text : item.data.text,
			tipo : tipoautor.getRawValue(),
			idtipo : tipoautor.getValue()
		}
		var p = new store2.recordType(reg, reg.id)
		store2.add(p);

		autor.store.removeAll();
		autor.setValue();
	});
	var materia = new Ext.form.ComboBox(Ext.app.autocomplete2({
		url : site_url('catalogo/materia/search'),
		anchor : '100%',
		fieldLabel : _s('Materia')
	}));

	materia.on('select', function(c, item) {
		Ext.app.comboAdd(store, item.data.id, item.data.text);
		materia.store.removeAll();
		materia.setValue();
	});
	var editorial = /*new Ext.form.ComboBox*/(Ext.app.autocomplete2({
		url : site_url('catalogo/editorial/search'),
		id : form_id + '_ed',
		name : 'nIdEditorial',
		anchor : '100%',
		create : true,
		fieldLabel : _s('Editorial')
	}));

	var id = Ext.app.createId();

	var cmpid = "<?php echo $this->input->get_post('cmpid');?>";
	var c = Ext.getCmp(cmpid);

	var store = Ext.app.createStore({
		id : 'id',
		model : [{
			name : 'id'
		}, {
			name : 'text'
		}]
	});

	var store2 = Ext.app.createStore({
		id : 'id',
		model : [{
			name : 'id'
		}, {
			name : 'text'
		}, {
			name : 'tipo'
		}, {
			name : 'idtipo'
		}]
	});

	var mpitemDeleter = new Extensive.grid.ItemDeleter({
		header : _s('Borrar'),
		width : 50
	});

	var grid = new Ext.grid.GridPanel({
		region : 'center',
		autoExpandColumn : "descripcion",
		loadMask : true,
		stripeRows : true,
		store : store,
		height : 100,
		sm : mpitemDeleter,
		columns : [mpitemDeleter, {
			header : _s('Id'),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'id',
			sortable : true
		}, {
			header : _s('Nombre'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'text',
			id : 'descripcion',
			sortable : true
		}],
		tbar : [{
			text : _s('Borrar'),
			iconCls : 'icon-delete',
			handler : function(button) {
				store.removeAll();
			}
		}]
	});
	Ext.app.addDeleteEvent(grid);

	var grid2 = new Ext.grid.GridPanel({
		region : 'center',
		autoExpandColumn : "descripcion",
		loadMask : true,
		stripeRows : true,
		store : store2,
		height : 100,
		columns : [{
			header : _s('Id'),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'id',
			sortable : true
		}, {
			header : _s('Nombre'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'text',
			id : 'descripcion',
			sortable : true
		}, {
			header : _s('Tipo'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'tipo',
			sortable : true
		}],
		tbar : [{
			text : _s('Borrar'),
			iconCls : 'icon-delete',
			handler : function(button) {
				store2.removeAll();
			}
		}]
	});

	var isbn = new Ext.form.TextField({
		xtype : 'textfield',
		id : 'cISBN',
		allowBlank : true
	});

	var titulo = new Ext.form.TextField({
		name : 'cTitulo',
		width : 330,
		allowBlank : false,
		selectOnFocus : true,
		fieldLabel : _s('cTitulo')
	});
	var portada = new Ext.form.TextField({
		readOnly : true,
		name : 'urlPortada',
		width : 300
	});

	var isbncontrol = new Ext.ux.form.ISBN({
		isbn_id : isbn.id,
		edit_id : editorial.id,
		prv_id : proveedor.id,
		next_id : titulo.id
	});

	var mostrarwebmanual = new Ext.ux.IconCombo({
		store : new Ext.data.SimpleStore({
			fields : ['id', 'text', 'icon'],
			data : [[-1, _s('mostrarweb-automatico'), 'icon-mostrar-automatico'], [0, _s('mostrarweb-nomostrar'), 'icon-mostrar-no'], [1, _s('mostrarweb-mostrar'), 'icon-mostrar-si']]
		}),
		value : -1,
		valueField : 'id',
		displayField : 'text',
		iconClsField : 'icon',
		triggerAction : 'all',
		mode : 'local',
		id: form_id + '_bMostrarWebManual',
		fieldLabel : _s('bMostrarWeb'),
		width : 300
	});

	var controls = [isbncontrol, {
		xtype : 'compositefield',
		fieldLabel : _s('cISBN'),
		anchor : '-20',
		defaults : {
			flex : 1
		},
		items : [isbn, {
			xtype : 'displayfield',
			value : _s('Tipo')
		}, tipo]
	}, {
		xtype : 'compositefield',
		fieldLabel : _s('cTitulo'),
		items : [titulo, {
			xtype : 'button',
			iconCls : 'icon-clean',
			width : 30,
			text : _s('Limpiar'),
			handler : function() {
				limpiar_titulo(titulo);
			}
		}]
	}, {
		xtype : 'compositefield',
		fieldLabel : _s('fPVP'),
		items : [{
			xtype : 'numberfield',
			id : 'fPVP',
			value : 0,
			width : '50',
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true
		}, {
			xtype : 'displayfield',
			value : _s('nIdIdioma')
		}, idioma,{
			xtype : 'displayfield',
			value : _s('dEdicion')
		}, {
			xtype : 'datefield',
			id : 'dEdicion',
			startDay : Ext.app.DATESTARTDAY,
			selectOnFocus : true,
			allowBlank : true
		}]
	}, {
		xtype : 'hidden',
		name : 'autores',
		id : form_id + '_aut'
	}, {
		xtype : 'hidden',
		name : 'bMostrarWebManual',
		id : form_id + '_web'
	}, {
		xtype : 'hidden',
		name : 'materias',
		id : form_id + '_mat'
	}, editorial, proveedor, seccion, {
		xtype : 'compositefield',
		fieldLabel : _s('cAutores'),
		items : [tipoautor, autor, {
			xtype : 'tbbutton',
			iconCls : 'iconoAutores',
			tooltip : _s('add-libro'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/autor/alta'),
					params : {
						autor : autor.getValue(),
						cmpid : autor.getId()
					}
				});
			}
		}]
	}, grid2, materia, grid, {
		xtype : 'compositefield',
		fieldLabel : _s('Portada'),
		items : [portada, {
			xtype : 'tbbutton',
			iconCls : 'icon-portada',
			tooltip : _s('Buscar portada'),
			handler : function() {
				var fn = function(res) {
					portada.setValue(res.url);
				}
				searchPicture(null, fn);
			}
		}]
	},mostrarwebmanual];


	/*var controls2 = {
        xtype: 'tabpanel',
        region: 'center',
        activeTab: 0,
        baseCls: 'x-plain',
        items: [{
            title: _s('General'),
            iconCls: 'icon-general',
            frame: true,
            //anchor: '100% 100%',
            height: 500,
            items: controls
        }, {
            title: _s('Texto'),
            iconCls: 'icon-sms',
            frame: true,
            //anchor: '100% 100%',
            height: 500,
            items: []
        }]
    };*/

	var url = site_url('catalogo/articulo/alta');

	var form = Ext.app.formStandarForm({
		controls : controls,
		icon : 'iconoAltaRapidaArticuloTab',
		title : _s('Alta rápida artículo'),
		width : 550,
		show : function() {
			if(text != '') {
				isbncontrol.setValue(text);
				isbncontrol.doQuery();
			}
		},
		fn_ok : function(res) {
			if(cmpid != '') {
				var c = Ext.getCmp(cmpid);
				if(c != null) {
					c.load(res);
				}
			}
		},
		fn_pre : function() {
			var aut = '';
			var mat = '';
			grid.getStore().each(function(r) {
				mat += r.data.id + ';';
			});
			grid2.getStore().each(function(r) {
				aut += r.data.id + '_' + r.data.idtipo + ';';
			});
			Ext.getCmp(form_id + '_aut').setValue(aut);
			Ext.getCmp(form_id + '_mat').setValue(mat);
			var v = Ext.getCmp(form_id + '_bMostrarWebManual').getValue();
			Ext.getCmp(form_id + '_web').setValue(v);
		},
		url : url
	});

	tipoautor.store.load({
		callback : function() {
			var id = parseInt("<?php echo $this->config->item('bp.catalogo.idtipoautordefault');?>");
			if(id != 0) {
				tipoautor.setValue(parseInt(id));
			}
		}
	});
	tipo.store.load({
		callback : function() {
			var id = parseInt("<?php echo $this->config->item('bp.catalogo.idtipoarticulodefault');?>");
			if(id != 0) {
				tipo.setValue(parseInt(id));
			}
		}
	});
	seccion.store.load({
		callback : function() {
			var id = parseInt("<?php echo $this->configurator->user('bp.catalogo.idsecciondefault');?>");
			if(id != 0) {
				seccion.setValue(parseInt(id));
			}
		}
	});
	idioma.store.load();

	form.show();
	return;
} catch (e) {
	console.dir(e);
}

})();
