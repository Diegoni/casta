<?php
/* Copyright (C) 2005      Patrick Rouillon     <patrick@rouillon.net>
 * Copyright (C) 2005-2009 Destailleur Laurent  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011-2012 Philippe Grand       <philippe.grand@atoo-net.com>
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
 *       \file       htdocs/compta/facture/contact.php
 *       \ingroup    facture
 *       \brief      Onglet de gestion des contacts des factures
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/discount.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/facturaelectronica/cae/class_factura_electronica.php';

$langs->load("cae");

$id     	= GETPOST('facid','int');  // For backward compatibility
$ref		= GETPOST('ref','alpha');
$action     = GETPOST('action');

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'facture', $id);

$object = new Facture($db);


/*
 * Ajout d'un nouveau contact
 */
 
 
if ($action == 'cae' && $user->rights->facture->creer) {
//TMS	
	$factura_e = new factura_electronica($db);
		
	if($ref == '')
	{
		$sql_fac = 
		"SELECT 
			* 
		FROM 
			`llx_facture` 
		INNER JOIN 
			`llx_societe` ON(`llx_facture`.`fk_soc` = `llx_societe`.`rowid`) 
		WHERE 
			`llx_facture`.`rowid` = $id";	
	}
	else
	{
		$sql_fac = 
		"SELECT 
			* 
		FROM 
			`llx_facture` 
		INNER JOIN 
			`llx_societe` ON(`llx_facture`.`fk_soc` = `llx_societe`.`rowid`)  
		WHERE 
			`llx_facture`.`facnumber` = '$ref'";
	}
							
	$resql_fac = $db->query($sql_fac);	
									
	$numr_fac = $db->num_rows($resql_fac);
	
	if($numr_fac > 0)
	{
		$fac_array = $db->fetch_array($resql_fac);
		
		$nro_doc	= ereg_replace("[^0-9]", "", $fac_array['siren']);
		//$fecha_cbte	= date("Ymd", strtotime($fac_array['datec']));
		$fecha_cbte	= date("Ymd");
		
		if($fac_array['type'] == 2)
		{
			$tipo_cbte = 3; //Nota de credito A
			
			$fac_array['total_ttc']	= $fac_array['total_ttc'] * -1;
			$fac_array['total']		= $fac_array['total'] * -1;
			$fac_array['tva']		= $fac_array['tva'] * -1;
		}
		else
		{
			$tipo_cbte = 1; //Factura A 
		}
		 
	}
	
	
				
	$factura = array(
		'tipo_cbte' 		=> 1, 
		'punto_vta' 		=> 1,
		
		'concepto'			=> 1,						# 1: productos, 2: servicios, 3: ambos
		'tipo_doc'			=> 80,						# 80: CUIT, 96: DNI, 99: Consumidor Final
		'nro_doc'			=> $nro_doc,	# 0 para Consumidor Final (<$1000)
				
		'imp_total'			=> round($fac_array['total_ttc'], 2) + 3,	# total del comprobante
		'imp_tot_conc'		=> "2.00",								# subtotal de conceptos no gravados
		'imp_neto'			=> round($fac_array['total'], 2),		# subtotal neto sujeto a IVA
		'imp_iva'			=> round($fac_array['tva'], 2),			# subtotal impuesto IVA liquidado
		'imp_trib'			=> "1.00",			# subtotal otros impuestos
		 /*
		'imp_total'			=> "179.25",		# total del comprobante
		'imp_tot_conc'		=> "2.00",			# subtotal de conceptos no gravados
		'imp_neto'			=> "150.00",		# subtotal neto sujeto a IVA
		'imp_iva'			=> "26.25",			# subtotal impuesto IVA liquidado
		'imp_trib'			=> "1.00",			# subtotal otros impuestos
		 */ 
		'imp_op_ex'			=> "0.00",			# subtotal de operaciones exentas
		'fecha_cbte'		=> $fecha_cbte,
		'fecha_venc_pago'	=> "",				# solo servicios
		# Fechas del per�odo del servicio facturado (solo si concepto = 1?)
		'fecha_serv_desde'	=> "",
		'fecha_serv_hasta'	=> "",
		'moneda_id'			=> "PES",			# no utilizar DOL u otra moneda 
		'moneda_ctz'		=> "1.000",			# (deshabilitado por AFIP)
	 );	
	
	foreach ($factura as $key => $value) 
	{
		echo $key.' '.$value.'<br>';
	}    
		    
	$cae = $factura_e->obtener_cae($factura);
	
	$cae['id_facture'] = $id;
		
	$factura_e->insert($cae);
	
	setEventMessage($langs->trans("CaeGuardado"));
}
 
 
 
 
/*
 * View
 */

llxHeader('', $langs->trans("CAE"), "Facture");

$form = new Form($db);
$formcompany = new FormCompany($db);
$contactstatic=new Contact($db);
$userstatic=new User($db);

if($ref == '')
{
	$sql_cae = "SELECT * FROM `tms_cae` WHERE id_facture = $id ORDER BY id_cae DESC";	
			
}
else
{
	$sql_facture = "SELECT * FROM `llx_facture` WHERE facnumber = '$ref'";
	
	$resql_facture = $db->query($sql_facture);	
								
	$numr_facture = $db->num_rows($resql_facture);
	
	if($numr_facture > 0)
	{
		$facture_array = $db->fetch_array($resql_facture);
		
		$sql_cae = "SELECT * FROM `tms_cae` WHERE id_facture = $facture_array[rowid]";
		
	}
}


							
$resql_cae = $db->query($sql_cae);	
								
$numr_cae = $db->num_rows($resql_cae);

/* *************************************************************************** */
/*                                                                             */
/* Mode vue et edition                                                         */
/*                                                                             */
/* *************************************************************************** */

if ($id > 0 || ! empty($ref))
{

	if ($object->fetch($id, $ref) > 0)
	{
		$object->fetch_thirdparty();

		$head = facture_prepare_head($object);

		dol_fiche_head($head, 'cae', $langs->trans('cae'), 0, 'bill');

		/*
		 *   Facture synthese pour rappel
		 */
				 
		print '<table class="border" width="100%">';
		
		$linkback = '<a href="'.DOL_URL_ROOT.'/compta/facture/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

		// Ref
		print '<tr><td width="20%">'.$langs->trans('Ref').'</td>';
		print '<td colspan="3">';
		$morehtmlref='';
		$discount=new DiscountAbsolute($db);
		$result=$discount->fetch(0,$object->id);
		if ($result > 0)
		{
			$morehtmlref=' ('.$langs->trans("CreditNoteConvertedIntoDiscount",$discount->getNomUrl(1,'discount')).')';
		}
		if ($result < 0)
		{
			dol_print_error('',$discount->error);
		}
		print $form->showrefnav($object, 'ref', $linkback, 1, 'facnumber', 'ref', $morehtmlref);
		print '</td></tr>';
		
		if($numr_cae > 0)
		{
			$cae_array = $db->fetch_array($resql_cae);
			//print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?facid=' . $object->id . '&amp;action=cae" readonly>' . $langs->trans('CAE') . '</a></div>';	
		}
		else
		{
			//print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?facid=' . $object->id . '&amp;action=cae">' . $langs->trans('CAE') . '</a></div>';
		}
		
		// Nro de Cae
		print '<tr><td width="20%">';
        print $langs->trans('Cae');
        print '</td><td colspan="3">';
        print $cae_array['cae'];
		print '</td></tr>';		
		
		// Resultado
		print '<tr><td width="20%">';
        print $langs->trans('CaeResultado');
        print '</td><td colspan="3">';
        print $cae_array['resultado'];
		print '</td></tr>';
		
		// cbtenro
		print '<tr><td width="20%">';
        print $langs->trans('Caecbtenro');
        print '</td><td colspan="3">';
        print $cae_array['cbtenro'];
		print '</td></tr>';
		
		// vencimiento
		print '<tr><td width="20%">';
        print $langs->trans('Caevencimiento');
        print '</td><td colspan="3">';
        print $cae_array['vencimiento'];
		print '</td></tr>';
		
		// emisiontipo
		print '<tr><td width="20%">';
        print $langs->trans('Caeemisiontipo');
        print '</td><td colspan="3">';
        print $cae_array['emisiontipo'];
		print '</td></tr>';
				
		// reproceso
		print '<tr><td width="20%">';
        print $langs->trans('Caereproceso');
        print '</td><td colspan="3">';
        print $cae_array['reproceso'];
		print '</td></tr>';
		
		// errmsg
		print '<tr><td width="20%">';
        print $langs->trans('Caeerrmsg');
        print '</td><td colspan="3">';
        print $cae_array['errmsg'];
		print '</td></tr>';
		
		// obs
		print '<tr><td width="20%">';
        print $langs->trans('Caeobs');
        print '</td><td colspan="3">';
        print $cae_array['obs'];
		print '</td></tr>';
		
		// error
		print '<tr><td width="20%">';
        print $langs->trans('Caeerror');
        print '</td><td colspan="3">';
        print $cae_array['error'];
		print '</td></tr>';		
		
		print "</table>";
		
		if($cae_array['cae'] == '')
		{
			print '<br><center><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?facid=' . $object->id . '&amp;action=cae">' . $langs->trans('CaeObtener') . '</center></a>';	
		}
		else
		{
			//despues borrar
			print '<br><center><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?facid=' . $object->id . '&amp;action=cae">' . $langs->trans('CaeObtener') . '</center></a>';
		}
	}
	else
	{
		// Record not found
		print "ErrorRecordNotFound";
	}
 
}


llxFooter();
$db->close();
