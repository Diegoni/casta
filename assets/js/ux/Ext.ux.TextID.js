Ext.ns('Ext.ux.form');

Ext.ux.form.TextID = function(config) {
	Ext.ux.form.TextID.superclass.constructor.call(this, config);
	this.addEvents('itemselect');
	this.addEvents('itemload');
};
/**
 * @private hide from doc gen
 */
Ext.ux.form.TextID = Ext.extend(Ext.form.TextField, {
	cantidadField : null,
	descuentoField : null,
	importeField : null,
	referenciaField : null,
	seccionField : null,
	articuloField : null,
	infoField : null,
	url_search : null,
	url_load : null,
	base : false,
	url_descuentos : null,
	field : null,
	fn_get_seccion : null,
	fn_get_descuento : null,
	aplicar_revargo : false,
	tarifas : null,
	autoselect : true,
	tarifas_general : null,
	introadd : true,
	useload : false,
	cache : false,
	help : '',
	_tip : null,
	_regex : [],
	/**
	 * Inicializa componente
	 */
	initComponent : function() {
		var me = this;
		me._regex = [{
			pattern : Ext.app.DOCSCANTIDAD,
			fn : function(m, c) {
				var v = (m[1] == '-') ? parseInt(-m[2]) : parseInt(m[2]);
				if(c.cantidadField != null) {
					c.cantidadField.setValue(v);
					c.subtotal();
					return true;
				}
			}
		}, {
			pattern : Ext.app.DOCSDESCUENTO,
			fn : function(m, c) {
				var v = parseInt(m[1]);
				if(c.descuentoField != null) {
					if((v >= 0) && (v <= 100)) {
						c.descuentoField.setValue(v);
						c.subtotal();
					} else
						c.info(_s('linea-descuento-error'));
					return true;
				}
			}
		}, {
			pattern : Ext.app.DOCSREF,
			fn : function(m, c) {
				var v = (m[1] != null) ? trim(m[1]) : '';
				if(c.referenciaField != null) {
					c.referenciaField.setValue(v);
					return true;
				}
			}
		}, {
			pattern : Ext.app.DOCSIMPORTE,
			fn : function(m, c) {
				var v = parseFloat(m[1]);
				if(c.importeField != null) {
					if(v >= 0) {
						v = v.decimal(Ext.app.DECIMALS);
						c.importeField.setValue(v);
						c.subtotal();
					} else
						c.info(_s('linea-importe-error'));
					return true;
				}
			}
		}, {
			pattern : "^" + Ext.app.TPV_HELP,
			fn : function() {
				me.info(me.help);
				return true;
			}
		}];

		if(this.cantidadField) {
			this.cantidadField.on('keypress', function(f, e) {
				if(e.getKey() == e.ENTER) {
					me.descuentoField.focus();
				}
			});
		}

		if(this.descuentoField) {
			this.descuentoField.on('keypress', function(f, e) {
				if(e.getKey() == e.ENTER) {
					me.importeField.focus();
				}
			});
		}

		if(this.importeField) {
			this.importeField.on('keypress', function(f, e) {
				if(e.getKey() == e.ENTER && this.introadd) {
					// Añadir...
					me.add();
				}
			});
		}

		// call parent initComponent
		Ext.ux.form.TextID.superclass.initComponent.call(this);
	},
	/**
	 * Selecciona un elemento
	 */
	add : function(extra) {
		var me = this;
		var el = this.el;

		if(me.seccionField != null) {
			var id = me.seccionField.getValue();
			if(me.field != null) {
				for(var i = 0; i < me.field.secciones.length; i++) {
					if(me.field.secciones[i].id == id) {
						me.field['seccion'] = me.field.secciones[i];
						break;
					}
				}
			}
		}
		if(extra != null) {
			me.field = Ext.apply(me.field, extra);
		}

		var pr = (me.importeField.getValue());
		try {
			if(pr == '') {
				pr = me.field['fPVP']
			}
			var old_dt = me.descuentoField.getValue(0);
			if ((me.field.bNoDto==true || me.field.nIdOferta!=null)) {
				me.descuentoField.setValue(0);
			}
			var valores = me.valores(pr, me.field['fIVA'], me.field['fRecargo']);
			if ((me.field.bNoDto==true || me.field.nIdOferta!=null)) {
				me.descuentoField.setValue(old_dt);
			}
			me.field = Ext.apply(me.field, {
				'nCantidad' : valores.cantidad,
				'fDescuento' : (me.field.bNoDto==true || me.field.nIdOferta!=null)?0:valores.descuento,
				'fPrecio' : valores.fPrecio,
				'fPVP' : valores.precio,
				'fImporte' : valores.unitario,
				'fIVAImporte' : valores.iva,
				'fBase' : valores.base,
				'fRecargoImporte' : valores.recargo,
				'fMargen' : Margen(valores.base, valores.cantidad * me.field.fPrecioCompra),
				'fTotal' : valores.total
			});

			me.fireEvent('itemselect', me, me.field);
			me.clear();
			me.setValue('');
		} catch (e) {
			//console.dir(e);
		}
		if (el.focus != null) el.focus();
		if (me.focus != null) me.focus();
	},
	/**
	 * Inicializa eventos
	 */
	initEvents : function() {
		var el = this.el;

		el.on({
			keydown : this.onKeyDownHandler,
			focus : this.onFocus,
			scope : this
		});
		var me = this;
		var fn = function() {
			me.add();
		}
		if(this.seccionField != null) {
			this.seccionField.on('select', function() {
				fn();
			});
			this.seccionField.on('keypress', function(t, e) {
				if(e.getKey() === e.ENTER) {
					if(t.getValue() != null)
						fn();
				}
			});
		}
	},
	/**
	 * Añade un nuevo comando al control
	 * @param {Object} pattern
	 * @param {Object} fn
	 */
	addPattern : function(pattern, fn, help) {
		if(help != null)
			this.help += help + '<br/>';
		this._regex[this._regex.length] = {
			pattern : pattern,
			fn : fn
		};
	},
	/**
	 * Focus
	 */
	onFocus : function() {
		var v = this.getValue();
		this.selectText(0, v.length);
	},
	/**
	 * Muestra texto de información
	 * @param {Object} text
	 */
	info : function(text) {
		if(this._tip == null) {
			this._tip = new Ext.ToolTip({
				target : this.el,
				anchor : 'top',
				iconCls : 'icon-alert',
				anchorOffset : 85, // center the anchor on the tooltip
				html : text
			});
		} else {
			this._tip.hide();
			this._tip.update(text);
		}
		this._tip.showBy(this.el);
	},
	/**
	 * Calcula el subtotal de la línea de venta
	 */
	subtotal : function() {
		if(this.infoField != null) {
			var data = this.valores()
		}
		//console.dir(data);
		this.infoField.setText(Ext.app.currencyFormatter(data.unitario));
	},
	/**
	 * Procesa los comandos de la casilla de texto
	 */
	processCommand : function() {
		var me = this;
		//me.disable();
		var value = this.getValue();
		value = value.trim();
		var stop = false;
		Ext.each(me._regex, function(p) {
			try {
				if(!stop) {
					var re = new RegExp(p.pattern);
					var m = re.exec(value);
					if(m != null)
						if(p.fn(m, me)) {
							stop = true;
						}
				}
			} catch (e) {
				console.dir(e);
			}
		});
		if(!stop) {
			me.load_articulo(value);
		} else {
			//me.enable();
			this.setValue('');
		}
	},
	/**
	 * Calcula el importe a partir de un precio
	 * @param {Object} precio
	 */
	valores : function(pr, iva, recargo, data) {
		var me = this;
		if(pr == null)
			pr = (me.importeField.getValue());
		var ct = (me.cantidadField.getValue());
		ct = (ct != '') ? parseInt(ct) : 1;
		var dt = (me.descuentoField.getValue());
		if((dt == '' || dt == null) && (me.fn_get_descuento != null) && (data != null)) {
			dt = me.fn_get_descuento(data);
			if(me.descuentoField != null)
				me.descuentoField.setValue(dt);
		}
		dt = (dt != '' && dt != null) ? parseFloat(dt) : 0;

		//console.log('Base ' + me.base);
		var totales = (me.base) ? ProcesarImportes(ct, 0, AplicarIVA(pr, iva), iva, recargo) : ProcesarImportes(ct, dt, pr, iva, recargo);
		//console.dir(totales);
		var fPrecio = (me.base) ? parseFloat(pr) : QuitarIVA(pr, iva);
		//console.dir(fPrecio);
		var field = {
			'fPrecio' : fPrecio,
			'precio' : pr,
			'cantidad' : ct,
			'descuento' : dt,
			'unitario' : (me.base) ? QuitarIVA(totales.unitario, iva) : totales.unitario,
			'base' : totales.base,
			'iva' : totales.iva,
			'recargo' : totales.recargo,
			'total' : totales.total
		}

		return field;
	},
	/**
	 * Limpia los campos
	 * @param {Object} all TRUE: limpia también el descuento
	 */
	clear : function(all) {
		if(this.articuloField != null)
			this.articuloField.setValue('');

		if(this.importeField != null)
			this.importeField.setValue('');

		if(this.cantidadField != null)
			this.cantidadField.setValue('');

		if(this.seccionField != null) {
			this.seccionField.getStore().removeAll();
			this.seccionField.setValue('');
		}

		if(all === true) {
			if(this.descuentoField != null)
				this.descuentoField.setValue('');
		}

		if(this.infoField != null)
			this.infoField.setText('');

		if(this.referenciaField != null)
			this.referenciaField.setValue('');

		this.field = null;
		this.setValue('');
	},
	/**
	 * Carga los datos
	 * @param {Object} data
	 */
	load : function(data) {
		var me = this;
		var relation = 'secciones';
		var id_cache = null;
		var in_cache = false;

		var accion = function(v) {
			
			var fn = function() {
				try {
					v.value_data.secciones
					if(me.articuloField != null) {
						me.articuloField.setValue(v.value_data.cTitulo);
					}
					var pr = (me.useload) ? (me.importeField.getValue()) : '';
					//console.log('Precio: ' + pr);
					if(pr == '') {
						pr = (me.base) ? v.value_data.fPrecio : v.value_data.fPVP;
						//console.log('Tarifas Articulo');
						//console.dir(v.value_data.tarifas)
						if(v.value_data.tarifas && (me.tarifas || me.tarifas_general)) {
							//El artículo tiene tarifas
							//console.log('Tiene tarifas');
							pr = getTarifa(v.value_data.fPVP, v.value_data.fIVA, v.value_data.nIdTipo, v.value_data.tarifas, me.tarifas, me.tarifas_general);
							//console.log('Precio tarifa: ' + pr);
							if(me.base)
								pr = QuitarIVA(pr, v.value_data.fIVA)
							me.importeField.setValue(pr);
						}
					} else {
						if(me.base)
							pr = AplicarIVA(pr, v.value_data.fIVA)
					}
					var recargo = me.aplicar_recargo ? v.value_data.fRecargo : 0;
					var old_dt = me.descuentoField.getValue(0);
					if ((v.value_data.bNoDto==true || v.value_data.nIdOferta!=null)) {
						me.descuentoField.setValue(0);
					}
					var valores = me.valores(pr, v.value_data.fIVA, recargo, v.value_data);
					if ((v.value_data.bNoDto==true || v.value_data.nIdOferta!=null)) {
						me.descuentoField.setValue(old_dt);
					}
					var coste = (valores.cantidad < 0) ? -v.value_data.fPrecioCompra : v.value_data.fPrecioCompra;
					var referencia = (me.referenciaField != null) ? me.referenciaField.getValue() : null;
					//console.log('Load item ' + v.value_data.bNoDto + ' = ' + ((v.value_data.bNoDto==true)?0:valores.descuento).toString());
					var field = Ext.apply(v.value_data, {
						'id' : data.id,
						'nCantidad' : valores.cantidad,
						'cTitulo' : v.value_data.cTitulo,
						'fDescuento' : (v.value_data.bNoDto==true || v.value_data.nIdOferta!=null)?0:valores.descuento,
						'fIVA' : v.value_data.fIVA,
						'fPrecio' : valores,
						'fPVP' : (me.base) ? (parseFloat(valores.precio) + parseFloat(valores.iva)) : valores.precio,
						'fImporte' : valores.unitario,
						'cReferencia' : referencia,
						'fIVAImporte' : valores.iva,
						'bNoDto': v.value_data.bNoDto,
						'nIdOferta': v.value_data.nIdOferta,
						'fBase' : valores.base,
						'fCoste' : coste,
						'fRecargo' : recargo,
						'fRecargoImporte' : valores.recargo,
						'fMargen' : Margen(valores.base, valores.cantidad * v.value_data.fPrecioCompra),
						'fTotal' : valores.total
					});
					me.field = field;

					me.fireEvent('itemload', me, me.field, v.value_data);

					if(me.fn_get_seccion != null) {
						var sec = me.fn_get_seccion(v.value_data.secciones);
						if(sec.select != null) {
							field['seccion'] = sec.select;
							field['secciones'] = sec.secciones;
							me.fireEvent('itemselect', me, field);
							me.clear();
							me.focus();
							return;
						}
					}
					if(me.seccionField != null) {
						var st = me.seccionField.getStore();
						st.removeAll();
						st.loadData({
							total_data : sec.secciones.length,
							value_data : sec.secciones
						});
						if(st.getTotalCount() > 0) {
							me.seccionField.setValue(st.getAt(0).data.id);
						}
						field['secciones'] = sec.secciones;
						me.field = field;
						me.seccionField.focus();
					} else {
						me.cantidadField.focus();
					}
				} catch (e) {
					console.dir(e);
				}
			}			
			if(me.cache && !in_cache) {
				Ext.app.setStorage(id_cache, v);
			}
			if(me.url_descuentos) {
				var id_cache2 = null;
				if(me.cache) {
					id_cache2 = serialize(me.url_descuentos) + '_' + serialize({
						id : data.id
					});
					var res = Ext.app.getStorage(id_cache2);
					if(res != null) {
						v.value_data['descuentos'] = res;
						fn();
						return;
					}
				}

				Ext.app.callRemote({
					url : me.url_descuentos,
					params : {
						id : data.id
					},
					fnok : function(res) {
						if(me.cache) {
							Ext.app.setStorage(id_cache2, res.value_data);
						}
						v.value_data['descuentos'] = res.value_data;
						fn();
					}
				});
			} else {
				fn();
			}
		}
		if(me.tarifas != null || me.tarifas_general != null)
			relation += ';tarifas';
		if(this.cache) {
			id_cache = serialize(this.url_load) + '_' + serialize({
				id : data.id,
				relation : relation
			});
			var res = Ext.app.getStorage(id_cache);
			if(res != null) {
				in_cache = true;
				accion(res);
				return;
			}
		}
		Ext.app.callRemote({
			url : this.url_load,
			params : {
				id : data.id,
				relation : relation
			},
			fnok : accion
		});

	},
	/**
	 * Busca un artículo
	 * @param {Object} title
	 */
	load_articulo : function(title) {
		var me = this;
		me.onFocus();
		if(me.url_search != null && title.trim() != '') {
			var store = Ext.app.getStore(me.url_search, ['id', 'text'], false, true);
			store.baseParams = {
				start : 0,
				limit : Ext.app.AUTOCOMPLETELISTSIZE
			}
			var fn = function() {
				//me.enable();

				if((parseInt(store.getTotalCount()) > 1)) {
					var listView = new Ext.list.ListView({
						store : store,
						columnSort : true,
						singleSelect : true,
						height : 250,
						reserveScrollOffset : true,
						columns : [{
							header : _s('Id'),
							width : .10,
							dataIndex : 'id'
						}, {
							header : _s('cDescripcion'),
							width : .90,
							dataIndex : 'text'
						}]
					});

					var fn_ok = function() {
						var v = listView.getSelectedIndexes();
						v = v[0];
						v = store.getAt(v);

						me.load(v.data);
					};
					var form = Ext.app.formStandarForm({
						controls : [listView],
						fn_ok : fn_ok
					});

					listView.on('dblclick', function(view, index) {
						var v = store.getAt(index);
						form.close();
						me.load(v.data);
					});
					form.show();
				} else {
					if(store.getTotalCount() > 0) {
						var v = store.getAt(0);
						me.load(v.data);
					} else {
						me.info(_s('linea-articulo-no-encontrado'));
					}
				}
			}
			store.load({
				params : {
					query : title,
					start : 0,
					limit : Ext.app.AUTOCOMPLETELISTSIZE
				},
				callback : fn
			});
		}
	},
	/**
	 * Evento de KeyPress
	 * @param {Object} e
	 * @param {Object} t
	 */
	onKeyDownHandler : function(e, t) {
		if(e.getKey() === e.ENTER) {
			this.processCommand();
		}
	},
	/**
	 * Aplicar recargo de equivalencia a las líneas
	 * @param {Object} aplicar
	 */
	aplicarRecargo : function(aplicar) {
		this.applicar_recargo = aplicar;
	},
	/**
	 * Asigna las tarifas para el cálculo de precios
	 * @param {Object} general
	 * @param {Object} tarifas
	 */
	setTarifas : function(general, tarifas) {
		//console.log('LINEAS: Asignanco tarifa');
		try {
			this.tarifas_general = general;
			this.tarifas = tarifas;
			//console.log('Tarifa ' + this.tarifas_general);
			//console.dir(this.tarifas);
		} catch (e) {
			console.dir(e);
		}
	}
});
// eo extend
// register xtype
Ext.reg('xtextid', Ext.ux.form.TextID);
