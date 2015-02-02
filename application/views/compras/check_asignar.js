(function() {

	var form_id = Ext.app.createId();

    var txtISBN = new Ext.form.TextField({
    	fieldLabel: _s('Código'),
        enableKeyEvents : true
    }); 

    txtISBN.on('specialkey',
        function(o, e){
            if (e.getKey() == e.ENTER){
                Ext.app.callRemote({
                    url: site_url('compras/albaranentrada/check'),
                    params: {
                        code: txtISBN.getValue(),
                        id: <?php echo $id;?>
                    },
                    fnok: function (res) {
                        var reg = {
                            id: txtISBN.getValue(),                            
                            titulo: res.titulo,
                            destino: res.text
                        }
                        store.insert(0, new ComboRecord(reg));
                        var detailEl = Ext.getCmp(form_id + '_html').body;                        
                        detailEl.applyStyles({
                            'background-color': '#FFFFFF'
                        });
                        var text = '<div style="font-size: 200%;color:green;align:center;">' + res.text + '</div>'+
                        '<div style="font-size: 150%;color:black;align:center;">' + res.titulo + '</div>';
                        detailEl.update(text);
                        txtISBN.setValue(null);
                    }                            
                });
            }
        },
        this
    );

    var model = [{
        name: 'id'
    }, {
        name: 'titulo'
    }, {
        name: 'destino'
    }];
    
    var store = new Ext.data.ArrayStore({
        fields: model
    });

    var grid = new Ext.grid.GridPanel({
        region: 'center',
        autoExpandColumn: "descripcion",
        loadMask: true,
        stripeRows: true,
        store: store,
        height: 300,
        id: id + "_grid",
        columns: [{
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true,
            hidden: true
        }, {
            header: _s('cTitulo'),
            width: Ext.app.TAM_COLUMN_TEXT*2,
            dataIndex: 'titulo',
            sortable: true
        }, {
            header: _s('Destino'),
            width: Ext.app.TAM_COLUMN_TEXT*2,
            id: 'descripcion',
            dataIndex: 'destino',
            sortable: true
        }]
    });

    var controls = [txtISBN, {
            xtype: 'iframepanel',
            id: form_id + '_html',
            height: 90,
            region: 'center'
        }, grid];

	var form = Ext.app.formStandarForm({
		controls: controls,
		disableok: true,
		autosize: false,
		height: 500,
		icon: 'iconoAsignarTab',
		width: 600,
		title: _s('Asignación de albarán'),
	});

	form.show();
	return;
})();