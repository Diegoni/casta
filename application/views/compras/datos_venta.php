<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<div class="details-panel"><?php
$error = array();
$hay = FALSE;
if (isset($ult_docs_general['entrada']))
{
	$hay = TRUE;
	$diff = dateDifference($ult_docs_general['entrada']['dFecha'], time());
	$dias = $diff[2];
}

if (isset($dCreacion))
{
	$hay = TRUE;
	$diff = dateDifference($dCreacion, time());
	$dias = $diff[2];
}

$stock = $pedir = $devolver = 0;
if (isset($secciones))
{
	foreach ($secciones as $seccion)
	{
		$stock += $seccion['nStockFirme'] + $seccion['nStockDeposito'] - $seccion['nStockReservado'];
		$pedir += $seccion['nStockRecibir'] + $seccion['nStockAPedir'] - $seccion['nStockServir'];
		$devolver += $seccion['nStockADevolver'];
	}
}
if (isset($nIdOferta)) $error[] = $this->lang->line('repo-oferta-warning');
if ($devolver > 0) $error[] = $this->lang->line('repo-devolver-warning');
if ($stock > 0) $error[] = $this->lang->line('repo-stock-warning');
if (isset($mes3))
{
	if ($mes3 <= 0 && $dias > 90) $error[] = $this->lang->line('repo-siventas-warning'); 
}
if (!$hay || $dias >= 365) $error[] = $this->lang->line('repo-antiguo-warning');
if ((!$hay || $dias >= 365) && ($mes12 <= 2)) $error[] = $this->lang->line('repo-siventas-year-warning');
if ($pedir > 0) $error[] = $this->lang->line('repo-pedir-warning');
?> 
<?php if (count($error) > 0 ):?>
<table width="100%">
	<tr>
		<td><?php echo implode('<br/>', $error); ?></td>
	</tr>
</table>
<?php endif; ?>
<table width="100%">
	<tr>
		<td colspan="5">
		<table width="100%">
			<tr>
				<td class="title"><?php echo format_enlace_cmd($cTitulo, site_url('catalogo/articulo/index/' . $nIdLibro));?></td>
				<td class="title" align="center" valign="top"><?php echo format_price($fPVP);?>
				(<?php echo format_price($fPrecio);?>)</td>
				<td class="author" align="right"><?php echo $cAutores;?></td>
			</tr>
		</table>
		</td>
	</tr>

	<tr valign="top">
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.datosventa');?>"><?php echo format_cover($nIdLibro, $this->config->item('bp.catalogo.cover.datosventa'), 'portada');?></td>
		<td>
		<table>
			<tr>
				<td class="label"><?php echo $this->lang->line('Estado')?>:</td>
				<td class="info"><?php echo $cEstado;?></td>
			</tr>

			<tr>
				<td class="label"><?php echo $this->lang->line('Proveedor')?>:</td>
				<td class="info"><?php echo $cProveedor;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Editorial')?>:</td>
				<td class="info"><?php echo $cEditorial;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Colección')?>:</td>
				<td class="info"><?php echo $cColeccion;?>&nbsp;</td>
			</tr>
		</table>
		</td>
		<td>

		<table>
			<tr>
				<td class="label"><?php echo $this->lang->line('Semana');?></td>
				<?php if (isset($idseccion)):?>
				<td class="number"><?php echo (isset($semana)?$semana:0);?></td>
				<?php endif;?>
				<td class="number"><?php echo (isset($t_semana)?$t_semana:0);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Mes');?></td>
				<?php if (isset($idseccion)):?>
				<td class="number"><?php echo (isset($mes)?$mes:0);?></td>
				<?php endif;?>
				<td class="number"><?php echo (isset($t_mes)?$t_mes:0);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('3 Meses');?></td>
				<?php if (isset($idseccion)):?>
				<td class="number"><?php echo (isset($mes3)?$mes3:0);?></td>
				<?php endif;?>
				<td class="number"><?php echo (isset($t_mes3)?$t_mes3:0);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('6 Meses');?></td>
				<?php if (isset($idseccion)):?>
				<td class="number"><?php echo (isset($mes6)?$mes6:0);?></td>
				<?php endif;?>
				<td class="number"><?php echo (isset($t_mes6)?$t_mes6:0);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('12 Meses');?></td>
				<?php if (isset($idseccion)):?>
				<td class="number"><?php echo (isset($mes12)?$mes12:0);?></td>
				<?php endif;?>
				<td class="number"><?php echo (isset($t_mes12)?$t_mes12:0);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('24 Meses');?></td>
				<?php if (isset($idseccion)):?>
				<td class="number"><?php echo (isset($mes24)?$mes24:0);?></td>
				<?php endif;?>
				<td class="number"><?php echo (isset($t_mes24)?$t_mes24:0);?></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<th class="none">&nbsp;</th>
				<?php if (isset($idseccion)):?><th><?php echo $this->lang->line('Sección')?></th><?php endif;?>
				<th><?php echo $this->lang->line('Todo')?></th>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Entrada')?></td>
				<?php if (isset($idseccion)):?><td class="info"><?php echo isset($ult_docs['entrada']['dCierre'])?format_datetime($ult_docs['entrada']['dCierre']):'&nbsp;';?></td><?php endif;?>
				<td class="info"><?php echo isset($ult_docs_general['entrada']['dCierre'])?format_datetime($ult_docs_general['entrada']['dCierre']):'&nbsp;';?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Salida')?></td>
				<?php if (isset($idseccion)):?><td class="info"><?php echo isset($ult_docs['salida'])?format_datetime($ult_docs['salida']):'&nbsp;';?></td><?php endif;?>
				<td class="info"><?php echo isset($ult_docs_general['salida'])?format_datetime($ult_docs_general['salida']):'&nbsp;';?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Devolución')?></td>
				<?php if (isset($idseccion)):?><td class="info"><?php echo isset($ult_docs['devolucion'])?format_datetime($ult_docs['devolucion']):'&nbsp;';?></td><?php endif;?>
				<td class="info"><?php echo isset($ult_docs_general['devolucion'])?format_datetime($ult_docs_general['devolucion']):'&nbsp;';?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('A Recibir')?></td>
				<?php if (isset($idseccion)):?><td class="info"><?php echo isset($ult_docs['pendiente'])?format_datetime($ult_docs['pendiente']):'&nbsp;';?></td><?php endif;?>
				<td class="info"><?php echo isset($ult_docs_general['pendiente'])?format_datetime($ult_docs_general['pendiente']):'&nbsp;';?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('A Pedir')?></td>
				<?php if (isset($idseccion)):?><td class="info"><?php echo isset($ult_docs['apedir'])?format_datetime($ult_docs['apedir']):'&nbsp;';?></td><?php endif;?>
				<td class="info"><?php echo isset($ult_docs_general['apedir'])?format_datetime($ult_docs_general['apedir']):'&nbsp;';?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
				<?php if (isset($secciones)):?> <br />
<table>
	<tr class="label">
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
	</tr>
<?php $par = FALSE;?>
	<?php foreach ($secciones as $seccion):?>
		<?php $class = ($par)? 'number2':'number';?>
	<tr>
		<td class="label"><?php echo $seccion['cNombre'];?> (<?php echo $seccion['nIdSeccion'];?>)</td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockDisponible']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockFirme']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockDeposito']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockRecibir']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockAPedir']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockServir']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockReservado']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockADevolver']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockMinimo']);?></td>
	</tr>
<?php $par = !$par;?>
	<?php endforeach;?>
</table>
<?php endif;?></div>
