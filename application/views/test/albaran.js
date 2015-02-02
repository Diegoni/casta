(function() {
	<?php
	$data['name'] = 'dsalb';
	$data['id'] = 'nIdDevolucion';
	$data['url'] = site_url('albaranentrada/search');
	$data['fields'][] = array('name' => 'nIdAlbaran');
	$data['fields'][] = array('name' => 'cRefInterna');
	$data['fields'][] = array('name' => 'cRefProveedor');
	$data['fields'][] = array('name' => 'cNumeroAlbaran');
	$data['fields'][] = array('name' => 'dFecha', 'type' => 'date');
	$data['fields'][] = array('name' => 'dCreacion', 'type' => 'date');
	$data['fields'][] = array('name' => 'dCierre', 'type' => 'date');
	$data['fields'][] = array('name' => 'cCUser');
	echo extjs_createjsonreader($data); 
	?>

	// Custom rendering Template
	var resultTpl = new Ext.XTemplate(
			'<tpl for="."><div class="search-item">',
			'{cNumeroAlbaran}<BR/>',
			'Id:{nIdAlbaran} / <b>Ref:</b>{cRefInterna} / {cRefProveedor} / {cNumeroAlbaran}<BR/>',
			'{dCreacion:date("M j, Y")}por {cCUser}', '</div></tpl>');

	var absform = new Ext.form.FormPanel({
		baseCls : 'x-plain',
		layout : 'absolute',
		url : 'save-form.php',
		border : true,
		defaultType : 'textfield',

		items : [{
					x : 0,
					y : 5,
					xtype : 'label',
					text : "<?php echo $this->lang->line('Código'); ?>"
				}, {
					x : 50,
					y : 0,
					xtype : 'combo',
					store : dsalb,
					frame : true,
					fieldLabel : "<?php echo $this->lang->line('Código'); ?>",
					name : 'cid',
					cls : 'icon-search-form',
					displayField : 'nIdAlbaranEntrada',
					resizable : true,
					typeAhead : true,
					loadingText : "<?php echo $this->lang->line('Buscando...'); ?>",
					width : 570,
					pageSize : 10,
					hideTrigger : true,
					enableKeyEvents : true,
					tpl : resultTpl,
					minChars : 1000,
					// anchor : '100%',
					itemSelector : 'div.search-item',
					listeners : {
						keypress : function(f, e) {
							if (e.getKey() == 13 && this.getValue() != '') {
								// Ext.app.msgFly('Keypress' , new
								// String(this.getValue()));
								f.doQuery(f.getValue(), true);
							}
						},
						beforeselect : function(f, r, i) {
							// Ext.app.msgFly('beforeselect' , 'Selección');
							var s = new String(r.data.nIdAlbaran);
							// alert(s);
							// this.suspendEvents();
							f.setRawValue(s);
							// this.resumeEvents();
							// Ext.getCmp('albform').findById('cid').setValue(s);
							Ext.getCmp('albform').findById('id').setValue(s);
						}
					}
				}, {
					x : 630,
					y : 0,
					width : 60,
					name : 'id',
					id : 'id',
					xtype : 'textfield',
					enable : false
				}, {
					xtype : 'tabpanel',
					x : 0,
					y : 27,
					activeTab : 0,
					plain : true,
					anchor : '100% 100%', // anchor width and height
					items : [{
						title : "<?php echo $this->lang->line('General'); ?>",
						iconCls : 'icon-general',
						layout : 'form',
						frame : true,
						// autoShow : true,
						items : [/*
									 * { xtype : 'fieldset', checkboxToggle :
									 * true, title : 'User Information',
									 * autoHeight : true, defaults : { width :
									 * 210 }, defaultType : 'textfield',
									 * collapsed : false, items : [{ fieldLabel :
									 * 'First Name', name : 'first', allowBlank :
									 * false }, { fieldLabel : 'Last Name', name :
									 * 'last' }, { fieldLabel : 'Company', name :
									 * 'company' }, { fieldLabel : 'Email', name :
									 * 'email', vtype : 'email' }] },
									 */{
							xtype : 'fieldset',
							checkboxToggle : true,
							title : 'Documento',
							autoHeight : true,
							defaults : {
								width : 210
							},
							defaultType : 'textfield',
							collapsed : false,
							items : [{
								items : [{
				frame : "true",
				items : [{
					items : [{
						xtype : "textfield"
					}, {
						xtype : "textfield"
					}],
					layout : "form",
					columnWidth : ".5"
				}, {
					items : [{
						xtype : "textfield"
					}, {
						xtype : "textfield"
					}],
					layout : "form",
					columnWidth : ".5"
				}],
				xtype : "form",
				layout : "column"
			}]
							},
								{
								layout : 'column',
								items : [{
											columnWidth : .5,
											layout : 'form',
											items : [{
														fieldLabel : 'Número',
														xtype : 'textfield',
														anchor: '100%'

													}, {
														fieldLabel : 'Divisa',
														xtype : 'combo'
													}]
										}, {
											columnWidth : .5,
											layout : 'form',
											items : [{
														fieldLabel : 'Fecha',
														xtype : 'datefield'
													}, {
														fieldLabel : 'Valor divisa',
														xtype : 'textfield'
													}]
										}]
							}]
						}]
					}, {
						title : "<?php echo $this->lang->line('Información'); ?>",
						iconCls : 'icon-information',
						layout : 'absolute',
						frame : true,
						// autoShow : true,
						items : [{
									x : 0,
									y : 5,
									xtype : 'label',
									text : 'Número',
									width : 60
								}, {
									x : 60,
									y : 0,
									width : 100,
									xtype : 'textfield'
								}, {
									x : 170,
									y : 5,
									width : 60,
									xtype : 'label',
									text : 'Fecha'

								}, {
									x : 230,
									y : 0,
									xtype : 'datefield'
								}, {
									x : 400,
									y : 5,
									width : 60,
									xtype : 'label',
									text : 'Divisa'
								}, {
									x : 460,
									y : 0,
									xtype : 'combo'
								}, {
									x : 640,
									y : 5,
									width : 60,
									xtype : 'label',
									text : 'Peso'
								}, {
									x : 700,
									y : 0,
									width : 60,
									xtype : 'numberfield'
								}]
					}, {
						title : "<?php echo $this->lang->line('Incidencias'); ?>",
						iconCls : 'icon-incidencias'
					}, {
						title : "<?php echo $this->lang->line('Notas'); ?>",
						iconCls : 'icon-notes'
					}, {
						title : "<?php echo $this->lang->line('Documentos'); ?>",
						iconCls : 'icon-documents'
					}, {
						title : "<?php echo $this->lang->line('Buscar'); ?>",
						iconCls : 'icon-search'
					}]

				}]
	});

	var absoluteForm = {
		title : 'Absolute Layout Form',
		id : 'abs-form-panel',
		layout : 'fit',
		region : 'center',
		// bodyStyle: 'padding:15px;',
		closable : true,
		title : "<?php echo $title;?>",
		id : "<?php echo $id;?>",
		iconCls : "<?php echo $icon;?>",
		items : {
			layout : 'fit',
			frame : true,
			bodyStyle : 'padding:10px 5px 5px;',
			items : absform,

			tbar : [{
				xtype : 'tbbutton',
				text : "<?php echo $this->lang->line('Acciones'); ?>",
				menu : [{
							text : "<?php echo $this->lang->line('Conexión'); ?>",
							handler : function(button) {
								// ActualizarTarifaWindow.show();
								alert('conexión'); // @todo hacer esto
							}
						}, {
							text : "<?php echo $this->lang->line('Salir'); ?>",
							handler : function(button) {
								// @todo preguntar
							}
						}]
			}, '-', {
				text : "<?php echo $this->lang->line('Nuevo'); ?>",
				iconCls : 'icon-new',
				id : 'alb_btnnew'
			}, {
				text : "<?php echo $this->lang->line('Refrescar'); ?>",
				iconCls : 'icon-refresh',
				id : 'alb_btnrefresh'
			}, {
				text : "<?php echo $this->lang->line('Eliminar'); ?>",
				iconCls : 'icon-delete',
				id : 'alb_btndel'
			}, '-', {
				text : "<?php echo $this->lang->line('Generar'); ?>",
				iconCls : 'icon-generate',
				id : 'alb_btngenerate'
			}, '-', {
				text : "<?php echo $this->lang->line('Imprimir'); ?>",
				iconCls : 'icon-print',
				id : 'alb_btnprint'
			}, '->', {
				xtype : 'tbbutton',
				text : "<?php echo $this->lang->line('Herramientas'); ?>",
				menu : [{
							text : "<?php echo $this->lang->line('Conexión'); ?>",
							handler : function(button) {
								// ActualizarTarifaWindow.show();
								alert('conexión'); // @todo hacer esto
							}
						}, {
							text : "<?php echo $this->lang->line('Salir'); ?>",
							handler : function(button) {
								// @todo preguntar
								window.location = "<?php echo site_url('user/logout');?>";
							}
						}]
			}]
		}
	};

	// return falbaran;
	return absoluteForm;
})();