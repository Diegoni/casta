(function(){
    try {

		var id = '<?php echo $id;?>';
		
		var fn_open = function(id) {
			console.log('open ' + id);
			//form.load(id);
			//form.selectTab(0);
		}
	
		var form = Ext.app.formGeneric();
	form.init({
				id : 'a',
				title : 'Test',
				url : site_url('mailing/mailing')
			});

		<?php
		$obj =& get_instance();
		$obj->load->model('mailing/m_contacto'); 
		$modelo2 = $obj->m_contacto->get_data_model();
		?>
		var grid = <?php echo extjs_creategrid($modelo2, $id.'_g_search', null, null, 'mailing.contacto', $obj->m_contacto->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;

		var grid_tab = Ext.app.formSearchForm({        
			grid: grid,
			id_grid: id + '_g_search_grid'
		});            
	
    form.addTab({
				title : _s('Notas'),
				iconCls : 'icon-notes',
				items : grid_tab
			});
	
	return form.show();

    } 
    catch (e) {
		console.log('error');
		console.log(e);
		//console.dir(e);
		//alert('error');
    }
})();
