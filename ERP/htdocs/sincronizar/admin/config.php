<?php
/* Copyright (C) 2004		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2005-2013	Laurent Destailleur		<eldy@users.sourceforge.org>
 * Copyright (C) 2011-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2011-2012  Juanjo Menent			<jmenent@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file       htdocs/paypal/admin/paypal.php
 * \ingroup    paypal
 * \brief      Page to setup paypal module
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/lib/sincronizar.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

$servicename='Sincronizar';

$langs->load("sincronizar");
$langs->load("bills");
$langs->load("dict");

if (! $user->admin) accessforbidden();

/*----------------------------------------------------------------------------
		UPDATE de la tabla tms_config_sincronizacion
----------------------------------------------------------------------------*/

$action = GETPOST('action');

if ($action == 'setvalue' && $user->admin)
{
	if(GETPOST('SincronizarAutomatica') == 'yes')
	{
		$registro['automatica'] = 1;
	}
	else
	{
		$registro['automatica'] = 0;
	}
	
	if(GETPOST('cantidad') < 1 )
	{
		$registro['cantidad'] = 1;
	}
	else
	{
		$registro['cantidad'] = GETPOST('cantidad');
	}
	
	
	$sql = 
	"UPDATE `tms_config_sincronizacion` 
		SET 
			`automatica`	= $registro[automatica],
			`cantidad`		= $registro[cantidad]
		WHERE 
			`id_config`		= 1";
	
	$db->query($sql);
	
	setEventMessage($langs->trans("SetupSaved"));
}


/*----------------------------------------------------------------------------
		SELECT de los origenes del pedido
----------------------------------------------------------------------------*/

$sql	= "SELECT * FROM `llx_c_input_reason` WHERE active = 1";

$origen_pedido	= $db->query($sql);	
	
$num_origen_pedido	= $db->num_rows($origen_pedido);					


/*----------------------------------------------------------------------------
		SELECT de las condiciones de pago
----------------------------------------------------------------------------*/

$sql	= "SELECT * FROM `llx_c_payment_term` WHERE active = 1";

$condicion_pago	= $db->query($sql);	
	
$num_condicion_pago	= $db->num_rows($condicion_pago);					


/*----------------------------------------------------------------------------
		SELECT de tms_config_sincronizacion
----------------------------------------------------------------------------*/

$sql	= "SELECT * FROM `tms_config_sincronizacion`";
$resql	= $db->query($sql);	
	
$numr	= $db->num_rows($resql);					
		
if($numr > 0)
{
	$registros = $db->fetch_array($resql);	
}


/*----------------------------------------------------------------------------
------------------------------------------------------------------------------
		VISTA
------------------------------------------------------------------------------
----------------------------------------------------------------------------*/


$form = new Form($db);

llxHeader('',$langs->trans("SincronizarSetup"));


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup").' de Sincronización',$linkback);
print '<br>';

$head = paypaladmin_prepare_head();

dol_fiche_head($head, 'config', 'Sincronización', 0, 'sincronizar');

print $langs->trans("SincronizarConfigDesc")."<br>\n";

print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	
	print '<input type="hidden" name="action" value="setvalue">';
	
	print '<table class="noborder" width="100%">';
	
		$var=true;
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("SincronizarParametros").'</td>';
		print '<td>'.$langs->trans("Value").'</td>';
		print "</tr>\n";
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarAutomatica").'</td><td>';
		print $form->selectyesno("SincronizarAutomatica", $registros['automatica']);
		print '</td></tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarCantidad").'</td><td>';
		print '<input size="64" type="number" name="cantidad" value="'.$registros['cantidad'].'">';
		print '</td></tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarCondicionPago").'</td><td>';
		print '<select name="id_llx_c_payment_term">';
		print '<option value="0"></option>';
		
		$c = 0;
		
		if($num_condicion_pago > 0)
		{	
			while ($c < $num_condicion_pago)
			{
				$condicion = $db->fetch_object($condicion_pago);
				
				if($registros['id_llx_c_payment_term'] == $condicion->rowid)
				{
					$select = 'selected';
				}
				else
				{
					$select = '';
				}	
				
				print '<option value="'.$condicion->rowid.'" '.$select.'>'.$langs->trans("PaymentConditionShort".$condicion->code).'</option>';
				
				$c++; 
			}
		}	
		
		print '</select>';
		print '</td></tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarOrigenPedido").'</td><td>';
		print '<select name="id_llx_c_input_reason">';
		print '<option value="0"></option>';
		
		$c = 0;
		
		if($num_origen_pedido > 0)
		{	
			while ($c < $num_origen_pedido)
			{
				$origen = $db->fetch_object($origen_pedido);
				
				if($registros['id_llx_c_input_reason'] == $origen->rowid)
				{
					$select = 'selected';
				}
				else
				{
					$select = '';
				}	
				
				print '<option value="'.$origen->rowid.'" '.$select.'>'.$langs->trans("DemandReasonType".$origen->code).'</option>';
				
				$c++; 
			}
		}	
		
		print '</select>';
		print '</td></tr>';

	print '</table>';

	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

llxFooter();
$db->close();
