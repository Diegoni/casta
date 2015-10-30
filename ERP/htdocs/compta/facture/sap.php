<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/discount.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/fe_mx/librerias/facturaElectronica.class.php';

$langs->load("cae");

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
		INNER JOIN 
			`llx_societe` ON(`llx_facture`.`fk_soc` = `llx_societe`.`rowid`) 
		WHERE 
			`llx_facture`.`rowid` = $id";	
	} else {
		$sql_fac = 
		"SELECT 
			* ,
			`llx_facture`.`datec` as `datecf` 
		FROM 
			`llx_facture` 
		INNER JOIN 
			`llx_societe` ON(`llx_facture`.`fk_soc` = `llx_societe`.`rowid`)  
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
	$fecha = $fe_mx->formato_fecha($fac_array['datecf']);
	
	$array_factura = array(
		'Encabezado' => array(
			'serie'				=> '',
			//'fecha'				=> $fecha,									//'2015-10-28T18:39:35',
			'fecha'				=> '2015-10-28T18:39:35',
			'folio'				=> '',
			'tipoDeComprobante'	=> 'ingreso',
			'formaDePago'		=> 'PAGO EN UNA SOLA EXHIBICIÓN',
			'metodoDePago'		=> 'Transferencía Electrónica',
			'condicionesDePago'	=> 'Contado',
			'NumCtaPago'		=> 'No identificado',
			//'subTotal'			=> round($fac_array['total'], 2),			//'10.00',
			'subTotal'			=> '10.00',
			'descuento'			=> '0.00',
			//'total'				=> round($fac_array['total_ttc'], 2) + 1,	//'11.60',
			'total'				=> '11.60',
			'Moneda'			=> 'MXN',
			'noCertificado'		=> '',
			'LugarExpedicion'	=> 'Nuevo León, México.'
		)
	);
	
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