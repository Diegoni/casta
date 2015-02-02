(function() {
	try {
		var id = "<?php echo $id;?>";
		var url = "<?php echo $url;?>";
		var cmpid = "<?php echo $cmpid;?>";

		/*var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		 url : site_url('ventas//search'),
		 id : form_id + '_sec'
		 }));*/

		var model = [{
			name : 'nIdPedido'
		}, {
			name : 'id'
		}, {
			name : 'nCantidad'
		}, {
			name : 'nIdCliente'
		}, {
			name : 'cCliente'
		}, {
			name : 'cEstado'
		}, {
			name : 'cTitulo'
		}, {
			name : 'cModo'
		}, {
			name : 'cContacto'
		}, {
			name : 'nIdLinea'
		}, {
			name : 'cSeccion'
		}, {
			name : 'nCantidadServida'
		}, {
			name : 'nIdInformacion'
		}, {
			name : 'nIdTipoInformacion'
		}, {
			name : 'cInformacion'
		}];

		var store = Ext.app.createStore({
			model : model,
			url : url
		});

		var sm = new Ext.grid.CheckboxSelectionModel();

		var columns = [sm, {
			header : _s("Id"),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdLinea',
			hidden : true,
			sortable : true
		}, {
			header : _s("nIdPedido"),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdPedido',
			hidden : true,
			sortable : true
		}, {
			header : _s("cCliente"),
			dataIndex : 'cCliente',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			id : 'descripcion',
			header : _s("cTitulo"),
			dataIndex : 'cTitulo',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			header : _s("nCantidad"),
			dataIndex : 'nCantidad',
			width : Ext.app.TAM_COLUMN_NUMBER_SHORT,
			sortable : true
		}, {
			header : _s("cModo"),
			dataIndex : 'cModo',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			header : _s("Destino"),
			dataIndex : 'cContacto',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			header : _s("cEstado"),
			dataIndex : 'cEstado',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			header : _s('cInformacion'),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'cInformacion',
			renderer : renderInfoCliente,
			sortable : true
		}];

		var grid = new Ext.grid.EditorGridPanel({
			store : store,
			anchor : '100% 80%',
			check : true,
			//height: 400,
			autoExpandColumn : 'descripcion',
			stripeRows : true,
			loadMask : true,
			sm : sm,

			bbar : Ext.app.gridBottom(store, true),

			viewConfig : {
				enableRowBody : true,
				forceFit : true,
				getRowClass : function(r, rowIndex, rowParams, store) {
					return (r.data.bReservado == true) ? 'cell-repo-stock' : '';
				},
			},
			listeners : {
				celldblclick : function(grid, row, column, e) {
					var record = grid.store.getAt(row);
					if(record.data.nCantidadServida > 0)
						return;
					var r = !record.data.bReservado;
					record.set('bReservado', r)
					record.commit();
				}
			},
			// grid columns
			columns : columns
		});

		var controls = [grid];

		var form = Ext.app.formStandarForm({
			controls : controls,
			autosize : false,
			labelWidth : 200,
			height : 500,
			icon : 'icon-email',
			width : 700,
			title : _s('avisar-clientes'),
			fn_ok : function() {
				var sel = grid.getSelectionModel().getSelections();
				var ids = '';
				Ext.each(sel, function(e) {
					ids += e.data.nIdLinea + '##' + e.data.cModo + '##' + e.data.cContacto + ';';
				});
				if(ids == '') {
					Ext.app.msgError(title, _s('no-items-marcados'));
					return false;
				}

				Ext.app.callRemote({
					url : site_url('ventas/pedidocliente/avisar2'),
					timeout : false,
					params : {
						cmpid : cmpid,
						ids : ids
					},
					fnok : function(obj) {
						var grid = Ext.getCmp(cmpid);
						grid.store.load();
						form.close();
					}
				});
			}
		});

		store.baseParams = {
			id : id
		};

		store.load();
		form.show();
	} catch(e) {
		console.dir(e);
	}
	return;

})();