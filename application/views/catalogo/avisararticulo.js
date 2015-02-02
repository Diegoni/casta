(function() {
	try {
		var id = "<?php echo $id;?>";
		var url = "<?php echo $url;?>";

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
			name : 'bReservado'
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
			id : 'descripcion',
			header : _s("cCliente"),
			dataIndex : 'cCliente',
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
			header : _s("cSeccion"),
			dataIndex : 'cSeccion',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			header : _s("Reservar"),
			width : Ext.app.TAM_COLUMN_TEXT,
			dataIndex : 'bReservado',
			renderer : function(v, x, r, row, col) {
				if(r.data.nCantidadServida > 0)
					return _s('YA RESERVADO');
				/*if(r.data.bReservado == true)
					x.css = 'cell-docs-referencia';*/
				return v ? _s('RESERVAR') : '';
			},
			sortable : true
		}];

		var grid = new Ext.grid.EditorGridPanel({
			store : store,
			anchor : '100% 100%',
			check : true,
            title: _s('Clientes'),
            iconCls: 'icon-clientes',
			height: 500,
			width: 750,
			autoExpandColumn : 'descripcion',
			stripeRows : true,
			loadMask : true,
			sm : sm,

			bbar : Ext.app.gridBottom(store, true),

			viewConfig : {
				enableRowBody : true,
				forceFit : true,
				getRowClass : function(r, rowIndex, rowParams, store) {
					return (r.data.bReservado == true)?'cell-repo-stock':'';
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

	var controls = {
        xtype: 'tabpanel',
        region: 'center',
        activeTab: 0,
        baseCls: 'x-plain',
        items: [grid, {
            title: _s('Texto'),
            iconCls: 'icon-sms',
            frame: true,
            //anchor: '100% 100%',
            height: 500,
            items: [Ext.app.formHtmlEditor({
				            name: 'texto_rs',
				            id: 'texto_rs',
							//hideLabel : true,
							label: _s('EMAIL Reservado'),
				            value : "<?php echo str_replace('"', '\\"', $texto_rs); ?>",
				            height: 120,
				            width: 750,
				            anchor: '100%'
				        })[0],
						Ext.app.formHtmlEditor({
				            name: 'texto_rc',
							label: _s('EMAIL Recibido'),
				            id: 'texto_rc',
				            value : "<?php echo str_replace('"', '\\"', $texto_rc); ?>",
				            height: 120,
				            width: 750,
				            anchor: '100%'
				        })[0],
						Ext.app.formHtmlEditor({
				            name: 'texto_srs',
				            id: 'texto_srs',
							//hideLabel : true,
							label: _s('SMS Reservado'),
				            value : "<?php echo str_replace('"', '\\"', $texto_srs); ?>",
				            height: 120,
				            width: 750,
				            anchor: '100%'
				        })[0],
						Ext.app.formHtmlEditor({
				            name: 'texto_src',
				            id: 'texto_src',
							label: _s('SMS Recibido'),
				            value : "<?php echo str_replace('"', '\\"', $texto_src); ?>",
				            height: 120,
				            width: 750,
				            anchor: '100%'
				        })[0]		
			     ]
        }]
    };


		var form = Ext.app.formStandarForm({
			controls : controls,
			autosize : false,
			labelWidth : 75,
			height : 600,
			icon : 'icon-email',
			width : 800,
			title : _s('avisar-clientes'),
			fn_ok : function(data) {
				var sel = grid.getSelectionModel().getSelections();
				var ids = '';
				Ext.each(sel, function(e) {
					ids += e.data.nIdLinea + '##' + e.data.cModo + '##' + e.data.cContacto + '##' + e.data.bReservado + ';';
					//console.log('Avisar a la línea ' + e.data.nIdLinea + ' Modo ' + e.data.cModo)
				});

				data.id = id;
				data.ids = ids;
				data.texto_rc = Ext.getCmp('texto_rc').getValue();
				data.texto_src = Ext.getCmp('texto_src').getValue();
				data.texto_srs = Ext.getCmp('texto_srs').getValue();
				data.texto_rs = Ext.getCmp('texto_rs').getValue();

				Ext.app.callRemote({
					url : site_url('catalogo/articulo/avisar'),
					timeout : false,
					params : data,
					fnok : function(obj) {
						var f = Ext.getCmp('<?php echo $cmpid;?>');
						f.refresh();
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
