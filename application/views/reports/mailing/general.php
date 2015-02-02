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
						</a> <?php echo $this->lang->line('report-ALIBRI Informa');?> - <?php echo $cDescripcion;?></td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>

<?php foreach($libros as $libro):?>
<div class="nopagebreak">
<table border="0" width="100%" cellspacing="1" cellpadding="2">
	<tr>
		<td colspan="3" class="contentheading"><img
			src="http://www.alibri.es/templates/alibri/images/arrow.png" />
			<?php echo $libro["cTitulo"];?></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td width="1%" valign="top" align="center"><?php echo format_cover_web($libro['nIdLibro']);?></td>
		<td width="99%" valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<?php if (isset($libro["cAutores"])):?>
			<tr>
				<td width="85%" class="titulo" colspan="2"><b><?php echo $this->lang->line('report-Autor/es');?></b>:
				<?php echo $libro["cAutores"];?></td>
			</tr>
			<?php endif;?>

			<?php if (isset($libro["cISBN"])):?>
			<tr>
				<td width="85%" class="titulo" colspan="2"><b><?php echo $this->lang->line('report-ISBN');?></b>:
				<?php echo $libro["cISBN"];?></td>
			</tr>
			<?php endif;?>

			<?php if (isset($libro["editorial"]['cNombre'])):?>
			<tr>
				<td width="85%" class="titulo" colspan="2"><b><?php echo $this->lang->line('report-Editorial');?></b>:
				<?php echo $libro["editorial"]["cNombre"];?></td>
			</tr>
			<?php endif;?>

			<?php if (isset($libro["nPag"]) && ($libro["nPag"] > 0)):?>
			<tr>
				<td width="85%" class="titulo" colspan="2"><b><?php echo $this->lang->line('report-Páginas');?></b>:
				<?php echo $libro["nPag"];?></td>
			</tr>
			<?php endif;?>

			<?php if (isset($libro["cEdicion"])):?>
			<tr>
				<td width="85%" class="titulo" colspan="2"><b><?php echo $this->lang->line('report-Edición');?></b>:
				<?php echo $libro["cEdicion"];?></td>
			</tr>
			<?php endif;?>

			<?php if (isset($libro["idioma"]['cDescripcion'])):?>
			<tr>
				<td width="85%" class="titulo" colspan="2"><b><?php echo $this->lang->line('report-Idioma');?></b>:
				<?php echo $libro["idioma"]['cDescripcion'];?></td>
			</tr>
			<?php endif;?>

			<?php if (isset($libro["tipo"]['cDescripcion'])):?>
			<tr>
				<td width="85%" class="titulo" colspan="2"><b><?php echo $this->lang->line('report-Soporte');?></b>:
				<?php echo $libro["tipo"]['cDescripcion'];?></td>
			</tr>
			<?php endif;?>
		</table>
		</td>
		<td width="1%" valign="top" nowrap="yes">
		<span class="precio"><?php echo format_price($libro["fPVP"]);?>
							</span> 
		</td>
		</tr>
		<tr>
		<td colspan="3">		

		<?php if (isset($sinopsis) && ($sinopsis == 'true') && (isset($libro["sinopsis"]['tSinopsis']))):?>
		<table class="contentpaneopen" width="100%" border="0" cellspacing="0"
			cellpadding="2">
			<tr>
				<td><?php echo strip_tags_attributes($libro["sinopsis"]['tSinopsis'], $this->config->item('bp.boletin.allowtags'));?></td>
			</tr>
		</table>
		<?php endif;?>

		<table class="moduletablecesta" cellspacing="0" cellpadding="0"
			width="100%" border="0">
			<tr>
				<td><a
					href="http://www.alibri.es/id/<?php echo $libro["nIdLibro"];?>">
				<img border="0"
					src="http://www.alibri.es/templates/alibri/images/arrow.gif" /> <?php echo $this->lang->line('report-Más información ');?></a></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</div>
					<?php endforeach;?>

<?php
	require (APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'mailing' . DIRECTORY_SEPARATOR . 'footer.php');
 ?>
