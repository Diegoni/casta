(function(){

    try {
        var id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        
        if (title == '') 
            title = _s('Email');
        if (icon == '') 
            icon = 'iconoEmailTab';
        if (id == '') 
            id = Ext.app.createId();
        var to = "<?php echo $to;?>";
        var cc = "<?php echo $cc;?>";
        var cco = "<?php echo $cco;?>";
        var msg = "<?php echo $msg;?>";
        var subject = "<?php echo $subject;?>";
        var file = "<?php echo $file;?>";
        
        var tagStore = new Ext.data.SimpleStore({
            fields: ['id', 'name'],
            data: [            /*
             * [ 'Architecture', 'Architecture' ], [ 'Sport',
             * 'Sport' ], [ 'Science', 'Science' ], [ 'Nature',
             * 'Nature' ], [ 'Technology', 'Technology' ], [
             * 'Travel', 'Travel' ]
             */
            ],
            sortInfo: {
                field: 'name',
                direction: 'ASC'
            }
        });
        
        var control = function(label, name, blank, value){
            return {
                triggerAction: 'all',
                minChars: 1,
                value: value,
                allowBlank: blank,
                allowAddNewData: true,
                //xtype: 'superboxselect',
                xtype: 'textfield',
                fieldLabel: label,
                emptyText: _s('Indique las direcciones de email'),
                resizable: true,
                name: name,
                id: id + name,
                anchor: '80%',
                store: tagStore,
                mode: 'local',
                displayField: 'name',
                valueField: 'id',
                extraItemCls: 'x-tag',
                // supressMultipleRemoveEvents : true,
                listeners: {
                    beforeadditem: function(bs, v, f){
                        // console.log('beforeadditem:', v);
                        // return false;
                    },
                    additem: function(bs, v){
                        // console.log('additem:', v);
                    },
                    beforeremoveitem: function(bs, v){
                        // console.log('beforeremoveitem:', v);
                        // return false;
                    },
                    removeitem: function(bs, v){
                        // console.log('removeitem:', v);
                    },
                    newitem: function(bs, v, f){
                        // v = v + '';
                        // v = v.slice(0, 1).toUpperCase() +
                        // v.slice(1).toLowerCase();
                        var newObj = {
                            id: v,
                            name: v
                        };
                        bs.addNewItem(newObj);
                    }
                }
            }
        }
        var To = control(_s('Para'), 'to', true, to);
        var CC = control(_s('CC'), 'cc', true, cc);
        var CCO = control(_s('CCO'), 'cco', true, cco);
        
        var form = new Ext.FormPanel({
            labelWidth: Ext.app.LABEL_SIZE,
            bodyStyle: 'padding:5px 5px 0',
            defaultType: 'textfield',
            region: 'center',
            closable: true,
            title: _s('Email'),
            iconCls: 'icon-email',
            cls: 'form-email',
            baseCls: 'x-plain',
            //frame: true,
            url: site_url('comunicaciones/email/send'),
            items: [To, CC, CCO, {
                xtype: 'textfield',
                name: 'subject',
                id: id + 'subject',
                value: subject,
                anchor: '90%',
                allowBlank: false,
                fieldLabel: _s('Asunto')
            }, {
                xtype: 'hidden',
                name: 'file',
                value: file
            }, Ext.app.formEditor({
                title: _s('Email'),
                anchor: '100% 100%',
                name: 'msg',
                id: id + 'msg',
                value: msg,
                allowBlank: false,
                id: id + 'msg'
            })],
            
            tbar: ['->', {
                text: _s('Imprimir'),
                iconCls: 'icon-print'
            }],
            buttons: [{
                text: _s('Enviar'),
                iconCls: 'icon-send',
                handler: function(b){
                    if (form.getForm().isValid()) {
                        var msg = Ext.getCmp(id + 'msg');
                        if (msg.getValue() == null) {
                            Ext.app.msgFly(title, _s('email-no-texto'));
                            msg.focus();
                            return;
                        }
                        var p = {
                            msg: Ext.getCmp(id + 'msg').getValue(),
                            to: Ext.getCmp(id + 'to').getValue(),
                            cc: Ext.getCmp(id + 'cc').getValue(),
                            cco: Ext.getCmp(id + 'cco').getValue(),
                            subject: Ext.getCmp(id + 'subject').getValue(),
                            file: file
                        };
                        /*p['msg'] = msg.getValue();
                         var to = Ext.getCmp(id + 'to');
                         console.log(to.getValue());*/
                        Ext.app.callRemote({
                            url: site_url('comunicaciones/email/send'),
                            form: form,
                            title: title,
                            wait: true,
                            params: p,
                            fnok: function(o){
                                // Ext.app.msgInfo(title, o.message);
                                panel.destroy();
                            }
                        });
                    }
                }
            }, {
                text: _s('Cerrar'),
                iconCls: 'icon-cancel',
                handler: function(b, f){
                    panel.destroy();
                }
            }]
        });
        
        var f = function(msg){
            var text = Ext.getCmp(id + 'msg').getValue();
            Ext.getCmp(id + 'msg').setValue(text + msg);
            //console.dir(Ext.getCmp(id));
            Ext.getCmp(id).setActiveTab(0);
        }
        var fget = function(){
            return Ext.getCmp(id + 'msg').getValue();
        }
        var templates = Ext.app.formTemplates({
            type: 'email',
            collapsible: false,
            collapsed: false,
            region: 'center',
            //height: 200,
            //minsize: 100,
            fnselect: f,
            fnget: fget
        });
        
        var panel = new Ext.TabPanel({
            xtype: 'tabpanel',
            activeTab: 0,
            baseCls: 'x-plain',
            //layout: 'border',
            title: title,
            id: id,
            iconCls: icon,
            region: 'center',
            closable: true,
            items: [form, templates]
        });
        
        return panel;
    } 
    catch (e) {
        console.dir(e);
    }
})();
