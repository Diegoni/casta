(function() {

	var id = "<?php echo $id;?>";
	var ref = "<?php echo isset($ref)?(($ref)?'true':'false'):'true';?>" == 'true';
	var cliente = "<?php echo $cliente;?>";

    var model = [{
        name: 'nIdAvisoRenovacion',
        type: 'int',
        column: {
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cTitulo',
        column: {
            header: _s('cTitulo'),
            width: Ext.app.TAM_COLUMN_ID,
            id: 'descripcion',
            sortable: true
        }
    }, {
        name: 'cRefCliente',
        column: {
            header: _s('cReferencia'),
            width: Ext.app.TAM_COLUMN_TEXT,
            editor: new Ext.form.TextField(),
            hidden : !ref,
            sortable: true
        }
    }];

    var url = site_url('suscripciones/avisorenovacion/get_avisos/' + id + '/' + cliente);
    var store = Ext.app.createStore({
        model: model,
        url: url
    });

    // Grid de pendientes de enviar
    var grid = Ext.app.createGrid({
        store: store,
        anchor: '100% 60%',
        autoExpandColumn: 'descripcion',
        stripeRows: true,
        loadMask: true,
        show_filter: false,
        pages: false,
        model: model,
        autoexpand: true,
        stripeRows: true,
        loadMask: true,
        rownumber: true,
        editor: true,
        checkbox: true
    });

	var medio = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('suscripciones/mediorenovacion/search'),
		allowBlank : false,
		name : 'modo',
		anchor : '100%',
		label : _s('Medio')
	}));

	var refcontrol = new Ext.form.TextField({
		fieldLabel : _s('Referencia'),
		name : 'ref',
		hidden : !ref,
		selectOnFocus : true,
		anchor : '100%'
	});

	var avisos = new Ext.form.TextField({
		name : 'avisos',
		hidden : true,
	});

	var controls = [avisos, {
		fieldLabel : _s('Responsable'),
		xtype : 'textfield',
		allowBlank : false,
		name : 'contacto',
		selectOnFocus : true,
		anchor : '90%'
	}, {
		fieldLabel : _s('Fecha'),
		value : new Date(),
		name : 'fecha',
		startDay : Ext.app.DATESTARTDAY,
		allowBlank : false,
		xtype : "datefield"
	}, medio, {
		xtype : 'compositefield',
		anchor : '-20',
		hidden : !ref,
		defaults : {
			flex : 1
		},
		items : [refcontrol, {
			xtype : 'button',
			iconCls : 'icon-copy',
			width : 30,
			hidden : !ref,
			handler : function() {
				var rf = refcontrol.getValue();
				grid.getStore().each(function(r) {
					r.set('cRefCliente', rf);
				});
			}
		}]
	}, grid];

	var url = "<?php echo $url;?>";

	var form = Ext.app.formStandarForm({
		controls : controls,
		title : "<?php echo $title;?>",
		icon : "<?php echo $icon;?>",
		labelWidth : 100,
		url : url,
        autosize: false,
        timeout: false,
        height: 500,
        width: 700,
		fn_pre : function() {
			var sel = grid.getSelectionModel().getSelections();
			var ids = '';
			Ext.each(sel, function(e) {
				ids += e.data.nIdAvisoRenovacion + '##' + (e.data.cRefCliente!=null?e.data.cRefCliente:refcontrol.getValue()) + ';';
			});
			if(ids == '') {
				Ext.app.msgError(title, _s('no-items-marcados'));
				return false;
			}
			avisos.setValue(ids);
		},
		fn_ok: function(){
			var f = Ext.getCmp('<?php echo $cmpid;?>');
			if (f!=null)
				f.refresh();			
		}
	});

	medio.store.load();
    store.load();
	form.show();
	return;
})();
