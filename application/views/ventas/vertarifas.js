(function() {

	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";
	if(title == '')
		title = _s('Tarifas de envio');
	if(icon == '')
		icon = 'iconoTarifasEnvio';
	if(form_id == '')
		form_id = Ext.app.createId();

	var model = [{
		name : 'id',
		type : 'int',
		column : {
			header : _s('Id'),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true,
			hidden : true
		}
	}, {
		name : 'nIdTarifa'
	}, {
		name : 'text',
		column : {
			header : _s('cDescripcion'),
			id: 'descripcion',
			width : Ext.app.TAM_COLUMN_TEXT*3,
			sortable : true
		}
	}];
	for(var i = 1; i <= 20; ++i) {
		model[model.length] = {
			name : 'fV' + i,
			type : 'float',
			column : {
				header : i,
				width : 50,
				sortable : true,
				editor : new Ext.form.NumberField({
					selectOnFocus : true,
					allowBlank : false,
					allowNegative : false
				})
			}
		}
	}

	var modoenvio = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('ventas/modoenvio/search'),
		id : form_id + '_modo'
	}));

	var reload = function(idm) {
		if(idm == null)
			tipo = Ext.app.getIdCombo(modoenvio);
		var grid = Ext.getCmp(form_id + '_grid');
		grid.store.baseParams = {
			start : 0,
			limit : Ext.app.PAGESIZE,
			tipo : tipo
		};
		grid.store.load();
	}
	var stores = [{
		store : modoenvio.store
	}];

	var tbar = [{
		xtype : 'label',
		html : _s('Modo de envío')
	}, modoenvio, '-', {
		tooltip : _s('cmd-clonar'),
		text : _s('Clonar'),
		iconCls : 'icon-clone',
		handler : function() {
			var idm  = Ext.app.getIdCombo(modoenvio);
			if (idm < 1)
			{
				modoenvio.focus();
				return;
			}
			Ext.app.callRemote({
				url : site_url('ventas/tarifasenvio/clonar'),
				timeout : false,
				wait : true,
				params : {
					id : idm
				},
				fnok : function(res) {
					console.dir(res);
					modoenvio.store.load();
					modoenvio.setValue(res.id);
					reload();
				}
			});
		}
	}, '-', {
		tooltip : _s('cmd-actualizar'),
		text : _s('Actualizar'),
		iconCls : 'icon-actualizar',
		handler : function() {
			reload();
		}
	}];

	var panel = Ext.app.createFormGrid({
		model : model,
		show_filter : false,
		id : form_id,
		//checkbox:true,
		title : title,
		icon : icon,
		idfield : 'id',
		urlget : site_url('ventas/tarifasenvio/get_tarifastipo'),
		urlupd : site_url('ventas/tarifasenvio/set_tarifa'),
		loadstores : stores,
		fn_pre : null,
		fn_add : null,
		fn_add : null,
		tbar : tbar,
		load : false
	});

	var grid = Ext.getCmp(form_id + '_grid');

	panel.on('afterrender', function(p) {
		var map = new Ext.KeyMap(p.getEl(), [{
			key : [10, 13],
			ctrl : true,
			stopEvent : true,
			fn : function() {
				reload();
			}
		}]);
	});
	return panel;
})();
