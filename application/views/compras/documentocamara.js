(function() {

	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";

	if(title == '')
		title = _s('Documentos Cámara');
	if(icon == '')
		icon = 'iconoDocumentosCamaraTab';
	if(form_id == '')
		form_id = Ext.app.createId();

	var form = Ext.app.formGeneric();

	var list_grids = [form_id + '_albaranes_grid']

	var data_load = null;

	var notas = Ext.app.formNotas();
	
	// Carga
	var fn_load = function(id, res) {
        notas.load(id);
		data_load = res;
		Ext.app.formLoadList({
			list : list_grids,
			params : {
				where : 'nIdDocumentoCamara=' + id
			}
		});
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
		url : site_url('compras/documentocamara'),
		fn_load : fn_load,
		fn_reset : fn_reset,
		fn_enable_disable : fn_enable_disable
	});

	var model = [{
		name : 'nIdAlbaran',
		column : {
			header : _s("Id"),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'id',
			sortable : true
		}
	}, {
		name : 'id'
	}, {
		name : 'cProveedor',
		column : {
			id : 'descripcion',
			header : _s("Proveedor"),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		},
		ro : true
	}, {
		name : 'dCierre',
		column : {
			header : _s("dCierre"),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDateShort,
			sortable : true
		},
		ro : true
	}, {
		name : 'dFecha',
		column : {
			header : _s("Fecha Proveedor"),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDateShort,
			sortable : true
		},
		ro : true
	}, {
		name : 'cSimbolo',
		column : {
			header : _s("nIdDivisa"),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		},
		ro : true
	}, {
		name : 'fCambioCamara',
		column : {
			header : _s("fCambioCamara"),
			width : Ext.app.TAM_COLUMN_MONEY,
			align : 'right',
			renderer : Ext.app.rendererPVP,
			sortable : true
		},
		ro : true
	}, {
		name : 'fImporteCamara',
		column : {
			header : _s("fImporteCamara"),
			width : Ext.app.TAM_COLUMN_MONEY,
			align : 'right',
			renderer : Ext.app.rendererPVP,
			sortable : true
		},
		ro : true
	}];

	var albaranes = Ext.app.createFormGrid({
		model : model,
		show_filter : false,
		id : form_id + "_albaranes",
		idfield : 'nIdAlbaran',
		urlget : site_url("compras/albaranentrada/get_list"),
		urldel : site_url("compras/documentocamara/del_items"),
		anchor : '100% 85%',
		load : false
	});

	//form.addTab(albaranes);
	var paises = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('perfiles/pais/search'),
		name : 'nIdPais',
		label : _s('nIdPais'),
		anchor : '100%',
		autoload: true,
		allowBlank : false
	}));

	var tipomercancia = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('compras/tipomercancia/search'),
		name : 'nIdTipoMercancia',
		label : _s('nIdTipoMercancia'),
		anchor : '100%',
		autoload: true,
		allowBlank : false
	}));

	var formaenvio = Ext.app.combobox({
		url : site_url('compras/documentocamara/formasenvio'),
		id : 'cFormaEnvio',
		label : _s('cFormaEnvio'),
		anchor : '100%',
		allowBlank : false
	});
	formaenvio.forceSelection=false;

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
		text : _s('Ver albarán'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if((record != null) && (record.data.id != null)) {
				Ext.app.execCmd({
					url : site_url('compras/albaranentrada/index/' + record.data.nIdAlbaran)
				});
			}
		},
		iconCls : 'iconoAlbaranEntrada'
	});


	var controls = [paises, tipomercancia, formaenvio, fecha, albaranes];

	form.addTab({
		title : _s('General'),
		iconCls : 'icon-general',
		items : {
			xtype : 'panel',
			layout : 'form',
			items : form.addControls(controls)
		}
	});

	form.addTabUser();

    var grid_notas = notas.init({
        id: form_id + "_notas",
        url: site_url('compras/documentocamara'),
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

	var grid_search_m = search_documentocamara(form_id, fn_open);

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
					url : site_url((data_load.dFecha != null)?'compras/documentocamara/abrir':'compras/documentocamara/cerrar'),
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

	var addAlbaranes = function(padre) {
		var model = [{
			name : 'nIdAlbaran'
		}, {
			name : 'id'
		}, {
			name : 'cProveedor'
		}, {
			name : 'dCierre'
		}, {
			name : 'dFecha'
		}, {
			name : 'cSimbolo'
		}, {
			name : 'fImporteCamara'
		}];

		//var b = Ext.getCmp(bibliotecas.id);

		var url = site_url("compras/albaranentrada/get_list");
		var store = Ext.app.createStore({
			model : model,
			url : url
		});

		var sm = new Ext.grid.CheckboxSelectionModel();
		var columns = [sm, {
			name : 'nIdAlbaran',
			header : _s("Id"),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'id',
			sortable : true
		}, {
			name : 'cProveedor',
			id : 'descripcion',
			header : _s("Proveedor"),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			name : 'dCierre',
			header : _s("dCierre"),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDateShort,
			sortable : true
		}, {
			name : 'dFecha',
			header : _s("Fecha Proveedor"),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDateShort,
			sortable : true
		}, {
			name : 'cSimbolo',
			header : _s("nIdDivisa"),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			name : 'fImporteCamara',
			header : _s("fImporteCamara"),
			width : Ext.app.TAM_COLUMN_MONEY,
			align : 'right',
			renderer : Ext.app.rendererPVP,
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

		var controls = [grid];

		var form = Ext.app.formStandarForm({
			controls : controls,
			autosize : false,
			height : 500,
			title : _s('Añadir albaranes'),
			fn_ok : function() {
				var sel = grid.getSelectionModel().getSelections();
				var url = site_url('compras/documentocamara/add_items')
				var ids = [];
				Ext.each(sel, function(item) {
					ids.push(item.data.id);
				});
				ids = implode(';', ids);
				Ext.app.callRemote({
					url : url,
					params : {
						id : padre.getId(),
						ids : ids
					},
					fnok : function() {
						fn_load(padre.getId());
					}
				})
			}
		});
		var idp = paises.getValue();
		if (idp < 0)
		{
			paises.focus();
			Ext.app.msgFly(title, _s('no-pais'));
			return;
		}
		store.baseParams = {
			'where' : 'nIdDocumentoCamara=NULL&nIdPais=' + idp
		}
		store.load();
		form.show();
	};
	// Acciones
	form.addAction('-');
	form.addAction({
		text : _s('Añadir albaranes'),
		handler : function() {
			addAlbaranes(form);
		},
		iconCls : 'iconoAlbaranEntrada',
		id : form.idform + 'btn_albaranes'
	});

	return form.show(open_id);
})();
