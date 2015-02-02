<?php
if (!isset($cols)) $cols = 2;
$width = (int) 100 / $cols;
if (!isset($stock)) $stock = 0;
?>
<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tbody>
		<tr>
			<td class="borde_verd">
			<table class="bordetaula" cellspacing="0" cellpadding="0"
				width="100%">
				<tbody>
					<tr>
						<td class="taula_banner" valign="top" width="760"><span
							class="txt_adreca"><?php echo $this->config->item('company.street.line');?></span></td>
						<td><img src="<?php echo $this->config->item('company.logo');?>"
							align="right" /></td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			<table class="taula_idiomas" cellspacing="0" cellpadding="0"
				width="100%" border="0">
				<tbody>
					<tr class="taula_idiomas">
						<td class="taula_idiomas" valign="middle"><a
							href="http://www.alibri.es"> <img border="0"
							src="http://www.alibri.es/templates/alibri/images/bullet2.gif" />
						</a> <?php echo $this->lang->line('report-ALIBRI Informa'); ?> - <?php echo $cDescripcion;?></td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>

<?php $col = 0; ?>
<?php if ($col == 0):?>
<div class="nopagebreak">
<table class="fons2" cellspacing="0" cellpadding="5" width="100%"
	border="0">
	<tbody>

		<tr>
		<?php endif;?>
		<?php foreach($libros as $libro):?>
			<td width="<?php echo $width;?>%">
			<table border="0" width="100%" cellspacing="1" cellpadding="2">
				<tr>
					<td width="1%" valign="top" align="center">
					<div class="nopagebreak">
					<?php echo format_cover_web($libro['nIdLibro']);?>						
					</div>
					</td>
					<td valign="top">
					<table width="100%">
						<tr>
							<td colspan="2" class="contentheading"><img
								src="http://www.alibri.es/components/com_commerce/catalog/images/arrow.png" />
								<?php echo $libro["cTitulo"];?></td>
						</tr>
						<tr>
							<td>
							<?php
							$datos = array(); 
							if (isset($libro["cAutores"]))	
								$datos[] = "<strong>{$libro["cAutores"]}</strong>";
							if (isset($libro["cISBN"]))	
								$datos[] = $libro["cISBN"];
							if (isset($libro["editorial"]['cNombre'])) 
								$datos[] = $libro["editorial"]['cNombre'];
							if (isset($libro["cAutores"]))	
								$datos[] = $libro["nPag"] .' ' . $this->lang->line('report-pags-short');
							if (isset($libro["cEdicion"]))	
								$datos[] = $libro["cEdicion"];
							if (isset($libro["idioma"]['cDescripcion']))
								$datos[] = $libro["idioma"]['cDescripcion'];
							if (isset($libro["tipo"]['cDescripcion']))
								$datos[] = $libro["tipo"]['cDescripcion']; 
								?>
							<?php echo (count($datos)>0)?implode(' | ', $datos):'&nbsp;';?>
							<?php if (($stock == 1) && (isset($libro['secciones']))): ?>
							<br/>
							<span style="font-size: 75%">
							<?php foreach ($libro['secciones'] as $seccion):?>
							<?php if ($seccion['nStockFirme'] + $seccion['nStockDeposito'] + $seccion['nStockRecibir'] > 0 ):?>
							<?php echo $seccion['cNombre'];?> : [F -  <strong><?php echo $seccion['nStockFirme'];?></strong> | 
							D -  <strong><?php echo $seccion['nStockDeposito'];?></strong> |
							R -  <strong><?php echo $seccion['nStockRecibir'];?></strong>]&nbsp;
							<?php endif;?>
							<?php endforeach;?>
							</span>
							<?php endif; ?> 
							</td>
							<td valign="top" align="right" nowrap="yes"><?php if (isset($dto) && ($dto>0)):?> <span
								class="precioold"><?php echo format_price($libro["fPVP"]);?> </span><BR />
							<span class="precio"><?php echo format_price($libro["fPVP"] * (1 - $dto/100.0));?></span>
							<br/><span style="font-size: 75%">(<?php echo $dto;?>%)</span>
							<?php else:?> <span class="precio"><?php echo format_price($libro["fPVP"]);?>
							</span> <?php endif;?></td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
			<?php $col++; ?>
			<?php if ($col == $cols):?>
			<?php $col = 0;?>
		</tr>
	</tbody>
</table>
</div>
<div class="nopagebreak">
<table class="fons2" cellspacing="0" cellpadding="5" width="100%"
	border="0">
	<tbody>
			<?php endif;?>
			<?php endforeach; ?>
			<?php if ($col > 0):?>
</tr>
</tbody>
</table>
</div>
			<?php endif;?>

<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'mailing' . DIRECTORY_SEPARATOR . 'footer.php'); ?>
