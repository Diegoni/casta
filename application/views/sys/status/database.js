function(data)
{
	//console.log ('En database');
	if (data.database != null) {	
        try {
            Ext.getCmp('m_username').setText(data.database.username);
            Ext.getCmp('m_hostname').setText(data.database.hostname);
            Ext.getCmp('m_database').setText(data.database.database);
			if (!data.database.logged && !Ext.app.logged_window){
				Ext.app.logged_window = true;
				Ext.app.login(false, function () {
					Ext.app.auth_reload(true);
					Ext.app.logged_window = false;
				}, _s('conexion-caducada'));	
			}			
        } 
        catch (e) {        
       }
	}
}