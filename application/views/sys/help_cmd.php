<table>
	<thead>
		<tr>
			<th colspan="2"><?php echo $this->lang->line('Comandos');?></th>
		</tr>
	</thead>
	<tr>
		<td>help</td>
		<td>Muestra esta ayuda</td>
	</tr>
	<tr class="alt">
		<td>do [acción]</td>
		<td>Ejecuta el controlador [acción] directamente</td>
	</tr>
	<tr>
		<td>do app app cfg</td>
		<td>Configuración PHP/Apache del servidor de aplicaciones</td>
	</tr>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Catálogo');?></th>
	</tr>
	<tr>
		<td>art [id]</td>
		<td>Muestra los datos del artículo con código [id]</td>
	</tr>
	<tr class="alt">
		<td>buscar</td>
		<td>Abre la ventana de búsqueda de artículos</td>
	</tr>
	<tr>
		<td>docs [id] [desde] [hasta]</td>
		<td>Muestra los documentos del artículo [id] en el periodo [desde] y
		[hasta]</td>
	</tr>
	<tr class="alt">
		<td>isbn [código]</td>
		<td>Convierte el código a EAN, ISBN10 e ISBN13</td>
	</tr>

	<tr>
		<th colspan="2"><?php echo $this->lang->line('Compras');?></th>
	</tr>
	<tr>
		<td>pv [id/texto]</td>
		<td>Abrir el proveedor [id] o búsca el [texto]</td>
	</tr>
	<tr class="alt">
		<td>pp [id/texto] ó pedprv [id/texto]</td>
		<td>Abrir la Pedido de proveedir con el número [id] o búsca el [texto]</td>
	</tr>
	<tr>
		<td>repo</td>
		<td>Abre la ventana de reposición</td>
	</tr>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Ventas');?></th>
	</tr>
	<tr>
		<td>cl [id/texto]</td>
		<td>Abrir el cliente [id] o búsca el [texto]</td>
	</tr>
	<tr class="alt">
		<td>pc [id/texto] ó ped [id/texto]</td>
		<td>Abrir la Pedido de cliente con el número [id] o búsca el [texto]</td>
	</tr>
	<tr>
		<td>as [id/texto] ó alb [id/texto]</td>
		<td>Abrir la ventana de Albarán de Salida con al albarán [id] o búsca
		el [texto]</td>
	</tr>
	<tr class="alt">
		<td>tpv [id/texto]</td>
		<td>Abrir la ventana de TPV con la factura [id] o búsca el [texto]</td>
	</tr>
	<tr>
		<td>facturacion [id/texto]</td>
		<td>Abrir la ventana de facturación con la factura [id] o búsca el
		[texto]</td>
	</tr>
	<tr class="alt">
		<td>abono [id]</td>
		<td>Muestra el impreso del abono número [id]</td>
	</tr>
	<tr>
		<td>tarifas</td>
		<td>Cálculo de tarifas de venta</td>
	</tr>
	<tr class="alt">
		<td>cambio</td>
		<td>Cálculo de cambios de divisa</td>
	</tr>
	<tr>
		<td>envios</td>
		<td>Cálculo de tarifas de envío de paquetes</td>
	</tr>
	<tr class="alt">
		<td>openbox|ob</td>
		<td>Abre el cajón portamonedas</td>
	</tr>

	<tr>
		<th colspan="2"><?php echo $this->lang->line('Comunicación');?></th>
	</tr>
	<tr>
		<td>ml [id/texto]</td>
		<td>Abrir el mailing [id] o búsca el [texto]</td>
	</tr>
	<tr class="alt">
		<td>bl [id/texto]</td>
		<td>Abrir el boletín [id] o búsca el [texto]</td>
	</tr>
	<tr>
		<td>ct [id/texto]</td>
		<td>Abrir el contacto [id] o búsca el [texto]</td>
	</tr>
	<tr class="alt">
		<td>sms [tel] "[texto]"</td>
		<td>Envía un SMS al teléfono [tel] con el texto [texto]</td>
	</tr>
	<tr>
		<td>msg [usuario] "[texto]"</td>
		<td>Envía un mensaje al usuario [usuario] con el texto [texto]</td>
	</tr>
	<tr class="alt">
		<td>mensajes</td>
		<td>Muestra los mensajes recibidos</td>
	</tr>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Calendario');?></th>
	</tr>
	<tr>
		<td>tr [id/texto]</td>
		<td>Abrir el trabajador [id] o búsca el [texto]</td>
	</tr>
	<tr class="alt">
		<td>tr dia [desde] [hasta]</td>
		<td>Muestra los turnos/trabajadores entre las fechas [desde] y [hasta]</td>
	</tr>
	<tr>
		<td>tr rsm [id] [año]</td>
		<td>Muestra el resumen de horas del trabajador [id] en el año [año]</td>
	</tr>
	<tr class="alt">
		<td>cal rsm [año]</td>
		<td>Muestra el resumen de horas de todos los trabajadores en el año
		[año]</td>
	</tr>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Sistema');?></th>
	</tr>
	<tr>
		<td>ver</td>
		<td>Muestra las versiones de las librerías utilizadas</td>
	</tr>
	<tr class="alt">
		<td>auth</td>
		<td>Muestra las autorizaciones del usuario</td>
	</tr>
	<tr class="alt">
		<td>authreload</td>
		<td>Recarga autorizaciones del usuario</td>
	</tr>
	<tr>
		<td>status</td>
		<td>Estado del servidor</td>
	</tr>
	<tr class="alt">
		<td>tareas</td>
		<td>Lista de tareas del sistema</td>
	</tr>
	<tr>
		<td>comandos</td>
		<td>Lista de comandos enviados por el sistema</td>
	</tr>
	<tr class="alt">
		<td>runcmd [id]</td>
		<td>Enviar el comando [id] al usuario que da la orden</td>
	</tr>
	<tr class="alt">
		<td>set [variable] [valor] [ámbito]</td>
		<td>Asigna el [valor] a la [variable] del sístema del [ámbito]:<br />
		<ul>
			<li><strong>user|[nada]</strong>: A la configuración del usario</li>
			<li><strong>terminal</strong>: A la configuración del terminal</li>
			<li><strong>system</strong>: A la configuración del sistema</li>
		</ul>
		</td>
	</tr>

</table>
