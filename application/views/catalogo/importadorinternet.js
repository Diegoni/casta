(function() {
	try {

		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";

		if(form_id == '')
			form_id = Ext.app.createId();
		if(title == '')
			title = _s('Alta desde Internet');
		if(icon == '')
			icon = 'iconoImportadorInternetTab';

		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('generico/seccion/search'),
			anchor : "90%",
			allowBlank : true,
			name : 'seccion',
			label : _s('Seccion')
		}));

		var materia = new Ext.ux.form.SuperBoxSelect(Ext.app.autocomplete2({
			url : site_url('catalogo/materia/search'),
			anchor : '90%',
			create : false,
			fieldLabel : _s('Materia')
		}));
		/*var materia = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('catalogo/materia/search'),
			anchor : "90%",
			allowBlank : true,
			name : 'materia',
			label : _s('Materia')
		}));*/

		var autor = new Ext.form.Checkbox({
			fieldLabel : _s('Crear autor'),
			checked : true
		});
		var coleccion = new Ext.form.Checkbox({
			fieldLabel : _s('Crear colección'),
			checked : true
		});

		var destino = new Ext.form.TextField({
			enableKeyEvents : true,
			fieldLabel : _s('ISBN/EAN'),
			//name: 'destino',
			anchor : '50%'
		});

		var fn_search = function(code) {
			var reg = {
				seccion : seccion.getRawValue(),
				materia : materia.getRawValue(),
				isbn : code,
				descripcion : _s('internet-buscando-code')
			}
			var item = store_historico.getCount();
			store_historico.add(new ComboRecord(reg));
			var ids = seccion.getValue();
			if(ids < 1)
				ids = null;
			var idm = materia.getValue();
			if(idm < 1)
				idm = null;
			var aut = autor.getValue();
			var col = coleccion.getValue(); 

			var url = site_url('catalogo/articulo/dilve');
			Ext.app.callRemote({
				url : url,
				nomsg : true,
				params : {
					isbn : code,
					seccion : ids,
					materia : idm,
					autor: aut,
					coleccion: col
				},
				fnok : function(res) {
					var record = store_historico.getAt(item);
					record.set('descripcion', res.message);
					record.commit();
				},
				fnnok : function(res) {
					var record = store_historico.getAt(item);
					record.set('descripcion', res.message);
					record.commit();
				}
			});
		}

		destino.on('keypress', function(t, e) {
			if(e.getKey() === e.ENTER) {
				var text = t.getValue();
				var codes = text.split(' ');
				Ext.each(codes, function(item) {
					if (item.trim() != '')
						fn_search(item);
				});				
				t.setValue('');
			}
		});
		var model_historico = [{
			name : 'seccion'
		}, {
			name : 'materia'
		}, {
			name : 'isbn'
		}, {
			name : 'descripcion'
		}, {
			name : 'id'
		}];

		var store_historico = new Ext.data.ArrayStore({
			fields : model_historico
		});

		var grid = new Ext.grid.GridPanel({
			region : 'center',
			autoExpandColumn : "descripcion",
			loadMask : true,
			stripeRows : true,
			store : store_historico,
			anchor : "100% 85%",
			columns : [{
				header : _s('Seccion'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'seccion',
				sortable : true
			}, {
				header : _s('Materia'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'materia',
				sortable : true
			}, {
				header : _s('ISBN/EAN'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'isbn',
				sortable : true
			}, {
				header : _s('Descripción'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'descripcion',
				id : 'descripcion',
				sortable : true
			}],
			tbar : Ext.app.gridStandarButtons({
				title : title,
				id : id + "_grid_historico"
			}),
			bbar : [{
				text : _s('Borrar lista'),
				iconCls : 'icon-clean',
				handler : function(button) {
					store_historico.removeAll();
				}
			}]
		});

		var form = new Ext.FormPanel({
			labelWidth : Ext.app.LABEL_SIZE,
			bodyStyle : 'padding:5px 5px 0',
			defaultType : 'textfield',
			region : 'center',
			closable : true,
			baseCls : 'x-plain',
			frame : true,
			items : [seccion, materia, autor, coleccion, destino, grid],
		});

		seccion.store.load();
		materia.store.load();

		var panel = new Ext.Panel({
			layout : 'border',
			title : title,
			id : id,
			iconCls : icon,
			region : 'center',
			closable : true,
			baseCls : 'x-plain',
			frame : true,
			items : [form]
		});

		return panel;
	} catch (e) {
		console.dir(e);
	}
})();
