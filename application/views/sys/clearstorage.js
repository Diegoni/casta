(function() {

	Ext.Msg.show({
		title : _s('clear_storage'),
		icon : Ext.Msg.QUESTION,
		buttons : Ext.MessageBox.YESNO,
		msg : _s('clear_storage_question'),
		fn : function(btn, text) {
			if(btn == 'yes') {
				Ext.app.askexit = false;
				var res = Ext.app.clearStorage();
				Ext.app.msgFly(_s('clear_storage'), sprintf(_s('clear_storage_ok'), res));
			}
		}
	});

	return;
})();
