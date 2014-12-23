<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Helpers
 * @category	Heleprs
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Comprueba si un texto está en formato UTF8
 *
 * @param string $string Texto a comprobar
 * @return bool
 */
function detectUTF8($string)
{
	return preg_match('%(?:
    [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
    |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
    |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
    |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
    |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
    |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
    |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
    )+%xs', $string);
}

/**
 * Codifica el texto en el formato que usa el sistema
 *
 * @param mixed $var Texto a codificar
 * @return mixed
 */
function string_encode($var)
{
	switch (gettype($var)) {
		case 'string':
			if (strpos($var, '{dt}') === 0)
			{
				$d = substr($var, 4);
				return (strlen($d)==0)?null:strtotime($d);
			}
			if (detectUTF8($var))
			{
				return trim($var);
			}
			else
			{
				return trim(utf8_encode($var));
			}
		case 'array':
			foreach($var as $k => $v)
			{
				$var[$k] = string_encode($v);
			}
			return $var;
		default:
			return $var;
	}
}

/**
 * deCodifica el texto en el formato que usa el sistema
 *
 * @param mixed $var Texto a decodificar
 * @return mixed
 */
function string_decode($var)
{
	switch (gettype($var)) {
		case 'string':
			if (!detectUTF8($var))
			{
				return $var;
			}
			else
			{
				return utf8_decode($var);
			}
		case 'array':
			foreach($var as $k => $v)
			{
				$var[$k] = string_decode($v);
			}
			return $var;
		default:
			return $var;
	}
}
/* End of file utf8_helper.php */
/* Location: ./system/application/helpers/utf8_helper.php */