(function(){
        var open_id = "<?php echo isset($open_id)?$open_id:'';?>";
        var form_id = "<?php echo isset($id)?$id:'';?>";
        var title = "<?php echo isset($title)?$title:'';?>";
        var icon = "<?php echo isset($icon)?$icon:'';?>";
        if (title == '') 
            title = _s('Etiquetas');
        if (icon == '') 
            icon = 'iconoImprimirEtiquetasTab';

        var fn_open = function(id){
        	console.log('ID ' + id);
        }

         <?php $modelo = $this->reg->get_data_model();?>
        var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'catalogo.grupoetiqueta', $this->reg->get_id(), null, FALSE, null, 'fn_open: fn_open');?>;
        
        var grid = Ext.getCmp(form_id + '_g_search_grid');
        grid.store.baseParams.sort = 'nIdPaquete';
        grid.store.baseParams.dir = 'DESC';
        grid.store.load();
		grid.on('rowdblclick', function(sm, rowIdx, e) {
			var sm = grid.getSelectionModel();
			if(sm.hasSelection()) {
				var sel = sm.getSelected();
				Ext.app.callRemote({
					url: site_url('catalogo/grupoetiqueta/imprimir/' + sel.data.id)
				});
			}
		});

        var panel = new Ext.Panel({
            layout: 'border',
            title: title,
            id: id,
            iconCls: icon,
            region: 'center',
            closable: true,
            baseCls: 'x-plain',
            frame: true,
            items: [grid_search]
        });
        
        return panel;
})();
