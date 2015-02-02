(function() {
	// TODO Crear el modelo con las rutinas de extjs PHP
	// TODO Crear el store con ExtLib
    var turnos = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('calendario/grupostrabajador/search')
        }));

	var trabajadores = new Ext.form.ComboBox(Ext.app
			.combobox({url: site_url('calendario/trabajador/search')}));
	<?php
			$data['name'] = 'dst';
			$data['id'] = 'id';
			$data['url'] = site_url('calendario/calendario/personal_dia2');
			$data['fields'][] = array('name' => 'id');
			$data['fields'][] = array('name' => 'dDia', 'type' => 'date');
			$data['fields'][] = array('name' => 'dDia2');
			$data['fields'][] = array('name' => 'Dia');
			$data['fields'][] = array('name' => 'Numero');
			$data['fields'][] = array('name' => 'Mes');
			$data['fields'][] = array('name' => 'MesNumero');
			//$data['fields'][] = array('name' => 'nIdFestivo');
			//$data['fields'][] = array('name' => 'nIdVacaciones');
			$data['fields'][] = array('name' => 'cTrabajador');
			$data['fields'][] = array('name' => 'fHoras');
			$data['fields'][] = array('name' => 'cDescripcion');
			$data['fields'][] = array('name' => 'cComentario');
			$data['fields'][] = array('name' => 'bTarde', 'type' => 'bool');
			echo extjs_createjsonreader($data, true); 
			?>

		var calendario = {
					region : 'center',
					xtype : 'editorgrid',
					id : "<?php echo $id;?>_cal2",
					autoExpandColumn : "descripcion",
					loadMask : true,
					stripeRows : true,
					clickstoEdit : 1,
					store : dst,
			        bbar: Ext.app.gridBottom(dst, false),
			        tbar: Ext.app.gridStandarButtons({
			                id: "<?php echo $id;?>_cal2",
			                bar: bar,
							title: _s('Ver días')
			            }),
					sm : new Ext.grid.RowSelectionModel({
								singleSelect : true
							}),
					columns : [{
								header : "<?php echo $this->lang->line('Id'); ?>",
								width : Ext.app.TAM_COLUMN_ID,
								dataIndex : 'id',
								hidden : true,
								sortable : true
							}, {
								header : "<?php echo $this->lang->line('Número Mes'); ?>",
								width : Ext.app.TAM_COLUMN_ID,
								dataIndex : 'MesNumero',
								hidden : true,
								sortable : true
							}, {
								header : "<?php echo $this->lang->line('Mes'); ?>",
								width : Ext.app.TAM_COLUMN_DATE,
								dataIndex : 'Mes',
								sortable : true
							}, {
								header : "<?php echo $this->lang->line('Dia'); ?>",
								width : Ext.app.TAM_COLUMN_DATE,
								dataIndex : 'Dia',
								sortable : true
							}, {
								header : "<?php echo $this->lang->line('Tarde'); ?>",
								width : Ext.app.TAM_COLUMN_BOOL,
								dataIndex : 'bTarde',
								editor : new Ext.form.Checkbox(),
								renderer : Ext.app.renderCheck,
								sortable : false
							}, {
								header : "<?php echo $this->lang->line('Fecha'); ?>",
								width : Ext.app.TAM_COLUMN_DATE,
								dataIndex : 'dDia2',
								sortable : true
							}, {
								header : "<?php echo $this->lang->line('Trabajador'); ?>",
								width : Ext.app.TAM_COLUMN_DATE,
								dataIndex : 'cTrabajador',
								renderer : function(val) {
									return Ext.app.renderCombo(val, trabajadores);
								},
								sortable : true
							}, {
								header : "<?php echo $this->lang->line('Horas'); ?>",
								width : Ext.app.TAM_COLUMN_NUMBER,
								dataIndex : 'fHoras',
								editor : new Ext.form.NumberField({
											selectOnFocus:true,
											allowBlank : false,
											allowNegative : false,
											style : 'text-align:left'

										}),
								renderer : function(v) {
									return v + " <?php echo $this->lang->line('horas'); ?>";
								},
								sortable : true
							}, {
								header : "<?php echo $this->lang->line('Descripción'); ?>",
								width : Ext.app.TAM_COLUMN_TEXT,
								dataIndex : 'cDescripcion',
								sortable : false
							}, {
								header : "<?php echo $this->lang->line('Comentario'); ?>",
								width : Ext.app.TAM_COLUMN_TEXT,
								dataIndex : 'cComentario',
								editor : new Ext.form.TextField({selectOnFocus:true}),
								id : 'descripcion',
								sortable : false
							}],
					listeners : {
						afteredit : function(e) {
							var ed = false;
							var params = {};
							params['id'] = e.record.data.id;
							if ((is_null(e.value, '') != is_null(e.originalValue, ''))) {
								params[e.field] = e.value;
								ed = true;
							}
							if (ed) {
								var url = site_url('calendario/calendario/upd');
								Ext.app.callRemote({
									url : url,
									title : "<?php echo $title;?>",
									waitmessage : _s('Actualizando'),
									params : params,
									fnok : function() {
										e.record.commit();
									},
									fnnok : function() {
										e.record.reject();
									}
								});
							} else {
								e.record.commit();
							}
						}
					},
		viewConfig :{
			enableRowBody : true,
			getRowClass : function(r, rowIndex, rowParams, store) {
				//console.log('cell-calendario-' + r.data.Numero);
				return 'cell-calendario-' + r.data.Numero;
			}
					}
				};
								
	var reload = function() {
		var d1 = Ext.getCmp("<?php echo $id;?>_fecha1").getRawValue();
		var d2 = Ext.getCmp("<?php echo $id;?>_fecha2").getRawValue();
		var t = Ext.app.getIdCombo(turnos);

		if (d1 == '' || d2 == '') {
			Ext.app.msgFly("<?php echo $title;?>",
					"<?php echo $this->lang->line('mensaje_faltan_datos'); ?>");
			return;
		}

		 var g = Ext.getCmp( "<?php echo $id;?>_cal2");
         if (g != null) {
             var st = g.store;
             if (st != null) {		
            	 st.baseParams = {
            		fecha1: d1,
            		fecha2: d2,
					grupo: t
            	 };
            	 st.load({
					waitMsg : "<?php echo $this->lang->line('Cargando'); ?>",
					callback : function() {
						g.doLayout();
					}
            	 });
             }
         } 
	};

	var bar = [{
				xtype : 'label',
				html : _s('Desde') + ':'
			}, {
				id : "<?php echo $id;?>_fecha1",
				value : new Date(),
				startDay: Ext.app.DATESTARTDAY,
				xtype : "datefield"
			}, '-', {
				xtype : 'label',
				html : _s('Hasta') + ':'
			}, {
				id : "<?php echo $id;?>_fecha2",
				value : new Date(),
				startDay: Ext.app.DATESTARTDAY,
				xtype : "datefield"
			}, '-', {
				xtype : 'label',
				html : _s('Turnos') + ':'
			}, turnos, '->', {
				tooltip : _s('cmd-calcular'),
				text : _s('Actualizar'),
				iconCls : 'icon-actualizar',
				listeners : {
					click : function(b) {
						reload();
					}
				}
			}];

	var stores = [{
		store : trabajadores.store
	}, {
		store:turnos.store
	}];
	
	Ext.app.loadStores(stores);

	 // Define el panel
    var panel = {
        title: "<?php echo $title;?>",
        id: "<?php echo $id;?>",
        region: 'center',
        closable: true,
        iconCls: "<?php echo $icon;?>",
        layout: 'border', 
        items: [calendario],
        
        tbar: bar
    };
	return panel
})();