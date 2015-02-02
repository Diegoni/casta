<?php echo extjs_searchfield($this);?>

function renderOK(value, p, r) {
	if (value == '1') {
		return "<?php echo $this->lang->line('cell_grid_ok'); ?>";
	} else if (value == '') {
		return "<?php echo $this->lang->line('cell_grid_pending'); ?>";
	} else {
		return "<?php echo $this->lang->line('cell_grid_error'); ?>";
	}
}

var storeTemplate = new Ext.data.Store({
			remoteSort : true,
			autoload : true,
			proxy : new Ext.data.HttpProxy({
						url : "<?php echo site_url('template/list_all');?>"
					}),
			reader : new Ext.data.JsonReader({
						root : 'value_data',
						totalProperty : 'total_data',
						idProperty : 'nIdTemplate',
						remoteSort : true,
						autoload : true
					}, [{
								name : 'nIdPlantilla'
							}, {
								name : 'cDescripcion'
							}, {
								name : 'tTexto'
							}, {
								name : 'cTipo'
							}, {
								name : 'bIsHTML',
								type : 'boolean'
							}, {
								name : 'cCUser'
							}, {
								name : 'dCreacion',
								type : 'date'
							}, {
								name : 'cAUser'
							}, {
								name : 'dAct',
								type : 'date'
							}])
		});
storeTemplate.setDefaultSort('cDescripcion', 'desc');

var store = new Ext.data.Store({
			remoteSort : true,
			autoload : true,
			proxy : new Ext.data.HttpProxy({
						url : "<?php echo site_url('communication/sms_list');?>"
					}),
			reader : new Ext.data.JsonReader({
						root : 'value_data',
						totalProperty : 'total_data',
						idProperty : 'nIdSMS',
						remoteSort : true
					}, [{
								name : 'nIdSMS'
							}, {
								name : 'cTo'
							}, {
								name : 'cMensaje'
							}, {
								name : 'cCUser'
							}, {
								name : 'cIdServidor'
							}, {
								name : 'bEnviado'
							}, {
								name : 'dCreacion',
								type : 'date'
							}])
		});
store.setDefaultSort('dCreacion', 'desc');

var grid = new Ext.grid.GridPanel({
	width : 700,
	height : 500,
	title : "<?php echo $this->lang->line('Histórico'); ?>",
	store : store,
	id : 'grid',
	stripeRows : true,
	autoExpandColumn : "mensaje",
	// trackMouseOver:false,
	// disableSelection:true,
	loadMask : true,

	// grid columns
	columns : [{
				header : "Id",
				dataIndex : 'nIdSMS',
				width : 20,
				align : 'right',
				sortable : true
			}, {
				id : 'mensaje', // id assigned so we can apply custom
				// css (e.g. .x-grid-col-topic b { color:#333 })
				header : "<?php echo $this->lang->line('Mensaje'); ?>",
				dataIndex : 'cMensaje',
				width : 420,
				// renderer: renderTopic,
				sortable : true
			}, {
				header : "<?php echo $this->lang->line('Número'); ?>",
				dataIndex : 'cTo',
				width : 100,
				hidden : true,
				sortable : true
			}, {
				header : "<?php echo $this->lang->line('Autor'); ?>",
				dataIndex : 'cCUser',
				width : 70,
				// align : 'right',
				sortable : true
			}, {
				// id: 'Fecha',
				header : "<?php echo $this->lang->line('Fecha'); ?>",
				dataIndex : 'dCreacion',
				width : 100,
				renderer : Ext.app.renderDate,
				sortable : true
			}, {
				header : "<?php echo $this->lang->line('Enviado'); ?>",
				dataIndex : 'bEnviado',
				width : 70,
				renderer : renderOK,
				// align : 'right',
				sortable : true
			}, {
				header : "<?php echo $this->lang->line('Id Ext'); ?>",
				dataIndex : 'cIdServidor',
				width : 70,
				// align : 'right',
				sortable : true
			}],

	// customize view config
	viewConfig : {
		forceFit : true,
		enableRowBody : true,
		showPreview : true
	},

	// paging bar on the bottom
	bbar : new Ext.PagingToolbar({
				pageSize : <?php echo $this->config->item('bp.data.limit');?>,
				store : store,
				displayInfo : true,
				displayMsg : "<?php echo $this->lang->line('grid_desplay_result'); ?>",
				emptyMsg : "<?php echo $this->lang->line('grid_desplay_no_topics'); ?>"
			})

});

var tabsNestedLayouts = {
	xtype : 'tabpanel',
	region : 'center',
	activeTab : 0,
	items : [{
		title : "<?php echo $this->lang->line('Envio'); ?>",
		layout : 'border',
		bodyStyle : 'padding:5px 5px 5px;',
		frame : true,
		tbar : [{
			text : "<?php echo $this->lang->line('Guardar como plantilla'); ?>",
			iconCls : 'icon-save',
			handler : function() {
				var nombre = '';
				Ext.MessageBox.prompt(
						"<?php echo $this->lang->line('Plantilla'); ?>",
						"<?php echo $this->lang->line('Nombre'); ?>:",
						function(btn, text) {
							if (btn == 'ok') {
								var msg = Ext.getCmp('smsform').findById('msg');
								msg = msg.getValue();
								Ext.Ajax.request({
									url : "<?php echo site_url('template/add/');?>",
									method : 'POST',
									waitTitle : "<?php echo $this->lang->line('Enviar SMS');?>",
									waitMsg : "<?php echo $this->lang->line('Creando Plantilla');?>",
									params : {
										msg : msg,
										name : text,
										type : 'sms'
									},

									success : function(result) {
										obj = Ext.util.JSON
												.decode(result.responseText);
										if (obj.success) {
											Ext.Msg
													.alert(
															"<?php echo $this->lang->line('Enviar SMS'); ?>",
															"<?php echo $this->lang->line('mensaje_save_ok'); ?>: "
																	+ obj.message);
											storeTemplate.load();
										} else {
											Ext.Msg
													.alert(
															"<?php echo $this->lang->line('Enviar SMS'); ?>",
															"<?php echo $this->lang->line('registro_error'); ?>: "
																	+ obj.error);
										}

									},

									failure : function(result) {
										Ext.Msg
												.alert(
														"<?php echo $this->lang->line('Error'); ?>",
														"<?php echo $this->lang->line('conexion_error'); ?>");
									}
								});
							}
						});

			}
		}],
		items : [{
			region : 'center',
			xtype : 'form',
			// bodyStyle : 'padding:5px 5px 0',
			baseCls : 'x-plain',
			labelWidth : 55,
			url : "<?php echo site_url('communication/sms_send');?>",
			defaultType : 'textfield',
			id : 'smsform',
			items : [{
						xtype : 'numberfield',
						fieldLabel : "<?php echo $this->lang->line('Número'); ?>",
						name : 'to',
						anchor : '97%',
						allowBlank : false
					}, {
						xtype : 'textarea',
						hideLabel : true,
						id : 'msg',
						name : 'msg',
						anchor : '97% -53',
						allowBlank : false
					}],
			buttons : [{
				text : "<?php echo $this->lang->line('Enviar'); ?>",
				handler : function() {
					Ext.getCmp('smsform').getForm().submit({
						method : 'POST',
						waitTitle : "<?php echo $this->lang->line('Enviar SMS');?>",
						waitMsg : "<?php echo $this->lang->line('Enviando Mensaje');?>",

						success : function(form, action) {
							obj = Ext.util.JSON
									.decode(action.response.responseText);
							Ext.Msg
									.alert(
											"<?php echo $this->lang->line('Enviar SMS'); ?>",
											"<?php echo $this->lang->line('mensaje_envio_ok'); ?>: "
													+ obj.message);
							store.load();
							Ext.getCmp('smsform').getForm().reset();
						},

						failure : function(form, action) {
							obj = Ext.util.JSON
									.decode(action.response.responseText);
							Ext.Msg
									.alert(
											"<?php echo $this->lang->line('Error'); ?>",
											"<?php echo $this->lang->line('mensaje_envio_error'); ?>: "
													+ obj.error);
						}

					});
				}
			}, {
				text : "<?php echo $this->lang->line('Limpiar'); ?>",
				handler : function() {
					Ext.getCmp('smsform').getForm().reset();
				}
			}]
		}, {
			title : "<?php echo $this->lang->line('Plantillas'); ?>",
			titleCollapse : "<?php echo $this->lang->line('Plantillas'); ?>",
			region : 'south',
			collapsible : true,
			// collapsed: true,
			height : 300,
			minSize : 100,
			// maxSize : 350,
			// bodyStyle : 'padding:10px;',
			split : true,
			xtype : 'grid',
			store : storeTemplate,
			id : 'gridT',
			stripeRows : true,
			autoExpandColumn : "texto",
			sm : new Ext.grid.RowSelectionModel({
						singleSelect : true
					}),
			// trackMouseOver:false,
			// disableSelection:true,
			loadMask : true,

			// grid columns
			columns : [{
						header : "Id",
						dataIndex : 'nIdPlantilla',
						width : 30,
						align : 'right',
						sortable : true
					}, {
						header : "<?php echo $this->lang->line('Descripcion'); ?>",
						dataIndex : 'cDescripcion',
						width : 200,
						sortable : true
					}, {
						id : 'texto',
						header : "<?php echo $this->lang->line('Texto'); ?>",
						dataIndex : 'tTexto',
						width : 400,
						sortable : false
					}, {
						header : "<?php echo $this->lang->line('Autor'); ?>",
						dataIndex : 'cCUser',
						width : 70,
						sortable : true
					}, {
						// id: 'Fecha',
						header : "<?php echo $this->lang->line('Fecha'); ?>",
						dataIndex : 'dCreacion',
						width : 100,
						renderer : Ext.app.renderDate,
						sortable : true
					}, {
						header : "<?php echo $this->lang->line('Actualizador'); ?>",
						dataIndex : 'cAUser',
						width : 70,
						// align : 'right',
						sortable : true
					}, {
						// id: 'Fecha',
						header : "<?php echo $this->lang->line('Actualización'); ?>",
						dataIndex : 'dAct',
						width : 100,
						renderer : Ext.app.renderDate,
						sortable : true
					}],

			// customize view config
			viewConfig : {
				forceFit : true,
				enableRowBody : true,
				showPreview : true
			},

			tbar : [{
				text : "<?php echo $this->lang->line('Eliminar'); ?>",
				iconCls : 'icon-delete',
				handler : function(sm, rowIdx, e) {
					var sm = Ext.getCmp('gridT').getSelectionModel();
					if (sm.hasSelection()) {
						var sel = sm.getSelected();
						// var msg = Ext.getCmp('smsform').findById('msg');
						Ext.Msg.show({
							title : "<?php echo $this->lang->line('Plantillas'); ?>",
							buttons : Ext.MessageBox.YESNOCANCEL,
							msg : "<?php echo $this->lang->line('elm-registro'); ?>",
							fn : function(btn) {
								if (btn == 'yes') {
									alert('Hay que borrar');
									Ext.app
											.msgFly(
													"<?php echo $this->lang->line('Plantillas'); ?>",
													"<?php echo $this->lang->line('Plantilla Eliminada'); ?>");
								}
							}
						})
						// msg.setValue(sel.data.tTexto);
					}
				}
			}],

			// paging bar on the bottom
			bbar : new Ext.PagingToolbar({
				pageSize : <?php echo $this->config->item('bp.data.limit');?>,
				store : storeTemplate,
				displayInfo : true,
				displayMsg : "<?php echo $this->lang->line('grid_desplay_result'); ?>",
				emptyMsg : "<?php echo $this->lang->line('grid_desplay_no_topics'); ?>"
			})
		}]

	}, grid]
};

var viewport = new Ext.Viewport({
			layout : 'border',
			renderTo : <?php echo isset($divid)?"'$divid'":'Ext.getBody()';?>,
			items : [tabsNestedLayouts,
					<?php echo extjs_panel_help($this, 'enviar sms');?>]
		});

Ext.getCmp('gridT').on('rowdblclick', function(sm, rowIdx, e) {
	var sm = Ext.getCmp('gridT').getSelectionModel();
	if (sm.hasSelection()) {
		var sel = sm.getSelected();
		var msg = Ext.getCmp('smsform').findById('msg');
		msg.setValue(sel.data.tTexto);
		Ext.app.msgFly("<?php echo $this->lang->line('Enviar SMS'); ?>",
				"<?php echo $this->lang->line('Plantilla Seleccionada'); ?>");
	}
});

store.load({
			params : {
				start : 0,
				limit : <?php echo $this->config->item('bp.data.limit');?>
			}
		});

storeTemplate.load({
			params : {
				start : 0,
				limit : <?php echo $this->config->item('bp.data.limit');?>
			}
		});