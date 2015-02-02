(function() {
	Ext.Msg.prompt(_s('estado-horas'), _s('Año'), function(ok, v) {
		if(ok != 'ok')
			return;
		Ext.app.callRemote({
			params : {
				year : v
			},
			title : _s('estado-horas'),
			url : site_url('calendario/calendario/estado_horas')
		})
	}, null, null, (new Date()).getYear() + 1900);

	return;
})();
