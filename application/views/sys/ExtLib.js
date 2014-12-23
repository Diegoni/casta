/**
 * Formatea moneda
 * @param {Object} c
 * @param {Object} d
 * @param {Object} t
 * @param {Object} s
 */
Ext.util.Format.CurrencyFactory = function(c, d, t, sl, sr) {
	if (c == null || c == undefined) c = 0;
	return function(n) {
		if((n == null) || (n == ''))
			n = 0;
	//console.log('CurrencyFactory ' + n + '->' + parseFloat(n))
	n = parseFloat(n);
		var signo = (n < 0) ? '-' : '';
		var m = ( c = Math.abs(c) + 1 ? c : 2, d = d || ",", t = t || ".", /(\d+)(?:(\.\d+)|)/.exec(n + ""));
		if(m == null)
			return n;
		var x = m[1].length > 3 ? m[1].length % 3 : 0;
		var value = ((sl != null) ? sl : '') + (signo + ( x ? m[1].substr(0, x) + t : "") + m[1].substr(x).replace(/(\d{3})(?=\d)/g, "$1" + t) + ( c ? d + (+m[2] || 0).toFixed(c).substr(2) : "")) + ((sr != null) ? sr : '');
		//console.log('CurrencyFactory ' + n + '->' + value)
		return value;
	}
}
/**
 * Aplicación
 */
Ext.app = Ext.apply(Ext.app, function() {
	var msgCt;
	function createBox(t, s) {
		return ['<div class="msg">', '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>', '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>', '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>', '</div>'].join('');
	}

	return {
		/**
		 * Instancia de Zeroclipboard
		 */
		clipboard : null,
		/**
		 * Flag de preguntar antes de salir
		 */
		askexit : true,

		/**
		 * Flag de ventana de mensajes a la vista
		 */
		showingmessage : false,

		/**
		 * Hash de Stores
		 */
		hashStores : new Array(),

		/**
		 * Control de tabs
		 */
		tab : null,

		/**
		 * LLamadas en curso
		 */
		request : 0,

		/**
		 * Comandos ejecutados
		 */
		commands : new Array(),

		/**
		 * Si/no de combos
		 * @param {Object} reload
		 */
		combo_data : null,

		/**
		 * Permisos del usuario
		 */
		permisos : new Array(),
		/**
		 * Último mensaje leído del chat
		 */
		lastMessageID : -1,

		/**
		 * Comprueba si el usuario tiene un permiso dado
		 * @param {Object} perm
		 */
		is_allow : function(perm) {
			return (Ext.app.permisos[perm] === true);
			//return in_array(perm, Ext.app.permisos);
		},
		/**
		 * Función de login
		 * @param {Object} reload
		 * @param {Object} fn
		 * @param {Object} title
		 */
		login : function(reload, fn, title) {
			Ext.app.formLogin(fn, null, reload, title);
		},
		reload : function() {
			Ext.Msg.show({
				title : _s('reload_app'),
				icon : Ext.Msg.QUESTION,
				buttons : Ext.MessageBox.YESNO,
				msg : _s('reload_app_question'),
				fn : function(btn, text) {
					if(btn == 'yes') {
						Ext.app.askexit = false;
						window.location = site_url();
					}
				}
			});
		},
		help : function(topic) {
			Ext.app.callRemote({
				url : site_url('help/' + topic)
			});
		},
		/**
		 * Sale de la aplicación. Pregunta
		 */
		exitApp : function() {
			Ext.Msg.show({
				title : _s('title'),
				buttons : Ext.MessageBox.YESNO,
				icon : Ext.Msg.QUESTION,
				msg : _s('q-salir'),
				fn : function(btn, text) {
					if(btn == 'yes') {
						Ext.app.callRemote({
							url : site_url('user/auth/logout'),
							fnok : function() {
								Ext.app.askexit = false;
								window.location = site_url();
							}
						});
					}
				}
			});
		},
		/**
		 * Crea el menú de la aplicación
		 */
		menuApp : {
			xtype : 'tbbutton',
			text : _s('title'),
			cls : 'x-btn-text-icon',
			iconCls : 'icon-app',
			menu : [{
				text : _s('Conexion'),
				iconCls : 'icon-conexion',
				handler : function(button) {
					Ext.app.login();
				}
			}, {
				text : _s('Salir'),
				icon : 'icon-salir',
				handler : function(button) {
					Ext.app.exitApp();
				}
			}]
		},

		/**
		 * Crea los combos
		 * @param {Object} config
		 */
		formComboPaises : function(config) {
			var idp = config['idpais'];
			var idr = config['idregion'];
			var value_p = config['value_p'];
			var value_r = config['value_r'];
			var allowblank = config['allowblank'];

			var id1 = Ext.app.combobox({
				id : idp,
				name : 'nIdPais',
				url : site_url('perfiles/pais/search'),
				label : _s('País'),
				autoload : true
			})

			id1['listeners'] = {
				select : {
					fn : function() {
						var g = Ext.getCmp(idr);
						//g.clearValue();
						g.reset();
						g.store.removeAll();
						g.store.load({
							params : {
								where : 'nIdPais=' + parseInt(this.value)
							}
						});
					}
				}
			}

			var id2 = Ext.app.combobox({
				id : idr,
				name : 'nIdRegion',
				url : site_url('perfiles/region/search'),
				label : _s('Región')//,
				//autoload: true
			});

			try {
				if(value_p != null) {
					id1 = new Ext.form.ComboBox(id1);
					id2 = new Ext.form.ComboBox(id2);
					id1.store.load({
						callback : function() {
							id1.setValue(value_p);
						}
					});

					if(value_r != null) {
						id2.store.removeAll();
						id2.store.load({
							params : {
								where : 'nIdPais=' + parseInt(value_p)
							},
							callback : function() {
								id2.setValue(parseInt(value_r));
							}
						});
					}
				}
			} catch (e) {
			}
			return [id1, id2];
		},
		/**
		 * Muestra un formulairo estándar
		 * @param {Object} config
		 */
		formStandarForm : function(config) {

			var fn_pre = config['fn_pre'];
			var fn_ok = config['fn_ok'];
			var title = config['title'];
			var url = config['url'];
			var wait = config['wait'];
			if(wait == null)
				wait = true;
			var upload = config['upload'];
			var timeout = config['timeout'];
			var id = config['id'];
			var width = config['width'];
			var height = config['height'];
			var focus = config['focus'];
			var icon = config['icon'];
			if(id == null)
				id = Ext.app.createId();
			var close = config['close'];
			if(close == null)
				close = 'close';
			var buttons = config['buttons'];
			var disableok = config['disableok'];
			if(disableok == null)
				disableok = false;

			var autosize = config['autosize'];
			if(autosize == null)
				autosize = true;
			if(buttons == null) {
				buttons = [];
				if(!disableok) {
					buttons[buttons.length] = {
						text : _s('Aceptar'),
						iconCls : 'icon-accept-form',
						handler : function(b) {
							if(form.getForm().isValid()) {
								if(fn_pre != null) {
									if(fn_pre() === false)
										return;
								}
								if(url != null) {
									window.hide();
									Ext.app.sendForm({
										url : url,
										form : form,
										timeout : timeout,
										title : title,
										upload : upload,
										wait : wait,
										fnok : function(o) {
											if(fn_ok != null) {
												if(fn_ok(o) === false)
													return;
											}
											window.close();
										},
										fnnok : function(o) {
											window.show();
										}
									});
								} else {
									if(fn_ok != null) {
										if(fn_ok(form.getForm().getValues()) === false) {
											return;
										}
									}
									window.close();
								}
							}
						}
					}
				}
				buttons[buttons.length] = {
					text : _s('Cerrar'),
					iconCls : 'icon-cancel-form',
					handler : function(b, f) {(close == 'close') ? window.close() : window.hide();
					}
				};
			} else {
				buttons[buttons.length] = {
					text : _s('Cerrar'),
					iconCls : 'icon-cancel-form',
					handler : function(b, f) {
						window.close();
					}
				}
			}
			/*buttons[buttons.length] = {
				text : _s('Limpiar'),
				iconCls : 'icon-clean',
				handler : function(b, f) {
					form.getForm().reset();
				}
			};*/

			var form = new Ext.FormPanel({
				monitorValid : true,
				defaults : {
					msgTarget: 'side',
				},
				labelWidth : (config['labelWidth'] != null) ? config['labelWidth'] : Ext.app.LABEL_SIZE,
				layout : 'form',
				border : false,
				//width: width,
				height : height,
				fileUpload : upload,
				bodyStyle : 'background:transparent;padding:2px',
				id : id,
				items : config['controls']
			});

			var window = new Ext.Window({
				title : config['title'],
				autoHeight : autosize,
				bodyStyle : 'padding: 10px 10px 0 10px;',
				width : (width != null) ? width : Ext.app.STANDARDFORM_WIDTH,
				height : (height != null) ? height : Ext.app.STANDARDFORM_HEIGHT,
				closeAction : close,
				iconCls : icon,
				border : false,
				resizable : false,
				plain : true,
				modal : true,
				items : form,
				id : id + '_window',
				listeners : {
					'show' : function(w) {
						if(focus != null)
							focus = Ext.getCmp(focus);
						if(focus == null && config['controls'] != null)
							focus = config['controls'][0];
						if(focus != null) {
							setTimeout(function() {
								try {
									focus.selectText();
									focus.focus();
								} catch (e) {
								}
							}, 500);
						}
						if(config['show'] != null)
							config['show']();
					}
				},
				buttons : [buttons]
			});
			return window;

		},
		/**
		 * Crea un menú con los comandos de exportación de GRIDS
		 */
		menuExport : function(id, is_grid, title) {
			var menu = [{
				iconCls : "icon-html",
				text : _s('cmd-export-html'),
				handler : function() {
					if(is_grid == true)
						Ext.app.exportGrid(id, 'html', title);
					else
						Ext.app.exportHTML(id.getId(), 'html');
				}
			}, {
				iconCls : "icon-rtf",
				text : _s('export_cmd_rtf'),
				handler : function() {
					if(is_grid == true)
						Ext.app.exportGrid(id, 'rtf', title);
					else
						Ext.app.exportHTML(id.getId(), 'rtf');
				}
			}, {
				iconCls : "icon-odt",
				text : _s('export_cmd_odt'),
				handler : function() {
					if(is_grid == true)
						Ext.app.exportGrid(id, 'odt', title);
					else
						Ext.app.exportHTML(id.getId(), 'odt');
				}
			}, {
				iconCls : "icon-word",
				text : _s('export_cmd_word'),
				handler : function() {
					if(is_grid == true)
						Ext.app.exportGrid(id, 'docx', title);
					else
						Ext.app.exportHTML(id.getId(), 'docx');
				}
			}, {
				iconCls : "icon-excel",
				text : _s('cmd-export-excel'),
				handler : function() {
					if(is_grid == true)
						Ext.app.exportGrid(id, 'xls', title);
					else
						Ext.app.exportHTML(id.getId(), 'xls');
				}
			}, {
				iconCls : "icon-pdf",
				text : _s('cmd-export-pdf'),
				handler : function() {
					if(is_grid == true)
						Ext.app.exportGrid(id, 'pdf', title);
					else
						Ext.app.exportHTML(id.getId(), 'pdf');
				}
			}, '-', {
				iconCls : "icon-email",
				text : _s('cmd-send-email'),
				handler : function() {
					if(is_grid == true)
						Ext.app.emailGrid(id, title);
					else
						Ext.app.emailHTML(id.getId(), title);
				}
			}, '-', {
				iconCls : "icon-copy",
				text : _s('export_cmd_clipboard'),
				handler : function() {
					if(is_grid == true)
						Ext.app.clipboardGrid(id, title);
					else
						Ext.app.clipboardHTML(id.getId());
				}
			}, '-', {
				iconCls : "icon-excel",
				text : _s('cmd-export-excel2'),
				handler : function() {
					if(is_grid == true)
						Ext.app.exportGrid(id, 'xlsx', title);
					else
						Ext.app.exportHTML(id.getId(), 'xlsx');
				}
			}];

			if(is_grid == true) {
				menu[menu.length] = '-';

				menu[menu.length] = {
					tooltip : _s('cmd-print'),
					iconCls : 'icon-print',
					id : id + '_btnprint',
					text : _s('Imprimir'),
					handler : function() {
						Ext.app.printGrid(id);
					}
				};
			}

			return {
				xtype : 'tbbutton',
				text : _s('Exportar'),
				cls : 'x-btn-text-icon',
				iconCls : "icon-download",
				menu : menu
			}
		},
		/**
		 * Formatos de divisas
		 */
		euroFormatter : Ext.util.Format.CurrencyFactory(2, ",", ".", '', "&euro;"),
		dollarFormatter : Ext.util.Format.CurrencyFactory(2, ",", ".", '', "&dolar;"),
		yenFormatter : Ext.util.Format.CurrencyFactory(2, ",", ".", "&yen;", ''),
		poundFormatter : Ext.util.Format.CurrencyFactory(2, ",", ".", "&pound;", ''),
		numberFormatter : Ext.util.Format.CurrencyFactory(Ext.app.DECIMALS, Ext.app.DEC_POINTS, Ext.app.THOUSANDS_SET),
		currencyFormatter : Ext.util.Format.CurrencyFactory(Ext.app.DECIMALS, Ext.app.DEC_POINTS, Ext.app.THOUSANDS_SET, Ext.app.SYMBOL_LEFT, Ext.app.SYMBOL_RIGHT),

		/**
		 * Dibuja una moneda en un grid
		 */
		renderMoney : function(val) {
			val = parseFloat(val);
			if(val > 0) {
				return '<span style="color:green;">' + Ext.app.euroFormatter(val) + '</span>';
			} else if(val < 0) {
				return '<span style="color:red;">' + Ext.app.euroFormatter(val) + '</span>';
			}
			return val;
		},
		/**
		 * Dibuja una fecha en un grid
		 */
		renderDate : function(value, p, r) {	
			//console.log('En date');	
			//console.log(value);
			try {				
				if( value.dateFormat == null) {
					//console.log('Es int ' + value + ' ' + parseInt(value));
					value = parseInt(value);
					if ((value == null) || (value == 0)) return '';
					value = new Date(NumberToDate(value));
				}
				//console.log('Es date ' + value ? value.dateFormat(Ext.app.DATEFORMATLONG) : '');
				return value ? value.dateFormat(Ext.app.DATEFORMATLONG) : '';
			} catch (e) {
				return '';
			}
		},
		/**
		 * Dibujo de la portada
		 */
		rendererPortada : function(value, p, r) {
			return Ext.app.getPortada(50).replace('{id}', r.data.nIdLibro);
		},
		/**
		 * Crea un TAG IMG con la portada
		 */
		getPortada : function(width, idname, id2) {
			var id = Ext.app.createId();
			if(idname == null)
				idname = 'id';
			id2 = (id2 == null) ? '' : (' id="' + id2 + '"');
			return '<img ' + id2 + ' src="' + site_url('catalogo/articulo/cover/{' + idname + '}/' + width + '?' + id) + '" width="' + width + '" />';
		},
		/**
		 * Dibuja una fecha en formato corto en un grid
		 */
		renderDateShort : function(value, p, r) {
			try {
				//console.log(value);				
				if( value.dateFormat == null) {
					//console.log('Es int ' + value + ' ' + parseInt(value));
					value = parseInt(value);
					if ((value == null) || (value == 0)) return '';
					value = new Date(NumberToDate(value));
				}
				//console.log('Es date ' + value ? value.dateFormat(Ext.app.DATEFORMATLONG) : '');
				return value ? value.dateFormat(Ext.app.DATEFORMATSHORT) : '';
			} catch (e) {
				return '';
			}

		},
		/**
		 * Dibuja una fecha en formato corto en un grid
		 */
		renderTime : function(value, p, r) {
			if( value.dateFormat == null) {
				//console.log('Es int ' + value + ' ' + parseInt(value));
				value = parseInt(value);
				if ((value == null) || (value == 0)) return '';
				value = new Date(NumberToDate(value));
			}
			return value ? value.dateFormat(Ext.app.TIMEFORMAT) : '';
		},
		/**
		 * Dibuja un check en un grid
		 */
		renderCheck : function(v, p, record) {
			if(p != null) {
				p.css += ' x-grid3-check-col-td';
				v = (v=='0')?false:((v=='1')?true:v);
				return '<div class="x-grid3-check-col' + ( v ? '-on' : '') + ' x-grid3-cc-' + this.id + '">&#160;</div>';
			} else {
				return '<div class="x-grid3-check-col' + ( v ? '-on' : '') + ' x-grid3-cc-' + this.id + '">' + ( v ? '[x]' : '&#160;') + '</div>';
			}
		},
		/**
		 * Dibuja un combdo en un grid
		 */
		renderCombo : function(val, grupos) {
			var g = grupos.store.queryBy(function(rec) {
				return rec.data.id == val;
			});
			if(g.itemAt(0) != null) {
				return g.itemAt(0).data.text;
			} else
				return val;
		},
		/**
		 * Imprime un iframe
		 */
		printIframe : function(id) {
			try {

				var re = new RegExp('FILE\:.(.*?)-', 'm');
				var html = Ext.getCmp(id).getFrameDocument().documentElement.innerHTML.substring(0, 200);
				var file = re.exec(html);
				if(file == null) {
				Ext.getCmp(id).getFrameWindow().print();
					return;
				}
				file = file[1];
				var url = site_url('sys/export/file/') + file + '/PRINT';
				Ext.app.addTabUrl({
					title : _s('Imprimir'),
					icon : 'iconoPreviewTab',
					url : url,
					print : false,
					navigation : false,
					'export' : false
				});
				return;
				//Ext.getCmp(id).iframe.print();
			} catch (ex) {
				Ext.app.msgError(_s('Imprimir'), _s('err_impresions') + '<br />' + ex);
			}
		},
		/**
		 * Envia un Grid por email
		 */
		exportGrid : function(id, type, title) {
			// Coge el grid
			var grid = Ext.getCmp(id);
			//try {
			// CSS
			Ext.ux.GridPrinter.stylesheetPath = Ext.app.PRINT_CSS;

			//HTML
			var html = Ext.ux.GridPrinter.print(grid, title);
			// Llama al controlador de exportaciones
			Ext.app.callRemote({
				url : site_url('sys/export/html'),
				params : {
					html : html,
					type : type
				},
				fnok : function(res) {
					// Descarga el archivo de resultado
					Ext.app.askexit = false;
					document.location = res.src;
					setTimeout(function() {
						Ext.app.askexit = true;
					}, 2);
				}
			});
		},
		/**
		 * Envia un Grid por email
		 */
		clipboardGrid : function(id, title) {
			// Coge el grid
			var grid = Ext.getCmp(id);
			//try {
			// CSS
			Ext.ux.GridPrinter.stylesheetPath = Ext.app.PRINT_CSS;

			//HTML
			var html = Ext.ux.GridPrinter.print(grid, title);
			this.setClipboard(html);
		},
		/**
		 * Exporta un Grid a diferentes formatos
		 */
		emailGrid : function(id, title) {
			// Coge el grid
			var grid = Ext.getCmp(id);
			//try {
			// CSS
			Ext.ux.GridPrinter.stylesheetPath = Ext.app.PRINT_CSS;

			//HTML
			var html = Ext.ux.GridPrinter.print(grid, title);
			// Llama al controlador de exportaciones
			Ext.app.callRemote({
				url : site_url('sys/export/html'),
				params : {
					html : html,
					type : 'pdf'
				},
				fnok : function(res) {
					Ext.app.callRemote({
						url : site_url('comunicaciones/email/index'),
						params : {
							id : Ext.app.createId(),
							file : res.file,
							subject : title
						}
					});
				}
			});
		},
		/**
		 * Email de un HTML
		 */
		emailHTML : function(id, title) {
			try {

				var re = new RegExp('FILE\:.(.*?)-', 'm');

				var html = Ext.getCmp(id).getFrameDocument().documentElement.innerHTML.substring(0, 200);
				var file = re.exec(html);
				if(file == null) {
					Ext.app.msgFly(_s('Exportar'), _s('no-report-file'));
					return;
				}
				file = file[1];
				Ext.app.callRemote({
					url : site_url('comunicaciones/email/index'),
					params : {
						id : Ext.app.createId(),
						file : file,
						subject : title
					}
				});

				return;
			} catch (ex) {
				Ext.app.msgError(_s('Exportar'), _s('err_exportacion') + '<br />' + ex);
			}
		},
		/**
		 * Exporta un HTML a diferentes formatos
		 */
		exportHTML : function(id, type) {
			try {

				var re = new RegExp('FILE\:.(.*?)-', 'm');
				var html = Ext.getCmp(id).getFrameDocument().documentElement.innerHTML.substring(0, 200);
				var file = re.exec(html);
				var url = null;
				if(file == null) {
					file = Ext.getCmp(id).getFrameWindow().location.href;
					url = site_url('sys/export/url/') + escape(file) + '/' + type;
					return;
				} else {
					file = file[1];
					url = site_url('sys/export/file/') + file + '/' + type;
				}

				Ext.app.askexit = false;
				document.location = url;
				setTimeout(function() {
					Ext.app.askexit = true;
				}, 2);
				return;
			} catch (ex) {
				Ext.app.msgError(_s('Exportar'), _s('err_exportacion') + '<br />' + ex);
			}
		},
		/**
		 * Exporta un HTML a diferentes formatos
		 */
		clipboardHTML : function(id) {
			try {
				var html = Ext.getCmp(id).getFrameDocument().documentElement.innerHTML;
				this.setClipboard(html);
				return;
			} catch (ex) {
			}
		},
		/**
		 *
		 * Muestra estado ocupado en la barra de tareas. SIN USO
		 */
		showStatusBusy : function() {
			/*
			 * statusBar = Ext.getCmp('statusbar'); if (statusBar)
			 * statusBar.showBusy();
			 */
		},
		/**
		 * Limpia el estado de la barra de tareas
		 */
		showStatusClear : function() {
			statusBar = Ext.getCmp('statusbar');
			if(statusBar)
				statusBar.clearStatus();
		},
		/**
		 * Muestra un texto en la barra de tareas
		 * @param {Object} text
		 * @param {Object} icon
		 */
		showStatusText : function(text, icon) {
			statusBar = Ext.getCmp('statusbar');
			if(statusBar)
				statusBar.setStatus({
					iconCls : icon,
					text : text
				});
		},
		/**
		 * Obtiene el Id de un combo
		 */
		getIdCombo : function(c) {
			var id = c.getValue();
			if(c.getRawValue() == '') {
				return null;
			}
			return id;
		},
		prepareCmd : function(cmd) {
			cmd = trim(cmd);
			//Busca las frases
			var p = cmd.split(" ");
			var str = '';
			var par = '';
			for(var i = 0; i < p.length; i++) {
				if(p[i].substr(0, 1) == "\"") {
					par = p[i];
				} else if((p[i].substr(p[i].length - 1, 1) == "\"") && (par != '')) {
					par += ' ' + p[i];
					par = par.replace(/\"/g, '');
					str += '/' + par;
					par = '';
				} else if((par != '')) {
					par += ' ' + p[i];
				} else if(p[i] != '') {
					str += '/' + p[i];
				}
			}
			if(par != '')
				str += '/' + par.replace(/\"/g, '');
			cmd = str.substr(1);
			if(cmd.substr(0, 3) == 'do/')
				cmd = cmd.substr(3);
			else
				cmd = 'cmd/' + cmd;
			return cmd;
		},
		/**
		 * Ejecuta un comando añadiendo la ventana al tab
		 * @param {Object} id
		 * @param {Object} title
		 * @param {Object} icon
		 */
		execCmd : function(config) {
			try {
				var id = config['id'];
				var title = config['title'];
				var icon = config['icon'];
				var url = config['url'];
				var fnok = (config['fnok'] != null) ? config['fnok'] : null;
				var fnnok = (config['fnok'] != null) ? config['fnnok'] : null;
				var timeout = config['timeout'];
				if(timeout === false) {
					timeout = Ext.app.TIMEOUTREMOTECALLMAX;
				}

				var id2 = new String(id);
				var ar = id2.split(";");
				var params = '';
				if(ar[1] != null) {
					params = '/' + ar[1];
				}
				ar = ar[0].split(".");

				if(url == null) {
					if(ar[0] != null) {
						// url = 'index.php?c=' + ar[0]; //Opcional dependiendo del modo
						// CI
						url = 'index.php/' + ar[0];
					}
					if(ar[1] != null) {
						// url = url + '&m=' + ar[1]; //Opcional dependiendo del modo CI
						url = url + '/' + ar[1];
					}
					//var T = Ext.getCmp('Tabs');
					if(ar[2] != 'unique') {
						id = id + '.' + Ext.app.createId();
					}
					if(params != null)
						url += params;
				}
				if(id == null) {
					id = Ext.app.createId();
				}

				var f = Ext.app.tab.findById(id);
				if(!f) {
					Ext.app.callRemote({
						url : url,
						wait : false,
						fnok : fnok,
						fnnok : fnnok,
						params : {
							id : id,
							title : title,
							icon : icon
						},
						timeout : timeout,
						success : function(xhr) {
							Ext.app.analizeResponse({
								xhr : xhr,
								id : id,
								title : title,
								icon : icon,
								fly : true
							});
						},
						failure : function(xhr) {
							Ext.app.analizeResponse({
								xhr : xhr,
								id : id,
								title : title,
								icon : icon
							});
						}
					});

				} else {
					Ext.app.tab.setActiveTab(id);
				}
			} catch (e) {
			}
		},
		/**
		 * Crea un ID aleatorio
		 */
		createId : function() {
			var now = new Date();
			return 't_' + parseInt(now.getTime());
		},
		/**
		 * Añade un TAB con un contenido HTML
		 * @param {Object} obj
		 */
		addTabJSONHTML : function(obj) {
			var id = obj.id != null ? obj.id : Ext.app.createId();
			var panel = Ext.app.formReport({
				title : obj.title,
				icon : obj.icon,
				id : id,
				html : obj.html
			});
			var n = Ext.app.tab.add(panel);

			Ext.app.tab.setActiveTab(n);
		},
		/**
		 * Añade un TAB con un contenido HTML de un archivo
		 * @param {Object} obj
		 */
		addTabJSONHTMLFILE : function(obj) {
			var id = obj.id != null ? obj.id : Ext.app.createId();
			var panel = Ext.app.formReport({
				title : obj.title,
				icon : obj.icon,
				id : id,
				src : obj.html_file
			});
			var n = Ext.app.tab.add(panel);

			Ext.app.tab.setActiveTab(n);
		},
		/**
		 * Añade un TAB con el origen de una
		 * @param {Object} obj
		 */
		addTabUrl : function(obj) {
			var id = obj.id != null ? obj.id : Ext.app.createId();
			var panel = Ext.app.formReport({
				title : obj.title,
				frame : false,
				icon : obj.icon,
				id : id,
				src : obj.url,
				print : obj.print,
				navigation : obj.navigation,
				'export' : obj['export']
			});
			var n = Ext.app.tab.add(panel);

			Ext.app.tab.setActiveTab(n);
		},
		/**
		 * Analiza la respuesta JSON
		 * @param {Object} obj
		 * @param {Object} config
		 */
		analizeJSONResponse : function(obj, config) {

			var id = (config != null && config['id'] != null) ? config['id'] : null;
			var title = (config != null && config['title'] != null) ? config['title'] : '';
			var icon = (config != null && config['icon'] != null) ? config['icon'] : null;
			var fly = (config != null && config['fly'] != null) ? config['fly'] : null;
			var nomsg = (config != null && config['nomsg'] != null) ? config['nomsg'] : false;

			var fnok = (config != null && config['fnok'] != null) ? config['fnok'] : null;
			var fnnok = (config != null && config['fnnok'] != null) ? config['fnnok'] : null;

			var text = '';
			var tipo = 'GENERAL';
			//console.dir(obj);
			try {
				var ok = false;
				// Hay un mensaje?
				if(obj.message != null) {
					tipo = 'JSON Mensaje';
					if(obj.success) {
						tipo = ('Es success = TRUE');
						if(fnok != null)
							fnok(obj);
						tipo = ('MOMSG: ' + nomsg);
						if(nomsg !== true) {
							if(fly)
								Ext.app.msgFly(title, obj.message, false, null, 'form-ok');
							else
								Ext.app.msgInfo(title, obj.message);
						}
					} else {
						tipo = ('Error');
						if(fnnok != null)
							fnnok(obj);
						Ext.app.msgError(title, obj.message);
					}
					ok = true;
				}

				// Hay un diálogo
				if(obj.dialog != null) {
					tipo = 'JSON Diálogo';
					if(obj.success) {
						if(fnok != null) {
							fnok(obj);
						}
						tipo = ('Es success = TRUE');
						Ext.app.msgFly(title, obj.dialog, true, 'icon-info', 'form-information');
					} else {
						if(fnnok != null)
							fnnok(obj);
						tipo = ('Error');
						Ext.app.msgError(title, obj.dialog);
					}
					ok = true;
				}

				// Hay un diálogo
				if(obj.lightbox != null) {
					tipo = 'JSON LIGHTBOX';
					if(obj.success) {
						tipo = ('Es success = TRUE');
						//tb_show(t, a, g);
						Ext.app.lightbox(title, obj.lightbox);
					} else {
						tipo = ('Error');
						Ext.app.msgError(title, obj.lightbox);
					}
					ok = true;
				}

				// Es un comando?
				if(obj.cmd != null) {
					tipo = ('Es un Comando');
					Ext.app.execCmd({
						id : obj.cmd
					});
					ok = true;
				}

				// Hay una acción JS?
				if(obj.js != null) {
					tipo = ('Es un código JS');
					text = obj.js;
					eval(obj.js);
					ok = true;
				}

				// Es un HTML?
				if(obj.html != null) {
					//Crea un TAB HTML
					tipo = ('Es un HTML');
					Ext.app.addTabJSONHTML(obj);
					ok = true;
				}

				// Es una redirección?
				if(obj.redirect != null) {
					Ext.app.askexit = false;
					document.location = obj.redirect;
					setTimeout(function() {
						Ext.app.askexit = true;
					}, 2);
					ok = true;
				}

				// Es un download?
				if(obj.download != null) {
					var msg = "<a href='" + obj.download + "' target='_blank'>" + _s('Descargar') + '</a>';
					Ext.app.msgFly('', msg);				
					ok = true;
				}

				// Es una URL?
				if(obj.url != null) {
					//Crea un TAB HTML
					tipo = ('Es un HTML');
					Ext.app.addTabUrl(obj);
					ok = true;
				}

				// Es un fichero HTML?
				if(obj.html_file != null) {
					//Crea un TAB HTML
					tipo = ('Es un HTML');
					Ext.app.addTabJSONHTMLFILE(obj);
					ok = true;
				}

				// Hay un Window
				if(obj.win != null) {
					tipo = ('Es un Window');
					id = (obj.id == null) ? id : obj.id
					text = obj.win;
					var newComponent = eval(obj.win);
					if (newComponent != null)
					{
						var i = Ext.app.tab.add(newComponent);
						Ext.app.tab.setActiveTab(i);
					}
					ok = true;
				}

				// Es un JSON sin mensaje?
				if(obj.success != null && obj.message == null && obj.dialog == null) {
					if(obj.success == true) {
						if(fnok != null)
							fnok(obj);
					} else {
						if(fnnok != null)
							fnnok(obj);
					}
					ok = true;
				}
				return ok;
			} catch (e) {
				//console.dir(obj);
				console.dir(e);
				if(Ext.app.tab != null) {
					Ext.app.tab.add({
						title : (title != null) ? title : _s('Error'),
						iconCls : (icon != null) ? icon : 'iconoErrorTab',
						id : id,
						closable : true,
						html : '<h1>ERROR ANALISIS JSON</h1>' + tipo + '<br/>' + e.description + '<br/>' + text
					});
					Ext.app.tab.setActiveTab(id);
					if(fnnok != null)
						fnnok(obj);
					if(fnok != null)
						fnok(obj);
				}
			}
		},
		/**
		 * Analiza la respuesta AJAX y realiza las acciones según el protocolo
		 * @param {Object} config
		 */
		analizeResponse : function(config) {
			var xhr = config['xhr'];
			var id = config['id'];
			var title = config['title'];
			var icon = config['icon'];
			var fly = config['fly'];
			var nomsg = config['nomsg'];

			var fnok = (config != null) ? config['fnok'] : null;
			var fnnok = (config != null) ? config['fnnok'] : null;
			if(xhr == null)
				return;

			var tipo = 'SIN ESPECIFICAR';
			try {
				if(xhr.status != 200) {
					if(xhr.status < 0) {
						if(nomsg != true)
							Ext.app.msgFly(xhr.status, xhr.statusText);
					} else if(xhr.status == 500) {
						if(Ext.app.tab != null) {
							Ext.app.tab.add({
								title : (title != null) ? title : _s('Error'),
								iconCls : (icon != null) ? icon : 'iconoErrorTab',
								id : id,
								closable : true,
								html : "<h1>ERROR 500</h1><br/>" + xhr.responseText
							});
							Ext.app.tab.setActiveTab(id);
						}
					}
					//Ext.app.msgError(xhr.status, xhr.statusText);
					if(fnnok != null)
						fnnok(obj);
					return;
				}

				var type = xhr.getResponseHeader('Content-type');
				if(type == 'application/json') {
					tipo = 'Creando JSON...';
					var obj = Ext.util.JSON.decode(xhr.responseText);
					tipo = 'Analizando JSON...';
					var ok = Ext.app.analizeJSONResponse(obj, config);
					if(!ok) {
						// Es HTML que genera un componente (por defecto)
						Ext.app.tab.add({
							title : (title != null) ? title : _s('Error'),
							iconCls : (icon != null) ? icon : 'iconoErrorTab',
							id : id,
							closable : true,
							html : xhr.responseText
						});
						Ext.app.tab.setActiveTab(id);
						if(fnnok != null)
							fnnok(obj);
					}
					/*else {
					 if (fnok != null)
					 fnok(obj);
					 }*/
					return;
				}
				// Por defecto se supone que es un tab que se añade
				else {
					tipo = 'Ejecutando JS';
					var newComponent = eval(xhr.responseText);
					Ext.app.tab.add(newComponent);
					Ext.app.tab.setActiveTab(id);
					if(fnnok != null)
						fnnok(obj);
				}
			} catch (e) {// alert(err);
				// tab.remove(id);
				//Ext.app.msgError(xhr.statusText, xhr.responseText);
				if(Ext.app.tab != null) {
					Ext.app.tab.add({
						title : (title != null) ? title : xhr.statusText,
						iconCls : (icon != null) ? icon : 'iconoErrorTab',
						id : id,
						closable : true,
						html : "</h1>ERROR " + tipo + '</h1>' + xhr.responseText
					});
					Ext.app.tab.setActiveTab(id);
					if(fnnok != null)
						fnnok(obj);
				}
			}
		},
		/**
		 * Muestra un mensaje de información genérico
		 */
		msgInfo : function(title, msg) {
			Ext.Msg.show({
				title : title,
				msg : msg,
				minWidth : 300,
				modal : true,
				icon : Ext.Msg.INFO,
				buttons : Ext.Msg.OK
			});
		},
		lightbox : function(title, msg) {
			Ext.Msg.show({
				title : title,
				msg : msg,
				minWidth : Ext.app.MESSAGELIGHTBOXWIDTH,
				modal : true,
				buttons : Ext.Msg.OK
			});
		},
		/**
		 * Muestra un mensaje de error
		 */
		msgError : function(title, msg) {
			Ext.Msg.show({
				title : title,
				msg : msg,
				minWidth : Ext.app.MESSAGEERRORWIDTH,
				modal : true,
				icon : Ext.Msg.ERROR,
				buttons : Ext.Msg.OK
			});
		},
		/**
		 * Muestra un mensaje flotante que desaparece al tiempo
		 */
		msgFly : function(title, msg, closable, icon, bg) {
			if(bg != null)
				msg = '<div class="' + bg + '">' + msg + '</div>';
			Ext.app.msgsWindow = new Ext.ux.window.MessageWindow({
				title : title,
				autoDestroy : true, //default = true
				autoHeight : true,
				autoHide : (closable != null) ? (!closable) : true, //default = true
				//baseCls: 'x-box',//defaults to 'x-window'
				//clip: 'bottom',//clips the bottom edge of the window border
				//bodyStyle: 'text-align:center',
				closable : true,
				hideFx : {
					delay : Ext.app.FLY_TIME,
					//duration: 0.25,
					mode : 'standard', //null,'standard','custom',or default ghost
					useProxy : false //default is false to hide window instead
				},
				hideAction : 'close',
				html : msg,
				iconCls : (icon != null) ? icon : 'icon-info',
				width : Ext.app.MESSAGEFLYWIDTH //optional (can also set minWidth which = 200 by default)
			}).show(Ext.getDoc());
			return;
		},
		/**
		 * Muestra un mensaje flotante que desaparece al tiempo
		 */
		msgFly2 : function(title, format, closable) {
			if(!msgCt) {
				msgCt = Ext.DomHelper.insertFirst(document.body, {
					id : 'msg-div'
				}, true);
			}
			msgCt.applyStyles('z-index: 999;');
			msgCt.alignTo(document, 't-t');
			var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
			if(closable) {
				title = '<div class="msg-box-close">&nbsp;</div>' + title;
			}
			var h = createBox(title, s);
			var m = Ext.DomHelper.append(msgCt, {
				html : h
			}, true);
			if(closable === true) {
				m.slideIn('b');
				m.on('click', function() {
					m.ghost("b", {
						remove : true
					});
				})
			} else {
				m.slideIn('t').pause(Ext.app.FLY_TIME).ghost("t", {
					remove : true
				});
			}
		},
		/**
		 * Muestra mensaje de espera
		 */
		msgWait : function(waitmessage) {
			Ext.MessageBox.show({
				msg : waitmessage,
				//progressText: 'Saving...',
				width : 300,
				wait : true,
				closable : true,
				waitConfig : {
					interval : 200
				},
				icon : 'icon-waiting'
				//animEl: 'mb7'
			});
		},
		/**
		 * Upload de archivos
		 */
		formUploadMedia : function(id, url, title, title_files, types, icon, fn) {
			var form_id = Ext.app.createId();

			var controls = [{
				xtype : 'hidden',
				id : form_id + '_files',
				value : '',
				name : 'file'
			}, {
				xtype : 'hidden',
				value : id,
				name : 'id'
			}, {
				xtype : 'awesomeuploader',
				gridHeight : 100,
				gridWidth : 440,
				height : 160,
				width : 460,
				supressPopups : true,
				//frame: true,
				flashSwfUploadPath : slash_item() + "/assets/js/ux/swfupload.swf",
				flashButtonSprite : slash_item() + "/assets/images/swfupload_browse_button_trans_56x22.png",
				flashUploadUrl : site_url('sys/upload/file'),
				standardUploadUrl : site_url('sys/upload/file'),
				xhrUploadUrl : site_url('sys/upload/file'),
				xhrFilePostName : 'file',
				flashUploadFilePostName : 'file',
				standardUploadFilePostName : 'file',
				flashSwfUploadFileTypes : types,
				flashSwfUploadFileTypesDescription : title_files,
				awesomeUploaderRoot : slash_item() + '/assets/images/',
				listeners : {
					scope : this,
					fileupload : function(uploader, success, result) {
						if(success) {
							var c = Ext.getCmp(form_id + '_files');
							var v = c.getValue();
							if(v != null && v != '')
								v += ';' + result.message;
							else
								v = result.message;
							c.setValue(v);
						}
					}
				}
			}];
			if(icon == null)
				icon = 'icon-documents';
			var form = Ext.app.formStandarForm({
				controls : controls,
				timeout : false,
				title : title,
				icon : icon,
				fn_ok : fn,
				url : url
			});

			form.show();
		},
		/**
		 * Envia los datos del formulario
		 * @param {Object} config
		 */
		sendForm : function(config) {
			var form = config['form'];
			var title = (config != null) ? config['title'] : null;
			var wait = (config['wait'] == null) ? true : config['wait'];
			var waitmessage = (config != null) ? config['waitmessage'] : null;
			var fnok = config['fnok'];
			var fnnok = config['fnnok'];
			var url = config['url'];
			var upload = config['upload'];
			var timeout = config['timeout'];
			var msgok = config['msgok'];
			var nomsg = (config['nomsg'] != null) ? config['nomsg'] : false;
			var fly = (config['fly'] != null) ? config['fly'] : true;
			if(url == null)
				url = form.url;
			if(waitmessage == null && wait == true)
				waitmessage = _s('sending-data');
			if(upload != true) {
				if(wait) {
					//Ext.app.msgWait(waitmessage);
					//form.hide();
				}
				//var p = form.getForm().getValues(false);
				var p = {};
				/*console.log(form.getForm().getValues(true));
				console.dir(form.getForm().getValues());
				console.dir(form.getForm().getFieldValues());*/
				parse_str(form.getForm().getValues(true), p);
				//console.dir(p);
				var fnok2 = function(o) {
					if(wait) {
						//Ext.MessageBox.hide();
						//form.show();
					}

					if(fnok != null)
						fnok(o)
				}
				var fnnok2 = function(o) {
					if(wait) {
						//Ext.MessageBox.hide();
						//form.show();
					}

					if(fnnok != null)
						fnnok(o)
				}
				Ext.app.callRemote({
					url : url,
					params : p,
					wait : wait,
					timeout : timeout,
					fly : fly,
					nomsg : nomsg,
					fnok : fnok2,
					fnnok : fnnok2
				});
			} else {
				if(msgok != null)
					Ext.app.msgInfo(title, msgok);
				if(fnok != null) {
					fnok();
				}
				form.getForm().submit({
					method : 'POST',
					url : url,
					timeout : timeout,
					clientValidation : true,
					success : function(form, action) {
						/*if (wait)
						Ext.MessageBox.hide();*/
						/*if (action != null && action.response != null) {
						Ext.app.analizeResponse({
						xhr: action.response,
						fnok: fnok,
						fnnok: fnnok,
						fly: fly,
						nomsg: nomsg,
						title: title
						});
						}
						else {*/
						/*if (msgok != null)
						Ext.app.msgInfo(title, msgok);
						if (fnok != null) {
						fnok();
						}*/
						//}
					//Ext.app.msgInfo(title, _s('envio_correcto'));
					},
					failure : function(form, action) {
						//return;
						if(wait)
							Ext.MessageBox.hide();
						try {
							switch (action.failureType) {
								case Ext.form.Action.CONNECT_FAILURE:
									Ext.app.msgError(title, _s('conexion_error'));
									break;
								case Ext.form.Action.SERVER_INVALID:
									Ext.app.msgError(title, action.result.message);
							}
						} catch (e) {

						}
						if(fnnok != null)
							fnnok();
					}
				});
			}
		},
		cacheRemote : new Array(),
		id_cache_storage: 'bp_storage_12345_',
		supports_html5_storage : function() {
			try {
				return 'localStorage' in window && window['localStorage'] !== null;
			} catch (e) {
				return false;
			}
		},
		clearStorage : function(key) {
			var st = this.supports_html5_storage();
			if(st) {
				var count = 0;
				try {
					if (key != null)
					{
						localStorage.removeItem(Ext.app.id_cache_storage + key);
						++count;
					}
					else
					{
						Ext.each(localStorage, function (value) {
							if (value.indexOf(Ext.app.id_cache_storage) == 0)
							{
								localStorage.removeItem(value);
								++count;
							}
						});
					}
				} catch(e) {
					//console.dir(e);
				}
			}
			return count;
		},
		setStorage : function(key, value) {
			var st = this.supports_html5_storage();
			if(st) {
				try {
					value = JSON.stringify(value)
					var old = localStorage.getItem(Ext.app.id_cache_storage + key);
					//value = serialize(value)
					localStorage.setItem(Ext.app.id_cache_storage + key, value);
					return;
				} catch(e) {
					this.setCache(key, value);
					return;
				}
			}
			this.cacheRemote[key] = value;
		},
		getStorage : function(key) {
			var st = this.supports_html5_storage();
			if(st) {
				try {
					var b = localStorage.getItem(Ext.app.id_cache_storage + key);
					if(b != null) {
						b = JSON.parse(b);
						//b = unserialize(b);
						return b;
					}
				} catch(e) {
					return this.getCache(key);
				}
			}
			return this.getCache(key);
		},
		setCache : function(key, value) {
			this.cacheRemote[key] = value;
		},
		getCache : function(key) {
			return this.cacheRemote[key];
		},
		/**
		 * Realiza una llamada AJAX al servidor y muestra un mensaje con la
		 * respuesta
		 */
		callRemote : function(config) {
			var url = (config != null) ? config['url'] : null;
			var title = (config != null) ? config['title'] : null;
			var waitmessage = (config != null) ? config['waitmessage'] : _s('sending-data');
			var params = (config['params'] != null) ? config['params'] : null;
			var wait = (config['wait'] != null) ? config['wait'] : null;
			var fnok = (config['fnok'] != null) ? config['fnok'] : null;
			var fnnok = (config['fnok'] != null) ? config['fnnok'] : null;
			var nomsg = (config['nomsg'] != null) ? config['nomsg'] : false;
			var fly = (config['fly'] != null) ? config['fly'] : true;
			var cache = (config['cache'] != null) ? config['cache'] : false;
			var storage = (config['storage'] != null) ? config['storage'] : false;
			var me = this;
			var timeout = (config['timeout'] != null) ? config['timeout'] : parseInt(Ext.app.TIMEOUTREMOTECALL);
			if(timeout === false) {
				timeout = Ext.app.TIMEOUTREMOTECALLMAX;
			}

			if(waitmessage == null && wait == true)
				waitmessage = _s('sending-data');

			if(params != null) {
				var params2 = {};
				jQuery.each(params, function(index, item) {
					params2[index] = (item != null) ? item : '';
				});
				params = params2;
			} else {
				params = {};
			}
			/*
			 if (Ext.app.SESSION_ID != '') {
			 params[Ext.app.SESSION_NAME] = Ext.app.SESSION_ID;
			 }*/
			if(cache) {

				var id_cache = serialize(url) + '_' + serialize(params);

				var res = me.getCache(id_cache);
				if(res != null) {
					Ext.app.analizeResponse({
						xhr : res,
						fly : fly,
						fnok : fnok,
						fnnok : fnnok,
						title : title,
						nomsg : nomsg
					});
					return;
				}
			}
			try {
				var call = url.replace(site_url(), '');
				if(!nomsg) {
					Ext.app.request++;
					jQuery(document.body).loading({
						align : Ext.app.LOADINGMASKALIGN,
						processData : false,
						traditional : true,
						text : _s('Conectando') + ' (' + Ext.app.request + ') - ' + call,
						effect : Ext.app.LOADINGMASKEFFECT,
						mask : false
					});
				}
				jQuery.ajax({
					url : url,
					data : params,
					timeout : timeout,
					type : 'POST',
					complete : function(o, result) {
						if(!nomsg) {
							Ext.app.request--;
							if(Ext.app.request <= 0) {
								jQuery(document.body).loading(false);
								Ext.app.request = 0;
							} else
								jQuery(document.body).loading({
									align : Ext.app.LOADINGMASKALIGN,
									text : _s('Conectando') + ' ' + Ext.app.request,
									effect : Ext.app.LOADINGMASKEFFECT,
									mask : false
								});
						}
						if(result == 'success') {
							var res = o;
							if(cache) {
								me.setCache(id_cache, res);
							}
							Ext.app.analizeResponse({
								xhr : res,
								fly : fly,
								fnok : fnok,
								fnnok : fnnok,
								title : title,
								nomsg : nomsg
							});
						} else {
							if(result == 'error') {
								var text = _s('Error') + '<b> ' + url + '</b>';
								if(o.status == 500) {
									text += '<br/>' + o.statusText + '<br/>' + o.responseText;
								}

								if(!nomsg)
									Ext.app.msgError(title, text);
							}
							if(result == 'timeout') {
								if(!nomsg)
									Ext.app.msgError(title, 'Timeout ' + url);
							}
							if(fnnok != null)
								fnnok();
						}
					}
				});
			} catch (e) {
				jQuery(document.body).loading(false);
				Ext.app.request--;
			}
		},
		/**
		 * Crea una colunma del tipo check
		 */
		checkColumn : function(dataIndex, header) {
			return new Ext.ux.grid.CheckColumn({
				header : header,
				dataIndex : dataIndex,
				width : Ext.app.TAM_COLUMN_BOOL,
				editor : new Ext.form.Checkbox(),
				sortable : true
			});
		},
		/**
		 * Realiza una pregunta y en caso de ser aceptada realiza una llamada
		 * remota
		 */
		callRemoteAsk : function(config) {
			var title = (config != null) ? config['title'] : null;
			var askmessage = (config != null) ? config['askmessage'] : null;

			Ext.Msg.show({
				title : title,
				buttons : Ext.MessageBox.YESNOCANCEL,
				icon : (config['icon'] == null)?Ext.Msg.QUESTION:config['icon'],
				msg : askmessage,
				fn : function(btn, text) {
					if(btn == 'yes') {
						Ext.app.callRemote(config);
					}
				}
			});
		},
		/**
		 * Actualiza el contenido del iframe de un formulario de Report
		 */
		reportPanelUpdate : function(config) {
			try {
				var id = (config != null) ? config['id'] : null;
				var url = (config != null) ? config['url'] : null;
				var params = (config != null) ? config['params'] : null;
				var panel = Ext.getCmp(id + "details-panel");
				var este = Ext.getCmp(id);
				//este.disable();
				este.getEl().mask(Ext.app.TEXT_CARGANDO);
				panel.on('documentloaded', function() {
					este.getEl().unmask();
					panel.un('documentloaded');
				});
				panel.load({
					url : url,
					params : params,
					loadMask : true,
					timeout : 300000,
					text : Ext.app.TEXT_CARGANDO,
					scripts : true
				});
			} catch (e) {
			}

		},
		/**
		 * Limpia los campos de un array
		 */
		clearFields : function(fields) {
			for(var i = 0; fields.length; i++) {
				var ctl = Ext.getCmp(fields[i].id);
				try {
					ctl.reset();
				} catch (e) {
				}
			}
		},
		/**
		 * Crea un formulario para mostrar un Report
		 */
		formReport : function(config) {

			// Carga la configuracion
			var title = (config != null) ? config['title'] : null;
			var id = (config != null) ? config['id'] : null;
			var icon = (config != null) ? config['icon'] : null;
			var filter = (config != null) ? config['filter'] : null;
			var action = (config != null) ? config['action'] : null;
			var loadstores = (config != null) ? config['stores'] : null;
			var create = (config != null) ? config['create'] : true;
			var html = (config != null) ? config['html'] : null;
			var src = (config != null) ? config['src'] : site_url('sys/app/blank');
			var print = (config['print'] != null) ? config['print'] : true;
			var navigation = (config['navigation'] != null) ? config['navigation'] : true;
			var _export = (config['export'] != null) ? config['export'] : true;
			var menu = config['menu'];
			var frame = config['frame'];
			if(menu == null)
				menu = true;
			if(html != null)
				src = null;

			var page = new Ext.ux.ManagedIFrame.Panel({
				region : 'center',
				xtype : 'iframepanel',
				showLoadIndicator : true,
				listeners : {
					documentloaded : function() {
					}
				},
				html : html,
				defaultSrc : src,
				autoCreate : {
					id : id + 'details-panel'
				},
				frameConfig : {
					autoCreate : {
						id : id + 'details-panel2'
					}
				}
			});

			// Barra de comandos
			var bbar = ['-', {
				tooltip : _s('cmd-calcular'),
				text : _s('Calcular'),
				iconCls : 'icon-run',
				listeners : {
					click : action
				}
			}, '-', {
				text : _s('cmd-limpiar'),
				tooltip : _s('cmd-limpiar'),
				iconCls : 'icon-clean',
				id : id + '_btnnew',
				handler : function(f) {
					Ext.app.clearFields(filter);
				}
			}]

			//if (frame !== true) {
			var bbar2 = [];
			if(print || _export)
				bbar2[bbar2.length] = '->';

			if(_export)
				bbar2[bbar2.length] = Ext.app.menuExport(page, false, title);
			if(print)
				bbar2[bbar2.length] = {
					tooltip : _s('cmd-print'),
					iconCls : 'icon-print',
					id : id + '_btnprint',
					handler : function() {
						Ext.app.printIframe(page.id);
					}
				}
			if(filter != null) {
				filter = filter.concat(bbar);
			} else if(navigation && src) {
				filter = [{
					tooltip : _s('Inicio'),
					iconCls : 'icon-home',
					listeners : {
						click : function(b) {
							page.setSrc(src);
						}
					}
				}, '-', {
					tooltip : _s('Ir a la página anterior'),
					iconCls : 'icon-back',
					handler : function(b) {
						var o = page.getFrameWindow();
					o.history.back();
					}
				}, {
					tooltip : _s('Ir a la página siguiente'),
					iconCls : 'icon-forward',
					handler : function(b) {
						var o = page.getFrameWindow();
					o.history.forward();
					}
				}];
			}

			if(bbar2.length > 0)
				filter = (filter != null) ? filter.concat(bbar2) : bbar2;

			// Define el panel
			var panel = {
				title : title,
				id : id,
				region : 'center',
				closable : true,
				iconCls : icon,
				layout : 'border',

				items : [page],

				tbar : filter,
				bbar : null
			};

			if(loadstores != null)
				Ext.app.loadStores(loadstores);

			if(create)
				return new Ext.Panel(panel)
			return panel;

		},
		/**
		 * Crea el formulario Login
		 */
		formLogin : function(fn, url, reload, title) {
			if(reload == null)
				reload = true;
			var login = new Ext.FormPanel({
				monitorValid : true,
				labelWidth : 80,
				layout : 'form',
				border : false,
				cls : 'loginform',
				bodyStyle : 'background:transparent;padding:2px',
				url : site_url('user/auth/login'),
				defaultType : 'textfield',
				items : [{
					fieldLabel : _s('Username'),
					name : 'username',
					allowBlank : false
				}, {
					fieldLabel : _s('Password'),
					name : 'password',
					inputType : 'password',
					allowBlank : false
				}/*,                                              {
				 fieldLabel: Ext.lang.server,
				 name: 'hostname',
				 value: Ext.app.HOSTNAME,
				 allowBlank: true
				 }, {
				 fieldLabel: Ext.lang.database,
				 name: 'database',
				 value: Ext.app.DATABASE,
				 allowBlank: true
				 }*/
				,{
					name : 'url',
					inputType : 'hidden',
					value : url
				}, {
					name : 'q-reload',
					inputType : 'hidden',
					value : reload
				}]
			});

			var winlogin = new Ext.Window({
				title : (title != null) ? title : Ext.app.APLICATION_TITLE,
				autoHeight : true,
				bodyStyle : 'padding: 10px 10px 0 10px;',
				width : 350,
				height : 300,
				closeAction : 'close',
				border : false,
				resizable : false,
				plain : true,
				modal : true,
				iconCls : 'icon-login',
				items : [login],
				buttons : [{
					text : _s('Login'),
					formBind : true,
					handler : function() {
						Ext.app.sendForm({
							title : site_url('user/auth/login'),
							form : login,
							url : site_url('user/auth/login'),
							fnok : function(obj) {
								winlogin.hide();
								if(fn != null)
									fn(true, obj.redirect)
							},
							fnnok : function(obj) {
								login.getForm().reset();
							}
						});
					}
				}]
			});
			winlogin.on('close', function() {
				if(fn != null)
					fn(false)
			})
			winlogin.show();
		},
		/**
		 * Crea un STORE o reutiliza uno ya creado
		 * @param {Object} url
		 * @param {Object} fields
		 * @param {Object} autoload
		 * @param {Object} unique
		 */
		getStore : function(url, fields, autoload, unique) {
			var id = url + fields.join('') + autoload;
			var store = Ext.app.hashStores[id];
			if(store == null || unique === true) {
				store = new Ext.data.JsonStore({
					autoLoad : (autoload != null) ? autoload : false,
					url : url,
					root : 'value_data',
					fields : fields
				});
				store.on('exception', function(e, type, a, o, r) {
					Ext.app.analizeResponse({
						xhr : r
					});
				});
				if(unique === false)
					Ext.app.hashStores[id] = store;
				return store
			}
			return store
		},
		autocomplete2 : function(config) {
			var store = Ext.app.getStore(config['url'], ['id', 'text'], false, true);
			store.baseParams = {
				start : 0,
				limit : Ext.app.AUTOCOMPLETELISTSIZE
			}
			config['xtype'] = 'superboxselect';
			config['emptyText'] = _s('Seleccione');
			config['resizable'] = true;
			config['minChars'] = 2;
			config['store'] = store;
			config['mode'] = 'remote';
			config['displayField'] = 'text';
			config['displayFieldTpl'] = '{text}';
			config['valueField'] = 'id';
			config['stackItems'] = 1;
			config['multiSelectMode'] = false;
			config['queryDelay'] = 0;
			config['triggerAction'] = 'all';
			config['single'] = true;

			return (config['create'] === true) ? new Ext.ux.form.SuperBoxSelect(config) : config;
		},
		/**
		 * Crea un COMBO de autocomplete con llamada AJAX
		 * @param {Object} params
		 */
		autocomplete : function(params) {
			var store = Ext.app.getStore(params['url'], ['id', 'text'], params['autoload'], true);
			store.baseParams = {
				start : 0,
				limit : Ext.app.AUTOCOMPLETELISTSIZE
			};

			var my_select = function(f, id, text) {
				try {
					//f.selectText();
					f.setValue(id);					
					if(params['fnselect']) {
						params['fnselect'](id);
					}
				} catch (e) {
				}
			}
			var combo = {
				xtype : 'combo',
				store : store,
				name : params['name'],
				width : params['width'],
				id : params['name'],
				displayField : 'text',
				valueField : 'id',
				typeAhead : true,
				anchor : '90%',
				minChars : 100,
				selectOnFocus : true,
				cls : 'searchcontrol',
				allowBlank : true,
				forceSelection : true,
				hiddenName : (params['hiddenName'] != null) ? params['hiddenName'] : params['name'],
				fieldLabel : (params['fieldLabel'] != null) ? params['fieldLabel'] : params['label'],
				loadingText : _s('Cargando'),
				//hideTrigger : true,
				enableKeyEvents : true,
				doQueryEx : function(event) {
					var me = this;
					this.focus();
					var q = this.getRawValue();
					console.log('combo buscando ' + q);
					try {
						this.clearValue();
						this.setValue(q);
						this.getStore().removeAll();
						this.el.addClass('loadingcontrol');
						this.getStore().load({
							params : {
								query : q,
								start : 0,
								limit : Ext.app.AUTOCOMPLETELISTSIZE
							},
							callback : function() {
								//console.log('stores load');
								me.el.removeClass('loadingcontrol');
								var fn = function(combo, data) {
									//console.log('en query load');
									me.getStore().baseParams  = {
										query : q,
										start : 0,
										limit : Ext.app.AUTOCOMPLETELISTSIZE
									};
									me.suspendEvents(false);
									me.setValue(data.id);
									my_select(me, parseInt(data.id), data.text);
									me.store.removeAll();
									me.resumeEvents();
									me.collapse();
									//console.log('final');
								};
								try {
									if(parseInt(me.store.getTotalCount()) == 1) {
										//doQuery();
										//
										var v = me.store.getAt(0);
										if(v != null)
											fn(me, v.data);
									}
								} catch (e) {
								}
							}
						});
						//f.doQuery(q, true);
					} catch (e) {
					}
				},
				listeners : {
					keypress : function(f, e) {
						if(e.getKey() == 13) {	
							console.log('RAW: ' + this.getRawValue());
							console.log('V: ' + this.getValue());
							console.log('EXP: ' + this.isExpanded());
							f.doQueryEx(e);
						}
					},
					select : function(f, r, i) {
						console.log('En select');
						my_select(f, r.data.id, r.data.text);
					}
				}
			};

			store.on('load', function(r, o) {
				var combo = Ext.getCmp(params['name']);
				if(combo != null) {
					var fn = function(combo, data) {
						combo.suspendEvents(false);
						combo.setValue(data.id);
						my_select(combo, parseInt(data.id), data.text);
						store.removeAll();
						combo.resumeEvents();
						combo.collapse();
					};
					try {
						if(parseInt(combo.store.getTotalCount()) == 1) {
							//doQuery();
							fn(combo, combo.store.getAt(0).data);
						}
					} catch (e) {
					}
				}
			});
			return combo;
		},
		autocomplete4 : function(params) {
			var combo = Ext.apply(params, {
				xtype : 'searchcombofield',
				id : params['name']
			});

			return combo;
		},
		/**
		 * Añade un elemento al combo
		 * @param {Object} store
		 */
		comboAddNew : function(store) {
			Ext.app.comboAdd(store, null, _s('new-item-combo'));
		},
		/**
		 * Añade un elemento al combo
		 * @param {Object} store
		 */
		comboAdd : function(store, id, text, text2) {
			try {
				ComboRecord = Ext.data.Record.create({
					name : 'id'
				}, {
					name : 'text'
				}, {
					name : 'text2'
				});
				store.add(new ComboRecord({
					'id' : id,
					'text' : text,
					'text2' : text2
				}));
			} catch (e) {
			}

		},
		/**
		 * Combo de idiomas
		 * @param {Object} lang
		 * @param {Object} name
		 */
		comboLangs : function(lang, name) {
			var langs = Ext.app.REPORTS_LANG;
			langs = (langs != '') ? langs.split(';') : null;
			var langs2 = [];
			langs2[langs2.length] = [null, _s('new-item-combo')];
			Ext.each(langs, function(lang) {
				langs2[langs2.length] = [lang, _s('language_' + lang), 'lang-' + lang]
			});
			return new Ext.ux.IconCombo({
				store : new Ext.data.SimpleStore({
					fields : ['id', 'text', 'icon'],
					data : langs2
				}),
				value : (lang == null) ? langs[0] : lang,
				valueField : 'id',
				displayField : 'text',
				iconClsField : 'icon',
				triggerAction : 'all',
				mode : 'local',
				name : (name == null) ? 'lang' : name,
				hiddenName : 'lang',
				fieldLabel : _s('Idioma'),
				width : 160
			});
		},
		/**
		 * Muestra el formulario de selección de reports y lo ejecuta
		 * @param {Object} config
		 */
		formSelectReport : function(config) {
			var id = config['id'];
			var action = config['action'];
			var list = config['list'];
			/*var langs = Ext.app.REPORTS_LANG;
			 langs = (langs != '') ? langs.split(';') : null;
			 var langs2 = [];
			 Ext.each(langs, function(lang){
			 langs2[langs2.length] = [lang, _s('language_' + lang), 'lang-' + lang]
			 });*/
			var formatos = new Ext.form.ComboBox(Ext.app.combobox({
				url : list,
				name : 'report',
				anchor : "90%",
				allowBlank : false,
				label : _s('Formato')
			}));

			var id_r = Ext.app.createId();
			var controls = [formatos, Ext.app.comboLangs()/*new Ext.ux.IconCombo({
			 store: new Ext.data.SimpleStore({
			 fields: ['id',


			 'text', 'icon'],
			 data: langs2
			 }),
			 value: langs[0],
			 valueField: 'id',
			 displayField: 'text',
			 iconClsField: 'icon',
			 triggerAction: 'all',
			 mode: 'local',
			 name: 'lang',
			 hiddenName: 'lang',
			 fieldLabel: _s('Idioma'),
			 width: 160
			 })*/
			,{
				xtype : 'hidden',
				value : id,
				name : 'id'
			}];

			var form = Ext.app.formStandarForm({
				controls : controls,
				title : _s('formato-select'),
				timeout : false,
				url : action
			});
			formatos.store.load({
				callback : function(r) {
					if(r.length == 1) {
						var report = r[0].data.id;
						Ext.app.callRemote({
							params : {
								id : id,
								report : report
							},
							url : action
						});
					} else {
						form.show();
					}
				}
			});
		},
		/**
		 * Muestra el formulario de selección de reports y lo ejecuta
		 * @param {Object} config
		 */
		formSelectReport2 : function(config) {
			try {
				var id = config['id'];
				var action = config['action'];
				var list = config['list'];
				var title = config['title'];
				var lang = config['lang'];

				// simple array store
				var store = new Ext.data.ArrayStore({
					fields : ['id', 'text'],
					data : list
				});
				var formatos = new Ext.form.ComboBox({
					displayField : 'text',
					valueField : 'id',
					store : list,
					mode : 'local',
					//name: 'report',
					hiddenName : 'report',
					anchor : "90%",
					typeAhead : true,
					triggerAction : 'all',
					forceSelection : true,
					emptyText : _s('Seleccione'),
					selectOnFocus : true,
					allowBlank : false,
					fieldLabel : _s('Formato')
				});

				formatos.setValue(formatos.getStore().getAt(0).data.id);
				var controls = [formatos, {
					xtype : 'hidden',
					value : id,
					name : 'id'
				}, {
					xtype : 'hidden',
					value : title,
					name : 'title'
				}, {
					xtype : 'hidden',
					value : lang,
					name : 'lang'
				}];

				var form = Ext.app.formStandarForm({
					controls : controls,
					title : _s('formato-select'),
					timeout : false,
					url : action
				});
				form.show();
			} catch (e) {
			}
		},
		/**
		 * Crea un combo normal con llamada AJAX
		 * @param {Object} config
		 */
		combobox : function(config) {
			//var name = null;
			var on_select = config['on_select'];
			var id = config['id'];
			var renderTo = config['renderTo'];
			var fieldLabel = (config['fieldLabel'] != null) ? config['fieldLabel'] : config['label'];
			var autoload = config['autoload'];
			var value = config['value'];
			var allowBlank = config['allowBlank'];
			var name = config['name'];
			var url = config['url'];
			var width = config['width'];
			var anchor = config['anchor'];
			var noselect = config['noselect'];
			var disabled = config['disabled'];
			var field = config['field'];
			var extrafields = config['extrafields'];
			if(field == null)
				field = 'text'
			var value = config['value'];
			if(value == null)
				value = 'id'
			if(extrafields != null) {
				extrafields[extrafields.length] = value;
				extrafields[extrafields.length] = field;
			} else {
				extrafields = [value, field];
			}

			if(name == null) {
				name = '_' + id;
			}
			if(id == null) {
				id = Ext.app.createId() + '_' + name + '_combo_' + field + '_' + value;
			}
			var store = Ext.app.getStore(url, extrafields, autoload);

			var combo = Ext.apply(config, {
				mode : 'local',
				xtype : 'combo',
				fieldLabel : fieldLabel,
				displayField : field,
				renderTo : renderTo,
				id : id,
				disabled : disabled,
				hiddenName : name,
				width : width,
				valueField : value,
				forceSelection : true,
				loadingText : _s('Cargando'),
				anchor : anchor,
				emptyText : _s('Seleccione'),
				typeAhead : true,
				enableKeyEvents : true,
				allowBlank : (allowBlank != null) ? allowBlank : true,
				triggerAction : 'all',
				selectOnFocus : true,
				listClass : 'x-combo-list-small',
				store : store,
				listeners : {
					select : {
						fn : function(c, r, i) {
							if(on_select != null)
								on_select(c, r, i);
						}
					}
				}
			});
			//combo = combo, config);

			// Añade sin selección
			if(noselect !== false) {
				store.on('beforeload', function(s, o) {
					o.add = true;
					Ext.app.comboAddNew(s);
					var combo = Ext.getCmp(id);

					if(combo != null) {
						combo.setValue(s.getAt(0).data[value]);
					}
				});
			}
			return combo;
		},
		/**
		 * Crea un grid para mostrar los templates
		 * @param {Object} type
		 * @param {Object} collapsible
		 * @param {Object} collapsed
		 * @param {Object} region
		 * @param {Object} height
		 * @param {Object} minsize
		 * @param {Object} fnselect
		 * @param {Object} fnget
		 */
		formTemplates : function(config) {
			var type = config['type'];
			var collapsible = config['collapsible'];
			var collapsed = config['collapsed'];
			var region = config['region'];
			var height = config['height'];
			var minsize = config['minsize'];
			var fnselect = config['fnselect'];
			var fnget = config['fnget'];

			var store = new Ext.data.Store({
				remoteSort : true,
				autoload : true,

				proxy : new Ext.data.HttpProxy({
					url : site_url('generico/template/get_list/')
				}),
				reader : new Ext.data.JsonReader({
					root : 'value_data',
					totalProperty : 'total_data',
					idProperty : 'id',
					remoteSort : true,
					autoload : true
				}, [{
					name : 'id',
					type : 'int'
				}, {
					name : 'nIdPlantilla',
					type : 'int'
				}, {
					name : 'cDescripcion'
				}, {
					name : 'tTexto'
				}, {
					name : 'cTipo'
				}, {
					name : 'bIsHTML',
					type : 'bool'
				}, {
					name : 'dCreacion',
					type : 'int'
				}, {
					name : 'cCUser'
				}, {
					name : 'dAct',
					type : 'int'
				}, {
					name : 'cAUser'
				}])
			});

			var save = function() {
				var nombre = '';
				Ext.MessageBox.prompt(_s('Plantilla'), _s('Nombre'), function(btn, text) {
					if(btn == 'ok') {

						var msg = fnget();
						var url = site_url('generico/template/add/');
						Ext.app.callRemote({
							url : url,
							title : _s('Plantillas'),
							waitmessage : _s('Creando Plantilla'),
							errormessage : _s('registro_error'),
							params : {
								tTexto : msg,
								cDescripcion : text,
								cTipo : type
							},
							fnok : function() {
								store.load({
									params : {
										start : 0,
										limit : parseInt(Ext.app.PAGESIZE),
										where : "cTipo=" + type
									}
								});
							}
						});
					}
				});
			};
			var guardar = new Ext.Button({
				text : _s('Guardar como plantilla'),
				iconCls : 'icon-save',
				handler : save
			});
			var id = Ext.app.createId();

			var grid = new Ext.grid.GridPanel({
				title : _s('Plantillas'),
				titleCollapse : _s('Plantillas'),
				autoExpandColumn : "descripcion",
				collapsible : collapsible,
				collapsed : collapsed,
				region : region,
				height : height,
				minSize : minsize,
				iconCls : 'icon-template',
				split : true,
				id : id,
				xtype : 'grid',
				store : store,
				stripeRows : true,
				sm : new Ext.grid.RowSelectionModel({
					singleSelect : true
				}),
				loadMask : true,

				columns : [{
					header : _s('Id'),
					dataIndex : 'nIdPlantilla',
					width : Ext.app.TAM_COLUMN_ID,
					align : 'right',
					sortable : true
				}, {
					header : _s('Descripcion'),
					dataIndex : 'cDescripcion',
					width : Ext.app.TAM_COLUMN_TEXT,
					sortable : true
				}, {
					id : 'texto',
					header : _s('Texto'),
					dataIndex : 'tTexto',
					id : 'descripcion',
					width : Ext.app.TAM_COLUMN_TEXT,
					sortable : false
				}, {
					header : _s('Autor'),
					dataIndex : 'cCUser',
					width : Ext.app.TAM_COLUMN_TEXT,
					sortable : true
				}, {
					header : _s('Fecha'),
					dataIndex : 'dCreacion',
					width : Ext.app.TAM_COLUMN_DATE,
					dateFormat : 'timestamp',
					renderer : Ext.app.renderDate,
					sortable : true
				}, {
					header : _s('cAUser'),
					dataIndex : 'cAUser',
					width : Ext.app.TAM_COLUMN_TEXT,
					sortable : true
				}, {
					header : _s('dAct'),
					dataIndex : 'dAct',
					width : Ext.app.TAM_COLUMN_DATE,
					renderer : Ext.app.renderDate,
					sortable : true
				}],
				bbar : Ext.app.gridBottom(store, true),
				tbar : Ext.app.gridStandarButtons({
					id : id,
					title : _s('Plantillas'),
					bar : [guardar, {
						text : _s('Eliminar'),
						iconCls : 'icon-delete',
						handler : function(sm, rowIdx, e) {
							var sm = grid.getSelectionModel();
							if(sm.hasSelection()) {
								var sel = sm.getSelected();
								Ext.app.callRemoteAsk({
									url : site_url('generico/template/del/'),
									title : _s('Plantillas'),
									askmessage : _s('elm-registro'),
									waitmessage : _s('elm-plantilla'),
									errormessage : _s('registro_error'),
									params : {
										id : sel.data.nIdPlantilla
									},
									fnok : function() {
										grid.getStore().remove(sel);
									}
								});
							}
						}
					}]
				})
			});

			if(!fnget) {
				guardar.disable();
			}

			var url = store.url + type;
			store.baseParams = {
				start : 0,
				limit : parseInt(Ext.app.PAGESIZE),
				where : "cTipo=" + type
			};
			store.load({
				url : url,
				params : {
					start : 0,
					limit : parseInt(Ext.app.PAGESIZE),
					where : "cTipo=" + type
				}
			});

			if(fnselect) {
				grid.on('rowdblclick', function(sm, rowIdx, e) {
					var sm = grid.getSelectionModel();
					if(sm.hasSelection()) {
						var sel = sm.getSelected();
						fnselect(sel.data.tTexto);
					}
				});
			}

			return grid;
		},		
		/**
		 * Dado un listado de grids, aplica enable/disable
		 */
		formEnableList : function(config) {
			Ext.each(config['list'], function(e, i) {
				var g = Ext.getCmp(e);
				if(g != null) {(config['enable']) ? g.enable() : g.disable();
				}
			});
		},
		/**
		 * Dado un listado de grids, los resetea
		 */
		formResetList : function(config) {
			try {
				Ext.each(config['list'], function(e) {
					var g = Ext.getCmp(e);
					if(g != null) {
						var st = g.store;
						if(st != null) {
							st.removeAll();
						}
					}

				});
				Ext.each(config['list'], function(e) {
					var g = Ext.getCmp(e);
					if(g != null) {
						var st = g.store;
						if(st != null) {
							st.baseParams = config['params'];
						}
					}
				});
			} catch (e) {
			}
		},
		/**
		 * Dado un listado de grids, da la orden de carga
		 */
		formLoadList : function(config) {
			try {
				Ext.each(config['list'], function(e) {
					var g = Ext.getCmp(e);
					if(g != null) {
						var st = g.store;
						if(st != null) {
							st.baseParams = config['params'];
							if(config['nolimits'] !== true) {
								st.baseParams['start'] = 0;
								st.baseParams['limit'] = parseInt(Ext.app.PAGESIZE);
							}

							st.load({
								waitMsg : _s('Cargando'),
								callback : function() {
									g.doLayout();
								}
							});
						}
					}
				});
			} catch (e) {
			}
		},
		/**
		 * Crea un store
		 */
		createStore : function(config) {

			// Parámetros
			var model = config['model'];
			var url = config['url'];
			var id = config['id'];
			var params = config['params'];
			var groupField = config['groupField'];
			var sortInfo = config['sortInfo'];
			var remotesort = config['remotesort'];
			var sort = config['sort'];
			var dir = config['dir'];
			var timeout = config['timeout'];
			if(timeout === false) {
				timeout = Ext.app.TIMEOUTREMOTECALLMAX;
			}
			var fields = [];

			// Crea los campos
			for(var i = 0; i < model.length; i++) {
				var d = model[i];
				var field = [];
				if(d.extras != null) {
					field = d.extras;
				}
				field['name'] = d.name;
				if(d.type != null)
					field['type'] = d.type;
				if(d.mapping != null)
					field['mapping'] = d.mapping;
				else
					field['mapping'] = d.name
				fields.push(field);
			}

			// Crea los datos del store
			var config = [];
			config['remoteSort'] = (remotesort != null) ? remotesort : true;
			if(groupField != null) {
				config['groupField'] = groupField;
				config['sortInfo'] = {
					field : sortInfo,
					direction : 'ASC'
				};
			}
			config['autoload'] = true;

			// Crea el prozy
			config['proxy'] = new Ext.data.HttpProxy({
				url : url
			});
			if(timeout != null)
				config['proxy'].conn = {
					timeout : timeout
				}
			config['reader'] = new Ext.data.JsonReader({
				root : 'value_data',
				totalProperty : 'total_data',
				idProperty : id
			}, fields);

			// Crea el store
			var store = (groupField != null) ? new Ext.data.GroupingStore(config) : new Ext.data.Store(config);

			store.on('exception', function(e, type, a, o, r) {
				Ext.app.analizeResponse({
					xhr : r
				});
			});
			// Orden
			if(sort != null) {
				if(dir == null)
					dir = 'asc';
				store.setDefaultSort(sort, dir);
			}
			if(config['pagesize'] != null) {
				store.baseParams = {
					start : 0,
					limit : config['pagesize']
				}
			}

			return store;
		},
		/**
		 * Crea las columnas de un grid a partir de los datos de un array
		 */
		createColumnsGrid : function(data) {
			var fields = [];

			for(var i = 0; i < data.length; i++) {
				var d = data[i];
				if(d.column != null) {
					var field = d.column;
					field['dataIndex'] = d.name;
					if(d.ro === true) {
						field['editor'] = false;
					}

					fields.push(field);
				}
			}
			return fields;
		},
		/**
		 * Crea los campos de un formulario para actualizar un GRID
		 */
		createAddFields : function(data) {
			var fields = [];
			try {
				for(var i = 0; i < data.length; i++) {
					var d = data[i];
					if((d.add != null) && (d.ro === false)) {
						var field = d.add;
						if(d.column != null)
							if(d.column.header != null)
								field['fieldLabel'] = d.column.header;
						if(field['fieldLabel'] == null)
							field['fieldLabel'] = _s(d.name);
						field['name'] = d.name;
						fields.push(field);
					}
				}

				return fields;
			} catch (e) {
			}
		},
		/**
		 * Crea un formulario estándar con un GRID
		 */
		createFormGrid : function(config) {

			var model = config['model'];
			var id = config['id'];
			/*if (id == null)
			 id = 'id';*/
			var title = config['title'];
			var icon = config['icon'];
			var idfield = config['idfield'];
			var urlget = config['urlget'];
			var urladd = config['urladd'];
			var urlupd = config['urlupd'];
			var urldel = config['urldel'];
			var plugins = config['plugins'];
			var loadstores = config['loadstores'];
			var groupField = config['groupField'];
			var sortInfo = config['sortInfo'];
			var fn_pre = config['fn_pre'];
			var fn_add = config['fn_add'];
			var load = config['load'];
			var mode = config['mode'];
			var fn_open = config['fn_open'];
			var tbar = config['tbar'];
			var bbar = config['bbar'];
			var rbar = config['rbar'];
			var preview = config['preview'];
			var anchor = config['anchor'];
			var pagesize = config['pagesize'];
			var reorder = config['reorder'];
			var show_filter = config['show_filter'];
			var viewConfig = config['viewConfig'];
			var timeout = config['timeout'];
			if(pagesize == null)
				pagesize = Ext.app.PAGESIZE;
			if(load == null)
				load = true;
			var checkbox = config['checkbox'] != null ? config['checkbox'] : false;

			// Crea el store del Grid
			var store = Ext.app.createStore({
				model : model,
				url : urlget,
				id : idfield,
				timeout: timeout,
				groupField : groupField,
				sortInfo : sortInfo,
				pagesize : pagesize,
				autoload : false
			});

			store.baseParams = {
				start : 0,
				limit : parseInt(pagesize)
			}

			// Función de añadido
			if(mode != 'search' && (urladd != null)) {
				var fnadd = function() {
					// Controles
					var addcontrols = Ext.app.createAddFields(model);

					// Función OK
					var fnok = function() {
						store.load({
							params : {
								start : 0,
								limit : parseInt(pagesize)
							}
						});
					};
					// Formulario
					Ext.app.showAddForm({
						title : _s('Añadir'),
						controls : addcontrols,
						url : urladd,
						fn_add : fn_add,
						fn_ok : fnok
					});
				};
			} else {
				var fnadd = null;
			}

			// Grid
			var grid = Ext.app.createGrid({
				'id' : id,
				'idfield' : idfield,
				'title' : title,
				'icon' : icon,
				'urlupd' : urlupd,
				'urldel' : urldel,
				'plugins' : plugins,
				'store' : store,
				'model' : model,
				'fnadd' : fnadd,
				'grouping' : (groupField != null),
				'autoexpand' : true,
				'fn_pre' : fn_pre,
				'fn_add' : fn_add,
				'load' : load,
				'mode' : mode,
				'fn_open' : fn_open,
				'tbar' : tbar,
				'bbar' : bbar,
				'rbar' : rbar,
				'pagesize' : pagesize,
				'preview' : preview,
				'reorder' : reorder,
				checkbox : checkbox,
				show_filter : show_filter,
				viewConfig : viewConfig
			});

			// Formulario
			var form = new Ext.Panel({
				layout : 'border',
				title : title,
				id : id,
				iconCls : icon,
				region : 'center',
				closable : true,
				baseCls : 'x-plain',
				frame : true,
				anchor : anchor,
				items : grid
			});

			if(mode == 'search') {
				var addcontrols = Ext.app.createAddFields(model);
				form.addcontrols = addcontrols;
			}

			// Carga los Stores
			if(load) {
				var stores = [{
					store : store,
					params : {
						start : 0,
						limit : parseInt(pagesize)
					}
				}];

			} else {
				var stores = [];
			}
			if(loadstores != null)
				stores = stores.concat(loadstores);

			//var grid = Ext.getCmp(id + '_grid');
			Ext.app.loadStores(stores);

			return form;
		},
		onAfterEdit : function(e, params, urlupd, title, fnok, fnnok) {
			if((is_null(e.value, '') != is_null(e.originalValue, ''))) {
				// Si es fecha, lo convierte a a número
				try {
					params[e.field] = DateToNumber(e.value.getTime());
				} catch (ex) {
					params[e.field] = e.value.toString();
				}
				Ext.app.callRemote({
					url : urlupd,
					title : title,
					waitmessage : _s('Actualizando'),
					params : params,
					fnok : function(res) {
						e.record.commit();
						if(fnok != null)
							fnok(res);
					},
					fnnok : function() {
						e.record.reject();
						if(fnnok != null)
							fnnok(res);
					}
				});
				return;
			}

			e.record.commit();
		},
		gridStandarButtons : function(config) {
			var bar = config['bar'];
			var id = config['id'];
			var title = config['title'];
			if(bar == null)
				bar = [];

			bar[bar.length] = '->';

			bar[bar.length] = Ext.app.menuExport(id, true, title);

			bar[bar.length] = {
				iconCls : 'iconoAyuda',
				handler : function() {
					Ext.app.help(title);
				}
			}
			return bar;
		},
		printGrid : function(id, title) {
			try {
				// Coge el grid
				var grid = Ext.getCmp(id);
				//try {
				// CSS
				Ext.ux.GridPrinter.stylesheetPath = Ext.app.PRINT_CSS;

				//HTML
				var html = Ext.ux.GridPrinter.print(grid, title);

				// Llama al controlador de exportaciones
				Ext.app.callRemote({
					url : site_url('sys/export/html'),
					title : _s('Imprimir'),
					nomsg : true,
					icon : 'iconoPreviewTab',
					params : {
						html : html,
						type : 'print'
					},
					fnok : function(o) {
						o.title = _s('Imprimir');
						o.icon = 'iconoPreviewTab';
						o.url = o.src;
						o.print = false;
						//Ext.app.addTabUrl(o);
					}
				});
			} catch (e) {
			}
			//Ext.app.msgFly('Grid', 'YA MISMO, PACIENCIA');
		},
		/**
		 * Crea un grid
		 */
		createGrid : function(config) {

			// Parámetros
			if(config == null)
				config = [];

			var id = config['id'];
			var idfield = config['idfield'];
			var title = config['title'];
			var icon = config['icon'];
			var urlupd = config['urlupd'];
			var urldel = config['urldel'];
			var editor = config['editor'];
			var plugins = config['plugins'];
			var store = config['store'];
			var columns = config['columns'];
			var fnadd = config['fnadd'];
			var grouping = config['grouping'];
			var pages = config['pages'] != null ? config['pages'] : true;
			var tbar = config['tbar'];
			var bbar = config['bbar'];
			var rbar = config['rbar'];
			var fn_pre = config['fn_pre'];
			var mode = config['mode'];
			var fn_open = config['fn_open'];
			var pagesize = config['pagesize'];
			var preview = config['preview'];
			var reorder = config['reorder'];
			var viewConfig = config['viewConfig'];
			var show_filter = config['show_filter'];
			var config_id = config['config_id'];
			if(show_filter == null)
				show_filter = true;
			if(pagesize == null)
				pagesize = Ext.app.PAGESIZE;
			if(id == null)
				id = Ext.app.createId();

			var rownumber = config['rownumber'] != null ? config['rownumber'] : false;
			var checkbox = config['checkbox'] != null ? config['checkbox'] : false;

			var autoexpand = (config['autoexpand'] == true) ? "descripcion" : null;
			var model = config['model'];

			// Columnas
			if(model != null) {
				columns = Ext.app.createColumnsGrid(model);
			}
			if(autoexpand != null) {
				var esta = false;
				Ext.each(columns, function item(e) {
					if(e.id == autoexpand) {
						esta = true;
						return false;
					}
				});
				if(!esta)
					autoexpand = null;
			}

			// Colmnas extras
			var extra = Array();

			// Selección
			var sm = checkbox ? new Ext.grid.CheckboxSelectionModel() : new Ext.grid.RowSelectionModel({
				singleSelect : false
			});

			// Rownumber?
			if(rownumber) {
				extra[extra.length] = new Ext.grid.RowNumberer();
			}

			// Checkbox?
			if(checkbox) {
				extra[extra.length] = sm;
			}
			// Columnas
			columns = extra.concat(columns);

			// Lee el formato guardado
			var formato_item = (config_id==null)?('grid.config.' + title):config_id;
			var config_columns = Ext.app.get_config(formato_item);
			var config_columns = config_columns.split('#');
			var config_columns2 = [];
			Ext.each(config_columns, function(c) {
				var a = c.split(',');
				var b = [];
				Ext.each (a, function (d){
					d = d.split(':');
					b[d[0]] = d[1];
				});
				config_columns2[b['id']] = b;
			});
			
			// Vista agrupada?
			var cfg_view = viewConfig;
			if(cfg_view != null) {
				//cfg_view.forceFit = true;
			} else {
				cfg_view = {
					/*forceFit : true*/
				}
			}
			if(grouping) {
				/*cfg_view.forceFit = true;*/
				cfg_view.hideGroupedColumn = true;
			}
			if(preview != null) {
				cfg_view.enableRowBody = true;
				cfg_view.showPreview = true;
				var f_p = cfg_view.getRowClass;
				cfg_view.getRowClass = function(record, rowIndex, p, ds) {
					if(this.showPreview && record != null) {
						p.body = preview(record);
					}
					if(f_p != null)
						return f_p(record, rowIndex, p, ds);
					return ((rowIndex % 2) == 1) ? 'grid-alt' : '';
				}
			}
			var view = (grouping) ? new Ext.grid.GroupingView(cfg_view) : null;
			var viewConfig = (!grouping) ? cfg_view : viewConfig;

			// Crea el grid en editor (sea o no necesario?)
			var data = Ext.apply(config, {
				region : 'center',
				id : id + "_grid",
				autoExpandColumn : autoexpand,
				loadMask : true,
				stripeRows : true,
				clicksToEdit : 'auto',
				store : store,
				plugins : plugins,
				view : view,
				viewConfig : viewConfig,
				sm : sm,
				columns : columns,
				tbar : []
			});

			if(tbar != null)
				data.tbar = tbar;

			// Hay paginación?
			if(pages) {
				var ps = new Ext.PagingToolbar({
					pageSize : parseInt(pagesize),
					store : store,
					displayInfo : true,
					//layout: 'fit',
					//prependButtons: true,
					displayMsg : _s('grid_desplay_result'),
					emptyMsg : _s('grid_desplay_no_topics'),
					items : ['-', {
						id : id + "_grid_pagesize",
						xtype : 'numberfield',
						value : pagesize,
						width : 40,
						listeners : {
							change : function(f) {
								var i = parseInt(f.getValue());
								if(i <= 0)
									i = 1000000;
								ps.pageSize = i;
							}
						}
					}]
				});
				data.bbar = ps;
			} else {
				data.bbar = new Ext.PagingToolbar({
					pageSize : 1000000,
					store : store,
					displayInfo : true,
					displayMsg : _s('grid_desplay_result_nopage'),
					emptyMsg : _s('grid_desplay_no_topics')
				});
			}

			// ¿Hay que añadir?
			if(fnadd != null) {
				data.tbar[data.tbar.length] = {
					tooltip : _s('Añadir'),
					text : _s('Añadir'),
					iconCls : 'icon-add',
					listeners : {
						click : function() {
							fnadd();
						}
					}
				}
			}

			// ¿Hay que borrar?
			if(urldel != null && (mode != 'search')) {
				var fndelete = function(sm, rowIdx, e) {
					var grid = Ext.getCmp(id + '_grid');
					var sm = grid.getSelectionModel();
					var sel = sm.getSelections();
					if(/* sm.hasSelection() */
					sel.length > 0) {
						var codes = '';
						for(var i = 0; i < sel.length; i = i + 1) {
							codes += sel[i].data.id + ';';
						}
						var msg = _s('elm-registro-multi');
						msg = msg.replace('%s', sel.length);
						Ext.app.callRemoteAsk({
							url : urldel,
							title : title,
							askmessage : msg,
							waitmessage : _s('Eliminando'),
							errormessage : _s('registro-error'),
							params : {
								id : codes
							},

							fnok : function() {
								try {
									var st = grid.getStore();
									st.load({
										params : {
											start : 0,
											limit : parseInt(Ext.app.PAGESIZE)
										}
									});
									// st.remove(sel);
								} catch (ex) {
									Ext.app.msgError(_s('eliminando'), _s('registro-error') + ex);
								}
							}
						});
					}
				};
				data.tbar[data.tbar.length] = {
					tooltip : _s('cmd-delregistro'),
					text : _s('Eliminar'),
					iconCls : 'icon-delete',
					handler : fndelete
				}
			}
			// Comandos adicionales top
			if(rbar != null) {
				// data.tbar[data.tbar.length] = '->';
				data.tbar = data.tbar.concat(rbar);
			}
			// Muestra un filtro de búsqueda
			if(show_filter) {
				var data_field = [];
				Ext.each(columns, function(item) {
					data_field[data_field.length] = [item.dataIndex, item.header];
				});
				var fields_busqueda = new Ext.data.SimpleStore({
					fields : ['id', 'text'],
					data : data_field
				});
				data.tbar[data.tbar.length] = {
					xtype : 'combo',
					id : id + '_field',
					store : fields_busqueda,
					displayField : 'text',
					valueField : 'id',
					typeAhead : true,
					mode : 'local',
					forceSelection : true,
					triggerAction : 'all',
					width : Ext.app.GRID_SEARCH_COMBO_WIDTH,
					selectOnFocus : true
				}
				data.tbar[data.tbar.length] = {
					value : '',
					id : id + '_busqueda',
					xtype : "textfield",
					emptyText : _s('Búsqueda'),
					enableKeyEvents : true,
					listeners : {
						keydown : function(t, e) {
							if(e.getKey() === e.ENTER) {
								var f = Ext.getCmp(id + '_field');
								var g = Ext.getCmp(id + '_grid');
								var s = g.getStore();
								if(t.getValue() != '') {
									if(f.getValue() != null && f.getValue() != '')
										s.baseParams = {
											where : f.getValue() + '=' + t.getValue(),
											start : 0,
											limit : Ext.app.PAGESIZE
										}
									else
										return;
								} else {
									s.baseParams = {
										start : 0,
										limit : Ext.app.PAGESIZE
									}
								}
								s.load();
							}
						}
					}
				}
			}

			// Hay campos de reorden?
			if(reorder) {
				function changeSortDirection(button) {
					var sortData = button.sortData;

					if(sortData != undefined) {
						store.baseParams.sort = button.sortData.name;
						store.baseParams.dir = button.sortData.direction;
						store.sort(button.sortData.name, button.sortData.direction);
						Ext.each(order, function(item) {
							if(button.sortData.name != item.sortData.name) {
								button.setIconClass('');
							}
						});
						button.sortData.direction = (button.sortData.direction == "DESC" ? "ASC" : "DESC");
						button.setIconClass((button.sortData.direction == 'DESC' ? 'sort-asc' : 'sort-desc'));
					}
				}

				var order = [];
				Ext.each(reorder, function(item) {
					order[order.length] = {
						text : _s(item.name),
						sortData : {
							name : item.dataIndex,
							direction : 'ASC'
						},
						handler : function() {
							changeSortDirection(this);
						}
					}
				});
				data.tbar[data.tbar.length] = {
					xtype : 'tbbutton',
					text : _s('Ordenar'),
					cls : 'x-btn-text-icon',
					iconCls : "icon-sort",
					menu : order
				}
			}

			// Exportar
			data.tbar = Ext.app.gridStandarButtons({
				bar : data.tbar,
				title : title,
				id : id + "_grid"

			});

			// Llama a la función de ampliación, si existe
			if(fn_pre != null) {
				data = fn_pre(data);
			}

			if(viewConfig != null) {
				data['viewConfig'] = viewConfig;
			}

			// Crea el grid
			var grid = (urlupd != null || editor === true) ? new Ext.grid.EditorGridPanel(data) : new Ext.grid.GridPanel(data);

			// ¿Hay que borrar? Se añade el borrado por tecla
			if(urldel != null && mode != 'search') {
				grid.on('keydown', function(e) {
					if(e.getKey() == e.DELETE && !grid.editing) {
						fndelete();
					}
				});
			}

			// ¿Hay que actualizar?
			if(urlupd != null) {
				grid.on('afteredit', function(e) {
					var params = {};
					params['id'] = e.record.data.id;
					Ext.app.onAfterEdit(e, params, urlupd, title);
				});
			}
			grid.on('render', function(e) {
				grid.view.hmenu.add(new Ext.menu.CheckItem({
					text : _s('Guardar formato'),
					iconCls : 'icon-save',
					handler : function() {
						var i = grid.getColumnModel().getColumnCount();
						var save = '';
						for(var j = 0; j < i; j++) {
							var column = grid.getColumnModel().getColumnAt(j);
							var d = 'id:' + column.id + ',' + 'width:' + column.width + ',' + 'hidden:' + column.hidden + '#';
							save += d;
						}
						Ext.app.callRemote({
							url : site_url('sys/configuracion/set'),
							params : {
								type : 'user',
								'var' : item,
								value : save
							},
							fn_ok : function() {
								Ext.app.set_config(formato_item, save);								
								Ext.app.msgFly(_s('Guardar formato'), _s('Formato guardado'));
							}
						});
					},
					hideOnClick : false
				}));
			});
			if(mode == 'search') {
				grid.on('dblclick', function(e) {
					var sm = grid.getSelectionModel();
					if(sm.hasSelection()) {
						var sel = sm.getSelected();
						if(fn_open != null) {
							fn_open(sel.data.id);
						}
					}
				});
			}
			if(preview != null)
				grid.preview = preview;

			return grid;
		},
		/**
		 * Muestra un formulario para añadir campos a un GRID
		 */
		showAddForm : function(config) {
			var title = config['title'];
			var controls = config['controls'];
			var url = config['url'];
			var fn_ok = config['fn_ok'];
			var fn_add = config['fn_add'];
			if(fn_add != null) {
				controls = fn_add(controls);
			}

			var form = Ext.app.formStandarForm({
				title : title,
				controls : controls,
				fn_ok : fn_ok,
				url : url
			});
			form.show();
		},
		formEditor : function(config) {
			var title = config['title'];
			var id = config['id'];
			var name = config['name'];
			var value = config['value'];
			var css = config['css'];
			var anchor = config['anchor'];
			if(anchor == null)
				anchor = '100% 91%'
			if(css == null)
				css = Ext.app.EDITOR_CSS;

			return {
				fieldLabel : title,
				xtype : 'tinymce',
				id : id,
				value : value,
				layout : 'fit',
				anchor : anchor,
				name : name,
				tinymceSettings : {
					theme : "advanced",
					content_css : css,
					language : 'es',
					relative_urls : false,
					remove_script_host : false,
					plugins : "imagemanager,safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template,iespell",
					theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
					theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
					theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
					// theme_advanced_buttons1 :
					// "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
					// theme_advanced_buttons2 :
					// "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
					// theme_advanced_buttons3 :
					// "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|",
					// theme_advanced_buttons4 :
					// "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
					extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
					//template_external_list_url: "example_template_list.js"
				}
			}
		},
		formSearchDialog : function(config) {
			return {
				url : config['url'],
				controls : config['controls'],
				columns : config['columns'],
				width : (config['width'] == null) ? null : config['width'],
				height : (config['height'] == null) ? null : config['height'],
				expand : (config['expand'] == null) ? null : config['expand'],
				title : (config['title'] == null) ? null : config['title'],
				texto : (config['Texto'] == null) ? null : config['Texto'],
				id : (config['id'] == null) ? null : config['id'],
				icon : (config['icon'] == null) ? null : config['icon'],
				store : null,
				grid : null,
				form : null,
				fn_add : (config['fn_add'] == null) ? null : config['fn_add'],

				doSearch : function() {
					// Valores
					var v = this.form.getForm().getValues(true);
					// Búsqueda
					this.store.baseParams = {
						where : v,
						start : 0,
						limit : Ext.app.PAGESIZE
					};
					var g = this.grid;
					this.store.load({
						callback : function() {
							g.doLayout();
						}
					});
				},
				init : function() {
					var fields = new Array();
					var c2 = new Array();
					c2.push(new Ext.grid.RowNumberer());
					Ext.each(this.columns, function(e) {
						c2.push(e);
						fields.push({
							name : e.dataIndex
						});
					});
					this.columns = c2;
					this.store = new Ext.data.Store({
						remoteSort : true,
						autoload : true,

						proxy : new Ext.data.HttpProxy({
							url : this.url
						}),
						reader : new Ext.data.JsonReader({
							root : 'value_data',
							totalProperty : 'total_data',
							idProperty : 'id',
							remoteSort : true,
							autoload : false
						}, fields)
					});
					var t = this;
					var sm = new Ext.grid.CheckboxSelectionModel();
					this.grid = new Ext.grid.GridPanel({
						store : this.store,
						anchor : '100% 80%',
						autoExpandColumn : this.expand,
						stripeRows : true,
						loadMask : true,
						sm : sm,

						bbar : new Ext.PagingToolbar({
							pageSize : parseInt(Ext.app.PAGESIZE),
							store : this.store,
							displayInfo : true,
							displayMsg : _s('grid_desplay_result'),
							emptyMsg : _s('grid_desplay_no_topics')
						}),

						// grid columns
						columns : this.columns
					});

					this.grid.on('keydown', function(e) {
						if(e.getKey() == e.DELETE) {
							var sel = t.grid.getSelectionModel().getSelections();
							t.grid.getStore().remove(sel);
						}
					});
					this.controls[this.controls.length] = this.grid;
				},
				run : function() {
					try {
						this.init();
						var t = this;

						var form = new Ext.FormPanel({
							monitorValid : true,
							labelWidth : 80,
							layout : 'form',
							border : false,
							bodyStyle : 'background:transparent;padding:2px',
							items : this.controls
						});

						var window = new Ext.Window({
							title : this.title,
							autoHeight : true,
							bodyStyle : 'padding: 10px 10px 0 10px;',
							width : 500,
							height : 500,
							closeAction : 'close',
							border : false,
							resizable : false,
							plain : true,
							modal : true,
							items : form,
							iconCls : t.icon,
							buttons : [{
								tooltip : _s('cmd-buscar'),
								text : _s('Buscar'),
								iconCls : 'icon-search',
								handler : function(f) {
									t.doSearch();
								}
							}, {
								text : _s('Limpiar'),
								tooltip : _s('cmd-limpiar'),
								iconCls : 'icon-clean',
								// id : id + '_btnnew',
								handler : function(f) {
									t.form.getForm().reset();
									// Ext.app.clearFields(controls);
								}
							}, {
								text : _s('Añadir'),
								tooltip : _s('cmd-add-emails'),
								iconCls : 'icon-add',
								handler : function(f) {
									var emails = [];
									t.grid.getStore().each(function(r) {
										emails[emails.length] = r.data.cEmail;
									});
									if(emails.length > 0) {
										emails = implode(';', emails);
										t.fn_add(t.id, emails, t.texto);
									} else {
										Ext.app.msgError(t.title, _s('no-mails-add'));
									}
								}
							}]
						});
						this.form = form;
						window.show();
					} catch (e) {
					}
				}
			}
		},
		/**
		 * Formulario de búsqueda
		 * @param {Object} config
		 */
		formSearchForm : function(config) {

			try {
				var title = config['title'];
				var id = config['id'];
				var icon = config['icon'];
				var id_grid = config['id_grid'];
				var fn_open = config['fn_open'];
				var fn_pre = config['fn_pre'];
				var grid = config['grid'];
				var pagesize = config['pagesize'];
				var audit = config['audit'];
				var show_id = config['show_id'];
				var query = config['query'];
				if(pagesize == null)
					pagesize = Ext.app.PAGESIZE;
				var searchcontrols = config['searchcontrols'];
				if(show_id == null)
					show_id = true;
				if(searchcontrols == null)
					searchcontrols = grid.addcontrols;
				var diff = 0;
				if(show_id) {
					var a = [{
						allowBlank : true,
						fieldLabel : _s('Id'),
						name : "id",
						xtype : "textfield"
					}];
					diff = 1;
					searchcontrols = a.concat(searchcontrols);
				}
				if(audit === true) {
					var r = [{
						allowBlank : true,
						fieldLabel : _s('dCreacion'),
						name : "dCreacion",
						xtype : "textfield"
					}, {
						allowBlank : true,
						fieldLabel : _s('dAct'),
						name : "dAct",
						xtype : "textfield"
					}]
					searchcontrols = searchcontrols.concat(r);
				}

				var submitSearchForm = function() {
					var fFields = searchForm.getForm().getValues();
					if (fn_pre != undefined)
						if (!(fFields=fn_pre(fFields)))
							return false;
					var where = Array();

					for(var f in fFields) {
						if(fFields[f].trim() != '') {
							var v = new String(fFields[f].trim());
							var f2 = (f.substr(0, 4) == '_nId') ? f.substr(1) : f;

							if(fields[f] == 'checkbox') {
								if((v == _s('bool_si') || (v == _s('bool_no'))))
									where[where.length] = f2 + '=' + ((v == _s('bool_si')) ? '1' : '0');
							} else
								where[where.length] = f2 + '=' + v;
						}
					};
					var w = implode('&', where);
					if(w != '') {
						var g = Ext.getCmp(id_grid);
						var ps = Ext.getCmp(id_grid + '_pagesize');
						if(g != null) {
							g.store.baseParams = {
								where : w,
								start : 0,
								limit : (ps != null) ? parseInt(ps.getValue()) : pagesize
							}
							g.store.load({});
						}
					}
				}
				var fields = Array();
				var columns = Ext.app.SEARCH_COLUMNS;
				var ct = searchcontrols.length;
				var grupos = parseInt(ct / columns);
				if(ct % columns > 0)
					grupos++;
				var tam = 1.0 / Ext.app.SEARCH_COLUMNS;
				if(tam == 0)
					tam = 1;
				var j = 0;
				var k = 0;
				var items = new Array()
				var height = 0;
				var local = 0;
				for(var i = 0; i < searchcontrols.length; i++) {
					var size = 20;
					fields[searchcontrols[i].name] = searchcontrols[i].xtype;
					if(items[j] == null) {
						items[j] = {
							columnWidth : tam,
							layout : 'form',
							bodyStyle : 'background:transparent;padding:2px',
							border : false,
							items : new Array()
						}
					}

					if(searchcontrols[i].xtype == 'datefield') {
						items[j]['items'][k] = grid.addcontrols[i - diff];
						items[j]['items'][k].xtype = 'textfield';
					} else if(searchcontrols[i].xtype == 'numberfield') {
						items[j]['items'][k] = searchcontrols[i];
						items[j]['items'][k].xtype = 'textfield';
					} else if(searchcontrols[i].xtype == 'checkbox') {
						items[j]['items'][k] = searchcontrols[i];
						items[j]['items'][k].store = Ext.app.combo_data;
						items[j]['items'][k].xtype = 'combo';
						items[j]['items'][k].typeAhead = true;
						items[j]['items'][k].triggerAction = 'all';
						items[j]['items'][k].emptyText = _s('bool_noselect');
						items[j]['items'][k].selectOnFocus = true;
						//items[j]['items'][k].id = Ext.app.createId();
					} else {
						items[j]['items'][k] = searchcontrols[i];
						items[j]['items'][k].anchor = '100%';
						size = 30;
					}
					items[j]['items'][k].allowBlank = true;
					items[j]['items'][k].id = null;
					local += size;
					k++;
					if(k >= grupos) {
						height = Math.max(local, height);
						local = 0;
						j++;
						k = 0;
					}
				}
				/*if (k == 0)*/
				//local += size;
				height = Math.max(local, height) + 67;
				tam = items[0].items.length;
				var searchForm = new Ext.FormPanel({
					region : 'north',
					height : Math.min(Ext.app.SEARCHFILTERHEIGHT, height),
					title : _s('Extended Filter'),
					collapsible : true,
					collapsed : false,
					autoScroll : true,
					split : true,
					labelWidth : Ext.app.LABEL_SIZE,
					items : [{
						layout : 'column',
						border : false,
						items : items
					}],
					buttons : [{
						text : _s('Borrar'),
						iconCls : 'icon-clean',
						handler : function() {
							searchForm.getForm().reset();
							var g = Ext.getCmp(id_grid);
							g.store.clearFilter();
						}
					}, {
						text : _s('Buscar'),
						iconCls : 'icon-search',
						handler : submitSearchForm
					}]
				});

				var panel = new Ext.Panel({
					layout : 'border',
					title : title,
					id : id,
					iconCls : icon,
					region : 'center',
					closable : true,
					baseCls : 'x-plain',
					frame : true,
					items : [searchForm, grid],
					listeners : {
						afterrender : function(p) {
							if(query != null && query != '') {
								var g = Ext.getCmp(id_grid);
								g.store.baseParams = {
									query : query,
									start : 0,
									limit : pagesize
								}
								g.store.load({});
							}
							var map = new Ext.KeyMap(p.getEl(), [{
								key : [10, 13],
								ctrl : true,
								stopEvent : true,
								fn : function() {
									submitSearchForm();
								}
							}, {
								key : "b",
								ctrl : true,
								stopEvent : true,
								//shift: true,
								fn : function() {
									submitSearchForm();
								}
							}, {
								key : "b",
								alt : true,
								stopEvent : true,
								//shift: true,
								fn : function() {
									submitSearchForm();
								}
							}]);
						}
					}
				});

				return panel;
			} catch (e) {
			}
		},
		/**
		 * Carga los stores indicados en un array por orden
		 */
		loadStores : function(stores, fn) {
			try {
				if(stores.length > 0) {
					var st = stores.pop();
					var params = null;
					if(st.params)
						params = st.params;

					if(st.store != null) {
						/*if (st.store.getCount() > 0) {
						Ext.app.loadStores(stores, el);
						}
						else {*/
						st.store.load({
							params : params,
							callback : function() {
								Ext.app.loadStores(stores, fn);
							}
						});
						//}
					} else {
						Ext.app.loadStores(stores, fn);
					}
				} else {
					if(fn != null)
						fn();
				}
			} catch (e) {
			}
		},
		gridBottom : function(store, pages) {
			return new Ext.PagingToolbar({
				pageSize : (pages === true) ? parseInt(Ext.app.PAGESIZE) : 1000000,
				store : store,
				displayInfo : true,
				displayMsg : _s('grid_desplay_result'),
				emptyMsg : _s('grid_desplay_no_topics')
			});

		},
		/**
		 * Crea un grid con el listado de elemantos marcables con check
		 * @param {Object} config
		 */
		formCheckList : function(config) {
			// Temas
			var urllist = config['urllist'];
			var urlupd = config['urlupd'];
			var id = config['id'];
			var title = config['title'];
			var params = config['params'];
			var form = config['form'];
			var text = (config['text'] != null) ? config['text'] : 'text';
			var idreg = config['idreg'];

			/**
			 * Crea el store
			 */
			var model = [{
				name : 'id',
				type : 'int'
			}, {
				name : idreg,
				type : 'int'
			}, {
				name : text
			}];

			var store = Ext.app.createStore({
				url : urllist,
				model : model
			});

			var check = new Ext.ux.grid.CheckColumn({
				dataIndex : 'id',
				header : '[x]',
				width : Ext.app.TAM_COLUMN_BOOL
			});

			var grid = new Ext.grid.EditorGridPanel({
				store : store,
				autoExpandColumn : "descripcion",
				id : id,
				stripeRows : true,
				loadMask : true,
				plugins : [check],
				// grid columns
				columns : [check, {
					header : _s('Id'),
					dataIndex : idreg,
					width : Ext.app.TAM_COLUMN_ID,
					align : 'right',
					hidden : true,
					sortable : true
				}, {
					header : _s('Descripcion'),
					dataIndex : text,
					id : 'descripcion',
					width : Ext.app.TAM_COLUMN_TEXT,
					sortable : true
				}],
				bbar : Ext.app.gridBottom(store, false),
				tbar : Ext.app.gridStandarButtons({
					id : id,
					title : title
				})
			});

			grid.on('afteredit', function(e) {
				if(e.field == 'id') {
					var params2 = params;
					if(params2 == null)
						params2 = {};
					params2[idreg] = e.record.data[idreg];
					params2['id'] = form.getId();
					e.field = 'value';
					Ext.app.onAfterEdit(e, params2, urlupd);
				}
			});
			return grid;
		},
		/**
		 * Crea un formulario con los perfiles
		 */
		formPerfiles : function() {
			return {
				tipoperfil : null,
				grid : null,
				id : null,
				mainform : null,
				url : null,
				etq: null,
				store : null,

				/**
				 * Carga los perfiles
				 * @param {Object} id
				 */
				load : function(id) {

					var t = this;
					this.store.baseParams = {
						id : parseInt(id)
					}
					this.store.load({
						waitMsg : _s('Cargando'),
						callback : function() {
							t.grid.doLayout();
						}
					});
				},
				/**
				 * Enable/Disable controles
				 * @param {Object} enable
				 */
				enable : function(enable) {(enable) ? this.grid.enable() : this.grid.disable();
				},
				/**
				 * Resetea los controles
				 */
				reset : function() {
					this.store.baseParams = {
						id : -1
					}
					this.store.removeAll();
				},
				/**
				 * Formulario para crear un email
				 * @param {Object} data
				 */
				crearEmail : function(data) {
					var t = this;
					var controls = [{
						xtype : 'hidden',
						id : t.id + 'e_new_id',
						name : 'id_c'
					}, {
						xtype : 'hidden',
						id : t.id + '_e_id',
						name : 'id',
						value : (data != null) ? data.id : null
					}, {
						xtype : 'hidden',
						id : t.id + 'e_new_type',
						name : 'tipo',
						value : 'E'
					}, t.tipoperfil, {
						xtype : 'textfield',
						name : 'cDescripcion',
						allowBlank : true,
						fieldLabel : _s('Descripcion'),
						value : (data != null) ? data.cDescripcion : null
					}, {
						xtype : 'textfield',
						name : 'cEMail',
						value : (data != null) ? data.cEMail : null,
						allowBlank : false,
						fieldLabel : _s('Email')
					}];

					this.formView(_s('Email'), controls, data, 'e_new_id', 'icon-email');
				},
				/**
				 * Formulario para crear un teléfono
				 * @param {Object} data
				 */
				crearTelefono : function(data) {
					var t = this;
					var controls = [{
						xtype : 'hidden',
						id : t.id + 't_new_id',
						name : 'id_c'
					}, {
						xtype : 'hidden',
						id : t.id + '_t_id',
						name : 'id',
						value : (data != null) ? data.id : null
					}, {
						xtype : 'hidden',
						id : t.id + 'e_new_type',
						name : 'tipo',
						value : 'T'
					}, t.tipoperfil, {
						xtype : 'textfield',
						name : 'cDescripcion',
						allowBlank : true,
						fieldLabel : _s('Descripcion'),
						value : (data != null) ? data.cDescripcion : null
					}, {
						xtype : 'textfield',
						name : 'cTelefono',
						value : (data != null) ? data.cTelefono : null,
						allowBlank : false,
						fieldLabel : _s('Telefono')
					}, {
						xtype : 'checkbox',
						name : 'bFax',
						allowBlank : false,
						fieldLabel : _s('Fax'),
						value : (data != null) ? ((data.bFax == 1) ? true : false) : null
					}];

					this.formView(_s('Telefono'), controls, data, 't_new_id', 'icon-telefono');
				},
				/**
				 * Formulario para crear un contacto
				 * @param {Object} data
				 */
				crearContacto : function(data) {
					var t = this;
					var controls = [{
						xtype : 'hidden',
						id : t.id + 'c_new_id',
						name : 'id_c'
					}, {
						xtype : 'hidden',
						id : t.id + '_c_id',
						name : 'id',
						value : (data != null) ? data.id : null
					}, {
						xtype : 'hidden',
						id : t.id + 'c_new_type',
						name : 'tipo',
						value : 'C'
					}, t.tipoperfil, {
						xtype : 'textfield',
						name : 'cDescripcion',
						allowBlank : true,
						fieldLabel : _s('Descripcion'),
						value : (data != null) ? data.cDescripcion : null
					}, {
						xtype : 'textfield',
						name : 'cNombre',
						value : (data != null) ? data.cNombre : null,
						allowBlank : false,
						fieldLabel : _s('Nnombre')
					}, {
						xtype : 'textfield',
						name : 'cApellido',
						allowBlank : true,
						fieldLabel : _s('Apellido'),
						value : (data != null) ? data.cApellido : null
					}];

					this.formView(_s('Contacto'), controls, data, 'c_new_id', 'icon-contacto');
				},
				/**
				 * Crea un formulario para dirección
				 * @param {Object} data
				 */
				crearDireccion : function(data) {
					var t = this;
					var c = Ext.app.formComboPaises({
						idpais : t.id + '_p',
						idregion : t.id + '_r',
						value_p : (data != null) ? data.nIdPais : null,
						value_r : (data != null) ? data.nIdRegion : null,
						allowblank : false
					});
					var controls = [{
						xtype : 'hidden',
						id : t.id + 'c_new_id',
						name : 'id_c'
					}, {
						xtype : 'hidden',
						id : t.id + '_c_id',
						name : 'id',
						value : (data != null) ? data.id : null
					}, {
						xtype : 'hidden',
						id : t.id + 'c_new_type',
						name : 'tipo',
						value : 'D'
					}, t.tipoperfil, {
						xtype : 'textfield',
						name : 'cDescripcion',
						allowBlank : true,
						fieldLabel : _s('Descripcion'),
						value : (data != null) ? data.cDescripcion : null
					}, {
						xtype : 'textarea',
						name : 'cTitular',
						anchor : '90%',
						value : (data != null) ? data.cTitular : null,
						allowBlank : true,
						fieldLabel : _s('Titular')
					}, {
						xtype : 'textarea',
						name : 'cCalle',
						anchor : '90%',
						allowBlank : false,
						fieldLabel : _s('Calle'),
						value : (data != null) ? data.cCalle : null
					}, {
						xtype : 'textfield',
						name : 'cCP',
						allowBlank : true,
						fieldLabel : _s('CP'),
						value : (data != null) ? data.cCP : null
					}, {
						xtype : 'textfield',
						name : 'cPoblacion',
						allowBlank : true,
						fieldLabel : _s('Población'),
						value : (data != null) ? data.cPoblacion : null
					}, c[0], c[1]];

					this.formView(_s('Direccion'), controls, data, 'c_new_id', 'icon-direccion');
				},
				/**
				 * Formulario genérico
				 * @param {Object} title
				 * @param {Object} controls
				 * @param {Object} data
				 * @param {Object} id
				 */
				formView : function(title, controls, data, id, icon) {
					var t = this;
					var form = Ext.app.formStandarForm({
						controls : controls,
						title : title,
						icon: icon,
						fn_pre : function() {
							if(t.mainform != null)
								Ext.getCmp(t.id + id).setValue(t.mainform.getId());
						},
						fn_ok : function() {
							t.grid.getStore().load({
								waitMsg : _s('Cargando'),
								callback : function() {
									t.grid.doLayout();
								}
							});
						},
						url : t.url + '/add'
					});

					if(data != null && data.nIdTipo != null) {
						Ext.getCmp(t.id + 'nIdTipo').setValue(data.nIdTipo);
					}

					form.show();
				},
				/**
				 * Aplica icono al perfil
				 * @param {Object} val
				 */
				tipo_perfil : function(val) {
					return "<div class='cell-perfil" + val + "'></div>";
				},
				/**
				 * Inicializa el formulario
				 * @param {Object} config
				 */
				init : function(config) {

					this.id = config['id'];
					this.url = config['url'];
					this.mainform = config['mainform'];
					this.etq = config['etq'];

					this.store = new Ext.data.Store({
						remoteSort : true,
						autoload : true,

						proxy : new Ext.data.HttpProxy({
							url : this.url + '/get_list'
						}),
						reader : new Ext.data.JsonReader({
							root : 'value_data',
							totalProperty : 'total_data',
							idProperty : 'id_u',
							remoteSort : true,

							autoload : true
						}, [{
							name : 'id'
						}, {
							name : 'id_u'
						}, {
							name : 'text'
						}, {
							name : 'cDescripcion'
						}, {
							name : 'tipo'
						}, {
							name : 'cPerfil'
						}])
					});

					this.tipoperfil = Ext.app.combobox({
						url : site_url('perfiles/tipoperfil/search'),
						id : this.id + 'nIdTipo',
						name : 'nIdTipo',
						label : _s('Tipo')
					})

					var t = this;
					var sm = new Ext.grid.RowSelectionModel({
						singleSelect : true
					});

					var fndelete = function(sm, rowIdx, e) {
						var grid = t.grid;
						var sm = grid.getSelectionModel();
						if(sm.hasSelection()) {
							var sel = sm.getSelected();
							Ext.app.callRemoteAsk({
								url : t.url + '/del',
								title : _s('profile-del'),
								askmessage : _s('elm-registro'),
								waitmessage : _s('eliminando'),
								errormessage : _s('registro_error'),
								params : {
									id : sel.data.id,
									tipo : sel.data.tipo
								},

								fnok : function() {
									try {
										var st = grid.getStore();
										st.load();
									} catch (ex) {
										Ext.app.msgError(_s('Eliminando'), _s('registro-error') + ex);
									}
								}
							});
						}
					};
					var fnedit = function(id, tipo) {
						Ext.app.callRemote({
							url : t.url + '/get',
							params : {
								id : parseInt(id),
								tipo : tipo
							},
							fnok : function(res) {
								if(tipo == 'E') {
									t.crearEmail(res.value_data);
								} else if(tipo == 'T') {
									t.crearTelefono(res.value_data);
								} else if(tipo == 'C') {
									t.crearContacto(res.value_data);
								} else if(tipo == 'D') {
									t.crearDireccion(res.value_data);
								}
							}
						})
					}
					this.grid = new Ext.grid.GridPanel({
						width : 700,
						height : 500,
						store : t.store,
						autoExpandColumn : "descripcion",
						id : id,
						stripeRows : true,
						loadMask : true,
						sm : sm,

						// grid columns
						columns : [{
							header : "",
							width : Ext.app.TAM_COLUMN_ICON,
							sortable : true,
							renderer : t.tipo_perfil,
							dataIndex : 'tipo'
						}, {
							header : _s('Id'),
							dataIndex : 'id',
							width : Ext.app.TAM_COLUMN_ID,
							align : 'right',
							hidden : true,
							sortable : true
						}, {
							header : _s('Tipo'),
							dataIndex : 'cPerfil',
							width : Ext.app.TAM_COLUMN_TEXT,
							sortable : true
						}, {
							header : _s('Descripcion'),
							dataIndex : 'cDescripcion',
							width : Ext.app.TAM_COLUMN_TEXT,
							sortable : true
						}, {
							header : _s('Valor'),
							dataIndex : 'text',
							id : 'descripcion',
							width : Ext.app.TAM_COLUMN_TEXT,
							sortable : true
						}],
						bbar : Ext.app.gridBottom(t.store, false),
						tbar : Ext.app.gridStandarButtons({
							id : id,
							title : _s('Perfiles'),
							bar : [{
								xtype : 'tbbutton',
								cls : 'x-btn-text-icon',
								text : _s('Añadir'),
								iconCls : 'icon-add',
								menu : [{
									iconCls : 'icon-direccion',
									text : _s('new-address'),
									handler : function() {
										t.crearDireccion();
									}
								}, {
									iconCls : "icon-telefono",
									text : _s('new-telefono'),
									handler : function() {
										t.crearTelefono();
									}
								}, {
									iconCls : "icon-email",
									text : _s('new-email'),
									handler : function() {
										t.crearEmail();
									}
								}, {
									iconCls : "icon-contacto",
									text : _s('new-contacto'),
									handler : function() {
										t.crearContacto();
									}
								}]
							}, {
								tooltip : _s('cmd-delregistro'),
								text : _s('Eliminar'),
								iconCls : 'icon-delete',
								handler : fndelete
							}]
						}),

						viewConfig : {
							enableRowBody : true,
							getRowClass : function(r, rowIndex, rowParams, store) {
								return '';
								return (rowIndex == 0) ? 'cell-perfil' + r.data.tipo : '';
							}
						}
					});

					this.grid.on('keydown', function(e) {
						if(e.getKey() == e.DELETE && !this.grid.editing) {
							fndelete();
						}
					});
					this.grid.on('dblclick', function(e) {
						var sm = t.grid.getSelectionModel();
						if(sm.hasSelection()) {
							var sel = sm.getSelected();
							fnedit(sel.data.id, sel.data.tipo);
						}
					});
					var tg = this.grid;
					var ctxRow = null;

					var unificar_direccion = function(idc, id) {
						var store = new Ext.data.Store({
							remoteSort : true,
							autoload : true,

							proxy : new Ext.data.HttpProxy({
								url : t.url + '/get_list',
								params: 'foo=bar'
							}),
							reader : new Ext.data.JsonReader({
								root : 'value_data',
								totalProperty : 'total_data',
								idProperty : 'id_u',
								remoteSort : true,

								autoload : true
							}, [{
								name : 'id'
							}, {
								name : 'id_u'
							}, {
								name : 'text'
							}, {
								name : 'cDescripcion'
							}, {
								name : 'tipo'
							}, {
								name : 'cPerfil'
							}])
						});
						var sm = new Ext.grid.RowSelectionModel({
							singleSelect : true
						});
						var grid = new Ext.grid.GridPanel({
							width : 700,
							height : 300,
							store : store,
							autoExpandColumn : "descripcion",
							//id : id,
							stripeRows : true,
							loadMask : true,
							sm : sm,

							// grid columns
							columns : [{
								header : "",
								width : Ext.app.TAM_COLUMN_ICON,
								sortable : true,
								renderer : t.tipo_perfil,
								dataIndex : 'tipo'
							}, {
								header : _s('Id'),
								dataIndex : 'id',
								width : Ext.app.TAM_COLUMN_ID,
								align : 'right',
								hidden : true,
								sortable : true
							}, {
								header : _s('Tipo'),
								dataIndex : 'cPerfil',
								width : Ext.app.TAM_COLUMN_TEXT,
								sortable : true
							}, {
								header : _s('Descripcion'),
								dataIndex : 'cDescripcion',
								width : Ext.app.TAM_COLUMN_TEXT,
								sortable : true
							}, {
								header : _s('Valor'),
								dataIndex : 'text',
								id : 'descripcion',
								width : Ext.app.TAM_COLUMN_TEXT,
								sortable : true
							}]
						});

						grid.on('keydown', function(e) {
							if(e.getKey() == e.DELETE && !this.grid.editing) {
								fndelete();
							}
						});

						var accion = function() {
							var sm = grid.getSelectionModel();
							if(sm.hasSelection()) {
								var sel = sm.getSelected();
								Ext.app.callRemote({
									url : t.url + '/unificar_direccion',
									params : {
										id1 : sel.data.id,
										id2 : id
									},
									fnok: function () {
										form.close();
										t.store.load();
									}
								});
							}
						}

						grid.on('dblclick', function(e) {
							accion();
						});

						store.load({
							params: {
								id: idc,
								tipo: 'D'
							},
							callback: function(r) {
								Ext.each(r, function(item) {
									if (item.data.id == id) {
										store.remove(item);
									}
								});
							}
						});

						var form = Ext.app.formStandarForm({
							controls : [grid],
							title : _s('Unificar'),
							icon: 'icon-unificar',
							fn_ok : function() {
								accion();
								return false;
							}
						});

						form.show();

					}
					var menuemail = new Ext.menu.Menu({
						allowOtherMenus : false,
						items : [{
							text : _s('Enviar email'),
							handler : function() {
								try {
									/*var sm = tg.getSelectionModel();
									 var sel = sm.getSelections();
									 if (sel.length > 0)*/
									if(ctxRow != null) {
										//var record = sel[0];
										Ext.app.callRemote({
											url : site_url('comunicaciones/email/index'),
											params : {
												to : ctxRow.data.text,
												id : Ext.app.createId()
											}
										});
									}
								} catch (e) {
								}
							},
							iconCls : 'icon-email'
						}]
					});
					var t = this;
					var menuedireccion = new Ext.menu.Menu({
						allowOtherMenus : false,
						items : [{
							text : _s('Ver mapa'),
							handler : function() {
								try {
									if(ctxRow != null) {
										Ext.app.callRemote({
											url : site_url('perfiles/direccion/mapa'),
											params : {
												direccion : ctxRow.data.text
											}
										});
									}
								} catch (e) {
								}
							},
							iconCls : 'iconoMapa'
						}, '-', {
							text : _s('Imprimir etiqueta'),
							handler : function() {
								try {
									if(ctxRow != null) {
										Ext.app.callRemote({
											url : site_url('etiquetas/etiqueta/print' + t.etq),
											params : {
												id : ctxRow.data.id
											}
										});
									}
								} catch (e) {
								}
							},
							iconCls : 'icon-label'
						}, {
							text : _s('Encolar etiqueta'),
							handler : function() {
								try {
									if(ctxRow != null) {
										Ext.app.callRemote({
											url : site_url('etiquetas/etiqueta/cola' + t.etq),
											params : {
												id : ctxRow.data.id
											}
										});
									}
								} catch (e) {
								}
							},
							iconCls : 'icon-label-cola'
						}, '-', {
				            text: _s('Enviar por courier'),
				            handler: function(){
				                sendCourier(t.url + '/courier', { 
				            	    	getId : function() { 
				                			return t.mainform.getId() + '_' + ctxRow.data.id;
				            	    		},
				            	    	refresh : function () {
				            	    		t.mainform.refresh();
				            	    	}
				            		}, 0);
				            },
				            iconCls: 'iconoCourier'
						}, '-', {
							text : _s('Unificar'),
							handler : function() {
								try {
									if(ctxRow != null) {
										unificar_direccion(t.store.baseParams.id, ctxRow.data.id);
									}
								} catch (e) {
									console.dir(e);
								}
							},
							iconCls : 'icon-unificar'
						},]
					});
					var menutelefono = new Ext.menu.Menu({
						allowOtherMenus : false,
						items : [{
							text : _s('Enviar SMS'),
							handler : function() {
								try {
									/*var sm = tg.getSelectionModel();
									 var sel = sm.getSelections();
									 if (sel.length > 0)*/
									if(ctxRow != null) {
										//var record = sel[0];
										Ext.app.execCmd({
											url : site_url('comunicaciones/sms/send/' + ctxRow.data.text)
										});
									}
								} catch (e) {
								}
							},
							iconCls : 'icon-sms'
						}]
					});

					this.grid.on('rowcontextmenu', function(gridPanel, rowIndex, e) {
						e.stopEvent();
						try {
							ctxRow = gridPanel.store.getAt(rowIndex);

							/*var sm = gridPanel.getSelectionModel();
							 var sel = sm.getSelections();
							 if (sel.length > 0) {*/
							if(ctxRow != null) {
								//var record = sel[0];
								if(ctxRow.data.tipo == 'T')
									menutelefono.showAt(e.getXY());
								if(ctxRow.data.tipo == 'D')
									menuedireccion.showAt(e.getXY());
								if(ctxRow.data.tipo == 'E')
									menuemail.showAt(e.getXY());
							}
						} catch (e) {
						}
					});
					this.tipoperfil.store.load();
					return this.grid;
				}
			}
		},
		/**
		 * Crea un formulario con los perfiles
		 */
		formNotas : function() {
			return {
				tiponota : null,
				grid : null,
				id : null,
				mainform : null,
				url : null,
				store : null,

				/**
				 * Carga los perfiles
				 * @param {Object} id
				 */
				load : function(id) {

					var t = this;
					this.store.baseParams = {
						start : 0,
						limit : parseInt(Ext.app.PAGESIZE),
						id : parseInt(id)
					}
					this.store.load({
						waitMsg : _s('Cargando'),
						callback : function(r) {
							t.grid.ownerCt.setIconClass('icon-history');
							Ext.each(r, function(item) {
								if(item.data.nIdTipoObservacion == 2) {
									t.grid.ownerCt.setIconClass('icon-alert');
									return false;
								}
							});
							t.grid.doLayout();
						}
					});
				},
				/**
				 * Enable/Disable controles
				 * @param {Object} enable
				 */
				enable : function(enable) {(enable) ? this.grid.enable() : this.grid.disable();
				},
				/**
				 * Resetea los controles
				 */
				reset : function() {
					this.store.baseParams = {
						id : -1
					}
					this.store.removeAll();
				},
				/**
				 * Formulario para crear un email
				 * @param {Object} data
				 */
				crearNota : function(data, tipo) {
					var t = this;
					var tiponota = (tipo!=null)?{xtype : 'hidden',
						name : 'tipo',
						value:tipo }:t.tiponota;
					var controls = [{
						xtype : 'hidden',
						value : t.mainform.getId(),
						name : 'id_r'
					}, {
						xtype : 'hidden',
						name : 'id',
						value : (data != null) ? data.id : null
					}, tiponota, Ext.app.formHtmlEditor({
				            name: 'Texto',
							hideLabel : true,
				            value : (data != null) ? data.tObservacion : null,
				            anchor: '100% 91%'
				        })[0]
					/*{
						xtype : 'textarea',
						hideLabel : true,
						anchor : '100%',
						name : 'Texto',
						allowBlank : true,
						width : '100%',
						fieldLabel : _s('Nota'),
						value : (data != null) ? data.tObservacion : null
					}*/];

					var form = Ext.app.formStandarForm({
						controls : controls,
						title : _s('Nota'),
						icon : 'icon-history',
						fn_ok : function() {
							t.grid.getStore().load({
								waitMsg : _s('Cargando'),
								callback : function() {
									t.grid.doLayout();
								}
							});
						},
						url : t.url + '/add_nota'
					});

					if(data != null && data.nIdTipoObservacion != null) {
						try {
							Ext.getCmp(t.id + 'nIdTipoObservacion').setValue(data.nIdTipoObservacion);
						} catch (e) {
						}
					}

					form.show();
				},
				/**
				 * Aplica icono al perfil
				 * @param {Object} val
				 */
				tipo_nota : function(val) {
					return "<div class='cell-nota" + val + "'></div>";
				},
				renderernota : function(val, x, r) {
					return "<div class='cell-nota" + r.data.nIdTipoObservacion + "'>" + val + "</div>";
				},
				/**
				 * Inicializa el formulario
				 * @param {Object} config
				 */
				init : function(config) {
					this.id = config['id'];
					this.url = config['url'];
					this.mainform = config['mainform'];

					this.store = new Ext.data.Store({
						remoteSort : true,
						autoload : true,

						proxy : new Ext.data.HttpProxy({
							url : this.url + '/notas'
						}),
						reader : new Ext.data.JsonReader({
							root : 'value_data',
							totalProperty : 'total_data',
							idProperty : 'id',
							remoteSort : true,
							autoload : true
						}, [{
							name : 'id'
						}, {
							name : 'tObservacion'
						}, {
							name : 'nIdTipoObservacion'
						}, {
							name : 'cTipoObservacion'
						}, {
							name : 'cCUser'
						}, {
							name : 'dCreacion'
						}, {
							name : 'cAUser'
						}, {
							name : 'dAct'
						}])
					});

					this.tiponota = Ext.app.combobox({
						url : site_url('generico/tiponota/search'),
						id : this.id + 'nIdTipoObservacion',
						name : 'tipo',
						label : _s('Tipo')
					})

					var t = this;
					var sm = new Ext.grid.RowSelectionModel({
						singleSelect : true
					});

					var fndelete = function(sm, rowIdx, e) {
						var grid = t.grid;
						var sm = grid.getSelectionModel();
						if(sm.hasSelection()) {
							var sel = sm.getSelected();
							Ext.app.callRemoteAsk({
								url : t.url + '/del_nota',
								title : _s('nota_del'),
								askmessage : _s('elm-registro'),
								params : {
									id : sel.data.id
								},

								fnok : function() {
									try {
										var st = grid.getStore();
										st.load();
									} catch (ex) {
										Ext.app.msgError(_s('eliminando'), _s('registro_error') + ex);
									}
								}
							});
						}
					};
					var fnedit = function(id, tipo) {
						Ext.app.callRemote({
							url : t.url + '/get_nota',
							params : {
								id : parseInt(id)
							},
							fnok : function(res) {
								t.crearNota(res.value_data);
							}
						});
					}
					this.grid = new Ext.grid.GridPanel({
						width : 700,
						height : 500,
						store : t.store,
						autoExpandColumn : "descripcion",
						id : id,
						stripeRows : true,
						loadMask : true,
						sm : sm,

						// grid columns
						columns : [{
							header : _s('Id'),
							dataIndex : 'id',
							width : Ext.app.TAM_COLUMN_ID,
							align : 'right',
							hidden : true,
							sortable : true
						}, {
							header : "",
							width : Ext.app.TAM_COLUMN_ICON,
							sortable : true,
							renderer : t.tipo_nota,
							dataIndex : 'nIdTipoObservacion'
						}, {
							header : _s('Tipo'),
							width : Ext.app.TAM_COLUMN_TEXT,
							sortable : true,
							dataIndex : 'cTipoObservacion'
						}, {
							header : _s('Nota'),
							dataIndex : 'tObservacion',
							//renderer: t.renderernota,
							id : 'descripcion',
							width : Ext.app.TAM_COLUMN_TEXT,
							sortable : true
						}, {
							dataIndex : 'dCreacion',
							dateFormat : 'timestamp',
							startDay : Ext.app.DATESTARTDAY,
							header : _s("dCreacion"),
							width : Ext.app.TAM_COLUMN_DATE,
							renderer : Ext.app.renderDate,
							sortable : true
						}, {
							dataIndex : 'cCUser',
							header : _s('cCUser'),
							width : Ext.app.TAM_COLUMN_TEXT,
							sortable : true
						}, {
							dataIndex : 'dAct',
							dateFormat : 'timestamp',
							startDay : Ext.app.DATESTARTDAY,
							header : _s("dCreacion"),
							width : Ext.app.TAM_COLUMN_DATE,
							renderer : Ext.app.renderDate,
							sortable : true
						}, {
							dataIndex : 'cAUser',
							header : _s('cCUser'),
							width : Ext.app.TAM_COLUMN_TEXT,
							sortable : true
						}],
						bbar : Ext.app.gridBottom(t.store, false),
						tbar : Ext.app.gridStandarButtons({
							id : id,
							title : _s('Perfiles'),
							bar : [{
								xtype : 'tbbutton',
								iconCls : "icon-new",
								menu : [{
									iconCls : 'cell-nota1',
									tooltip : _s('cmd-addregistro'),
									text : _s('Normal'),
									handler : function() {
										t.crearNota(null, 1);
									}
								}, {
									iconCls : 'cell-nota2',
									tooltip : _s('cmd-addregistro'),
									text : _s('Alarma'),
									handler : function() {
										t.crearNota(null, 2);
									}
								},{
									iconCls : 'cell-nota3',
									tooltip : _s('cmd-addregistro'),
									text : _s('Interno'),
									handler : function() {
										t.crearNota(null, 3);
									}
								},{
									iconCls : 'cell-nota4',
									tooltip : _s('cmd-addregistro'),
									text : _s('Envio'),
									handler : function() {
										t.crearNota(null, 4);
									}
								}]
							}, {
								tooltip : _s('cmd-delregistro'),
								text : _s('Eliminar'),
								iconCls : 'icon-delete',
								handler : fndelete
							}]
						}),

						viewConfig : {
							enableRowBody : true,
							getRowClass : function(r, rowIndex, rowParams, store) {
								return 'cell-nota' + r.data.nIdTipoObservacion;
							}
						}
					});

					this.grid.on('keydown', function(e) {
						if(e.getKey() == e.DELETE && !this.grid.editing) {
							fndelete();
						}
					});
					this.grid.on('dblclick', function(e) {
						var sm = t.grid.getSelectionModel();
						if(sm.hasSelection()) {
							var sel = sm.getSelected();
							fnedit(sel.data.id, sel.data.tipo);
						}
					});
					t.tiponota.store.load();
					return this.grid;
				}
			}
		},
		/**
		 * Crea un form genérico de gestión de datos
		 */
		formGeneric : function() {
			return {
				/**
				 * Estado de los datos
				 */
				dirty : false,
				/**
				 * ID del registro
				 */
				id : null,
				/**
				 *  ID del form
				 */
				idform : null,
				/**
				 * Título del form
				 */
				title : null,
				/**
				 * Icono de form
				 */
				icon : null,
				/**
				 * URL de la búsqueda
				 */
				url_search : null,
				/**
				 * URL del load
				 */
				url_load : null,
				/**
				 * URL del save
				 */
				url_save : null,
				/**
				 * URL del delete
				 */
				url_delete : null,
				/**
				 * URL del print
				 */
				url_print : null,
				/**
				 * URL del send
				 */
				url_send: null,
				/**
				 * Muestra el botón nuevo
				 */
				action_new: true,
				/**
				 * Controles vinculados a datos
				 */
				controls : new Array(),
				/**
				 * Tabs
				 */
				tabs : [],
				/**
				 * Herramientas
				 */
				tools : [],
				/**
				 * Acciones
				 */
				actions : [],
				/**
				 * Comandos
				 */
				commands : [],
				/**
				 * KeyMaps
				 */
				keymaps : [],
				/**
				 * Stores
				 */
				stores : [],
				/**
				 * Funcion de enable/disable de herramientas
				 * @param {Object} config
				 */
				fn_enable_disable : null,
				/**
				 * Funcion de Load
				 * @param {Object} config
				 */
				fn_load : null,
				/**
				 * Función de reset
				 * @param {Object} config
				 */
				fn_reset : null,
				/**
				 * Funcion de idioma
				 * @param {Object} config
				 */
				fn_lang : null,
				/**
				 * Función de save
				 * @param {Object} config
				 */
				fn_save : null,
				/**
				 * Título del formulario
				 * @param {Object} config
				 */
				title2 : '',
				/**
				 * Inicializa el formulario
				 * @param {Object} config
				 */
				init : function(config) {
					this.idform = config['id'];
					this.title = config['title'];
					this.icon = config['icon'];

					this.fn_load = config['fn_load'];
					this.fn_reset = config['fn_reset'];
					this.fn_save = config['fn_save'];
					this.fn_enable_disable = config['fn_enable_disable'];
					this.fn_lang = config['fn_lang'];
					if(config['action_new'] != null) this.action_new = config['action_new'];

					if(config['url'] != null) {
						this.url_search = config['url'] + '/search';
						this.url_load = config['url'] + '/get'
						this.url_save = config['url'] + '/upd'
						this.url_delete = config['url'] + '/del'
						this.url_print = config['url'] + '/printer'
						this.url_send = config['url'] + '/send'
					} else {
						this.url_search = config['url_search'];
						this.url_load = config['url_load'];
						this.url_save = config['url_save'];
						this.url_delete = config['url_delete'];
						this.url_print = config['url_print'];
						this.url_send = config['url_send'];
					}
				},
				/**
				 * Actualiza el ID
				 * @param {Object} _id
				 */
				setId : function(_id) {
					this.id = _id != null ? parseInt(_id) : null;
					this.setTitle(this.id);
				},
				/**
				 * Cambia el título del formulario
				 * @param {Object} title
				 */
				setTitle : function(title) {
					var f = Ext.getCmp(this.idform);
					this.title2 = this.title + ((title != null) ? ' ' + title : '');
					f.setTitle((this.dirty?'*':'') + this.title2);
				},
				/**
				 * Devuelve el Id
				 */
				getId : function() {
					return (this.id != null) ? parseInt(this.id) : null;
				},
				/**
				 * Comprueba si se han tocado los datos
				 */
				isDirty : function() {
					return this.dirty;
				},
				/**
				 * Cambia el estado de datos modificados del formulario
				 * @param {Object} st
				 */
				setDirty : function(st) {
					this.dirty = (st != null) ? st : true;
					this.refreshStatus();
					var ctl = Ext.getCmp(this.idform + '_id');
					var id = this.getId();
					if(ctl != null) {
						ctl.setValue((this.dirty ? '*' : '') + ((id != null) ? id : ''));
						// Cambia el estilo si es nuevo, modificado o normal
						// Hay ID:
						var cls = (id != null) ? ((this.dirty) ? 'reg-upd-dirty' : 'reg-upd-no-dirty') : ((this.dirty) ? 'reg-add-dirty' : 'reg-add-no-dirty');
						ctl.removeClass('reg-upd-dirty');
						ctl.removeClass('reg-upd-no-dirty');
						ctl.removeClass('reg-add-dirty');
						ctl.removeClass('reg-add-no-dirty');
						ctl.addClass(cls);
					}
					var f = Ext.getCmp(this.idform);
					f.setTitle((this.dirty?'*':'') + this.getTitle());
				},
				/**
				 * Actualiza el estado de los botones de acción
				 */
				refreshStatus : function() {
					this.enableButton(this.idform + '_btndel', this.getId() != null);
					this.enableButton(this.idform + '_btnrefresh', this.getId() != null);
					this.enableButton(this.idform + '_btnprint', this.getId() != null);
					this.enableButton(this.idform + '_btnsave', this.dirty || (this.getId() != null));
					if(this.fn_enable_disable)
						this.fn_enable_disable(this);
				},
				/**
				 * Habilita/deshabilita un botón
				 */
				enableButton : function(id, enable) {
					var ctl = Ext.getCmp(id);
					if(ctl != null) {
						if(enable)
							ctl.enable();
						else
							ctl.disable();
					}
				},
				/**
				 * Devuelve los TABS
				 */
				getTabs : function() {
					return this.tabs;
				},
				/**
				 * Código de autocompletado
				 */
				searchControl : function() {
					var me = this;

					var s = Ext.app.autocomplete({
						url : this.url_search,
						fieldLabel : _s('Código'),
						name : this.idform + '_search',
						id : this.idform + '_search',
						fnselect : function(id) {
							if(id != me.getId())
								me.load(id);
						}
					});
					s['x'] = Ext.app.TAM_COLUMN_ID * 2;
					s['anchor'] = '88%';
					s['y'] = 2;

					return new Ext.form.ComboBox(s);
				},
				/**
				 * Rellena el formulario con los datos
				 * @param {Object} res
				 */
				setData : function(res, partial) {
					// Copia los valores del JSON a los campos
					try {
						for(var k in this.controls) {
							k = this.controls[k];
							var ctl = Ext.getCmp(k);
							if((ctl != null && ctl.getName != null)) {
								var name = ctl.getName();
								var subname = [];
								try {
									if(name != null)
										subname = name.split('[');
								} catch (e) {
								}
								var val = null;
								if(subname.length > 1) {
									val = res.value_data[subname[0]][subname[1].replace(']', '')];
									if(val == null)
										val = res.value_data[subname[0]]['_' + subname[1].replace(']', '')]
								} else {
									val = res.value_data[name];
									if(val == null)
										val = res.value_data['_' + name]
								}
								if(val != null) {
									if(ctl.xtype == 'datefield') {
										val = new Date(NumberToDate(val));
									} else if(ctl.xtype == 'xdatetimetext') {
										val = Ext.app.renderDate(new Date(NumberToDate(val)));
									}
								}
								if((partial !== true) || (partial === true && val != null)) {
									ctl.originalValue = val;
									ctl.reset();
								}
							}
						}

						for(var k in res.value_data) {
							var ctl = Ext.getCmp(this.idform + 'fld_' + k);
							if((ctl != null)) {
								var val = res.value_data[k];
								if(res.value_data[k] != null) {
									if(ctl.xtype == 'datefield') {
										val = new Date(NumberToDate(res.value_data[k]));
									} else if(ctl.xtype == 'xdatetimetext') {
										val = Ext.app.renderDate(new Date(NumberToDate(res.value_data[k])));
									} /*else if (ctl.xtype == 'checkbox') {
										val = (val=='0')?false:((val=='1')?true:val);
										console.log(ctl.name + ' ' + val);
										ctl.checked = val;
									}*/
								}
								ctl.originalValue = val;
								ctl.reset();
							}
						}

						// Refresh
						//this.refreshStatus();
						// Datos limpios
						this.setDirty(false);
					} catch (e) {
					}
				},
				/**
				 * Mask del formulario
				 * @param {Object} text
				 */
				mask : function(text) {
					if(text == null)
						text = _s('Cargando');
					try {
						Ext.getCmp(this.idform).getEl().mask(text, 'x-mask-loading');
					} catch (e) {
					}
				},
				/**
				 * Quita el mask del formulario
				 */
				unmask : function() {
					try {
						Ext.getCmp(this.idform).getEl().unmask();
					} catch (e) {
					}
				},
				_tip : null,
				tip : function(text) {
					var me = this;
					var el = Ext.getCmp(this.idform).getEl();
					if(this._tip == null) {
						this._tip = new Ext.ToolTip({
							target : el,
							autoHide : false,
							//anchor: 'top',
							iconCls : 'icon-alert',
							anchorOffset : 85, // center the anchor on the tooltip
							html : text
						});
					} else {
						this._tip.hide();
						this._tip.update(text);
					}
					this._tip.showBy(el);
				},
				/**
				 * Carga los datos
				 * @param {Object} _id
				 */
				load : function(_id, fn) {
					var f = this;
					this.checkDirty(function() {
						// Almacena el Id antiguo
						var old_id = f.getId();
						// Llama al servidor para leer los datos. Si OK,
						// rellena
						// los campos, sino no hace nada
						f.mask();
						f.setId(_id);
						Ext.app.callRemote({
							url : f.url_load,
							params : {
								cmpid : f.idform,
								id : parseInt(_id),
								relation : true
							},
							fnok : function(res) {
								// Actualiza los campos
								f.reset(false);
								f.setData(res);
								if(f.fn_load != null)
									f.fn_load(_id, res.value_data);
								f.refreshStatus();
								f.unmask();
								if(fn)
									fn(true);
							},
							fnnok : function() {
								f.setId(old_id);
								f.unmask();
								if(fn)
									fn(false);
							}
						})
					});
				},
				/**
				 * Recarga los datos
				 */
				refresh : function() {
					var id = this.getId();
					if(this.id != null) {
						this.load(id);
					}
				},
				/**
				 *  Envia los datos al servidor
				 */
				save : function(fn, refresh) {
					// Prepara los valores del formulario
					try {
						var params = {};
						var ok = true;
						var miss = '';
						for(var i = 0; i < this.controls.length; i++) {
							var id = this.controls[i];
							var ctl = Ext.getCmp(id);
							if(ctl != null) {
								if(!ctl.readOnly) {
									if(ctl.isValid != null) {
										if(ctl.isValid(false)) {
											if(ctl.isDirty() || ctl.originalValue != ctl.getValue()) {
												if(ctl.xtype == 'datefield') {
													var d = ctl.getValue();
													try {
														params[ctl.name] = DateToNumber(d.getTime());
													} catch (e) {
													}
												} else {
													var v = ctl.getValue()
													//if (v != null) {
													params[ctl.name] = v;
													//}
												}
											}
										} else {
											ok = false;
										}
									}
								}
							}
						}

						// ¿Error?
						if(!ok) {
							Ext.app.msgFly(this.title, _s('mensaje_faltan_datos'));
							return;
						}
						// Añade el ID si se está editando
						if(this.id != null) {
							params['id'] = this.getId();
						}
						// Toma datos del formulario
						if(this.fn_save != null) {
							params = this.fn_save(this.getId(), params);
							if(params === false) {
								this.unmask();
								return;
							}
						}

						// Envía los datos al controlador
						this.mask(_s('save-register'));
						var f = this;
						Ext.app.callRemote({
							url : this.url_save,
							params : params,
							fnok : function(res) {
								f.unmask();
								f.setDirty(false);
								if(refresh !== false)
									f.load(parseInt(res.id), fn)
								else {
									f.setId(res.id);
									fn(res);
								}
							},
							fnnok : function() {
								f.unmask();
								if(fn)
									fn(false);
							}
						});
					} catch (e) {
					}
				},
				/**
				 * Comprueba si se han modificado los datos y pregunta si se quieren perder
				 * @param {Object} fn
				 */
				checkDirty : function(fn) {
					if(this.dirty) {
						var t = this;
						Ext.Msg.show({
							title : this.title,
							buttons : Ext.MessageBox.YESNOCANCEL,
							msg : _s('register-dirty-lost'),
							fn : function(btn, text) {
								if(btn == 'yes') {
									t.setDirty(false);
									fn();
								}
							}
						});
					} else {
						fn();
					}
				},
				/**
				 * Limpia el formulario
				 * @param {Object} id_also
				 */
				reset : function(id_also) {
					var f = this;
					this.checkDirty(function() {
						for(var i = 0; i < f.controls.length; i++) {
							var id = f.controls[i];
							var ctl = Ext.getCmp(id);
							if(ctl != null) {
								try {
									if(ctl.xtype == 'checkbox')
										ctl.checked = ctl.initialConfig.checked;
									else
										ctl.originalValue = null;
									ctl.reset();
								} catch (e) {

								}
							}
						}
						if(f.fn_reset != null)
							f.fn_reset();
						if(id_also !== false) {
							var s = Ext.getCmp(f.idform + '_search');
							s.reset();
							s.getStore().removeAll();
							f.setId(null);
							f.setDirty(false);
						}
					});
				},
				/**
				 * Elimina el registro actual del formulario
				 */
				del : function() {
					var f = this;
					if(this.getId() != null) {
						Ext.app.callRemoteAsk({
							url : this.url_delete,
							title : this.title,
							timeout: false,
							askmessage : _s('elm-registro'),
							waitmessage : _s('Eliminando'),
							errormessage : _s('registro-error'),
							params : {
								id : this.getId()
							},
							fnok : function() {
								f.setDirty(false);
								f.reset();
							}
						});
					}
				},
				/**
				 * Añade una acción al menú de acciones
				 * @param {Object} action
				 */
				addAction : function(action) {
					var i = this.actions.length;
					if (action.id == this.idform + 'btn_enviar')
						action.menu = [];
					this.actions[i] = action;
				},
				/**
				 * Añade una herramienta al menú de herramientas
				 * @param {Object} tool
				 */
				addTools : function(tool) {
					var i = this.tools.length;
					this.tools[i] = tool;
				},
				/**
				 * Añade una herramienta a la barra de comandos principal
				 * @param {Object} tool
				 */
				addCommand : function(tool) {
					var i = this.commands.length;
					this.commands[i] = tool;
				},
				/**
				 * Añade una herramienta a la barra de comandos principal
				 * @param {Object} tool
				 */
				addKeyMap : function(keymap) {
					var i = this.keymaps.length;
					this.keymaps[i] = keymap;
				},
				/**
				 * Añade los controles al formulario
				 * @param {Object} controls
				 */
				addControls : function(controls) {
					var f = this;
					var c2 = new Array();
					for(var i = 0; i < controls.length; i++) {
						if(controls[i]['xtype'] == 'compositefield' || controls[i]['xtype'] == 'fieldset') {
							controls[i]['items'] = this.addControls(controls[i]['items']);
							//var c = new Ext.form.Field(controls[i]);
							//c2.push(c);
						} else {
							if(is_array(controls[i])) {
								if(controls[i].id != null) {
									var name = this.idform + 'fld_' + controls[i].id;
									controls[i]['name'] = controls[i].id;
									controls[i].id = name;
									controls[i]['listeners'] = {
										change : function() {
											f.setDirty();
										}
									};
									if(controls[i]['xtype'] == 'combo') {
										var c = new Ext.form.ComboBox(controls[i]);
										name = c.getId();
										this.stores.push(name);
										controls[i] = c;
									}
									//c2.push(c);
									this.controls.push(name);
								}
							} else {
									controls[i].on('change', function() {
									f.setDirty();
								});
								this.controls.push(controls[i].getId());
							}
						}
					}
					//return c2;
					return controls;
				},
				/**
				 * Añade un Tab
				 * @param {Object} tab
				 */
				addTab : function(tab) {
					var i = this.tabs.length;
					tab['frame'] = true;
					tab['layout'] = 'fit';
					tab['closable'] = false;
					this.tabs[i] = tab;
				},
				/**
				 * Añade un TAB genérico para notas
				 */
				addTabNotas : function() {
					this.addTab({
						title : _s('Notas'),
						iconCls : 'icon-notes'
					});
				},
				/**
				 * Añade un TAB genérico para documentos
				 */
				addTabDocumentos : function() {
					this.addTab({
						title : _s('Documentos'),
						iconCls : 'icon-documents'
					});
				},
				// Añade un TAB genérico para búsquedas
				addTabBuscar : function() {
					this.addTab({
						title : _s('Buscar'),
						iconCls : 'icon-search'
					});
				},
				// Añade un TAB genérico para aditar usuarios de datos
				addTabUser : function() {
					var form = this;
					var controls = [{
						xtype : 'xdatetimetext',
						id : 'dCreacion',
						width : Ext.app.TAM_COLUMN_DATE * 2,
						allowBlank : true,
						readOnly : true,
						renderer : Ext.app.renderDate,
						fieldLabel : _s('dCreacion')
					}, {
						xtype : 'textfield',
						id : 'cCUser',
						anchor : '90%',
						allowBlank : true,
						readOnly : true,
						fieldLabel : _s('cCUser')

					}, {
						xtype : 'xdatetimetext',
						id : 'dAct',
						width : Ext.app.TAM_COLUMN_DATE * 2,
						allowBlank : true,
						readOnly : true,
						fieldLabel : _s('dAct')
					}, {
						xtype : 'textfield',
						id : 'cAUser',
						anchor : '90%',
						allowBlank : true,
						readOnly : true,
						fieldLabel : _s('cAUser')
					}];
					form.addTab({
						title : _s('Usuarios'),
						iconCls : 'icon-users',
						items : {
							xtype : 'panel',
							layout : 'form',
							items : form.addControls(controls)
						}
					});
				},
				/**
				 * Selecciona un TAB
				 * @param {int} id Número de TAB
				 */
				selectTab : function(id) {
					var tab = Ext.getCmp(this.idform + '_tab');
					if(tab != null)
						tab.setActiveTab(id);
				},
				/**
				 * Esconde un TAB
				 * @param {int} id Número de TAB
				 */
				showTab : function(id, visible) {
					var tab = Ext.getCmp(this.idform + '_tab');
					if(tab != null) {
						tab.unhideTabStripItem(id);
					}
				},
				/**
				 * Esconde un TAB
				 * @param {int} id Número de TAB
				 */
				hideTab : function(id, visible) {
					var tab = Ext.getCmp(this.idform + '_tab');
					if(tab != null) {
						tab.hideTabStripItem(id);
					}
				},
				default_print : null,
				preview : Ext.app.get_config('reports.preview') == 'true',
				send : function(id, lang, fn, select) {
					var f = this;
					Ext.app.callRemote({
						url : this.url_send,
						params : {
							id : f.getId(),
							lang : lang,
							report : id,
							select: select,
							email : true,
							fax : true
						},
						fnok : function(res) {
							if(res.success)
							{
								if (fn != null)
									fn(res);
								else
									f.refresh();
							}				
						}
					});
				},

				print : function(id, lang) {
					var f = this;
					if(lang == null) {
						lang = (f.fn_lang != null) ? f.fn_lang() : langs[0];
					}
					if(id == null) {
						id = f.default_print;
					}
					if(this.getId() != null) {
						try {
							Ext.app.callRemote({
								url : this.url_print,
								timeout : false,
								params : {
									id : this.getId(),
									title : f.title2,
									lang : lang,
									preview : f.preview,
									report : id
								}
							});
						} catch (e) {
						}
					}
				},
				getTitle : function() {
					return this.title2;
				},
				search : function(text, where, users) {
					var f = this;
					var m = ['id', 'text'];
					var m2 = [{
						name : 'dCreacion'
					}, {
						name : 'dAct'
					}, {
						name : 'cCUser'
					}, {
						name : 'cAUser'
					}];

					var model = (users == true) ? m.concat(m2) : m;
					var c = [{
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
					var c2 = [{
						header : _s('cCUser'),
						width : Ext.app.TAM_COLUMN_TEXT,
						dataIndex : 'cCUser',
						sortable : true
					}, {
						header : _s('dCreacion'),
						width : Ext.app.TAM_COLUMN_DATE,
						dateFormat : 'timestamp',
						renderer : Ext.app.renderDate,
						dataIndex : 'dCreacion',
						sortable : true
					}, {
						header : _s('cAUser'),
						width : Ext.app.TAM_COLUMN_TEXT,
						dataIndex : 'cAUser',
						sortable : true
					}, {
						header : _s('dAct'),
						width : Ext.app.TAM_COLUMN_DATE,
						dateFormat : 'timestamp',
						renderer : Ext.app.renderDate,
						dataIndex : 'dAct',
						sortable : true
					}];
					var columns = (users == true) ? c.concat(c2) : c;
					var store = Ext.app.getStore(f.url_search, model, false, true);
					var fn = function() {
						if((parseInt(store.getTotalCount()) > 1)) {

							var listView = Ext.app.createGrid({
								store : store,
								columns : columns,
								title : _s('Búsqueda de registros'),
								mode : 'search',
								fn_open : function(id) {
									f.load(id);
									form.close();
								}
							});

							listView.setHeight(Ext.app.FORM_SEARCH_HEIGHT);

							var fn_ok = function() {
								var sm = listView.getSelectionModel();
								if(sm.hasSelection()) {
									var sel = sm.getSelected();
									f.load(sel.data.id);
								}
							};
							var form = Ext.app.formStandarForm({
								controls : [listView],
								height : Ext.app.FORM_SEARCH_HEIGHT,
								fn_ok : fn_ok
							});

							listView.on('dblclick', function(view, index) {
								var sm = t.grid.getSelectionModel();
								if(sm.hasSelection()) {
									var sel = sm.getSelected();
									form.close();
									f.load(sel.data.id);
								}
							});
							form.show();
						} else {
							if(store.getTotalCount() > 0) {
								var v = store.getAt(0);
								f.load(v.data.id);
							}
							else {
								Ext.app.msgInfo(f.getTitle(), _s('registros no encontrados'));
							}
						}
					}
					store.load({
						params : {
							query : text,
							start : 0,
							limit : Ext.app.PAGESIZE,
							sort : 'dCreacion',
							dir : 'DESC',
							where : where
						},
						callback : fn
					});
				},
				/**
				 * Crea el formulario
				 */
				form : function(open_id) {
					var f = this;
					var sc = this.searchControl();

					var tbar = [];
					// Añade las acciones
					if(this.actions.length > 0) {
						tbar[0] = {
							xtype : 'tbbutton',
							text : _s('Acciones'),
							iconCls : 'icon-actions',
							id : this.idform + '_btnactions',
							menu : this.actions
						};
						tbar[0].menu[i] = '-';
					}

					// Añade las herramientas
					if(this.tools.length > 0) {
						var tools = {
							text : _s('Herramientas'),
							iconCls : 'icon-tool',
							id : this.idform + 'alb_btntools',
							menu : this.tools
						}
						if(this.actions.length > 0) {
							var i = tbar[0].menu.length;
							tbar[0].menu[i] = '-';
							i = tbar[0].menu.length;
							tbar[0].menu[i] = tools;
						} else {
							tbar[0] = tools;
						}
					}
					if(this.commands.length > 0)
						tbar = tbar.concat(this.commands);

					var menuPrint = new Ext.menu.Menu({
						id : this.idform + '_menuPrint'
					});

					var langs = Ext.app.REPORTS_LANG;
					langs = (langs != '') ? langs.split(';') : null;
					
					if (this.action_new) {
						tbar[tbar.length] = {
							text : _s('Nuevo'),
							iconCls : 'icon-new',
							id : this.idform + '_btnnew',
							visible: this.action_new,
							handler : function() {
								f.reset();
							}
						}
						tbar[tbar.length] = '-';
					}

					var tbar2 = [{
						text : _s('Guardar'),
						iconCls : 'icon-save',
						id : this.idform + '_btnsave',
						handler : function() {
							f.save();
						}
					}, {
						text : _s('Refrescar'),
						iconCls : 'icon-refresh',
						id : this.idform + '_btnrefresh',
						handler : function() {
							f.refresh();
						}
					}, '-', new Ext.Toolbar.SplitButton({
						text : _s('Imprimir'),
						tooltip : _s('tooltip-imprimir'),
						iconCls : 'icon-print',
						id : this.idform + '_btnprint',
						menu : menuPrint,
						handler : function() {
							try {
								var lang = (f.fn_lang != null) ? f.fn_lang() : langs[0];
								f.print(f.default_print, lang);
							} catch (e) {
							}
						}
					}), '->', {
						text : _s('Eliminar'),
						iconCls : 'icon-delete',
						id : this.idform + '_btndel',
						handler : function() {
							f.del();
						}
					}, {
						iconCls : 'icon-help',
						handler : function() {
							Ext.app.help(f.title);
						}
					}];
					tbar = tbar.concat(tbar2);

					var form = {
						refresh : function() {
							f.refresh();
						},
						layout : 'border',
						application : f,
						title : this.title,
						id : this.idform,
						iconCls : this.icon,
						items : {
							baseCls : 'x-plain',
							layout : 'absolute',
							region : 'center',
							border : true,
							frame : true,
							xtype : 'panel',
							defaultType : 'textfield',

							items : [{
								x : 0,
								y : 2,
								width : Ext.app.TAM_COLUMN_ID,
								readOnly : true,
								xtype : 'textfield',
								id : this.idform + '_id'
							}, {
								x : Ext.app.TAM_COLUMN_ID + 2,
								y : 2,
								text : _s('Codigo'),
								width : Ext.app.TAM_COLUMN_ID,
								xtype : 'label'

							}, sc, {
								x : 0,
								y : 25,
								xtype : 'tabpanel',
								activeTab : 0,
								anchor : '100% 100%',
								id : this.idform + '_tab',
								items : this.getTabs()
							}]
						},
						region : 'center',
						closable : true,
						listeners : {
							beforeclose : function() {
								if (f.isDirty())
								{
									f.checkDirty(function() {
										f.setDirty(false);
										var f2 = Ext.getCmp(f.idform);
										f2.destroy();
									});
									return false;
								}								
							},
							afterrender : function(c) {
								// Aceleradores
								f.addKeyMap({
									key : Ext.app.KEYMAP_FORM_PRINT,
									ctrl : Ext.app.KEYMAP_FORM_CTRL,
									alt : Ext.app.KEYMAP_FORM_ALT,
									shift : Ext.app.KEYMAP_FORM_SHIFT,
									stopEvent : true,
									fn : function() {
										f.print(f.default_print, langs[0]);
									}
								});
								f.addKeyMap({
									key : Ext.app.KEYMAP_FORM_REFRESH,
									ctrl : Ext.app.KEYMAP_FORM_CTRL,
									alt : Ext.app.KEYMAP_FORM_ALT,
									shift : Ext.app.KEYMAP_FORM_SHIFT,
									stopEvent : true,
									fn : function() {
										f.refresh();
									}
								});
								f.addKeyMap({
									key : Ext.app.KEYMAP_FORM_SAVE,
									ctrl : Ext.app.KEYMAP_FORM_CTRL,
									alt : Ext.app.KEYMAP_FORM_ALT,
									shift : Ext.app.KEYMAP_FORM_SHIFT,
									stopEvent : true,
									fn : function() {
										f.save();
									}
								});
								if (this.action_new) {
									f.addKeyMap({
										key : Ext.app.KEYMAP_FORM_NEW,
										ctrl : Ext.app.KEYMAP_FORM_CTRL,
										alt : Ext.app.KEYMAP_FORM_ALT,
										shift : Ext.app.KEYMAP_FORM_SHIFT,
										stopEvent : true,
										fn : function() {
											f.reset();
										}
									});
								}

								var map = new Ext.KeyMap(c.getEl(), f.keymaps);
							},
							render : function(c) {
								c.application.refreshStatus();
								var fn = function(st) {
									var s = st.pop();
									if(s != null) {
										var ctl = Ext.getCmp(s);
										if(ctl != null) {
											ctl.store.load({
												callback : function() {
													fn(st);
												}
											});
										} else
											fn(st);
									} else {
										if((open_id == '') || (open_id == null))
											f.reset();
										else {
											f.search(open_id, null, true);
										}
									}
								}
								fn(f.stores);
								// Carga los impresos
								Ext.app.callRemote({
									url : f.url_print,
									cache : true,
									params : {
										list : true
									},
									fnok : function(res) {
										var send = Ext.getCmp(f.idform + 'btn_enviar');
										//console.dir(send);
										Ext.each(langs, function(lang) {
											var reports = [];
											var reports2 = [];
											if(res.value_data != null) {
												Ext.each(res.value_data, function(item) {
													var text = item.text;
													if(item['default']) {
														text = '<b>' + text + '</b>';
														f.default_print = item.id;
													}
													reports[reports.length] = {
														text : text,
														iconCls : 'icon-print-doc',
														handler : function() {
															f.print(item.id, lang);
														}
													}
													if (send!=null)
														reports2[reports2.length] = {
															text : text,
															iconCls : 'icon-send-doc',
															handler : function() {
																f.send(item.id, lang);
															}
														}
												});

												menuPrint.add({
													text : _s('language_' + lang),
													menu : reports,
													iconCls : 'lang-' + lang,
													handler : function() {
														f.print(f.default_print, lang);
													}
												});
												if (send != null) {
													send.menu.add({
														text : _s('language_' + lang),
														menu : reports2,
														iconCls : 'lang-' + lang,
														handler : function() {
															f.send(null, lang);
														}
													});													
												}
											} else {
												menuPrint.add({
													text : _s('language_' + lang),
													iconCls : 'lang-' + lang,
													handler : function() {
														f.print(null, lang);
													}
												});
												/*if (send != null) {
													send.add({
														text : _s('language_' + lang),
														iconCls : 'lang-' + lang,
														handler : function() {
															f.print(null, lang);
														}
													});													
												}*/
											}
										});
										menuPrint.add({
											text : _s('Vista Previa'),
											//iconCls: 'icon-preview',
											checked : (Ext.app.get_config('reports.preview') == 'true'),
											//enableToggle: true,
											checkHandler : function(item, checked) {
												f.preview = checked;
											}
										});
										/*if (send != null) {
											send.menu.add({
												text : _s('Seleccionar...'),
												menu : reports2,
												iconCls : 'icon-send',
												handler : function() {
													f.send(null, null, null, true);
												}
											});													
										}*/

										//console.dir(send);
									}
								});
							}
						},
						tbar : tbar
					}

					return form;
				},
				// Crea el formulario
				show : function(id) {
					return this.form(id);
				}
			}
		},
		/**
		 * Inicializa el entorno ExtJS
		 */
		initApp : function() {
			Ext.BLANK_IMAGE_URL = Ext.app.BLANK;
			Ext.QuickTips.init();
			Ext.app.combo_data = [['1', _s('bool_si')], ['0', _s('bool_no')]];
			Ext.form.Field.prototype.msgTarget = Ext.app.MSG_TAGET;
			Ext.app.initClipboard();
		},
		/**
		 * Control HTMLEditor
		 * @param {Object} config
		 */
		formHtmlEditor : function(config) {
			return [Ext.apply(config, {
				fieldLabel : config['label'],
				name : config['name'],
				id : config['id'],
				xtype : 'htmleditor',
				plugins : [new Ext.ux.form.HtmlEditor.Word(), new Ext.ux.form.HtmlEditor.Divider(), new Ext.ux.form.HtmlEditor.Table(), new Ext.ux.form.HtmlEditor.HR(), new Ext.ux.form.HtmlEditor.IndentOutdent(), new Ext.ux.form.HtmlEditor.SubSuperScript(), new Ext.ux.form.HtmlEditor.RemoveFormat()],
				anchor : config['anchor']
			})];
		},
		/**
		 * Formulario de nuevo password
		 */
		formNewPassword : function() {
			var id = Ext.app.createId();

			var controls = [{
				fieldLabel : _s('Anterior'),
				name : "old",
				id : id + '_o',
				inputType : 'password',
				width : 200,
				maxLength : 64,
				xtype : 'textfield',
				allowBlank : false
			}, {
				fieldLabel : _s('Nuevo'),
				id : id + '_n',
				name : "new",
				allowBlank : false,
				xtype : 'textfield',
				inputType : 'password',
				vtype : 'password',
				width : 200,
				maxLength : 64
			}, {
				fieldLabel : _s('Verificación'),
				id : id + '_v',
				name : "ver",
				xtype : 'textfield',
				inputType : 'password',
				vtype : 'password',
				allowBlank : false,
				width : 200,
				maxLength : 64,
				initialPasswordField : id + '_n'
			}];
			var url = site_url('user/auth/passwd');

			var form = Ext.app.formStandarForm({
				controls : controls,
				icon : 'icon-password',
				title : _s('cambiar_password'),
				url : url
			});
			form.show();

		},
		/**
		 * Crea un menú con los comandos de exportación de GRIDS
		 */
		menuUsuario : function() {
			return [{
				iconCls : "icon-portal",
				text : _s('Portal'),
				handler : function() {
					Ext.app.showPortal();

				}
			}, '-', {
				iconCls : "icon-login",
				text : _s('cmd-cambiar-sesion'),
				handler : function() {
					Ext.app.login(false, function() {
						Ext.app.auth_reload(true);
					}, _s('cmd-cambiar-sesion'));
				}
			}, {
				iconCls : "icon-recargar-perm",
				text : _s('Recargar permisos'),
				handler : function() {
					Ext.app.auth_reload(false);
				}
			}, {
				iconCls : "icon-password",
				text : _s('cambiar_password'),
				handler : function() {
					Ext.app.formNewPassword();
				}
			}, '-', {
				iconCls : "icon-reload",
				text : _s('reload_app'),
				handler : function() {
					Ext.app.reload();
				}
			}, '-', {
				iconCls : "iconoConfiguracion",
				text : _s('Configuración'),
				handler : function() {
					Ext.app.execCmd({
						url : site_url('sys/configuracion/user')
					});
				}
			}, '-', {
				iconCls : "iconoMensajes",
				text : _s('Mensajes'),
				handler : function() {
					Ext.app.execCmd({
						url : site_url('sys/mensaje/index')
					});
				}
			}, '-', {
				iconCls : "icon-exit",
				text : _s('cmd_salir'),
				handler : function() {
					Ext.app.exitApp();
				}
			}];
		},
		config_system : new Hash(),
		config_user : new Hash(),

		reload_constants : function() {
			/*Ext.app.DECIMALS = parseInt(Ext.app.get_config('bp.currency.decimals'));
			Ext.app.DEC_POINTS = Ext.app.get_config('bp.currency.dec_points');
			Ext.app.THOUSANDS_SET = Ext.app.get_config('bp.currency.thousands_sep');
			Ext.app.SYMBOL_LEFT = Ext.app.get_config('bp.currency.symbol_left');
			Ext.app.SYMBOL_RIGHT = Ext.app.get_config('bp.currency.symbol_right');*/
			Ext.app.APLICATION_TITLE = Ext.app.get_config('bp.application.name');
			Ext.app.DATEFORMATLONG = Ext.app.get_config('bp.date.formatlong');
			Ext.app.DATEFORMATSHORT = Ext.app.get_config('bp.date.format');
			Ext.app.TIMEFORMAT = Ext.app.get_config('bp.date.formattime');
			Ext.app.DATESTARTDAY = parseInt(Ext.app.get_config('bp.date.startday'));

			Ext.app.TIMEOUTREMOTECALL = parseInt(Ext.app.get_config('bp.application.timeout'));
			Ext.app.PAGESIZE = parseInt(Ext.app.get_config('bp.data.limit'));
			Ext.app.AUTOCOMPLETELISTSIZE = Ext.app.get_config('bp.data.search.limit');
			Ext.app.STAYALIVETIME = parseInt(Ext.app.get_config('bp.application.stayalive'));
			//Ext.app.FACTURAREFRESH = parseInt(Ext.app.get_config('bp.portal.portlets.facturasrefresh'));
			Ext.app.MARGEN_MINIMO = parseInt(Ext.app.get_config('bp.ventas.margenminimo'));
			Ext.app.NUM_CEROS_DOCUMENTOS = parseInt(Ext.app.get_config('bp.docs.ceros'));
			Ext.app.MSG_FLY_ALIGN = Ext.app.get_config('bp.application.fly.align');
			Ext.app.FLY_TIME = parseInt(Ext.app.get_config('bp.application.fly'));


			//Ext.app.PRINT_SERVER_HOST = Ext.app.get_config('bp.printerserver.host');
			//Ext.app.PRINT_SERVER_PORT = Ext.app.get_config('bp.printerserver.port');
			//Ext.app.DRAWER_SERVER_HOST = Ext.app.get_config('bp.drawerserver.host');
			//Ext.app.DRAWER_SERVER_PORT = Ext.app.get_config('bp.drawerserver.port');
			//Ext.app.LABEL_SERVER_HOST = Ext.app.get_config('bp.labelserver.host');
			//Ext.app.LABEL_SERVER_PORT = Ext.app.get_config('bp.labelserver.port');

			Ext.app.MENU_STYLE = Ext.app.get_config('bp.application.menustyle');
			Ext.app.HELP = Ext.app.get_config('bp.application.help');
			//Ext.app.PRINT_TICKET = Ext.app.get_config('bp.factura.ticket.print')  == 'true';
			//Ext.app.NEW_TICKET = Ext.app.get_config('bp.factura.ticket.nuevo')  == 'true';

			Ext.app.DEFAULT_PAIS = parseInt(Ext.app.get_config('bp.address.pais'));
			Ext.app.DEFAULT_REGION = parseInt(Ext.app.get_config('bp.address.region'));
			Ext.app.MSG_TAGET = Ext.app.get_config('bp.application.msgtarget');

			Ext.app.ITEMS_BUSQUEDA_ARTICULOS = parseInt(Ext.app.get_config('bp.articulos.busquedas.items'));

			Ext.app.REPORTS_LANG = Ext.app.get_config('reports.language');
			//Ext.app.PRINT_PREVIEW = Ext.app.get_config('reports.preview') == 'true';

			Ext.app.GRIDCOLUMNS_HIDE_FACTURACION = Ext.app.get_config('bp.grid.facturacion.hide');
			Ext.app.GRIDCOLUMNS_HIDE_TPV = Ext.app.get_config('bp.grid.tpv.hide');
			Ext.app.GRIDCOLUMNS_HIDE_ALBARANSALIDA = Ext.app.get_config('bp.grid.albaransalida.hide');
			Ext.app.GRIDCOLUMNS_HIDE_PEDIDOCLIENTE = Ext.app.get_config('bp.grid.pedidocliente.hide');

			Ext.app.GRIDCOLUMNS_HIDE_PEDIDOPROVEEDOR = Ext.app.get_config('bp.grid.pedidoproveedor.hide');
			Ext.app.GRIDCOLUMNS_HIDE_ALBARANENTRADA = Ext.app.get_config('bp.grid.albaranentrada.hide');
			Ext.app.GRIDCOLUMNS_HIDE_DEVOLUCION = Ext.app.get_config('bp.grid.devolucion.hide');

			Ext.app.SHOW_PORTADA_BUSCAR = Ext.app.get_config('catalogo.buscar.showportada', 'user') == 'true';
			Ext.app.PEDIDOS_ACTUALIZAR_PRECIOS = Ext.app.get_config('ventas.pedidocliente.actualizar') == 'true';
			Ext.app.FORMATOABONO = Ext.app.get_config('bp.abono.formatodefecto');

			Ext.app.PRINTETQGROUP = parseInt(Ext.app.get_config('compras.etiquetas.grupos'));

			Ext.app.SIMBOLODIVISA = Ext.app.get_config('bp.divisa.simbolo');
			Ext.app.DIVISA_DEFAULT = Ext.app.get_config('bp.divisa.default');
			Ext.app.TPV_CACHE = Ext.app.get_config('bp.cache.tpv')  == 'true';

			Ext.app.DIASPROMOCIONARTICULO = parseInt(Ext.app.get_config('bp.articulo.diaspromocion'));
			Ext.app.PROMOCIONWEB = Ext.app.get_config('bp.promocion.web');
		},
		/**
		 * Carga la configuración del usuario
		 */
		config_load : function(fn) {
			Ext.app.callRemote({
				url : site_url('sys/configuracion/config'),
				nomsg : true,
				fnok : function(res) {
					Ext.app.config_system = res.system;
					Ext.app.config_user = res.user;
					Ext.app.reload_constants();
					if (fn != null) fn();
				}
			});
		},
		/**
		 * Devuelve una valor de la configuración
		 * @param {String} item Id de la variable
		 * @param {String} mode terminal, user, system
		 * @param {bool} cascade Solo busca la variable del tipo, no en cascada
		 */
		get_config : function(item, mode, cascade) {
			if (cascade == null) cascade = true;
			if (item != null) {
				item = item.replace(/\s/g, '.');
				if (mode == 'terminal' || mode == null) {
					var b = localStorage.getItem(item);
					//console.log('LOAD: ' + item + ' -> ' + b);
					if(b != null && b != '') {
						return b;
					}
					return (cascade)?Ext.app.get_config(item, 'user'):new String('');
				}
				if (mode == 'user') {
					if (Ext.app.config_user[item] != null 
						&& Ext.app.config_user[item].toString() != '')
						return new String(Ext.app.config_user[item]);
					return (cascade)?Ext.app.get_config(item, 'system'):new String('');
				}
				if (mode == 'system') {
					if(Ext.app.config_system[item] != null)
						return new String(Ext.app.config_system[item]);
					return new String('');
				}
			}
			return Ext.app.config_system;
		},
		/**
		 * Asigna un valor a la configuración
		 * @param {Object} item
		 */
		set_config : function(item, value, mode) {
			item = item.replace(/\s/g, '.');
			if (mode == 'terminal') {
				//console.log('SAVE: ' + item + ' -> ' + value);
				localStorage.setItem(item, value);
			}
			if (mode == 'user'  || mode == null) {
				Ext.app.config_user[item] = value;
				// Guarda en el sistema
				Ext.app.callRemote({
					url : site_url('sys/configuracion/set'),
					nomsg: true,
					params : { 'var': item, value: value, type: 'user'}
				});			
			}
			if (mode == 'system') {
				Ext.app.config_system[item] = value;
			}
			// Recarga las constantes
			//Ext.app.reload_constants();
		},
		/**
		 * Carga los permisos
		 * @param {Object} nomsg
		 */
		auth_reload : function(nomsg, fn) {
			Ext.app.callRemote({
				url : site_url('user/auth/auth_reload'),
				nomsg : nomsg,
				fnok : function(res) {
					Ext.app.permisos = res.auth;
				}
			});
			Ext.app.config_load(fn);
		},
		/**
		 * Ejecuta la aplicación en modo completo
		 */
		runApp : function() {
			Ext.app.initApp();

			try {
				//Ext.ux.form.HistoryClearableComboBox
				var comandosstore = new Ext.data.ArrayStore({
					fields : ['cmd'],
					data : []
				});
				CmdRecord = Ext.data.Record.create({
					name : 'cmd'
				});

				comandosstore.add(new CmdRecord({
					cmd : 'help'
				}));
				var comandos = new Ext.form.ComboBox({
					//emptyText: _s('Comando'),
					cls : 'cmd-field',
					store : comandosstore,
					mode : 'local',
					displayField : 'cmd',
					valueField : 'cmd',
					autoSelect : false,
					enableKeyEvents : true,
					width : (Ext.app.MENU_STYLE == 'treebar') ? 220 : 400,
					anchor : '100%',
					triggerAction : 'all',
					listeners : {
						keypress : function(f, e) {
							if(e.getKey() == e.ENTER) {
								var c = trim(f.getRawValue());
								if(c != '') {
									var cmd = Ext.app.prepareCmd(c);
									//cmd = cmd.toLowerCase();
									f.clearValue();
									if(cmd != '') {
										var i = comandosstore.find('cmd', c);
										if(i > -1)
											comandosstore.removeAt(i)
										comandosstore.add(new CmdRecord({
											cmd : c
										}));
										var url = "index.php/" + cmd;
										Ext.app.callRemote({
											url : url,
											params : {
												id : Ext.app.createId()
											},

											wait : false,
											timeout : false,
											success : function(xhr) {
												Ext.app.analizeResponse({
													xhr : xhr,
													fly : true
												});
											},
											failure : function(xhr) {
												Ext.app.analizeResponse({
													xhr : xhr
												});
											}
										});
									}
								}
							}
						}
					}
				});

				var plugins = [];
				if(Ext.app.TAB_SCROLLER_MENU) {
					plugins[plugins.length] = new Ext.ux.TabScrollerMenu({
						maxText : 15,
						pageSize : 5
					});
				}

				if(Ext.app.TAB_CLOSE_MENU) {
					plugins[plugins.length] = new Ext.ux.TabCloseMenu({
						closeTabText : _s('Cerrar'),
						closeOtherTabsText : _s('Cerrar otras'),
						closeAllTabsText : _s('Cerrar todas')
					});
				}

				var tabControl = ( {
					region : 'center',
					slideDuration : .15,
					xtype : 'slidingtabpanel',
					id : 'Tabs',
					enableTabScroll : true,
					plugins : (plugins.length > 0) ? plugins : null,
					activeTab : 0
				});

				function switchTabs(e) {
					// note the items.items - no idea why
					tabs = Ext.app.tab;
					var items = tabs.items.items;
					// grab the active tab
					var active_tab = tabs.getActiveTab();
					// grab the total number of tabs
					var total_tabs = items.length;

					// loop the tabs
					for( i = 0; i < items.length; i++) {
						// find the active tab based on the id property.
						if(active_tab.id == items[i].id) {
							// do we want to move left?
							if(e == Ext.EventObject.LEFT) {
								// move left
								var next = (i - 1)
								if(next < 0) {
									// we're at -1, set to last tab
									next = (total_tabs - 1);
								}
							} else {
								// move right
								var next = (i + 1);
								if(next >= total_tabs) {
									// we've gone 1 too many set to start position.
									next = 0;
								}
							}
							// set the tab and return there's no need to carry on
							tabs.setActiveTab(items[next].id);
							return;
						}
					}
				}

				var map = new Ext.KeyMap(document, {
					key : [Ext.EventObject.LEFT, Ext.EventObject.RIGHT],
					alt : true,
					/*shift : true,*/
					/*ctrl: false,*/
					stopEvent : true,
					fn : switchTabs
				});

				var items = null;

				if(Ext.app.MENU_STYLE == 'treebar') {
					// Tipo de aplicación con menú en forma de árbol
					var menutree = {
						region : 'west',
						collapseFirst : true,
						collapsible : true,
						collapseMode : 'mini',
						title : _s('mnu-principal'),
						titleCollapse : _s('mnu-principal'),
						tabTip : _s('mnu-principal'),
						xtype : 'treepanel',
						id : 'TreeMenu',
						//loadMask: true,
						iconCls : 'iconoMenu',
						width : 225,
						autoScroll : true,
						split : true,
						useArrows : true,
						animate : true,
						rootVisible : false,
						listeners : {
							click : function(node, e) {
								if(node.leaf == true) {
									Ext.app.execCmd({
										id : node.id,
										timeout : false,
										title : node.text,
										icon : node.attributes.iconCls + 'Tab'
									});
								}
							}
						},
						bbar : [comandos],
						tbar : [{
							tooltip : _s('cmd-expandir'),
							iconCls : 'iconoExpandir',
							listeners : {
								click : function() {
									var x = Ext.getCmp('TreeMenu');
									x.expandAll();
								}
							}
						}, {
							tooltip : _s('cmd-contraer'),
							iconCls : 'iconoContraer',
							listeners : {
								click : function() {
									var x = Ext.getCmp('TreeMenu');
									x.collapseAll();
								}
							}
						}, {
							tooltip : _s('Ayuda'),
							iconCls : 'iconoAyuda',
							listeners : {
								click : function() {
									var T = Ext.getCmp('Tabs');
									T.setActiveTab('help-tab');
								}
							}
						}],
						loader : new Ext.tree.TreeLoader({
							//loadMask: true,
							dataUrl : site_url('sys/app/menu')
						}),
						root : new Ext.tree.AsyncTreeNode({
							expanded : true
						})
					}

					var statusbar = {
						region : 'south',
						xtype : 'statusbar',
						id : 'statusbar',
						height : '20',
						defaultText : _s('Listo'),

						items : [{
							xtype : 'label',
							html : '<b>' + _s('server') + ':</b>&nbsp;'
						}, {
							xtype : 'label',
							id : 'm_hostname',
							text : ''
						}, {
							xtype : 'label',
							html : '.'
						}, {
							xtype : 'label',
							id : 'm_database',
							text : ''
						}, {
							xtype : 'label',
							html : '.'
						}, {
							xtype : 'label',
							id : 'm_username',
							text : ''
						}, {
							xtype : 'tbbutton',
							iconCls : "icon-user",
							menu : Ext.app.menuUsuario()
						}]
					}
					var center = tabControl;
					items = [menutree, center, statusbar];
				} else if(Ext.app.MENU_STYLE == 'taskbar') {
					// Tipo de aplicación con menú en forma de botón de tareas
					var startMenu = new Ext.ux.StartMenu({
						iconCls : 'iconoMenuTaskBar',
						height : Ext.app.TASKBAR_HEIGHT,
						shadow : true,
						title : _s('mnu-principal'),
						width : Ext.app.TASKBAR_WIDTH,
						panelWidth : Ext.app.TASKBAR_PANEL_WIDTH
					});

					var startBtn = new Ext.Button({
						text : _s('Menú'),
						id : 'ux-startbutton',
						iconCls : 'start',
						menu : startMenu,
						menuAlign : 'bl-tl',
						clickEvent : 'mousedown',
						template : new Ext.Template('<table cellspacing="0" class="x-btn"><tbody class="{1}"><tr>', '<td class="ux-startbutton-left"><i>&#160;</i></td>', '<td class="ux-startbutton-center"><em class="{2} unselectable="on">', '<button class="x-btn-text" type="{0}" style="height:30px;"></button>', '</em></td>', '<td class="ux-startbutton-right"><i>&#160;</i></td>', '</tr></tbody></table>')
					});
					createMenuFunction(startMenu);
					startMenu.add({
						text : _s('Ayuda'),
						iconCls : 'iconoAyuda',
						listeners : {
							click : function() {
								Ext.app.showHelp();
							}
						}
					});
					var menuUsuario = Ext.app.menuUsuario();
					Ext.each(menuUsuario, function(item) {
						startMenu.addTool(item);
					});
					var bbar = [startBtn, '-', comandos, '->', {
						xtype : 'label',
						html : '<b>' + _s('Servidor') + ':</b>&nbsp;'
					}, {
						xtype : 'label',
						id : 'm_hostname',
						text : ''
					}, {
						xtype : 'label',
						html : '.'
					}, {
						xtype : 'label',
						id : 'm_database',
						text : ''
					}, {
						xtype : 'label',
						html : '.'
					}, {
						xtype : 'label',
						id : 'm_username',
						text : ''
					}];

					var center = tabControl;
					center['bbar'] = bbar;
					items = [center];
				} else/*if (Ext.app.MENU_STYLE == 'menubar')*/
				{
					//createMenuFunction(startMenu);
					//startMenu.add();
					var tb = new Ext.Toolbar({
						enableOverflow : true
					});
					createMenuFunction(tb);
					tb.add('->');
					tb.add({
						xtype : 'tbbutton',
						iconCls : "icon-user",
						text : _s('Usuario'),
						menu : Ext.app.menuUsuario()
					});
					tb.add({
						text : _s('Ayuda'),
						iconCls : 'iconoAyuda',
						listeners : {
							click : function() {
								Ext.app.showHelp();
							}
						}
					});
					var tbar = [{
						text : _s('Ayuda'),
						iconCls : 'iconoAyuda',
						listeners : {
							click : function() {
								Ext.app.showHelp();
							}
						}
					}, '->', {
						xtype : 'tbbutton',
						iconCls : "icon-user",
						menu : Ext.app.menuUsuario()
					}];
					var bbar = [comandos, '->', {
						xtype : 'label',
						html : '<b>' + _s('Servidor') + ':</b>&nbsp;'
					}, {
						xtype : 'label',
						id : 'm_hostname',
						text : ''
					}, {
						xtype : 'label',
						html : '.'
					}, {
						xtype : 'label',
						id : 'm_database',
						text : ''
					}, {
						xtype : 'label',
						html : '.'
					}, {
						xtype : 'label',
						id : 'm_username',
						text : ''
					}];

					var north = {
						region : 'north',
						//xtype: 'tool',
						height : 30,
						items : [tb]
					}

					var center = tabControl;
					center['bbar'] = bbar;
					items = [north, center];
				}
				/*Ext.Ajax.on('beforerequest', function(){
				 jQuery(document.body).loading({
				 align: 'top-left',
				 text: _s('sending-data'),
				 effect: 'update',
				 mask: false
				 });
				 }, this);

				 Ext.Ajax.on('requestcomplete', function(){
				 jQuery(document.body).loading(false);
				 }, this);

				 Ext.Ajax.on('requestexception', function(){
				 jQuery(document.body).loading(false);
				 }, this);*/
				var viewport = new Ext.Viewport({
					layout : 'border',
					renderTo : Ext.getBody(),
					items : items
				});

				var T = Ext.getCmp('Tabs');
				Ext.app.tab = T;
				//Ext.app.showHelp();
				Ext.app.showPortal();
				Ext.app.auth_reload(true);
			} catch (e) {
			}

			Ext.status.init();

			Ext.app.initClipboard();

			//http://msdn.microsoft.com/en-us/library/ms536907(VS.85).aspx
			var closeIt = function() {
				if(Ext.app.askexit)
					return _s('q-reload');
				else
					return null;
			}
			window.onbeforeunload = closeIt;
		},
		showHelp : function() {
			var T = Ext.getCmp('Tabs');
			var f = T.findById('help-tab');

			if(f == null) {
				Ext.app.addTabJSONHTMLFILE({
					title : _s('Ayuda'),
					id : 'help-tab',
					icon : 'help',
					html_file : Ext.app.HELP
				});
			} else {
				T.setActiveTab('help-tab');
			}
		},
		showPortal : function() {
			var T = Ext.getCmp('Tabs');
			var f = T.findById('portal-tab');

			if(f == null) {
				Ext.app.execCmd({
					title : _s('Portal'),
					id : 'portal-tab',
					icon : 'icon-portal',
					url : site_url('sys/portal/index')
				});
			} else {
				T.setActiveTab('portal-tab');
			}
		},
		print_window : null,
		/**
		 * Envía datos al servidor de impresión
		 * @param {Object} param
		 */
		printServer : function(param, host, port) {
			if(host == null)
				host = Ext.app.get_config('bp.printerserver.host');
			if(port == null)
				port = Ext.app.get_config('bp.printerserver.port');
			var url = 'http://' + host + ':' + port + '?' + Ext.urlEncode(param);
			if(this.print_window != null) {
				this.print_window.close();
			}
			//console.log(url);
			this.print_window = new Ext.IframeWindow({
				id : id,
				width : 0,
				height : 0,
				title : _s("Imprimiendo"),
				src : url
			});
			this.print_window.on('show', function(w) {
				w.hide();
			});
			this.print_window.show();
		},
		/**
		 * Manda imprimir un ticket
		 * @param {Object} report
		 * @param {Object} title
		 */
		printTicket : function(src, title) {
			/*Ext.app.callRemote({
			 url: report,
			 nomsg: true,
			 fnok: function(res){
			 Ext.app.printServer({
			 cmd: 'ticket',
			 report: res.message,
			 title: title
			 });
			 });*/
			Ext.app.printServer({
				cmd : 'ticket',
				src : src,
				title : title
			});
		},
		/**
		 * Abre el cajón portamonedas
		 */
		openBox : function() {
			Ext.app.printServer({
				cmd : 'openbox'
			}, Ext.app.get_config('bp.drawerserver.host'), Ext.app.get_config('bp.drawerserver.port'));
		},
		/**
		 * Manda imprimir un etiquetas
		 * @param {Object} src
		 * @param {Object} title
		 */
		printLabels : function(src, title) {
			var url = site_url('catalogo/grupoetiqueta/labels');
			Ext.app.callRemote({
				url : url,
				timeout : false,
				params : {
					src : src,
					title : title
				}
			});
			return;
			Ext.app.printServer({
				cmd : 'label',
				src : src,
				title : title
			}, Ext.app.get_config('bp.labelserver.host'), Ext.app.get_config('bp.labelserver.port'));
		},
		/**
		 * Añade el evento de borrar al pulsar DEL en un grid
		 * @param {Object} grid
		 */
		addDeleteEvent : function(grid) {
			grid.on('keydown', function(e) {
				if(e.getKey() == e.DELETE && !grid.editing) {
					try {
						var sm = grid.getSelectionModel();
						var sel = sm.getSelections();
						var record = grid.getStore().getAt(sel);
						grid.getStore().remove(record);
					} catch (e) {
					}
				}
			});
		},
		/**
		 * Eventos del sistema
		 */
		eventos : new BPObserver(),
		rendererPVP : function(val, x, r, row, col) {
			if(r != null && x != null)
				x.css = 'cell-docs-pvp';

			return Ext.app.numberFormatter(val);
		},
		/**
		 * añadir un menu contextual en cada fila del grid
		 * @param {Object} grid
		 * @param {Object} id
		 */
		addContextMenuLibro : function(grid, id, lineas) {
			if(id == null)
				id = 'nIdLibro';
			return Ext.app.addContextMenu(grid, id, lineas, 'catalogo/articulo/index', _s('Ver artículo'), 'iconoArticulos');
		},
		/**
		 * añadir un menu contextual en cada fila del grid
		 * @param {Object} grid
		 * @param {Object} id
		 */
		addContextMenu : function(grid, id, lineas, url, title, icon) {
			try {
				var ctxRow = null;
				var contextmenu = new Ext.menu.Menu({
					allowOtherMenus : false,
					items : [{
						text : _s(title),
						handler : function() {
							try {
								if(ctxRow) {
									Ext.app.execCmd({
										url : site_url(url + '/' + ctxRow.data[id])
									});
								}
							} catch (e) {
							}
						},
						iconCls : icon
					}]
				});

				grid.on('rowcontextmenu', function(gridPanel, rowIndex, e) {
					e.stopEvent();
					ctxRow = grid.store.getAt(rowIndex);
					if(lineas != null) {
						lineas.setItemSelect(ctxRow);
					}
					contextmenu.showAt(e.getXY());
				});
			} catch (e) {
			}
			return contextmenu;
		},
		/**
		 * añadir un menu contextual en cada fila del grid
		 * @param {Object} grid
		 * @param {Object} id
		 */
		addContextMenuEmpty : function(grid, lineas) {
			try {
				var ctxRow = null;
				var contextmenu = new Ext.menu.Menu({
					allowOtherMenus : false,
					items : []
				});

				grid.on('rowcontextmenu', function(gridPanel, rowIndex, e) {
					//console.log('En rowcontextmenu');
					e.stopEvent();
					ctxRow = grid.store.getAt(rowIndex);
					if(lineas != null) {
						lineas.setItemSelect(ctxRow);
					}
					contextmenu.showAt(e.getXY());
				});
			} catch (e) {
			}
			return contextmenu;
		},
		/**
		 * Devuelve los items seleccionados de un grid
		 * @param {Object} grid
		 */
		gridGetChecked : function(grid, idname) {
			try {
				var codes = '';
				if(idname == null)
					idname = 'id';
				var sel = grid.getSelectionModel().getSelections();
				if(sel.length == 0)
					return null;
				for(var i = 0; i < sel.length; i = i + 1) {
					codes += sel[i].data[idname] + ';';
				}
				return codes;
			} catch (e) {
			}
		},
		/**
		 * Inicializa el clipboard
		 */
		initClipboard : function() {
			try {

			} catch (e) {
			}
		},
		/**
		 * Añade info al clipboard
		 */
		setClipboard : function(text) {
			try {
				if(this.clipboard == null) {
					ZeroClipboard.setMoviePath(slash_item() + "/assets/js/zeroclipboard/ZeroClipboard10.swf");
					var el = Ext.DomHelper.insertAfter(document.body, {
						html : '<div id="d_clip_container" class="centered"><div id="d_clip_button" class="my_clip_button">' + _s('add_portapapeles') + '</div></div>'
					}, true);

					this.clipboard = new ZeroClipboard.Client();
					this.clipboard.setHandCursor(true);
					this.clipboard.addEventListener('complete', function(client, text) {
						jQuery('#d_clip_container').hide();
						Ext.app.msgFly(_s('clipboard'), _s('clipboard-ok'));
					});
					this.clipboard.glue('d_clip_button', 'd_clip_container');
				}
				jQuery('#d_clip_container').show();

				this.clipboard.setText(text)
				return;
			} catch (e) {
			}
		},
		/**
		 * Devuelve si un tipo es revista o no
		 */
		IsRevista : function(id) {
			return ((id == 6) || (id == 14));
		},
		playSoundError : function(id) {
			if (id == null) id = 'audio1';
			//console.log('Playing ' + id);
			var sound = document.getElementById(id);
			//console.dir(sound);
			if (sound)
				sound.play();
			//console.dir(jQuery('#' + id))
			/*console.log('Playing ' + id);

			thissound.Play();*/
		}
	}
}());
