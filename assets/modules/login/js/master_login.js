Ext.BLANK_IMAGE_URL = 'assets/images/s.gif';

Ext.onReady(function(){
    Ext.QuickTips.init();

	// Create a variable to hold our EXT Form Panel. 
	// Assign various config options as seen.	 
    var login = new Ext.FormPanel({ 
        labelWidth:70,
        url:'index.php?c=login', 
        frame:true, 
		style:'background-color:#ffffff;padding-top:85px; padding-left: 130px;background-image:url(\'assets/images/login.jpg\');background-repeat:no-repeat',
		monitorValid:true,
		defaultType:'textfield',
	// Specific attributes for the text fields for username / password. 
	// The "name" attribute defines the name of variables sent to the server.
        items:[/*{ 
				xtype:'combo'
				, fieldLabel:'Server'
				, hiddenName:'loginServer'		// Gunakan "hiddenName", jka Yang akan dikirim ke-SerVer brupa Nilai
												// valueField (Bukan String yang terlihat oleh User)
				, allowBlank:true
				, forceSelection: true			// Sehingga User gak bisa Maksa ngetik Text yg TIDAK ada dlm HASIL LIST
				, typeAhead: true				// User bisa melakukan Query dg mgetik minimal 2 HURUF
				, minChars: 2					// --------------------------------------------^
				, triggerAction: 'all'			// AkTifKan COMBO, jika User melakukan Click tanda Combo-nya.
				, displayField:'name_server'
				, valueField:'id_server'									
				, mode: 'remote'
				, store: new Ext.data.Store({						// Setting Store, jgn lupa untuk setting Mode "remote"
					proxy: new Ext.data.HttpProxy({
						url: 'index.php?c=login&m=get_server_list'	// get JSON from this function
					})
					, reader: new Ext.data.JsonReader({
						root : 'value_data', id : 'id_server'		// Inisialisasi index array JSON yang akan dikirim
					}, ['id_server', 'name_server'])				// Sehingga bisa dibaca oleh COmbo...
				})
				, width:200
			},{ 
				fieldLabel:'Database',  
				name:'logindatabase', 
				allowBlank:true,
				width:200
			},*/
			{ 
				fieldLabel:'User ID', 
				name:'loginUsername', 
				allowBlank:false, 
				width:200
			},{ 
				fieldLabel:'Password', 
				name:'loginPassword', 
				inputType:'password', 
				allowBlank:false, 
				width:200
			}
		],

	// All the magic happens after the user clicks the button     
        buttons:[{ 
                text:'Login',
                formBind: true,	 
                // Function that fires when user clicks the button 
                handler:function(){ 
                    login.getForm().submit({ 
                        method:'POST', 
                        waitTitle:'Connecting', 
                        waitMsg:'Sending data...',

			// URL to send your username / password variables to 
                        url:'index.php?c=login&m=ajax_validateLogin',

			// Functions that fire (success or failure) when the server responds. 
			// The one that executes is determined by the 
			// response that comes from login.asp as seen below. The server would 
			// actually respond with valid JSON, 
			// something like: response.write "{ success: true}" or 
			// response.write "{ success: false, errors: { reason: 'Login failed. Try again.' }}" 
			// depending on the logic contained within your server script.
			// If a success occurs, the user is notified with an alert messagebox, 
			// and when they click "OK", they are redirected to whatever page
			// you define as redirect. 

                        success:function(){ 
                        	//Ext.Msg.alert('Status', 'Login Successful!.'){//, function(btn, text){
				//   if (btn == 'ok'){
		                        var redirect = 'index.php?c=main'; 
		                        window.location = redirect;
                                //   }
			        //});
                        },

			// Failure function, see comment above re: success and failure. 
			// You can see here, if login fails, it throws a messagebox
			// at the user telling him / her as much.  

                        failure:function(form, action){ 
                            if(action.failureType == 'server'){ 
                                obj = Ext.util.JSON.decode(action.response.responseText); 
                                Ext.Msg.alert('Authentication server is unreachable&nbsp;:', obj.errors.reason); 
                            }else{ 
                                Ext.Msg.alert('Warning!', 'Login Failed! : ' + action.response.responseText ); 
                            } 
                            login.getForm().reset(); 
                        } 
                    }); 
                } 
            }] 
    });

	// This just creates a window to wrap the login form. 
	// The login object is passed to the items collection.       
    var win = new Ext.Window({
        layout:'fit',
        width:450,
        height:220,
		closable: false,
        resizable: false,
        plain: true,
        items:  [login]
	});
	win.show();
});