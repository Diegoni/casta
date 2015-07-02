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

if ($action == 'setvalue')
{
	$registro = array(
		'comentario'	=> GETPOST('comentario'),
		'id_estado'		=> GETPOST('id_estado'),
		'date_upd'		=> date('Y-m-d H:i:s'),
		'id_error'		=>  GETPOST('id_error')
	);
		
	$sql = 
	"UPDATE `tms_log_errores` 
		SET 
			`comentario`	= '$registro[comentario]',
			`id_estado`		= $registro[id_estado],
			`date_upd`		= '$registro[date_upd]'
		WHERE 
			`id_log`		= $registro[id_error]";
	
	$db->query($sql);
		
	setEventMessage($langs->trans("SetupSaved"));
}

if(isset($_GET['id_error']))
{
	$sql	= "SELECT 
					* 
				FROM 
					`tms_log_errores`
				INNER JOIN 
					`tms_estados_log` ON(tms_log_errores.id_estado = tms_estados_log.id_estado) 
				WHERE 
					`id_log` = $_GET[id_error]";
					
	$resql	= $db->query($sql);	
		
	$numr	= $db->num_rows($resql);	
	
	$sql	= "SELECT 
					* 
				FROM 
					`tms_estados_log`
				ORDER BY 
					estado";
					
	$sql_estados = $db->query($sql);
	
	$num_estados = $db->num_rows($sql_estados);
	
	$i		= 0;		
}
else
{
	
	$sql		= "SELECT 
						* 
					FROM 
						`tms_config_sincronizacion`";
						
	$resql		= $db->query($sql);	
	
	$config		= $db->fetch_array($resql);
		
	$sql	= "SELECT 
					* 
				FROM 
					`tms_log_errores`
				INNER JOIN 
					`tms_estados_log` ON(tms_log_errores.id_estado = tms_estados_log.id_estado) 
				ORDER BY 
					id_log DESC LIMIT 0, $config[cantidad]";
					
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

dol_fiche_head($head, 'errores', 'Sincronización', 0, 'sincronizar');

if(isset($_GET['id_error']))
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
		print '<td>'.$langs->trans("SincronizarParametros").'</td>';
		print '<td>'.$langs->trans("Value").'</td>';
		print "</tr>\n";
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarError").'</td><td>';
		print $registro['error'];
		print '</td></tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarFecha").'</td><td>';
		print date('d-m-Y H:i:s', strtotime($registro['date_add']));
		print '</td></tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarComentario").'</td><td>';
		$doleditor=new DolEditor('PAYPAL_MESSAGE_OK',$conf->global->PAYPAL_MESSAGE_OK,'',100,'comentario','In',false,true,true,ROWS_4,60);
		$doleditor->Create();
		
		
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print $langs->trans("SincronizarEstado").$registro['id_estado'].'</td><td>';
		print '<select name="id_estado">';
		
		if($num_estados > 0)
		{	
			while ($i < $num_estados)
			{
				$estados = $db->fetch_object($sql_estados);
				
				if($registro['id_estado'] == $estados->id_estado)
				{
					$select = 'selected';
				}
				else 
				{
					$select = '';
				}
				
				print '<option value="'.$estados->id_estado.'" '.$select.'>'.$estados->estado.'</option>';
				
				$i++;
			}
		}	
		
		print '</select>';
		print '</td></tr>';
		
		print '</table>';
		
		print '<input name="id_error" value="'.$_GET['id_error'].'" type="hidden">';
		
		print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';
		
		print '</form>';	
		
	}
	
}
else
{	
	print $langs->trans("SincronizarUltimos").' '.$config['cantidad'].' '.$langs->trans("SincronizarRegistros")."<br>\n";
	
	print '<br>';
	print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="setvalue">';
	
	print '<table class="noborder" width="100%">';
	
	$var=true;
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("SincronizarID").'</a></td>';
	print '<td>'.$langs->trans("SincronizarError").'</td>';
	print '<td>'.$langs->trans("SincronizarAlta").'</td>';
	print '<td>'.$langs->trans("SincronizarModificacion").'</td>';
	print '<td>'.$langs->trans("SincronizarEstado").'</td>';
	print "</tr>\n";
	
	if($numr > 0)
	{	
		while ($i < $numr)
		{
			$registro = $db->fetch_object($resql);
			
			$var=!$var;
			print '<tr '.$bc[$var].'><td>';
			print $registro->id_log.'</td><td>';
			print '<a href="'.DOL_URL_ROOT.'/sincronizar/admin/errores.php?id_error='.$registro->id_log.'">';
			print $registro->error.'</a></td><td>';
			print date('d-m-Y', strtotime($registro->date_add)).'</td><td>';
			print date('d-m-Y', strtotime($registro->date_upd)).'</td><td>';
			
			if($registro->id_estado == 1)
			{
				$label = 'danger';
			}
			else
			if($registro->id_estado == 2)
			{
				$label = 'success';
			}
			else
			if($registro->id_estado == 3)
			{
				$label = 'warning';				
			}
			
			print '<span class="label label-'.$label.'">';
			print $registro->estado.'</span></td>';
			print '</tr>';
			
			$i++;	
		}
	}
	
	print '</table>';
	print '</form>';
}

llxFooter();
$db->close();
