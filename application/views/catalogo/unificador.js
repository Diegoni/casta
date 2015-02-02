(function(){

    try {
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        
        if (form_id == '') 
            form_id = Ext.app.createId();
        
        var store = Ext.app.createStore({
            id: 'id',
            model: [{
                name: 'id'
            }, {
                name: 'text'
            }]
        });
        
        var store2 = Ext.app.createStore({
            url: "<?php echo $url_search;?>",
            id: 'id',
            model: [{
                name: 'id'
            }, {
                name: 'text'
            }]
        });
        store2.baseParams = {
            start: 0,
            limit: Ext.app.AUTOCOMPLETELISTSIZE
        }
        
        
        var origen = new Ext.form.ComboBox(Ext.app.autocomplete({
            allowBlank: false,
            label: _s('Correcto'),
            anchor: '100%',
            url: "<?php echo $url_search;?>"
        }));
        
        var destino = new Ext.form.TextField({
            enableKeyEvents: true,
            fieldLabel: _s('Duplicado'),
            //name: 'destino',
            anchor: '50%'
        });
        
        destino.on('keypress', function(t, e){
            if (e.getKey() === e.ENTER) {
                store2.load({
                    params: {
                        query: t.getValue()
                    },
                    callback: function(r, o, s){
                        Ext.each(r, function(item){
                            //console.dir(item);
                            try {
                                if ((store.find('id', item.data.id) == -1) && (item.data.id != origen.getValue())) {
                                    //console.log('ADD');
                                    Ext.app.comboAdd(store, item.data.id, item.data.text);
                                }
                                else {
                                    //console.log('NO ADD');
                                }
                            } 
                            catch (e) {
                                console.dir(e);
                            }
                        });
                    }
                });
                t.setValue('');
            }
        });
        
        var sm = new Ext.grid.CheckboxSelectionModel();
        
        var grid = new Ext.grid.GridPanel({
            region: 'center',
            autoExpandColumn: "descripcion",
            loadMask: true,
            stripeRows: true,
            store: store,
            anchor: "100% 85%",
            sm: sm,
            columns: [sm, {
                header: _s('Id'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'id',
                sortable: true
            }, {
                header: _s('Nombre'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'text',
                id: 'descripcion',
                sortable: true
            }],
            tbar: [{
                text: _s('Borrar lista'),
                iconCls: 'icon-clean',
                handler: function(button){
                    store.removeAll();
                }
            }]
        });
        
        var form = new Ext.FormPanel({
            labelWidth: Ext.app.LABEL_SIZE,
            bodyStyle: 'padding:5px 5px 0',
            defaultType: 'textfield',
            region: 'center',
            closable: true,
            baseCls: 'x-plain',
            frame: true,
            items: [origen, destino, grid],
            buttons: [{
                text: _s('Unificar'),
                iconCls: 'icon-unificar',
                handler: function(button){
                    var id1 = origen.getValue();
                    if (id1 == '') {
                        Ext.app.msgFly(title, _s('no-registro-origen'));
                        origen.focus();
                        return;
                    }
                    
                    var codes = '';
                    var sel = sm.getSelections();
                    for (var i = 0; i < sel.length; i = i + 1) {
                        if (sel[i].data.id != id1) {
                            codes += sel[i].data.id + ';';
                        }
                    }
                    if (codes.length == 0) {
                        Ext.app.msgFly(title, _s('no-registro-seleccionadas'));
                        return;
                    }
                    var url = "<?php echo $url;?>";
                    // console.dir(codes);
					form.getEl().mask(Ext.app.TEXT_CARGANDO);
                    Ext.app.callRemote({
                        url: url,
                        title: title,
                        timeout: false,
                        //wait: true,
                        params: {
                            id1: id1,
                            id2: codes
                        },
                        fnok: function(){
                            store.removeAll();
							form.getEl().unmask();
                        },
                        fnnok: function(){
							form.getEl().unmask();
                        }
                    });
                }
            }]
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
            items: [form]
        });
        
        return panel;
    } 
    catch (e) {
        console.dir(e);
    }
})();
