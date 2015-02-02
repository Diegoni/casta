<html>
<head>
<title>Pedido de Proveedor</title>
	<style type="text/css" xml:space="preserve">
/*<![CDATA[*/
.peque {
font : 8px Verdana, Geneva, Arial, Helvetica, sans-serif;
}

/*]]>*/
</style>
</head>
<body>
	<table width="100%" border="1" cellspacing="0" cellpadding="4" bordercolor="#333333">
		<tr>
			<td rowspan="4" align="center" valign="top" width="50%">
				<font face="verdana" size="+1">
FEDERACION ESPAÑOLA<br />
DE CAMARAS DEL LIBRO<br />
</font>
				<font face="verdana" size="+0">
CAMBRA DEL LLIBRE DE CATALUNYA<br /></font>
				<font face="verdana" size="-1">
Mallorca. 274, 1r - Tel. 93 215 42 54 - 08037 Barcelona</font>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<font face="verdana" size="+1">INTRASTAT</font>
				<br />
				<br />
				<br />
				<br />
				<br />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="center" class="peque" rowspan="2">
				CODI IMPORTADOR/INTRODUCTOR<br/>
				<font face="verdana" size="+1">
					<?php echo $this->config->item('bp.camara.codigoimportador');?>
					</font>
			</td>
		</tr>
		<tr>
			<td align="center"  class="peque">
						A EMPLENAR PER LA CAMBRA DEL LLIBRE DE CATALUNYA
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table width="100%">
					<tr  class="item-row-no">
						<td>
							<font face="verdana" size="-1">Importador/Introductor:</font>
						</td>
						<td align="right" nowrap="nowrap">
							<font face="verdana" size="-1">Població:</font>
						</td>
						<td>
							<font face="verdana" size="-1">
								<strong>BARCELONA</strong>
							</font>
						</td>
						<td>
							<font face="verdana" size="-1">NIF: <strong><?php echo $this->config->item('company.vat');?></strong></font>
						</td>
					</tr>
					<tr class="item-row-no">
						<td colspan="4" valign="top">
							<font face="verdana" size="+0">
								<strong><?php echo trim(strtoupper($this->config->item('company.name')));?></strong>
							</font>
						</td>
					</tr>
					<tr  class="item-row-no">
						<td>
							<font face="verdana" size="-1">Domicili:<br/><strong>
								<?php echo ($this->config->item('company.address.1')!='')?$this->config->item('company.address.1') . ' ':'';?>
<?php echo ($this->config->item('company.address.2')!='')?$this->config->item('company.address.2') . ' ':'';?>
<?php echo ($this->config->item('company.address.3')!='')?$this->config->item('company.address.3') . ' ':'';?></strong></font>
						</td>
						<td align="right">
							<font face="verdana" size="-1">Telèfon:</font>
						</td>
						<td>
							<font face="verdana" size="-1"  nowrap="yes">
								<strong><?php echo str_replace(' ', '', $this->config->item('company.telephone'));?></strong>
							</font>
						</td>
						<td>
							<font face="verdana" size="-1">DNI:</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table width="100%">
					<tr class="item-row-no">
						<td>
							<font face="verdana" size="-1">Forma de tramesa</font>
						</td>
						<td>
							<font face="verdana" size="-1">
								<strong><?php echo $cFormaEnvio;?></strong>
							</font>
						</td>
						<td align="right">
							<font face="verdana" size="-1">Quilos nets:</font>
						</td>
						<td>
							<font face="verdana" size="-1">
								<strong>
									<?php echo format_number((int) ($nPeso/1000));?>
								</strong>
							</font>
						</td>
						<td>
							<font face="verdana" size="-1">Tipus de mercaderia:</font>
						</td>
						<td>
							<font face="verdana" size="-1">
								<strong><?php echo $cTipoMercancia; ?></strong>
							</font>
						</td>
					</tr>
					<tr class="item-row-no">
						<td>
							<font face="verdana" size="-1">País de procedència:</font>
						</td>
						<td colspan="3">
							<font face="verdana" size="-1">
								<strong><?php echo $cPais; ?></strong>
							</font>
						</td>
						<td>
							<font face="verdana" size="-1">Posició estadística:</font>
						</td>
						<td>
							<font face="verdana" size="-1">
								<strong><?php echo $this->config->item('bp.camara.posicionestadistica');?></strong>
							</font>
						</td>
					</tr>
					<tr class="item-row-no">
						<td>
							<font face="verdana" size="-1">País d'origen:</font>
						</td>
						<td colspan="3">
							<font face="verdana" size="-1">
								<strong><?php echo $cPais; ?></strong>							
							</font>
						</td>
						<td>
							<font face="verdana" size="-1">tramesa per:</font>
						</td>
						<td colspan="2">
							<font face="verdana" size="-1">
								<strong><?php echo $this->config->item('bp.camara.tramesa');?></strong>
							</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table>
					<tr class="item-row-no">
						<td class="item-row-no">
							<font face="verdana" size="-1" width="1%" nowrap="yes">Quantitat de divises:</font>
						</td>
						<td  class="item-row-no">
							<font face="verdana" size="-1">
								<strong>
									<?php echo format_price($fImporteCamara, FALSE);?>
								</strong>
							</font>
						</td>
					</tr>
					<tr class="item-row-no">
						<td class="item-row-no">
							<font face="verdana" size="-1" width="1%" nowrap="yes">Tipus de divisa:</font>
						</td>
						<td class="item-row-no">
							<font face="verdana" size="-1">
								<strong><?php echo strtoupper($divisa['cDescripcion']);?></strong>
							</font>
						</td>
					</tr>
				</table>
			</td>
			<td colspan="2" valign="top">
				<font face="verdana" size="-1">
Federación Española de Cámaras del Libro<br />
PD</font>
			</td>
		</tr>
	</table>
<div class="page-break"></div>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr class="items-th">
		<td width="50%" class="items-th">Proveïdor</td>
		<td width="1%" class="items-th">Data</td>
		<td width="1%" align="left" class="items-th">Factura</td>
		<td width="1%" align="left" class="items-th">Import</td>
		<td width="1%" align="left" class="items-th">Divisa</td>
		<td width="1%" align="left" class="items-th">Euros</td>
	</tr>
<?php $total = 0; ?>
<?php foreach ($albaranes as $albaran):?>
<tr class="item-row-short">
<td width="50%" class="item-row-short"><?php echo $albaran['cProveedor'];?></td>
<td width="1%" nowrap="yes" class="item-row-short"><?php echo format_date($albaran['dFecha']);?></td>
<td width="1%" nowrap="yes" align="left" class="item-row-short"><?php echo $albaran['cNumeroAlbaran'];?></td>
<td width="1%" nowrap="yes" align="right" class="item-row-short"><?php echo format_price($albaran['fImporteCamara'], FALSE);?></td>
<td width="1%" nowrap="yes" align="left" class="item-row-short"><?php echo $albaran['cSimbolo'];?></td>
<td width="1%" align="right" nowrap="yes" class="item-row-short"><?php echo format_price($albaran['fImporteCamara'] * $albaran['fCambioCamara']);?></td>
</tr>
<?php $total += format_decimals($albaran['fImporteCamara'] * $albaran['fCambioCamara']); ?>
<?php endforeach; ?>
	<tr class="items-th">
		<td colspan="6" class="items-th" align="right"><strong><?php echo format_price($total);?></strong></td>
	</tr>
</table>
</body>
</html>
