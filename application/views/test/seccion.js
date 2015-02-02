(function() {
	<?php
	$store = 'store';
	$data['name'] = $store;
	$data['id'] = 'nIdSeccion';
	$data['url'] = site_url('seccion/get_list/');
	$data['fields'][] = array('name' => 'nIdSeccion');
	$data['fields'][] = array('name' => 'cNombre');
	$data['fields'][] = array('name' => 'bBloqueada', 'type' => 'bool');
	$data['fields'][] = array('name' => 'bWeb', 'type' => 'bool');
	$data['fields'][] = array('name' => 'dCreacion', 'type' => 'date');
	$data['fields'][] = array('name' => 'cCUser');
	$data['fields'][] = array('name' => 'dAct', 'type' => 'date');
	$data['fields'][] = array('name' => 'cAUser');
	$data['fields'][] = array('name' => '_id', 'type' => 'int');
	$data['fields'][] = array('name' => '_parent', 'type' => 'int');
	$data['fields'][] = array('name' => '_level', 'type' => 'int');
	$data['fields'][] = array('name' => '_lft', 'type' => 'int');
	$data['fields'][] = array('name' => '_rgt', 'type' => 'int');
	$data['fields'][] = array('name' => '_is_leaf', 'type' => 'bool');
	$data['sort'] = 'cNombre';
	$data['dir'] = 'desc';
	echo extjs_createjsonreader($data);
?>

	// example of custom renderer function
	function change(val) {
		if (val > 0) {
			val = '<span style="color:green;">' + val + '</span>';
		} else if (val < 0) {
			val = '<span style="color:red;">' + val + '</span>';
		}
		return val;
	}

	// example of custom renderer function
	function pctChange(val) {
		if (val > 0) {
			val = '<span style="color:green;">' + val + '%</span>';
		} else if (val < 0) {
			val = '<span style="color:red;">' + val + '%</span>';
		}
		return val;
	}

	var record = Ext.data.Record.create([{
				name : 'cNombre'
			}, {
				name : 'bBloqueada',
				type : 'bool'
			}, {
				name : 'bWeb',
				type : 'bool'
			}, {
				name : 'dCreacion',
				type : 'date'
			}, {
				name : 'cCUser'
			}, {
				name : 'dAct',
				type : 'date'
			}, {
				name : 'cAUser'
			}, {
				name : '_id',
				type : 'int'
			}, {
				name : '_parent',
				type : 'auto'
			}, {
				name : '_is_leaf',
				type : 'bool'
			}]);
	var store3 = new Ext.ux.maximgb.treegrid.AdjacencyListStore({
				autoLoad : true,
				url : "index.php?c=seccion&m=get_list",
				reader : new Ext.data.JsonReader({
							id : '_id',
							root : 'value_data',
							totalProperty : 'total_data',
							successProperty : 'success'
						}, record)
			});

	/*
	 * var store2 = new Ext.ux.maximgb.treegrid.NestedSetStore({ autoLoad :
	 * true, reader : new Ext.data.JsonReader({ id : '_id' }, record), proxy :
	 * new Ext.data.MemoryProxy(data) });
	 */

	// create the Grid
	var grid2 = new Ext.ux.maximgb.treegrid.GridPanel({
				region : 'center',
				id : "<?php echo $id;?>_tree",
				store : store3,
				master_column_id : 'cNombre',
				columns : [{
							id : 'cNombre',
							header : "Sección",
							width : 160,
							sortable : true,
							dataIndex : 'cNombre'
						}, {
							header : "Web",
							width : 85,
							sortable : true,
							// renderer : Ext.util.Format.dateRenderer('m/d/Y'),
							editable: true,
							dataIndex : 'bWeb'
						}, {
							header : "Protegida",
							width : 85,
							sortable : true,
							// renderer : Ext.util.Format.dateRenderer('m/d/Y'),
							dataIndex : 'bBloqueada'
						}, {
							header : "Last Updated",
							width : 85,
							sortable : true,
							//renderer : Ext.app.renderDate,
							dataIndex : 'dAct'
						}],
				stripeRows : true,
				autoExpandColumn : 'cNombre',
				title : 'Array Grid',
				root_title : 'Sección',
				viewConfig : {
					enableRowBody : true
				}
			});
	// grid2.getSelectionModel().selectFirstRow();
	var secciones = new Ext.Panel({
				layout : 'border',
				title : "<?php echo $title;?>",
				id : "<?php echo $id;?>",
				iconCls : "<?php echo $icon;?>",
				region : 'center',
				closable : true,
				baseCls : 'x-plain',
				frame : true,
				items : [grid2]
			});

	// alert(grid);
	return secciones;
})();
