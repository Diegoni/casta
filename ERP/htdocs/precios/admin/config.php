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

$langs->load("precios");

if (! $user->admin) accessforbidden();


/*----------------------------------------------------------------------------
		Guardamos la configuración
----------------------------------------------------------------------------*/

function calcular_precio($precio, $condiciones)
{
	if($condiciones['condicion'] == 0) // Valor Fijo
	{
		if($condiciones['tipo'] == 1)
		{
			$precio = $precio + $condiciones['valor'];
		}
		else
		{
			$precio = $precio - $condiciones['valor'];
		}
	}
	else // Porcentaje
	{
		if($condiciones['tipo'] == 1)
		{
			$precio = $precio + ($precio * $condiciones['valor'] / 100);
		}
		else
		{
			$precio = $precio - ($precio * $condiciones['valor'] / 100);
		}
	}
	
	return $precio;
}



/*----------------------------------------------------------------------------
		Guardamos la configuración
----------------------------------------------------------------------------*/

function formato_importe($importe)
{
	$importe = round($importe, 2);
		
	return $importe;
}

/*----------------------------------------------------------------------------
		Guardamos la configuración
----------------------------------------------------------------------------*/

$action = GETPOST('action');

if ($action == 'setvalue' && $user->admin)
{
	$sql	= 
		"SELECT 
			* 
		FROM 
			`llx_categorie_product` 
		INNER JOIN 
			`llx_product` ON(`llx_categorie_product`.`fk_product` = `llx_product`.`rowid`)
		WHERE
			`fk_categorie` = $_GET[id]
		ORDER BY 
			`description`";
	
	$product_query = $db->query($sql);	
		
	$num_product = $db->num_rows($product_query);	
}
else
{
	$id = GETPOST('id');
	
	$sql	= 
		"SELECT 
			* 
		FROM 
			`llx_categorie_product` 
		INNER JOIN 
			`llx_product` ON(`llx_categorie_product`.`fk_product` = `llx_product`.`rowid`)
		WHERE
			`fk_categorie` = $id
		ORDER BY 
			`description`";
	
	$product_query = $db->query($sql);	
		
	$num_product = $db->num_rows($product_query);	
	
	if($num_product > 0)
	{
		$c = 0;
		
		$linea = '';
		
		while ($c < $num_product)
		{
			$product = $db->fetch_array($product_query);
			
			if(isset($_POST[$product['rowid']]))
			{
				$price_ttc		= GETPOST('price_ttc_'.$product['rowid']);
				$price			= GETPOST('price_'.$product['rowid']);
				$price_min_ttc	= GETPOST('price_min_ttc_'.$product['rowid']);
				$price_min		= GETPOST('price_min_'.$product['rowid']);
				
				$sql	= 
					"UPDATE
						`llx_product` 
					SET 
						`price_ttc` 	= $price_ttc,
						`price` 		= $price,
						`price_min_ttc`	= $price_min_ttc,
						`price_min`		= $price_min
					WHERE 
						`rowid` = $product[rowid]";
						
				$linea .= '<tr '.$bc[$var].'>';
				$linea .= '<td>';
				$linea .= '<a href="/casta/ERP/htdocs/product/card.php?id='.$product['rowid'].'">';
				$linea .= '<img src="/casta/ERP/htdocs/theme/eldy/img/object_product.png" border="0" alt="" title="Mostrar producto "> ';
				$linea .= $product['ref'].'</a></td>';
				$linea .= '<td>'.$product['description'].'</td>';
				$linea .= '<td>'.formato_importe($price_ttc).'</td>';
				$linea .= '<td>'.formato_importe($price).'</td>';
				$linea .= '<td>'.formato_importe($product['tva_tx']).'</td>';
				$linea .= '<td>'.formato_importe($price_min_ttc).'</td>';
				$linea .= '<td>'.formato_importe($price_min).'</td>';
				$linea .= '</tr>';
				
				$db->query($sql);	
			}
			
			$c++;
		}
	}

	setEventMessage($langs->trans("SetupSaved"));
}

/*----------------------------------------------------------------------------
		SELECT de los origenes del pedido
----------------------------------------------------------------------------*/

$sql	= 
	"SELECT 
		* 
	FROM 
		`llx_categorie`
	ORDER BY
		`label`";

$categorie_query = $db->query($sql);	
	
$num_categorie	= $db->num_rows($categorie_query);		
			


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

if ($action != 'confirm')
{
	print '<br>';
	print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">';
	
		print '<input type="hidden" name="action" value="setvalue">';
		
		print '<table class="noborder" width="100%">';
		
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("PrecioDato").'</td>';
		print '<td>'.$langs->trans("PrecioValor").'</td>';
		print '</tr>';
		
		
		
		$var=!$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans("PrecioCategoria").'</td>';
		print '<td><select name="id" required>';
		print '<option></option>';
			if($num_categorie > 0)
			{
				$c = 0;
				while ($c < $num_categorie)
				{
					$categorie = $db->fetch_array($categorie_query);
					
					print '<option value="'.$categorie['rowid'].'">'.$categorie['label'].'</option>';
					
					$c++;
				}
			}
		print '</select></td>';
		print '</tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans("PrecioTipo").'</td>';
		print '<td>';
		print '<input type="radio" name="tipo" value="1" checked> Aumento<br>';
		print '<input type="radio" name="tipo" value="0"> Descuento';
		print '</td>';
		print '</tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans("PrecioValor").'</td>';
		print '<td>';
		print '<input type="text" name="valor" min="0" required>';
		print '</td>';
		print '</tr>';
		
		$var=!$var;
		print '<tr '.$bc[$var].'>';
		print '<td>'.$langs->trans("PrecioCondicion").'</td>';
		print '<td>';
		print '<input type="radio" name="condicion" value="1" checked> Porcentaje %<br>';
		print '<input type="radio" name="condicion" value="0"> Valor fijo';
		print '</td>';
		print '</tr>';
				
		print '</table>';
			
		print '<center><input type="submit" class="button" value="'.$langs->trans("PrecioCalcular").'"></center><br>';
	
	print '</form>';
}

if(isset($_GET['id']))
{
	print $langs->trans("ReceConfigDesc")."<br>\n";
	
	print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	
	print '<input type="hidden" name="action" value="confirm">';
	print '<input type="hidden" name="tipo" value="'.$_GET['tipo'].'">';
	print '<input type="hidden" name="valor" value="'.$_GET['valor'].'">';
	print '<input type="hidden" name="condicion" value="'.$_GET['condicion'].'">';
	print '<input type="hidden" name="id" value="'.$_GET['id'].'">';
	
	print '<table class="noborder" width="100%">';
	
	print '<tr class="liste_titre">';
	print '<td rowspan="2"></td>';
	print '<td rowspan="2">'.$langs->trans("PrecioRef").'</td>';
	print '<td rowspan="2">'.$langs->trans("PrecioEtiqueta").'</td>';
	print '<td colspan="2">'.$langs->trans("PrecioPrecioVenta").'</td>';
	print '<td colspan="2">'.$langs->trans("PrecioPrecioVentaSinIVA").'</td>';
	print '<td rowspan="2">'.$langs->trans("PrecioTasaIVA").'</td>';
	print '<td colspan="2">'.$langs->trans("PrecioPrecioVentaMin").'</td>';
	print '<td colspan="2">'.$langs->trans("PrecioPrecioVentaMinSINIVA").'</td>';
	print '</tr>';
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("PrecioNuevo").'</td>';
	print '<td>'.$langs->trans("PrecioAnterior").'</td>';
	print '<td>'.$langs->trans("PrecioNuevo").'</td>';
	print '<td>'.$langs->trans("PrecioAnterior").'</td>';
	print '<td>'.$langs->trans("PrecioNuevo").'</td>';
	print '<td>'.$langs->trans("PrecioAnterior").'</td>';
	print '<td>'.$langs->trans("PrecioNuevo").'</td>';
	print '<td>'.$langs->trans("PrecioAnterior").'</td>';
	print '</tr>';
	
	$condiciones = array(
		'tipo'		=> $_GET['tipo'],
		'valor'		=> $_GET['valor'],
		'condicion'	=> $_GET['condicion']
	);
	
	if($num_product > 0)
	{
		$c = 0;
		while ($c < $num_product)
		{
			$product = $db->fetch_array($product_query);
			
				$new_price_ttc		= calcular_precio($product['price_ttc'], $condiciones);
				$new_price			= calcular_precio($product['price'], $condiciones);
				$new_price_min_ttc	= calcular_precio($product['price_min_ttc'], $condiciones);
				$new_price_min		= calcular_precio($product['price_min'], $condiciones);
							
				$var=!$var;
				print '<tr '.$bc[$var].'>';
				print '<td><input type="checkbox" name="'.$product['rowid'].'" value="'.$product['rowid'].'" checked></td>';
				print '<td>';
				print '<a href="/casta/ERP/htdocs/product/card.php?id='.$product['rowid'].'">';
				print '<img src="/casta/ERP/htdocs/theme/eldy/img/object_product.png" border="0" alt="" title="Mostrar producto "> ';
				print $product['ref'].'</a></td>';
				print '<td>'.$product['description'].'</td>';
				print '<td><input name="price_ttc_'.$product['rowid'].'" value="'.formato_importe($new_price_ttc).'"></td>';
				print '<td>'.formato_importe($product['price_ttc']).'</td>';
				print '<td><input name="price_'.$product['rowid'].'" value="'.formato_importe($new_price).'"></td>';
				print '<td>'.formato_importe($product['price']).'</td>';
				print '<td>'.formato_importe($product['tva_tx']).'</td>';
				print '<td><input name="price_min_ttc_'.$product['rowid'].'" value="'.formato_importe($new_price_min_ttc).'"></td>';
				print '<td>'.formato_importe($product['price_min_ttc']).'</td>';
				print '<td><input name="price_min_'.$product['rowid'].'" value="'.formato_importe($new_price_min).'"></td>';
				print '<td>'.formato_importe($product['price_min']).'</td>';
				print '</tr>';
			
			$c++;
		}
	}
				
	print '</table>';
	
	print '<br><center><input type="submit" class="button" value="'.$langs->trans("PrecioConfirmar").'"></center>';
	
	print '</form>';

}


if ($action == 'confirm' && $user->admin)
{
	print $langs->trans("PrecioModificados")."<br><br>\n";
	
	print '<table class="noborder" width="100%">';
	
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("PrecioRef").'</td>';
	print '<td>'.$langs->trans("PrecioEtiqueta").'</td>';
	print '<td>'.$langs->trans("PrecioPrecioVenta").'</td>';
	print '<td>'.$langs->trans("PrecioPrecioVentaSinIVA").'</td>';
	print '<td>'.$langs->trans("PrecioTasaIVA").'</td>';
	print '<td>'.$langs->trans("PrecioPrecioVentaMin").'</td>';
	print '<td>'.$langs->trans("PrecioPrecioVentaMinSINIVA").'</td>';
	print '</tr>';
	
	print $linea;
	
	print '</table>';

}

llxFooter();
$db->close();