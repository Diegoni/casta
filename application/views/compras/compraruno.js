(function() {
	var form_id = Ext.app.createId();

	var id_libro = parseInt('<?php echo $nIdLibro;?>');
	var id_seccion = parseInt('<?php echo $nIdSeccion;?>');
	var cantidad = parseInt('<?php echo $nCantidad;?>');

	var pedidos = new Ext.form.ComboBox(Ext.app.combobox({
		url: site_url('compras/pedidoproveedor/abiertos'),
		id: form_id + '_pedidos',
		width: 600,
		label: _s('Pedidos')
	}));

	pedidos.store.on('load', function(s, r) {
		if (s.getTotalCount() > 0) {
			pedidos.setValue(s.getAt(s.getTotalCount() - 1).data.id);
		}
	});
	var p = Ext.app.combobox({
		url: site_url('proveedores/proveedor/search'),
		width: 600,
		label: _s('Proveedor'),
        extrafields: ['text2'],
		id: form_id + '_proveedores',
	});
	p['tpl'] = '<tpl for="."><div class="x-combo-list-item"><b>{text}</b>{text2}</div></tpl>'
	p['listAlign'] = [ 'bl-bl', [0,0] ];

	var proveedores = new Ext.form.ComboBox(p);

	var id_proveedor = null;
	var loadpedidos = function(id) {
		if (id == null) {
			Ext.app.msgError(title, _s('no_proveedor'));
			return;
		}
		var dp = Ext.getCmp(form_id + "_deposito").getValue();
		id_proveedor = id;
		pedidos.store.removeAll();
		pedidos.store.baseParams = {
			deposito: (dp ? 1 : 0),
			proveedor: id,
			seccion: id_seccion
		};
		pedidos.store.load();
	};
	var loadproveedores = function(data, id) {
		proveedores.store.removeAll();
		Ext.each(data, function(item) {
			if (item.disabled !== true) {
				var text = item.text;
				if (item['default']) {
					text = '(*)' + text;
					id = parseInt(item.nIdProveedor);
				}
				Ext.app.comboAdd(proveedores.store, item.nIdProveedor, text, item.text2);
			}
		});
		loadpedidos(id);
		proveedores.setValue(new String(id));
		proveedores.onTriggerClick();
	}
	proveedores.on('select', function(c, r, i) {
		loadpedidos(r.data.id);
	});
	proveedores.store.load({
		params: {
			where: 'nIdProveedor=-1'
		}
	});
	var controls = [{
		fieldLabel: _s('Deposito'),
		id: form_id + "_deposito",
		xtype: "checkbox",
		listeners: {
			check: function(f, c) {
				if (id_proveedor != null) {
					loadpedidos(id_proveedor);
				}
			}
		}
	}, proveedores, pedidos, {
		xtype: 'textfield',
		value: Ext.app.get_config('bp.compras.referencia'),
		fieldLabel: _s('Referencia'),
		id: form_id + "_ref",
		width: 600
	}, new Ext.ux.form.Spinner({
		fieldLabel: _s('Cantidad'),
		id: form_id + "_cantidad",
        selectOnFocus: true,
		value: cantidad,
		width: 60,
		strategy: new Ext.ux.form.Spinner.NumberStrategy()
	}),{
		//xtype: 'iframepanel',
		height: Ext.app.REPOINFOHEIGHT,
		width: Ext.app.PEDIRWIDTH - 35,
		autoScroll: true,
		id: form_id + '_info'
	}];

	var url = site_url('compras/resposicion/pedir');

	var form = Ext.app.formStandarForm({
		controls: controls,
		title: _s('Pedir'),
		icon: 'icon-pedir',
		width: Ext.app.PEDIRWIDTH,
		fn_ok: function(res) {

			var idl = id_libro;
			var idpd = pedidos.getValue();
			var idp = id_proveedor;
			var ids = id_seccion;
			var dp = Ext.getCmp(form_id + "_deposito").getValue();
			var qt = Ext.getCmp(form_id + "_cantidad").getValue();
			var ref = Ext.getCmp(form_id + "_ref").getValue();
			Ext.app.set_config('bp.compras.referencia', ref);

			Ext.app.callRemote({
				url: site_url('compras/reposicion/pedir'),
				params: {
					id: idl,
					ref: ref,
					idpd: idpd,
					idp: idp,
					ids: ids,
					dp: dp,
					qt: qt
				},
				fnok: function(obj) {
					form.close();
				}
			});
		}
	});

	form.show();
	form.disable();
	Ext.app.callRemote({
		url: site_url('compras/reposicion/get_datos_venta'),
		params: {
			ids: id_seccion,
			id: id_libro
		},
		nomsg: true,
		fnok: function(obj) {
			form.enable();
			if (obj.success) {
				var detailEl = Ext.getCmp(form_id + "_info").body;
				detailEl.applyStyles({
					'background-color': '#FFFFFF'
				});

				detailEl.hide().update(obj.message).slideIn('l', {
					stopFx: true,
					duration: .1
				});

				loadproveedores(obj.data.proveedores, obj.data.nIdProveedor);
			} else {
				Ext.app.msgError(title, _s('registro_error') + ': ' +
				obj.message);
			}
		}
	});

	return;
})();