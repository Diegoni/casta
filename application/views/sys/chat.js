(function(){
    var form_id = "chat_global";
    var title = _s('Mensajes');
    var icon = 'iconoMensajesTab';

	var old_tab = Ext.app.tab.findById(form_id);
    if (old_tab != null) {
    	Ext.app.tab.setActiveTab(form_id);
    	return null;
    }

	var chatURL = site_url("sys/mensaje/chat");
	var cache = new Array();
	//var request = new Array();
	//var requestg = new Array();
	var lastMessageID = -1;
	var lastPaintId = -1;
	var firstPaintId = -1;
	var updateInterval = 1000; 
	var params;
	var tid =0; 
	var MsgSign = false; 
	var first_time = true;

	var username = '<?php echo $this->userauth->get_username(); ?>';
	var name = '<?php echo $this->userauth->get_name(); ?>';

	var sendMessage = function(to, msg, group) {
		var txt = (msg!=null && msg!='')?msg:txtMsg.getValue();
		if ( txt =='')
			return false; 
		cache.push({
			msg : txt,
			to: to,
			group: (group == true)
		});
		txtMsg.setValue(''); 
	}

	var requestNewMessages = function(){
		if (cache.length > 0) {
			params =  cache.shift();
		} else
			params = {};

		//if (params.only !== true)
		Ext.apply(params, { last_id: lastMessageID });

		try {
			Ext.Ajax.request({
				url : chatURL,
				params : params,
				success : function(response) {
					var res = Ext.decode(response.responseText);
					if (res.success) {
						readMessages(res.messages, params.only, params.first_id != null); 
						readUser(res.users);
						readGrupo(res.grupos);
						lastMessageID = res.last_id;
					}
				}
			});			
		} catch (e) {
			console.dir(e);
		}
	}

	var readUser = function(user) {
		var tpl = "<div class='box-user'>" +
				"<a href=# onclick=\"Ext.app.startPrivate('{1}',1, '{2}')\" title='{2}'>{0}</a></div>";
		var html = ""; 
		Ext.each(user,function(r,i){
			html += String.format(tpl,r.name,r.user, r.user.replace("'", "\\'")); 
		}); 
		if (Ext.get('userList'))
			Ext.getCmp('userList').body.update(html); 
	}

	var readGrupo = function(user) {
		var tpl = "<div class='box-group'>" +
				"<a href=# onclick=\"Ext.app.startPrivate({1}, 2, '{2}')\" title='{2}'>{2}</a></div>";
		var html = ""; 
		Ext.each(user,function(r,i){
			html += String.format(tpl, r.name, r.id, r.name.replace("'", "\\'")); 
		}); 
		if (Ext.get('groupList'))
			Ext.getCmp('groupList').body.update(html); 
	}

	Ext.app.startPrivate = function(nameUser, mode, name) {
		if (nameUser == username)
			return false;
		var id = 'chat-'+ nameUser; 
		var tabs = Ext.getCmp('chat');
		var tab = tabs.getComponent(id);
		if(tab) {
				tabs.setActiveTab(tab);
		} else {
			tab = tabs.add(
				new privChat({
					id : id,
					title: name,
					tUser: name,
					mode: mode,
					iconCls: (mode==1)?'icon-private-chat':'icon-private-group',
					username: username,
					sendMessageGlobal: function (to, msg) {
							sendMessage(nameUser, msg, (mode==2)?true:false);
						},
					callRefresh : function (id, firstId) {
							if (firstId!=null) {
								(mode==1)?cache.push({idu: nameUser, only: true, first_id: firstId}):
								cache.push({idg: nameUser, only: true, first_id: firstId});
							}
							else {
								(mode==1)?cache.push({idu: nameUser, only: true}):
								cache.push({idg: nameUser, only: true});
							}
						}
					})
				);
			tabs.setActiveTab(tab);
			
			(mode==2)?cache.push({
				idg: nameUser
			}):cache.push({
				idu: nameUser
			});
		}
		return tab;
	}

	var readMessages = function(msg, only, notabs) {
		var nuevos = false;
		Ext.each(msg,function(r,i) {
			// Usuarios
			var id1 = 'chat-'+ r.destino; 
			var id2 = 'chat-'+ r.origen; 
			var tabs = Ext.getCmp('chat');
			if (r.grupo == null) {
				var tab = tabs.getComponent(id1);
				if (!tab && !first_time && r.destino!=null && notabs != true) {
					tab = Ext.app.startPrivate(r.destino, 1, r.destinonombre);
				}
				if(tab) {
					tab.readMessages([r]);				
				}
				if (id2!=id1)
				{
					var tab = tabs.getComponent(id2);
					if (!tab && !first_time && r.origen!=null && notabs != true) {
						tab = Ext.app.startPrivate(r.origen, 1, r.origennombre);
					}
					if(tab) {
						tab.readMessages([r]);				
					}
				}
			}
			// Grupos
			else {
				var id = 'chat-'+ r.grupo; 
				var tab = tabs.getComponent(id);
				if (!tab && !first_time && notabs != true) {
					tab = Ext.app.startPrivate(r.grupo, 2, r.gruponombre);
				} 
				if(tab) {
					tab.readMessages([r]);				
				}
			}
			if (only !== true) {
				if ((r.id < firstPaintId || firstPaintId == -1) || (r.id > lastPaintId || lastPaintId == -1)) {
					chat_write(r, username, chatPanel, r.id < firstPaintId);
					if (r.id < firstPaintId || firstPaintId == -1) firstPaintId = r.id;
					if (r.id > lastPaintId || lastPaintId == -1) lastPaintId = r.id;
				}
				if (r.new) nuevos = true;
			}
		}); 
		first_time = false;
		if (nuevos) panel.setTitle(String.format("<span class='blink'>{0}</span>", title));
		
		if (msg.length>0) {
			lastMessageID = msg[msg.length-1].id; 
			Ext.app.lastMessageID = lastMessageID;
		}
		repeatMsg(); 
		
	}

	var repeatMsg = function () {
		clearTimeout(tid);
		tid =0;	
		tid = requestNewMessages.defer(updateInterval,Ext.getCmp(form_id)); 
	}

	var userList = {
		region:'east',
		width: 240,
		margins:'0 0 0 2',
		autoScroll: true,
		layout:'border',
		bodyStyle:'padding:5px;',
		items:[{
			id:'userList',	
			region:'center',
			autoScroll: true,
			bodyStyle:'padding:5px;'
		},{
			id:'groupList',	
			region:'south',
			height: 100,
			autoScroll: true,
			tbar: [{
						iconCls : 'icon-group-add',
						handler : function() {
				            Ext.Msg.prompt(_s('nuevo-grupo'), _s('Nombre'), function(ok, v){
				                if (ok != 'ok') 
				                    return;
				                Ext.app.callRemote({
				                    params: {
				                        cDescripcion: v
				                    },
				                    url: site_url('sys/mensajegrupo/add'),
				                    fnok : function (res) {
				                    	Ext.app.startPrivate(res.id, 2, v);
				                    }

				                })
				            });
						}
					}],
			bodyStyle:'padding:5px;'
		}]
	};

	var chatPanel = new Ext.form.DisplayField({
		id:'chatPanel',
		border:true,	
		region:'center',
		cls: 'x-form-text',
		autoScroll:true
	});  

	var NorthPanel = {
		border:false,
		margins:'2 2 2 2',	
		region:'center',
		layout:'border',
		items:[chatPanel, userList],
		tbar: [{
				iconCls : 'icon-chat-prev',
				handler : function() {
					cache.push({ first_id: firstPaintId });
				}
			}, '-', {
				iconCls : 'icon-refresh',
				handler : function() {
					chat_clear(chatPanel);
					lastMessageID = -1;
					lastPaintId = -1;
					firstPaintId = -1;
					first_time = true;
				}
			}, '-', {
				iconCls : 'icon-clean',
				handler : function() {
					chat_clear(chatPanel);
				}
			}]
	};

	var txtMsg = new Ext.form.TextField({
		name:'txtMsg',
		region:'center',
		enableKeyEvents : true,
		listeners: {
			specialkey: function(o, e){
				if (e.getKey() == e.ENTER)
					sendMessage(); 
			}
		}
	}); 

	var btnSend = new Ext.Button({
		text:_s('Enviar'),
		iconCls: 'icon-send-chat',
		flex: 1,
		handler:function(){
			sendMessage(); 
			txtMsg.focus(); 
		}
	}); 

	var btnPanel = {
		border:false,
		region:'east',
		margins:'0 0 0 2',
		width:75,
		layout:'vbox',
	    layoutConfig: {
	    	padding:'0',
	    	align:'stretch'
		},
		items:[btnSend]
	}

	var SouthPanel = {
		region:'south',
		height:30,
		layout:'border',
		border:false,
		margins:'2 2 2 2',
		items:[txtMsg, btnPanel]
	};

	var chat = {
		//id: form_id,
        region: 'center',
		border:false,
		title: _s('Global Chat'),
		iconCls: 'icon-chat-main',
        baseCls: 'x-plain',
        frame: true,
        bodyStyle: 'padding: 5px 5px 0px;',
		layout:'border',
		items:[NorthPanel, SouthPanel],
		listeners:{
			activate:function(){
				MsgSign = true; 
				this.set_focus();
			},
			deactivate:function(){
				MsgSign = true; 
			},
			afterrender: function() {
				Ext.app.showingmessage = true;
				repeatMsg();
			},
			destroy: function()
			{
				Ext.app.showingmessage = false;
				clearTimeout(tid);
			}
		},
		set_focus : function() {
			txtMsg.focus();
			chatPanel.el.scroll('b', 100000, true);
			MsgSign = false;
		}
	};

    var tabpanel = new Ext.TabPanel({
        xtype: 'tabpanel',
        region: 'center',
        activeTab: 0,
        baseCls: 'x-plain',
		id: 'chat',
		//enableTabScroll: true,
		plugins: new Ext.ux.TabCloseMenu(),
        items: [chat]
    });
    
    var panel = new Ext.Panel({
        layout: 'border',
        title: title,
        id: form_id,
        iconCls: icon,
        region: 'center',
        closable: true,
        items: [tabpanel],
		listeners:{
			activate:function(){
				var tab = tabpanel.getActiveTab();
				tab.set_focus();
				panel.setTitle(_s('Global Chat'));
			}
		}
    });
    
    return panel;
})();
