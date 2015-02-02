(function() {
	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";
	if(title == '')
		title = _s('Pedidos Web');
	if(icon == '')
		icon = 'iconoPedidosWebTab';


	var renderPedidoWeb = function(c, v, r) {
		return '<b>' + r.data.customer + '</b><br/>' +
			'<font color="green">' + r.data.payment_method + '</font><br/>' +
			'<font color="blue">' + r.data.shipping_method + '<br/>' +
			r.data.shipping_address_1 + ' / ' +
			r.data.shipping_address_2 + ' / ' +
			r.data.shipping_postcode + ' / ' +
			r.data.shipping_city + ' / ' +
			r.data.shipping_country + '</font>';
	}

	// Pedidos
	var model = [{
		name : 'order_id',
		column : {
			header : _s("Id"),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true
		}
	}, {
		name : 'customer',
		column : {
			header : _s("Cliente"),
			width : Ext.app.TAM_COLUMN_TEXT,
			id : 'descripcion',
			renderer : renderPedidoWeb,
			sortable : true
		},
		ro : true
	}, {
		name : 'order_status',
		column : {
			header : _s("Estado"),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'total',
		column : {
			header : _s("Total"),
			width : Ext.app.TAM_COLUMN_TEXT,
			align : 'right',
			renderer : Ext.app.renderMoney,
			sortable : true
		}
	}, {
		name : 'foreign_id',
		column : {
			header : _s("Id.BP"),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true
		}
	}, {
		name : 'date_added',
		column : {
			header : _s("dCreacion"),
			width : Ext.app.TAM_COLUMN_DATE * 2,
			renderer : Ext.app.renderDate,
			sortable : true
		}
	}, {
		name : 'date_modified',
		column : {
			header : _s("dAct"),
			width : Ext.app.TAM_COLUMN_DATE,
			renderer : Ext.app.renderDate,
			sortable : true
		}
	}, {
		name : 'payment_method'
	}, {
		name : 'shipping_method'
	}, {
		name : 'shipping_address_1'
	}, {
		name : 'shipping_address_2'
	}, {
		name : 'shipping_city'
	}, {
		name : 'shipping_postcode'
	}, {
		name : 'shipping_country'
	}];

	var form = Ext.app.createFormGrid({
		model : model,
		title : title,
		icon : icon,
		id : form_id + "_pedidos",
		idfield : 'order_id',
		urlget : site_url("web/webpage/get_pedidos"),
		viewConfig : {
			enableRowBody : true,
			getRowClass : function(r, rowIndex, rowParams, store) {
				return (r.data.foreign_id > 0) ? '':'cell-repo-tratar';
			}
		},
		anchor : '100% 85%',
		load : false
	});

	var grid = Ext.getCmp(form_id + '_pedidos_grid');
	var cm_lineas = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuEmpty(grid, cm_lineas);
	var m_pedido = contextmenu.add({
		text : _s('Ver pedido local'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if(record != null && record.data.foreign_id != null) {
				Ext.app.execCmd({
					url : site_url('ventas/pedidocliente/index/' + record.data.foreign_id)
				});
			}
		},
		iconCls : 'iconoPedidoCliente'
	});
	var m_addpedido = contextmenu.add({
		text : _s('Importar pedido'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			//console.dir(record.data);
			if(record != null && (record.data.foreign_id == '' || record.data.foreign_id == null) && record.data.order_id != '') {
				var seccion = new Ext.form.ComboBox(Ext.app.combobox({
					url : site_url('generico/seccion/search'),
					name : 'seccion',
					anchor : '100%',
					label : _s('Sección')
				}));

				var controls = [seccion, {
					xtype : 'hidden',
					name : 'id',
					value : record.data.order_id
				}];

				var url = site_url('web/webpage/importar_pedido');

				var form = Ext.app.formStandarForm({
					controls : controls,
					title : _s('Importar pedido'),
					iconCls : 'icon-import',
					url : url,
					fn_ok : function(res) {
						Ext.app.execCmd({
							url: site_url('ventas/pedidocliente/index/' + res.id)
						});
						Ext.app.set_config('bp.importar_pedido.default', seccion.getValue(), 'user');

						grid.store.load();
					}
				});	

				seccion.store.load({
					callback: function() {
						var v = Ext.app.get_config('bp.importar_pedido.default', 'user');
						if (v != null && v != '')
							seccion.setValue(parseInt(v));
					}
				});
				form.show();
			}
		},
		iconCls : 'icon-import'
	});

	var m_enviado = contextmenu.add({
		text : _s('Marcar como enviado'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if(record != null && record.data.foreign_id != null) {
				Ext.app.execCmd({
					url : site_url('web/webpage/enviado/' + record.data.foreign_id),
					fnok: function(){
						grid.store.load();
					}
				});
			}
		},
		iconCls : 'icon-send'
	});

	cm_lineas.setContextMenu(contextmenu)
	var fn_check_menu = function(item) {
		(item.data.foreign_id != null) ? m_pedido.enable() : m_pedido.disable(); 
		(item.data.foreign_id == null) ? m_addpedido.enable() : m_addpedido.disable();
		(item.data.foreign_id != null && item.data.order_status_id!=3) ? m_enviado.enable() : m_enviado.disable(); 
	}
	cm_lineas.setCheckMenu(fn_check_menu);

	grid.store.baseParams = {
		start : 0,
		limit : Ext.app.PAGESIZE
	}
	grid.store.load();

	return form;
})();
