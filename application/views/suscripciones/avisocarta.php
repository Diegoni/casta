<?php $titlelen = isset($titlelen)?$titlelen:100;?>
<?php $clientelen = isset($clientelen)?$clientelen:40;?>
<div id="page-wrap">
<div id="header">Avís de renovació / Aviso de Renovación</div>
<div id="identity">
<div id="logo"><img
	src="<?php echo image_asset_url($this->config->item('company.logo.print'));?>" /></div>

<div id="address"><?php echo $this->config->item('company.name');?><br />
<?php echo ($this->config->item('company.address.1')!='')?$this->config->item('company.address.1') . '<br/>':'';?>
<?php echo ($this->config->item('company.address.2')!='')?$this->config->item('company.address.2') . '<br/>':'';?>
<?php echo ($this->config->item('company.address.3')!='')?$this->config->item('company.address.3') . '<br/>':'';?>
<?php echo $this->lang->line('NIF');?>: <?php echo $this->config->item('company.vat');?>
</div>
</div>
<div style="clear: both"></div>
<div id="customer">
<div id="customer-title"><?php echo wordwrap(format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']), $clientelen, '<br/>');?><br />
<?php echo format_address_print($cliente['direccion']);?><br />
<br />
<br />
</div>
</div>
<p>Benvolgut/da: <strong><?php echo format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']);?></strong>
<br />
<br />
Li preguem que ens confirmi si desitja renovar les següents
subscripcions, properes al seu venciment, abans de l'1 de desembre. Un
cop superada aquesta data ALIBRI no es pot fer responsable dels
possibles retards en la recepció dels números.<br />
<br />
Tingui en compte que els editors no accepten reclamacions d'exemplars
transcorreguts tres mesos des de la seva publicació.<br />
<br />
Podeu realitzar les renovacions mitjançant le següent pàgina web:<br />
<br />
<a href="http://www.alibri.es/renovacion">http://www.alibri.es/renovacion</a> <br />
<br />
Li solicitarem el següent codi de client i referència. No oblidi fer
menció del seu nom i cognoms.<br />
<br />
<ul>
	<li>Client: <strong><?php echo $cliente['nIdCliente'];?></strong></li>
	<li>Refèrencia: <strong><?php echo $cliente['cRandom'];?></strong></li>
</ul>
<br />
<br />
També poden confirmar la seva subscripció a través de les següents vies:
<br />
<br />

<ul>
	<li>e-mail: <strong>suscripciones@alibri.es</strong></li>
	<li>Fax: <strong>+34 93 412 27 02</strong></li>
	<li>Carta: <strong>Balmes 26, ES-08007 BARCELONA - SPAIN</strong></li>
</ul>
<br />
<br />
</p>
<hr />
<p>Estimado/a: <strong><?php echo format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']);?></strong>
<br />
<br />
Le rogamos nos confirme si desea renovar las siguientes suscripciones,
cercanas a su vencimiento, antes del día 1 de diciembre. Una vez
superada esta fecha ALIBRI no se hace responsable de los posibles
retrasos en la recepción de los números.<br />
<br />
Tenga en cuenta que los editores no aceptan reclamaciones de ejemplares
pasados tres meses de su publicación. <br />
<br />
Puede realizar sus renovaciones en la siguiente página web:<br />
<br />
<a href="http://www.alibri.es/renovacion">http://www.alibri.es/renovacion</a> <br />
<br />
Utilice el siguiente código de cliente y referencia que les será
solicitado. No olvide hacer mención de su nombre y apellido:<br />
<br />


<ul>
	<li>Cliente: <strong><?php echo $cliente['nIdCliente'];?></strong></li>
	<li>Referencia: <strong><?php echo $cliente['cRandom'];?></strong></li>
</ul>
<br />
<br />
También puede confirmar su suscripción por las siguientes formas: <br />
<br />

<ul>
	<li>e-mail: <strong>suscripciones@alibri.es</strong></li>
	<li>Fax: <strong>+34 93 412 27 02</strong></li>
	<li>Carta: <strong>Balmes 26, ES-08007 BARCELONA - SPAIN</strong></li>
</ul>
<br />
<br />
Els preus reflexats corresponen a l'últim periode facturat i poden patir
modificacions.<br />
<br />

<br />
<table class="items">
	<tr>
		<th class="items-th">Subscripció / Suscripción</th>
		<th class="items-th">Revista</th>
		<th class="items-th">D. Renova. / F. Renova.</th>
		<th class="items-th">Quantitat / Cantidad</th>
	</tr>
	<?php foreach($suscripciones as $linea):?>
	<?php $pvp = $linea['suscripcion']['fPrecio'];?>
	<?php $iva = $linea['revista']['fIVA'];?>
	<?php $importes = format_calculate_importes(array(
		'fPrecio' 		=> format_quitar_iva($pvp, $iva),
		'nCantidad' 	=> 1, 
		'fRecargo'		=> 0, 
		'fIVA' 			=> $iva, 
		'fDescuento' 	=> 0
	));
	?>
	<tr class="item-row">
		<td class="item-ct"><?php echo $linea['suscripcion']['nIdSuscripcion'];?></td>
		<td class="item-name"><?php echo format_title($linea['revista']['cTitulo'], $titlelen);?>
		<?php if (isset($linea['direnv'])):?> <br />
		<br />
		<div class="small">
		<strong>Adreça d'enviament/Dirección de envío:</strong><br />
		<?php echo format_address_print($linea['direnv']);?>
		</div>
		<?php endif; ?></td>
		<td class="item-pvp"><?php echo format_date($linea['suscripcion']['dRenovacion']);?></td>
		<td align="right" class="item-dto">1</td>
	</tr>
	<?php endforeach;?>
</table>
<div style="clear: both"></div>

<div id="footer">
<p>Moltes gràcies. Salutacions cordials,<br />
<br />
Muchas gracias. Saludos Cordiales,<br />
<br />

<strong>Departament de Subscripcions</strong><br />
ALIBRI Llibreria, S.L</p>
<div style="clear: both"></div>
<div id="terms">
<h5><?php echo $this->lang->line('Contacto');?></h5>
<div class="center"><?php echo $this->lang->line('Tel.');?>: <?php echo $this->config->item('company.telephone');?>&nbsp;/&nbsp;
<?php echo $this->lang->line('Fax.');?>: <?php echo $this->config->item('company.fax');?>
&nbsp;/&nbsp;<?php echo $this->lang->line('eMail');?>: <?php echo $this->config->item('company.email');?>
&nbsp;/&nbsp;<?php echo $this->lang->line('Web');?>: <?php echo $this->config->item('company.url');?><br />
</div>
</div>
</div>

</div>
<div class="page-break"></div>
