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

if (! $user->admin) accessforbidden();

$action = GETPOST('action');

if ($action == 'new')
{
	$sql	= 
		"SELECT 
			* 
		FROM 
			`llx_c_paiement` 
		WHERE 
			active = 1";
					
	$sql_estados = $db->query($sql);
	
	$num_estados = $db->num_rows($sql_estados);
	
	
	$sql	= 
		"SELECT 
			payment 
		FROM 
			`ps_orders` 
		GROUP BY 
			payment 
		ORDER BY 
			`payment`";
					
	$sql_payment = $db->query($sql);
	
	$num_payment = $db->num_rows($sql_payment);
	
}
else
if ($action == 'add')
{
	$registro = array(
		'ps_order_payment'	=> GETPOST('ps_order_payment'),
		'llx_c_paiement_id'	=> GETPOST('llx_c_paiement_id')
	);
	
	$sql	= 
		"INSERT INTO `tms_payment` (
			`ps_order_payment`,
			`llx_c_paiement_id`
		)VALUES(
			'$registro[ps_order_payment]',
			$registro[llx_c_paiement_id]
		);";	
					
	$db->query($sql);
	
	setEventMessage($langs->trans("SetupSaved"));
}
else	
if ($action == 'setvalue')
{
	$registro = array(
		'ps_order_payment'	=> GETPOST('ps_order_payment'),
		'llx_c_paiement_id'	=> GETPOST('llx_c_paiement_id'),
		'id_payment'		=> GETPOST('id_payment')
	);
		
	$sql = 
	"UPDATE `tms_payment` 
		SET 
			`ps_order_payment`	= '$registro[ps_order_payment]',
			`llx_c_paiement_id`	= $registro[llx_c_paiement_id]
		WHERE 
			`id_payment`		= $registro[id_payment]";
	
	$db->query($sql);
		
	setEventMessage($langs->trans("SetupSaved"));
}


if(isset($_GET['delete']))
{
	$sql	= 
		"DELETE FROM 
			`tms_payment`
		WHERE
			`id_payment` = $_GET[delete]";
					
	$db->query($sql);	
	
	setEventMessage($langs->trans("SetupDelete"));		
}


if(isset($_GET['id']))
{
	$sql = 
		"SELECT 
			id_payment,
			ps_order_payment,
    		llx_c_paiement_id,
    		code
		FROM 
			`tms_payment`
		INNER JOIN
 			llx_c_paiement ON(llx_c_paiement.id = tms_payment.llx_c_paiement_id)
 		WHERE
			`id_payment` = $_GET[id]";
					
	$resql	= $db->query($sql);	
		
	$numr	= $db->num_rows($resql);	
	
	$sql = 
		"SELECT 
			* 
		FROM 
			`llx_c_paiement` 
		WHERE 
			active = 1";
					
	$sql_estados = $db->query($sql);
	
	$num_estados = $db->num_rows($sql_estados);
	
	
	$sql = 
		"SELECT 
			payment 
		FROM 
			`ps_orders` 
		GROUP BY 
			payment 
		ORDER BY 
			`payment`";
					
	$sql_payment = $db->query($sql);
	
	$num_payment = $db->num_rows($sql_payment);
	
	$i		= 0;		
}
else
{
	$sql = 
		"SELECT 
			id_payment,
			ps_order_payment,
    		llx_c_paiement_id,
    		code
		FROM 
			`tms_payment`
		INNER JOIN
 			llx_c_paiement ON(llx_c_paiement.id = tms_payment.llx_c_paiement_id)";
			
	$resql	= $db->query($sql);	
		
	$numr	= $db->num_rows($resql);					
	$i		= 0;	
}




/*
 *	View
 */

$form = new Form($db);

llxHeader('',$langs->trans("SincronizarSetup"));


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup").' de Sincronización',$linkback);
print '<br>';

$head = paypaladmin_prepare_head();

dol_fiche_head($head, 'pagos', 'Sincronización', 0, 'sincronizar');

if ($action == 'new')
{
	print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
		
	print '<table class="noborder" width="100%">';
		
	$var=true;
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("SincronizarParametros").'</td>';
	print '<td>'.$langs->trans("Value").'</td>';
	print "</tr>\n";
				
	$var=!$var;
	print '<tr '.$bc[$var].'><td>';
	print '<select name="ps_order_payment" required>';
	print '<option value=""></option>';
		
	if($num_payment > 0)
	{	
		while ($i < $num_payment)
		{
			$payments = $db->fetch_object($sql_payment);
	
			print '<option value="'.$payments->payment.'">'.$payments->payment.'</option>';
			
			$i++;
		}
	}	
		
	print '</select></td><td>';
	
	print '<select name="llx_c_paiement_id" required>';
	print '<option value=""></option>';
	
	$i = 0;
		
	if($num_estados > 0)
	{	
		while ($i < $num_estados)
		{
			$estados = $db->fetch_object($sql_estados);
	
			print '<option value="'.$estados->id.'">'.$estados->code.'</option>';
			
			$i++;
		}
	}	
		
	print '</select>';
	print '</td></tr>';
	print '</table>';
		
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
		
	print '</form>';		
}
else
if(isset($_GET['id']))
{
	if($numr > 0)
	{
		$registro = $db->fetch_array($resql);	
		
		print '<br>';
		print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="action" value="setvalue">';
		
		print '<table class="noborder" width="100%">';
		
		$var=true;
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("SincronizarID").'</td>';
		print '<td>'.$langs->trans("SincronizarParametros").'</td>';
		print '<td>'.$langs->trans("Value").'</td>';
		print "</tr>\n";
				
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print ''.$registro['id_payment'].'</td><td>';
		
		print '<select name="ps_order_payment" required>';
		print '<option value=""></option>';
		if($num_payment > 0)
		{	
			while ($i < $num_payment)
			{
				$payments = $db->fetch_object($sql_payment);
				
				if($registro['ps_order_payment'] == $payments->payment)
				{
					$select = 'selected';
				}
				else 
				{
					$select = '';
				}
		
				print '<option value="'.$payments->payment.'" '.$select.'>'.$payments->payment.'</option>';
				
				$i++;
			}
		}	
		print '</select></td><td>';
		
		print '<select name="llx_c_paiement_id" required>';
		$i = 0;
		if($num_estados > 0)
		{	
			while ($i < $num_estados)
			{
				$estados = $db->fetch_object($sql_estados);
				
				if($registro['llx_c_paiement_id'] == $estados->id)
				{
					$select = 'selected';
				}
				else 
				{
					$select = '';
				}
				
				print '<option value="'.$estados->id.'" '.$select.'>'.$estados->code.'</option>';
				
				$i++;
			}
		}	
		print '</select>';
		print '</td></tr>';
		print '</table>';
		
		print '<input name="id_payment" value="'.$_GET['id'].'" type="hidden">';
		
		print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';
		
		print '</form>';	
		
	}
	
}
else
{	
	print $langs->trans("SincronizarModoPago")."<br>\n";
	
	print '<br>';
	print '<table class="noborder" width="100%">';
	
	$var=true;
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("SincronizarID").'</a></td>';
	print '<td>'.$langs->trans("SincronizarModulo").'</td>';
	print '<td>'.$langs->trans("SincronizarCodigo").'</td>';
	print '<td>'.$langs->trans("SincronizarOpciones").'</td>';
	print "</tr>\n";
	
	if($numr > 0)
	{
		$cadena = "'".$langs->trans("SincronizarEliminarConfirmar")."'";
			
		while ($i < $numr)
		{
			$registro = $db->fetch_object($resql);
			
			$var=!$var;
			print '<tr '.$bc[$var].'><td>';
			print $registro->id_payment.'</td><td>';
			print '<a href="'.DOL_URL_ROOT.'/sincronizar/admin/pagos.php?id='.$registro->id_payment.'">';
			print $registro->ps_order_payment.'</a></td><td>';
			print $registro->code.'</td><td>';
			print '<a href="'.DOL_URL_ROOT.'/sincronizar/admin/pagos.php?id='.$registro->id_payment.'">';
			print '<span class="label label-primary">';
			print $langs->trans("SincronizarModificar").'</span></a> ';
			print '<a href="'.DOL_URL_ROOT.'/sincronizar/admin/pagos.php?delete='.$registro->id_payment.'" onclick="return confirm('.$cadena.')">';
			print '<span class="label label-danger">';
			print $langs->trans("SincronizarEliminar").'</span></a></td>';
			print '</tr>';
			
			$i++;	
		}
	}
	
	print '</table>';
	print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="new">';
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("New").'"></center>';
	print '</form>';
}

llxFooter();
$db->close();
