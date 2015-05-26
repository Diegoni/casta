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

if ($action == 'setvalue' && $user->admin)
{

}

$sql	= "SELECT * FROM `tms_log_clientes` ORDER BY id_log DESC LIMIT 0, 10";
$resql	= $db->query($sql);	
	
$numr	= $db->num_rows($resql);					
$i		= 0;


/*
 *	View
 */

$form = new Form($db);

llxHeader('',$langs->trans("SincronizarSetup"));


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup").' de Sincronización',$linkback);
print '<br>';

$head = paypaladmin_prepare_head();

dol_fiche_head($head, 'clientes', 'Sincronización', 0, 'sincronizar');
print $langs->trans("SincronizarUltimos")."<br>\n";

print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="action" value="setvalue">';

print '<table class="noborder" width="100%">';

$var=true;
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("SincronizarNombre").'</a></td>';
print '<td>'.$langs->trans("SincronizarEmail").'</td>';
print '<td>'.$langs->trans("SincronizarPhone").'</td>';
print '<td>'.$langs->trans("SincronizarSystem").'</td>';
print '<td>'.$langs->trans("SincronizarAction").'</td>';
print '<td>'.$langs->trans("SincronizarFecha").'</td>';
print '<td>'.$langs->trans("SincronizarEstado").'</td>';
print "</tr>\n";

if($numr > 0)
{	
	while ($i < $numr)
	{
		$clientes = $db->fetch_object($resql);
		
		if($clientes->system == 'dolibar')
		{
			$id_cliente = $clientes->id_row;
		}
		else
		{
			$sql_sin	= "SELECT * FROM `tms_clientes_sin` WHERE `id_ps_customer` = $clientes->id_row";
			
			$resql_sin	= $db->query($sql_sin);	
				
			$numr_sin	= $db->num_rows($resql_sin);	
			
			if($numr > 0)
			{
				$array_sin = $db->fetch_array($resql_sin);
				
				$id_cliente = $array_sin['id_llx_societe'];
			}	
		}
	
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print '<a href="'.DOL_URL_ROOT.'/societe/soc.php?socid='.$id_cliente.'" title="'.$langs->trans("SincronizarDetalle").'">';
		print $clientes->nombre.'</a></td><td>';
		print $clientes->email.'</td><td>';
		print $clientes->phone.'</td><td>';
		print $langs->trans('Sincronizar'.$clientes->system).'</td><td>';
		print $langs->trans('Sincronizar'.$clientes->action).'</td><td>';
		print date('d-m-Y', strtotime($clientes->date_upd)).'</td><td>';
		print$langs->trans('SincronizarEstado'. $clientes->id_estado);
		print '</td></tr>';
		
		$i++;	
	}
}

/*
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("CSSUrlForPaymentForm").'</td><td>';
print '<input size="64" type="text" name="PAYPAL_CSS_URL" value="">';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("MessageOK").'</td><td>';
$doleditor=new DolEditor('PAYPAL_MESSAGE_OK',$conf->global->PAYPAL_MESSAGE_OK,'',100,'dolibarr_details','In',false,true,true,ROWS_4,60);
$doleditor->Create();
print '</td></tr>';
*/
print '</table>';
print '</form>';

llxFooter();
$db->close();
