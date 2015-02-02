(function() {

	Ext.Msg.prompt(_s('Eliminar stock contado'), _s('Nombre backup'), function(ok, v) {
		if(ok != 'ok')
			return;
		Ext.app.callRemote({
			url : site_url('stocks/stockcontado/reset'),
			params : {
				name : v
			}
		});
	});
	return;

})();
