<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/
/*
$route['default_controller'] = "application";
$route['scaffolding_trigger'] = "";

$route['login'] = 'application/checkLogin';
*/

$route['default_controller'] = 'sys/app';
$route['scaffolding_trigger'] = 'scaffoldingtakeout';

// Define your own routes below -------------------------------------------

// Limpieza de las cosas de Firebug 
$route['(.*?)\?_dc=.*?'] = '$1';

$route['help/(:any)'] = 'sys/app/help/$1';

// Comandos directos
#Sistema
$route['cmd/help/(:any)'] = 'sys/app/help/$1';
$route['cmd/help'] = 'sys/app/help_cmd';
$route['cmd/ver'] = 'sys/app/version';
$route['cmd/authreload'] = 'user/auth/auth_reload';
$route['cmd/auth'] = 'user/auth/show';
$route['cmd/status'] = 'sys/app/status';
$route['cmd/phpinfo'] = 'sys/app/phpinfo';
$route['cmd/msg/(:any)'] = 'sys/mensaje/send/$1';
$route['cmd/msg'] = 'sys/mensaje/send';
$route['cmd/mensajes'] = 'sys/mensaje/index';
$route['cmd/tareas'] = 'sys/tarea/index';
$route['cmd/comandos'] = 'sys/comando/index';
$route['cmd/runcmd/(:any)'] = 'sys/comando/runcmd/$1';
$route['cmd/runtask/(:any)'] = 'sys/tarea/runtask/$1';
$route['cmd/log'] = 'sys/logview/index';
$route['cmd/apc'] = 'sys/app/apc';
$route['cmd/cron'] = 'sys/scheduler/info';
$route['cmd/explorer'] = 'sys/app/explorer';
$route['cmd/dilve/(:any)'] = 'sys/test/dilve/$1';
$route['cmd/internet/(:any)'] = 'sys/test/internet/$1';
$route['cmd/sinli'] = 'compras/albaranentrada/sinli';
$route['app/app/login'] = 'sys/app/login';
$route['cmd/database'] = 'sys/app/database';
$route['cmd/bp2lc'] = 'sys/bp2lc/index';

#configuración
$route['cmd/config/(:any)'] = 'sys/configuracion/configure/$1';
$route['cmd/config'] = 'sys/configuracion/configure';
$route['cmd/set/(:any)'] = 'sys/configuracion/set/$1';

#Comunicaciones
$route['cmd/sms/(:any)'] = 'comunicaciones/sms/send/$1';
$route['cmd/sms'] = 'comunicaciones/sms/index';
$route['cmd/ml/(:any)'] = 'mailing/mailing/index/$1';
$route['cmd/ml'] = 'mailing/mailing/index';
$route['cmd/bl/(:any)'] = 'mailing/boletin/index/$1';
$route['cmd/bl'] = 'mailing/boletin/index';
$route['cmd/ct/(:any)'] = 'mailing/contacto/index/$1';
$route['cmd/ct'] = 'mailing/contacto/index';
$route['cmd/etq'] = 'etiquetas/etiqueta/index';

$route['cmd/email'] = 'comunicaciones/email/index';
$route['cmd/email/(:any)'] = 'comunicaciones/email/index/$1';

#Compras
$route['cmd/repo'] = 'compras/reposicion/index';
$route['cmd/pedprv/(:any)'] = 'compras/pedidoproveedor/index/$1';
$route['cmd/pedprv'] = 'compras/pedidoproveedor/index';
$route['cmd/pp/(:any)'] = 'compras/pedidoproveedor/index/$1';
$route['cmd/pp'] = 'compras/pedidoproveedor/index';
$route['cmd/pv/(:any)'] = 'proveedores/proveedor/index/$1';
$route['cmd/pv'] = 'proveedores/proveedor/index';
$route['cmd/dev/(:any)'] = 'compras/devolucion/index/$1';
$route['cmd/dev'] = 'compras/devolucion/index';
$route['cmd/dv/(:any)'] = 'compras/devolucion/index/$1';
$route['cmd/dv'] = 'compras/devolucion/index';
$route['cmd/recibir'] = 'compras/pedidoproveedor/pendienterecibir';
$route['cmd/rc'] = 'compras/reclamacion/index';
$route['cmd/rc/(:any)'] = 'compras/reclamacion/index/$1';
$route['cmd/cc'] = 'compras/cancelacion/index';
$route['cmd/cc/(:any)'] = 'compras/cancelacion/index/$1';
$route['cmd/cerrar'] = 'compras/pedidoproveedor/pendientecerrar';
$route['cmd/ae/(:any)'] = 'compras/albaranentrada/index/$1';
$route['cmd/ae'] = 'compras/albaranentrada/index';

#Ventas
$route['cmd/tarifas'] = 'ventas/cambiodivisa/tarifas';
$route['cmd/divisa'] = 'ventas/cambiodivisa/cambio';
$route['cmd/envios'] = 'ventas/tarifasenvio/index';
$route['cmd/tpv/(:any)'] = 'ventas/tpv/index/$1';
$route['cmd/tpv'] = 'ventas/tpv/index';
$route['cmd/facturacion/(:any)'] = 'ventas/factura/index/$1';
$route['cmd/facturacion'] = 'ventas/factura/index';
$route['cmd/ft/(:any)'] = 'ventas/factura/index/$1';
$route['cmd/ft'] = 'ventas/factura/index';
$route['cmd/openbox'] = 'ventas/factura/openbox';
$route['cmd/ob'] = 'ventas/factura/openbox';
$route['cmd/abono/(:any)'] = 'ventas/abono/index/$1';
$route['cmd/ab/(:any)'] = 'ventas/abono/index/$1';
$route['cmd/cliente/(:any)'] = 'clientes/cliente/index/$1';
$route['cmd/cliente'] = 'clientes/cliente/index';
$route['cmd/cl/(:any)'] = 'clientes/cliente/index/$1';
$route['cmd/cl'] = 'clientes/cliente/index';
$route['cmd/albaransalida/(:any)'] = 'ventas/albaransalida/index/$1';
$route['cmd/albaransalida'] = 'ventas/albaransalida/index';
$route['cmd/as/(:any)'] = 'ventas/albaransalida/index/$1';
$route['cmd/as'] = 'ventas/albaransalida/index';
$route['cmd/alb/(:any)'] = 'ventas/albaransalida/index/$1';
$route['cmd/alb'] = 'ventas/albaransalida/index';
$route['cmd/pc/(:any)'] = 'ventas/pedidocliente/index/$1';
$route['cmd/pc'] = 'ventas/pedidocliente/index';
$route['cmd/ped/(:any)'] = 'ventas/pedidocliente/index/$1';
$route['cmd/ped'] = 'ventas/pedidocliente/index';
$route['cmd/ae/(:any)'] = 'compras/albaranentrada/index/$1';
$route['cmd/ae'] = 'compras/albaranentrada/index';
$route['cmd/web'] = 'web/webpage/pedidos_web';

#Catalogo
$route['cmd/docs/(:any)'] = 'catalogo/articulo/documentos/$1';
$route['cmd/docs'] = 'catalogo/articulo/documentos';
$route['cmd/isbn/(:any)'] = 'catalogo/articulo/isbnean/$1';

$route['cmd/art/(:any)'] = 'catalogo/articulo/index/$1';
$route['cmd/art'] = 'catalogo/articulo/index';
$route['cmd/buscar'] = 'catalogo/articulo/buscar';
$route['cmd/buscar/(:any)'] = 'catalogo/articulo/buscar/$1';
$route['cmd/query'] = 'catalogo/articulo/query';
$route['cmd/ubicar'] = 'catalogo/articulo/ubicar';
$route['cmd/ed/(:any)'] = 'catalogo/editorial/index/$1';
$route['cmd/ed'] = 'catalogo/editorial/index';

#Calendario
$route['cmd/tr/dia/(:any)'] = 'calendario/calendario/personal_dia/$1';
$route['cmd/cal/rsm/(:any)'] = 'calendario/calendario/estado_horas/$1';
$route['cmd/tr/rsm/(:any)'] = 'calendario/trabajador/resumen/$1';
$route['cmd/tr/(:any)'] = 'calendario/trabajador/index/$1';
$route['cmd/tr'] = 'calendario/trabajador/index';
$route['cmd/calendario'] = 'calendario/trabajador/consultar';

#Suscripciones
$route['cmd/sus/(:any)'] = 'suscripciones/suscripcion/index/$1';
$route['cmd/sus'] = 'suscripciones/suscripcion/index';
$route['cmd/avisos'] = 'suscripciones/avisorenovacion/index';

#Stocks
$route['cmd/reg/(:any)'] = 'stocks/arreglostock/index/$1';
$route['cmd/reg'] = 'stocks/arreglostock/index';

#Esto ha de estar al final
$route['cmd/(:any)'] = 'catalogo/articulo/buscar/$1';

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */