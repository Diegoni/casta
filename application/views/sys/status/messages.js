function (res) {
	if (res.messages != null && (res.messages.length > 0))
		chat_controller();
	return;
}	
