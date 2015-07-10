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
 * 
 * 

 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/facturaelectronica/lib/facturaelectronica.lib.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

$servicename = 'Factura Electrónica';

$langs->load("facturaelectronica");
$langs->load("bills");
$langs->load("dict");

if (! $user->admin) accessforbidden();

$sql	= 
	"SELECT 
		* 
	FROM 
		`tms_config_factura_electronica`";
	
$fe_query = $db->query($sql);	
		
$num_fe	= $db->num_rows($fe_query);
	
if($num_fe > 0)
{
	$fe_array = $db->fetch_array($fe_query);
}

$csr = str_replace(".key", '.csr', $fe_array['clave_privada']);


/*----------------------------------------------------------------------------
		Guardamos la configuración
----------------------------------------------------------------------------*/

$action = GETPOST('action');

if ($action == 'setvalue' && $user->admin)
{
	$command = 'openssl genrsa -out '.$fe_array['clave_privada'].' 1024';
	exec($command);
	//WSASS - Autogestión Certificados Homologación
	$command = 'openssl req -new -key '.$fe_array['clave_privada'].' -subj "/C=AR/O='.$fe_array['empresa'].'/CN=mi certificado 1/serialNumber=CUIT '.$fe_array['cuil'].'" -out '.$csr.'';
	exec($command);	
}


/*----------------------------------------------------------------------------
------------------------------------------------------------------------------
		VISTA
------------------------------------------------------------------------------
----------------------------------------------------------------------------*/


$form = new Form($db);

llxHeader('',$langs->trans("FEcertificado"));


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup").' de Factura electrónica',$linkback);
print '<br>';

$head = paypaladmin_prepare_head();

dol_fiche_head($head, 'generar', 'Factura electrónica', 0, 'facturaelectronica');

print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	
	print "<center>";
	print "<table>";
	print "<tr class='liste_titre'>";
	print "<td>".$langs->trans("FEcertificado")."</td>";
	print "<td>".$langs->trans("FEclave_privada")."</td>";
	print "</tr>";
	
	print "<tr>";
	if(is_file($csr))
	{
		$file = fopen($csr, "r") or exit($langs->trans("FEnoCSR"));
		
		print "<td><textarea cols='80' rows='16'>";
		while(!feof($file))
		{
			print fgets($file);
		}
		fclose($file);
		print "</textarea></td>";	
	}
	else
	{
		$langs->trans("FEnoCSR");
	}
	
	if(is_file($fe_array['clave_privada']))
	{
		$file = fopen($fe_array['clave_privada'], "r") or exit($langs->trans("FEnoClavePrivada"));
		
		print "<td><textarea cols='80' rows='16'>";
		while(!feof($file))
		{
			print fgets($file);
		}
		fclose($file);
		print "</textarea></td>";
	}
	else
	{
		$langs->trans("FEnoClavePrivada");
	}
	print "</tr>";
	print "</table>";
	print "</center>";
	
	print '<br><input type="hidden" name="action" value="setvalue">';
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("FEgenerar").'"></center>';

print '</form>';

llxFooter();
$db->close();
