(function() {
	try {
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";
		if(title === '')
			title = _s('Articulo');
		if(icon === '')
			icon = 'iconoArticulosTab';

		var list_grids = [form_id + 'btn_promocionar', 
			form_id + 'btn_avisar', 
			form_id + 'btn_ventas', 
			form_id + 'btn_eliminar_portada', 
			form_id + 'btn_portada', 
			form_id + 'btn_portada3', 
			form_id + 'btn_portada4', 
			form_id + 'btn_portada2', 
			form_id + 'btn_analisis', 
			form_id + 'btn_documents', 
			form_id + 'btn_devoluciones', 
			form_id + 'btn_devoluciones2', 
			form_id + 'btn_antiguedad', 
			form_id + 'btn_stockcontado', 
			form_id + 'btn_comprobarprecios', 
			form_id + 'btn_analisisventas', 
			form_id + 'btn_precios', 
			form_id + 'btn_suscripciones', 
			form_id + '_libros_grid', 
			form_id + '_media_grid', 
			form_id + 'btn_compras', 
			form_id + 'btn_suscripciones2'];

		var iva = null;
		var notas = Ext.app.formNotas();
		
		var data_load = null;

		// Carga
		var fn_load = function(id, res) {
			try {
				notas.load(id);
				data_load = res;
				if(res.bMostrarWebManual === null) {
					mostrarwebmanual.setValue(-1);
				}

				var p = Ext.getCmp(proveedor.id);
				var p2 = Ext.getCmp(proveedormanual.id);
				var c = Ext.getCmp(coleccion.id);
				var e = Ext.getCmp(editorial.id);
				var o = Ext.getCmp(oferta.id);
				if(res.editorial !== null)
					p.setValue(res.editorial.nIdProveedor);
				p2.setValue(res.nIdProveedorManual);
				e.setValue(res.nIdEditorial);
				c.store.baseParams = {
					where : 'nIdEditorial=' + (res.nIdEditotial !== null) ? res.nIdEditotial : -1,
					start : 0,
					limit : Ext.app.AUTOCOMPLETELISTSIZE
				};
				c.setValue(res.nIdColeccion);
				o.setValue(res.nIdOferta);
				if (Ext.app.IsRevista(res.nIdTipo))  
					form.showTab(2); 
				else 
					form.hideTab(2);
				iva = res.fIVA;
				var panel = Ext.getCmp(form_id + "details-panel");
				panel.setSrc(res.info);
				if(res.sinopsis.tSinopsis !== null) {
					sinopsis.setValue(res.sinopsis.tSinopsis);
				}
				Ext.app.formLoadList({
					list : list_grids,
					params : {
						where : 'nIdLibro=' + parseInt(id),
						id : parseInt(id)
					}
				});
			} catch (ex) {
				console.dir(ex);
			}
		};
		
		var fn_save = function(id, data) {

			var p = Ext.getCmp(proveedor.id);
			var c = Ext.getCmp(coleccion.id);
			var e = Ext.getCmp(editorial.id);
			var o = Ext.getCmp(oferta.id);
			var p2 = Ext.getCmp(proveedormanual.id);
			if(data['nIdProveedor_'] !== null)
				data['nIdProveedor'] = data['nIdProveedor_'];
			if(p !== null && p.isDirty())
				data['nIdProveedor'] = p.getValue();
			if(p2 !== null) {
				if(p2.getValue() === null || p2.getValue() === '')
					data['nIdProveedorManual'] = null;
				else {
					data['nIdProveedor'] = p2.getValue();
					data['nIdProveedorManual'] = p2.getValue();
				}
			}
			if(e !== null && e.isDirty()) {
				data['nIdEditorial'] = e.getValue();
			}
			if(c !== null && c.isDirty())
				data['nIdColeccion'] = c.getValue();
			if(o !== null && o.isDirty())
				data['nIdOferta'] = o.getValue();

			if(sinopsis.isDirty()) {
				data["sinopsis[tSinopsis]"] = sinopsis.getValue();
				data['tSinopsis'] = null;
			}
			if(tipo.isDirty()) {
				data["nIdTipo"] = tipo.getValue();
			}
			return data;
		}
		// Borrado
		var fn_reset = function() {
			notas.reset();
			iva = 0;
			form.hideTab(2);
			Ext.app.formResetList({
				list : list_grids,
				params : {
					where : 'nIdLibro=-1',
					id : -1
				}
			});

			var s = Ext.getCmp(coleccion.id);
			s.store.baseParams = {
				where : 'nIdEditorial=-1',
				start : 0,
				limit : Ext.app.AUTOCOMPLETELISTSIZE
			}
			s.reset();

			var panel = Ext.getCmp(form_id + "details-panel");
			panel.setSrc('about:blank');
			try {
				var id = parseInt("<?php echo $this->config->item('bp.catalogo.idtipoarticulodefault');?>");
				if(id != 0) {
					tipo.setValue(parseInt(id));
				}
			} catch (e) {
				console.dir(e);
			}
			form.setData({
                value_data: {
                    'bPrecioLibre': false,
                    'bNoDto': false
                }
            }, true);

		};
		var fn_enable_disable = function(form) {
			notas.enable(form.getId() > 0);
			Ext.app.formEnableList({
				list : list_grids,
				enable : (form.getId() > 0)
			});
		};
		// Formulario
		var form = Ext.app.formGeneric();
		form.init({
			id : form_id,
			title : title,
			icon : icon,
			url : site_url('catalogo/articulo'),
			fn_load : fn_load,
			fn_save : fn_save,
			fn_reset : fn_reset,
			fn_enable_disable : fn_enable_disable
		});

		// Controles normales
		var tipo = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('catalogo/tipolibro/search'),
			name : 'nIdTipo',
			allowBlank : true
		}));
		tipo.on('select', function(c, v) {
			console.dir(v);
			console.log('select');
			(Ext.app.IsRevista(parseInt(c.getValue()))) ? form.showTab(2) : form.hideTab(2);
		});
		var estado = Ext.app.combobox({
			//label: _s('nIdEstado'),
			url : site_url('catalogo/estadolibro/search'),
			id : 'nIdEstado',
			allowBlank : true
		});

		var idioma = Ext.app.combobox({
			url : site_url('generico/idioma/search'),
			id : 'nIdIdioma',
			allowBlank : true
		});

		var encuadernacion = Ext.app.combobox({
			url : site_url('catalogo/encuadernacion/search'),
			id : 'nIdEncuadernacion',
			allowBlank : true
		});

		var oferta = Ext.app.autocomplete2({
			url : site_url('catalogo/oferta/search'),
			name : 'nIdOferta_',
			create : true,
			width : 400,
			allowBlank : true
		});

		var plazoenvio = Ext.app.combobox({
			url : site_url('compras/plazoenvio/search'),
			id : 'nIdPlazoEnvio',
			allowBlank : true,
            disabled: true,
            readOnly: true,
		});

		var plazoenviomanual = Ext.app.combobox({
			url : site_url('compras/plazoenvio/search'),
			id : 'nIdPlazoEnvioManual',
			allowBlank : true
		});

		var coleccion = /*new Ext.form.ComboBox*/(Ext.app.autocomplete2({
			url : site_url('catalogo/coleccion/search'),
			//fieldLabel: _s('nIdColeccion'),
			//autoload: true,
			width : 700,
			name : 'nIdColeccion_',
			create : true,
			//id: form_id + '_col',
			allowBlank : true
		}));

		var proveedor = /*new Ext.ux.form.SuperBoxSelect*/(Ext.app.autocomplete2({
			url : site_url('proveedores/proveedor/search'),
			name : 'nIdProveedor_',
			anchor : '100%',
			create : true,
			disabled : true,
			fieldLabel : _s('Prov. Ed.')
		}));

		var proveedormanual = /*new Ext.ux.form.SuperBoxSelect*/(Ext.app.autocomplete2({
			url : site_url('proveedores/proveedor/search'),
			name : 'nIdProveedorManual_',
			anchor : '100%',
			create : true,
			fieldLabel : _s('Proveedor')
		}));

		var editorial = /*new Ext.form.ComboBox*/(Ext.app.autocomplete2({
			url : site_url('catalogo/editorial/search'),
			//id: form_id + '_ed',
			name : 'nIdEditorial_',
			create : true,
			anchor : '100%',
			fieldLabel : _s('Editorial')
		}));

		var fn_add = function(controls) {
			var c = {
				xtype : 'hidden',
				id : 'nIdLibro',
				value : form.getId()
			}
			controls[controls.length] = c;
			return controls;
		}
		var ean = new Ext.form.NumberField({
			name : 'nEAN',
			allowNegative : false,
			allowBlank : true,
			allowDecimals : false
		});

		var isbn10 = new Ext.form.TextField({
			name : 'cISBN10',
			readOnly : true
		});

		var isbn = new Ext.form.TextField({
			xtype : 'textfield',
			name : 'cISBN',
			allowBlank : true
		});

		var precio = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fPrecio',
			width : 50,
			value : 0,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true,
			listeners : {
				change : function(me, e) {
					pvp.setValue(AplicarIVA(me.getValue(), iva));
				}
			}
		});

		var pvp = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fPVP',
			value : 0,
			width : 50,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true,
			listeners : {
				change : function(me, e) {
					precio.setValue(QuitarIVA(me.getValue(), iva));
				}
			}
		});

		/*var mostrarweb = new Ext.ux.IconCombo({
			store : new Ext.data.SimpleStore({
				fields : ['id', 'text', 'icon'],
				data : [[0, _s('mostrarweb-nosemuestra'), 'icon-mostrar-no'], [1, _s('mostrarweb-semuestra'), 'icon-mostrar-si']]
			}),
			//disabled: true,
			valueField : 'id',
			displayField : 'text',
			iconClsField : 'icon',
			readOnly : true,
			//triggerAction: 'all',
			mode : 'local',
			name : 'bMostrarWeb',
			fieldLabel : _s('Defecto'),
			width : 300
		});*/

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
			name : 'bMostrarWebManual',
			fieldLabel : _s('Manual'),
			width : 300
		});

		var titulo = new Ext.form.TextField({
			name : 'cTitulo',
			width : 700,
			allowBlank : false,
			selectOnFocus : true/*,
			 fieldLabel: _s('cTitulo')*/
		});

		var fn_limpiar_titulo = function() {
			var t = titulo.getValue().toLowerCase();
			t = ucfirst(t);
			titulo.setValue(t);
		}
		var controls = [{
			xtype : 'fieldset',
			items : [{
				xtype : 'compositefield',
				fieldLabel : _s('cISBN'),
				items : [{
					xtype : 'isbnfield',
					isbn_id : isbn.id,
					ean_id : ean.id,
					isbn10_id : isbn10.id,
					edit_id : editorial.id,
					prv_id : proveedor.id,
					next_id : titulo.id,
					form : form
				}, isbn, {
					xtype : 'displayfield',
					value : _s('EAN')
				}, ean, {
					xtype : 'displayfield',
					value : _s('ISBN-10')
				}, isbn10, {
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
					handler : fn_limpiar_titulo
				}]
			}, {
				xtype : 'compositefield',
				fieldLabel : _s('cEdicion'),
				items : [{
					xtype : 'textfield',
					width : 30,
					id : 'cEdicion',
					selectOnFocus : true,
					allowBlank : true
				}, {
					xtype : 'displayfield',
					value : _s('dEdicion')
				}, {
					xtype : 'datefield',
					id : 'dEdicion',
					startDay : Ext.app.DATESTARTDAY,
					selectOnFocus : true,
					allowBlank : true
				}, {
					xtype : 'displayfield',
					value : _s('nIdIdioma')
				}, idioma, {
					xtype : 'displayfield',
					value : _s('nPag')
				}, {
					xtype : 'numberfield',
					id : 'nPag',
					width : 50,
					style : 'text-align:left'
				}, {
					xtype : 'displayfield',
					value : _s('nIdEncuadernacion')
				}, encuadernacion, {
					xtype : 'displayfield',
					value : _s('fPeso')
				}, {
					xtype : 'numberfield',
					id : 'fPeso',
					style : 'text-align:left',
					width : 30
				}]
			}]
		}, {
			xtype : 'fieldset',
			items : [{
				xtype : 'compositefield',
				fieldLabel : _s('nIdColeccion'),
				items : [coleccion, {
					xtype : 'displayfield',
					value : _s('#')
				}, {
					xtype : 'textfield',
					width : 50,
					id : 'cNColeccion',
					selectOnFocus : true,
					allowBlank : true
				}, {
					xtype : 'tbbutton',
					iconCls : "iconoColecciones",
					tooltip : _s('add-coleccion'),
					handler : function() {
						var c = Ext.getCmp(editorial.id);
						if(c.getValue() > 0)
							Ext.Msg.prompt(form.getTitle(), _s('add-coleccion'), function(ok, v) {
								if(ok != 'ok')
									return;

								Ext.app.callRemote({
									url : site_url('catalogo/coleccion/add'),
									params : {
										nIdEditorial : c.getValue(),
										cNombre : v
									},
									fnok : function(res) {
										var c = Ext.getCmp(coleccion.id);
										c.setValue(res.id);
									}
								});
							});
					}
				}]
			}, editorial, proveedor, proveedormanual]
		}, {
			xtype : 'fieldset',
			items : [{
				xtype : 'compositefield',
				fieldLabel : _s('nIdEstado'),
				items : [estado, {
					xtype : 'displayfield',
					value : _s('nIdOferta')
				}, oferta]
			}, {
				xtype : 'compositefield',
				fieldLabel : _s('fPrecio'),
				items : [precio, {
					xtype : 'displayfield',
					value : _s('fPVP')
				}, pvp, {
					xtype : 'displayfield',
					value : _s('fPrecioOriginal')
				}, {
					xtype : 'numberfield',
					id : 'fPrecioOriginal',
					width : 50,
					readOnly : true//,
					//style: 'text-align:left'
				}, {
					xtype : 'displayfield',
					value : _s('fPrecioCompra')
				}, {
					xtype : 'numberfield',
					id : 'fPrecioCompra',
					readOnly : true,
					width : 50,
					style : 'text-align:left'
				}, {
					xtype : 'displayfield',
					value : _s('fIVA')
				}, {
					xtype : 'textfield',
					width : 30,
					id : 'fIVA',
					selectOnFocus : true,
					readOnly : true,
					allowBlank : true
				}, {
					xtype : 'displayfield',
					value : _s('bPrecioLibre')
				}, {
					xtype : 'checkbox',
					id : 'bPrecioLibre'
				}, {
					xtype : 'displayfield',
					value : _s('bNoDto')
				}, {
					xtype : 'checkbox',
					id : 'bNoDto'
				}]
			}]
		}, {
			xtype : 'fieldset',
			title : _s('Web'),
			items : [{
				xtype : 'compositefield',
				fieldLabel : _s('Dirección'),
				items : [{
					xtype : 'textfield',
					id : 'cURL',
					width : 400,
					readOnly : true
				}, {
					xtype : 'tbbutton',
					iconCls : "icon-web",
					tooltip : _s('ver-web'),
					handler : function() {
						if(data_load.cURL !== null) {
							Ext.app.addTabJSONHTMLFILE({
								html_file : data_load.cURL,
								icon : 'iconoWebTab',
								title : form.getTitle()
							});
						}
					}
				}]
			}, {
				xtype : 'compositefield',
				fieldLabel : _s('Manual'),
				items : [mostrarwebmanual, {
						xtype : 'displayfield',
						value : _s('nIdPlazoEnvioManual')
					}, plazoenviomanual, {
						xtype : 'displayfield',
						value : _s('nIdPlazoEnvio')
					}, plazoenvio]
			}/*, mostrarweb*/]
		}];

		// Controles normales
		var periodorevista = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('catalogo/periodorevista/search'),
			fieldLabel : _s('nIdTipoPeriodoRevista'),
			name : 'revista[nIdTipoPeriodoRevista]',
			allowBlank : true
		}));
		var tiposuscripcion = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('catalogo/tiposuscripcionrevista/search'),
			fieldLabel : _s('nIdTipoSuscripcion'),
			name : 'revista[nIdTipoSuscripcion]',
			allowBlank : true
		}));
		var periodosuscripcion = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('catalogo/periodosuscripcion/search'),
			fieldLabel : _s('nIdPeriodo'),
			name : 'revista[nIdPeriodo]',
			allowBlank : true
		}));

		var revista = [periodorevista, new Ext.form.NumberField({
			xtype : 'numberfield',
			fieldLabel : _s('nEjemplares'),
			name : 'revista[nEjemplares]',
			width : 50,
			value : 0,
			allowNegative : false,
			allowDecimals : false,
			selectOnFocus : true,
			allowBlank : true
		}), tiposuscripcion, periodosuscripcion, {
			xtype : 'checkbox',
			fieldLabel : _s('bRenovable'),
			id : 'revista[bRenovable]'
		}];

		// Relacionados
		var model = [{
			name : 'nIdLibro',
			column : {
				header : _s("Id"),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'id',
				sortable : true
			}
		}, {
			name : 'id'
		}, {
			name : 'cAutores',
			column : {
				header : _s("cAutores"),
				width : Ext.app.TAM_COLUMN_TEXT,
				sortable : true
			},
			ro : true
		}, {
			name : 'cTitulo',
			column : {
				header : _s('cTitulo'),
				width : Ext.app.TAM_COLUMN_TEXT,
				id : 'descripcion',
				sortable : true
			},
			ro : true
		}];

		var fnselect = function(id) {
			try {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/add_relacionado'),
					params : {
						'id1' : parseInt(form.getId()),
						'id2' : parseInt(id)
					},
					fnok : function() {
						new_libro.setValue(null);
						Ext.app.formLoadList({
							list : [form_id + '_libros_grid'],
							params : {
								id : form.getId()
							}
						});
					}
				});
			} catch (e) {
				console.dir(e);
			}
		}
		var new_libro = new Ext.form.ComboBox(Ext.app.autocomplete({
			url : site_url('catalogo/articulo/search'),
			width: 500,
			name : form_id + "_addlibro",
			id : form_id + "_addlibro",
			fnselect : fnselect
		}));

		var relacionados = Ext.app.createFormGrid({
			model : model,
			id : form_id + "_libros",
			//title: _s("Libros"),
			//icon: "icon-libros",
			idfield : 'id',
			show_filter: false,
			urlget : site_url("catalogo/articulo/relacionados"),
			urldel : site_url("catalogo/articulo/del_relacionado"),
			rbar : [{
				xtype : 'label',
				html : _s('new-libro')
			}, new_libro],
			anchor : '100% 85%',
			load : false
		});
		var grid = Ext.getCmp(form_id + '_libros_grid');
		var cm_lineas = fn_contextmenu();
		var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', cm_lineas);
		cm_lineas.setContextMenu(contextmenu)

		var model_media = [{
			name : 'nIdMedia',
			column : {
				header : _s("Id"),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'id',
				sortable : true
			}
		}, {
			name : 'id'
		}, {
			name : 'cUrl',
			column : {
				header : _s("URL"),
				width : Ext.app.TAM_COLUMN_TEXT,
				sortable : true
			},
			ro : true
		}, {
			name : 'cTipo',
			column : {
				header : _s("Tipo"),
				width : Ext.app.TAM_COLUMN_TEXT,
				sortable : true
			},
			ro : true
		}, {
			name : 'cTitulo',
			column : {
				header : _s("cTitulo"),
				id: 'descripcion',
				width : Ext.app.TAM_COLUMN_TEXT,
				sortable : true
			},
			ro : false
		}, {
			name : 'cDescripcion'
		}, {
			name : 'cImagen'
		}];

		var new_media = new Ext.form.TextField({
			enableKeyEvents : true,
			width: 500,
			listeners : {
				keypress : function(f, e) {
					if(e.getKey() == 13) {
						try {
							Ext.app.callRemote({
								url : site_url('catalogo/articulo/add_media'),
								params : {
									id : form.getId(),
									url : new_media.getValue()
								},
								fnok : function() {
									new_media.setValue(null);
									Ext.app.formLoadList({
										list : [form_id + '_media_grid'],
										params : {
											id : form.getId()
										}
									});
								}
							});
						} catch (e) {
							console.dir(e);
						}
					}
				}
			}
		});

        var rendererImagen = function(r){
            var t = '<table border="0" width="100%"><tr><td width="50px" height="50px">' 
            	+ ((r.data.cImagen!=null && r.data.cImagen!='')?('<img width="50px" src="' + r.data.cImagen + '" />'):'')
                + '</td><td>'
            	+ '<b>' + ((r.data.cDescripcion!=null)?(r.data.cDescripcion):'') + '</b>'
                +'</td></tr></table>';
            return t;
        }
		
		var media = Ext.app.createFormGrid({
			model : model_media,
			id : form_id + "_media",
			show_filter: false,
			idfield : 'id',
			urlget : site_url("catalogo/articulo/media"),
			urldel : site_url("catalogo/articulo/del_media"),
			rbar : [{
				xtype : 'label',
				html : _s('new-url')
			}, new_media, {
            text: _s('add-documentos'),
            iconCls: 'icon-upload',
            handler: function(button){
				var url = site_url('catalogo/articulo/add_media_file');
                Ext.app.formUploadMedia(form.getId(), url, _s('add-documentos'), _s('documentos-files'), '*.doc;*.pdf;*.rtf;*.txt;*.odt;*.docx', null,
                function() {
					Ext.app.formLoadList({
						list : [form_id + '_media_grid'],
						params : {
							id : form.getId()
						}
					});
                });
            }
        }],
			anchor : '100% 85%',
			preview: rendererImagen,
			load : false
		});

		grid = Ext.getCmp(form_id + '_media_grid');
		var cm_lineas2 = fn_contextmenu();
		var contextmenu2 = Ext.app.addContextMenuEmpty(grid, cm_lineas2);
		contextmenu2.add({
			text : _s('Ver'),
			handler : function() {
				var record = cm_lineas2.getItemSelect();
				if((record !== null) && (record.data.cUrl !== null)) {
					Ext.app.addTabJSONHTMLFILE({
						html_file : record.data.cUrl,
						icon : 'iconoWebTab',
						title : form.getTitle()
					});
				}
			},
			iconCls : 'icon-web'
		});
		contextmenu2.add('-');
		contextmenu2.add({
			text : _s('Cambiar descripción'),
			handler : function() {
				var record = cm_lineas2.getItemSelect();
				if((record !== null) && (record.data.nIdMedia !== null)) {
		            Ext.Msg.prompt(_s('Cambiar descripción'), _s('Descripción'), function(ok, v){
		                if (ok != 'ok') 
		                    return;
		                Ext.app.callRemote({
		                    params: {
		                        id: record.data.nIdMedia,
		                        text: v
		                    },
		                    url: site_url('catalogo/articulo/upd_media_text'),
							fnok : function() {
								Ext.app.formLoadList({
									list : [form_id + '_media_grid'],
									params : {
										id : form.getId()
									}
								});
							}
		                })
		            }, null, null, record.data.cDescripcion);
				}
			},
			iconCls : 'icon-edit'
		});
		contextmenu2.add('-');
		contextmenu2.add({
			text : _s('Eliminar imagen'),
			handler : function() {
				var record = cm_lineas2.getItemSelect();
				if((record !== null) && (record.data.nIdMedia !== null)) {
					Ext.app.callRemoteAsk({
						url : site_url('catalogo/articulo/del_media_image'),
						title : _s('Eliminar imagen'),
						askmessage : _s('elm-imagen-media'),
						params : {
							id : record.data.nIdMedia
						},
						fnok : function() {
							Ext.app.formLoadList({
								list : [form_id + '_media_grid'],
								params : {
									id : form.getId()
								}
							});
						}
					});
				}
			},
			iconCls : 'icon-delete'
		});

		// General
		form.addTab({
			title : _s('Vista'),
			iconCls : 'icon-report',
			items : {
				cls : 'form-articulo',
				id : form_id + "details-panel",
				xtype : 'iframepanel'
			}
		});

		form.addTab({
			title : _s('General'),
			iconCls : 'icon-general',
			items : {
				xtype : 'panel',
				layout : 'form',
				items : form.addControls(controls)
			}
		});
		var sinopsis = new Ext.ux.TinyMCE(Ext.app.formEditor({
			title : _s('Sinopsis'),
			anchor : '100% 100%',
			name : 'tSinopsis',
			id : form_id + '_sinopsis'
		}));
		form.addTab({
			title : _s('Publicación'),
			iconCls : 'iconoSuscripcionesTab',
			items : {
				xtype : 'panel',
				layout : 'form',
				items : form.addControls(revista)
			}
		});
		form.addTab({
			title : _s('Sinopsis'),
			iconCls : 'icon-sinopsis',
			items : form.addControls([sinopsis])
		});

		form.addTab({
			title : _s('Relacionados'),
			iconCls : 'icon-relacionados',
			items : {
				xtype : 'panel',
				layout : 'form',
				items : form.addControls(relacionados)
			}
		});

		form.addTab({
			title : _s('Multimedia'),
			iconCls : 'icon-multimedia',
			items : {
				xtype : 'panel',
				layout : 'form',
				items : form.addControls(media)
			}
		});


		var grid_notas = notas.init({
			id : form_id + "_notas",
			url : site_url('catalogo/articulo'),
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

		form.addAction({
			text : _s('documentos_articulo_form'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/documentos/' + form.getId())
				});
			},
			iconCls : 'icon-documents',
			id : form.idform + 'btn_documents'
		});
		form.addAction({
			text : _s('Compras'),
			handler : function() {
				Ext.app.callRemote({
					params : {
						idl : form.getId(),
						tipo : 'entdev;entalb'
					},
					url : site_url('catalogo/articulo/documentos')
				});
			},
			iconCls : 'iconoAlbaranEntrada',
			id : form.idform + 'btn_compras'
		});
		form.addAction({
			text : _s('Ventas'),
			handler : function() {
				Ext.app.callRemote({
					params : {
						idl : form.getId(),
						tipo : 'entdevcmp;salcmp'
					},
					url : site_url('catalogo/articulo/documentos')
				});
			},
			iconCls : 'iconoAlbaranSalida',
			id : form.idform + 'btn_ventas'
		});
		form.addAction({
			text : _s('Devoluciones'),
			handler : function() {
				Ext.app.callRemote({
					params : {
						idl : form.getId(),
						tipo : 'saldevall'
					},
					url : site_url('catalogo/articulo/documentos')
				});
			},
			iconCls : 'iconoDevolucion',
			id : form.idform + 'btn_devoluciones2'
		});

		form.addAction({
			text : _s('devoluciones_sin_entregar'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/devoluciones/' + form.getId())
				});
			},
			iconCls : 'iconoDevolucion',
			id : form.idform + 'btn_devoluciones'
		});
        form.addAction('-');
		var fn_suscripciones = function(){
            if (form.getId() !== null) {
                Ext.app.callRemote({
					params: {revista:   form.getId()},
                    url: site_url('suscripciones/suscripcion/buscar')
                });
            }
        }
        form.addAction({
            text: _s('Suscripciones'),
            iconCls: 'iconoSuscripciones',
            handler: fn_suscripciones,
            id: form_id + 'btn_suscripciones'
        });


		form.addTools({
			text : _s('antiguedad_articulo'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/antiguedad/' + form.getId())
				});
			},
			iconCls : 'iconAntiguo',
			id : form.idform + 'btn_antiguedad'
		});
		form.addTools('-');

		form.addTools({
			text : _s('stock_contado_articulo'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/stockcontado/' + form.getId())
				});
			},
			iconCls : 'iconoStockContado',
			id : form.idform + 'btn_stockcontado'
		});

		form.addTools({
			text : _s('Análisis de stock inventario'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('stocks/stockcontado/analisis/' + form.getId())
				});
			},
			iconCls : 'iconoStockRetrocedido',
			id : form.idform + 'btn_analisis'
		});
		form.addTools('-');
		form.addTools({
			text : _s('precios_articulo'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/historicoprecios/' + form.getId())
				});
			},
			iconCls : 'icon-precio',
			id : form.idform + 'btn_precios'
		});
		form.addTools({
			text : _s('Comprobación precios'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/check_precios/' + ean.getValue()),
					timeout: false
				});
			},
			iconCls : 'iconoConsultaPrecios',
			id : form.idform + 'btn_comprobarprecios'
		});
		form.addTools('-');
		form.addTools({
			text : _s('avisar-clientes'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/avisar'),
					params : {
						id : form.getId(),
						cmpid : form.idform
					}
				});
			},
			iconCls : 'icon-email',
			id : form.idform + 'btn_avisar'
		});
		form.addTools('-');
		form.addTools({
			text : _s('Análisis de ventas'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/analisis/' + form.getId())
				});
			},
			iconCls : 'iconoReports',
			id : form.idform + 'btn_analisisventas'
		});

		var fn_portada = function(id, texto, el) {
			var fn = function(res) {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/set_cover'),
					params : {
						url : res.url,
						id : id
					},
					fnok : function() {
						// Refresca el elemento
						if(el !== null) {
							try {
								el.src = site_url('catalogo/articulo/cover/' + id + '/' + el.width + '?' + Ext.app.createId());
							} catch (e) {
								console.dir(e);
							}
						}
					}
				});
			}
			searchPicture(texto, fn);
		}

	form.addAction({
			text : _s('Portada'),
			iconCls : 'icon-portada',
			menu: [{
			text : _s('Buscar portada por título'),
			handler : function() {
				var panel = Ext.getCmp(form_id + "details-panel");
				var dom = panel.getFrameDocument();
				var el = dom.getElementById('portada');
				var texto = titulo.getValue();
				fn_portada(form.getId(), texto, el);
			},
			iconCls : 'icon-portada',
			id : form.idform + 'btn_portada'
		},{
			text : _s('Buscar portada por ISBN'),
			handler : function() {
				var panel = Ext.getCmp(form_id + "details-panel");
				var dom = panel.getFrameDocument();
				var el = dom.getElementById('portada');
				var texto = isbn.getValue();
				fn_portada(form.getId(), texto, el);
			},
			iconCls : 'icon-portada',
			id : form.idform + 'btn_portada2'
		}, {
			text : _s('Buscar portada por EAN'),
			handler : function() {
				var panel = Ext.getCmp(form_id + "details-panel");
				var dom = panel.getFrameDocument();
				var el = dom.getElementById('portada');
				var texto = ean.getValue();
				fn_portada(form.getId(), texto.toString(), el);
			},
			iconCls : 'icon-portada',
			id : form.idform + 'btn_portada3'
		},{
			text : _s('Copiar portada de una dirección'),
			handler : function() {
				var panel = Ext.getCmp(form_id + "details-panel");
				var dom = panel.getFrameDocument();
				var el = dom.getElementById('portada');
				var texto = ean.getValue();
				Ext.Msg.prompt(form.getTitle(), _s('Dirección'), function(ok, v) {
					if(ok != 'ok')
						return;
					fn_portada(form.getId(), v.toString(), el);
				});
			},
			iconCls : 'icon-portada',
			id : form.idform + 'btn_portada4'
		}, '-', {
			text : _s('Eliminar portada'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/set_cover'),
					params : {
						id : form.getId()
					},
					fnok : function() {
						// Refresca el elemento
						try {
							var panel = Ext.getCmp(form_id + "details-panel");
							var dom = panel.getFrameDocument();
							var el = dom.getElementById('portada');
							el.src = site_url('catalogo/articulo/cover/' + form.getId() + '/' + el.width + '?' + Ext.app.createId());
						} catch (e) {
							console.dir(e);
						}
					}
				});

			},
			iconCls : 'icon-portada-delete',
			id : form.idform + 'btn_eliminar_portada'
		}]
		});
	

		form.addAction('-');
		form.addAction({
			text : _s('Promocionar Web'),
			handler : function() {
				var data = {
					nIdLibro : form.getId(),
					nIdTipoPromocion : Ext.app.PROMOCIONWEB,
					cDescripcion : _s('promo-web-articulo'),
					dInicio : DateToNumber(new Date()),
					dFinal : DateToNumber(DateAdd('d', Ext.app.DIASPROMOCIONARTICULO, new Date()))
				};
				Ext.app.callRemote({
					url : site_url('catalogo/promocion/add'),
					params : data,
					fnok : function() {
						form.refresh();
					}
				});
			},
			iconCls : 'iconoPromociones',
			id : form.idform + 'btn_promocionar'
		});

		tipo.store.load();
		tiposuscripcion.store.load();
		periodosuscripcion.store.load();
		periodorevista.store.load();

		var c = Ext.getCmp(editorial.id);
		c.on('additem', function(c, v) {
			var s = Ext.getCmp(coleccion.id);
			s.store.baseParams = {
				where : 'nIdEditorial=' + v,
				start : 0,
				limit : Ext.app.AUTOCOMPLETELISTSIZE
			}
			s.reset();
			var p = Ext.getCmp(proveedor.id);
			if(p !== null) {
				Ext.app.callRemote({
					url : site_url('catalogo/editorial/get/' + v),
					fnok : function(res) {
						p.setValue(res.value_data.nIdProveedor);
					}
				});
			}
		});
		return form.show(open_id);
	} catch (e) {
		console.dir(e);
	}
})();
