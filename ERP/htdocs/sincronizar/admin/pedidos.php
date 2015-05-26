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
	if(GETPOST('SincronizarAutomatica') == 'yes')
	{
		$automatica = 1;
	}
	else
	{
		$automatica = 0;
	}
	
	$sql = 
	"UPDATE `tms_config_sincronizacion` 
		SET 
			`automatica`	= $automatica
		WHERE 
			`id_config`		= 1";
	
	$db->query($sql);
	
	setEventMessage($langs->trans("SetupSaved"));
}

$sql	= "SELECT * FROM `tms_config_sincronizacion`";
$resql	= $db->query($sql);	
	
$numr	= $db->num_rows($resql);					
		
if($numr > 0)
{
	$registros = $db->fetch_array($resql);	
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
dol_fiche_head($head, 'pedidos', 'Sincronización', 0, 'sincronizar');

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

print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

llxFooter();
$db->close();
