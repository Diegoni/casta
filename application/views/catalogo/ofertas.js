(function(){

    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    
    if (title == '') 
        title = _s('Ofertas');
    if (icon == '') 
        icon = 'iconoOfertasTab';
    if (form_id == '') 
        form_id = Ext.app.createId();
    
    var form = Ext.app.formGeneric();
    
    var list_grids = [form_id + '_lineas_grid']
    
    // Carga
    var fn_load = function(id){
        Ext.app.formLoadList({
            list: list_grids,
            params: {
                where: 'nIdOferta=' + id
            }
        });
    }
    
    // Borrado
    var fn_reset = function(){
        Ext.app.formResetList({
            list: list_grids,
            params: {
                where: 'nIdOferta=-1'
            }
        });
    }
    
    var fn_enable_disable = function(){
        var list_buttons = [form.idform + 'btn_lineas'];
        (form.getId() > 0) ? art.enable() : art.disable();
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
        url: site_url('catalogo/oferta'),
        fn_load: fn_load,
        fn_reset: fn_reset,
        fn_enable_disable: fn_enable_disable
    });
    
    
    var model = [{
        name: 'id',
        column: {
            header: _s("Id"),
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'nIdLibro'
    }, {
        name: 'cTitulo',
        column: {
            id: 'descripcion',
            header: _s("cTitulo"),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        },
        ro: true
    }];
    
    var grid = Ext.app.createFormGrid({
        model: model,
        id: form_id + "_lineas",
        idfield: 'id',
        urlget: site_url("catalogo/articulo/get_list"),
        urldel: site_url("catalogo/oferta/del_items"),
        anchor: '100% 75%',
        load: false
    });
    var g = Ext.getCmp(form_id + "_lineas_grid");
    var cm_lineas = fn_contextmenu();
    var contextmenu = Ext.app.addContextMenuLibro(g, 'nIdLibro', cm_lineas);
    cm_lineas.setContextMenu(contextmenu)
    //addMenuPedir(cm_lineas);
    addMenuDocumentos(cm_lineas);
    addMenuVentas(cm_lineas);
    
    var tipos = Ext.app.combobox({
        url: site_url('catalogo/tipooferta/search'),
        id: 'nIdTipoOferta',
        label: _s('Tipo'),
        anchor: '90%',
        allowBlank: true
    });
    
    
    var select = function(){
        console.log('Select');
        if (form.getId() != null) {
            var url = site_url('catalogo/articulo/upd');
            var idl = parseInt(art.getValue());
            Ext.app.callRemote({
                url: url,
                params: {
                    id: idl,
                    nIdOferta: form.getId()
                },
                fnok: function(){
                    Ext.getCmp(form_id + '_lineas_grid').store.load();
                    art.setValue();
                }
            });
        }
        else {
            Ext.app.msgFly(title, _s('mensaje_falta_ubicacion'));
            ctl.focus();
        }
    }
    
    var ctl = new Ext.form.TextField({
        enableKeyEvents: true,
        fieldLabel: _s('Id')
    });
    
    ctl.on('keypress', function(t, e){
        if (e.getKey() === e.ENTER) {
            var tx = t.getValue();
            art.store.load({
                params: {
                    query: tx,
                    start: 0,
                    limit: Ext.app.AUTOCOMPLETELISTSIZE
                },
                callback: function(c){
                    t.setValue();
                    //console.dir(c);
                    if (c.length == 1) {
                        art.setValue(c[0].id);
                        select();
                    }
                    else {
                        art.setValue(tx);
                        art.doQueryEx();
                    }
                }
            });
        }
    });
    var art = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/articulo/search'),
        label: _s('Artículo'),
        anchor: '90%',
        fnselect: select
    }));
    
    
    var controls = [{
        xtype: 'textfield',
        id: 'cDescripcion',
        fieldLabel: _s('cDescripcion'),
        anchor: '90%'
    
    }, tipos, {
        xtype: 'numberfield',
        id: 'fValor',
        allowBlank: true,
        allowNegative: false,
        allowDecimals: true,
        width: 30,
        maxValue: 100,
        minValue: 0,
        decimalPrecision: Ext.app.DECIMALS,
        fieldLabel: _s('fValor')
    },/*ctl,*/ art, grid];
    
    form.addTab({
        title: _s('General'),
        iconCls: 'icon-general',
        items: {
            xtype: 'panel',
            layout: 'form',
            cls: 'form-oferta',
            items: form.addControls(controls)
        }
    });
    
    form.addTabUser();
    
    var fn_open = function(id){
        form.load(id);
        form.selectTab(0);
    }
    
         <?php $modelo = $this->reg->get_data_model();?>
         var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'catalogo.oferta', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
         var fn_open = function(id){
         form.load(id);
         form.selectTab(0);
         }
     
     form.addTab({
     title: _s('Búsqueda'),
     iconCls: 'icon-search',
     items: Ext.app.formSearchForm({
     grid: grid_search,
     id_grid: form_id + '_g_search_grid'
     })
     });
    return form.show(open_id);
})();
