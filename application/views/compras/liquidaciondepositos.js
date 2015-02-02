(function() {

	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";

	if(title == '')
		title = _s('Liquidación depósitos');
	if(icon == '')
		icon = 'iconoDepositosTab';
	if(form_id == '')
		form_id = Ext.app.createId();

	var form = Ext.app.formGeneric();

	var list_grids = [form_id + '_albaranes_grid']

	var data_load = null;
	var cliente_datos = null;
	var cliente_id = null;

	var notas = Ext.app.formNotas();
	
	// Carga
	var fn_load = function(id, res) {
        notas.load(id);
		data_load = res;
		Ext.app.formLoadList({
			list : list_grids,
			params : {
				where : 'nIdDocumentoDeposito=' + id
			}
		});
		fn_load_direcciones(res.nIdProveedor, res.nIdDireccion);
		fn_load_cliente(res.nIdProveedor);
	}
	// Guardar
	var fn_save = function(id, data) {
		if(Ext.getCmp(direcciones.id).getValue() != '')
			data['nIdDireccion'] = Ext.getCmp(direcciones.id).getValue();
			var idcliente = cliente_id;
		if((data_load != null && idcliente != data_load.nIdCliente) || data_load == null)
			data['nIdProveedor'] = idcliente;
		return data;
	}

	// Borrado
	var fn_reset = function() {
        notas.reset();
		Ext.app.formResetList({
			list : list_grids,
			params : {
				where : 'nIdDocumento=-1'
			}
		});
	}
	var fn_enable_disable = function() {
		var list_buttons = [form.idform + 'btn_albaranes'];
		notas.enable(form.getId() > 0);

		Ext.app.formEnableList({
			list : list_buttons,
			enable : (form.getId() > 0) && (data_load!=null) && (data_load.dFecha == null)
		});

		var m = Ext.getCmp(form.idform + 'btn_cerrar_menu');
		var m2 = Ext.getCmp(form.idform + 'btn_cerrar3');
		m.enable();
		m2.enable();
		m.setText(_s('Cerrar'));
		m2.setText(_s('Cerrar'));
		if (data_load == null) {
		    m.disable();
		    m2.disable();
		}
		else 
		    if ((data_load.dFecha != null)) {
		        m.setText(_s('Abrir'));
		        m2.setText(_s('Abrir'));
		    }
	}

	form.init({
		id : id,
		title : title,
		icon : icon,
		url : site_url('compras/liquidaciondepositos'),
		fn_load : fn_load,
		fn_save : fn_save,
		fn_reset : fn_reset,
		fn_enable_disable : fn_enable_disable
	});

	var fn_set_data = function(data) {
		if(data.direcciones)
			direcciones = data.direcciones;
		if(data.cliente_id)
			cliente_id = data.cliente_id;
		if(data.cliente_datos)
			cliente_datos = data.cliente_datos;
		if(data.data_load)
			data_load = data.data_load;
	}
	var fn_get_data = function() {
		return {
			direcciones : direcciones,
			cliente_id : cliente_id,
			cliente_datos : cliente_datos,
		}
	}

	var controles = documentosProveedor(form, 'nIdDireccion', fn_get_data, fn_set_data, Ext.app.PERFIL_PEDIDO);

	var direcciones = controles.direcciones;
	var proveedor = controles.cliente;
	var fn_load_direcciones = controles.fn_load_direcciones;
	var fn_load_cliente = controles.fn_load_cliente;

	var refs = {
		xtype : 'compositefield',
		fieldLabel : _s('cRefProveedor'),
		msgTarget : 'side',
		anchor : '-20',
		/*defaults: {
		 flex: 1
		 },*/
		items : [{
			xtype : 'textfield',
			id : 'cRefProveedor',
			allowBlank : true,
			width : '200'
		}, {
			xtype : 'displayfield',
			value : _s('cRefInterna')
		}, {
			xtype : 'textfield',
			id : 'cRefInterna',
			allowBlank : true,
			width : '200'
		}]
	};


	var model = [{
		name : 'nIdAlbaran',
		column : {
			header : _s("Alb.Sal."),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true
		}
	}, {
		name : 'nIdAlbaranEntrada',
		column : {
			header : _s("Alb.Ent."),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true
		}
	}, {
		name : 'id'
	}, {
		name : 'nIdLineaAlbaran'
	}, {
		name : 'nIdLibro'
	}, {
		name : 'cTitulo',
		column : {
			id : 'descripcion',
			header : _s("Titulo"),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		},
		ro : true
	}, {
		name : 'dCreacion',
		column : {
			header : _s("dCreacion"),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDateShort,
			sortable : true
		},
		ro : true
	}, {
		name : 'fPrecio',
		column : {
			header : _s("fPrecio"),
			width : Ext.app.TAM_COLUMN_MONEY,
			align : 'right',
			renderer : Ext.app.rendererPVP,
			sortable : true
		},
		ro : true
	}, {
		name : 'fDescuento',
		column : {
			header : _s("fDescuento"),
			width : Ext.app.TAM_COLUMN_NUMBER_SHORT,
			align : 'center',
			sortable : true
		},
		ro : true
	}, {
		name : 'fPVP',
		column : {
			header : _s("fPVP"),
			width : Ext.app.TAM_COLUMN_MONEY,
			align : 'right',
			renderer : Ext.app.rendererPVP,
			sortable : true
		},
		ro : true
	}, {
		name : 'nEnDeposito',
		column : {
			header : _s("Cantidad"),
			width : Ext.app.TAM_COLUMN_NUMBER_SHORT,
			sortable : true
		},
		ro : true
	}];

	var albaranes = Ext.app.createFormGrid({
		model : model,
		show_filter : false,
		id : form_id + "_albaranes",
		idfield : 'id',
		urlget : site_url("ventas/albaransalidalineadeposito/get_list"),
		urldel : site_url("compras/liquidaciondepositos/del_items"),
		anchor : '100% 85%',
		load : false
	});

	/*var proveedor = (Ext.app.autocomplete2({
		url : site_url('proveedores/proveedor/search'),
		name : 'nIdProveedor',
		anchor : '100%',
		create : true,
		fieldLabel : _s('Proveedor')
	}));*/

	var fecha = new Ext.form.DateField({
		xtype : 'datefield',
		fieldLabel : _s('dCierre'),
		startDay : Ext.app.DATESTARTDAY,
		disabled : true,
		name : 'dFecha',
		value : new Date(),
		allowBlank : true
	});

	var cm_lineas = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuEmpty(Ext.getCmp(form_id + "_albaranes_grid"), cm_lineas);
	contextmenu.add({
		text : _s('Ver albarán de salida'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if((record != null) && (record.data.nIdAlbaran != null)) {
				Ext.app.execCmd({
					url : site_url('ventas/albaransalida/index/' + record.data.nIdAlbaran)
				});
			}
		},
		iconCls : 'iconoAlbaranSalida'
	});
	contextmenu.add('-');
	contextmenu.add({
		text : _s('Ver albarán de entrada'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if((record != null) && (record.data.nIdAlbaranEntrada != null)) {
				Ext.app.execCmd({
					url : site_url('compras/albaranentrada/index/' + record.data.nIdAlbaranEntrada)
				});
			}
		},
		iconCls : 'iconoAlbaranEntrada'
	});

	contextmenu.add('-');
	contextmenu.add({
		text : _s('Ver artículo'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			console.dir(record);
			if((record != null) && (record.data.nIdLibro != null)) {
				Ext.app.execCmd({
					url : site_url('catalogo/articulo/index/' + record.data.nIdLibro)
				});
			}
		},
		iconCls : 'iconoArticulos'
	});

	var controls = [proveedor, fecha, refs, albaranes];
	documentosAddTabs(form, controls);

	/*form.addTab({
		title : _s('General'),
		iconCls : 'icon-general',
		items : {
			xtype : 'panel',
			layout : 'form',
			items : form.addControls(controls)
		}
	});

	form.addTabUser();*/

    var grid_notas = notas.init({
        id: form_id + "_notas",
        url: site_url('compras/liquidaciondepositos'),
        mainform: form
    });

    form.addTab(new Ext.Panel({
        layout: 'border',
        id: form_id + "_notas",
        title: _s('Histórico'),
        iconCls: 'icon-history',
        region: 'center',
        baseCls: 'x-plain',
        frame: true,
        items: grid_notas
    }));

	var fn_open = function(id) {
		form.load(id);
		form.selectTab(0);
	}

	var grid_search_m = search_liquidaciondepositos(form_id, fn_open);

	form.addTab({
		title: _s('Búsqueda'),
		iconCls: 'icon-search',
		items: Ext.app.formSearchForm({
			grid: grid_search_m,
			audit: false,
			id_grid: form_id + '_g_search_grid'
			})
	});
	// Acciones
	// Cerrar el documento
	var fn_cerrar = function(fnpost) {
		var fn = function(result) {
			if(result) {
				Ext.app.callRemote({
					url : site_url(((data_load!=null) && (data_load.dFecha == null))?'compras/liquidaciondepositos/cerrar':'compras/liquidaciondepositos/abrir'),
					wait : true,
					params : {
						id : form.getId()
					},
					fnok : function() {
						form.refresh();
					}
				});
			}
		}
		if(form.isDirty()) {
			form.save(fn);
		} else {
			fn(true);
		}
	}
	form.addCommand({
		text : _s('Cerrar'),
		iconCls : 'icon-generar-doc',
		handler : fn_cerrar,
		id : form.idform + 'btn_cerrar3'
	});

	form.addAction({
		text : _s('Cerrar'),
		handler : function(f) {
			fn_cerrar();
		},
		iconCls : 'icon-generar-doc',
		id : form.idform + 'btn_cerrar_menu'
	});

	var addArticulos = function(padre) {

		var idp = cliente_id;
		if (idp < 0)
		{
			//proveedor.focus();
			Ext.app.msgFly(title, _s('no-proveedor-select'));
			return;
		}

		var model = [{
			name : 'nIdLineaAlbaran'
		}, {
			name : 'nIdLibro'
		}, {
			name : 'cTitulo'
		}, {
			name : 'dCreacion'
		}, {
			name : 'fPVP'
		}, {
			name : 'nEnDeposito'
		}];

		var url = site_url("compras/liquidaciondepositos/get_items");
		var store = Ext.app.createStore({
			model : model,
			url : url
		});

		var sm = new Ext.grid.CheckboxSelectionModel();
		var columns = [sm, {
			name : 'nIdLineaAlbaran',
			header : _s("Id"),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdLineaAlbaran',
			sortable : true
		}, {
			name : 'cTitulo',
			id : 'descripcion',
			header : _s("Titulo"),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			name : 'dCreacion',
			header : _s("dCreacion"),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDateShort,
			sortable : true
		}, {
			name : 'fPVP',
			header : _s("fImporteCamara"),
			width : Ext.app.TAM_COLUMN_MONEY,
			align : 'right',
			renderer : Ext.app.rendererPVP,
			sortable : true
		}, {
			name : 'nCantidad',
			header : _s("Cantidad"),
			width : Ext.app.TAM_COLUMN_INT,
			sortable : true
		}];

		var grid = new Ext.grid.GridPanel({
			store : store,
			anchor : '100% 80%',
			height : 400,
			autoExpandColumn : 'descripcion',
			stripeRows : true,
			loadMask : true,
			sm : sm,

			bbar : Ext.app.gridBottom(store, true),

			// grid columns
			columns : columns
		});

		var fecha = new Ext.form.DateField({
			xtype : 'datefield',
			fieldLabel : _s('dHasta'),
			startDay : Ext.app.DATESTARTDAY,
			disabled : true,
			name : 'hasta',
			value : new Date(),
			allowBlank : true
		});

		//var controls = [fecha, grid];


	    var id = Ext.app.createId();
	    var title = _s('Añadir artículos');
	    var icon = 'iconoArticulosTab';
	    
	    var reload = function(url){
	        var d = Ext.getCmp(id + "_fecha").getValue();
	        store.baseParams = {
				'id' : idp,
				desde: DateToNumber(d.getTime())
			}
			store.load();
	    };
	    
	    var controls = [{
	        id: id + "_fecha",
	        fieldLabel: _s('Desde'),
	        value: new Date(),
	        startDay: Ext.app.DATESTARTDAY,
	        xtype: "datefield"
	    }, grid];
	    
	    var buttons = [{
	        text: _s('Buscar'),
	        iconCls: 'icon-search',
	        handler: function(){
	            reload();
	        }
	    }, {
	        text: _s('Añadir'),
	        iconCls: 'icon-add',
	        handler: function(){
				var sel = grid.getSelectionModel().getSelections();
				var url = site_url('compras/liquidaciondepositos/add_items')
				var ids = [];
				Ext.each(sel, function(item) {
					console
					ids.push({
						linea: item.data.nIdLineaAlbaran, 
						idl: item.data.nIdLibro
					});
				});
				//ids = implode(';', ids);
				Ext.app.callRemote({
					url : url,
					params : {
						id : padre.getId(),
						ids : ids,
						pv : idp
					},
					fnok : function() {
						Ext.app.formLoadList({
							list : list_grids,
							params : {
								where : 'nIdDocumentoDeposito=' + padre.getId()
							}
						});
						reload();
					}
				})
	        }
	    /*}, {
	        text: _s('Resumen'),
	        iconCls: 'icon-report',
	        handler: function(){
	            reload(site_url('calendario/trabajador/resumen'))
	        }*/
	    }];
	    
	    var form = Ext.app.formStandarForm({
	        controls: controls,
	        buttons: buttons,
	        title: _s('Añadir artículos'),
	        icon: icon
	    });
	    
	    form.show();


		/*var form = Ext.app.formStandarForm({
			controls : controls,
			autosize : false,
			height : 500,
			title : _s('Añadir artículos'),
			fn_ok : function() {
			}
		});*/
		//form.show();
	};
	// Acciones
	form.addAction('-');
	form.addAction({
		text : _s('Añadir artículos'),
		handler : function() {
			addArticulos(form);
		},
		iconCls : 'iconoArticulos',
		id : form.idform + 'btn_albaranes'
	});

	return form.show(open_id);
})();
