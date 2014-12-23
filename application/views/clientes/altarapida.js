(function() {
	// Controles normales
	var tipocliente = Ext.app.combobox({
		url : site_url('clientes/tipocliente/search'),
		name : 'nIdTipoCliente',
		anchor : '90%',
		allowBlank : true,
		label : _s('Tipo Cliente')
	});

	var id = Ext.app.createId();

	var cmpid = "<?php echo $this->input->get_post('cmpid');?>";
	var text = "<?php echo $this->input->get_post('text');?>";
	var email = validateEmail(text)?text:'';
	if (email != '') text = '';
	var c = Ext.getCmp(cmpid);

	var c = Ext.app.formComboPaises({
		idpais : id + '_p',
		idregion : id + '_r',
		value_p : Ext.app.DEFAULT_PAIS,
		value_r : Ext.app.DEFAULT_REGION,
		allowblank : false
	});
	var nombre = new Ext.form.TextField({
		xtype : 'textfield',
		name : 'cNombre',
		value : text,
		allowBlank : true,
		fieldLabel : _s('cNombre')
	});

	var apellido = new Ext.form.TextField({
		xtype : 'textfield',
		name : 'cApellido',
		allowBlank : true,
		fieldLabel : _s('cApellido')
	});
	var controls = [tipocliente, {
		xtype : 'compositefield',
		anchor : '-20',
		defaults : {
			flex : 1
		},
		items : [nombre, apellido, {
			xtype : 'button',
			iconCls : 'icon-split',
			width : 30,
			handler : function() {
				var a = apellido;
				var n = nombre;
				part_names(n, a);
			}
		}, {
			xtype : 'button',
			iconCls : 'icon-clean',
			width : 30,
			handler : function() {
				apellido.setValue(ucwords(apellido.getValue().toLowerCase()));
				nombre.setValue(ucwords(nombre.getValue().toLowerCase()));
			}
		}]
	}, {
		xtype : 'textarea',
		grow : true,
		id : 'cEmpresa',
		anchor : '90%',
		allowBlank : true,
		fieldLabel : _s('cEmpresa')
	}, {
		xtype : 'compositefield',
		fieldLabel : _s('NIF'),
		anchor : '-20',
		items : [{
			xtype : 'textfield',
			allowBlank : true,
			id : 'cNIF'
		}]
	}, {
		xtype : 'textfield',
		id : 'cCalle',
		anchor : '90%',
		allowBlank : true,
		fieldLabel : _s('cCalle')
	}, {
		xtype : 'compositefield',
		//anchor: '-20',
		fieldLabel : _s('Población'),
		defaults : {
			flex : 1
		},
		items : [{
			xtype : 'textfield',
			id : 'cPoblacion',
			//anchor: '100%',
			allowBlank : true
		}, {
			xtype : 'displayfield',
			value : _s('cCP')
		}, {
			xtype : 'textfield',
			id : 'cCP',
			//width: '50',
			allowBlank : true
		}]
	}, {
		xtype : 'compositefield',
		anchor : '-20',
		//fieldLabel: _s('País'),
		defaults : {
			flex : 1
		},
		items : [c[0]/*,   {
		 xtype: 'displayfield',
		 value: _s('Región')
		 }*/
		, c[1]]
	}, {
		xtype : 'textfield',
		id : 'cTelefono',
		anchor : '90%',
		allowBlank : true,
		fieldLabel : _s('Teléfono')
	}, {
		xtype : 'textfield',
		id : 'cEmail',
		anchor : '90%',
		value: email,
		allowBlank : true,
		fieldLabel : _s('cEmail')
	}];

	var url = site_url('clientes/cliente/alta');

	var form = Ext.app.formStandarForm({
		controls : controls,
		icon : 'iconoClientesAltaRapidaTab',
		title : _s('alta-rapida-cliente'),
		fn_ok : function(res) {
			if(cmpid != '') {
				var c = Ext.getCmp(cmpid);
				if(c != null) {
					c.load(res.id);
				}
			}
		},
		url : url
	});

	tipocliente.store.load();
	form.show();
	return;

})();
