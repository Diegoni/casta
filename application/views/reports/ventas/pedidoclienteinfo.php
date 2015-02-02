<?php $this->load->helper('asset');?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php'); ?>
<?php $num_lineas_1 = isset($num_lineas_1)?$num_lineas_1:10;?>
<?php $num_lineas_2 = isset($num_lineas_2)?$num_lineas_2:15;?>
<?php $historico = isset($historico)?$historico:0;?>
<?php
$titulo = ($nIdEstado==3)?$this->lang->line('report-presupuesto'):$this->lang->line('report-Pedido de Cliente');
$nIdDocumento = $nIdPedido;
$texto_condiciones = $this->lang->line('text-pedidocliente');
$borrador = FALSE;

$count = 0;
$actual = 0;
$paginas = array();
$pagina = 0;
$titulos = count($lineas);
$ejemplares = 0;
foreach($lineas as $linea)
{
	$paginas[$pagina][] = $linea;
	$ejemplares += $linea['nCantidad'];
	$actual++;
	if ((($pagina == 0) && ($actual == $num_lineas_1))||(($pagina > 0) && ($actual == $num_lineas_2)))
	{
		$pagina++;
		$actual = 0;
	}
}
$obj = get_instance();
$obj->load->model('catalogo/m_articulo');
?>

<div id="page-wrap"><?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'header.php'); ?>
<?php $total = 0;?> <?php $ivas = array();?> <?php $bases = array();?> <?php $actual = 0;?>
<?php foreach($paginas as $pagina):?>
<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-#');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Cant');?></th>
		<!--  <th class="items-th"><?php echo $this->lang->line('report-Referencia');?></th>-->
		<th class="items-th"><?php echo $this->lang->line('report-Título');?></th>
		<?php if ($precio):?>
		<th class="items-th"><?php echo $this->lang->line('report-Precio');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Desc.');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Base');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Tipo IVA');?></th>
		<?php endif;?>
		<!-- <th>IVA</th>-->
		<!-- <th>Importe</th>-->
	</tr>
	<?php foreach($pagina as $linea):?>
	<tr class="item-row">
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.articulo');?>">
			<?php echo format_cover($linea['nIdLibro'], $this->config->item('bp.catalogo.cover.articulo'));?>
		</td>
		<td class="item-ct"><?php echo $linea['nCantidad'];?></td>
		<td class="item-name"><?php echo format_title($linea['cTitulo'], $titlelen);?><br />
		<?php echo (isset($linea['cRefCliente'])&& ($linea['cRefCliente']!=''))?$this->lang->line('report-Ref.'). ': ' . format_title($linea['cRefCliente'], $reflen):'';?>
		[<strong><?php echo $linea['nIdLibro'];?></strong>] <?php echo (isset($linea['cISBN'])&&$linea['cISBN']!='')?'[' . $linea['cISBN'] . ']':'';?>
		<?php echo (isset($linea['cAutores'])&&$linea['cAutores']!='')?format_title($linea['cAutores'], $autorlen):'';?>
		<?php echo (isset($linea['cEditorial'])&&$linea['cEditorial']!='')?' - <strong>' . $linea['cEditorial'] . '</strong>':'';?><br/>
		<?php echo (isset($linea['cEstado'])&&$linea['cEstado']!='')?'[' . $linea['cEstado'] . ']':'';?>
		<?php
		$libro = $obj->m_articulo->load($linea['nIdLibro'], TRUE);		 
		?>
		<?php echo (isset($libro['cEstado'])&&$libro['cEstado']!='')?'[' . $libro['cEstado'] . ']':'';?>
		<?php if (isset($libro['secciones'])):?> <br />
		<div  style="page-break-inside: avoid;">
<table class="small">
	<tr class="small">
		<th><?php echo $this->lang->line('report-Sec');?></th>
		<th><?php echo $this->lang->line('report-F');?></th>
		<th><?php echo $this->lang->line('report-D');?></th>
		<th><?php echo $this->lang->line('report-Rec');?></th>
		<th><?php echo $this->lang->line('report-A Ped.');?></th>
		<th><?php echo $this->lang->line('report-A Ser.');?></th>
		<th><?php echo $this->lang->line('report-Reser.');?></th>
		<th><?php echo $this->lang->line('report-A De.');?></th>
	</tr>

	<?php foreach ($libro['secciones'] as $seccion):?>
	<?php if (($seccion['nStockFirme'] !=0) || ($seccion['nStockDeposito'] != 0) 
		|| ($seccion['nStockRecibir'] != 0)
		|| ($seccion['nStockAPedir'] != 0)
		|| ($seccion['nStockServir'] !=  0)
		|| ($seccion['nStockReservado'] != 0)
		|| ($seccion['nStockADevolver'] != 0)):?>
	<tr class="small">
		<td><?php echo $seccion['cNombre'];?> (<?php echo $seccion['nIdSeccion'];?>)</td>
		<td><?php echo $seccion['nStockFirme'];?></td>
		<td><?php echo $seccion['nStockDeposito'];?></td>
		<td><?php echo $seccion['nStockRecibir'];?></td>
		<td><?php echo $seccion['nStockAPedir'];?></td>
		<td><?php echo $seccion['nStockServir'];?></td>
		<td><?php echo $seccion['nStockReservado'];?></td>
		<td><?php echo $seccion['nStockADevolver'];?></td>
	</tr>
	<?php endif; ?>

	<?php endforeach;?>
</table>
</div>
<?php endif;?> <?php if (isset($libro['materias'])):?>
	<div class="small">
	<?php foreach ($libro['materias'] as $seccion):?>
		<?php echo $seccion['cNombre'];?>,
	<?php endforeach;?>
	</div>
<?php endif; ?>
<?php if (isset($libro['ubicaciones'])):?>
	<div class="small">
	<?php foreach ($libro['ubicaciones'] as $seccion):?>
		<?php echo $seccion['cDescripcion'];?>,
	<?php endforeach;?>
	</div>
<?php endif; ?>  
		</td>
		<?php if ($precio):?>
		<td class="item-pvp"><?php echo format_price($linea['fPVP'], FALSE);?></td>
		<td class="item-dto"><?php echo ($linea['fDescuento']>0)?format_number($linea['fDescuento']):'&nbsp;';?></td>
		<td class="item-base"><?php echo format_price($linea['fBase'], FALSE);?></td>
		<td class="item-iva"><?php echo format_number($linea['fIVA']);?></td>
		<!--  <td><?php echo format_number($linea['fIVAImporte']);?></td>-->
		<!-- <td><?php echo format_price($linea['fTotal'], FALSE);?></td>-->
		<?php endif; ?>
	</tr>
	<?php $total += $linea['fTotal'];?>
	<?php $ivas[$linea['fIVA']] = (isset($ivas[$linea['fIVA']])?$ivas[$linea['fIVA']]:0) + $linea['fIVAImporte'];?>
	<?php $bases[$linea['fIVA']] = (isset($bases[$linea['fIVA']])?$bases[$linea['fIVA']]:0) + $linea['fBase'];?>
	<?php endforeach;?>
</table>
<?php $actual++;?> <?php if ($actual != (count($paginas))):?>
<div class="page-break"></div>
<?php endif;?> <?php $iva = 0;?> <?php $base = 0;?> <?php endforeach;?>
<div style="clear: both"></div>

<?php foreach($ivas as $k => $v):?> <?php $iva += $v;?> <?php $base += $bases[$k];?>
<?php endforeach;?>

<div id="footer">
<table id="totals">
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Ejemplares');?></td>
		<td class="total-value"><?php echo format_number($ejemplares);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Títulos');?></td>
		<td class="total-value"><?php echo format_number($titulos);?></td>
	</tr>
	<?php if ($precio):?>

	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Base');?></td>
		<td class="total-value"><?php echo format_price($base);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="total-value"><?php echo format_price($iva);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Total');?></td>
		<td class="total-value"><?php echo format_price($total);?></td>
	</tr>
	<?php endif; ?>
</table>
	<?php if ($precio):?>

<table id="taxes">
	<tr>
		<td class="taxes-head"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Base');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Importe');?></td>
	</tr>
	<?php $iva = 0;?>
	<?php $base = 0;?>
	<?php foreach($ivas as $k => $v):?>
	<tr>
		<td class="taxes-value"><?php echo format_number($k);?></td>
		<td class="taxes-value"><?php echo format_price($bases[$k], FALSE);?></td>
		<td class="taxes-value"><?php echo format_price($v, FALSE);?></td>
	</tr>
	<?php $iva += $v;?>
	<?php $base += $bases[$k];?>
	<?php endforeach;?>
</table>
	<?php endif; ?>
<div style="clear: both"></div>
<div id="terms">
<h5><?php echo $this->lang->line('report-Comentarios');?></h5>
<?php if (isset($tNotasExternas) && ($tNotasExternas != '')):?>
<div class="separator"><?php echo $tNotasExternas;?></div>
<?php endif; ?>
<?php if (isset($tNotasInternas) && ($tNotasInternas != '')):?>
<div class="separator"><?php echo $tNotasInternas;?></div>
<?php endif; ?>
<?php $notas = $this->reg->get_notas($nIdPedido);?>
<?php foreach ($notas as $nota):?>
<div class="separator"><?php echo $nota['tObservacion']; ?></div>
<?php endforeach; ?>

</div>
</div>
</div>
