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
		Generación del archivo
----------------------------------------------------------------------------*/

$action = GETPOST('action');

if ($action == 'setvalue' && $user->admin)
{
	$registro = array(
		'folder'	=> GETPOST('folder'),
		'min_dias'	=> GETPOST('min_dias'),
		'cuil'		=> GETPOST('cuil'),
	);
	
	if(is_dir($registro['folder']))
	{
		$registro['folder'] = str_replace('\\', '-', $registro['folder']);
		
		$sql = 
			"UPDATE `tms_config_rece` 
				SET 
					`folder`			= '$registro[folder]',
					`min_dias`			= $registro[min_dias],
					`cuil`				= $registro[cuil]
				WHERE 
					`id_config`		= 1";
		
		$db->query($sql);
			
		setEventMessage($langs->trans("SetupSaved"));
	}
	else
	{
		setEventMessage("No existe el directorio de la carpeta", 'errors');
	}
}
else
if ($action == 'punto_venta' && $user->admin)
{
	$registro = array(
		'punto_venta'		=> GETPOST('punto_venta'),
		'cod_autorizacion'	=> GETPOST('cod_autorizacion'),
		'id_punto'			=> GETPOST('id_punto'),
	);
	
	$sql = 
		"UPDATE `tms_puntos_venta` 
			SET 
				`punto_venta`		= $registro[punto_venta],
				`cod_autorizacion`	= $registro[cod_autorizacion]
			WHERE 
				`id_punto`			= $registro[id_punto]";
		
	$db->query($sql);
			
	setEventMessage($langs->trans("SetupSaved"));
}
else
if ($action == 'new' && $user->admin)
{
	$registro = array(
		'punto_venta'		=> GETPOST('punto_venta'),
		'cod_autorizacion'	=> GETPOST('cod_autorizacion'),
	);
	
	$sql	= 
		"SELECT 
				* 
			FROM 
				`tms_puntos_venta`
			WHERE 
				`punto_venta` = $registro[punto_venta]
			";
	
	$punto_query = $db->query($sql);	
		
	$num_punto	= $db->num_rows($punto_query);	
	
	if($num_punto > 0)
	{
		setEventMessage("El punto de venta ya esta creado", 'errors');
	}
	else
	{
		$sql = 
			"INSERT INTO `tms_puntos_venta` (
					`punto_venta`,
					`cod_autorizacion`
				)
				VALUES (
					$registro[punto_venta],
					$registro[cod_autorizacion]
				)";
		
		$db->query($sql);
		
		$id_insert = $db->last_insert_id('tms_puntos_venta');
		
		if($id_insert != 0)
		{
			setEventMessage($langs->trans("SetupSaved"));	
		}	
	}	
}
else
if(isset($_GET['delete']))
{
	$registro = array(
		'id_punto'		=> $_GET['delete']
	);
	
	$sql = 
		"DELETE FROM 
				`tms_puntos_venta` 
			WHERE
				`id_punto`  = $registro[id_punto]";
		
	$db->query($sql);
			
	setEventMessage($langs->trans("SetupSaved"));
}


/*----------------------------------------------------------------------------
		SELECT de los origenes del pedido
----------------------------------------------------------------------------*/

$sql	= 
	"SELECT 
		* 
	FROM 
		`tms_config_rece`";

$config_query = $db->query($sql);	
	
$num_config	= $db->num_rows($config_query);		


$sql	= 
	"SELECT 
		* 
	FROM 
		`tms_puntos_venta`";

$puntos_query = $db->query($sql);	
	
$num_puntos	= $db->num_rows($puntos_query);
		

if($num_config > 0)
{
	$c = 0;
	
	while ($c < $num_config)
	{
		$config_rece = $db->fetch_array($config_query);
		
		$c++;
	}
}			



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

dol_fiche_head($head, 'config', 'Rece', 0, 'rece');

print $langs->trans("ReceConfigDesc")."<br>\n";

print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';

	print '<input type="hidden" name="action" value="setvalue">';
	
	print '<table class="noborder" width="100%">';
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("ReceDato").'</td>';
	print '<td>'.$langs->trans("ReceValor").'</td>';
	print '</tr>';
	
	$config_rece['folder'] = str_replace('-', '\\', $config_rece['folder']);
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("ReceCarpeta").'</td>';
	print '<td><input name="folder" value="'.$config_rece['folder'].'" size="80" required></td>';
	print '</tr>';

	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("ReceMinDias").'</td>';
	print '<td><input name="min_dias" value="'.$config_rece['min_dias'].'" size="20" required></td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("ReceCuil").'</td>';
	print '<td><input name="cuil" value="'.$config_rece['cuil'].'" size="20" required></td>';
	print '</tr>';
			
	print '</table>';
		
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	
	print '<br><a href="'.DOL_URL_ROOT.'/rece/admin/config.php?action=new" class="button">'.$langs->trans("New").'</a>';
	
	if(isset($_GET['action']))
	{
		print '<br><hr>';
		print '<label>'.$langs->trans("RecePuntoVenta").'</label><input name="punto_venta" value="'.$puntos['punto_venta'].'">';
		print '<label>'.$langs->trans("ReceCodAutorizacion").'</label><input name="cod_autorizacion" value="'.$puntos['cod_autorizacion'].'">';
		print '<input type="hidden" name="action" value="new">';
		print '<input type="submit" class="button" value="'.$langs->trans("Create").'"> ';
		print '<a href="'.DOL_URL_ROOT.'/rece/admin/config.php"  class="button">'.$langs->trans("Cancel").'</a>';
		print '<br><hr>';
			
	}
	
	print '<table class="noborder" width="100%">';
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("RecePuntoVenta").'</td>';
	print '<td>'.$langs->trans("ReceCodAutorizacion").'</td>';
	print '<td>'.$langs->trans("ReceValor").'</td>';
	print '</tr>';
	
	if($num_puntos > 0)
	{
		$c = 0;
		
		$cadena = "'".$langs->trans("ReceEliminarConfirmar")."'";
		
		while ($c < $num_puntos)
		{
			$puntos = $db->fetch_array($puntos_query);
			
			$var=!$var;
			print '<tr '.$bc[$var].'>';
			
			if(isset($_GET['id']) && $puntos['id_punto'] == $_GET['id'])
			{
				print '<td><input name="punto_venta" value="'.$puntos['punto_venta'].'"></td>';
				print '<td><input name="cod_autorizacion" value="'.$puntos['cod_autorizacion'].'"></td>';
				print '<input type="hidden" name="action" value="punto_venta">';
				print '<input type="hidden" name="id_punto" value="'.$puntos['id_punto'].'">';
				print '<td>
							<input type="submit" class="button" value="'.$langs->trans("Modify").'">
							<a href="'.DOL_URL_ROOT.'/rece/admin/config.php" class="button">'.$langs->trans("Cancel").'</a>
						</td>';
			}
			else 
			{
				print '<td>'.$puntos['punto_venta'].'</td>';
				print '<td>'.$puntos['cod_autorizacion'].'</td>';
				print '<td>
							<a href="'.DOL_URL_ROOT.'/rece/admin/config.php?id='.$puntos['id_punto'].'">
								<span class="label label-primary">'.$langs->trans("Modify").'</span>
							</a> 
							<a href="'.DOL_URL_ROOT.'/rece/admin/config.php?delete='.$puntos['id_punto'].'" onclick="return confirm('.$cadena.')">
								<span class="label label-danger">'.$langs->trans("Delete").'</span>
							</a>
						</td>';
			}	
			
			print '</tr>';			
			
			$c++;
		}
	}	
				
	print '</table>';
	
print '</form>';

llxFooter();
$db->close();
