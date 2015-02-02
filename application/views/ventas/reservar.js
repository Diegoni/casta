(function(){
    var form_id = Ext.app.createId();
    
    var id_libro = parseInt('<?php echo $nIdLibro;?>');
    var id_seccion = parseInt('<?php echo $nIdSeccion;?>');
    var cantidad = parseInt('<?php echo $nCantidad;?>');
    
    var pedidos = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('ventas/pedidocliente/search'),
        id: form_id + '_pedidos',
        label: _s('Pedidos')
    }));
    
    pedidos.store.on('load', function(s, r){
        if (s.getTotalCount() > 0) {
            pedidos.setValue(s.getAt(s.getTotalCount() - 1).data.id);
        }
    });
    
    var id_cliente = null;
    var loadpedidos = function(id){
        if (id == null) {
            Ext.app.msgError(title, _s('no_cliente'));
            return;
        }
        id_cliente = id;
        pedidos.store.removeAll();
        pedidos.store.baseParams = {
            where: 'nIdCliente=' + id + '&nIdEstado=1'
        };
        pedidos.store.load();
    };
    
    var clientefield = new Ext.form.ComboBox(Ext.app.autocomplete({
        fieldLabel: _s('Cliente'),
        anchor: '100%',
        url: site_url('clientes/cliente/search'),
        create: true,
        fnselect: function(id){
            loadpedidos(id);
        }
    }));
    
    clientefield.load = function(id){
		id_cliente = id;
        fn_docs_load_cliente({
            id: id,
            clientefield: clientefield
        });
    }
    
    var cliente = {
        xtype: 'compositefield',
        fieldLabel: _s('Cliente'),
        msgTarget: 'side',
        anchor: '100%',
        items: [clientefield, {
            xtype: 'tbbutton',
            iconCls: "icon-add",
            tooltip: _s('nuevo-clienteproveedor'),
            handler: function(){
                var c = Ext.getCmp(clientefield.id);
                console.dir(c);
                
                Ext.app.callRemote({
                    url: site_url('clientes/cliente/alta'),
                    params: {
                        text : c.getValue(),
                        cmpid: c.getId()
                    }
                });
            }
        }]
    }
    var descuento = new Ext.form.NumberField({
        fieldLabel: _s('Descuento'),
        allowNegative: false,
        allowDecimals: true,
        value: 0,
        minValue: 0,
        maxValue: 100,
        decimalPrecision: Ext.app.DECIMALS,
        //style: 'text-align:center',
        selectOnFocus: true,
        width: 30
    });
    var ref = new Ext.form.TextField({
        fieldLabel: _s('Referencia'),
        selectOnFocus: true,
        anchor: '100%'
    });
    
    var controls = [cliente, pedidos, new Ext.ux.form.Spinner({
        fieldLabel: _s('Cantidad'),
        id: form_id + "_cantidad",
        value: cantidad,
        width: 60,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    }), descuento, ref];
    
    var url = site_url('compras/resposicion/pedir');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: _s('Pedir'),
        icon: 'iconoPedidoClienteTab',
        //width: Ext.app.PEDIRWIDTH,
        fn_ok: function(res){
        
            var idl = id_libro;
            var idpd = pedidos.getValue();
            var idc = id_cliente;
            var ids = id_seccion;
            var qt = Ext.getCmp(form_id + "_cantidad").getValue();
            
            Ext.app.callRemote({
                url: site_url('ventas/pedidocliente/reservar'),
                params: {
                    id: idl,
                    idpd: idpd,
                    idc: idc,
                    ids: ids,
                    cantidad: qt,
                    dto: descuento.getValue(),
                    ref: ref.getValue()
                },
                fnok: function(obj){
                    form.close();
                }
            });
        }
    });
    
    form.show();
    
    return;
})();
