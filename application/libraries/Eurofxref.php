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
 * URL del BCE para el cambio diario
 * @var string
 */
define('URL_ECB_DAILY', 'http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml');
/**
 * URL del BCE para el histórico del cambio
 * @var string
 */
define('URL_ECB_OLD', 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist-90d.xml');

/**
 * Cambios de divisa del BCE
 * http://www.ecb.int/stats/exchange/eurofxref/html/index.en.html
 * @author alexl
 *
 */
class Eurofxref {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Out
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		log_message('debug', 'Eurofxref Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Actualiza el cambio de la divisa
	 * @param int $fecha Fecha
	 * @return array, null si no hay cambio en la fecha dada
	 */
	function get($fecha = null)
	{
		if (empty($fecha))
		{
			$url = URL_ECB_DAILY;
			$data = file_get_contents($url);
		}
		else
		{
			$url = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist-90d.xml';
			$url = URL_ECB_OLD;
			$data = file_get_contents($url);
			$fecha = date('Y-m-d', $fecha);			
			$regex = '/<Cube.time=\"' . $fecha . '\"\>(.*)<\/Cube\>/';
			$res = preg_match_all($regex, $data, $ar);
			if (!isset($ar[1][0])) return null;
			$data = str_replace('"',"'", $ar[1][0]);
		}
		$regex = "/currency='([[:alpha:]]+)'.rate='([[:graph:]]+)'/";
		$res = preg_match_all($regex, $data, $ar);

		$changes = array();
		foreach($ar[1] as $k => $v)
		{
			$value = (float) $ar[2][$k];
			$changes[$v] = $value;
		}
		foreach($changes as $k => $currency)
		{
			$changes[$k] = array(
				'fCompra' => $currency,
				'fVenta' => (float)(($currency != 0)?(1 / $currency):0)
			);
		}
		return $changes;
	}
}

/* End of file eurofxref.php */
/* Location: ./system/libraries/eurofxref.php */