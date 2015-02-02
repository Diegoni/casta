(function() {
	try {
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";
		if(title == '')
			title = _s('Escuelas');
		if(icon == '')
			icon = 'iconoEscuelasTab';

		var list_grids = [form_id + '_departamentos_grid', form_id + '_importes_grid', form_id + 'btn_estado', form_id + '_add', form_id + '_del']

		var iva = null;
		var notas = Ext.app.formNotas();

		// Carga
		var fn_load = function(id, res) {
			try {
				notas.load(id);
				data_load = res;

				Ext.app.formLoadList({
					list : list_grids,
					params : {
						id : parseInt(id)
					}
				});
			} catch (e) {
				console.dir(e);
			}
		}
		var fn_save = function(id, data) {

			return data;
		}
		// Borrado
		var fn_reset = function() {
			notas.reset();
			iva = 0;
			Ext.app.formResetList({
				list : list_grids,
				params : {
					id : -1
				}
			});
		}
		var fn_enable_disable = function(form) {
			notas.enable(form.getId() > 0);
			Ext.app.formEnableList({
				list : list_grids,
				enable : (form.getId() > 0)
			});
		}
		// Formulario
		var form = Ext.app.formGeneric();
		form.init({
			id : form_id,
			title : title,
			icon : icon,
			url : site_url('eoi/escuela'),
			fn_load : fn_load,
			fn_save : fn_save,
			fn_reset : fn_reset,
			fn_enable_disable : fn_enable_disable
		});

		// Controles normales
		var caja = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('ventas/caja/search'),
			name : 'nIdCaja',
			fieldLabel : _s('nIdCaja'),
			anchor : '50%',
			label : _s('Caja')
		}));

		var serie = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('ventas/serie/search'),
			name : 'nIdSerie',
			fieldLabel : _s('nIdSerie'),
			anchor : '50%',
			label : _s('nIdSerie')
		}));

		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('generico/seccion/search'),
			name : 'nIdSeccion',
			fieldLabel : _s('nIdSeccion'),
			anchor : '50%',
			label : _s('nIdSeccion')
		}));

		var comision = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fComision',
			fieldLabel : _s('fComision'),
			width : 50,
			value : 0,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true
		});

		var descuento = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fDescuento',
			fieldLabel : _s('fDescuento'),
			width : 50,
			value : 0,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true
		});

		var descripcion = new Ext.form.TextField({
			name : 'cDescripcion',
			width : 700,
			allowBlank : false,
			selectOnFocus : true,
			fieldLabel : _s('cDescripcion')
		});

		var controls = [descripcion, caja, seccion, serie, comision, descuento, {
			xtype : 'textfield',
			fieldLabel : _s('cUsuario'),
			id : 'cUsuario'
		}, {
			xtype : 'textfield',
			fieldLabel : _s('cPin'),
			id : 'cPin'
		}];

		// Departamentos
		var model = [{
			name : 'nIdDepartamento',
			column : {
				header : _s("Id"),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'id',
				sortable : true
			}
		}, {
			name : 'id'
		}, {
			name : 'cCliente',
			column : {
				header : _s("Departamento"),
				width : Ext.app.TAM_COLUMN_TEXT,
				id : 'descripcion',
				sortable : true
			},
			ro : true
		}, {
			name : 'cDescripcion',
			column : {
				header : _s("cDescripcion"),
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField(),
				sortable : true
			}
		}, {
			name : 'nIdCliente',
			column : {
				header : _s("nIdCliente"),
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField(),
				sortable : true
			}
		}];

		var fnselect = function(id) {
			try {
				Ext.Msg.prompt(form.getTitle(), _s('cDescripcion'), function(ok, v) {
					if(ok != 'ok')
						return;
					Ext.app.callRemote({
						url : site_url('eoi/escuela/add_departamento'),
						params : {
							'id1' : parseInt(form.getId()),
							'id2' : parseInt(id),
							'descripcion' : v
						},
						fnok : function() {
							new_departamento.setValue(null);
							Ext.app.formLoadList({
								list : [form_id + '_departamentos_grid'],
								params : {
									id : form.getId()
								}
							});
						}
					});
				});
			} catch (e) {
				console.dir(e);
			}
		}
		var new_departamento = new Ext.form.ComboBox(Ext.app.autocomplete({
			url : site_url('clientes/cliente/search'),
			name : form_id + "_adddepartamento",
			id : form_id + "_adddepartamento",
			fnselect : fnselect
		}));

		var departamentos = Ext.app.createFormGrid({
			model : model,
			id : form_id + "_departamentos",
			idfield : 'id',
			urlget : site_url("eoi/escuela/departamentos"),
			urldel : site_url("eoi/escuela/del_departamento"),
			urlupd : site_url("eoi/departamento/upd"),
			rbar : [{
				xtype : 'label',
				html : _s('new-departamento')
			}, new_departamento],
			anchor : '100% 85%',
			load : false
		});
		var grid = Ext.getCmp(form_id + '_departamentos_grid');
		var cm_lineas = fn_contextmenu();
		var contextmenu = Ext.app.addContextMenu(grid, 'nIdCliente', cm_lineas, 'clientes/cliente/index', _s('Ver cliente'), 'iconoClientesTab');
		cm_lineas.setContextMenu(contextmenu)

		// Importes
		var model2 = [{
			name : 'nIdImporte',
			column : {
				header : _s("Id"),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'id',
				sortable : true
			}
		}, {
			name : 'id'
		}, {
			name : 'bPositivo'
		}, {
			name : 'dFecha',
			column : {
				header : _s("dFecha"),
				width : Ext.app.TAM_COLUMN_DATE,
				renderer : Ext.app.renderDateShort,
				sortable : true
			},
			ro : true
		}, {
			name : 'cConcepto',
			column : {
				header : _s("cConcepto"),
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField({
					selectOnFocus : true
				}),
				id : 'descripcion',
				sortable : true
			}
		}, {
			name : 'fEntrada',
			column : {
				header : _s("Entrada"),
				width : Ext.app.TAM_COLUMN_NUMBER,
				editor : new Ext.form.NumberField({
					allowBlank : false,
					selectOnFocus : true,
					allowNegative : true
				}),
				align : 'right',
				renderer : Ext.app.renderMoney,
				sortable : true
			}
		}, {
			name : 'fSalida',
			column : {
				header : _s("Salida"),
				width : Ext.app.TAM_COLUMN_NUMBER,
				editor : new Ext.form.NumberField({
					selectOnFocus : true,
					allowBlank : false,
					allowNegative : true
				}),
				align : 'right',
				renderer : Ext.app.renderMoney,
				sortable : true
			}
		}];

		var add_importe = function(title, icon, positivo) {
			var importe = new Ext.form.NumberField({
				allowBlank : true,
				allowNegative : false,
				allowDecimals : true,
				name : 'fImporte',
				fieldLabel : _s('fImporte')
			});
			var controls = [{
				value : DateAdd('d', -1, new Date()),
				fieldLabel : _s('Fecha'),
				name : 'dFecha',
				allowBlank : false,
				startDay : Ext.app.DATESTARTDAY,
				xtype : "datefield"
			}, {
				xtype : 'textfield',
				name : 'cConcepto',
				fieldLabel : _s('cConcepto'),
				width : '90%'
			}, importe, /*{
			 xtype : 'hidden',
			 name : 'bPositivo',
			 value : positivo
			 }, */
			{
				xtype : 'hidden',
				name : 'nIdEOI',
				value : form.getId()
			}];

			var url = site_url('eoi/importe/add');

			var form2 = Ext.app.formStandarForm({
				icon : icon,
				controls : controls,
				timeout : false,
				title : title,
				url : url,
				fn_pre : function() {
					if(!positivo) {
						console.log('negativo' + -parseFloat(importe.getValue()));
						importe.setValue(-parseFloat(importe.getValue()));
					}
				},
				fn_ok : function() {
					var grid = Ext.getCmp(form_id + '_importes_grid');
					grid.store.load();
				}
			});

			form2.show();
		}
		var importes = Ext.app.createFormGrid({
			model : model2,
			id : form_id + "_importes",
			idfield : 'id',
			urlget : site_url("eoi/escuela/importes"),
			urldel : site_url("eoi/escuela/del_importe"),
			urlupd : site_url("eoi/importe/upd"),
			rbar : [{
				tooltip : _s('cmd-addimporte'),
				text : _s('Añadir importe positivo'),
				iconCls : 'icon-plus',
				id : form_id + "_add",
				listeners : {
					click : function(b) {
						add_importe(_s('Añadir importe positivo'), 'icon-plus', true);
					}
				}
			}, {
				tooltip : _s('cmd-delimporte'),
				text : _s('Añadir importe negativo'),
				iconCls : 'icon-minus',
				id : form_id + "_del",
				listeners : {
					click : function(b) {
						add_importe(_s('Añadir importe negativo'), 'icon-minus', false);
					}
				}
			}],
			anchor : '100% 85%',
			load : false
		});

		form.addTab({
			title : _s('General'),
			iconCls : 'icon-general',
			items : {
				xtype : 'panel',
				layout : 'form',
				cls : 'form-eoi',
				items : form.addControls(controls)
			}
		});

		form.addTab({
			title : _s('Departamentos'),
			iconCls : 'iconoDepartamentosTab',
			items : {
				xtype : 'panel',
				layout : 'form',
				items : form.addControls(departamentos)
			}
		});

		form.addTab({
			title : _s('Importes'),
			iconCls : 'icon-precio',
			items : {
				xtype : 'panel',
				layout : 'form',
				items : form.addControls(importes)
			}
		});

		// Notas
		var grid_notas = notas.init({
			id : form_id + "_notas",
			url : site_url('eoi/escuela'),
			mainform : form
		});

		form.addTab(new Ext.Panel({
			layout : 'border',
			id : form_id + "_notas",
			title : _s('Histórico'),
			iconCls : 'icon-history',
			region : 'center',
			baseCls : 'x-plain',
			frame : true,
			items : grid_notas
		}));

		// Usuarios
		form.addTabUser();

		var estado_cuenta = function(form) {
			var controls = [{
				value : DateAdd('d', -1, new Date()),
				fieldLabel : _s('Desde'),
				name : 'fecha',
				allowBlank : false,
				startDay : Ext.app.DATESTARTDAY,
				xtype : "datefield"
			}, {
				xtype : 'hidden',
				name : 'id',
				value : form.getId()
			}];

			var url = site_url('eoi/escuela/estadocuenta');

			var form2 = Ext.app.formStandarForm({
				icon : 'iconoReportTab',
				controls : controls,
				timeout : false,
				title : _s('estado-cuenta'),
				url : url
			});

			form2.show();
		}

		var libros_tpv = new Ext.ux.TinyMCE(Ext.app.formEditor({
			title : _s('Sinopsis'),
			anchor : '100% 100%',
			name : 'tSinopsis',
			id : form_id + '_sinopsis'
		}));
		form.addTab({
			title : _s('TPV'),
			iconCls : 'iconoTPVTab',
			items : form.addControls([{
				xtype : 'textarea',
				id : 'cLibros',
				anchor : '100% 91%'
				}])
		});		
		// Búsqueda
		var fn_open = function(id) {
			form.load(id);
			form.selectTab(0);
		}

		<?php $modelo = $this->reg->get_data_model(array('nIdCaja', 'nidSerie', 'nIdCliente', 'nIdSeccion', 'cPin'));?>
		var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'eoi.escuela', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;

		form.addTab({
			title : _s('Búsqueda'),
			iconCls : 'icon-search',
			items : Ext.app.formSearchForm({
				grid : grid_search,
				id_grid : form_id + '_g_search_grid'
			})
		});

		form.addAction({
			text : _s('estado-cuenta'),
			iconCls : 'icon-report',
			handler : function() {
				estado_cuenta(form);
			},
			id : form.idform + 'btn_estado'
		});

		seccion.store.load();
		serie.store.load();
		caja.store.load();
		return form.show(open_id);
	} catch(e) {
		console.dir(e);
	}
})();
