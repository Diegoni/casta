(function(){
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    if (title == '') 
        title = _s('Tareas');
    if (icon == '') 
        icon = 'iconoTareasTab';
    
    /*var eois = new Ext.form.ComboBox(Ext.app
     .combobox({url: "<?php echo site_url('eoi/escuela/search');?>")});*/
    var renderEstado = function(val, x, r){
		return _s('tareas_estado_' + val);
    }
    
    var model = [{
        column: {
            header: "",
            width: Ext.app.TAM_COLUMN_ICON,
            //dataIndex: 'id',
            sortable: true
        }
    }, {
        name: 'id',
        column: {
            header: _s("Id"),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true
        }
    }, {
        name: 'nIdTarea'
    }, {
        name: 'cDescripcion',
        column: {
            header: _s('cDescripcion'),
            width: Ext.app.TAM_COLUMN_TEXT,
            
            id: 'descripcion',
            sortable: true
        },
        ro: false
    }, {
        name: 'cComando',
        column: {
            header: _s("Tarea"),
            width: Ext.app.TAM_COLUMN_TEXT,
            editor: new Ext.form.TextField({
                listeners: {
                    focus: function(f){
                        f.selectText();
                    }
                }
            }),
            sortable: true
        },
        ro: false
    }, {
        name: 'nIdEstado',
        column: {
            header: _s('nIdEstado'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer: renderEstado,
            sortable: true
        },
        ro: false
    }, {
        name: 'cResultado',
        column: {
            header: _s('cResultado'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        },
        ro: true
    }, {
        name: 'dInicio',
        extras: {
            dateFormat: 'timestamp',
            startDay: Ext.app.DATESTARTDAY
        },
        type: 'date',
        column: {
            header: _s('dInicio'),
            width: Ext.app.TAM_COLUMN_DATE,
            renderer: Ext.app.renderDate,
            sortable: true
        },
        ro: true
    }, {
        name: 'dCreacion',
        extras: {
            dateFormat: 'timestamp',
            startDay: Ext.app.DATESTARTDAY
        },
        type: 'date',
        column: {
            header: _s('dCreacion'),
            width: Ext.app.TAM_COLUMN_DATE,
            renderer: Ext.app.renderDate,
            sortable: true
        },
        ro: true
    }, {
        name: 'cCUser',
        column: {
            header: _s('cCUser'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        },
        ro: true
    }, {
        name: 'dAct',
        extras: {
            dateFormat: 'timestamp',
            startDay: Ext.app.DATESTARTDAY
        },
        type: 'date',
        column: {
            header: _s('dAct'),
            width: Ext.app.TAM_COLUMN_DATE,
            renderer: Ext.app.renderDate,
            sortable: true
        },
        ro: true
    }];
    var stores = [];
    return Ext.app.createFormGrid({
        model: model,
        id: form_id,
        title: title,
        icon: icon,
        idfield: 'id',
        urlget: site_url('sys/tarea/get_list'),
        urldel: site_url('sys/tarea/del'),
        urlupd: site_url('sys/tarea/upd'),
        loadstores: stores,
        viewConfig: {
            enableRowBody: true,
            getRowClass: function(r, rowIndex, rowParams, store){
                return 'icon-tarea-' + r.data.nIdEstado;
            }
        },
        fn_pre: null,
        fn_add: null,
        load: true
    });
    
    
})();
