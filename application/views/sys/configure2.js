(function(){
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    if (title == '') 
        title = _s('Configuración');
    if (icon == '') 
        icon = 'iconoConfiguracionTab';
    if (form_id == '') 
        form_id = Ext.app.createId();
    var terminal = <?php echo (isset($terminal) && $terminal)?'true':'false';?>;

    var items = [];
    <?php
    $tabs = array();
    $values = array();
    $reset = '';
    $values_terminal = '';
    $load = '';
    $count  = 1;
    $pre_create = '';
    foreach ($groups as $name => $group)
    {
        $controls = array();
        foreach ($group['vars'] as $control)
        {
            $this->lang->line($control);
            $v = ($terminal)?"Ext.app.get_config('{$control}', 'terminal', false)":"'{$items[$control]['value']}'";
            if ($items[$control]['type'] == 'text' || $items[$control]['type'] == 'int' || $items[$control]['type'] == 'float')
            {
                $controls[] = "{
                    xtype: 'textfield',
                    fieldLabel: _s('{$control}'),
                    id: form_id + '_{$control}',
                    name: '{$control}',
                    value: {$v}, 
                    anchor: '80%',
                    allowBlank: true
                }";
            }
            elseif ($items[$control]['type'] == 'bool')
            {
                $controls[] = "{
                    xtype: 'checkbox',
                    fieldLabel: _s('{$control}'),
                    id: form_id + '_{$control}',
                    name: '{$control}',
                    checked: {$v} == 'true',
                    //value: {$v},
                    allowBlank: true
                }";
            }
            elseif ($items[$control]['type'] == 'list')
            {
                $data = preg_split('/\|/', $items[$control]['values']);
                foreach ($data as $k => $v2)
                {
                    $data[$k] = "['{$v2}']";
                }
                $data = implode(',', $data);
                $controls[] = "new Ext.form.ComboBox({
                    store: new Ext.data.ArrayStore({
                                fields: ['text'],
                                data : [{$data}]
                            }),
                    fieldLabel: _s('{$control}'),
                    name: '{$control}',
                    id: form_id + '_{$control}',
                    displayField:'text',
                    typeAhead: true,
                    mode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    value: {$v},
                    //emptyText:'Select a state...',
                    selectOnFocus:true
                    })";
            }
            elseif ($items[$control]['type'] == 'combo')
            {
                $items[$control]['values'] = str_replace('.', '/', $items[$control]['values']);
                $pre_create .= "var combo_{$count} = new Ext.form.ComboBox(Ext.app.combobox({
                    url: site_url('{$items[$control]['values']}'),
                    label: _s('{$control}'),
                    //autoload: true,
                    //value: {$v},
                    name: '{$control}',
                    id: form_id + '_{$control}',
                    anchor: '90%'
                    }));\n";
                $controls[] = "combo_{$count}";
                $load .= "combo_{$count}.getStore().load({
                            callback : function() {
                                combo_{$count}.setValue(parseInt($v));
                                //combo_{$count}.originalValue = $v;
                                //combo_{$count}.reset();
                            }
                        });\n";
                ++$count;
            }
            elseif ($items[$control]['type'] == 'combo2')
            {
                $items[$control]['values'] = str_replace('.', '/', $items[$control]['values']);

                $pre_create .= "var combo_{$count} = (Ext.app.autocomplete2({
                    url: site_url('{$items[$control]['values']}'),
                    fieldLabel: _s('{$control}'),
                    //autoload: true,
                    value: {$v},
                    name: '{$control}',
                    id: form_id + '_{$control}',
                    anchor: '90%'
                    }));\n";
                $controls[] = "combo_{$count}";
                $load .= "Ext.getCmp(combo_{$count}.id).setValue(parseInt($v));\n";
                ++$count;
            }
            $values[] = "'{$control}': Ext.getCmp(form_id + '_{$control}').getValue()";
            $values_terminal .= "Ext.app.set_config('{$control}', Ext.getCmp(form_id + '_{$control}').getValue(), 'terminal');";
            $reset .= "Ext.getCmp(form_id + '_{$control}').reset();";
        }
        $controls = implode(',', $controls);

        $tabs[] = "{
            title: _s('{$name}'),
            iconCls: '{$group['icon']}',
            layout: 'border',
            frame: true,
            bodyStyle: 'padding: 5px 5px 0px;',
            items: [{
                region: 'center',
                xtype: 'form',
                //cls: 'form-sms',
                baseCls: 'x-plain',
                labelWidth: 250,
                defaultType: 'textfield',                
                waitMsgTarget: true,
                id: form_id + '_form',
                items: [{$controls}]
            }]
        }";
    }
    $values = implode(',', $values);
    ?>
    <?php echo $pre_create;?>
    var panel = {
        xtype: 'tabpanel',
        region: 'center',
        activeTab: 0,
        baseCls: 'x-plain',
        items: [<?php echo implode(',', $tabs);?>],
        buttons: [{
            text: _s('Guardar'),
            iconCls: 'icon-save',
            handler: function(button){
                <?php if ($terminal):?>
                <?php echo $values_terminal;?>
                Ext.app.reload_constants();
                <?php else: ?>
                button.disable();
                var ctl = {<?php echo $values;?>};
                Ext.app.callRemote({
                    url: site_url('<?php echo $url;?>'),
                    params: ctl,
                    fnok: function(){
                        Ext.app.reload_constants();
                        button.enable();
                    },
                    fnnok: function(){
                        Ext.app.reload_constants();
                        button.enable();
                    }
                });
                <?php endif; ?>
            }
        }]};
    var form = new Ext.Panel({
        layout: 'border',
        title: title,
        id: form_id,
        iconCls: icon,
        region: 'center',
        closable: true,
        items: [panel]
    });

    //console.log(Ext.app.get_config('catalogo.buscar.showportada', 'terminal', false));
    try {
    <?php echo $load;?>
    } catch(e) { 
        console.dir(e); 
    }
    return form;
})();
