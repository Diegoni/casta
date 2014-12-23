<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Acceso a ASM
 * @author alexl
 *
 */
class ASM {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Último error
	 * @var string
	 */
	private $_error;
	/**
	 * URL de la función de petición de envío
	 * @var string
	 */
	private $urlenviar;
	/**
	 * URL de la función de petición de etiqueta
	 * @var string
	 */
	private $urletiqueta;
	/**
	 * UID de identificación en el servicio
	 * @var string
	 */
	private $uid;

	/**
	 * Constructor
	 * @return ASM
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->uid = $this->obj->config->item('bp.asm.uid');
		$this->urlenviar = $this->obj->config->item('bp.asm.url.enviar');
		$this->urletiqueta = $this->obj->config->item('bp.asm.url.etiqueta');

		log_message('debug', 'ASM Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Realiza la llamada SOAP
	 * @param string $url URL del servicio
	 * @param string $function Nombre de la función SOAP
	 * @param array $params Parámtros a pasar ala llamara
	 * @return FALSE: ha habido error, array: Respuesta
	 */
	private function soap_call($url, $function, $params)
	{
		$values = '';
		foreach ($params as $key => $value) 
		{
			$values .= "<{$key}>{$value}</{$key}>";
		}

		$soap_request = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
		<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
		<soap:Body>
		<{$function} xmlns=\"http://www.asmred.com/\">
		{$values}
		</{$function}>
		</soap:Body>
		</soap:Envelope>";

		$header = array(
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: \"http://www.asmred.com/{$function}\"",
			"Content-length: ".strlen($soap_request),
		);

		$soap_do = curl_init();
		curl_setopt($soap_do, CURLOPT_URL, $url );
		curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($soap_do, CURLOPT_TIMEOUT,        60);
		curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($soap_do, CURLOPT_POST,           TRUE );
		curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_request);
		curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);

		$res = curl_exec($soap_do);

		if ( $res === false) 
		{
			$err = $this->_error = 'Curl error: ' . curl_error($soap_do);
			curl_close($soap_do);
			return FALSE;
		} 
		curl_close($soap_do);

		$this->obj->load->library('Utils');
		return $this->obj->utils->xml2array($res);
	}

	/**
	 * Genera la etiqueta para ASM
	 * @param int $id Id del envío ASM
	 * @return FALSE: ha habido error, string: nombre del fichero PDF con la etiqueta
	 */
	function etiqueta($id)
	{
		$params = array (
			'codigo' => $id,
			'tipoEtiqueta' => 'PDF'
			);

		#var_dump($this->urletiqueta); die();
		$res = $this->soap_call($this->urletiqueta, 'EtiquetaEnvio', $params);
		#var_dump($res); die();
		#echo '<pre>'; print_r($res); echo '</pre>'; die();
		if (isset($res['soap:Envelope']['soap:Body']['EtiquetaEnvioResponse']['EtiquetaEnvioResult']['base64Binary']))
		{
			$name = time() . '.pdf';
			$this->obj->load->library('HtmlFile');
			$fout = $this->obj->htmlfile->pathfile($name);
			$data = $res['soap:Envelope']['soap:Body']['EtiquetaEnvioResponse']['EtiquetaEnvioResult']['base64Binary'];
			#var_dump(base64_decode($data)); die();
			file_put_contents($fout, base64_decode($data));
			return $name;
		}
		$this->_error = isset($res['soap:Envelope']['soap:Body']['EtiquetaEnvioResponse']['EtiquetaEnvioResult']['Servicios']['Envio']['Errores']['Error'])?
			$res['soap:Envelope']['soap:Body']['EtiquetaEnvioResponse']['EtiquetaEnvioResult']['Servicios']['Envio']['Errores']['Error']:
				$this->_error = $this->obj->lang->line('asm-no-etiqueta');
		return FALSE;
	}

	/**
	 * Realiza una petición de envío de paquete
	 * @param string $ref Referencia del envío
	 * @param array $direccion Dirección de envío
	 * @param array $cliente Datos del cliente
	 * @param string $email Email del cliente
	 * @param string $telefono Teléfono del cliente
	 * @param int $dia Fecha del envío. Por defecto hoy
	 * @param float $reembolso Valor del reembolso
	 * @param string $observaciones Observaciones sobre el envío
	 * @param int $bultos Número de bultos
	 * @return FALSE: error, string Id de ASM del envío
	 */
	function enviar($ref, $direccion, $cliente, $email, $telefono, $dia = null, $reembolso = null, $observaciones = '', $bultos = 1, &$resultado = null)
	{
		$this->obj->load->model('proveedores/m_direccion');
		$direccion['cTitular'] = trim($direccion['cTitular']);
		$direccion['cPoblacion'] = isset($direccion['cPoblacion'])?trim($direccion['cPoblacion']):'';
		if (empty($direccion['cPoblacion']) && !empty($direccion['cRegion'])) $direccion['cPoblacion'] = $direccion['cRegion'];
		if (empty($direccion['cTitular']))
			$direccion['cTitular'] = format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']);

		$recoger = $this->obj->m_direccion->load($this->obj->config->item('bp.ventas.direcciones.recoger'));
		$recoger['cPoblacion'] = trim($recoger['cPoblacion']);
		if (empty($recoger['cPoblacion']) && !empty($recoger['cRegion'])) $recoger['cPoblacion'] = $recoger['cRegion'];

		$data = array (
			'uid' 				=> $this->uid,
			'desde'				=> $this->obj->config->item('bp.ventas.hora.desde'),
			'hasta'				=> $this->obj->config->item('bp.ventas.hora.hasta'),
			'direccionrecoger' 	=> $recoger,
			'telefonorecoger' 	=> $this->obj->config->item('bp.ventas.telefono.recoger'),
			'emailrecoger' 		=> $this->obj->config->item('bp.ventas.email.recoger'),
			'telefonoenviar' 	=> $telefono,
			'emailenviar'		=> $email,
			'direccionenviar'	=> $direccion,
			'observaciones'		=> $observaciones,
			'dia'				=> isset($dia)?$dia:time(),
			'ref'				=> $ref,
			'bultos'			=> $bultos,
			'reembolso'			=> $reembolso
		);

		if (isset($resultado))
			$resultado = implode(', ', $direccion) . '<br/>' . $telefono . '<br/>' . $observaciones . (($reembolso>0)?('<br/>' . $reembolso):'');
		
		$xml = $this->obj->load->view('sys/asm_envio', $data, TRUE);

		$xml = preg_replace('/>[\s\n]*</', '><', $xml);

		$res = $this->soap_call($this->urlenviar, 'GrabaServicios', array('docIn' => $xml));

		if (isset($res['soap:Envelope']['soap:Body']['GrabaServiciosResponse']['GrabaServiciosResult']['Servicios']['Envio_attr']['codbarras']))
		{
			return $res['soap:Envelope']['soap:Body']['GrabaServiciosResponse']['GrabaServiciosResult']['Servicios']['Envio_attr']['codbarras'];
		}
		#echo '<pre>'; print_r($res); echo '</pre>'; die();

		$this->_error = ($res['soap:Envelope']['soap:Body']['GrabaServiciosResponse']['GrabaServiciosResult']['Servicios']['Envio']['Errores']['Error']);
		return FALSE;
	}

	/**
	 * Último error generado
	 * @return string
	 */
	function get_error()
	{
		return $this->_error;
	}
}
/* End of file Scribd.php */
/* Location: ./system/libraries/scribd.php */