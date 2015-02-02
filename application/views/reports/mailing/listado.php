<?php
if (!isset($autor)) $autor = 'false';
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

<table class="fons2" cellspacing="0" cellpadding="5" width="100%"
	border="0">
	<tbody>

	<?php foreach($libros as $libro):?>
		<tr class="linealistado">
		<?php if ($autor=='true'):?>
			<td><img
				src="http://www.alibri.es/templates/alibri/images/arrow.png" />
				<?php echo $libro["cAutores"];?></td>
				<?php endif;?>
			<td class="linealistado"><?php if ($autor!='true'):?><img
				src="http://www.alibri.es/templates/alibri/images/arrow.png" />
				<?php endif;?><?php echo $libro["cTitulo"];?></td>
			<td class="precio" nowrap="yes"><?php if (isset($dto)):?> <span
				class="precioold"><?php echo format_price($libro["fPVP"]);?> </span>
			(<?php echo $dto;?>%)&nbsp;<span class="precio"><?php echo format_price($libro["fPVP"] * (1 - $dto/100.0));?></span>
			<?php else:?> <span class="precio"><?php echo format_price($libro["fPVP"]);?>
			</span> <?php endif;?></td>
		</tr>


		<?php endforeach; ?>
	</tbody>
</table>

<!-- Cabecera del Documento -->
<table class="fonsgris" cellspacing="0" cellpadding="5" width="100%"
	border="0">
	<tbody>
		<tr>
			<td valign="top">
			<table cellspacing="0" cellpadding="0" width="100%" border="0">
				<tbody>
					<tr>
						<td class="caixaresuminf" align="middle" colspan="4"></td>
					</tr>
					<tr>
						<td class="taula_idiomas" align="middle" colspan="4">
						<div class="c1"><?php echo $this->config->item('company.name');?><br />
						<?php echo $this->config->item('company.address.1');?> <?php echo $this->config->item('company.address.2');?>
						<?php echo $this->config->item('company.address.3');?><br />
						<?php echo $this->lang->line('report-tel-short');?> <?php echo $this->config->item('company.telephone');?>
						<?php echo $this->lang->line('report-fax-short');?> <?php echo $this->config->item('company.fax');?><br />
						<a
							href="mailto:<?php echo $this->config->item('company.email');?>"><?php echo $this->config->item('company.email');?></a><br />
						</div>
						</td>
					</tr>
					<tr>
						<td class="caixaresuminf" align="middle" height="3"><img
							src="http://www.alibri.es/templates/alibri/images/transp_5h.gif"
							width="7" /></td>
					</tr>
					<tr>
						<td align="center"><?php echo $this->lang->line('report-text-delete'); ?>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
