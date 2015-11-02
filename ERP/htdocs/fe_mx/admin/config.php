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
require_once DOL_DOCUMENT_ROOT.'/fe_mx/lib/fe_mx.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

$servicename = 'Factura Electronica';

$langs->load("facturaelectronica");
$langs->load("bills");
$langs->load("dict");

if (! $user->admin) accessforbidden();

/*----------------------------------------------------------------------------
		Guardamos la configuración
----------------------------------------------------------------------------*/

$action = GETPOST('action');

if ($action == 'setvalue' && $user->admin){
	$registro = array(
		'ambiente'			=> GETPOST('ambiente'),
		'cuil'				=> GETPOST('cuil'),
		'certificado'		=> GETPOST('certificado'),
		'clave_privada'		=> GETPOST('clave_privada'),
		'path_certificado'	=> GETPOST('path_certificado'),
		'homo_web_ser'		=> GETPOST('homo_web_ser'),
		'homo_wsfe_v1'		=> GETPOST('homo_wsfe_v1'),
		'prod_web_ser'		=> GETPOST('prod_web_ser'),
		'prod_wsfe_v1'		=> GETPOST('prod_wsfe_v1'),
		'request'			=> GETPOST('request'),
		'response'			=> GETPOST('response'),
		'path_request'		=> GETPOST('path_request'),
	);
	
	if(is_dir($registro['path_certificado']) && is_dir($registro['path_request']) ){
		$registro['path_certificado'] = str_replace('\\', '-', $registro['path_certificado']);
		$registro['path_request'] = str_replace('\\', '-', $registro['path_request']);
		
		$sql = 
			"UPDATE `tms_config_factura_electronica` 
				SET 
					`ambiente`			= '$registro[ambiente]',
					`cuil`				= '$registro[cuil]',
					`certificado`		= '$registro[certificado]',
					`clave_privada`		= '$registro[clave_privada]',
					`path_certificado`	= '$registro[path_certificado]',
					`homo_web_ser`		= '$registro[homo_web_ser]',
					`homo_wsfe_v1`		= '$registro[homo_wsfe_v1]',
					`prod_web_ser`		= '$registro[prod_web_ser]',
					`prod_wsfe_v1`		= '$registro[prod_wsfe_v1]',
					`request`			= '$registro[request]',
					`response`			= '$registro[response]',
					`path_request`		= '$registro[path_request]'
				WHERE 
					`id_config`		= 1";
		
		$db->query($sql);
			
		setEventMessage($langs->trans("SetupSaved"));
	} else {
		setEventMessage("No existe el directorio de la carpeta", 'errors');
	}
}

/*----------------------------------------------------------------------------
		SELECT de los origenes del pedido
----------------------------------------------------------------------------*/

$sql	= 
"SELECT 
	* 
FROM 
	`tms_config_factura_electronica`";

$fe_query = $db->query($sql);	
$num_fe	= $db->num_rows($fe_query);

if($num_fe > 0){
	$fe_array = $db->fetch_array($fe_query);
}			


/*----------------------------------------------------------------------------
------------------------------------------------------------------------------
		VISTA
------------------------------------------------------------------------------
----------------------------------------------------------------------------*/


$form = new Form($db);

llxHeader('',$langs->trans("FacturaElectronicaSetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup").' de Factura electrónica',$linkback);
print '<br>';

$head = paypaladmin_prepare_head();
dol_fiche_head($head, 'config', 'Factura Electrónica', 0, 'facturaelectronica');

print $langs->trans("FEconfigDesc")."<br>\n";

print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="setvalue">';	
	print '<table class="noborder" width="100%">';
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("FEdato").'</td>';
	print '<td>'.$langs->trans("FEvalor").'</td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEambiente").'</td>';
	print '<td><input name="ambiente" value="'.$fe_array['ambiente'].'" size="80" required></td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEcuil").'</td>';
	print '<td><input name="cuil" value="'.$fe_array['cuil'].'" size="80" required></td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEcertificado").'</td>';
	print '<td><input name="certificado" value="'.$fe_array['certificado'].'" size="80" required></td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEclave_privada").'</td>';
	print '<td><input name="clave_privada" value="'.$fe_array['clave_privada'].'" size="80" required></td>';
	print '</tr>';
	
	$fe_array['path_certificado'] = str_replace('-', '\\', $fe_array['path_certificado']);
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEpath_certificado").'</td>';
	print '<td><input name="path_certificado" value="'.$fe_array['path_certificado'].'" size="80" required></td>';
	print '</tr>';
		
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEhomo_web_ser").'</td>';
	print '<td><input name="homo_web_ser" value="'.$fe_array['homo_web_ser'].'" size="80" required></td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEhomo_wsfe_v1").'</td>';
	print '<td><input name="homo_wsfe_v1" value="'.$fe_array['homo_wsfe_v1'].'" size="80" required></td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEprod_web_ser").'</td>';
	print '<td><input name="prod_web_ser" value="'.$fe_array['prod_web_ser'].'" size="80" required></td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEprod_wsfe_v1").'</td>';
	print '<td><input name="prod_wsfe_v1" value="'.$fe_array['prod_wsfe_v1'].'" size="80" required></td>';
	print '</tr>';
		
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FErequest").'</td>';
	print '<td><input name="request" value="'.$fe_array['request'].'" size="80" required></td>';
	print '</tr>';	
		
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEresponse").'</td>';
	print '<td><input name="response" value="'.$fe_array['response'].'" size="80" required></td>';
	print '</tr>';
	
	$fe_array['path_request'] = str_replace('-', '\\', $fe_array['path_request']);
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("FEpath_request").'</td>';
	print '<td><input name="path_request" value="'.$fe_array['path_request'].'" size="80" required></td>';
	print '</tr>';
			
	print '</table>';
		
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

llxFooter();
$db->close();