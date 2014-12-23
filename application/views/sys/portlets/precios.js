var Portlet_precios_this = null;

function Portlet_precios_timer(){
    //console.log('En timer..' + Ext.app.FACTURAREFRESH);
    if (Portlet_precios_this != null) 
        Portlet_precios_this.load();
}

function Portlet_precios(){
    return {
        id: null,
        txtMsg: null,
        txtISBN: null,

        html: function(params){
            return null;
        },
        
        init: function(params){
            this.id = Ext.app.createId();
            this.txtISBN = new Ext.form.TextField({
                region:'north',
                enableKeyEvents : true
            }); 
            var t = this;
            this.txtISBN.on('specialkey',
                function(o, e){
                    if (e.getKey() == e.ENTER){
                        Ext.app.callRemote({
                            url: site_url('catalogo/articulo/precio'),
                            params: {
                                code: t.txtISBN.getValue()
                            },
                            fnok: function (res) {
                                var detailEl = Ext.getCmp(t.id + '_html').body;
                                
                                detailEl.applyStyles({
                                    'background-color': '#FFFFFF'
                                });
                                var precio2 = 0;
                                res.fPVP = res.fPVP.replace(',', '.');
                                var precio = parseFloat(res.fPVP); 
                                var descuento = (Ext.app.get_config('ventas.tpv.aplicardescuento')=='true')?parseInt(Ext.app.get_config('ventas.tpv.descuento')):0;
                                if (descuento > 0) {
                                    precio = parseFloat(res.fPVP) * (1 - descuento/100.0);                        
                                    precio2 = parseFloat(res.fPVP); 
                                }

                                var text = '<div style="font-size: 200%;color:green;align:center;">' + res.cTitulo + '</div>'
                                + '<div style="font-size: 150%;color:blue;">' + ((res.cAutores!=null)?res.cAutores:'') + '</div>'
                                + '<div style="font-size: 500%;color:red;float:center;display:block;">' + Ext.app.currencyFormatter(precio)
                                + ((descuento>0)?('<span style="font-size: 50%;color:black;">(' + descuento + '%) ' + Ext.app.currencyFormatter(precio2) + '</span>'):'')
                                + '</div>';
                                detailEl.update(text);
                                t.txtISBN.setValue(null);
                            }                            
                        });
                    }
                },
                this
            );

            var panel = {
                xtype: 'panel',
                layout : 'border',
                id: this.id,
                height: 200,
                items: [this.txtISBN, {
                    xtype: 'iframepanel',
                    id: this.id + '_html',
                    region: 'center'
                }]
            }
            return panel;
        },
        
        load: function(){
        },
        tools: function(tools){
        }
    }
}
