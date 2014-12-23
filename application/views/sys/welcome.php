<style>
body,h1,h2,h3 {
	font-family: arial, tahoma, verdana, helvetica;
	font-style: normal;
	font-variant: normal;
	font-weight: normal;
	font-size: 11px;
	line-height: normal;
	font-size-adjust: none;
	font-stretch: normal;
	-x-system-font: none;
}

h3 {
	font-size: 110%;
}

h4 {
	font-size: 105%;
}
</style>
<h3><?php echo $this->config->item('bp.application.name');?></h3>
<small>03/12/210</small>
 
<ul>
</dt><li>Se ordena la búsqueda de facturas
</li><li>Se arregla un fallo en el cambio de fecha de factura para que los cobros también actualice la fecha
</li></li> 
<li><dt>Compras
</dt><li>Se añade la información de reposición del libro en la ventana de pedir.
</li><li>Reposición: Se aumenta el tamaño de la información del artículo para se visualice el stock en las secciones.
</li><dt>Reposición</dt><li> Se arregla un error en la carga de proveedores en Chrome
</li></li> 
<li><dt>Catálogo
</dt><li>Artículos: Se muestra en negrita el proveedor habitual del artículo
</li><li>Artículos: Se añade la herramienta para consultar la antigüedad de un artículo
</li></ul> 

<small>30/11/2010</small>

<ul>
	<dt>Catálogo</dt>
	<li>Se añade el precio de coste y margen a la ficha del libro</li>
	<li>En la acción artículo se permite buscar un artículo</li>
	<li>Se añade una herramienta que arregla los precios de coste de los
	artículos</li>
	<li>Se arregla el problema de los descuentos a 0 en la relación de
	descuentos de proveedor</li>
	<li>Se cambia el resultado de la búsqueda de artículos para que muestre
	en una línea aparte los datos de los libros y la portada</li>
	<li>Se añade el menú contextual al resultado de las búsquedas de
	artículos</li>
</ul>
<ul>
	<dt>Clientes</dt>
	<li>Se añade la búsqueda por email</li>
</ul>
<ul>
	<dt>Concursos</dt>
	<li>Se añaden los albaranes de devolución de los clientes en las
	estadísticas del consorci</li>
</ul>
<ul>
	<dt>Ventas</dt>
	<li>Se añade el importe de la venta a la factura para poder buscada</li>
</ul>
<ul>
	<dt>Portal</dt>
	<li>Se añade un panel de seguimiento de la cuenta de Twitter de la
	librería</li>
	<li>Se añade un panel de los últimos post de la Intranet de la librería
	</li>
	<li>En el panel de las últimas ventas se muestra el importe de las
	facturas</li>
</ul>
