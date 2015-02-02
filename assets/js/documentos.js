var getLang = function(data_load) {
	// console.dir(data_load);
	if(data_load == null)
		return null;
	if(data_load.cliente != null) {

		if(data_load.cliente.cIdioma != null && data_load.cliente.cIdioma != '')
			return data_load.cliente.cIdioma;
	}
	if(data_load.proveedor) {
		if(data_load.proveedor.cIdioma != null && data_load.proveedor.cIdioma != '')
			return data_load.proveedor.cIdioma;
	}
	if(data_load.cIdioma != null && data_load.cIdioma != '')
		return data_load.cIdioma;
	if((data_load.direccion != null && data_load.direccion.cIdioma != null))
		return data_load.direccion.cIdioma;
	if(data_load.cliente == null)
		return null;
	return null;
}
/*
 * ! Ext JS Library 3.3.1 Copyright(c) 2006-2010 Sencha Inc.
 * licensing@sencha.com http://www.sencha.com/license
 */
var ImageChooser = function(config) {
	this.config = config;
}

ImageChooser.prototype = {
	// cache data by image name for easy lookup
	lookup : {},
	form_id : null,

	show : function(texto, callback) {
		if(!this.win) {
			this.initTemplates();

			this.store = new Ext.data.JsonStore({
				url : this.config.url,
				root : 'images',
				fields : ['name', 'url'],
				listeners : {
					'load' : {
						fn : function() {
							this.view.select(0);
						},
						scope : this,
						single : true
					}
				}
			});

			var formatData = function(data) {
				data.shortName = data.name.ellipse(15);
				this.lookup[data.name] = data;
				return data;
			};
			var me = this;

			this.view = new Ext.DataView({
				tpl : this.thumbTemplate,
				singleSelect : true,
				overClass : 'x-view-over',
				itemSelector : 'div.thumb-wrap',
				emptyText : '<div style="padding:10px;">' + _s('no-images-found') + '</div>',
				store : this.store,
				listeners : {
					'selectionchange' : {
						fn : this.showDetails,
						scope : this,
						buffer : 100
					},
					'dblclick' : {
						fn : this.doCallback,
						scope : this
					},
					'loadexception' : {
						fn : this.onLoadException,
						scope : this
					},
					'beforeselect' : {
						fn : function(view) {
							return view.store.getRange().length > 0;
						}
					}
				},
				prepareData : formatData.createDelegate(this)
			});
			var id = Ext.app.createId();
			this.form_id = id;
			var cfg = {
				iconCls : 'icon-portada',
				title : _s('Buscar imagen'),
				id : 'img-chooser-dlg',
				layout : 'border',
				minWidth : 500,
				minHeight : 300,
				modal : true,
				closeAction : 'hide',
				border : false,
				items : [{
					id : 'img-chooser-view',
					region : 'center',
					autoScroll : true,
					items : this.view,
					tbar : [{
						xtype : 'textfield',
						id : id + 'filter',
						value : texto,
						selectOnFocus : true,
						width : 100,
						listeners : {
							'render' : {
								fn : function() {
									Ext.getCmp(id + 'filter').getEl().on('keypress', function(e) {
										if(e.keyCode == e.ENTER)
											this.filter();
									}, this, {
										buffer : 500
									});
								},
								scope : this
							}
						}
					}, ' ', '-', {
						text : _s('Método')
					}, {
						id : id + 'sortSelect',
						xtype : 'combo',
						typeAhead : true,
						triggerAction : 'all',
						width : 100,
						editable : false,
						mode : 'local',
						displayField : 'desc',
						valueField : 'name',
						lazyInit : false,
						value : 'auto',
						store : new Ext.data.ArrayStore({
							fields : ['name', 'desc'],
							data : [['auto', _s('General')], ['google', _s('Imágenes')], ['google2', _s('Enlaces')]]
						})
					}, {
						xtype : 'button',
						iconCls : 'icon-search',
						width : 30,
						value : '',
						handler : function() {
							me.filter();
						}
					}]
				}, {
					id : 'img-detail-panel',
					region : 'east',
					autoScroll : true,
					split : true,
					width : 250,
					minWidth : 150,
					maxWidth : 400
				}],
				buttons : [{
					text : _s('aceptar'),
					id : id + 'ok-btn',
					iconCls : 'icon-accept-form',
					handler : this.doCallback,
					scope : this
				}, {
					text : _s('cerrar'),
					iconCls : 'icon-cancel-form',
					handler : function() {
						this.win.hide();
					},
					scope : this
				}],
				keys : {
					key : 27, // Esc key
					handler : function() {
						this.win.hide();
					},
					scope : this
				}
			};
			Ext.apply(cfg, this.config);
			this.win = new Ext.Window(cfg);
		}

		this.reset();
		if(texto != null && texto.trim() != '')
			this.filter();

		this.win.show();
		this.callback = callback;
	},
	initTemplates : function() {
		this.thumbTemplate = new Ext.XTemplate('<tpl for=".">', '<div class="thumb-wrap" id="{name}">', '<div class="thumb"><img src="{url}" title="{name}"></div>', '<span>{shortName}</span></div>', '</tpl>');
		this.thumbTemplate.compile();

		this.detailsTemplate = new Ext.XTemplate('<div class="details">', '<tpl for=".">', '<img src="{url}">', '</tpl>', '</div>');
		this.detailsTemplate.compile();
	},
	showDetails : function() {
		var selNode = this.view.getSelectedNodes();
		var detailEl = Ext.getCmp('img-detail-panel').body;
		if(selNode && selNode.length > 0) {
			selNode = selNode[0];
			Ext.getCmp(this.form_id + 'ok-btn').enable();
			var data = this.lookup[selNode.id];
			detailEl.hide();
			this.detailsTemplate.overwrite(detailEl, data);
			detailEl.slideIn('l', {
				stopFx : true,
				duration : .2
			});
		} else {
			Ext.getCmp(this.form_id + 'ok-btn').disable();
			detailEl.update('');
		}
	},
	filter : function() {
		var filter = Ext.getCmp(this.form_id + 'filter');
		var v = Ext.getCmp(this.form_id + 'sortSelect').getValue();
		if(this.view.getEl() != null)
			this.view.getEl().mask();
		var me = this;
		this.view.store.load({
			params : {
				text : filter.getValue(),
				method : v
			},
			callback : function() {
				if(me.view.getEl() != null)
					me.view.getEl().unmask();
			}
		});
	},
	sortImages : function() {
		var v = Ext.getCmp('sortSelect').getValue();
		this.view.store.sort(v, v == 'name' ? 'asc' : 'desc');
		this.view.select(0);
	},
	reset : function() {
		if(this.win.rendered) {
			Ext.getCmp(this.form_id + 'filter').reset();
			this.view.getEl().dom.scrollTop = 0;
		}
	},
	doCallback : function() {
		var selNode = this.view.getSelectedNodes()[0];
		var callback = this.callback;
		var lookup = this.lookup;
		this.win.hide(this.animateTarget, function() {
			if(selNode && callback) {
				var data = lookup[selNode.id];
				callback(data);
			}
		});
	},
	onLoadException : function(v, o) {
		this.view.getEl().update('<div style="padding:10px;">Error loading images.</div>');
	}
};

String.prototype.ellipse = function(maxLength) {
	if(this.length > maxLength) {
		return this.substr(0, maxLength - 3) + '...';
	}
	return this;
};
var searchPicture = function(texto, fn) {
	var chooser = new ImageChooser({
		url : site_url('catalogo/articulo/search_portada'),
		width : Ext.app.SEARCHPICTURE_WIDTH,
		height : Ext.app.SEARCHPICTURE_HEIGHT
	});
	chooser.show(texto, fn);
}
var addUbicacion = function(idlibro, fn) {

	var form_id = Ext.app.createId();

	var seccion = (Ext.app.autocomplete2({
		url : site_url('catalogo/ubicacion/search'),
		fieldLabel : _s('Ubicación'),
		anchor : '90%',
		name : 'nIdUbicacion',
		allowBlank : true
	}));

	var controls = [seccion, {
		xtype : 'hidden',
		name : 'nIdLibro',
		value : idlibro
	}];

	var url = site_url('catalogo/articuloubicacion/add');
	var form = Ext.app.formStandarForm({
		controls : controls,
		url : url,
		icon : 'iconoUbicacionTab',
		title : _s('Añadir Ubicación'),
		width : 530,
		fn_ok : function(res) {
			if(fn != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/articuloubicacion/get_list'),
					params : {
						where : 'nIdLibro=' + idlibro
					},
					fnok : fn
				});
			}
		}
	});

	form.show();
	return;
}
var addMateria = function(idlibro, fn) {

	var form_id = Ext.app.createId();

	var seccion = (Ext.app.autocomplete2({
		url : site_url('catalogo/materia/search'),
		fieldLabel : _s('Materia'),
		anchor : '90%',
		name : 'nIdMateria',
		allowBlank : true
	}));

	var controls = [seccion, {
		xtype : 'hidden',
		name : 'nIdLibro',
		value : idlibro
	}];

	var url = site_url('catalogo/articulomateria/add');
	var form = Ext.app.formStandarForm({
		controls : controls,
		url : url,
		icon : 'iconoMateriasTab',
		title : _s('Añadir Materia'),
		width : 530,
		fn_ok : function(res) {
			if(fn != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/articulomateria/get_list'),
					params : {
						where : 'nIdLibro=' + idlibro
					},
					fnok : fn
				});
			}
		}
	});

	form.show();
	return;
}
var addProveedor = function(idlibro, fn) {

	var form_id = Ext.app.createId();

	var seccion = (Ext.app.autocomplete2({
		url : site_url('proveedores/proveedor/search'),
		fieldLabel : _s('Proveedor'),
		anchor : '90%',
		name : 'nIdProveedor',
		allowBlank : true
	}));

	var controls = [seccion, {
		xtype : 'hidden',
		name : 'nIdLibro',
		value : idlibro
	}, {
		xtype : 'numberfield',
		name : 'fDescuento',
		fieldLabel : _s('Descuento'),
		width : 30
	}];

	var url = site_url('catalogo/proveedorarticulo/add');
	var form = Ext.app.formStandarForm({
		controls : controls,
		url : url,
		icon : 'iconoProveedoresTab',
		title : _s('Añadir Proveedor'),
		width : 530,
		fn_ok : function(res) {
			if(fn != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/proveedorarticulo/get_list'),
					params : {
						where : 'nIdLibro=' + idlibro
					},
					fnok : fn
				});
			}
		}
	});

	form.show();
	return;
}
var addPromocion = function(idlibro, fn) {

	var form_id = Ext.app.createId();

	var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('catalogo/tipopromocion/search'),
		fieldLabel : _s('Tipo'),
		anchor : '90%',
		name : 'nIdTipoPromocion',
		allowBlank : true
	}));

	var controls = [seccion, {
		value : new Date(),
		fieldLabel : _s('Desde'),
		name : 'dInicio',
		allowBlank : false,
		startDay : Ext.app.DATESTARTDAY,
		xtype : "datefield"
	}, {
		fieldLabel : _s('Hasta'),
		name : 'dFinal',
		allowBlank : true,
		startDay : Ext.app.DATESTARTDAY,
		xtype : "datefield"
	}, {
		xtype : 'hidden',
		name : 'nIdLibro',
		value : idlibro
	}];

	var url = site_url('catalogo/promocion/add');
	var form = Ext.app.formStandarForm({
		controls : controls,
		url : url,
		icon : 'iconoPromocionesTab',
		title : _s('Añadir Promoción'),
		width : 530,
		fn_ok : function(res) {
			if(fn != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/promocion/get_list'),
					params : {
						where : 'nIdPromocion=' + res.id
					},
					fnok : fn
				});
			}
		}
	});

	seccion.store.load();
	form.show();
	return;
}
var addSeccion = function(idlibro, fn) {

	var form_id = Ext.app.createId();

	var seccion = (Ext.app.autocomplete2({
		url : site_url('generico/seccion/search'),
		fieldLabel : _s('Sección'),
		anchor : '90%',
		name : 'nIdSeccion',
		allowBlank : true
	}));

	var controls = [seccion, {
		xtype : 'hidden',
		name : 'nIdLibro',
		value : idlibro
	}, new Ext.ux.form.Spinner({
		fieldLabel : _s('Mínimo'),
		name : 'nStockMinimo',
		value : 0,
		width : 60,
		strategy : new Ext.ux.form.Spinner.NumberStrategy()
	}), new Ext.ux.form.Spinner({
		fieldLabel : _s('Máximo'),
		name : 'nStockMaximo',
		value : 0,
		width : 60,
		strategy : new Ext.ux.form.Spinner.NumberStrategy()
	})];

	var url = site_url('catalogo/articuloseccion/add');
	var form = Ext.app.formStandarForm({
		controls : controls,
		url : url,
		icon : 'iconoSeccionTab',
		title : _s('Añadir Sección'),
		width : 530,
		fn_ok : function(res) {
			if(fn != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/articuloseccion/get_list'),
					params : {
						where : 'nIdLibro=' + idlibro
					},
					fnok : fn
				});
			}
		}
	});

	form.show();
	return;
}
var addAutor = function(idlibro, fn) {

	var form_id = Ext.app.createId();

	var tipoautor = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('catalogo/tipoautor/search'),
		name : 'nIdTipoAutor',
		width : 100,

		allowBlank : true
	}));

	var autor = new Ext.form.ComboBox(Ext.app.autocomplete2({
		url : site_url('catalogo/autor/search'),
		width : 230,
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
		var id = tipoautor.getValue();
		if(id < 1) {
			tipoautor.setValue(1);
		}

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
	var id = Ext.app.createId();

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

	var grid2 = new Ext.grid.GridPanel({
		region : 'center',
		autoExpandColumn : "descripcion",
		loadMask : true,
		stripeRows : true,
		store : store2,
		height : 200,
		width : 490,
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

	var controls = [{
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
	}, {
		xtype : 'hidden',
		name : 'autores',
		id : form_id + '_aut'
	}, {
		xtype : 'hidden',
		name : 'id',
		value : idlibro
	}, grid2];

	var form = Ext.app.formStandarForm({
		controls : controls,
		icon : 'iconoAutoresTab',
		title : _s('Añadir Autor'),
		width : 530,
		fn_ok : function(res) {
			var params = {
				id : idlibro
			}
			var index = 0;
			grid2.getStore().each(function(r) {
				params['autores[' + index + '][nIdAutor]'] = r.data.id;
				params['autores[' + index + '][nIdTipoAutor]'] = (r.data.idtipo > 0) ? r.data.idtipo : 1;
				index++;
			});
			Ext.app.callRemote({
				url : site_url('catalogo/articulo/upd'),
				params : params,
				fnok : function() {
					if(fn != null) {
						Ext.app.callRemote({
							url : site_url('catalogo/articuloautor/get_list'),
							params : {
								where : 'nIdLibro=' + idlibro
							},
							fnok : fn
						});
					}
				}
			});
		}
	});

	tipoautor.store.load();

	form.show();
	return;
}
/**
 * Enviar un documento
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            title
 * @param {Object}
 *            url
 */
var documentosEnviar = function(form, title, url, fn) {
	Ext.app.callRemote({
		url : url,
		params : {
			id : form.getId(),
			email : true,
			fax : true
		},
		fnok : function(res) {
			if(res.success)
			{
				if (fn != null)
					fn(res);
				else
					form.refresh();
			}				
		}
	});
	return;
	var controls = [{
		xtype : 'checkbox',
		id : 'email',
		allowBlank : true,
		fieldLabel : _s('Email'),
		checked : true
	}, {
		xtype : 'checkbox',
		id : 'fax',
		allowBlank : true,
		fieldLabel : _s('Fax'),
		checked : true
	}, {
		xtype : 'checkbox',
		id : 'imprimir',
		allowBlank : true,
		fieldLabel : _s('Imprimir'),
		checked : true
	}, {
		xtype : 'hidden',
		id : 'id',
		value : form.getId()
	}];

	var form2 = Ext.app.formStandarForm({
		controls : controls,
		icon : 'icon-send',
		title : title,
		labelWidth : 100,
		url : url,
		fn_ok : function(res) {
			form.refresh();
		}
	});
	form2.show();
	return;
}
/**
 * Menú liquidar stock
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            url
 */
var addButtonLiquidarStock = function(form, url) {
	return {
		text : _s('Liquidar stock de una sección'),
		handler : function() {
			documentosLiquidarStock(form, url);
		},
		iconCls : 'icon-tool',
		id : form.idform + 'btn_liquidarstock'
	};
}
/**
 * Acción de liquidar stock de una sección
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            url
 */
var documentosLiquidarStock = function(form, url) {
	var fn = function() {
		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('generico/seccion/search'),
			name : 'seccion',
			anchor : '100%',
			label : _s('Sección')
		}));

		var cliente = Ext.app.autocomplete2({
			fieldLabel : _s('Cliente'),
			name : 'cliente',
			anchor : '100%',
			url : site_url('clientes/cliente/search')
		});

		var controls = [cliente, seccion];

		var form2 = Ext.app.formStandarForm({
			controls : controls,
			icon : 'icon-tools',
			title : _s('Liquidar stock de una sección'),
			labelWidth : 100,
			url : url,
			fn_ok : function(res) {
				if(res.id != null) {
					form.load(res.id);
				}
			}
		});
		Ext.app.loadStores([{
			store : seccion.store
		}]);
		form2.show();
		return;

	}
	if(form.isDirty()) {
		Ext.Msg.show({
			title : _s('Liquidar stock de una sección'),
			buttons : Ext.MessageBox.YESNOCANCEL,
			msg : _s('register-dirty-lost'),
			fn : function(btn, text) {
				if(btn == 'yes') {
					form.setDirty(false);
					fn();
				}
			}
		});
	} else
		fn()
}
/**
 * Menú pedir un título
 *
 * @param {Object}
 *            lineas
 */
var addMenuPedir = function(lineas) {
	var menu = lineas.getContextMenu();
	var m = menu.add({
		text : _s('Pedir'),
		handler : function() {
			// console.log('En menu Pedir');
			var record = lineas.getItemSelect();
			if(record != null) {
				// console.dir(record.data);
				Ext.app.execCmd({
					url : site_url('compras/reposicion/pedir_uno/' + record.data.nIdLibro + '/' + record.data.nIdSeccion + '/' + record.data.nCantidad)
				});
			}
		},
		iconCls : 'icon-pedir'
	});
	return m;
}
/**
 * Menú reservar un título
 *
 * @param {Object}
 *            lineas
 */
var addMenuReservar = function(lineas) {
	var menu = lineas.getContextMenu();
	var m = menu.add({
		text : _s('Reservar'),
		handler : function() {
			var record = lineas.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : site_url('ventas/pedidocliente/reservar/' + record.data.nIdLibro + '/' + record.data.nIdSeccion + '/' + record.data.nCantidad)
				});
			}
		},
		iconCls : 'iconoPedidoCliente'
	});
	return m;
}
/**
 * Menú separador
 *
 * @param {Object}
 *            lineas
 */
var addMenuSeparator = function(lineas) {
	var menu = lineas.getContextMenu();
	var m = menu.add('-');
	return m;
}
/**
 * Menú pedir a proveedor
 *
 * @param {Object}
 *            lineas
 */
var addMenuPedir = function(lineas) {
	var menu = lineas.getContextMenu();
	var m = menu.add({
		text : _s('Pedir'),
		handler : function() {
			// console.log('En menu Pedir');
			var record = lineas.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : site_url('compras/reposicion/pedir_uno/' + record.data.nIdLibro + '/' + record.data.nIdSeccion + '/' + record.data.nCantidad)
				});
			}
		},
		iconCls : 'icon-pedir'
	});
	return m;
}
/**
 * Menú de documentos
 *
 * @param {Object}
 *            lineas
 */
var addMenuDocumentos = function(lineas) {
	var menu = lineas.getContextMenu();

	var m = menu.add({
		text : _s('documentos_articulo_form'),
		handler : function() {
			var record = lineas.getItemSelect();
			if(record != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/documentos/' + record.data.nIdLibro)
				});
			}
		},
		iconCls : 'icon-documents'
	});
}
/**
 * Menú de ventas
 *
 * @param {Object}
 *            lineas
 */
var addMenuVentas = function(lineas) {
	var menu = lineas.getContextMenu();

	var m = menu.add({
		text : _s('Ventas'),
		handler : function() {
			var record = lineas.getItemSelect();
			if(record != null) {
				Ext.app.callRemote({
					params : {
						idl : record.data.nIdLibro,
						tipo : 'entdevcmp;salcmp'
					},
					url : site_url('catalogo/articulo/documentos')
				});
			}
		},
		iconCls : 'iconoFacturacion'
	});
}
/**
 * Menú de compras
 *
 * @param {Object}
 *            lineas
 */
var addMenuCompras = function(lineas) {
	var menu = lineas.getContextMenu();

	var m = menu.add({
		text : _s('Compras'),
		handler : function() {
			var record = lineas.getItemSelect();
			if(record != null) {
				Ext.app.callRemote({
					params : {
						idl : record.data.nIdLibro,
						tipo : 'entdev;entalb'
					},
					url : site_url('catalogo/articulo/documentos')
				});
			}
		},
		iconCls : 'iconoAlbaranEntrada'
	});
}
var addMenuStock = function(lineas, form) {
	var menu = lineas.getContextMenu();

	var m = menu.add({
		text : _s('Stock'),
		handler : function() {
			var record = lineas.getItemSelect();
			if(record != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/stock/' + record.data.nIdLibro)
				});
			}
		},
		iconCls : 'iconoStocks'
	});
}
/**
 * Menú de cancelar una línea
 *
 * @param {Object}
 *            menu
 * @param {Object}
 *            grid
 * @param {Object}
 *            url
 */
var addMenuCancelar = function(form, lineas, url) {
	return addMenuGeneral(_s('Cancelar'), form, lineas, 'icon-cancel', function(record) {
		return url + '/' + record.data.nIdLinea;
	});
}
var addMenuGeneral = function(text, form, lineas, icon, fn) {
	var menu = lineas.getContextMenu();
	var m = menu.add({
		text : text,
		handler : function() {
			var record = lineas.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : fn(record),
					fnok : function() {
						if(form != null)
							form.refresh();
					}
				});
			}
		},
		iconCls : icon
	});

	return m;
}
/**
 * Elige la sección por defecto
 *
 * @param {Object}
 *            fn_get_data
 * @param {Object}
 *            fn_set_data
 * @param {Object}
 *            defecto
 * @param {Object}
 *            vedadas
 * @param {Object}
 *            allsecciones
 * @param {Object}
 *            default_exist
 */
var get_seccion_defecto = function(fn_get_data, fn_set_data, defecto, vedadas, allsecciones, default_exist, haystock) {

	var secciondefecto = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('generico/seccion/search'),
		name : 'seccion',
		anchor : '50%',
		label : _s('Sección def.')
	}));
	var config_defecto = defecto;
	var config_vedadas = vedadas;
	if(haystock == null)
		haystock = false;

	var fn_get_seccion = function(secciones) {
		var data = fn_get_data();
		if(data.s_defecto == null) {
			data.s_defecto = Ext.app.get_config(config_defecto);
			if(data.s_defecto != '')
				data.s_defecto = data.s_defecto.split(';');
			else
				data.s_defecto = new Array();
			fn_set_data({
				s_defecto : data.s_defecto
			});
		}
		if(data.s_vedadas == null) {
			data.s_vedadas = Ext.app.get_config(config_vedadas);
			if(data.s_vedadas != '')
				data.s_vedadas = data.s_vedadas.split(';');
			else
				data.s_vedadas = new Array();
			fn_set_data({
				s_vedadas : data.s_vedadas
			});
		}
		var defecto = new Array();
		var otras = new Array();

		var select = secciondefecto.getValue();
		if(select == '')
			select = null;

		var seleccionado = null;

		Ext.each(secciones, function(item) {
			var stock = parseInt(item.nStockFirme) + parseInt(item.nStockDeposito);
			if(!haystock || (haystock && (stock > 0))) {
				if(select == item.nIdSeccion) {
					// Es una por defecto, y ya existe
					seleccionado = {
						select : {
							'id' : item.nIdSeccion,
							'text' : item.cNombre + '(' + (stock) + ')',
							'stock' : stock
						},
						secciones : null
					}
					return false;
				} else if(!in_array(item.nIdSeccion, data.s_vedadas) || (allsecciones)) {
					// var stock = item.nStockFirme + item.nStockDeposito;
					var el = {
						'id' : item.nIdSeccion,
						'text' : item.cNombre + '(' + (stock) + ')',
						'stock' : stock
					}
					if(in_array(item.nIdSeccion, data.s_defecto) && (!allsecciones)) {
						defecto.push(el);
					} else {
						if(stock != 0 || (allsecciones)) {
							otras.push(el);
						}
					}
				}
			}
		});
		if(seleccionado != null)
			return seleccionado;

		defecto.sort(function(a, b) {
			return (b.stock - a.stock);
		});
		if((select != null) && !default_exist) {
			return {
				select : {
					'id' : select,
					'text' : secciondefecto.getRawValue(),
					'stock' : 0
				},
				secciones : null
			}
		}
		if(defecto.length == 1 || ((defecto.length > 1) && (defecto[0].stock != 0) && (defecto[1].stock == 0))) {
			return {
				select : defecto[0],
				secciones : defecto
			}
		}
		defecto = defecto.concat(otras);

		defecto.sort(function(a, b) {
			return (b.stock - a.stock);
		});
		return {
			select : null,
			secciones : defecto
		}
	}
	return {
		fn_get_seccion : fn_get_seccion,
		secciondefecto : secciondefecto
	}
}
/**
 * Crea los controles para añadir items a un documento de cliente
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            direccion_field
 * @param {Object}
 *            fn_get_data
 * @param {Object}
 *            fn_set_data
 */
var documentosCliente = function(form, direccion_field, fn_get_data, fn_set_data, perfil) {
	return documentosClienteProveedor(form, direccion_field, fn_get_data, fn_set_data, _s('Cliente'), site_url('clientes/perfilcliente/get_list'), site_url('clientes/cliente/search'), site_url('clientes/cliente/index'), site_url('clientes/cliente/alta'), 'nIdCliente', site_url('clientes/cliente/info'), perfil);
}
/**
 * Crea los controles para añadir items a un documento de proveedor
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            direccion_field
 * @param {Object}
 *            fn_get_data
 * @param {Object}
 *            fn_set_data
 */
var documentosProveedor = function(form, direccion_field, fn_get_data, fn_set_data, perfil) {
	return documentosClienteProveedor(form, direccion_field, fn_get_data, fn_set_data, _s('Proveedor'), site_url('proveedores/perfilproveedor/get_list'), site_url('proveedores/proveedor/search'), site_url('proveedores/proveedor/index'), site_url('proveedores/proveedor/alta'), 'nIdProveedor', site_url('proveedores/proveedor/info'), perfil);
}
var documentosClienteProveedor = function(form, direccion_field, fn_get_data, fn_set_data, label, url_list, url_search, url_open, url_add, id_name, url_info, perfil) {

	var direcciones = Ext.app.combobox({
		url : url_list,
		width : Ext.app.DIRECCIONESCOMBOWIDTH,
		extrafields : ['nIdPais'],
		name : direccion_field
	});

	var fn_load_direcciones = function(id, select_id) {
		var data = fn_get_data();
		var res = fn_docs_load_direcciones({
			select_id : select_id,
			id : id,
			cliente_datos : data.cliente_datos,
			info_button : data.info_button,
			data_load : data.data_load,
			perfil : perfil,
			title : data.title,
			direcciones : data.direcciones,
			id_name : id_name,
			url_info : url_info,
			fn : function(res) {
				fn_set_data(res);
			}
		});
		fn_set_data({
			tooltip_cliente : ''
		});
	}

	var clientefield = new Ext.form.ComboBox(Ext.app.autocomplete({
		fieldLabel : label,
		width : Ext.app.CLIENTEFIELDWIDTH,
		url : url_search,
		fnselect : function(id) {
			fn_set_data({
				cliente_id : id,
				tooltip_cliente : ''
			});

			var data = fn_get_data();
			if(data.data_load == null || (data.data_load != null && data.data_load.nIdCliente != id))
				form.setDirty();

			fn_load_direcciones(id);
		},
		create : true
	}));

	var fn_load_cliente = function(id) {
		fn_set_data({
			cliente_id : id
		});
		fn_docs_load_cliente({
			id : id,
			clientefield : clientefield
		});
	}

	clientefield.load = function(id) {
		fn_load_direcciones(id);
		fn_load_cliente(id);
	}
	var info_button = new Ext.Button({
		xtype : 'tbbutton',
		iconCls : "icon-info",
		tooltip : _s('info-clienteproveedor'),
		handler : function(b) {
			var data = fn_get_data();
			Ext.app.msgFly2(data.title, data.tooltip_cliente, true);
		}
	});

	var cliente = {
		xtype : 'compositefield',
		fieldLabel : label,
		msgTarget : 'side',
		anchor : '100%',
		items : [clientefield, info_button, {
			xtype : 'tbbutton',
			iconCls : "icon-edit",
			tooltip : _s('ver-clienteproveedor'),
			handler : function() {
				var data = fn_get_data();
				if(data.cliente_id != null) {
					Ext.app.execCmd({
						url : url_open + '/' + data.cliente_id
					});
				}
			}
		}, {
			xtype : 'tbbutton',
			iconCls : "icon-add",
			tooltip : _s('nuevo-clienteproveedor'),
			handler : function() {
				var c = Ext.getCmp(clientefield.id);

				Ext.app.callRemote({
					url : url_add,
					params : {
						text : c.getValue(),
						cmpid : c.getId()
					}
				});
			}
		}, {
			xtype : 'displayfield',
			value : _s('Dirección')
		}, direcciones]
	}

	return {
		cliente : cliente,
		info_button : info_button,
		clientefield : clientefield,
		direcciones : direcciones,
		fn_load_direcciones : fn_load_direcciones,
		fn_load_cliente : fn_load_cliente
	}
}
/**
 * Añade los tabs generales a los documentos
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            controls
 */
var documentosAddTabs = function(form, controls, icon) {

	// General
	form.addTab({
		title : _s('General'),
		iconCls : 'icon-general',
		items : {
			xtype : 'panel',
			layout : 'form',
			cls : icon,
			items : form.addControls(controls)
		}
	});

	/*-------------------------------------------------------------------------
	* Resto de TABS
	*-------------------------------------------------------------------------
	*/
	// Notas
	var notas1 = Ext.app.formHtmlEditor({
            id: 'tNotasExternas',
            anchor: '100% 91%'
        })[0];
	/*{
		xtype : 'textarea',
		id : 'tNotasExternas',
		anchor : '100% 91%'
	};*/
	form.addTab({
		title : _s('tNotasExternas'),
		iconCls : 'icon-notes',
		items : form.addControls([notas1])
	});

	var notas2 = Ext.app.formHtmlEditor({
            id: 'tNotasInternas',
            anchor: '100% 91%'
        })[0];
	/*{
		xtype : 'textarea',
		id : 'tNotasInternas',
		anchor : '100% 91%'
	};*/
	form.addTab({
		title : _s('tNotasInternas'),
		iconCls : 'icon-notes',
		items : form.addControls([notas2])
	});

	// Usuarios
	form.addTabUser();
}
/**
 * Acción de ajustar márgenes
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            lineas
 */
var documentosAjustarMargen = function(form, lineas) {
	Ext.Msg.prompt(form.getTitle(), _s('Margen mínimo'), function(ok, v) {
		if(ok != 'ok')
			return;
		v = parseFloat(v);
		lineas.aplicarMargen(v, true);
	}, null, null, Ext.app.MARGEN_MINIMO);
}
/**
 * Convierte a negativo las cantidades
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            lineas
 */
var documentosNegativo = function(form, lineas) {
	lineas.aplicarNegativo();
}
/**
 * Menú ajustar márgen
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            lineas
 */
var addButtonAjustarMargen = function(form, lineas) {
	form.addAction({
		text : _s('Ajustar margen'),
		handler : function() {
			documentosAjustarMargen(form, lineas);
		},
		iconCls : 'icon-actions',
		id : form.idform + 'btn_ajustarmargen'
	});
}
var addButtonNegativo = function(form, lineas) {
	form.addAction({
		text : _s('Pasar cantidades a negativas'),
		handler : function() {
			documentosNegativo(form, lineas);
		},
		iconCls : 'icon-actions',
		id : form.idform + 'btn_negativo'
	});
}
/**
 * Acción de documentos abiertos
 *
 * @param {Object}
 *            form
 */
var documentosAbiertos = function(form) {
	form.search('', 'nIdEstado=1', true);
}
/**
 * Menú de documentos abiertos
 *
 * @param {Object}
 *            form
 */
var addButtonAbiertos = function(form) {
	form.addAction({
		text : _s('Documentos abiertos'),
		handler : function() {
			documentosAbiertos(form);
		},
		iconCls : 'icon-doc-abiertos',
		id : form.idform + 'btn_docs_open'
	});
}
/**
 * Acciónd de abonar un documento
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            url
 */
var documentosAbonar = function(form, url) {
	var fn = function() {
		Ext.app.callRemote({
			url : site_url(url),
			params : {
				id : form.getId()
			},
			fnok : function(res) {
				if(res.id != null) {
					form.load(res.id);
				}
			}
		});
	}
	if(form.isDirty()) {
		Ext.Msg.show({
			title : _s('Abonar'),
			buttons : Ext.MessageBox.YESNOCANCEL,
			msg : _s('register-dirty-lost'),
			fn : function(btn, text) {
				if(btn == 'yes') {
					form.setDirty(false);
					fn();
				}
			}
		});
	} else
		fn()
}
/**
 * Menú de abonar un documento
 *
 * @param {Object}
 *            form
 * @param {Object}
 *            url
 */
var addButtonAbonar = function(form, url) {
	form.addAction({
		text : _s('Abonar'),
		handler : function() {
			documentosAbonar(form, url);
		},
		iconCls : 'icon-doc-abonar',
		id : form.idform + 'btn_abonar'
	});
}
/**
 * Muestra las líneas de pedido
 *
 * @param {Object}
 *            id
 * @param {Object}
 *            fn
 */
var fn_docs_select_lineas_pedido = function(id, fn) {
	var model = [{
		name : 'nIdLibro'
	}, {
		name : 'id'
	}, {
		name : 'nCantidad'
	}, {
		name : 'nIdSeccion'
	}, {
		name : 'cAutores'
	}, {
		name : 'cTitulo'
	}, {
		name : 'cSeccion'
	}, {
		name : 'nIdEstado'
	}, {
		name : 'cEstado'
	}, {
		name : 'fDescuento'
	}, {
		name : 'fPVP'
	}, {
		name : 'fPVP2'
	}, {
		name : 'fPrecio'
	}, {
		name : 'fPrecio2'
	}, {
		name : 'fBase'
	}, {
		name : 'fCoste'
	}, {
		name : 'cRefCliente'
	}, {
		name : 'fIVA'
	}];

	var url = site_url("ventas/pedidoclientelinea/pendientes");
	var store = Ext.app.createStore({
		model : model,
		url : url
	});

	var sm = new Ext.grid.CheckboxSelectionModel();

	var columns = [sm, {
		header : _s("Id"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'nIdLibro',
		sortable : true
	}, {
		header : _s("nCantidad"),
		dataIndex : 'nCantidad',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}, {
		header : _s("cAutores"),
		dataIndex : 'cAutores',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		id : 'descripcion',
		header : _s("cTitulo"),
		dataIndex : 'cTitulo',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		header : _s("Estado"),
		dataIndex : 'cEstado',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		header : _s("Referencia"),
		dataIndex : 'cRefCliente',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		header : _s("fDescuento"),
		dataIndex : 'fDescuento',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}, {
		header : _s("fPVP"),
		dataIndex : 'fPVP',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}];

	var grid = new Ext.grid.GridPanel({
		store : store,
		anchor : '95% 80%',
		height : 400,
		autoExpandColumn : 'descripcion',
		stripeRows : true,
		loadMask : true,
		sm : sm,

		bbar : Ext.app.gridBottom(store, true),

		// grid columns
		columns : columns
	});

	var actualizar = new Ext.form.Checkbox({
		checked : Ext.app.PEDIDOS_ACTUALIZAR_PRECIOS,
		allowBlank : true
	});

	var copiarreferencia = new Ext.form.Checkbox({
		checked : true,
		allowBlank : true
	});

	var seleccionar = new Ext.Button({
		xtype : 'tbbutton',
		iconCls : "icon-filter",
		text : _s('pedidos-seleccionar-servir'),
		handler : function() {
			var sel = grid.getSelectionModel();
			sel.clearSelections();
			var selectedRecords = new Array();
			grid.getStore().each(function(r) {
				if(r.data.nIdEstado == 3) {
					selectedRecords.push(r);
				}
			});
			sel.selectRecords(selectedRecords, true);
		}
	});

	var catalogados = new Ext.Button({
		xtype : 'tbbutton',
		iconCls : "icon-filter",
		text : _s('pedidos-seleccionar-catalogados'),
		handler : function() {
			var sel = grid.getSelectionModel();
			sel.clearSelections();
			var selectedRecords = new Array();
			grid.getStore().each(function(r) {
				if(r.data.nIdEstado == 6) {
					selectedRecords.push(r);
				}
			});
			sel.selectRecords(selectedRecords, true);
		}
	});

	var controls = [{
		xtype : 'compositefield',
		style : 'text-align: left; align: left;',
		hideLabel : true,
		msgTarget : 'side',
		anchor : '-20',
		items : [actualizar, {
			xtype : 'displayfield',
			value : _s('Actualizar precios')
		}, copiarreferencia, {
			xtype : 'displayfield',
			value : _s('Copiar referencias')
		}, seleccionar, catalogados]
	}, grid];

	var form = Ext.app.formStandarForm({
		controls : controls,
		autosize : false,
		labelWidth : 200,
		height : 500,
		icon : 'iconoPedidoClienteTab',
		width : 700,
		title : _s('Pedido cliente'),
		fn_ok : function() {
			var sel = grid.getSelectionModel().getSelections();
			var ids = [];
			var act = actualizar.getValue();
			Ext.each(sel, function(item) {
				if((item.data.fPVP == 0) || act)
					item.data.fPVP = item.data.fPVP2;
				item.data.fPrecio = item.data.fPrecio2;
				item.data.cReferencia = item.data.cRefCliente;
				ids.push(item.data);
			});
			if(fn != null)
				fn(ids, copiarreferencia.getValue());
		}
	});

	store.baseParams = {
		id : id
	};

	store.load();
	form.show();
}
/**
 * Carga un cliente
 *
 * @param {Object}
 *            config
 */
var fn_docs_load_cliente = function(config) {
	var id = config['id'];
	var clientefield = config['clientefield'];

	clientefield.clearValue();
	clientefield.store.removeAll();
	clientefield.store.load({
		params : {
			query : id,
			start : 0,
			limit : Ext.app.AUTOCOMPLETELISTSIZE
		},
		callback : function(c) {
			clientefield.setValue(id);
		}
	});
}
/**
 * Carga direcciones
 *
 * @param {Object}
 *            config
 */
var fn_docs_load_direcciones = function(config) {
	var id = config['id'];
	var select_id = config['select_id'];
	var cliente_datos = config['clientes_datos'];
	var info_button = config['info_button'];
	var data_load = config['data_load'];
	var direcciones = config['direcciones'];
	var title = config['title'];
	var tooltip_cliente = null;
	var fn = config['fn'];
	var id_name = config['id_name'];
	var url_info = config['url_info'];
	var perfil = config['perfil'];
	if(perfil == null)
		perfil = Ext.app.PERFIL_GENERAL;

	if(cliente_datos != null)
		if(id == cliente_datos[id_name]) {
			return;
		}
	cliente_datos = null;

	if(id != null) {
		Ext.app.callRemote({
			url : url_info,
			params : {
				id : id
			},
			nomsg : true,
			fnok : function(res) {
				tooltip_cliente = res.value_data.info;
				cliente_datos = res.value_data;
				if(tooltip_cliente != null) {
					if (info_button) {
						if(info_button.el != null) {
							Ext.get(info_button.el).stopFx();
							Ext.get(info_button.el).frame("ff0000", 1, {
								duration : 3
							});
						}
						// Ext.app.msgFly(title, tooltip_cliente);
						info_button.setTooltip(new Ext.ToolTip({
							text : tooltip_cliente,
							title : _s('info-clienteproveedor'),
							autoHide : false,
							closable : true,
							draggable : true
						}));
					}
				} else {
					if (info_button)
						info_button.setTooltip(new Ext.ToolTip({
							text : _s('cliente-no-info'),
							title : _s('info-clienteproveedor'),
							autoHide : false,
							closable : true,
							draggable : true
						}));
				}

				if(fn != null) {
					var res = {
						tooltip_cliente : tooltip_cliente,
						cliente_datos : cliente_datos
					};
					fn(res);
				}
			}
		})
	}

	if(select_id == null && data_load != null)
		select_id = data_load.nIdDireccion;

	load_combo_direcciones(id, direcciones, select_id, perfil, fn);
}

var load_combo_direcciones = function(id, direcciones, select_id, perfil, fn, solo_perfil) {
	var d = Ext.getCmp(direcciones.id);
	var s = d.getStore();
	d.setValue(null);
	d.reset();
	s.removeAll();
	if(id != null) {
		s.load({
			params : {
				tipo : 'D',
				'long' : true,
				id : id
			},
			callback : function() {
				//console.log('Seleccionando id ' + select_id + ' perfil ' + perfil + ' solo perfil ' + solo_perfil);
				if(select_id != null || (select_id <= 0 && perfil == null)) {
					if (select_id > 0)
						d.setValue(parseInt(select_id));
				} else {
					var i = null;
					var count = 0;
					//console.log('buscando perfil ' + perfil);
					s.each(function(r) {
						//console.dir(r);
						if(r.json != null) {
							//console.log('perfil r ' + r.json.id_perfil);
							if(r.json.id_perfil == perfil) {
								i = count;
								return false;
							} else if(r.json.id_perfil == Ext.app.PERFIL_GENERAL && solo_perfil !== true) {
								i = count;
								//return false;
							}
						}
						count++;
					});
					if(!is_int(i)) {
						//console.log('no se ha encontrado, cojo el primero');
						if(count > 1 && select_id == null) {
							d.setValue(s.getAt(1).data.id);
							select_id = s.getAt(1).data.id;
						} else {
							d.setValue(s.getAt(0).data.id);
							select_id = s.getAt(0).data.id;
						}
					} else {
						select_id = s.getAt(i).data.id;
						//console.log('SE ha encontrado, ' + i + ' = ' + select_id);
						d.setValue(parseInt(select_id));
					}
				}
				if(fn != null) {
					var res = {
						id_direccion : parseInt(select_id)
					};
					fn(res);
				}
			}
		});
	}

}
/**
 * Control de las líneas de un documento
 *
 * @param {Object}
 *            config
 */
var docLineaControl = function(config) {
	try {
		// Parámetros
		// @TODO Convertirlos a un extend!!!
		var fn_get_seccion = config['fn_get_seccion'];
		var url_search = config['url_search'];
		var url_load = config['url_load'];
		var url_descuentos = config['url_descuentos'];
		var fn_change = config['fn_change'];
		var fn_get_descuento = config['fn_get_descuento'];
		var nIdLinea = config['nIdLinea'];
		var nIdDocumento = config['nIdDocumento'];
		var cReferencia = config['cReferencia'];
		var hide = config['hide'];
		var anchor = config['anchor'];
		var extrafields = config['extrafields'];
		var fn_check_ident = config['fn_check_ident'];
		var coste = config['coste'];
		var getRowClass = config['getRowClass'];
		var resetextrafields = config['resetextrafields'];
		if(resetextrafields == null)
			resetextrafields = true;
		if(coste == null)
			coste = true;
		var firmedeposito = config['firmedeposito'];
		if(firmedeposito == null)
			firmedeposito = true;
		var use_secciones = config['use_secciones'];
		if(use_secciones == null)
			use_secciones = true;
		var autoselect = config['autoselect'];
		if(autoselect == null)
			autoselect = true;
		var base = config['base'];
		if(base == null)
			base = false;
		var margen_error = config['margen_error'];
		if(margen_error == null)
			margen_error = true;

		var introadd = config['introadd'];
		if(introadd == null)
			introadd = true;
		var use_creation = config['use_creation'];
		if(use_creation == null)
			use_creation = false;

		var cache = config['cache'];
		if(cache == null)
			cache = false;
		hide = (hide != null) ? hide.split(';') : new Array();
		var aplicar_recargo = false;
		var exentoIVA = false;
		var tarifas = null;
		var tarifas_general = null;

		var last_documento = null;

		var lineas_delete = Array();

		var enable_control = true;

		// Store de datos
		var storefields = {
			id : 'id',
			model : [{
				name : 'nIdLinea'
			}, {
				name : 'nIdDocumento'
			}, {
				name : 'nIdSeccion'
			}, {
				name : 'secciones'
			}, {
				name : 'nIdLibro'
			}, {
				name : 'cTitulo'
			}, {
				name : 'nCantidad'
			}, {
				name : 'nEnFirme'
			}, {
				name : 'nEnDeposito'
			}, {
				name : 'fPVP'
			}, {
				name : 'fImporte'
			}, {
				name : 'fCoste'
			}, {
				name : 'fMargen'
			}, {
				name : 'fIVA'
			}, {
				name : 'fRecargo'
			}, {
				name : 'nIdOferta'
			}, {
				name : 'bNoDto'
			}, {
				name : 'fDescuento'
			}, {
				name : 'fPrecio',
			}, {
				name : 'fBase'
			}, {
				name : 'fIVAImporte'
			}, {
				name : 'fRecargoImporte'
			}, {
				name : 'fTotal'
			}, {
				name : 'cSeccion'
			}, {
				name : 'cReferencia'
			}, {
				name : 'cRefInterna'
			}, {
				name : 'cCUser'
			}, {
				name : 'dCreacion'
			}, {
				name : 'cAUser'
			}, {
				name : 'dAct'
			}, {
				name : 'bNuevo'
			}]
		};

		// Añade las campos extras
		if(extrafields != null) {
			Ext.each(extrafields, function(item) {
				storefields['model'][storefields['model'].length] = {
					name : item.dataIndex
				}
			});
		}

		var rt = Ext.data.Record.create(storefields);
		var store = new Ext.data.ArrayStore({
			fields : storefields.model
		});
		store.on('update', function(s, r, o) {
			if(fn_change)
				fn_change(s);
		});

		store.on('add', function(s, r, i) {
			if(fn_change)
				fn_change(s, r);
		});

		store.on('remove', function(s, r, i) {
			if(r.data.nIdLinea != null)
				lineas_delete.push(r.data.nIdLinea);
			if(fn_change)
				fn_change(s, r);
		});
		// Controles
		var cantidad = new Ext.form.NumberField({
			enableKeyEvents : true,
			selectOnFocus : true,
			width : 30
		});
		var descuento = new Ext.form.NumberField({
			enableKeyEvents : true,
			selectOnFocus : true,
			width : 30
		});
		var importe = new Ext.form.NumberField({
			enableKeyEvents : true,
			selectOnFocus : true,
			width : 50
		});
		if(use_secciones) {
			var seccion = new Ext.form.ComboBox(Ext.app.combobox({
				enableKeyEvents : true,
				allowBlank : true
			}));
		}
		var articulo = new Ext.form.TextField({
			readOnly : true,
			width : 200
		});
		var referencia = new Ext.form.TextField({
			selectOnFocus : true,
			width : 100
		});
		var info = new Ext.form.Label({
			width : 200
		});

		// TextBox de control de comandos
		var control = new Ext.ux.form.TextID({
			/* width: '80', */
			cache : cache,
			autoselect : autoselect,
			base : base,
			url_search : url_search,
			url_load : url_load,
			url_descuentos : url_descuentos,
			fn_get_seccion : fn_get_seccion,
			fn_get_descuento : fn_get_descuento,
			cantidadField : cantidad,
			descuentoField : descuento,
			importeField : importe,
			seccionField : (use_secciones) ? seccion : null,
			articuloField : articulo,
			referenciaField : referencia,
			introadd : introadd,
			infoField : info
		});

		control.on('itemselect', function(c, data) {
			if(data == null || (data.seccion == null && use_secciones))
				return;
			var reg = Ext.apply(data, {
				'nIdLibro' : parseInt(data.id),
				'nIdSeccion' : (use_secciones) ? parseInt(data.seccion.id) : null,
				'cSeccion' : (use_secciones) ? data.seccion.text : null,
				'fImporte' : parseFloat((exentoIVA) ? (data.fImporte - data.fIVAImporte) : data.fImporte),
				'fIVA' : parseFloat((exentoIVA) ? 0 : data.fIVA),
				'fRecargo' : (aplicar_recargo) ? parseFloat(data.fRecargo) : 0,
				'fIVAImporte' : parseFloat((exentoIVA) ? 0 : data.fIVAImporte),
				'fTotal' : parseFloat((exentoIVA) ? (data.fTotal - data.fIVAImporte) : data.fTotal),
				'fPVP' : parseFloat((exentoIVA) ? data.fBase : data.fPVP),
				'nIdOferta': data.nIdOferta,
				'bNoDto': data.bNoDto,
				'nIdDocumento' : last_documento
			});

			if(extrafields != null && resetextrafields) {
				Ext.each(extrafields, function(item) {
					if(data[item.dataIndex] != null)
						reg[item.dataIndex] = null;
				});
			}

			//console.log('itemselect');
			//console.dir(data);

			// Comprueba si hay un item igual, para aumentar la cantidad
			var hay = false;
			store.each(function(r2) {
				if((reg.nIdLibro == r2.data.nIdLibro) && ((parseFloat(reg.fPVP) == parseFloat(r2.data.fPVP) && !base) || (parseFloat(reg.fPrecio) == parseFloat(r2.data.fPrecio) && base)) && (reg.nIdSeccion == r2.data.nIdSeccion) && (reg.cReferencia == (r2.data.cReferencia == null ? '' : r2.data.cReferencia)) && (parseFloat(reg.fDescuento) == parseFloat(r2.data.fDescuento))) {
					var si = true;
					if(fn_check_ident != null) {
						si = fn_check_ident(reg, r2.data);
					}
					if(si) {
						r2.set('nCantidad', reg.nCantidad + r2.data.nCantidad);
						recalculate(r2);
						hay = true;
						return false;
					}
				}
			});
			if(!hay) {
				if(Ext.app.LINEASLASTFIRST)
					store.insert(0, new ComboRecord(reg));
				else
					store.add(new ComboRecord(reg));
			}
			control.focus();
		});
		var controles_linea = [control, {
			xtype : 'tbbutton',
			iconCls : "icon-bookadd",
			tooltip : _s('add-libro'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/alta'),
					params : {
						text : control.getValue(),
						cmpid : control.getId()
					}
				});
			}
		}, {
			xtype : 'displayfield',
			value : _s('Artículo')
		}, articulo];

		if(use_secciones)
			controles_linea = controles_linea.concat([{
				xtype : 'displayfield',
				value : _s('nIdSeccion')
			}, seccion]);
		controles_linea = controles_linea.concat([{
			xtype : 'displayfield',
			value : _s('Cant')
		}, cantidad, {
			xtype : 'displayfield',
			value : _s('Dto')
		}, descuento, {
			xtype : 'displayfield',
			value : _s( base ? 'fPrecio' : 'fPVP')
		}, importe, {
			xtype : 'displayfield',
			value : _s('Ref')
		}, referencia, info]);

		var linea = {
			xtype : 'compositefield',
			msgTarget : 'side',
			fieldLabel : _s('Línea'),
			anchor : '-20',
			items : controles_linea
		};

		var recalculate = function(r) {

			var valores = (base) ? ProcesarImportes(r.data.nCantidad, r.data.fDescuento, AplicarIVA(r.data.fPrecio, r.data.fIVA), r.data.fIVA, r.data.fRecargo) : ProcesarImportes(r.data.nCantidad, r.data.fDescuento, r.data.fPVP, r.data.fIVA, r.data.fRecargo);

			r.set('fImporte', valores.unitario);
			r.set('fBase', valores.base);
			r.set('fTotal', valores.total);
			r.set('fIVAImporte', valores.iva);
			r.set('fMargen', Margen(valores.base, r.data.nCantidad * r.data.fCoste));
		}
		var rendererDescuento = function(val, x, r, row, col) {
			if(r != null)
				x.css = 'cell-docs-descuento';

			return Ext.app.numberFormatter(val);
		}
		var rendererCantidad = function(val, x, r, row, col) {
			if(r != null)
				x.css = 'cell-docs-cantidad';

			return parseInt(val);
		}
		var rendererReferencia = function(val, x, r, row, col) {
			if(r != null)
				x.css = 'cell-docs-referencia';

			return val;
		}
		var renderers = {
			'rendererDescuento' : rendererDescuento,
			'rendererCantidad' : rendererCantidad,
			'rendererReferencia' : rendererReferencia
		}

		var itemDeleter = new Extensive.grid.ItemDeleter();

		var cantidadEditor = new Ext.form.NumberField({
			allowBlank : false,
			allowNegative : true,
			allowDecimals : false,
			selectOnFocus : true
		});

		var PVPEditor = new Ext.form.NumberField({
			allowBlank : false,
			allowNegative : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true
		});
		var descuentoEditor = new Ext.form.NumberField({
			allowBlank : false,
			allowNegative : false,
			allowDecimals : true,
			minValue : 0,
			maxValue : 100,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:center',
			selectOnFocus : true
		});

		var editores = {
			'cantidadEditor' : cantidadEditor,
			'descuentoEditor' : descuentoEditor,
			'PVPEditor' : PVPEditor,
			'cantidadEditor' : cantidadEditor
		}

		var cellTips = new Ext.ux.CellToolTips({
			tipConfig : {
				anchor : 'left'
			},
			ajaxTips : [{
				field : 'nIdLibro',
				tpl : '<b>{cTitulo}</b><br />' + Ext.app.getPortada(75, 'nIdLibro')
			}]
		});

		var columns = [itemDeleter, {
			header : _s('Id'),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdLinea',
			hidden : in_array('nIdLinea', hide),
			sortable : true
		}, {
			header : _s('nIdDocumento'),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdDocumento',
			hidden : in_array('nIdDocumento', hide),
			sortable : true
		}, {
			header : _s('nIdLibro'),
			dataIndex : 'nIdLibro',
			width : Ext.app.TAM_COLUMN_ID,
			hidden : in_array('nIdLibro', hide),
			sortable : true
		}, {
			header : _s('nCantidad'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			hidden : in_array('nCantidad', hide),
			dataIndex : 'nCantidad',
			renderer : rendererCantidad,
			editor : cantidadEditor,
			sortable : true
		}, {
			header : _s('nEnFirme'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'nEnFirme',
			hidden : in_array('nEnFirme', hide) || !firmedeposito,
			hideable : firmedeposito,
			sortable : true
		}, {
			header : _s('nEnDeposito'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'nEnDeposito',
			hidden : in_array('nEnDeposito', hide) || !firmedeposito,
			hideable : firmedeposito,
			sortable : true
		}, {
			header : _s('cTitulo'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'cTitulo',
			hidden : in_array('cTitulo', hide),
			id : 'descripcion',
			sortable : true
		}, {
			align : 'right',
			header : _s( base ? 'fPrecio' : 'fPVP'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			hidden : in_array( base ? 'fPrecio' : 'fPVP', hide),
			dataIndex : base ? 'fPrecio' : 'fPVP',
			sortable : true,
			editor : PVPEditor,
			renderer : Ext.app.rendererPVP

		}, {
			header : _s('fDescuento'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			hidden : in_array('fDescuento', hide),
			editor : descuentoEditor,
			renderer : rendererDescuento,
			dataIndex : 'fDescuento',
			align : 'center',
			sortable : true
		}, {
			header : _s('fImporte'),
			hidden : in_array('fImporte', hide),
			align : 'right',
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fImporte',
			renderer : Ext.app.numberFormatter,
			sortable : true
		}, {
			header : _s('fBase'),
			hidden : in_array('fBase', hide),
			align : 'right',
			renderer : Ext.app.numberFormatter,
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fBase',
			sortable : true
		}, {
			header : _s('fIVA'),
			hidden : in_array('fIVA', hide),
			align : 'center',
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fIVA',
			sortable : true
		}, {
			header : _s('fIVAImporte'),
			hidden : in_array('fIVAImporte', hide),
			align : 'right',
			renderer : Ext.app.numberFormatter,
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fIVAImporte',
			sortable : true
		}, {
			header : _s('fRecargo'),
			hidden : in_array('fRecargo', hide),
			align : 'center',
			width : Ext.app.TAM_COLUMN_NUMBER,
			renderer : Ext.app.numberFormatter,
			dataIndex : 'fRecargo',
			sortable : true
		}, {
			header : _s('fRecargoImporte'),
			hidden : in_array('fRecargoImporte', hide),
			align : 'right',
			renderer : Ext.app.numberFormatter,
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fRecargoImporte',
			sortable : true
		}, {
			header : _s('fTotal'),
			hidden : in_array('fTotal', hide),
			align : 'right',
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fTotal',
			renderer : Ext.app.numberFormatter,
			sortable : true
		}, {
			header : _s('cSeccion'),
			hidden : !use_secciones || in_array('cSeccion', hide),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'cSeccion',
			hideable : use_secciones,
			sortable : true
		}, {
			header : _s('fCoste'),
			hidden : in_array('fCoste', hide) || !coste,
			align : 'right',
			renderer : Ext.app.numberFormatter,
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fCoste',
			hideable : coste,
			sortable : true
		}, {
			header : _s('fMargen'),
			hidden : in_array('fMargen', hide) || !coste,
			align : 'right',
			renderer : Ext.app.numberFormatter,
			width : Ext.app.TAM_COLUMN_NUMBER,
			dataIndex : 'fMargen',
			hideable : coste,
			sortable : true
		}, {
			header : _s('cReferencia'),
			hidden : in_array('cReferencia', hide),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'cReferencia',
			editor : new Ext.form.TextField({
				allowBlank : true
			}),
			renderer : rendererReferencia,
			sortable : true
		}, {
			header : _s('cRefInterna'),
			hidden : in_array('cRefInterna', hide),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'cRefInterna',
			editor : new Ext.form.TextField({
				allowBlank : true
			}),
			renderer : rendererReferencia,
			sortable : true
		}, {
			header : _s('cCUser'),
			hidden : in_array('cCUser', hide),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'cCUser',
			sortable : true
		}, {
			header : _s('dCreacion'),
			hidden : in_array('dCreacion', hide),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDate,
			dataIndex : 'dCreacion',
			sortable : true
		}, {
			header : _s('cAUser'),
			hidden : in_array('cAUser', hide),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'cAUser',
			sortable : true
		}, {
			header : _s('dAct'),
			hidden : in_array('dAct', hide),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDate,
			dataIndex : 'dAct',
			sortable : true
		}];

		if(extrafields != null) {
			Ext.each(extrafields, function(item) {
				if(item.renderer != null && is_string(item.renderer)) {
					//console.log('sip');
					item.renderer = renderers[item.renderer];
				}
				/*
				 * if (item.editor != null) item.editor =
				 * editores[item.editors];
				 */
				columns[columns.length] = item;
			});
		}

		var grid = new Ext.grid.EditorGridPanel({
			region : 'center',
			autoExpandColumn : "descripcion",
			loadMask : true,
			stripeRows : true,
			store : store,
			anchor : anchor,
			columns : columns,
			sm : itemDeleter,
			listeners : {
				afteredit : function(e) {
					// console.dir(e);
					if(enable_control) {
						if(e.originalValue != e.value) {
							recalculate(e.record);
						}
					} else {
						// console.log('NOL');
						e.cancelEdit();
						e.reject();
					}
				}
			},
			plugins : [cellTips],
			viewConfig : {
				enableRowBody : true,
				getRowClass : function(r, rowIndex, rowParams, store) {
					if(getRowClass != null) {
						var a = getRowClass(r, rowIndex, rowParams, store);
						if(a != null)
							return a;
					}
					if(r.data.fPrecio == 0 || r.data.fPVP == 0)
						return 'cell-doc-precio0';
					if(margen_error)
						return ((r.data.fMargen < Ext.app.MARGEN_MINIMO) && coste) ? 'cell-doc-margenminimo' : '';
					return '';
				}
			}
		});

		Ext.app.addDeleteEvent(grid);
		var item_select = null;
		var fn_check_menu = null;
		var setrecord = {
			/**
			 * Selecciona un item del menú
			 *
			 * @param {Object}
			 *            item
			 */
			setItemSelect : function(item) {
				item_select = item;
				if(fn_check_menu != null)
					fn_check_menu(item);
			},
			/**
			 * Devuelve el item seleccionado
			 */
			getItemSelect : function() {
				return item_select;
			}
		}

		var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', setrecord);
		return {
			control : control,
			linea : linea,
			grid : grid,
			info : function(text) {
				this.control.info(text);
			},
			/**
			 * Limpia los datos
			 */
			clear : function() {
				last_documento = null;
				lineas_delete = new Array();
				store.removeAll();
			},
			/**
			 * Habilita el control
			 */
			enable : function() {
				// grid.enable();
				control.enable();
				itemDeleter.enable();
				cantidadEditor.enable();
				PVPEditor.enable();
				descuentoEditor.enable();
				enable_control = true;
			},
			/**
			 * Deshabilita el control
			 */
			disable : function() {
				// grid.disable();
				control.disable();
				itemDeleter.disable();
				cantidadEditor.disable();
				PVPEditor.disable();
				descuentoEditor.disable();
				enable_control = false;
			},
			/**
			 * Añadir un registro
			 *
			 * @param {Object}
			 *            r
			 */
			add : function(r) {
				//console.dir(r);
				var recargo = aplicar_recargo ? r.fRecargo : 0;
				//console.log('EXENTO IVA ' + exentoIVA);
				r.fIVA = parseFloat(r.fIVA);
				r.fDescuento = parseFloat(r.fDescuento);
				r.fPrecio = parseFloat(r.fPrecio);
				var valores = ProcesarImportes(r.nCantidad, r.fDescuento, r.fPVP, r.fIVA, recargo);
				var coste = (r.nCantidad < 0) ? -r.fCoste : r.fCoste;

				// Comprueba si hay un item igual, para aumentar la cantidad
				var hay = false;
				store.each(function(r2) {
					if((r.nIdLibro == r2.nIdLibro) && (r.fPVP == r2.fPVP) && (r.nIdSeccion == r2.nIdSeccion) && (r.cReferencia == r2.cReferencia)) {
						var si = true;
						// Si hay fields extras, tienen que ser iguales...
						if(extrafields != null) {
							Ext.each(extrafields, function(item) {
								if(r[item.dataIndex] != r2[item.dataIndex]) {
									si = false;
									return false;
								}
							});
						}
						if(si) {
							if(fn_check_ident != null) {
								si = fn_check_ident(r, r2);
							}
							if(si) {
								r.set('nCantidad', r.nCantidad + r2.nCantidad);
								hay = true;
								return false;
							}
						}
					}
				});
				if(!hay) {
					var field = {
						'nIdLibro' : r.nIdLibro,
						'nCantidad' : r.nCantidad,
						'cTitulo' : r.cTitulo,
						'fDescuento' : r.fDescuento,
						'fPrecio' : r.fPrecio,
						'fIVA' :  parseFloat(exentoIVA) ? 0 : r.fIVA,
						'fPVP' :  parseFloat(exentoIVA) ? valores.base : r.fPVP,
						'fImporte' : valores.unitario,
						'cReferencia' : r.cReferencia,
						'cRefInterna' : r.cRefInterna,
						'fIVAImporte' :  parseFloat(exentoIVA) ? 0 : valores.iva,
						'fBase' : valores.base,
						'fCoste' : (coste==null)?0:coste,
						'fRecargo' : recargo,
						'fRecargoImporte' : valores.recargo,
						'fMargen' : Margen(valores.base, r.nCantidad * r.fCoste),
						'nIdSeccion' : r.nIdSeccion,
						'cSeccion' : r.cSeccion,
						'fTotal' : valores.total
					}
					// Si hay extrafields, los añade
					if(extrafields != null) {
						Ext.each(extrafields, function(item) {
							field[item.dataIndex] = r[item.dataIndex];
						});
					}

					store.add(new ComboRecord(field));
				}
			},
			/**
			 * Carga el grid de líneas de documentos
			 *
			 * @param {Object}
			 *            data
			 */
			load : function(data, nuevo) {
				// console.log('Cargando líneas');
				try {
					store.suspendEvents(false);
					Ext.each(data, function(r) {
						//console.dir(r);
						if (r.fCoste == null) r.fCoste = 0;
						//console.log(r.fCoste, parseFloat(r.fCoste));
						var reg = {
							'nIdLibro' : parseInt(r.nIdLibro),
							'nIdSeccion' : parseInt(r.nIdSeccion),
							'cSeccion' : r.cSeccion,
							'cTitulo' : r.cTitulo,
							'fDescuento' : parseFloat(r.fDescuento),
							'fPrecio' : parseFloat(r.fPrecio),
							'fImporte' : parseFloat(r.fImporte),
							'nCantidad' : parseInt(r.nCantidad),
							'nEnFirme' : parseInt(r.nEnFirme),
							'nEnDeposito' : parseInt(r.nEnDeposito),
							'fIVA' : parseFloat(r.fIVA),
							'fRecargo' : parseFloat(r.fRecargo),
							'fBase' : parseFloat(r.fBase),
							'fCoste' : r.fCoste,
							'fMargen' : Margen(r.fBase, r.nCantidad * r.fCoste),
							'fIVAImporte' : parseFloat(r.fIVAImporte),
							'fRecargoImporte' : parseFloat(r.fRecargoImporte),
							'fRecargo' : parseFloat(r.fRecargo),
							'fTotal' : parseFloat(r.fTotal),
							'fPVP' : parseFloat(r.fPVP),
							'nIdLinea' : parseInt(r[nIdLinea]),
							'nIdDocumento' : parseInt(r[nIdDocumento]),
							'cReferencia' : r[cReferencia],
							'cRefInterna' : r.cRefInterna,
							'cCUser' : r.cCUser,
							'cAUser' : r.cAUser,
							'dCreacion' : r.dCreacion,
							'nIdOferta': r.nIdOferta,
							'bNoDto': r.bNoDto,
							'dAct' : r.dAct,
							'bNuevo' : nuevo
						}

						if(extrafields != null) {
							Ext.each(extrafields, function(item) {
								reg[item.dataIndex] = r[item.dataIndex];
							});
						}
						store.add(new ComboRecord(reg));
						if((r[nIdDocumento] != null) && (r[nIdDocumento] != ''))
							last_documento = r[nIdDocumento];
					});
					store.resumeEvents();
				} catch (e) {
					store.resumeEvents();
					// console.dir(e);
				}
				grid.getView().refresh();
				fn_change(store);
			},
			/**
			 * Devuelve el menú contextual
			 */
			getContextMenu : function() {
				return contextmenu;
			},
			/**
			 * Añade un menú contextual
			 *
			 * @param {Object}
			 *            menu
			 */
			addMenu : function(menu) {
				return contextmenu.add(menu);
			},
			/**
			 * Devuelve el listado de elementos del listado
			 *
			 * @param {Object}
			 *            data
			 */
			get : function(data) {
				var index = 0;
				var me = this;
				grid.getStore().each(function(r) {
					var dirty = false;
					if(extrafields != null) {
						Ext.each(extrafields, function(item) {
							/*console.log('Is modified ' + item.dataIndex + ' '
							 + r.isModified(item.dataIndex));*/
							dirty = dirty || r.isModified(item.dataIndex);
						});
					}
					//console.log('dirty ' + dirty);
					//console.dir(r.data);
					//console.log(r.isModified('cRefInterna'));
					var fPrecio = (base) ? parseFloat(r.data.fPrecio) : QuitarIVA(r.data.fPVP, r.data.fIVA);
					//console.log(base + ' Precio ' + fPrecio);
					if(dirty || r.isModified('nCantidad') || r.isModified('fBase') || r.isModified('fRecargo') || r.isModified('fIVA') || r.isModified('cReferencia') || r.isModified('cRefInterna') || (r.data.nIdLinea == null) || (r.data.bNuevo === true)) {
						data['lineas[' + index + '][' + nIdLinea + ']'] = r.data.nIdLinea;
						if (r.isModified('nIdDocumento') || r.data.nIdLinea==null)
							data['lineas[' + index + '][' + nIdDocumento + ']'] = r.data.nIdDocumento;
						data['lineas[' + index + '][id]'] = r.data.nIdLinea;
						if (r.isModified('cReferencia') || r.data.nIdLinea==null)
							data['lineas[' + index + '][' + cReferencia + ']'] = r.data.cReferencia;
						if (r.isModified('cRefInterna') || r.data.nIdLinea==null)
							data['lineas[' + index + '][cRefInterna]'] = r.data.cRefInterna;
						if (r.isModified('nIdLibro') || r.data.nIdLinea==null)
							data['lineas[' + index + '][nIdLibro]'] = r.data.nIdLibro;
						if (r.isModified('nCantidad') || r.data.nIdLinea==null)
							data['lineas[' + index + '][nCantidad]'] = r.data.nCantidad;
						if (r.isModified('fDescuento') || r.data.nIdLinea==null)
							data['lineas[' + index + '][fDescuento]'] = r.data.fDescuento;
						if (r.isModified('fPrecio') || r.isModified('fPVP') || r.data.nIdLinea==null)
							data['lineas[' + index + '][fPrecio]'] = fPrecio;
						if (r.isModified('fIVA') || r.data.nIdLinea==null)
							data['lineas[' + index + '][fIVA]'] = r.data.fIVA;
						if (r.isModified('fRecargo') || r.data.nIdLinea==null)
							data['lineas[' + index + '][fRecargo]'] = r.data.fRecargo;
						if (r.isModified('fCoste') || r.data.nIdLinea==null)
							data['lineas[' + index + '][fCoste]'] = r.data.fCoste;
						if (r.isModified('nIdSeccion') || r.data.nIdLinea==null)
							data['lineas[' + index + '][nIdSeccion]'] = r.data.nIdSeccion;
						if (r.data.dCreacion != null && use_creation)
							data['lineas[' + index + '][dCreacion]'] = r.data.dCreacion;
						if (extrafields != null) {
							Ext.each(extrafields, function(item) {
								if (r.data[item.dataIndex] != null && (r.isModified(item.dataIndex) || r.data.nIdLinea==null))
									data['lineas[' + index + '][' + item.dataIndex + ']'] = r.data[item.dataIndex];
							});
						}
					}
					index++;
				});
				Ext.each(lineas_delete, function(i) {
					data['lineas[' + index + '][delete]'] = i;
					index++;
				});
				//console.dir(data);
				return data;
			},
			/**
			 * Aplica un descuento a todo el documento
			 *
			 * @param {Object}
			 *            dto
			 */
			descuento : function(dto) {
				store.suspendEvents(false);
				if (dto < 0) dto = 0;
				if (dto > 100) dto = 100;
				grid.getStore().each(function(r) {
					if (r.data.bNoDto != true && r.data.nIdOferta == null)
					{
						r.set('fDescuento', dto);
						recalculate(r);
					}
				});
				store.resumeEvents();
				grid.getView().refresh();
				fn_change(store);
			},
			/**
			 * Cambia las cantidades a negativo
			 */
			aplicarNegativo : function() {
				store.suspendEvents(false);
				store.each(function(r) {
					r.set('nCantidad', -r.data.nCantidad);
					recalculate(r);
				});
				store.resumeEvents();
				grid.getView().refresh();
				fn_change(store);
			},
			/**
			 * Ajusta el margen para todo el documento
			 *
			 * @param {Object}
			 *            margen
			 * @param {Object}
			 *            acinco
			 */
			aplicarMargen : function(margen, acinco) {
				store.suspendEvents(false);
				store.each(function(r) {
					if(r.data.fMargen <= margen) {
						var dto = DescuentoMaximo(QuitarIVA(r.data.fPVP, r.data.fIVA), r.data.fCoste, margen, acinco);
						r.set('fDescuento', dto);
						recalculate(r);
					}
				});
				store.resumeEvents();
				grid.getView().refresh();
				fn_change(store);
			},
			/**
			 * Aplica un precio por defecto a todo el documento que tiene precio 0
			 *
			 * @param {Object}
			 *            precio
			 */
			aplicarPrecio0 : function(precio) {
				store.suspendEvents(false);
				store.each(function(r) {
					if(r.data.fPVP <= 0) {
						r.set('fPVP', precio);
						recalculate(r);
					}
				});
				store.resumeEvents();
				grid.getView().refresh();
				fn_change(store);
			},
			/**
			 * Aplica recargo de equivalencia, Si/No
			 *
			 * @param {Object}
			 *            aplicar
			 */
			aplicarRecargo : function(aplicar) {
				aplicar_recargo = aplicar;
			},
			/**
			 * Exento de IVA si/no
			 *
			 * @param {Object}
			 *            exento
			 */
			exentoIVA : function(exento) {
				exentoIVA = ((exento === true) || (parseInt(exento) == 1))
				if(exento) {
					store.suspendEvents(false);
					grid.getStore().each(function(r) {
						r.set('fPVP', QuitarIVA(r.data.fPVP, r.data.fIVA));
						r.set('fIVA', 0);
						r.set('fIVAImporte', 0);
						recalculate(r);
					});
					store.resumeEvents();
					grid.getView().refresh();
					fn_change(store);
				} else {
					//console.log("Hay que leer el IVA de todos los productos");
				}
			},
			/**
			 * Asigna las tarifas para el cálculo de precios
			 *
			 * @param {Object}
			 *            general
			 * @param {Object}
			 *            tarifas
			 */
			setTarifas : function(general, tarifas) {
				try {
					/*
					 * console.log('Lineas, asignando tarifas');
					 * console.log('General: ' + general);
					 * console.log('Indiviual: '); console.dir(tarifas);
					 */
					control.setTarifas(general, tarifas);
					tarifas_general = general;
					tarifas = tarifas;
				} catch (e) {
					console.dir(e);
				}
			},
			/**
			 * Selecciona un item del menú
			 *
			 * @param {Object}
			 *            item
			 */
			setItemSelect : function(item) {
				// console.log('Añadiendo item ' + item);
				item_select = item;
			},
			/**
			 * Devuelve el item seleccionado
			 */
			getItemSelect : function() {
				// console.log('Leyendo item ' + item_select);
				return item_select;
			},
			/**
			 * Asigna controlador el estado del menú
			 *
			 * @param {Object}
			 *            fn
			 */
			setCheckMenu : function(fn) {
				fn_check_menu = fn;
			}
		}
	} catch (e) {
		console.dir(e);
	}
}
/**
 * Estructura para los menues contextuales
 */
var fn_contextmenu = function() {
	return {
		cm : null,
		item_select : null,
		fn_check : null,
		/**
		 * Selecciona un item del menú
		 *
		 * @param {Object}
		 *            item
		 */
		setItemSelect : function(item) {
			this.item_select = item;
			if(this.fn_check != null)
				this.fn_check(item);
		},
		/**
		 * Devuelve el item seleccionado
		 */
		getItemSelect : function() {
			return this.item_select;
		},
		/**
		 * Asigna controlador el estado del menú
		 *
		 * @param {Object}
		 *            fn
		 */
		setContextMenu : function(cm) {
			this.cm = cm;
		},
		getContextMenu : function() {
			return this.cm;
		},
		setCheckMenu : function(fn) {
			this.fn_check = fn;
		}
	}
}
var fn_docs_select_lineas_devolucion = function(id, fn) {
	var model = [{
		name : 'nIdLibro'
	}, {
		name : 'id'
	}, {
		name : 'nCantidad'
	}, {
		name : 'nIdSeccion'
	}, {
		name : 'nIdLinea'
	}, {
		name : 'cAutores'
	}, {
		name : 'cTitulo'
	}, {
		name : 'cSeccion'
	}, {
		name : 'fDescuento'
	}, {
		name : 'fPVP'
	}, {
		name : 'fPrecio'
	}, {
		name : 'fBase'
	}, {
		name : 'fCoste'
	}, {
		name : 'fIVA'
	}];

	var url = site_url("compras/devolucionlinea/rechazables");
	var store = Ext.app.createStore({
		model : model,
		url : url
	});

	var sm = new Ext.grid.CheckboxSelectionModel();

	var columns = [sm, {
		header : _s("Id"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'nIdLibro',
		sortable : true
	}, {
		header : _s("nCantidad"),
		dataIndex : 'nCantidad',
		width : Ext.app.TAM_COLUMN_NUMBER,
		editor : new Ext.form.NumberField({
			allowBlank : false,
			allowNegative : true,
			allowDecimals : false,
			selectOnFocus : true
		}),
		sortable : true
	}, {
		header : _s("cAutores"),
		dataIndex : 'cAutores',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		id : 'descripcion',
		header : _s("cTitulo"),
		dataIndex : 'cTitulo',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		header : _s("fDescuento"),
		dataIndex : 'fDescuento',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}, {
		header : _s("fPVP"),
		dataIndex : 'fPVP',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}, {
		header : _s("cSeccion"),
		dataIndex : 'cSeccion',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}];

	var grid = new Ext.grid.EditorGridPanel({
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

	var motivo = new Ext.form.TextField({
		allowBlank : false,
		selectOnFocus : true,
		anchor : '100%',
		fieldLabel : _s('Motivo')
	})
	var controls = [grid, motivo];

	var form = Ext.app.formStandarForm({
		controls : controls,
		autosize : false,
		height : 500,
		icon : 'icon-rechazar',
		width : 700,
		title : _s('Rechazar'),
		fn_ok : function() {
			var sel = grid.getSelectionModel().getSelections();
			var ids = [];
			Ext.each(sel, function(item) {
				ids.push(item.data);
			});
			if(fn != null)
				fn(ids, motivo.getValue());
		}
	});

	store.baseParams = {
		id : id
	};

	store.load();
	form.show();
}
var rendererEstadoLibro = function(val, x, r, row, col) {
	if(in_array(r.data.nIdEstadoLibro, [6, 5, 11, 4, 7, 8, 13, 14, 12, 15, 9])) {
		console.dir(row);
		val = '<span style="background-color: red; color: white;">' + val + '</span>';
	}

	return val;
}

var sendCourier = function(url, form, importe, fn) {
	var form_id = Ext.app.createId();

	var controls = [{		
		xtype: 'checkbox',
		fieldLabel: _s('Reembolso'),
		id: form_id + 'rem'
	}, {
		xtype: 'textfield',
		value: importe,
		selectOnFocus: true,
		fieldLabel: _s('Importe'),
		id: form_id + 'importe'		
	}, {
        fieldLabel: _s('Dia'),
        value: new Date(),
        startDay: Ext.app.DATESTARTDAY,
		id: form_id + 'dia',
        xtype: "datefield"
	}, new Ext.ux.form.Spinner({
        fieldLabel: _s('Bultos'),
        id: form_id + "bultos",
        width: 60,
        value: 1,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    }), {
		xtype: 'textfield',
		selectOnFocus: true,
		fieldLabel: _s('Observaciones'),
		id: form_id + 'obs'
    }];

 	var form2 = Ext.app.formStandarForm({
		controls : controls,
		icon : 'iconoCourierTab',
		title : _s('Enviar por courier'),
		fn_ok : function() {
			var r = Ext.getCmp(form_id + 'rem').checked;
			var v = Ext.getCmp(form_id + 'importe').getValue();
			var b = Ext.getCmp(form_id + 'bultos').getValue();
			var obs = Ext.getCmp(form_id + 'obs').getValue();
			var d = Ext.getCmp(form_id + 'dia').getValue();
           	Ext.app.callRemote({
                url: url,
                params: {
                    id: form.getId(),
                    reembolso: r,
                    importe: v,
                    dia: DateToNumber(d.getTime()),
                    bultos: b,
                    obs: obs
                },
                fnok: function() {
                	if (form)
                    	form.refresh();
                    if (fn)
                    	fn();
                }
            });
		}
	});

	form2.show();

}
