(function(){

    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    
    if (title == '') 
        title = _s('Albarán Agrupado');
    if (icon == '') 
        icon = 'iconoConcursoGeneralTab';
    if (form_id == '') 
        form_id = Ext.app.createId();
    
    var form = Ext.app.formGeneric();
    
    var list_grids = [form_id + '_albaranes_grid']
    
    // Carga
    var fn_load = function(id){
        Ext.app.formLoadList({
            list: list_grids,
            params: {
                where: 'nIdAlbaranAgrupado=' + id
            }
        });
    }
    
    // Borrado
    var fn_reset = function(){
        Ext.app.formResetList({
            list: list_grids,
            params: {
                where: 'nIdAlbaranAgrupado=-1'
            }
        });
    }
    
    var fn_enable_disable = function(){
        var list_buttons = [form.idform + 'btn_albaranes'];
        
        Ext.app.formEnableList({
            list: list_buttons,
            enable: (form.getId() > 0)
        });
        
        Ext.app.formEnableList({
            list: list_grids,
            enable: (form.getId() > 0)
        });
    }
    
    form.init({
        id: id,
        title: title,
        icon: icon,
        url: site_url('concursos/albaranagrupado'),
        fn_load: fn_load,
        fn_reset: fn_reset,
        fn_enable_disable: fn_enable_disable
    });
    
    var model = [{
        name: 'nIdAlbaran',
        column: {
            header: _s("Id"),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true
        }
    }, {
        name: 'id'
    }, {
        name: 'cPedido',
        column: {
            id: 'descripcion',
            header: _s("Pedido"),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        },
        ro: true
    }];
    
    var albaranes = Ext.app.createFormGrid({
        model: model,
        id: form_id + "_albaranes",
        idfield: 'nIdPedido',
        urlget: site_url("concursos/albaran/get_list"),
        urldel: site_url("concursos/albaranagrupado/del_items"),
        anchor: '100% 85%',
        load: false
    });
    
    //form.addTab(albaranes);
    var bibliotecas = Ext.app.combobox({
        url: site_url('concursos/biblioteca2/search'),
        id: 'nIdBiblioteca',
        label: _s('cBiblioteca'),
        anchor: '100%',
        allowBlank: false
    });

    var facturas = Ext.app.combobox({
        url: site_url('concursos/facturaconcurso/search'),
        id: 'nIdFactura',
            disabled: true,		
        label: _s('Factura'),
        anchor: '100%',
        allowBlank: true
    });
    
    var controls = [bibliotecas, facturas, albaranes];
    
    form.addTab({
        title: _s('General'),
        iconCls: 'icon-general',
        items: {
            xtype: 'panel',
            layout: 'form',
            items: form.addControls(controls)
        }
    });
        
    form.addTabUser();
    
    var fn_open = function(id){
        form.load(id);
        form.selectTab(0);
    }
    
    
     <?php
     $obj =& get_instance();
     $obj->load->model('concursos/m_albaranagrupado');
     $modelo2 = $obj->m_albaranagrupado->get_data_model();
     ?>
     
     var grid_search_m = <?php echo extjs_creategrid($modelo2, $id.'_g_search', null, null, 'concursos.albaranagrupado', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
     
     
     form.addTab({
     title: _s('Búsqueda'),
     iconCls: 'icon-search',
     items: Ext.app.formSearchForm({
     grid: grid_search_m,
     id_grid: form_id + '_g_search_grid'
     })
     });
     
    // Acciones
    
    var addAlbaranes = function(padre, tema){
        var model = [{
            name: 'nIdAlbaran'
        }, {
            name: 'id'
        }, {
            name: 'nIdPedido'
        }, {
            name: 'cPedido'
        }];
        
        var b = Ext.getCmp(bibliotecas.id);
        
        var url = site_url("concursos/albaran/get_sinagrupar/" + b.getValue());
        var store = Ext.app.createStore({
            model: model,
            url: url
        });
        
        var sm = new Ext.grid.CheckboxSelectionModel();
        
        var columns = [sm, {
            header: _s("Id"),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true
        }, {
            id: 'descripcion',
            header: _s("Pedido"),
            dataIndex: 'cPedido',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }];
        
        var grid = new Ext.grid.GridPanel({
            store: store,
            anchor: '100% 80%',
            height: 400,
            autoExpandColumn: 'descripcion',
            stripeRows: true,
            loadMask: true,
            sm: sm,
            
            bbar: Ext.app.gridBottom(store, true),
            
            // grid columns
            columns: columns
        });
        
        var controls = [grid];
        
        var form = Ext.app.formStandarForm({
            controls: controls,
            autosize: false,
            height: 500,
            title: _s('Añadir albaranes'),
            fn_ok: function(){
                var sel = grid.getSelectionModel().getSelections();
                var url = site_url('concursos/albaranagrupado/add_items')
                var ids = [];
                Ext.each(sel, function(item){
                    ids.push(item.data.id);
                });
                ids = implode(';', ids);
                Ext.app.callRemote({
                    url: url,
                    params: {
                        id: padre.getId(),
                        ids: ids
                    },
                    fnok: function(){
                        fn_load(padre.getId());
                    }
                })
            }
        });
        store.load();
        form.show();        
    };
        
    // Acciones
    form.addAction({
        text: _s('Añadir albaranes'),
        handler: function(){
            addAlbaranes(form);
        },
        //iconCls: 'icon-stock',
        id: form.idform + 'btn_albaranes'
    });
    
    return form.show(open_id);
})();
