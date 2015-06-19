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

$servicename='Sincronizar';

$langs->load("rece");
$langs->load("bills");
$langs->load("dict");

if (! $user->admin) accessforbidden();

function armar_cadena($cadena, $cantidad, $tipo)
{
	if($tipo == 'Varchar')
	{
		$char_completar = ' ';
	}
	else
	if($tipo == 'Importe' || $tipo == 'Int' || $tipo == 'Otro')
	{
		$char_completar = '0';
	}
	
	if($cantidad > strlen($cadena))
	{
		if($tipo == 'Varchar')
		{
			$campo = $cadena;
		}
		else
		{
			$campo = '';
		}	
									
		$limite = $cantidad - strlen($cadena);
									
		for ($i = 0; $i < $limite; $i++) 
		{ 
			$campo .= $char_completar; 
		}
		
		if($tipo == 'Importe' || $tipo == 'Int' || $tipo == 'Otro')
		{
			$campo .= $cadena;
		}
	}
	else
	{
		$campo = substr($cadena, 0, $cantidad);	
	}
	
	return $campo;
}

/*----------------------------------------------------------------------------
		UPDATE de la tabla tms_config_sincronizacion
----------------------------------------------------------------------------*/

$action = GETPOST('action');

if ($action == 'setvalue' && $user->admin)
{
	$sql = 
		"SELECT 
			llx_facture.*, 
			llx_societe.nom,
			llx_societe.siren
		FROM 
			`llx_facture` 
		INNER JOIN 
			llx_societe ON(llx_facture.fk_soc = llx_societe.rowid)";

	$facturas_query = $db->query($sql);	
		
	$num_facturas	= $db->num_rows($facturas_query);
		
	$facturas_agenerar = GETPOST('toGenerate');
	
	$c = 0;
	$total_operaciones = 0;
	$total_neto = 0;
	$total_impuesto = 0;
	
	if($num_facturas > 0)
	{
		$archivo = '';
			
		while ($c < $num_facturas)
		{
			$factura = $db->fetch_array($facturas_query);
			
			if(in_array($factura['rowid'], $facturas_agenerar))
			{
				$linea = '';
				
				$r = 0;
				
				if($num_facturas > 0)
				{
					$sql	= "SELECT * FROM `tms_rece_campos` ORDER BY orden";

					$rece_query = $db->query($sql);	
		
					$num_rece	= $db->num_rows($rece_query);
		
					while ($r < $num_rece)
					{
						$rece = $db->fetch_array($rece_query);
						
						if($rece['default'] != '')
						{
							$campo = $rece['default'];
						}
						else
						if($rece['campo_dolibarr'] == '')
						{
							$campo = armar_cadena('', $rece['cantidad'], $rece['tipo']);
						}
						else
						{
							if($rece['tipo'] == 'Fecha')
							{
								$campo = date('Ymd', strtotime($factura[$rece['campo_dolibarr']]));
							}
							else
							if($rece['tipo'] == 'Varchar')
							{
								$campo = armar_cadena($factura[$rece['campo_dolibarr']], $rece['cantidad'], $rece['tipo']);
							}
							else
							if($rece['tipo'] == 'Importe')
							{
								$campo = '';
								
								$importe = round($factura[$rece['campo_dolibarr']], 2);
								
								$importe_entero = round($factura[$rece['campo_dolibarr']]);
								
								$resto = $importe - $importe_entero;
								
								if($resto == 0)
								{
									$importe .= '00'; 
								}
								else
								if(strlen($resto) == 1)
								{
									$importe .= '0'; 
								}
								
								$importe = str_replace('.', '', $importe);
																
								$campo = armar_cadena($importe, $rece['cantidad'], $rece['tipo']);
														
							}
							else
							if($rece['tipo'] == 'Int')
							{
								$campo = '';
								
								$numeros = ereg_replace("[^0-9]", "", $factura[$rece['campo_dolibarr']]);
								
								$campo = armar_cadena($numeros, $rece['cantidad'], $rece['tipo']); 
							}							
							else
							{
								$campo = armar_cadena($factura[$rece['campo_dolibarr']], $rece['cantidad'], 'Otro');
							}
						}
						
						$linea .= $campo; 
						
						$r++;
					}

					$total_operaciones = $total_operaciones + $factura['total_ttc'];
					$total_neto = $total_neto + $factura['total'];
					$total_impuesto = $total_impuesto + $factura['tva'];

					$file = fopen("archivo.txt", "a");
					fwrite($file, $linea . PHP_EOL);
					fclose($file);
				}
							
			}
			
			$c++;
		}

		$file = fopen("archivo.txt", "a");
		fwrite($file, $total_operaciones . PHP_EOL);
		fclose($file);	

	}
	
}


/*----------------------------------------------------------------------------
		SELECT de los origenes del pedido
----------------------------------------------------------------------------*/

$sql	= "SELECT * FROM `llx_facture`";

$facturas_query = $db->query($sql);	
	
$num_facturas	= $db->num_rows($facturas_query);					



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
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("RecePeriodo").'</td>';
	print '<td>'.$langs->trans("RecePuntodeVenta").'</td>';
	print '<td>'.$langs->trans("ReceNroAutorizacion").'</td>';
	print '<td>'.$langs->trans("ReceInformaFechas").'</td>';
	print '<td>'.$langs->trans("RecePrestacionesServicio").'</td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td><input name="mes" value="'.date('m').'" readonly><input name="ano" value="'.date('Y').'" readonly></td>';
	print '<td><input name="PuntodeVenta" value="" required></td>';
	print '<td><input name="NroAutorizacion" value=""></td>';
	print '<td><input type="checkbox" name="" value=""></td>';
	print '<td><input type="checkbox" name="" value=""></td>';
	print '</tr>';
	
	print '</table>';

	print '<table class="noborder" width="100%">';
		
		$var=true;
		print '<tr class="liste_titre">';
		print '<td></td>';
		print '<td>'.$langs->trans("ReceTipoRegistro").'</td>';
		print '<td>'.$langs->trans("ReceFechaComprobante").'</td>';
		print '<td>'.$langs->trans("ReceNroComprobante").'</td>';
		print '<td>'.$langs->trans("ReceCodDocumento").'</td>';
		print '<td>'.$langs->trans("ReceNroIdentificacion").'</td>';
		print '<td>'.$langs->trans("ReceImporteOperacion").'</td>';
		print '<td>'.$langs->trans("ReceImporteOperacionPrecio").'</td>';
		print '<td>'.$langs->trans("ReceImporteNeto").'</td>';
		print '<td>'.$langs->trans("ReceImpuestoLiquidado").'</td>';
		print '<td>'.$langs->trans("ReceImpuestoLiquidadoRNI").'</td>';
		print '<td>'.$langs->trans("ReceImpuestoOperaciones").'</td>';
		print "</tr>\n";
		
		$c = 0;
		$total_operaciones = 0;
		$total_neto = 0;
		$total_impuesto = 0;
		
		if($num_facturas > 0)
		{	
			while ($c < $num_facturas)
			{
				$factura = $db->fetch_array($facturas_query);
				
				$var=!$var;
				print '<tr '.$bc[$var].'>';
				print '<td><input type="checkbox" name="toGenerate[]" value="'.$factura['rowid'].'" checked></td>';
				print '<td></td>'; //ReceTipoRegistro
				print '<td>'.$factura['datef'].'</td>';
				print '<td>'.$factura['facnumber'].'</td>';
				print '<td></td>'; //ReceCodDocumento
				print '<td></td>'; //ReceNroIdentificacion
				print '<td>'.$factura['total_ttc'].'</td>';
				print '<td></td>'; //ReceImporteOperacionPrecio
				print '<td>'.$factura['total'].'</td>';
				print '<td>'.$factura['tva'].'</td>';
				print '<td></td>'; //ReceImpuestoLiquidadoRNI
				print '<td></td>'; //ReceImpuestoOperaciones
				print '</tr>';
				
				$total_operaciones = $total_operaciones + $factura['total_ttc'];
				$total_neto = $total_neto + $factura['total'];
				$total_impuesto = $total_impuesto + $factura['tva'];
				
				$c++; 
			}
		}
		
		print '<tr class="liste_total">';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td>'.$total_operaciones.'</td>';
		print '<td></td>';
		print '<td>'.$total_neto.'</td>';
		print '<td>'.$total_impuesto.'</td>';
		print '<td></td>';
		print '<td></td>';
		print "</tr>\n";	
			
		
	print '</table>';
	
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

llxFooter();
$db->close();
