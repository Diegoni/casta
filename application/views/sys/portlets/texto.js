function Portlet_texto(){
    return {
        id: null,
        url: null,
        html: function(params){
            return null;
        },
        
        init: function(params){
            this.url = site_url(params[1]);
			this.id = Ext.app.createId();
            var panel = {
                xtype: 'iframepanel',
				id: this.id,
                height: params[0],
                defaultSrc: this.url
            }
            return panel;
        },
        
        load: function(){
        
        },
        
        tools: function(tools){
            var tools1 = [];
            var t = this;
            tools1.push({
                id: 'refresh',
                handler: function(){
					var panel =Ext.getCmp(t.id); 
                    panel.setSrc(t.url);
                }
            });
            return tools1.concat(tools);
        }
    }
}
