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

define('CODEBAR_HEIGHT', 20);

/**
 * Generador de códigos de barra
 * <pre>
 *  1: Code 11           51: Pharma One-Track         90: KIX Code
 *  2: Standard 2of5     52: PZN                      92: Aztec Code
 *  3: Interleaved 2of5  53: Pharma Two-Track         93: DAFT Code
 *  4: IATA 2of5         55: PDF417                   97: Micro QR Code
 *  6: Data Logic        56: PDF417 Trunc             98: HIBC Code 128
 *  7: Industrial 2of5   57: Maxicode                 99: HIBC Code 39
 *  8: Code 39           58: QR Code                 102: HIBC Data Matrix
 *  9: Extended Code 39  60: Code 128-B              104: HIBC QR Code
 * 13: EAN               63: AP Standard Customer    106: HIBC PDF417
 * 16: GS1-128           66: AP Reply Paid           108: HIBC MicroPDF417
 * 18: Codabar           67: AP Routing              112: HIBC Aztec Code
 * 20: Code 128          68: AP Redirection          128: Aztec Runes
 * 21: Leitcode          69: ISBN                    129: Code 23
 * 22: Identcode         70: RM4SCC                  130: Comp EAN
 * 23: Code 16k          71: Data Matrix             131: Comp GS1-128
 * 24: Code 49           72: EAN-14                  132: Comp Databar-14
 * 25: Code 93           75: NVE-18                  133: Comp Databar Ltd
 * 28: Flattermarken     76: Japanese Post           134: Comp Databar Ext
 * 29: Databar-14        77: Korea Post              135: Comp UPC-A
 * 30: Databar Limited   79: Databar-14 Stack        136: Comp UPC-E
 * 31: Databar Extended  80: Databar-14 Stack Omni   137: Comp Databar-14 Stack
 * 32: Telepen Alpha     81: Databar Extended Stack  138: Comp Databar Stack Omni
 * 34: UPC-A             82: Planet                  139: Comp Databar Ext Stack
 * 37: UPC-E             84: MicroPDF                140: Channel Code
 * 40: Postnet           85: USPS OneCode            141: Code One
 * 47: MSI Plessey       86: UK Plessey              142: Grid Matrix
 * 49: FIM               87: Telepen Numeric
 * 50: Logmars           89: ITF-14
 * </pre>
 * @todo Por terminar
 * @author alexl
 *
 */
class CodebarLib {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	private $path;

	/**
	 * Constructor
	 * @return CodebarLib
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		$this->path = $this->obj->config->item('codebar.path');

		log_message('debug', 'Codebar Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Genera el código de barras
	 * @param string $code Código a generar
	 * @param int $type tipo de código
	 * @param int $height Tamaño
	 * @return array 'FILE' => Fichero generaro, 'URL' => url generada
	 */
	private function _generate($code, $type, $height = null)
	{
		if (!isset($height)) $height = CODEBAR_HEIGHT; 
		$name =  "{$code}-{$height}-{$type}.png";
		$fout = DIR_CODEBAR_PATH . $name;
		if (!file_exists($fout))
		{
			$cmd = "{$this->path} -o \"{$fout}\" --height={$height} -d \"{$code}\" --barcode={$type}";
			set_time_limit(0);
			$r = system($cmd, $result);
		}
		return array('FILE' => $fout, 'URL' => base_url() . URL_TEMP_PATH . $name);
	}

	/**
	 * Genera el código de barras en un fichero
	 * @param string $code Código a generar
	 * @param int $type tipo de código
	 * @param int $height Tamaño
	 * @return string Fichero generado
	 */
	function file($code, $type, $height = null)
	{
		$res = $this->_generate($code, $type, $height);
		return $res['FILE'];
	}

	/**
	 * Genera el código de barras en una URL
	 * @param string $code Código a generar
	 * @param int $type tipo de código
	 * @param int $height Tamaño
	 * @return string Fichero generado
	 */
	function url($code, $type, $height = null)
	{
		$res = $this->_generate($code, $type, $height);
		return $res['URL'];
	}

	/**
	 * Genera el código de barras en un TAG HTML IMG
	 * @param string $code Código a generar
	 * @param int $type tipo de código
	 * @param int $height Tamaño
	 * @return string Fichero generado
	 */
	function image($code, $type, $height = null)
	{
		$res = $this->_generate($code, $type, $height);
		return "<img src=\"{$res['URL']}\" />";
	}
	
	/**
	 * Genera el código de barras y lo muestra en el navegado directamente como URL
	 * @param string $code Código a generar
	 * @param int $type tipo de código
	 * @param int $height Tamaño
	 */
	function redirect($code, $type, $height = null)
	{
		$res = $this->_generate($code, $type, $height);
		redirect($res['URL']);
	}

	/**
	 * Genera el código de barras en un fichero y lo muestra en el navegador como stream imagen
	 * @param string $code Código a generar
	 * @param int $type tipo de código
	 * @param int $height Tamaño
	 */
	function out($code, $type, $height = null)
	{
		$res = $this->_generate($code, $type, $height);
		header("Content-type: image/png");
		#header("Content-Disposition: attachment; filename={$name}");
		#header('Content-Length: ' . filesize($res['FILE']));
		#header("Pragma: no-cache");
		#header("Expires: 0");
		//unlink($fout);
		#echo $res['FILE']; return;
		#$data = file_get_contents($res['FILE']);
		#echo $data;
		readfile($res['FILE']);
		return;
	}

}

/* End of file CodebarLib.php */
/* Location: ./system/libraries/CodebarLib.php */