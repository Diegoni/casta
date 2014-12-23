function(data)
{
	if (data.version != null) {
		if (data.version.version != Ext.app.client_version) {
			var msg = _s('q-nuevaversion');
			msg = msg.replace('%1', data.version.version);
			msg = msg.replace('%2', Ext.app.client_version);
			Ext.app.client_version = data.version.version;
			Ext.app.msgFly(_s('title'), msg, true, 'icon-alert', 'form-update');
			/*
			Ext.Msg.show({
				title: _s('title'),
				buttons: Ext.MessageBox.YESNO,
				icon: Ext.Msg.QUESTION,
				msg: msg,
				fn: function(btn, text){
					if (btn == 'yes') {
						Ext.app.askexit = false;
						window.location = site_url();
					}
				}
			});*/
		}
	}
}

