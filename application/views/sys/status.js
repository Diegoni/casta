/**
 * Status de la aplicación
 */
var Ext = Ext || {};

Ext.status = {    
	/**
	 * Funciones de estado
	 */
    status_functions: new Array(),
	/**
	 * Parámetros para las llamads
	 */
    params_functions: new Array(),
    
	/**
	 * Inicializa el proceso
	 */
    init: function(){
		// Crea las funciones			
		var a = null;
		<?php foreach($js as $key => $code):?>
		Ext.status.status_functions[Ext.status.status_functions.length] = <?php echo $code;?>;
		<?php endforeach; ?>
		
		<?php foreach($params as $key => $code):?>
		Ext.status.params_functions[Ext.status.params_functions.length] = <?php echo $code;?>;
		<?php endforeach; ?>
		
		Ext.status.call();
    },
    
	/**
	 * Ejecuta los plugins
	 * @param {Object} data
	 */
    runStatus: function(data){
		for(var i=0;i<Ext.status.status_functions.length; i++)
		{
			Ext.status.status_functions[i](data);
		};
    },
    
	/**
	 * Obtiene los parámetros de las llamadas
	 */
    getParams: function(){
		var params = '';
		for(var i=0;i<Ext.status.params_functions.length; i++)
		{
			var p2 = Ext.status.params_functions[i]();
			params += ((params!='')?'&':'') + p2;
		};
		//console.log(params);
        return (params!='')?params : null;
    },
	
    /**
     * Recibe ordenes desde el servidor
     */
    run: function(){
        try {
            // Solo llama si no hay llamadas pendientes
            //if (Ext.app.request == 0) {
				Ext.status.call();
            //}
        } 
        catch (e) {
            console.dir(e);
        }
    },
	
	/**
	 * Llamada remota
	 */
    call: function(){
        try {
			var params = Ext.status.getParams();
            Ext.app.callRemote({
                url: site_url('sys/app/get_status'),
				nomsg: true,
				timeout: 3000,
				params: {
					'config': params
				},						
                fnok: function(data){
                    Ext.status.runStatus(data.message)
                }
            });
			//Lanza el timer
			setTimeout(Ext.status.run, Ext.app.STAYALIVETIME);
        } 
        catch (e) {
            console.dir(e);
        }
    }
};