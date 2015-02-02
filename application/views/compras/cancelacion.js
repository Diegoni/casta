(function(){
    try {
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Cancelación pedido proveedor');
        if (icon == '') 
            icon = 'iconoCancelacionPedidoProveedorTab';
        
        var list_grids = []
        
        var iva = null;
        // Carga
        var fn_load = function(id, res){
			data_load = res;
            notas.load(id);
            fn_load_direcciones(res.nIdProveedor, res.nIdDireccion);
            fn_load_cliente(res.nIdProveedor);
            try {
                var panel = Ext.getCmp(form_id + "details-panel");
                panel.setSrc(res.info);
            } 
            catch (e) {
                console.dir(e);
            }
        }
        
        var fn_save = function(id, data){
            data['nIdDireccion'] = Ext.getCmp(direcciones.id).getValue();
            data['nIdProveedor'] = Ext.getCmp(clientefield.id).getValue();
            return data;
        }
        
        // Borrado
        var fn_reset = function(){
            var panel = Ext.getCmp(form_id + "details-panel");
            panel.setSrc('about:blank');
        }
        
        var fn_enable_disable = function(form){
  			Ext.app.formEnableList({
                list: [form.idform + 'btn_enviar'],
                enable: (form.getId() > 0)
            });
		}
        
        var data_load = null;
        var ultimo_texto = null;
        var ultimo_cambio = null;
        var ultimo_id = null;
        var ultimo_title = null;
        var tooltip_cliente = null;
        var cliente_datos = null;
        var cliente_id = null;
        var s_defecto = null;
        var s_vedadas = null;
        var id_defecto = null;
        
        var fn_set_data = function(data){
            if (data.cliente_id) 
                cliente_id = data.cliente_id;
            if (data.cliente_datos) 
                cliente_datos = data.cliente_datos;
            if (data.info_button) 
                info_button = data.info_button;
            if (data.data_load) 
                data_load = data.data_load;
            if (data.title) 
                title = data.title;
            if (data.direcciones) 
                direcciones = data.direcciones;
            if (data.s_defecto) 
                s_defecto = data.s_defecto;
            if (data.s_vedadas) 
                s_vedadas = data.s_vedadas;
            if (data.tooltip_cliente) {
                tooltip_cliente = data.tooltip_cliente;
				try {
					msg.update(data.tooltip_cliente);
				} 
				catch (e) {
				}
            }
        }
        
        var fn_get_data = function(){
            return {
                cliente_id: cliente_id,
                cliente_datos: cliente_datos,
                tooltip_cliente: tooltip_cliente,
                info_button: info_button,
                data_load: data_load,
                title: title,
                direcciones: direcciones,
                s_defecto: s_defecto,
                s_vedadas: s_vedadas
            }
        }

		var fn_lang = function(){
			return getLang(data_load);
        }
        
        // Formulario
        var form = Ext.app.formGeneric();
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('compras/cancelacion'),
            fn_load: fn_load,
            fn_save: fn_save,
			fn_lang: fn_lang,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
        
        
        var controles = documentosProveedor(form, 'nIdDireccion', fn_get_data, fn_set_data, Ext.app.PERFIL_RECLAMACIONES);
        
        // Carga direcciones
        var cliente = controles.cliente;
        var info_button = controles.info_button;
        var clientefield = controles.clientefield;
        var direcciones = controles.direcciones;
        var fn_load_direcciones = controles.fn_load_direcciones;
        var fn_load_cliente = controles.fn_load_cliente;
        
        var msg = new Ext.Panel({
            cls: 'info-msg',            
			autoScroll: true,
			anchor: '100%'
            /*height: 80,
            width: 600*/
        });
        
        // Controles normales
        var controls = [cliente, msg];
        
        // General
        form.addTab({
            title: _s('Vista'),
            iconCls: 'icon-report',
            items: {
                cls: 'form-cancelacion',
                id: form_id + "details-panel",
                xtype: 'iframepanel'
            }
        });
        
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
				cls: 'form-cancelacion',
                items: form.addControls(controls)
            }
        });

        var notas = Ext.app.formNotas();
        var grid_notas = notas.init({
            id: form_id + "_notas",
            url: site_url('compras/cancelacion'),
            mainform: form
        });
		
        form.addTab(new Ext.Panel({
            layout: 'border',
            id: form_id + "_notas",
            title: _s('Histórico'),
            iconCls: 'icon-history',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: grid_notas
        }));
        
        // Usuarios
        form.addTabUser();
        // Búsqueda
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
         <?php $modelo = $this->reg->get_data_model(array('nIdDireccion'));?>
         <?php echo 'var grid_search = ' . extjs_creategrid($modelo, $id.'_g_search', null, null, 'compras.cancelacion', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
         
         form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search,
                //audit: false,
                id_grid: form_id + '_g_search_grid'
            })
         });

        var fn_enviar = function(){
            documentosEnviar(form, _s('Enviar reclamación'), site_url('compras/cancelacion/send'));
        }
        
        form.addAction({
            text: _s('Enviar'),
            handler: function(){
                fn_enviar();
            },
            iconCls: 'icon-send',
            id: form.idform + 'btn_enviar'
        });
        
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
