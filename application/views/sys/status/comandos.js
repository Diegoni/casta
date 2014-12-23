function (res) {
    if (res.commands != null) {
		var commands = res.commands;
		
		if (commands.length <= 0) 
			return;
			
			//console.dir(commands);
			Ext.each(commands, function (cmd){
				try {
					var obj = Ext.util.JSON.decode(cmd.tComando);
	                 //console.dir(obj);
	                 Ext.app.analizeJSONResponse(obj);
				} 
				catch (e) {
					Ext.app.analizeJSONResponse(cmd.tComando);
					//console.dir(e);
				}
			 	Ext.app.callRemote({
					url: site_url('sys/comando/ejecutado'),
					params: {
						id: parseInt(cmd.id)
					}
				});				
			});		
	}
}	
