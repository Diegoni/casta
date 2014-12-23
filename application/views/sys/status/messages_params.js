function (data)
{
	if (Ext.app.showingmessage == null) return;  
	//console.log(Ext.app.showingmessage);
	return 'message=' + !Ext.app.showingmessage + ';m_id=' + Ext.app.lastMessageID;
}
