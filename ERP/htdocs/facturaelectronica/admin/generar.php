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
require_once DOL_DOCUMENT_ROOT.'/rece/lib/rece.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

$servicename = 'Rece';

$langs->load("rece");
$langs->load("bills");
$langs->load("dict");

if (! $user->admin) accessforbidden();


/*----------------------------------------------------------------------------
------------------------------------------------------------------------------
		VISTA
------------------------------------------------------------------------------
----------------------------------------------------------------------------*/


$form = new Form($db);

llxHeader('',$langs->trans("ReceSetup"));


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup").' de Rece',$linkback);
print '<br>';

$head = paypaladmin_prepare_head();

dol_fiche_head($head, 'generar', 'Rece', 0, 'rece');

print $langs->trans("ReceConfigDesc")."<br>\n";

print '<br>';
print '<form method="post" action="https://wswhomo.afip.gov.ar/wsfev1/service.asmx">';

	print '<input type="hidden" name="action" value="setvalue">';
	
	print '<table class="noborder" width="100%">';
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("ReceCampo").'</td>';
	print '<td>'.$langs->trans("ReceValor").'</td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td><input name="Auth" value=""></td>';
	
	print '</table>';
	
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("PrecioConfirmar").'"></center>';

print '</form>';

llxFooter();
$db->close();
