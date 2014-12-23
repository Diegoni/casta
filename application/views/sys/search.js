<?php $obj = get_instance(); ?>
var search_albaranentrada = function(grid_id, fn_open)
{ 
	<?php 
	$obj->load->model('compras/m_albaranentrada');
	$modelo = $obj->m_albaranentrada->get_data_model(array('nIdDireccion', 'tNotasInternas', 'tNotasExternas', 'fCambioDivisa',
	'bValorado', 'bPrecioLibre', 'fImporteCamara', 'nIdPais', 'nIdDocumentoCamara', 'nPeso', 'nIdTipoMercancia',
	'bAplicarGastosDefecto', 'dAct', 'cAUser', 'fCambioCamara', 'fPrecioCambio'
	));
	
	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 'compras.albaranentrada', $obj->m_albaranentrada->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_pedidoproveedor = function(grid_id, fn_open)
{ 
	 <?php 
	 $obj->load->model('compras/m_pedidoproveedor');
	 $modelo = $obj->m_pedidoproveedor->get_data_model(array('nIdDivisa', 'nIdDireccion', 'fValorDivisa'));
	 
	 echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 'compras.pedidoproveedor', $obj->m_pedidoproveedor->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	 ?>;
}

var search_documentocamara = function(grid_id, fn_open)
{
	<?php
	$obj->load->model('compras/m_documentocamara');
	$modelo = $obj->m_documentocamara->get_data_model();

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 'compras.documentocamara', $obj->m_documentocamara->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_cursointernet = function(grid_id, fn_open)
{
	<?php 
	$obj->load->model('eoi/m_curso');
	$modelo = $obj->m_curso->get_data_model();
	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 'eoi.curso', $obj->m_curso->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_listanovedad = function(grid_id, fn_open)
{
	<?php
	$obj->load->model('concursos/m_listanovedad');
	$modelo = $obj->m_listanovedad->get_data_model();

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 'concursos.listanovedad', $obj->m_listanovedad->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_reclamacionsuscripcion = function(grid_id, fn_open)
{
	
	<?php 
	$obj->load->model('suscripciones/m_reclamacion');
	$modelo = $obj->m_reclamacion->get_data_model(array('nIdDireccionCliente', 'nIdDireccionProveedor', 'tDescripcion'));

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 'suscripciones.reclamacion', $obj->m_reclamacion->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_tiporeclamacionsuscripcion = function(grid_id, fn_open)
{
	
	<?php 
	$obj->load->model('suscripciones/m_tiporeclamacion');
	$modelo = $obj->m_tiporeclamacion->get_data_model(array('tTexto'));

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 
		'suscripciones.tiporeclamacion', $obj->m_tiporeclamacion->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_suscripciones = function(grid_id, fn_open)
{
	<?php 
	$obj->load->model('suscripciones/m_suscripcion');
	$modelo = $obj->m_suscripcion->get_data_model(array('nEntradas', 'nFacturas', 'nIdUltimaFactura', 'nIdUltimoAlbaran', 'fPrecioCompra', 'nIdPedidoProveedor', 'nIdLineaPedidoProveedor', 'dPrimerInicio' ,'nIdRevista', 'nIdCliente'));

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 
		'suscripciones.suscripcion', $obj->m_suscripcion->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open',
		'sys/submenusus.js', array('cProveedor', 'cCliente', 'cRevista', 'nIdRevista', 'nIdCliente'));
	?>;
}

var search_etiquetasformatos = function(grid_id, fn_open)
{
	
	<?php 
	$obj->load->model('etiquetas/m_etiquetaformato');
	$modelo = $obj->m_etiquetaformato->get_data_model(array('tFormato'));

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 
		'etiquetas.etiquetaformato', $obj->m_etiquetaformato->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_pedidocliente = function(grid_id, fn_open)
{	
	<?php 
	$obj->load->model('ventas/m_pedidocliente');
	$modelo = $obj->m_pedidocliente->get_data_model(array('bCatalogar', 'bLock', 'bExentoIVA', 'nIdDirEnv', 'nIdDirFac', 'nIdFactura', 'nIdAlbaranDescuentaAnticipo', 'tNotasExternas', 'tNotasInternas'));

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 
		'ventas.pedidocliente', $obj->m_pedidocliente->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}

var search_lineaspedidoconcurso = function(grid_id, fn_open)
{	
	<?php 
	$obj->load->model('concursos/m_pedidoconcursolinea');
	$modelo = $obj->m_pedidoconcursolinea->get_data_model(array(
		'nIdLineaDevolucion', 'nIdLineaPedidoProveedor', 'nIdLineaAlbaranEntrada', 'nIdLineaPedidoCliente', 
		'nIdLineaAlbaranSalida', 'nIdLineaDevolucion', 
		'dCreacion', 'dAct', 'cCUser', 'cAUser', 'nIdLibro') );

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 
		'concursos.pedidoconcursolinea', $obj->m_pedidoconcursolinea->get_id(), 
		null, FALSE, null, 'mode:"search", fn_open: fn_open',
		null, FALSE, array('nIdPedidoProveedor', 'nIdLineaPedidoProveedor', 
			'nIdAlbaranEntrada', 'nIdLineaAlbaranEntrada', 'nIdAlbaranSalida',
			'nIdConcurso', 'nIdLibro', 'cTitulo2', 'cCUser2', 'dCreacion2', 'cISBN2', 'nEAN', 'cEditorial', 'cAutores2'));
	?>;
}

var search_liquidaciondepositos = function(grid_id, fn_open)
{
	<?php
	$obj->load->model('compras/m_liquidaciondepositos');
	$modelo = $obj->m_liquidaciondepositos->get_data_model(array('nIdDireccion'));

	echo 'return ' . extjs_creategrid($modelo, '" + grid_id + "_g_search', null, null, 'compras.liquidaciondepositos', $obj->m_liquidaciondepositos->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');
	?>;
}
