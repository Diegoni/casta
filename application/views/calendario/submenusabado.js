var tb = grid.getTopToolbar();
tb.insert(0, {
	text : _s('Crear año'),
	handler : function() {
            Ext.Msg.prompt(_s('Crear año'), _s('Crear año'), function(ok, v){
                if (ok != 'ok') 
                    return;
                Ext.app.callRemote({
                    params: {
                        year: v
                    },
                    title: _s('Año'),
                    url: site_url('calendario/sabado/crear'),
					fnok: function() {
						grid.store.load();
					}
                })
            }, null, null, (new Date()).getYear() + 1900);
	},
	iconCls : 'icon-calendar-sabados'
});