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

$servicename='Rece';

$langs->load("rece");
$langs->load("bills");
$langs->load("dict");

if (! $user->admin) accessforbidden();

/*----------------------------------------------------------------------------
		Función para unificar formatos de importes
----------------------------------------------------------------------------*/

function formato_importe($importe)
{
	$importe = explode('.', $importe);
	
	$decimal = substr($importe[1], 0, 2);
	
	if(strlen($decimal) < 1)
	{
		$decimal = '00'; 
	}
	else
	if(strlen($decimal) < 2)
	{
		$decimal = '0'; 
	}
	
	$formato = $importe[0].'.'.$decimal;
	
	return $formato;
}

/*----------------------------------------------------------------------------
		Función para corroborar los datos antes de generar el txt
----------------------------------------------------------------------------*/

function comprobar_factura($factura)
{
	if($factura['siren'] == '')
	{
		$errores['CUIL'] = 'El nro de cuil no puede estar vacio';
	}
	
	$cuil = ereg_replace("[^0-9]", "", $factura['siren']);
	
	if(strlen($cuil) != 11)
	{
		$errores['CUIL'] = 'La cantidad de valores no es correcta';
	}

	$fecha		= date('Y-m-j');
	$nuevafecha = strtotime('-5 day' , strtotime($fecha)) ;
	$nuevafecha = date('Y-m-j' , $nuevafecha);
	
	$fecha_min	= strtotime($nuevafecha);
	$fecha_max	= strtotime($fecha);
	$fecha_fac	= strtotime($factura['datef']);
		
	if($fecha_min > $fecha_fac)
	{
		$errores['FECHA'] = 'La fecha del comprobante no puede ser menor a '.$nuevafecha;
	}
	else
	if($fecha_max < $fecha_fac)
	{
		$errores['FECHA'] = 'La fecha del comprobante no puede ser mayor a '.$fecha;
	}
		
	
	if(isset($errores))
	{
		return $errores;
	}
	else 
	{
		return 1;	
	}
}

/*----------------------------------------------------------------------------
		Función para armar la cadena con el formato para importar
----------------------------------------------------------------------------*/

function armar_cadena($cadena, $cantidad, $tipo)
{
	if($tipo == 'Varchar')
	{
		$char_completar = ' ';
	}
	else
	if($tipo == 'Int')
	{
		$char_completar = '0';
		
		$cadena = ereg_replace("[^0-9]", "", $cadena);
	}
	else
	if($tipo == 'Importe')
	{
		$char_completar = '0';
		
		$importe = round($cadena, 2);
								
		$importe_entero = round($cadena);
		
		if($importe > $importe_entero)
		{
			$resto = $importe - $importe_entero;	
			
			$resto = round($resto, 2);
			
			$a_resto = explode('.', $resto);
		}
		else
		{
			$resto = $importe_entero - $importe;	
			
			$resto = round($resto, 2);
			
			$a_resto = explode('.', $resto);
		}
								
		if($resto == 0)
		{
			$importe .= '00'; 
		}
		else
		if(strlen($a_resto[1]) == 1)
		{
			$importe .= '0'; 
		}
								
		$cadena = str_replace('.', '', $importe);		
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
		Generación del archivo
----------------------------------------------------------------------------*/

$action = GETPOST('action');

if ($action == 'setvalue' && $user->admin)
{
	$sql	= 
		"SELECT 
			`llx_facture`.*, 
			`llx_societe`.`nom`, 
			`llx_societe`.`siren` 
		FROM 
			`llx_facture` 
		INNER JOIN 
			`llx_societe` ON(`llx_facture`.`fk_soc` = `llx_societe`.`rowid`) 
		WHERE 
			`llx_facture`.`fk_statut`= 1 
		ORDER BY 
			`llx_facture`.`datef`, `llx_facture`.`facnumber`";

	$facturas_query = $db->query($sql);	
		
	$num_facturas	= $db->num_rows($facturas_query);
		
	$facturas_agenerar = GETPOST('toGenerate');
	$mes = GETPOST('mes');
	$ano = GETPOST('ano');
	$pdv = GETPOST('PuntodeVenta');
	$coa = GETPOST('CodAutorizacion');
	$inv = GETPOST('InformaFechas');
	$pes = GETPOST('PrestacionesServicio');
	
	if($inv)
	{
		$inv_text = '1';
	}
	else 
	{
		$inv_text = '0';
	}
	
	if($pes)
	{
		$pes_text = '1';
	}
	else 
	{
		$pes_text = '0';
	}
	
	$nombre_archivo = 'RECE'.$ano.$mes.$pdv.$coa.$inv_text.$pes_text.'.txt';
	
	$c = 0;
	$total_operaciones = 0;
	$total_neto = 0;
	$total_impuesto = 0;
	
	if($num_facturas > 0)
	{
		$archivo = '';
		
		$cant_lineas = 0;
			
		while ($c < $num_facturas)
		{
			$factura = $db->fetch_array($facturas_query);
			
			if(in_array($factura['rowid'], $facturas_agenerar))
			{
				$cant_lineas++;
				
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
							{
								$campo = armar_cadena($factura[$rece['campo_dolibarr']], $rece['cantidad'], $rece['tipo']);
							}
						}
						
						$linea .= $campo; 
						
						$r++;
					}

					$total_operaciones	= $total_operaciones + $factura['total_ttc'];
					$total_neto 		= $total_neto + $factura['total'];
					$total_impuesto		= $total_impuesto + $factura['tva'];

					$file = fopen($nombre_archivo, "a");
					fwrite($file, $linea . PHP_EOL);
					fclose($file);
				}
			}
			
			$c++;
		}
		
		$totales = '2';
		$totales .= $ano.$mes;
		$totales .= armar_cadena('', 13, 'Varchar');
		$totales .= armar_cadena($cant_lineas, 8, 'Int');
		$totales .= armar_cadena('', 17, 'Varchar');
		$totales .= '20305949125';
		$totales .= armar_cadena('', 22, 'Varchar');
		$totales .= armar_cadena($total_operaciones, 15, 'Importe');
		$totales .= armar_cadena(0, 15, 'Importe');
		$totales .= armar_cadena($total_neto, 15, 'Importe');
		$totales .= armar_cadena($total_impuesto, 15, 'Importe');
		$totales .= armar_cadena(0, 15, 'Importe');
		$totales .= armar_cadena(0, 15, 'Importe');
		$totales .= armar_cadena(0, 15, 'Importe');
		$totales .= armar_cadena(0, 15, 'Importe');
		$totales .= armar_cadena(0, 15, 'Importe');
		$totales .= armar_cadena(0, 15, 'Importe');
		$totales .= armar_cadena('', 61, 'Varchar');
		$totales .= '*';
		
		$file = fopen($nombre_archivo, "a");
		fwrite($file, $totales . PHP_EOL);
		fclose($file);	

	}
	
}

/*----------------------------------------------------------------------------
		SELECT de los origenes del pedido
----------------------------------------------------------------------------*/

$sql	= 
	"SELECT 
		`llx_facture`.*, 
		`llx_societe`.`nom`, 
		`llx_societe`.`siren` 
	FROM 
		`llx_facture` 
	INNER JOIN 
		`llx_societe` ON(`llx_facture`.`fk_soc` = `llx_societe`.`rowid`) 
	WHERE 
		`llx_facture`.`fk_statut`= 1 
	ORDER BY 
		`llx_facture`.`datef`, `llx_facture`.`facnumber`";

$facturas_query = $db->query($sql);	
	
$num_facturas	= $db->num_rows($facturas_query);					



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
	print '<td>'.$langs->trans("RecePeriodo").'</td>';
	print '<td>'.$langs->trans("RecePuntoVenta").'</td>';
	print '<td>'.$langs->trans("ReceCodAutorizacion").'</td>';
	print '<td>'.$langs->trans("ReceInformaFechas").'</td>';
	print '<td>'.$langs->trans("RecePrestacionesServicio").'</td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td><input name="mes" value="'.date('m').'" readonly><input name="ano" value="'.date('Y').'" readonly></td>';
	print '<td><input name="PuntodeVenta" value="" required></td>';
	print '<td><input name="CodAutorizacion" value=""></td>';
	print '<td><input name="InformaFechas" type="checkbox" value="1"></td>';
	print '<td><input name="PrestacionesServicio" type="checkbox"  value="1"></td>';
	print '</tr>';
	
	print '</table>';

	print '<table class="noborder" width="100%">';
		
		$var=true;
		print '<tr class="liste_titre">';
		print '<td></td>';
		print '<td>'.$langs->trans("ReceTipoRegistro").'</td>';
		print '<td>'.$langs->trans("ReceFechaComprobante").'</td>';
		print '<td>'.$langs->trans("ReceNroComprobante").'</td>';
		print '<td>'.$langs->trans("ReceNombre").'</td>';
		print '<td title="'.$langs->trans("ReceCodDocumento").'">'.$langs->trans("ReceRCodDocumento").'</td>';
		print '<td title="'.$langs->trans("ReceNroIdentificacion").'">'.$langs->trans("ReceRNroIdentificacion").'</td>';
		print '<td title="'.$langs->trans("ReceImporteOperacion").'">'.$langs->trans("ReceRImporteOperacion").'</td>';
		print '<td title="'.$langs->trans("ReceImporteOperacionPrecio").'">'.$langs->trans("ReceRImporteOperacionPrecio").'</td>';
		print '<td title="'.$langs->trans("ReceImporteNeto").'">'.$langs->trans("ReceRImporteNeto").'</td>';
		print '<td>'.$langs->trans("ReceImpuestoLiquidado").'</td>';
		print '<td title="'.$langs->trans("ReceImpuestoLiquidadoRNI").'">'.$langs->trans("ReceRImpuestoLiquidadoRNI").'</td>';
		print '<td title="'.$langs->trans("ReceImpuestoOperaciones").'">'.$langs->trans("ReceRImpuestoOperaciones").'</td>';
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
				
				$error = comprobar_factura($factura);
				
				if(is_array($error))
				{
					$mensaje = '';
					
					foreach ($error as $key => $value) 
					{
						$mensaje .= $key.' : '.$value;
					}
				}
				
				$var=!$var;
				print '<tr '.$bc[$var].'>';
				if(is_array($error))
				{
					print '<td><img src="../../theme/eldy/img/error.png" border="0" alt="" title="'.$mensaje.'"></td>';					
				}
				else
				{
					print '<td><input type="checkbox" name="toGenerate[]" value="'.$factura['rowid'].'"></td>';	
				}
				
				print '<td></td>'; //ReceTipoRegistro
				print '<td>'.date('d-m-Y', strtotime($factura['datef'])).'</td>';
				print '<td>'.$factura['facnumber'].'</td>';
				print '<td>'.$factura['nom'].'</td>';
				print '<td>80</td>'; //ReceCodDocumento
				print '<td>'.$factura['siren'].'</td>'; //ReceNroIdentificacion
				print '<td align="right">'.formato_importe($factura['total_ttc']).'</td>';
				print '<td></td>'; //ReceImporteOperacionPrecio
				print '<td align="right">'.formato_importe($factura['total']).'</td>';
				print '<td align="right">'.formato_importe($factura['tva']).'</td>';
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
		print '<td></td>';
		print '<td align="right">'.formato_importe($total_operaciones).'</td>';
		print '<td></td>';
		print '<td align="right">'.formato_importe($total_neto).'</td>';
		print '<td align="right">'.formato_importe($total_impuesto).'</td>';
		print '<td></td>';
		print '<td></td>';
		print "</tr>\n";	
			
		
	print '</table>';
	
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

llxFooter();
$db->close();