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

$sql		= "SELECT * FROM `tms_config_sincronizacion`";
$resql		= $db->query($sql);	
$config		= $db->fetch_array($resql);	

$sql		= "SELECT * FROM `tms_log_pedidos` ORDER BY id_log DESC LIMIT 0, $config[cantidad]";
$resql		= $db->query($sql);	
	
$numr		= $db->num_rows($resql);					
$i			= 0;


/*
 *	View
 */

$form = new Form($db);

llxHeader('',$langs->trans("SincronizarSetup"));


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup").' de Sincronización',$linkback);
print '<br>';

$head = paypaladmin_prepare_head();

dol_fiche_head($head, 'pedidos', 'Sincronización', 0, 'sincronizar');
print $langs->trans("SincronizarUltimos").' '.$config['cantidad'].' '.$langs->trans("SincronizarRegistros")."<br>\n";

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
		$pedidos = $db->fetch_object($resql);
		
		if($pedidos->system == 'dolibar')
		{
			$label = 'primary';				
		}
		else
		{
			$label = 'info';	
		}
	
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print '<a href="'.DOL_URL_ROOT.'/commande/list.php?viewstatut=&search_sale=&search_user=-1&search_ref='.$pedidos->reference.'&search_ref_customer=&search_company=&ordermonth=&orderyear=&deliverymonth=&deliveryyear=&button_search.x=0&button_search.y=0&button_search=Buscar" title="'.$langs->trans("SincronizarDetalle").'">';
		print '<img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_order.png" border="0" alt=""> ';
		print $pedidos->reference.'</a></td><td>';
		print $pedidos->total_ttc.'</td><td>';
		print $pedidos->payment.'</td><td>';
		print '<span class="label label-'.$label.'">';
		print $langs->trans('Sincronizar'.$pedidos->system).'</label></td><td>';
		print $langs->trans('Sincronizar'.$pedidos->action).'</td><td>';
		print date('d-m-Y', strtotime($pedidos->date_upd)).'</td><td>';
		print$langs->trans('SincronizarEstado'. $pedidos->id_estado);
		print '</td></tr>';
		
		$i++;	
	}
}

print '</table>';
print '</form>';

llxFooter();
$db->close();
