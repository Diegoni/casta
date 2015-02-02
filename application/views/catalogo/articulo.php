<?php $this->load->helper('asset');?>
<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery.thickbox.min.js');?>
<?php echo js_asset('jQuery/jquery.html5uploader.min.js');?>
<?php echo css_asset('thickbox.css');?>
<script language="javascript">
var image = '<?php echo image_asset('s.gif', '', array('border' => 0, 'width' => '16px', 'height' => '16px'));?>';

function enlace_js(texto, js, style){
    if (style == null) 
        style = 'cmd-link';
    return "<span class='" + style + "'><a href=\"javascript:" + js + ";\">" + texto + "</a></span>";
}

var enlace_cmd = function (texto, url, style){
	url = parent.site_url(url);
	var js = "parent.Ext.app.execCmd({url: '" + url + "'});";
	return enlace_js(texto, js, style);
}

function format_js_del(id, url, fn){
    url += '/del/' + id;
    url = parent.site_url(url);
    var js = "parent.Ext.app.callRemoteAsk({" +
    "url: '" +
    url +
    "'," +
    "askmessage: parent._s('elm-registro')," +
    "fnok: function(){" +
    fn +
    "(" +
    id +
    ");" +
    "}});";
    return enlace_js(null, js, 'icon-delete');
}

function articulo_autor_add(res){
    parent.Ext.each(res.value_data, function(item){
        if (jQuery('#aut_' + item.id).length == 0) {
            var del = format_js_del(item.id, 'catalogo/articuloautor', 'articulo_autor_del');
            var html = '<tr id="aut_' + item.id + '">' +
            '<td>' +
            del +
            '</td>' +
            '<td class="label">'+
            enlace_cmd(item.cAutor,parent.site_url('catalogo/autor/index/'+item.nIdAutor)) +            
            '</td>' +
            '<td class="text">' +
            item.cTipoAutor +
            '</td>' +
            '</tr>';
            $("#autores > tbody").append(html);
        }
    });
}

function articulo_materia_add(res){
    parent.Ext.each(res.value_data, function(item){
        if (jQuery('#mat_' + item.id).length == 0) {
            var del = format_js_del(item.id, 'catalogo/articulomateria', 'articulo_materia_del');
            var html = '<tr style="display:none" id="mat_' + item.id + '">' +
            '<td>' +
            del +
            '</td>' +
            '<td class="label">' +
            item.id +
            '</td>' +
            '<td class="text">' +
            item.cCodMateria+
            '</td>' +
            '<td class="text">' +
            item.cNombre+
            '</td>' +
            '</tr>';
            $("#materias > tbody").append(html);
            jQuery('#mat_' + item.id).fadeIn('slow');
        }
    });
}

function articulo_ubicacion_add(res){
    parent.Ext.each(res.value_data, function(item){
        if (jQuery('#ub_' + item.id).length == 0) {
            var del = format_js_del(item.id, 'catalogo/articuloubicacion', 'articulo_ubicacion_del');
            var html = '<tr id="ub_' + item.id + '">' +
            '<td>' +
            del +
            '</td>' +
            '<td class="label">' +
            item.id +
            '</td>' +
            '<td class="text">' +
            item.cDescripcion+
            '</td>' +
            '<td class="text">' +
            item.dCreacion+
            '</td>' +
            '</tr>';
            $("#ubicaciones > tbody").append(html);
        }
    });
}

function articulo_promocion_add(res){
    parent.Ext.each(res.value_data, function(item){
        if (jQuery('#pro_' + item.id).length == 0) {
            var del = format_js_del(item.id, 'catalogo/promocion', 'articulo_promocion_del');
            var html = '<tr style="display:none" id="pro_' + item.id + '">' +
            '<td>' +
            del +
            '</td>' +
            '<td class="text">' +
            item.cTipoPromocion+
            '</td>' +
            '<td class="text">' +
            item.dInicio+
            '</td>' +
            '<td class="text">' +
            item.dFinal+
            '</td>' +
            '</tr>';
            $("#promociones > tbody").append(html);
            jQuery('#pro_' + item.id).fadeIn('slow');
        }
    });
}

function articulo_proveedor_add(res){
    parent.Ext.each(res.value_data, function(item){
        if (jQuery('#pv_' + item.id).length == 0) {            
            var del = format_js_del(item.id, 'catalogo/proveedorarticulo', 'articulo_proveedor_del');
            var cmd = enlace_cmd(item.id, 'proveedores/proveedor/index/' + item.id);
            var html = '<tr id="pv_' + item.id + '">' +
            '<td>' +
            del +
            '</td>' +
            '<td class="text">' +
            cmd +
            '</td>' +
            '<td class="text">' +
            item.cProveedor +
            '</td>' +
            '<td class="text">' +
            parent._s('origen_proveedor_libro')+
            '</td>' +
            '<td class="text">' +
            item.fDescuento +
            '</td>' +
            '<td class="text">' +
            item.nDiasEnvio+
            '</td>' +
            '</tr>';
            $("#proveedores > tbody").append(html);
        }
    });
}

function articulo_seccion_add(res){
    parent.Ext.each(res.value_data, function(item){
        if (jQuery('#sec_' + item.id).length == 0) {
            var del = format_js_del(item.id, 'catalogo/articuloautor', 'articulo_seccion_del');
            var cmd = enlace_cmd(parent._s('Pedir'), 'compras/reposicion/pedir_uno/' + item.nIdLibro + '/' + item.nIdSeccion + '/1');
            var cmd2 = enlace_cmd(parent._s('Reservar'), 'ventas/pedidocliente/reservar/' + item.nIdLibro + '/' + item.nIdSeccion + '/1');
            var html = '<tr id="sec_' + item.id + '">' +
            '<td>' +
            del +
            '</td>' +
            '<td class="label">' +
            item.cNombre +
            ' (' +
            item.nIdSeccion +
            ')</td>' +
            '<td class="number">' +
            item.nStockDisponible +
            '</td>' +
            '<td class="number">' +
            item.nStockFirme +
            '</td>' +
            '<td class="number">' +
            item.nStockDeposito +
            '</td>' +
            '<td class="number">' +
            item.nStockRecibir +
            '</td>' +
            '<td class="number">' +
            item.nStockAPedir +
            cmd +
            '</td>' +
            '<td class="number">' +
            item.nStockServir + 
            cmd2 +
            '</td>' +
            '<td class="number">' +
            item.nStockReservado +
            '</td>' +
            '<td class="number">' +
            item.nStockAExamen +
            '</td>' +
            '<td class="number">' +
            item.nStockADevolver +
            '</td>' +
            '<td class="number">' +
            item.nStockMinimo +
            '</td>' +
            '<td class="number">' +
            item.nStockMaximo +
            '</td>' +
            '</tr>';
            $("#secciones > tbody").append(html);
        }
    });
}

function registro_del(tipo, id){
    jQuery('#' + tipo + id).fadeOut('slow', function(){
        jQuery('#' + tipo + id).remove()
    });
}

function articulo_autor_del(id){
    registro_del('aut_', id);
}

function articulo_ubicacion_del(id){
    registro_del('ub_', id);
}

function articulo_materia_del(id){
    registro_del('mat_', id);
}

function articulo_proveedor_del(id){
    registro_del('pv_', id);
}

function articulo_seccion_del(id){
    registro_del('sec_', id);
}

function articulo_promocion_del(id){
    registro_del('pro_', id);
}

function articulo_codigo_del(id){
    registro_del('ac_', id);
}

</script>
<div class="data">
	<table width="100%">
	<tr>
		<th colspan="5" class="titulo">(<?php echo $nIdLibro;?>) <?php echo $cTitulo;?>
			<span class="tipoarticulo_<?php echo $nIdTipo;?>"><?php echo $tipo['cDescripcion'];?></span>
		</th>
	</tr>
	<?php if (count($codigos) > 0): ?>
	<tr>
		<th colspan="5">
			<?php foreach($codigos as $code): ?>			
			<span id="ac_<?php echo $code['nIdCodigo']?>" class="articulocodigo">
			<?php if ($code['bDelete']): ?><?php echo format_js_del($code['nIdCodigo'], 'catalogo/articulocodigo', 'articulo_codigo_del');?><?php endif; ?> <?php echo $code['nCodigo'];?>
			</span>
			
		<?php endforeach; ?>
		</th>
	</tr>
<?php endif; ?>
	<tr>
		<td colspan="3"><span class="autores"><?php echo $cAutores;?></span>
			<?php if (isset($promociones)):?>
				<table  id="promociones">
					<thead>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('Promoción');?></th>
				<th class="label" colspan="1"><?php echo $this->lang->line('Desde');?></th>
				<th class="label" colspan="1"><?php echo $this->lang->line('Hasta');?></th>
			</tr>
			</thead>
			<tbody>
		<?php foreach ($promociones as $promocion):?>
		<tr id="pro_<?php echo $promocion['id'];?>">
			<td width="1%"><?php
			echo format_js_del($promocion['id'], 'catalogo/promocion', 'articulo_promocion_del');?>
			</td>
			<td class="label"><?php echo $promocion['cTipoPromocion'];?></td>
			<td class="number"><?php echo format_date($promocion['dInicio']);?></td>
			<td class="number"><?php echo format_date($promocion['dFinal']);?></td>
		</tr>
		<?php endforeach;?>
		</tbody>
			<tfoot>
		<tr>
			<td colspan="4"><?php echo format_enlace_js($this->lang->line('Añadir Promoción'), "parent.addPromocion({$nIdLibro}, articulo_promocion_add)", 'icon-add');?></td>
		</tr>
	</tfoot>

		</table>
			<?php endif;?>
		</td>
		<td colspan="2"><span class="precio"><?php echo format_price((isset($fPVP)?$fPVP:0));?>
		(<?php echo format_price($fPrecio);?>) <?php if (isset($nIdOferta)):?>
		<br />
		<span class="precio-oferta"><?php echo format_price(format_add_iva($fPrecioOriginal, $fIVA));?> (<?php echo format_price($fPrecioOriginal);?>)</span>
		</span> <?php endif;?>
		<?php if (time() - $dEdicion < 0):?>
			<br/><span class="preventa"><?php echo $this->lang->line('PREVENTA');?> <?php echo format_date($dEdicion);?></span>
			<?php endif;?>
		</td>
	</tr>
	<tr valign="top">
		<td id="portada2"
			width="<?php echo $this->config->item('bp.catalogo.cover.articulo');?>"><?php echo format_lightbox(format_cover($nIdLibro, $this->config->item('bp.catalogo.cover.articulo'), 'portada'), format_url_cover($nIdLibro));?>
			<div class="upload">
			      <input id="multiple" type="file"  name="upload" />
			</div>			
			</td>
		<td>
		<table>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('Datos');?></th>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('ISBN');?>:</td>
				<td class="info"><?php echo isset($cISBN)?$cISBN:'';?>&nbsp; <?php echo isset($cISBN10)?$cISBN10:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('EAN');?>:</td>
				<td class="info"><?php echo isset($nEAN)?$nEAN:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Estado');?>:</td>
				<td class="info"><?php echo $cEstado;?></td>
			</tr>

			<tr>
				<td class="label"><?php echo $this->lang->line('Proveedor');?>:</td>
				<td class="info"><?php if (isset($cProveedor)):?> <?php echo format_enlace_cmd($cProveedor, site_url('proveedores/proveedor/index/' . $nIdProveedor));?>
				<?php endif;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Editorial');?>:</td>
				<td class="info"><?php echo format_enlace_cmd($cEditorial, site_url('catalogo/editorial/index/' . $nIdEditorial));?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Colección');?>:</td>
				<td class="info"><?php if (isset($cColeccion)):?> <?php echo format_enlace_cmd($cColeccion, site_url('catalogo/coleccion/index/' . $nIdColeccion));?>
				&nbsp; <?php echo isset($cNColeccion)? ' # ' . $cNColeccion:'';?> <?php endif;?>&nbsp;
				</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Páginas');?>:</td>
				<td class="info"><?php echo $nPag;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Coste');?>:</td>
				<td class="info"><?php echo format_price($fPrecioCompra);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Margen');?>:</td>
				<td class="info"><?php echo format_percent(format_margen($fPrecio, $fPrecioCompra));?></td>
			</tr>
			<?php if (isset($nIdOferta)):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('Oferta');?>:</td>
				<td class="info"><?php echo format_enlace_cmd($oferta['cDescripcion'], site_url('catalogo/oferta/index/' . $oferta['nIdOferta']));?></td>
			</tr>
			<?php endif;?>
			<?php if (isset($revista['nIdPeriodo']) || isset($revista['nIdTipoPeriodoRevista']) || isset($revista['nIdTipoSuscripcion'])):?>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('Datos Publicación');?></th>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('nIdPeriodo');?>:</td>
				<td class="info"><?php echo isset($revista['cPeriodoSuscripcion'])?$revista['cPeriodoSuscripcion']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('nIdTipoPeriodoRevista');?>:</td>
				<td class="info"><?php echo isset($revista['cTipoPeriodoRevista'])?$revista['cTipoPeriodoRevista']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('nIdTipoSuscripcion');?>:</td>
				<td class="info"><?php echo isset($revista['cTipoSuscripcionRevista'])?$revista['cTipoSuscripcionRevista']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('nEjemplares');?>:</td>
				<td class="info"><?php echo isset($revista['nEjemplares'])?$revista['nEjemplares']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('bRenovable');?>:</td>
				<td class="info"><?php echo $this->lang->line(($revista['bRenovable'] == 1)?'RENOVABLE':'NO RENOVABLE');?></td>
			</tr>
			<?php endif; ?>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('Ventas');?></th>
			</tr>

			<tr>
				<td class="label"><?php echo $this->lang->line('Semana');?></td>
				<td class="info"><?php echo $t_semana;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Mes');?></td>
				<td class="info"><?php echo $t_mes;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('3 Meses');?></td>
				<td class="info"><?php echo $t_mes3;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('6 Meses');?></td>
				<td class="info"><?php echo $t_mes6;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('12 Meses');?></td>
				<td class="info"><?php echo $t_mes12;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('24 Meses');?></td>
				<td class="info"><?php echo $t_mes24;?></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('Últimos documentos');?></th>
			</tr>
			<?php if (isset($ult_docs_general['entrada'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('Entrada');?></td>
				<td class="info"><?php echo isset($ult_docs_general['entrada']['dCierre'])?format_date($ult_docs_general['entrada']['dCierre']):'&nbsp;';?>
				<?php echo isset($ult_docs_general['entrada']['dFecha'])?
				('(' .$this->lang->line('F.Alb.') . format_date($ult_docs_general['entrada']['dFecha']) .')'):'&nbsp;';?><br />
				<?php echo format_enlace_cmd($ult_docs_general['entrada']['nIdAlbaran'], site_url('compras/albaranentrada/index/' . $ult_docs_general['entrada']['nIdAlbaran']));?>
				- <?php echo format_enlace_cmd(format_name($ult_docs_general['entrada']['cNombre'], $ult_docs_general['entrada']['cApellido'], $ult_docs_general['entrada']['cEmpresa']), site_url('proveedores/proveedor/index/' . $ult_docs_general['entrada']['nIdProveedor']));?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['salida'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('Salida');?></td>
				<td class="info"><?php echo isset($ult_docs_general['salida'])?format_date($ult_docs_general['salida']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['devolucion'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('Devolución');?></td>
				<td class="info"><?php echo isset($ult_docs_general['devolucion'])?format_date($ult_docs_general['devolucion']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['pendiente'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('A Recibir');?></td>
				<td class="info"><?php echo isset($ult_docs_general['pendiente'])?format_date($ult_docs_general['pendiente']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['apedir'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('A Pedir');?></td>
				<td class="info"><?php echo isset($ult_docs_general['apedir'])?format_date($ult_docs_general['apedir']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($fPrecioProveedor)):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('Últ. Precio Prv.');?></td>
				<td class="info"><?php echo format_price($fPrecioProveedor, FALSE). ' ' . $divisa['cSimbolo']. ' (' . format_date($dFechaPrecioProveedor) .')';?></td>
			</tr>
			<?php endif; ?>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<th class="label" colspan="3"><?php echo $this->lang->line('Tarifas');?></th>
			</tr>
			<?php foreach ($tarifas as $t):?>
			<tr>
				<td class="label"><?php echo $t['cDescripcion'];?>:</td>
				<td class="info"><?php echo format_price($t['fPrecio']);?></td>
				<td class="info"><?php echo format_price(format_add_iva($t['fPrecio'], $fIVA));?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		</td>
	</tr>
</table>
<table id="autores">
	<tbody>
		<tr>
			<th colspan="3" class="seccion"><?php echo $this->lang->line('Autores');?>
			</th>
		</tr>
		<tr>
			<th width="1px">&nbsp;</th>
			<th><?php echo $this->lang->line('Autor');?></th>
			<th><?php echo $this->lang->line('Tipo');?></th>
		</tr>
		<?php if (count($autores) > 0):?>
		<?php foreach($autores as $autor):?>
		<tr id="aut_<?php echo $autor['id'];?>">
			<td width="1%"><?php
			echo format_js_del($autor['id'], 'catalogo/articuloautor', 'articulo_autor_del');?>
			</td>
			<td width="90%" class="autores"><?php echo format_enlace_cmd($autor['cAutor'], site_url('catalogo/autor/index/' . $autor['nIdAutor']));?></td>
			<td width="9%"><?php echo $autor['cTipoAutor'];?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3"><?php echo format_enlace_js($this->lang->line('Añadir Autor'), "parent.addAutor({$nIdLibro}, articulo_autor_add)", 'icon-add');?></td>
		</tr>
	</tfoot>
</table>

		<?php if (count($notas)>0):?> <br />
<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('Comentarios');?>
		</th>
	</tr>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Tipo');?></th>
		<th><?php echo $this->lang->line('Comentario');?></th>
		<th><?php echo $this->lang->line('Fecha');?></th>
		<th><?php echo $this->lang->line('Usuario');?></th>
	</tr>

	<?php foreach ($notas as $nota):?>
	<tr>
		<td nowrap="nowrap" width="1%" class="cell-nota<?php echo $nota['nIdTipoObservacion'];?>">&nbsp;&nbsp;</td>
		<td nowrap="nowrap" width="1%" ><?php echo $nota['cTipoObservacion'];?></td>
		<td><strong><?php echo $nota['tObservacion'];?></strong></td>
		<td nowrap="nowrap" width="1%" ><?php echo format_date($nota['dCreacion']);?></td>
		<td nowrap="nowrap" width="1%" ><?php echo $nota['cCUser'];?></td>
	</tr>

	<?php endforeach;?>
</table>
	<?php endif;?> <br />
<table id="secciones">
	<tbody>
		<tr>
			<th colspan="12" class="seccion"><?php echo $this->lang->line('Secciones');?>
			</th>
		</tr>
		<tr>
			<th width="1px">&nbsp;</th>
			<th><?php echo $this->lang->line('Sección');?></th>
			<th><?php echo $this->lang->line('Disponible');?></th>
			<th><?php echo $this->lang->line('Firme');?></th>
			<th><?php echo $this->lang->line('Depósito');?></th>
			<th><?php echo $this->lang->line('Recibir');?></th>
			<th><?php echo $this->lang->line('A Pedir');?></th>
			<th><?php echo $this->lang->line('A Servir');?></th>
			<th><?php echo $this->lang->line('Reservado');?></th>
			<th><?php echo $this->lang->line('A Devolver');?></th>
			<th><?php echo $this->lang->line('Mínimo');?></th>
			<th><?php echo $this->lang->line('Máximo');?></th>
		</tr>
		<?php if (count($secciones)>0):?>
		<?php foreach ($secciones as $seccion):?>
		<tr id="sec_<?php echo $seccion['id'];?>">
			<td width="1%"><?php
			echo format_js_del($seccion['id'], 'catalogo/articuloseccion', 'articulo_seccion_del');?>
			</td>
			<td class="label"><?php echo $seccion['cNombre'];?> (<?php echo $seccion['nIdSeccion'];?>)</td>
			<td class="number"><?php echo $seccion['nStockDisponible'];?></td>
			<td class="number"><?php echo $seccion['nStockFirme'];?></td>
			<td class="number"><?php echo $seccion['nStockDeposito'];?></td>
			<td class="number"><?php echo $seccion['nStockRecibir'];?></td>
			<td class="number"><?php echo $seccion['nStockAPedir'];?> <?php echo format_enlace_cmd($this->lang->line('Pedir'), site_url('compras/reposicion/pedir_uno/' . $nIdLibro. '/' . $seccion['nIdSeccion'] . '/1'));?>
			</td>
			<td class="number"><?php echo $seccion['nStockServir'];?> <?php echo format_enlace_cmd($this->lang->line('Reservar'), site_url('ventas/pedidocliente/reservar/' . $nIdLibro. '/' . $seccion['nIdSeccion'] . '/1'));?>
			</td>
			<td class="number"><?php echo $seccion['nStockReservado'];?></td>
			<td class="number"><?php echo format_enlace_cmd($seccion['nStockADevolver'], site_url('catalogo/articulo/devoluciones/' . $nIdLibro));?></td>
			<td class="number"><?php echo $seccion['nStockMinimo'];?></td>
			<td class="number"><?php echo $seccion['nStockMaximo'];?></td>
		</tr>
		<?php endforeach;?>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="12"><?php echo format_enlace_js($this->lang->line('Añadir Sección'), "parent.addSeccion({$nIdLibro}, articulo_seccion_add)", 'icon-add');?>
			</td>
		</tr>
	</tfoot>
</table>
<br />
<table id="materias">
	<tbody>
		<tr>
			<th colspan="10" class="seccion"><?php echo $this->lang->line('Materias');?>
			</th>
		</tr>
		<tr>
			<th width="1px">&nbsp;</th>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('Código');?></th>
			<th><?php echo $this->lang->line('Materia');?></th>
		</tr>
		<?php if (count($materias)>0):?>
		<?php foreach ($materias as $seccion):?>
		<tr id="mat_<?php echo $seccion['id'];?>">
			<td width="1%"><?php
			echo format_js_del($seccion['id'], 'catalogo/articulomateria', 'articulo_materia_del');?></td>
			<td width="1" class="label"><?php echo $seccion['nIdMateria'];?></td>
			<td class="text"><?php echo $seccion['cCodMateria'];?></td>
			<td class="text"><?php echo $seccion['cNombre'];?></td>
		</tr>
		<?php endforeach;?>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4"><?php echo format_enlace_js($this->lang->line('Añadir Materia'), "parent.addMateria({$nIdLibro}, articulo_materia_add)", 'icon-add');?>
			</td>
		</tr>
	</tfoot>
</table>
<br />
<table id="palabrasclave">
	<tbody>
		<tr>
			<th colspan="10" class="seccion"><?php echo $this->lang->line('Palabras clave');?>
			</th>
		</tr>
		<tr>
			<th width="1px">&nbsp;</th>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('cPalabraClave');?></th>
		</tr>
		<?php if (count($palabrasclave)>0):?>
		<?php foreach ($palabrasclave as $seccion):?>
		<tr id="mat_<?php echo $seccion['id'];?>">
			<td width="1%"><?php
			echo format_js_del($seccion['id'], 'catalogo/articulopalabraclave', 'articulo_palabraclave_del');?></td>
			<td width="1" class="label"><?php echo $seccion['id'];?></td>
			<td class="text"><?php echo $seccion['cPalabraClave'];?></td>
		</tr>
		<?php endforeach;?>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4"><?php echo format_enlace_js($this->lang->line('Añadir Materia'), "parent.addMateria({$nIdLibro}, articulo_materia_add)", 'icon-add');?>
			</td>
		</tr>
	</tfoot>
</table>
<br />

<table id="proveedores">
	<tbody>
		<tr>
			<th colspan="10" class="seccion"><?php echo $this->lang->line('Proveedores');?>
			</th>
		</tr>
		<tr>
			<th width="1%">&nbsp;</th>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('Proveedor');?></th>
			<th><?php echo $this->lang->line('Origen');?></th>
			<th><?php echo $this->lang->line('Descuento');?></th>
			<th><?php echo $this->lang->line('Días');?></th>
		</tr>
		<?php if (count($proveedores_all)>0):?>
		<?php foreach ($proveedores_all as $seccion):?>
		<?php if (isset($seccion['id'])):?>
		<tr id="pv_<?php echo $seccion['id'];?>">
			<td width="1%"><?php
			echo ($seccion['origen'] == 'libro')?format_js_del($seccion['id'], 'catalogo/proveedorarticulo', 'articulo_proveedor_del'):'&nbsp;';?></td>
			<td class="label"><?php echo format_enlace_cmd($seccion['nIdProveedor'], site_url('proveedores/proveedor/index/' . $seccion['nIdProveedor']));?>
			</td>
			<td class="text"><?php if ($seccion['default']):?> <strong>* <?php endif;?><?php echo $seccion['text'];?>
			<?php if ($seccion['default']):?> </strong> <?php endif;?>
			<?php if (isset($seccion['text2'])):?><?php echo $seccion['text2'];?><?php endif;?>
			<?php if ($seccion['disabled']):?><br/><span class="articulocodigo label-warning"><?php echo $this->lang->line('DESACTIVADO');?></span><?php endif;?>
		</td>
			<td class="text"><?php echo $this->lang->line('origen_proveedor_' .$seccion['origen']);?></td>
			<td width="1" class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
			<td width="1" class="number"><?php echo (isset($seccion['nDiasEnvio']))?$seccion['nDiasEnvio']:'';?></td>
		</tr>
		<?php endif;?>
		<?php endforeach;?>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6"><?php echo format_enlace_js($this->lang->line('Añadir Proveedor'), "parent.addProveedor({$nIdLibro}, articulo_proveedor_add)", 'icon-add');?>
			</td>
		</tr>
	</tfoot>
</table>
		<?php if (isset($pedidos_proveedor) || isset($pedidos_cliente) || isset($presupuestos)):?> <br />
<table>
<?php if (isset($pedidos_proveedor) && count($pedidos_proveedor) > 0):?>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('Pedidos proveedor pendientes');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('Fecha');?></th>
		<th><?php echo $this->lang->line('Estado');?></th>
		<th><?php echo $this->lang->line('Proveedor');?></th>
		<th><?php echo $this->lang->line('Sección');?></th>
		<th><?php echo $this->lang->line('Cantidad');?></th>
		<th><?php echo $this->lang->line('Importe');?></th>
		<th><?php echo $this->lang->line('Descuento');?></th>
	</tr>
	<?php foreach ($pedidos_proveedor as $seccion):?>
	<tr>
		<td width="1%" class="label"><?php echo format_enlace_cmd($seccion['id'], site_url('compras/pedidoproveedor/index/' . $seccion['id']));?>
		</td>
		<td class="text"><?php echo format_date((isset($seccion['dFecha']) && $seccion['dFecha']!='')?$seccion['dFecha']:$seccion['dCreacion']);?></td>
		<td class="text"><?php echo $seccion['cEstado'];?><?php if (isset($seccion['cInformacion'])):?>
		<br />
		<div class="icon-status-text-<?php echo $seccion['nIdInformacion'];?>"><?php echo $this->lang->line($seccion['cInformacion']);?></div>
		<?php echo format_date($seccion['dFechaInformacion']);?> <?php endif;?></td>
		<td class="text"><?php echo format_enlace_cmd($seccion['nIdPv'], site_url('proveedores/proveedor/index/' . $seccion['nIdPv']));?>
		- <?php echo format_name($seccion['cNombre'], $seccion['cApellido'], $seccion['cEmpresa']);?></td>
		<td class="text"><?php echo $seccion['cSeccion'];?></td>
		<td class="text"><?php echo $seccion['nCantidad']-$seccion['nRecibidas'];?>
			<?php echo $seccion['bDeposito']?('<span style="color: green;">'.$this->lang->line('DP') .'</span>'):''; ?>
		</td>
		<td class="text"><?php echo format_price($seccion['fPrecio']);?></td>
		<td class="text"><?php echo format_percent($seccion['fDescuento']);?></td>
	</tr>

	<?php endforeach;?>
	<?php endif; ?>

	<?php if (isset($pedidos_cliente) && count($pedidos_cliente) > 0):?>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('Pedidos cliente pendientes');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('Fecha');?></th>
		<th><?php echo $this->lang->line('Estado');?></th>
		<th><?php echo $this->lang->line('Cliente');?></th>
		<th><?php echo $this->lang->line('Sección');?></th>
		<th><?php echo $this->lang->line('Cantidad');?></th>
		<th><?php echo $this->lang->line('Importe');?></th>
		<th><?php echo $this->lang->line('Descuento');?></th>
		<th><?php echo $this->lang->line('Avisado');?></th>
	</tr>
	<?php foreach ($pedidos_cliente as $seccion):?>
	<tr>
		<td width="1" class="label"><?php echo format_enlace_cmd($seccion['id'], site_url('ventas/pedidocliente/index/' . $seccion['id']));?></td>
		<td class="text"><?php echo format_date($seccion['dFecha']);?></td>
		<td class="text"><?php echo $seccion['cEstado'];?></td>
		<td class="text"><?php echo format_enlace_cmd($seccion['nIdCl'], site_url('clientes/cliente/index/' . $seccion['nIdCl']));?>
		- <?php echo format_name($seccion['cNombre'], $seccion['cApellido'], $seccion['cEmpresa']);?></td>
		<td class="text"><?php echo $seccion['cSeccion'];?></td>
		<td class="text"><?php echo $seccion['nCantidad'];?></td>
		<td class="text"><?php echo format_price($seccion['fPrecio']);?></td>
		<td class="text"><?php echo format_percent($seccion['fDescuento']);?></td>
		<td class="text"><?php echo $seccion['bAviso']?format_datetime($seccion['dAviso']):'&nbsp;';?></td>
	</tr>

	<?php endforeach;?>
	<?php endif; ?>

	<?php if (isset($presupuestos) && count($presupuestos) > 0):?>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('Presupuestos pendientes');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('Fecha');?></th>
		<th><?php echo $this->lang->line('Estado');?></th>
		<th><?php echo $this->lang->line('Cliente');?></th>
		<th><?php echo $this->lang->line('Sección');?></th>
		<th><?php echo $this->lang->line('Cantidad');?></th>
		<th><?php echo $this->lang->line('Importe');?></th>
		<th><?php echo $this->lang->line('Descuento');?></th>
	</tr>
		<?php foreach ($presupuestos as $seccion):?>
		<tr>
			<td width="1" class="label"><?php echo format_enlace_cmd($seccion['id'], site_url('ventas/pedidocliente/index/' . $seccion['id']));?></td>
			<td class="text"><?php echo format_date($seccion['dFecha']);?></td>
			<td class="text"><?php echo $seccion['cEstado'];?></td>
			<td class="text"><?php echo format_enlace_cmd($seccion['nIdCl'], site_url('clientes/cliente/index/' . $seccion['nIdCl']));?>
			- <?php echo format_name($seccion['cNombre'], $seccion['cApellido'], $seccion['cEmpresa']);?></td>
			<td class="text"><?php echo $seccion['cSeccion'];?></td>
			<td class="text"><?php echo $seccion['nCantidad'];?></td>
			<td class="text"><?php echo format_price($seccion['fPrecio']);?></td>
			<td class="text"><?php echo format_percent($seccion['fDescuento']);?></td>
		</tr>
		<?php endforeach;?>
	<?php endif; ?>

</table>
	<?php endif; ?>
<table id="ubicaciones">
	<tbody>
		<tr>
			<th colspan="10" class="seccion"><?php echo $this->lang->line('Ubicaciones');?>
			</th>
		</tr>
		<tr>
			<th width="1%">&nbsp;</th>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('Ubicación');?></th>
			<th><?php echo $this->lang->line('Fecha');?></th>
		</tr>
		<?php if (isset($ubicaciones) && (count($ubicaciones) > 0)):?>
		<?php foreach ($ubicaciones as $seccion):?>
		<tr id="ub_<?php echo $seccion['id'];?>">
			<td width="1%"><?php
			echo format_js_del($seccion['id'], 'catalogo/articuloubicacion', 'articulo_ubicacion_del');?></td>
			<td width="1" class="label"><?php echo $seccion['nIdUbicacion'];?></td>
			<td class="text"><?php echo $seccion['cDescripcion'];?></td>
			<td class="text"><?php echo format_date($seccion['dAct']);?></td>
		</tr>
		<?php endforeach;?>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4"><?php echo format_enlace_js($this->lang->line('Añadir Ubicación'), "parent.addUbicacion({$nIdLibro}, articulo_ubicacion_add)", 'icon-add');?>
			</td>
		</tr>
	</tfoot>
</table>
		<?php if (isset($sinopsis['tSinopsis'])):?> <br />
<table class="header">
	<tr>
		<th class="seccion"><?php echo $this->lang->line('Sinopsis');?></th>
	</tr>
	<tr>
		<td><?php echo $sinopsis['tSinopsis'];?></td>
	</tr>
</table>
		<?php endif; ?></div>
<script type="text/javascript">
$(function() {
	$("#portada2, #multiple").html5Uploader({
		name: "file",
		postUrl: parent.site_url('catalogo/articulo/set_cover/<?php echo $nIdLibro;?>'),
		onServerError: function (e) {
			parent.Ext.app.msgFly(parent._s('Portada'),parent._s('error al cambiar la portada'));
		},
		onServerLoad: function (e, file) {
			var el = $('#portada');
			el = el[0];
			if (el.src != null)
				el.src = parent.site_url('catalogo/articulo/cover/<?php echo $nIdLibro;?>/' + el.width + '?' + parent.Ext.app.createId());
			parent.Ext.app.msgFly(parent._s('Portada'),parent.sprintf(parent._s('cambiar la portada ok'), file.fileName));
		}	
	});
});
</script>