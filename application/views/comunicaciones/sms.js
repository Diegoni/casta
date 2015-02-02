(function(){
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    if (title == '') 
        title = _s('Enviar SMS');
    if (icon == '') 
        icon = 'iconoSMSTab';
    if (form_id == '') 
        form_id = Ext.app.createId();
    var renderOK = function(value, p, r){
        if (value == '1') {
			if (r.data.dEnviado == null)
				return _s('cell_grid_error');
			else
            	return _s('cell_grid_ok');
        }
        else 
            if (value == 0) {
                return _s('cell_grid_pending');
            }
            else {
                return _s('cell_grid_error');
            }
    }
    
    
     <?php
     
     $data = null;
     $data['name'] = 'store';
     $data['id'] = 'id';
     $data['url'] = site_url('comunicaciones/sms/get_list');
     $data['fields'][] = array('name' => 'id');
     $data['fields'][] = array('name' => 'nIdSMS');
     $data['fields'][] = array('name' => 'cTo');
     $data['fields'][] = array('name' => 'cMensaje');
     $data['fields'][] = array('name' => 'cCUser');
     $data['fields'][] = array('name' => 'cIdServidor');
     $data['fields'][] = array('name' => 'bDone');
     $data['fields'][] = array('name' => 'dCreacion');
     $data['fields'][] = array('name' => 'dEnviado');
     $data['fields'][] = array('name' => 'cEstado');
     $data['sort'] = 'dCreacion';
     $data['dir'] = 'desc';
     echo extjs_createjsonreader($data);
     ?>
     
    var f = function(msg){
        Ext.getCmp(form_id + 'msg').setValue(msg);
    };
    
    var fget = function(){
        return Ext.getCmp(form_id + 'msg').getValue();
    }
    
    var templates = Ext.app.formTemplates({
        type: 'sms',
        collapsible: true,
        collapsed: false,
        region: 'south',
        height: 200,
        minsize: 100,
        fnselect: f,
        fnget: fget
    });
    
    var fn_actualizar = function(){
        Ext.app.callRemote({
            url: site_url('comunicaciones/sms/check'),
            fnok: function(){
                store.reload();
            }
        });
    }
    var tbar = Ext.app.gridStandarButtons({
        id: form_id + '_grid'
    });

    var t2 = new Array();
	t2.push({
        text: _s('Actualizar estado'),
        iconCls: 'icon-actualizar',
        handler: fn_actualizar
    });
    tbar = t2.concat(tbar);
    var grid = new Ext.grid.GridPanel({
        width: 700,
        height: 500,
        title: _s('Histórico'),
        store: store,
        id: form_id + '_grid',
        iconCls: 'icon-history',
        stripeRows: true,
        loadMask: true,
        
        autoExpandColumn: "descripcion",
        
        // grid columns
        columns: [{
            header: _s('Id'),
            dataIndex: 'id',
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }, {
            header: _s('Número'),
            dataIndex: 'cTo',
            width: Ext.app.TAM_COLUMN_NUMBER,
            // hidden : true,
            sortable: true
        }, {
            header: _s('Mensaje'),
            dataIndex: 'cMensaje',
            id: 'descripcion',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }, {
            header: _s('Autor'),
            dataIndex: 'cCUser',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }, {
            header: _s('Fecha'),
            dataIndex: 'dCreacion',
            width: Ext.app.TAM_COLUMN_DATE,
            renderer: Ext.app.renderDate,
            dateFormat: 'timestamp',
            sortable: true
        }, {
            header: _s('Enviado'),
            dataIndex: 'bDone',
            width: Ext.app.TAM_COLUMN_TEXT,
            renderer: renderOK,
            sortable: true
        }, {
            header: _s('Fecha Envio'),
            dataIndex: 'dEnviado',
            width: Ext.app.TAM_COLUMN_DATE,
            renderer: Ext.app.renderDate,
            dateFormat: 'timestamp',
            sortable: true
        }, {
            header: _s('Estado'),
            dataIndex: 'cEstado',
            width: Ext.app.TAM_COLUMN_NUMBER,
            sortable: true
        }],
        
        viewConfig: {
            enableRowBody: true
        },
        bbar: Ext.app.gridBottom(store, true),
        tbar: tbar
    });

	var ctxRow = null;
	var contextmenu = new Ext.menu.Menu({
		allowOtherMenus: false,
		items: [{
			text: _s('Reenviar'),
			handler: function() {
				try {
					if (ctxRow) {
						Ext.app.execCmd({
							url: site_url('comunicaciones/sms/resend/' + ctxRow.data['id'])
						});
					}
				} catch (e) {
					console.dir(e);
				}
			},
			iconCls: 'icon-refresh'
		}]
	});

	grid.on('rowcontextmenu', function(gridPanel, rowIndex, e) {
		e.stopEvent();
		ctxRow = grid.store.getAt(rowIndex);
		contextmenu.showAt(e.getXY());
	});
    
    var smspanel = {
        xtype: 'tabpanel',
        region: 'center',
        activeTab: 0,
        baseCls: 'x-plain',
        items: [{
            title: _s('Texto'),
            iconCls: 'icon-sms',
            layout: 'border',
            frame: true,
            bodyStyle: 'padding: 5px 5px 0px;',
            items: [{
                region: 'center',
                xtype: 'form',
				cls: 'form-sms',
                baseCls: 'x-plain',
                labelWidth: 55,
                url: site_url('comunicaciones/sms/send'),
                defaultType: 'textfield',
                id: form_id + '_smsform',
                waitMsgTarget: true,
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _s('Número'),
                    id: form_id + 'to',
                    name: 'to',
                    value: '<?php if (!empty($to)) echo $to;?>',
                    anchor: '80%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    hideLabel: true,
                    id: form_id + 'msg',
                    name: 'msg',
                    anchor: '90% -53',
                    value: '<?php if (!empty($msg)) echo $msg;?>',
                    allowBlank: false
                }],
                buttons: [{
                    text: _s('Enviar'),
					iconCls: 'icon-send',
                    handler: function(button){
                        button.disable();
                        var form = Ext.getCmp(form_id + '_smsform');
                        Ext.app.sendForm({
                            form: form,
                            fnok: function(){
                                store.load();
                                button.enable();
                                form.getForm().reset();
                            },
                            fnnok: function(){
                                button.enable();
                            }
                        });
                    }
                }, {
                    text: _s('Limpiar'),
					iconCls: 'icon-clean',
                    handler: function(){
                        Ext.getCmp(form_id + '_smsform').getForm().reset();
                    }
                }]
            }, templates]
        }, grid]
    };
    
    var sms = new Ext.Panel({
        layout: 'border',
        title: title,
        id: form_id,
        iconCls: icon,
        region: 'center',
        closable: true,
        items: [smspanel]
    });
    
    store.load({
        params: {
            start: 0,
            limit: Ext.app.PAGESIZE
        }
    });
    return sms;
})();
