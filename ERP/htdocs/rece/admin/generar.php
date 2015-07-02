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
		Datos de configuración
----------------------------------------------------------------------------*/

$sql	= 
	"SELECT 
		* 
	FROM 
		`tms_config_rece`";

$config_rece_query = $db->query($sql);	
	
$num_rece_query	= $db->num_rows($config_rece_query);

if($num_rece_query > 0)
{
	$c = 0;
	
	while ($c < $num_rece_query)
	{
		$config_rece = $db->fetch_array($config_rece_query);
		$c++;
	}
}

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
		Función para modificar los numeros de factura
----------------------------------------------------------------------------*/

function formato_factura_numero($factura, $type)
{
	global $conf;	
	
	$conf->global->FACTURE_MERCURE_MASK_REPLACEMENT;
	$conf->global->FACTURE_MERCURE_MASK_DEPOSITM;
	
		
	if($type == 2)
	{
		$mascara = $conf->global->FACTURE_MERCURE_MASK_CREDIT;
	}
	else
	{
		$mascara = $conf->global->FACTURE_MERCURE_MASK_INVOICE;
	}
	
	$array_mascara = explode('{', $mascara);
	
	foreach($array_mascara as $key => $value)
	{
		$value = str_replace('}', '', $value);
		if(is_numeric($value))
		{
			$new_value = $value;
		}
	}
	
	$mascara = str_replace('{', '', $mascara);
	$mascara = str_replace('}', '', $mascara);
	
	$posicion = strpos($mascara, $new_value);
	
	$final = $posicion + strlen($new_value);
	
	$factura = substr($factura, $posicion, $final);
			
	return $factura;
}

/*----------------------------------------------------------------------------
		Función para corroborar los datos antes de generar el txt
----------------------------------------------------------------------------*/

function comprobar_factura($factura, $config_rece)
{
	if($factura['siren'] == '')
	{
		$errores['CUIL'] = $langs->trans("ReceCuilVacio");
	}
	
	$cuil = ereg_replace("[^0-9]", "", $factura['siren']);
	
	if(strlen($cuil) != 11)
	{
		$errores['CUIL'] = $langs->trans('ReceCantidadIncorrecta');
	}
	
	$fecha		= date('Y-m-j');
	$nuevafecha = strtotime("-".$config_rece['min_dias']." day" , strtotime($fecha)) ;
	$nuevafecha = date('Y-m-j' , $nuevafecha);
	
	$fecha_min	= strtotime($nuevafecha);
	$fecha_max	= strtotime($fecha);
	$fecha_fac	= strtotime($factura['datef']);
		
	if($fecha_min > $fecha_fac)
	{
		$errores['FECHA'] = $langs->trans('ReceFechaMin').$nuevafecha;
	}
	else
	if($fecha_max < $fecha_fac)
	{
		$errores['FECHA'] = $langs->trans('ReceFechaMax').$fecha;
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
		
		if($cadena < 0)
		{
			$cadena = $cadena * -1;
		}
		
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
			`llx_facture`.`fk_statut`= 1 OR
			`llx_facture`.`fk_statut`= 2  
		ORDER BY 
			`llx_facture`.`datef`, `llx_facture`.`facnumber`";

	$facturas_query = $db->query($sql);	
		
	$num_facturas	= $db->num_rows($facturas_query);
		
	$facturas_agenerar = GETPOST('toGenerate');
	$mes = GETPOST('mes');
	$ano = GETPOST('ano');
	$cuil= GETPOST('cuil');
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
	
	$config_rece['folder'] = str_replace('-', '\\', $config_rece['folder']);
	
	$nombre_archivo = $config_rece['folder'];
	
	$nombre_archivo .= 'RECE'.$ano.$mes.$pdv.$coa.$inv_text.$pes_text.'.txt';
	
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
					$sql = 
						"SELECT 
							* 
						FROM 
							`tms_rece_campos` 
						ORDER BY 
							orden";

					$rece_query = $db->query($sql);	
		
					$num_rece	= $db->num_rows($rece_query);
		
					while ($r < $num_rece)
					{
						$rece = $db->fetch_array($rece_query);
						
						// 1 - Si hay un campo default se completa con ese valor
						if($rece['default'] != '')
						{
							$campo = armar_cadena($rece['default'], $rece['cantidad'], $rece['tipo']);
						}
						else
						// 2 - Algunos valores pueden ser enviados en el form como el punto de venta	
						if($rece['post'] != '')
						{
							$campo = armar_cadena(GETPOST($rece['post']), $rece['cantidad'], $rece['tipo']);
						}
						else
						// 3 - Si no tiene ni valor default, ni post, ni campo de datos se debe llenar con ' ' o 0;
						if($rece['campo_dolibarr'] == '')
						{
							$campo = armar_cadena('', $rece['cantidad'], $rece['tipo']);
						}
						else
						{
							// 4 - Si es fecha se debe tener en cuenta si se informa o no fecha y el formato de la fecha
							if($rece['tipo'] == 'Fecha')
							{
								if($inv_text == 1)
								{
									$campo = date('Ymd', strtotime($factura[$rece['campo_dolibarr']]));	
								}
								else
								{
									$campo = armar_cadena('', $rece['cantidad'], $rece['tipo']);
								}
							}
							else
							{
								// 5 - Hay que cambiar el formato del numero del comprobante dado que la afip solo acepta numeros
								if($rece['campo_dolibarr'] == 'facnumber')
								{
									$facnumber = formato_factura_numero($factura['facnumber'], $factura['type']);
									$campo = armar_cadena($facnumber, $rece['cantidad'], $rece['tipo']);
								}
								// 6 - Se carga el dato y se adapata a la cantidad dispuesta por la afip dependiendo del tipo. 	
								else
								{
									$campo = armar_cadena($factura[$rece['campo_dolibarr']], $rece['cantidad'], $rece['tipo']);	
								}
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
					
					$sql	= 
						"UPDATE 
							`llx_facture` 
						SET 
							`rece` = 1 
						WHERE
							`llx_facture`.`rowid` = $factura[rowid]";
						
					$db->query($sql);	
				}				
			}
			
			$c++;
		}
		
		$totales = '2';
		$totales .= $ano.$mes;
		$totales .= armar_cadena('', 13, 'Varchar');
		$totales .= armar_cadena($cant_lineas, 8, 'Int');
		$totales .= armar_cadena('', 17, 'Varchar');
		$totales .= armar_cadena($cuil, 11, 'Varchar');
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
	
	$coa = $coa + 1;

	$sql = 
		"UPDATE 
				`tms_puntos_venta` 
			SET 
				`cod_autorizacion`	= $coa
			WHERE 
				`punto_venta`		= $pdv";
		
	$db->query($sql);
			
	setEventMessage($langs->trans("SetupSaved"));
	
}

/*----------------------------------------------------------------------------
		SELECT de los origenes del pedido
----------------------------------------------------------------------------*/

$sql	= 
	"SELECT 
		`llx_facture`.*, 
		`llx_societe`.`nom`, 
		`llx_societe`.`rowid` as id_societe,
		`llx_societe`.`siren` 
	FROM 
		`llx_facture` 
	INNER JOIN 
		`llx_societe` ON(`llx_facture`.`fk_soc` = `llx_societe`.`rowid`) 
	WHERE 
		`llx_facture`.`fk_statut`= 1
	ORDER BY 
		`llx_facture`.`datef` DESC, `llx_facture`.`facnumber`
	LIMIT
		0, $config_rece[limite]";

$facturas_query = $db->query($sql);	
	
$num_facturas	= $db->num_rows($facturas_query);		


$sql	= 
	"SELECT 
		* 
	FROM 
		`tms_puntos_venta`";

$puntos_query = $db->query($sql);	
	
$num_puntos	= $db->num_rows($puntos_query);	

$puntos_query2 = $db->query($sql);	


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
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';

	print '<input type="hidden" name="action" value="setvalue">';
	
	print '<table class="noborder" width="100%">';
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("RecePeriodo").'</td>';
	print '<td>'.$langs->trans("RecePuntoVenta").'</td>';
	print '<td>'.$langs->trans("ReceCodAutorizacion").'</td>';
	print '<td>'.$langs->trans("ReceInformaFechas").'</td>';
	print '<td>'.$langs->trans("RecePrestacionesServicio").'</td>';
	print '<td>'.$langs->trans("ReceCuil").'</td>';
	print '</tr>';
	
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td><input name="mes" value="'.date('m').'" readonly><input name="ano" value="'.date('Y').'" readonly></td>';
	print '<td><select name="PuntodeVenta" id="PuntodeVenta" onchange="cambiar_valor()" required>';
			if($num_puntos > 0)
			{
				$c = 0;	
				while ($c < $num_puntos)
				{
					$puntos = $db->fetch_array($puntos_query);
					
					print '<option value="'.$puntos['punto_venta'].'">'.$puntos['punto_venta'].'</option>';
					
					if($c == 0)
					{
						$codautorizacion = $puntos['cod_autorizacion'];
					}
					
					$c++;
				}
			}
	print '</select>';			
	print '</td>';
	print '<td><input name="CodAutorizacion" id="CodAutorizacion" value="'.$codautorizacion.'"></td>';
	print '<td><input name="InformaFechas" type="checkbox" value="1" checked></td>';
	print '<td><input name="PrestacionesServicio" type="checkbox"  value="1"></td>';
	print '<td><input name="cuil" value="'.$config_rece['cuil'].'" readonly></td>';
	print '</tr>';
	
	print '</table>';
	
	print '<script>';
	print 'function cambiar_valor() {';
	
	print ' var x = document.getElementById("PuntodeVenta");';
	print ' var y = document.getElementById("CodAutorizacion");';
	
	if($num_puntos > 0)
	{
		$c = 0;	
		while ($c < $num_puntos)
		{
			$puntos = $db->fetch_array($puntos_query2);
			
			print 'if(x.value == '.$puntos['punto_venta'].'){';
			print ' y.value ='.$puntos['cod_autorizacion'].'';	
			print '}';
			
			$c++;
		}
	}
	
	print '}';
	print '</script>';

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
				
				$error = comprobar_factura($factura, $config_rece);
				
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
					print '<td>';
					print '<input type="checkbox" name="toGenerate[]" value="'.$factura['rowid'].'" title="'.$mensaje.'" disabled>';
					print ' <img src="'.DOL_URL_ROOT.'/theme/eldy/img/error.png" border="0" alt="" title="'.$mensaje.'">';
					print '</td>';					
				}
				else
				{
					if($factura['rece'] == 1)
					{
						$icono = '<img src="'.DOL_URL_ROOT.'/theme/eldy/img/tick.png" border="0" alt="" title="Factura generada">'; 
					}
					else 
					{
						$icono = '';
					}
					
					print '<td><input type="checkbox" name="toGenerate[]" value="'.$factura['rowid'].'"> '.$icono.'</td>';
						
				}
				
				print '<td></td>'; //ReceTipoRegistro
				print '<td>'.date('d-m-Y', strtotime($factura['datef'])).'</td>';
				print '<td>';
				print '<a href="'.DOL_URL_ROOT.'/compta/facture.php?facid='.$factura['rowid'].'" title="'.formato_factura_numero($factura['facnumber'], $factura['type']).'">';
				print '<img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_bill.png" border="0"> ';
				print $factura['facnumber'].'</a></td>';
				print '<td>';
				print '<a href="'.DOL_URL_ROOT.'/societe/soc.php?socid='.$factura['id_societe'].'">';
				print '<img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_company.png" border="0"> ';
				print $factura['nom'].'</a></td>';
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
