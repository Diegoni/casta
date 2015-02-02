(function() {

	var open_id = "<?php echo $open_id;?>";
	var id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "iconoInventariarTab";
	if(title == '')
		title = _s('Inventariar');
	if(id == '')
		id = Ext.app.createId();

	var origen2 = Ext.app.createStore({
		url : site_url('catalogo/articuloseccion/get_list'),
		model : [{
			name : 'id'
		}, {
			name : 'nIdLibro'
		}, {
			name : 'nIdSeccion'
		}, {
			name : 'nStock'
		}, {
			name : 'nStockFirme'
		}, {
			name : 'nStockDeposito'
		}, {
			name : 'cNombre'
		}]
	});

	var MAX_ITEMS = 30;

	var store_insert = function (p) {
		store.insert(0, p);
		var max = store.getCount();
		if (max >= MAX_ITEMS) {
			store.removeAt(max-1);
		}
		//console.log('records ' + store.getCount());
	}

	var show_error = function (text) {
		Ext.app.playSoundError();
		error.setVisible(true);
		var reg = {
			id : Ext.app.createId(),
			titulo : '<span style="color:red">' + text + '</span>',
			seccion : '<span style="background-color: red; color:white; font-weight: bolder;">' + _s('ERROR') + '</span>',
			fecha : DateToNumber(new Date().getTime())
		}
		var p = new store.recordType(reg, reg.id)
		store_insert(p);

	}

	var last_id = null;
	var last_count = 0;
	var add_item = function(idl, idt, ct, ids, idu, nombre, titulo) {
		var url = site_url('stocks/stockcontado/add2');
		//grid.getEl().mask(Ext.app.TEXT_CARGANDO);
		Ext.app.callRemote({
			url : url,
			params : {
				idl : idl,
				idt : idt,
				ct : ct,
				ids : ids,
				idu : idu
			}/*,
			 fnok : function() {
			 },
			 fnnok : function() {
			 grid.getEl().unmask();
			 }*/
		});
		//grid.getEl().unmask();
		var pre = '';
		if(nombre == null)
			nombre = seccion.getRawValue();
		if(last_id == idl) {
			last_count += parseInt(ct);
			titulo = '<span style="color:blue">[' + last_count + '] ' + titulo + '</span>';
		} else {
			last_id = idl;
			last_count = parseInt(ct);
		}
		if(nombre != seccion.getRawValue())
			nombre = '<b>' + nombre + '</b>';
		var reg = {
			id : Ext.app.createId(),
			nIdLibro : idl,
			nIdSeccion : ids,
			nIdTipo : idt,
			titulo : titulo,
			ubicacion : ubicacion.getRawValue(),
			seccion : nombre,
			cantidad : ct,
			fecha : DateToNumber(new Date().getTime()),
			tipo : tipo.getRawValue()
		}
		var p = new store.recordType(reg, reg.id)
		store_insert(p);
		Ext.app.playSoundError('audio6');
		//console.dir(store);
		//grid.getView().refresh();
		cantidad.setValue(1);
		art.setValue();
	}
	var select = function(id, nombre, query) {
		var idl = (art.getValue());
		var idu = (ubicacion.getValue());
		var ct = (cantidad.getValue());
		var idt = (tipo.getValue());
		var ids = (seccion.getValue());
		
		if(id != null)
			ids = id;
		if(ids == null || ids < 1) {
			show_error(_s('mensaje_falta_seccion'));
			Ext.app.msgFly(title, _s('mensaje_falta_seccion'));
			seccion.focus();
			return;
		}
		if(idl == null || idl < 1) {
			show_error(_s('mensaje_falta_articulo'));
			Ext.app.msgFly(title, _s('mensaje_falta_articulo'));
			ctl.focus();
			return;
		}
		if(idt == null || idt < 1) {
			show_error(_s('mensaje_falta_tipo'));
			Ext.app.msgFly(title, _s('mensaje_falta_tipo'));
			tipo.focus();
			return;
		}
		if (id == null && forzar.getValue() === true) 
			id = seccion.getValue();

		// Comprueba si hay seccion
		if(id == null) {
			origen2.load({
				params : {
					where : 'nIdLibro=' + idl
				},
				callback : function(data) {
					var esta = false;
					Ext.each(data, function(item) {
						if(ids == item.data.nIdSeccion) {
							esta = true;
							return false;
						}
					});
					if(!esta) {
						// Busca en las hijas
						Ext.each(data, function(item) {
							//console.dir(item.data);
							if ((item.data.nStockFirme != 0 || item.data.nStockDeposito  != 0)
								&& (secciones_hijas[item.data.nIdSeccion] != undefined))
							{
								esta = true;
								ids = item.data.nIdSeccion;
								return false;
							}
						});

						if (!esta)
							form_seccion(query);
						else
							select(ids, secciones_hijas[ids], query);
						return;
					} else
						select(ids, null, query);
				}
			});
			return;
		}
		var tx = art.getRawValue().match(/\/(.*?)\(/);		

		if(query != null && tx != null) {
			Ext.app.setStorage('inv2_' + query, {
				idl : idl,
				isbn: tx[1].trim(),
				idt : idt,
				ids : ids,
				nombre : nombre,
				art : art.getRawValue()
			});
		}
		ctl.focus();

		add_item(idl, idt, ct, ids, idu, nombre, art.getRawValue());
	}
	var ctl = new Ext.form.TextField({
		enableKeyEvents : true,
		selectOnFocus : true,
		fieldLabel : _s('Id')
	});

	ctl.on('keypress', function(t, e) {
		if(e.getKey() === e.ENTER) {
			error.setVisible(false);
			var tx = t.getValue();
			t.setValue();
			var letra = tx.substr(0, 1).toLowerCase();
			if(letra == '¿')
				letra = '+';
			if(letra == 'u') {
				var ub = tx.substr(1);
				ubicacion.getStore().load({
					params : {
						query : ub,
						start : 0,
						limit : Ext.app.AUTOCOMPLETELISTSIZE
					},
					callback : function(c) {
						t.setValue();
						Ext.app.playSoundError('audio3');
						ubicacion.setValue(ub);
						var reg = {
							id : Ext.app.createId(),
							titulo : ubicacion.getRawValue(),
							seccion : '<span style="color:green">' + _s('Ubicación').toUpperCase() + '</span>',
							fecha : DateToNumber(new Date().getTime())
						}
						var p = new store.recordType(reg, reg.id)
						store_insert(p);
					}
				});
			} else if(letra == '+' || letra == '-') {
				var qt = parseInt(tx.substr(1));
				cantidad.setValue((letra == '-') ? -qt : qt)
				Ext.app.playSoundError('audio4');
			} else {
				tx = tx.trim();
				if(tx.length > 0) {
					var cache = Ext.app.getStorage('inv2_' + tx);
					if(cache != null && (cache.idl == tx || cache.isbn == tx)) {
						var idu = (ubicacion.getValue());
						var ct = (cantidad.getValue());
						add_item(cache.idl, cache.idt, ct, cache.ids, idu, cache.nombre, cache.art);
						return;
					}
					art.store.load({
						params : {
							query : tx,
							start : 0,
							limit : Ext.app.AUTOCOMPLETELISTSIZE
						},
						callback : function(c) {
							t.setValue();
							if(c.length == 1) {
								art.setValue(c[0].id);
								select(null, null, tx);
							} else if(c.length == 0) {
								show_error(tx);
							} else {
								Ext.app.playSoundError('audio5');
								form_search.show();
								//art.setValue(tx);
							}
						}
					});
				}
			}
		}
	});
	
	var art = new Ext.form.ComboBox(Ext.app.autocomplete({
		allowBlank : false,
		url : site_url('catalogo/articulo/search'),
		label : _s('Artículo'),
		anchor : '80%',
		fnselect : select
	}));

	var model = ['id', 'text'];

	var columns = [{
		header : _s("Id"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'id',
		sortable : true
	}, {
		header : _s("cDescripcion"),
		dataIndex : 'text',
		id : 'descripcion',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}];

	var listView = Ext.app.createGrid({
		store : art.store,
		columns : columns,
		title : _s('Búsqueda de registros'),
		mode : 'search'/*,
		 fn_open : function(id) {
		 form_search.hide();
		 art.setValue(id);
		 select();
		 ctl.focus();
		 }*/
	});

	listView.setHeight(Ext.app.FORM_SEARCH_HEIGHT);

	listView.on('dblclick', function(view, index) {
		var sm = listView.getSelectionModel();
		if(sm.hasSelection()) {
			var sel = sm.getSelected();
			form_search.hide();
			art.setValue(sel.data.id);
			select();
			ctl.focus();
		}
	});
	var fn_ok = function() {
		var sm = listView.getSelectionModel();
		if(sm.hasSelection()) {
			var sel = sm.getSelected();
			form_search.hide();
			art.setValue(sel.data.id);
			select();
			ctl.focus();
		}
	};
	var form_search = Ext.app.formStandarForm({
		controls : [listView],
		close : 'hide',
		height : Ext.app.FORM_SEARCH_HEIGHT,
		fn_ok : fn_ok
	});

	var form_seccion = function(query) {
		var model_seccion = ['nIdSeccion', 'cNombre'];

		var count = 0;
		var ids = null;
		var ids_first = null;
		var total = 0;
		//console.dir(origen2);

		origen2.each(function(r2) {
			total++;
			if(ids_first == null)
				ids_first = r2;
			if(r2.nStockFirme > 0 || r2.nStockDeposito > 0) {
				count++;
				ids = r2;
			}
		});
		if(count > 1)
			ids = null
		if(ids == null && total == 1)
			ids = ids_first;
		if(ids != null) {
			select(ids.data.nIdSeccion, ids.data.cNombre);
			return;
		}

		var columns_seccion = [{
			header : _s("Id"),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdSeccion',
			sortable : true
		}, {
			header : _s("cSeccion"),
			dataIndex : 'cNombre',
			id : 'descripcion',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}];

		var listView_seccion = Ext.app.createGrid({
			store : origen2,
			columns : columns_seccion,
			title : _s('Búsqueda de registros'),
			mode : 'search'
		});

		listView_seccion.setHeight(Ext.app.FORM_SEARCH_HEIGHT);

		listView_seccion.on('dblclick', function(view, index) {
			var sm = listView_seccion.getSelectionModel();
			if(sm.hasSelection()) {
				var sel = sm.getSelected();
				form_seccion.hide();
				select(sel.data.nIdSeccion, sel.data.cNombre, query);
			}
		});
		var fn_ok_seccion = function() {
			var sm = listView_seccion.getSelectionModel();
			if(sm.hasSelection()) {
				var sel = sm.getSelected();
				form_seccion.hide();
				select(sel.data.nIdSeccion, sel.data.cNombre, query);
			}
		};
		var form_seccion = Ext.app.formStandarForm({
			controls : [listView_seccion],
			close : 'hide',
			disableok : true,
			height : Ext.app.FORM_SEARCH_HEIGHT,
			fn_ok : fn_ok_seccion
		});
		form_seccion.show();
	}
	var cantidad = new Ext.form.NumberField({
		allowBlank : true,
		allowNegative : true,
		allowDecimals : false,
		width : 50,
		minValue : -300,
		value : 1,
		selectOnFocus : true,
		maxValue : 300,
		fieldLabel : _s('Cantidad')
	});
	var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('generico/seccion/search'),
		anchor : '80%',
		label : _s('Sección')
	}));

	var secciones_hijas = [];
	seccion.on('select', function(cb, r, index) {
		Ext.app.callRemote({
			url : site_url('generico/seccion/get_list'),
			params : {
				id : r.data.id
				},
			fnok : function(data) {
				secciones_hijas = new Array();
					Ext.each(data.value_data, function(item) {
						secciones_hijas[item.id] = item.cNombre;
					});
				ctl.focus();
			}
		});
	});

	var forzar = new Ext.form.Checkbox({
		anchor : '80%',
		fieldLabel : _s('Forzar sección')
	});
	var tipo = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('stocks/tipostock/search'),
		anchor : '80%',
		label : _s('nIdTipoStock')
	}));

	var ubicacion = new Ext.form.ComboBox(Ext.app.autocomplete({
		url : site_url('catalogo/ubicacion/search'),
		label : _s('Ubicación'),
		anchor : '80%'
	}));

	var model = [{
		name : 'id'
	}, {
		name : 'nIdLibro'
	}, {
		name : 'nIdSeccion'
	}, {
		name : 'nIdTipo'
	}, {
		name : 'titulo'
	}, {
		name : 'cantidad'
	}, {
		name : 'seccion'
	}, {
		name : 'ubicacion'
	}, {
		name : 'fecha',
		type : 'date'
	}, {
		name : 'tipo'
	}];

	var store = new Ext.data.ArrayStore({
		fields : model
	});
	var error = new Ext.form.Label({
		xtype : 'label',
		iconCls : 'icon-clean',
		cls : 'error',
		width : 400,
		//hidden : true,
		text : _s('registro_no_encontrado')
	});

	var form = new Ext.FormPanel({
		region : 'north',
		baseCls : 'form-inventario',
		height : 250,
		labelWidth : 100,
		bodyStyle : 'padding:5px 5px 0',
		defaultType : 'textfield',
		buttonAlign : 'left',
		buttons : [{
			text : _s('Limpiar'),
			iconCls : 'icon-clean',
			handler : function() {
				form.getForm().reset();
				grid.store.removeAll();
				tipo.setValue("<?php echo $this->config->item('bp.contarstocks.firme');?>");
			}
		}],
		items : [{
			xtype : 'compositefield',
			fieldLabel : _s('Id'),
			items : [ctl, error]
		}, tipo, art, cantidad, seccion, forzar, ubicacion]
	});

	var grid = new Ext.grid.GridPanel({
		region : 'center',
		autoExpandColumn : "descripcion",
		loadMask : true,
		stripeRows : true,
		store : store,
		id : id + "_grid",
		columns : [{
			header : _s('Id'),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'id',
			sortable : true,
			hidden : true
		}, {
			header : _s('dCreacion'),
			width : Ext.app.TAM_COLUMN_DATE,
			dataIndex : 'fecha',
			startDay : Ext.app.DATESTARTDAY,
			renderer : Ext.app.renderTime,
			sortable : true
		}, {
			header : _s('nIdSeccion'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'seccion',
			sortable : true
		}, {
			header : _s('cTitulo'),
			width : Ext.app.TAM_COLUMN_TEXT,
			id : 'descripcion',
			dataIndex : 'titulo',
			sortable : true
		}, {
			header : _s('nCantidad'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'cantidad',
			sortable : true
		}, {
			header : _s('Ubicación'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'ubicacion',
			sortable : true
		}, {
			header : _s('nIdTipoStock'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'tipo',
			sortable : true
		}]

	});
	var cm_albaranes = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', cm_albaranes);
	contextmenu.add('-');
	contextmenu.add({
		text : _s('stock_contado_articulo'),
		handler : function() {
			Ext.app.callRemote({
				url : site_url('catalogo/articulo/stockcontado/' + form.getId())
			});
		},
		iconCls : 'iconoStockContado',
		handler : function() {
			var record = cm_albaranes.getItemSelect();
			if(record != null && record.data.nIdLibro != null) {
				Ext.app.execCmd({
					url : site_url('catalogo/articulo/stockcontado/' + record.data.nIdLibro)
				});
			}
		}
	});
	contextmenu.add('-');
	contextmenu.add({
		text : _s('Deshacer'),
		iconCls : 'icon-undo',
		handler : function() {
			var record = cm_albaranes.getItemSelect();
			if(record != null && record.data.nIdLibro != null && record.data.nIdSeccion) {
				add_item(record.data.nIdLibro, record.data.nIdTipo, -record.data.cantidad, record.data.nIdSeccion, null, record.data.seccion, record.data.titulo);
			}
		}
	});

	var fn_reset_ubicaciones = function() {
		Ext.app.callRemoteAsk({
			url : site_url('stocks/stockcontado/resetubicaciones'),
			askmessage : _s('eliminar-ubicaciones-q')
		});
	}
	var fn_asignar_ubicaciones = function() {
		Ext.app.callRemote({
			url : site_url('stocks/stockcontado/asignarubicaciones')
		});
	}
	/*var tbar = [{
		xtype : 'tbbutton',
		text : _s('Acciones'),
		iconCls : 'icon-actions',
		menu : [{
			text : _s('Asignar ubicaciones temporales'),
			iconCls : 'iconoAsignar',
			handler : fn_asignar_ubicaciones
		}, '-', {
			text : _s('Eliminar ubicaciones temporales'),
			iconCls : 'icon-delete',
			handler : fn_reset_ubicaciones
		}]
	}];*/
	var tbar = /*tbar.concat((*/Ext.app.gridStandarButtons({
		title : title,
		id : id + "_grid"
	})/*)*/;

	var panel = new Ext.Panel({
		layout : 'border',
		title : title,
		id : id,
		iconCls : icon,
		region : 'center',
		closable : true,
		baseCls : 'x-plain',
		frame : true,
		tbar : tbar,
		listeners : {
			afterrender : function() {
				setTimeout(function() {
					error.setVisible(false);
				}, 1000);
			}
		},
		items : [form, grid]
	});

	tipo.store.load({
		callback : function() {
			tipo.setValue("<?php echo $this->config->item('bp.contarstocks.firme');?>");
		}
	});
	seccion.store.load();

	return panel;
})();
