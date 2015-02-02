(function() {

	var CrearForm = new Ext.FormPanel({
				monitorValid : true,
				labelWidth : 50,
				border : false,
				bodyStyle : 'background:transparent;padding:2px',
				defaults : {
					anchor : '95%',
					allowBlank : false,
					msgTarget : 'side'
				},
				items : [{
							xtype : 'textfield',
							id : 'year',
							emptyText : _s('Año'),
							fieldLabel : 'Año',
							name : 'year'
						}]
			});

	var CrearWindow = new Ext.Window({
		title : _s('Crear Año'),
		autoHeight : true,
		bodyStyle : 'padding: 10px 10px 0 10px;',
		layout : 'form',
		width : 500,
		height : 300,
		closeAction : 'hide',
		resizable : false,
		plain : true,
		modal : true,
		items : CrearForm,
		buttons : [{
			text : _s('Aceptar'),
			handler : function() {
				if (CrearForm.getForm().isValid()) {
					CrearForm.getForm().submit({
						// method : 'POST',
						waitTitle : _s('Crear Año'),
						waitMsg : _s('Creando'),
						url : site_url('calendario/dia/crear'),
						success : function(fp, o) {
							Ext.Msg
									.alert(
											_s('Crear Año'),
											o.result.message);
							CrearWindow.hide();
							var g = Ext.getCmp('<?php echo $id;?>_grid');
							var st = g.getStore();
							st.load();
						},
						failure : function(fp, o) {
							Ext.Msg
									.alert(_s('Crear Año'), _s('Error') + o.result.message);
						}

					});
				}
			}
		}, {
			text : _s('Cerrar'),
			handler : function() {
				CrearWindow.hide();

			}
		}]
	});

	var fn = function(m) {
		var b = new Array();
		b[0] = {
			text : _s('Crear Año'),
			handler : function() {
				CrearWindow.show();
			},
			iconCls : 'icon-new-calendar',
			id : m.id + 'btn_new'
		};
		b[1] = '-';
		m.tbar = b.concat(m.tbar);
		return m;
	}

	<?php $modelo = $this->reg->get_data_model();?>

	var grid = <?php echo extjs_creategrid($modelo, $id, $this->lang->line($title), $icon, 'calendario.dia', $this->reg->get_id(), 'fn');?>;
	return grid;

})();