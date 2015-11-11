<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/discount.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/fe_mx/librerias/facturaElectronica.class.php';

$langs->load("cae");
$langs->load("bills");

$id     	= GETPOST('facid','int');
$ref		= GETPOST('ref','alpha');
$action     = GETPOST('action');

// Security check
if ($user->societe_id) $socid=$user->societe_id;
	$result = restrictedArea($user, 'facture', $id);
	
	$object = new Facture($db);
	
	 
if ($action == 'sap' && $user->rights->facture->creer) {
	$fe_mx = new facturaElectronica($db);
	
	/*----------------------------------------------------------------------------
		01 - Busco la factura por id o referencia
	----------------------------------------------------------------------------*/
		
	if($ref == ''){
		$sql_fac = 
		"SELECT 
			* ,
			`llx_facture`.`datec` as `datecf` 
		FROM 
			`llx_facture` 
		WHERE 
			`llx_facture`.`rowid` = $id";	
	} else {
		$sql_fac = 
		"SELECT 
			* ,
			`llx_facture`.`datec` as `datecf` 
		FROM 
			`llx_facture` 
		WHERE 
			`llx_facture`.`facnumber` = '$ref'";
	}
	$resql_fac = $db->query($sql_fac);	
	$numr_fac = $db->num_rows($resql_fac);
	
	/*----------------------------------------------------------------------------
		02 - Adapto los valores de la factura
	----------------------------------------------------------------------------*/
	
	if($numr_fac > 0){
		$fac_array = $db->fetch_array($resql_fac);
		$nro_doc	= ereg_replace("[^0-9]", "", $fac_array['siren']);
		$fecha_cbte	= date("Ymd");
		
		if($fac_array['type'] == 2){
			$tipo_cbte = 3; //Nota de credito A
			$fac_array['total_ttc']	= $fac_array['total_ttc'] * -1;
			$fac_array['total']		= $fac_array['total'] * -1;
			$fac_array['tva']		= $fac_array['tva'] * -1;
		} else {
			$tipo_cbte = 1; //Factura A 
		}
	}
	
	/*---------------------------------------------------------------------------------
			TIMBRADO 
	---------------------------------------------------------------------------------*/
	
	// Array con la factura
	$encabezado = array(
		'fecha'				=> $fe_mx->formato_fecha($fac_array['datecf']),
		'formaDePago'		=> $fe_mx->formaDePago($fac_array['fk_cond_reglement'], $langs),
		'condicionesDePago'	=> $fe_mx->condicionesDePago($fac_array['fk_mode_reglement'], $langs),	
	);
	
	$receptor	= $fe_mx->receptor($fac_array['fk_soc']);
	$pais		= explode(":", MAIN_INFO_SOCIETE_COUNTRY);
	$provincia	= $fe_mx->provincia(MAIN_INFO_SOCIETE_STATE);
	$concepto	= $fe_mx->concepto($fac_array['rowid']);
	
	
		
	$array_factura = array(
		'Encabezado' => array(
			'serie'				=> '',
			'fecha'				=> $encabezado['fecha'],							
			'folio'				=> '',
			'tipoDeComprobante'	=> 'ingreso',
			'formaDePago'		=> $encabezado['formaDePago'],
			'metodoDePago'		=> 'Transferencía Electrónica',
			'condicionesDePago'	=> $encabezado['condicionesDePago'],
			'NumCtaPago'		=> 'No identificado',
			'subTotal'			=> round($fac_array['total'], 2),	
			'descuento'			=> '0.00',
			'total'				=> round($fac_array['total_ttc'], 2),
			'Moneda'			=> MAIN_MONNAIE,
			'noCertificado'		=> '',
			'LugarExpedicion'	=> ''.$provincia.', '.$pais[2].'.'
		),
		'Receptor' 			=> $receptor['Receptor'],
		'Domicilio' 		=> $receptor['Domicilio'],
		'DatosAdicionales'	=> $receptor['DatosAdicionales'],
		'Emisor' => array(
			'rfc'				=> MAIN_INFO_TVAINTRA,
			'nombre'			=> MAIN_INFO_SOCIETE_ADDRESS,
			'RegimenFiscal'		=> 'REGIMEN GENERAL DE LEY'
		),
		'ExpedidoEn' => array(
				'calle'				=> MAIN_INFO_SOCIETE_ADDRESS,
				'noExterior'		=> '',
				'noInterior'		=> '',
				'colonia'			=> '',
				'localidad'			=> '',
				'municipio'			=> '',
				'estado'			=> $provincia,
				'pais'				=> $pais[2],
				'codigoPostal'		=> MAIN_INFO_SOCIETE_ZIP,
		),
		'Datos Adicionales' => array(
			'tipoDocumento'		=> 'Factura',
			'observaciones'		=> $fac_array['note_public']
		),
	);	
	/*
	foreach ($concepto as $valores) {
		$array_factura['Concepto'][] = $valores;
	}
	*/	
	// Obtengo resultados, ver como mejorar
	$mensaje = $fe_mx->timbrado($array_factura);
	
	if($mensaje['resultado']){
		foreach ($mensaje['archivo'] as $extencion => $archivo) {
			$link[$extencion] = DOL_URL_ROOT.FE_MX_URL.$archivo;
		}		
		
		$sap = array(
			'id_facture' 	=> $id,
			'sap'			=> $mensaje['UUID'],
			'xml'			=> $link['xml'],
			'pdf'			=> $link['pdf'],
			'png'			=> $link['png'],
			'date_add'		=> date('Y/m/d H:i:s'),
		);
			
		$fe_mx->insert($sap);
		
	}	
	
}
 
/* *************************************************************************** */
/*                                                                             */
/*		VISTA                                                     			   */
/*                                                                             */
/* *************************************************************************** */

llxHeader('', $langs->trans("CAE"), "Facture");

$form 			= new Form($db);
$formcompany 	= new FormCompany($db);
$contactstatic	= new Contact($db);
$userstatic		= new User($db);



/*----------------------------------------------------------------------------
		Contenido
----------------------------------------------------------------------------*/

if ($id > 0 || ! empty($ref)){
	if ($object->fetch($id, $ref) > 0){
		$object->fetch_thirdparty();
		$head = facture_prepare_head($object);
		dol_fiche_head($head, 'sap', $langs->trans('sap'), 0, 'bill');
		
		if($mensaje['resultado']){
			foreach ($link as $value) {
				echo '<a href="'.$value.'" target="_blank">'.$mensaje['UUID'].'</a>';	
			}
		}else{
			echo $mensaje['error'];
		};
	
		if($cae_array['cae'] == ''){
			print '<br><center><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?facid=' . $object->id . '&amp;action=sap">' . $langs->trans('SapObtener') . '</center></a>';	
		} 
		
	} else {
		// Record not found
		print "ErrorRecordNotFound";
	}
 
}

llxFooter();
$db->close();