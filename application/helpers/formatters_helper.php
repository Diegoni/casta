<?php

//if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Codifica en latin1 desde UTF8
 * http://www.fischerlaender.net/wp-content/uploads/Latin1UTF8
 *
 * @param string $text Texto en UTF9
 * @return string
 */
function mixed_to_latin1($text) {
	static $utf8_to_latin1;
	static $latin1_to_utf8;

	if (!$utf8_to_latin1) {
		for ($i = 32; $i <= 255; $i++) {
			//$this->latin1_to_utf8[chr($i)] = utf8_encode(chr($i));
			$utf8_to_latin1[utf8_encode(chr($i))] = chr($i);
		}
	}

	foreach ($utf8_to_latin1 as $key => $val) {
		$text = str_replace($key, $val, $text);
	}
	return $text;
}

/**
 * Formatea una fecha para ser usada en MSSQL
 *
 * @param date $date Fecha a formatear
 * @param bool $keys true: formato con llave {}, false: formato ISO
 * @return string
 */
function format_mssql_date($date, $keys = TRUE) {
	//echo '<pre>format_mssql_date ' . date("Y-m-d\TH:i:s", $date) . '</pre>';
	//return '{ts \'' . date("Y-m-d H:i:s", $date) . '\'}';
	if ($keys) {
		#return '{ts \'' . date("Y-m-d H:i:s", $date) . '\'}';
		return '\'' . date("Y-m-d H:i:s", $date) . '\'';
	} else {
		return date("Y-m-d\TH:i:s", $date);
	}
}

/**
 * Formatea una fecha para ser usada en MSSQL
 *
 * @param date $date Fecha a formatear
 * @param bool $keys true: formato con llave {}, false: formato ISO
 * @return string
 */
function format_mssql_datetime($date, $keys = TRUE) {
	//echo '<pre>format_mssql_date ' . date("Y-m-d\TH:i:s", $date) . '</pre>';
	if ($keys) {
		#return '{ts \'' . date("Y-m-d H:i:s", $date) . '\'}';
		return '\'' . date("Y-m-d H:i:s", $date) . '\'';
	} else {
		return date("Y-m-d\TH:i:s", $date);
	}
}

/**
 * Convierte una fecha a PHP
 *
 * @param string $date
 * @return date
 */
function to_date($date) 
{
	//@todo basura a limpiar
	if (is_numeric($date)) return $date;
	$d = preg_split('/T/', $date);
	if (isset($d[1])) 
	{
		$d = preg_split('/[\/.-]/', $d[0]);
		return mktime(0, 0, 0, $d[1], $d[2], $d[0]);
	} 
	else 
	{
		$s = preg_split('/\s/', $d[0]);
		$d = preg_split('/[\/.-]/', $s[0]);
		#print_r($s); print_r($d);
		if (isset($s[1]))
		{
			$h = preg_split('/[\:]/', $s[1]);				 
		}
		else 
			$h =array(0, 0, 0);
		if (strlen($d[0]) == 4)
		{
			$t = $d[0];
			$d[0] = $d[2];
			$d[2] = $t; 
		}
		#print_r($h);
		return mktime($h[0], $h[1], $h[2], $d[1], $d[0], $d[2]);
	}

	return mktime(0, 0, 0, $d[1], $d[0], $d[2]);
}

/**
 * Formatea la salida de un precio
 *
 * @param double $price Valor
 * @return string
 */
function format_price($price, $symbol = TRUE) 
{
	#var_dump($price, (float)$price);
	$price = (float)$price;
	if (!isset($price))
		$price = 0;
	$obj = & get_instance();
	return (($symbol) ? $obj->config->item('bp.currency.symbol_left') : '') .
	number_format($price,
	$obj->config->item('bp.currency.decimals'),
	$obj->config->item('bp.currency.dec_points'),
	$obj->config->item('bp.currency.thousands_sep')) .
	(($symbol) ? $obj->config->item('bp.currency.symbol_right') : '');
}

/**
 * Formatea un texto como un botón de acción
 * @param string $texto Texto a mostrar
 * @param string $url Comando
 * @return string
 */
function format_button_cmd($text, $url) {
	return format_enlace_cmd('<span class="button">' . $text . '</span>', $url);
}

/**
 * Cálculo del margen de un producto
 * @param float $venta Precio de venta (sin IVA)
 * @param float $coste Precuo de coste (sin IVA)
 * @return float
 */
function format_margen($venta, $coste) {
	if ($venta < 0)
	$venta = -$venta;
	if ($coste < 0)
	$coste = -$coste;
	$m = ($venta == 0) ? 0 : (1 - ($coste / $venta));
	$m *= 100;
	return $m;
}

/**
 * Formatea la salida de una línea de texto
 * http://emilio.aesinformatica.com/2008/06/20/cortar-caracteres-de-una-cadena-en-php/
 * @param string $title Texto
 * @param int $len Longitud máxima
 * @return string
 */
function format_title($text, $max) {
	$text = trim($text);
	$length = strlen($text);

	if ($length > $max) {
		$tmptext = explode(' ', $text);
		$text = '';

		for ($i = 0; $max > strlen($text) + strlen($tmptext[$i]); $i++) {
			$text .= ' ' . $tmptext[$i];
		}

		if (!$text) {
			$text = substr($tmptext[0], 0, $max);
		}
		if (strlen($text) != $length)
		$text .= '...';
	}
	return trim($text);
}

/**
 * Formatea la salida de un número entero
 *
 * @param double $price Valor
 * @return string
 */
function format_number($price) {
	$obj = & get_instance();
	$dec = ((int) $price == $price) ? 0 : $obj->config->item('bp.currency.decimals');
	return number_format($price, $dec, $obj->config->item('bp.percent.dec_points'), $obj->config->item('bp.currency.thousands_sep'));
}

/**
 * Formatea la salida de un texto
 *
 * @param string $text Valor
 * @return string
 */
function format_text($text) {
	return htmlspecialchars($text);
}

/**
 * Formatea la salida de un porcentaje
 *
 * @param double $numbre Valor
 * @return string
 */
function format_percent($number) {
	#var_dump($number, (float) $number);
	$number = (float) $number;
	$obj = & get_instance();
	return $obj->config->item('bp.percent.symbol_left') .
	number_format($number,
	$obj->config->item('bp.percent.decimals'),
	$obj->config->item('bp.percent.dec_points'),
	$obj->config->item('bp.percent.thousands_sep')) .
	$obj->config->item('bp.percent.symbol_right');
}

/**
 * Formatea la salida de un porcentaje
 *
 * @param double $numbre Valor
 * @return string
 */
function format_name($nombre, $apellido, $empresa, $ft = FALSE) {
	$nombre = trim($nombre);
	$apellido = trim($apellido);
	$name = trim(((isset($nombre)) ? $nombre: '') . ((isset($apellido)) ?(' ' . $apellido): ''));
	if (isset($empresa) && (trim($empresa) != '')) {
		$empresa = trim($empresa);
		if ($name != '') {
			$name .= ($ft)?('<br />' . $empresa): (' (' . $empresa . ')');
		} else {
			$name = trim($empresa);
		}
	}
	return $name;
}

/**
 * Formatea la salida de un porcentaje
 *
 * @param double $numbre Valor
 * @return string
 */
function format_autor($nombre, $apellido) {
	$name = array();
	if (isset($apellido) && trim($apellido) != '')
	$name[] = trim($apellido);
	if (isset($nombre) && trim($nombre) != '')
	$name[] = trim($nombre);
	return implode(', ', $name);
}

/**
 * Formatea la salida de una fecha
 *
 * @param date $date Fecha
 * @return string
 */
function format_date($date) {
	$obj = & get_instance();
	return ($date == 0) ? '' : date($obj->config->item('bp.date.format'), $date);
}

/**
 * Formatea el número de factira
 * @param int $numero Número de factura
 * @param int $serie Serie
 */
function format_numerofactura($numero, $serie) {
	$obj = & get_instance();
	return sprintf($obj->config->item('bp.factura.format'), $numero, $serie);
}

/**
 * Formatea la salida de una fecha y hora
 *
 * @param date $date Fecha
 * @return string
 */
function format_datetime($date = null) {
	$obj = & get_instance();

	if (!isset($date)) $date = time();

	return ($date == 0) ? '' : date($obj->config->item('bp.date.formatlong'), $date);
}

/**
 * Formatea la salida de una hora
 *
 * @param date $date Fecha
 * @return string
 */
function format_time($date) {
	$obj = & get_instance();

	return ($date == 0) ? '' : date($obj->config->item('bp.date.formattime'), $date);
}

/**
 * Formatea la salida de una fecha
 *
 * @param date $date
 * @return string
 */
function format_str_to_date($date) {
	$obj = & get_instance();
	$d = preg_split('/\//', $date);

	#print_r($d);
	if (count($d) != 3) {
		return strtotime($date);
	}
	return mktime(0, 0, 0, $d[$obj->config->item('bp.date.format.i.mes')],
	$d[$obj->config->item('bp.date.format.i.dia')],
	$d[$obj->config->item('bp.date.format.i.year')]);
}

/**
 * Crea un array de % de diferencia entre 2 arrays
 *
 * @param array $ar1
 * @param array $ar2
 * @return array
 */
function array_compare_percent($ar1, $ar2) {

	$ar3 = array();
	for ($i = 0; $i < count($ar1); $i++) {
		$ar3[$i] = ($ar1[$i] <> 0) ? (($ar2[$i] - $ar1[$i]) / $ar1[$i]) * 100 : 0;
	}
	return $ar3;
}

/**
 * Suma 2 arrays
 *
 * @param array $ar1
 * @param array $ar2
 * @return array
 */
function array_add($ar1, $ar2) {

	$ar3 = array();
	foreach ($ar1 as $k => $v) {
		$ar3[$k] = (float) (isset($ar1[$k]) ? $ar1[$k] : 0) + (float) (isset($ar2[$k]) ? $ar2[$k] : 0);
	}
	return $ar3;
}

/**
 * Resta 2 arrays
 *
 * @param array $ar1
 * @param array $ar2
 * @return array
 */
function array_subs($ar1, $ar2) {

	$ar3 = array();
	foreach ($ar1 as $k => $v) {
		$ar3[$k] = (float) (isset($ar1[$k]) ? $ar1[$k] : 0) - (float) (isset($ar2[$k]) ? $ar2[$k] : 0);
	}
	return $ar3;
}

/**
 * Suma los elementos de un array
 *
 * @param array $ar
 * @return double
 */
function array_total($ar) {

	$total = 0;
	for ($i = 0; $i < count($ar); $i++) {
		$total += (float) $ar[$i];
	}
	return $total;
}

/**
 * http://www.linuxjournal.com/article/9585
 * @param $email
 * @return unknown_type
 */
function check_email_address($email) {
	// First, we check that there's one @ symbol,
	// and that the lengths are right.
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Email invalid because wrong number of characters
		// in one section or wrong number of @ symbols.
		return false;
	}

	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
		}
	}
	// Check if domain is IP. If not,
	// it should be valid domain name
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if
			(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}

/**
 * Validate an email address.
 * Provide email address (raw input)
 * Returns true if the email address has the email
 * address format and the domain exists.
 * http://www.linuxjournal.com/article/9585
 * @param $email
 * @return unknown_type
 */
function valid_email($email) {
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex) {
		$isValid = false;
	} else {
		$domain = substr($email, $atIndex + 1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64) {
			// local part length exceeded
			$isValid = false;
		} else if ($domainLen < 1 || $domainLen > 255) {
			// domain part length exceeded
			$isValid = false;
		} else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
			// local part starts or ends with '.'
			$isValid = false;
		} else if (preg_match('/\\.\\./', $local)) {
			// local part has two consecutive dots
			$isValid = false;
		} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
			// character not valid in domain part
			$isValid = false;
		} else if (preg_match('/\\.\\./', $domain)) {
			// domain part has two consecutive dots
			$isValid = false;
		} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
			// character not valid in local part unless
			// local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
				$isValid = false;
			}
		}
		if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
			// domain not found in DNS
			$isValid = false;
		}
	}
	return $isValid;
}

/**
 * Function to strip tags and attributes, but with allowable attributes.
 * Usage:
 *  Allowable attributes can be comma seperated or array
 *
 * Example:
 * <?php strip_tags_attributes($string,'<strong><em><a>','href,rel'); ?>
 *
 * http://php.net/manual/en/function.strip-tags.php
 * @param $string
 * @param $allowtags
 * @param $allowattributes
 * @return unknown_type
 */
function strip_tags_attributes($string, $allowtags = null, $allowattributes = array()) {
	$string = strip_tags($string, $allowtags);
	if (!is_null($allowattributes)) {
		if (!is_array($allowattributes)) {
			$allowattributes = explode(",", $allowattributes);
		}
		if (is_array($allowattributes)) {
			$allowattributes = implode(")(?<!", $allowattributes);
		}
		if (strlen($allowattributes) > 0) {
			$allowattributes = "(?<!" . $allowattributes . ")";
		}
		$string = preg_replace_callback("/<[^>]*>/i", create_function(
                                '$matches',
                                'return preg_replace("/ [^ =]*' . $allowattributes . '=(\"[^\"]*\"|\'[^\']*\')/i", "", $matches[0]);'
                                ), $string);
	}
	return $string;
}

/**
 * PHP's strip_tags() function will remove tags, but it
 * doesn't remove scripts, styles, and other unwanted
 * invisible text between tags.  Also, as a prelude to
 * tokenizing the text, we need to insure that when
 * block-level tags (such as <p> or <div>) are removed,
 * neighboring words aren't joined.
 * http://nadeausoftware.com/articles/2007/09/php_tip_how_strip_html_tags_web_page
 *
 * @param string $text
 * @return string
 */
function strip_html_tags($text) {

	$text = preg_replace(
	array(
	// Remove invisible content
                        '@<head[^>]*?>.*?</head>@siu',
                        '@<style[^>]*?>.*?</style>@siu',
                        '@<script[^>]*?.*?</script>@siu',
                        '@<object[^>]*?.*?</object>@siu',
                        '@<embed[^>]*?.*?</embed>@siu',
                        '@<applet[^>]*?.*?</applet>@siu',
                        '@<noframes[^>]*?.*?</noframes>@siu',
                        '@<noscript[^>]*?.*?</noscript>@siu',
                        '@<noembed[^>]*?.*?</noembed>@siu',
	// Add line breaks before & after blocks
                        '@<((br)|(hr))@iu',
                        '@</?((address)|(blockquote)|(center)|(del))@iu',
                        '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                        '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                        '@</?((table)|(th)|(td)|(caption))@iu',
                        '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                        '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                        '@</?((frameset)|(frame)|(iframe))@iu',
	),
	array(
                        ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                        "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                        "\n\$0", "\n\$0",
	),
	$text);

	// Remove all remaining tags and comments and return.
	return strip_tags($text);
}

/**
 * Here is a function to sort an array by the key of his sub-array.
 * serpro at gmail dot com
 * @param array $array
 * @param string $subkey
 * @param bool $sort_ascending
 */
function sksort(&$array, $subkey ='id', $sort_ascending = TRUE) 
{

	if (count($array)) $temp_array[key($array)] = array_shift($array);

	foreach ($array as $key => $val) 
	{
		$offset = 0;
		$found = false;
		foreach ($temp_array as $tmp_key => $tmp_val) 
		{
			$cmp = (is_numeric($val[$subkey])) ? (-$val[$subkey] + $tmp_val[$subkey]):strcoll($val[$subkey], $tmp_val[$subkey]);
			if (!$found && $cmp > 0) 
			{
				$temp_array = array_merge((array) array_slice($temp_array, 0, $offset),
					array($key => $val),
					array_slice($temp_array, $offset)
				);
				$found = true;
			}
			$offset++;
		}
		if (!$found) $temp_array = array_merge($temp_array, array($key => $val));
	}

	if ($sort_ascending && isset($temp_array)) {
		$array = array_reverse($temp_array);
	} else {
		$array = (isset($temp_array) ? $temp_array : $array);
	}
}

/**
 * Añade dias, meses y/o años a una fecha
 * @param int $givendate Fecha inicial
 * @param int $day Dias a añadir
 * @param int @mth Meses a añadir
 * @param int $yr Años a añadir
 * @return int
 */
function dateadd($cd, $day = 0, $mth = 0, $yr = 0) 
{
	//$cd = strtotime($givendate);
	$newdate = /*date('Y-m-d h:i:s', */mktime(date('h',$cd),
	date('i',$cd), date('s',$cd), date('m',$cd) + $mth,
	date('d',$cd) + $day, date('Y',$cd) + $yr)/*)*/;
	return $newdate;
}

/**
 * PHP DateDiff Function
 * VBScript's DateDiff function is a powerful way to express differences between dates,
 * and PHP lacks a similar function. Here's a replica of VBScript's DateDiff function in PHP.
 * http://www.addedbytes.com/code/php-datediff-function/
 *
 * @param string $interval
 * 		 $interval can be:
 * 	 	 yyyy - Number of full years
 * 	 	 q - Number of full quarters
 * 		 m - Number of full months
 * 		 y - Difference between day numbers
 * 		 (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
 * 		 d - Number of full days
 * 		 w - Number of full weekdays
 * 		 ww - Number of full weeks
 * 		 h - Number of full hours
 * 		 n - Number of full minutes
 * 		 s - Number of full seconds (default)
 * @param date $datefrom
 * @param date $dateto
 * @param bool $using_timestamps
 * @return int
 */
function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
	if (!$using_timestamps) {
		$datefrom = strtotime($datefrom, 0);
		$dateto = strtotime($dateto, 0);
	}
	$difference = $dateto - $datefrom; // Difference in seconds

	switch ($interval) {

		case 'yyyy': // Number of full years

			$years_difference = floor($difference / 31536000);
			if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
				$years_difference--;
			}
			if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
				$years_difference++;
			}
			$datediff = $years_difference;
			break;

		case "q": // Number of full quarters

			$quarters_difference = floor($difference / 8035200);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$quarters_difference--;
			$datediff = $quarters_difference;
			break;

		case "m": // Number of full months

			$months_difference = floor($difference / 2678400);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$months_difference--;
			$datediff = $months_difference;
			break;

		case 'y': // Difference between day numbers

			$datediff = date("z", $dateto) - date("z", $datefrom);
			break;

		case "d": // Number of full days

			$datediff = floor($difference / 86400);
			break;

		case "w": // Number of full weekdays

			$days_difference = floor($difference / 86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $datefrom);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			if ($odd_days > 7) { // Sunday
				$days_remainder--;
			}
			if ($odd_days > 6) { // Saturday
				$days_remainder--;
			}
			$datediff = ($weeks_difference * 5) + $days_remainder;
			break;

		case "ww": // Number of full weeks

			$datediff = floor($difference / 604800);
			break;

		case "h": // Number of full hours

			$datediff = floor($difference / 3600);
			break;

		case "n": // Number of full minutes

			$datediff = floor($difference / 60);
			break;

		default: // Number of full seconds (default)

			$datediff = $difference;
			break;
	}

	return $datediff;
}

/**
 * Devuelve todos los nombres de las variables GET y POST
 * @return array
 */
function get_post_names() {
	$data = array();
	foreach ($_POST as $k => $v) {
		$data[$k] = $k;
	}
	foreach ($_GET as $k => $v) {
		$data[$k] = $k;
	}
	return $data;
}

/**
 * Devuelve todos los datos de las variables GET y POST
 * @return array
 */
function get_post_all() {
	return $_POST + $_GET;
}

/**
 * Convierte un valor a entero para ser procesado por la base de datos
 * @param mixed $value Valor a convertir
 * @return int, FALSE si no se puede convertir
 */
function format_toint($value) {
	if ((trim($value)) == '') {
		$value = null;
	} else {
		if (is_numeric($value)) {
			$value = (int) $value;
		} elseif (!is_null($value)) {
			$value = FALSE;
		}
	}
	return $value;
}

/**
 * Convierte un valor a punto flotante para ser procesado por la base de datos
 * @param mixed $value Valor a convertir
 * @return float, FALSE si no se puede convertir
 */
function format_tofloat($value) {
	if ((trim($value)) == '') {
		$value = null;
	} else {
		$value = str_replace(',', '.', $value);
		if (is_numeric($value)) {
			//echo "$value es numeric ";
			//$value = (float) $value;
		} elseif (!is_null($value)) {
			$value = FALSE;
		}
	}
	return $value;
}

/**
 * Convierte un valor a boleano para ser procesado por la base de datos
 * @param mixed $value Valor a convertir
 * @return 0, 1, FALSE si no se puede convertir
 */
function format_tobool($value) {
	if (is_bool($value))
		return ($value) ? 1 : 0;
	if ((trim($value)) == '') {
		$value = null;
	} elseif (in_array(strtolower($value), array('true', 'on', '1'))) {
		$value = 1;
	} elseif (in_array(strtolower($value), array('off', 'false', '0'))) {
		$value = 0;
	} else {
		$value = FALSE;
	}
	return $value;
}

/**
 * Convierte un valor a fecha para ser procesado por la base de datos
 * @param mixed $value Valor a convertir
 * @return int, FALSE si no se puede convertir
 */
function format_todate($value) 
{
	//echo '<pre>todate ' . $value . '</pre>';
	if ((trim($value)) == '') {
		$value = null;
	} else {
		$value = format_mssql_date(is_numeric($value) ? $value : format_str_to_date($value), false);
	}
	return $value;
}

/**
 * Devuelve un texto a null si es null
 * @param mixed $var
 */
function to_null($var) 
{
	return isset($var) ? $var : 'null';
}

/**
 * Comprueba si un texto es null
 * @param mixed $var
 */
function is_null_str($var) 
{
	return ($var == 'null') ? null : $var;
}

/**
 * Formatea una dirección
 * @param array $d Registro de la dirección
 * @param bool $long TRUE: Formato largo
 */
function format_address($d, $long = FALSE) 
{
	$text = array();
	if ($long) $text[] = $d['nIdDireccion'];
	if ($long & !empty($d['cDescripcion']) && trim($d['cDescripcion']) != '') $text[] = '[' . $d['cDescripcion'] . ']';
	if ($long & !empty($d['cPerfil']) && trim($d['cPerfil']) != '') $text[] = '[' . $d['cPerfil'] . ']';
	if (!empty($d['cTitular']) && trim($d['cTitular']) != '') $text[] = '[' . $d['cTitular'] . ']';
	if (!empty($d['cCalle']) && trim($d['cCalle']) != '') $text[] = $d['cCalle'];
	
	if (!empty($d['cCP']) && trim($d['cCP']) != '') $text[] =  $d['cCP'];
	if (!empty($d['cPoblacion']) && trim($d['cPoblacion']) != '') $text[] =  $d['cPoblacion'];
	if (!empty($d['cRegion'])) $text[] = $d['cRegion'];
	if (!empty($d['cPais'])) $text[] = $d['cPais'];
	$text = implode(' - ', $text);
	/*$text = ($long ? $d['nIdDireccion'] . ' - ' : '') .
	($long ? ((isset($d['cPerfil']) && ($d['cPerfil'] != '')) ? ('[' . $d['cPerfil'] . '] - ') : '') : '') .
	((isset($d['cTitular']) && (trim($d['cTitular']) != '')) ? ('[' . $d['cTitular'] . '] - ') : '') .
	$d['cCalle'] .
	((isset($d['cCP']) && (trim($d['cCP']) != '')) ? ', ' . $d['cCP'] : '') .
	((isset($d['cPoblacion'])) ? ' - ' . $d['cPoblacion'] : '') .
	((isset($d['cRegion'])) ? ' - ' . $d['cRegion'] : '') .
	((isset($d['cPais'])) ? ' - ' . $d['cPais'] : '');

	$text = trim(str_replace('- -', '-', $text));*/

	$perfil = array(
        'tipo' => 'D',
        'id_perfil' => $d['nIdTipo'],
        'cPerfil' => isset($d['cPerfil']) ? $d['cPerfil'] : null,
        'cDescripcion' => $d['cDescripcion'],
        'text' => $text,
        'id_u' => 'D' . $d['nIdDireccion'],
        'id' => $d['nIdDireccion']);

	return $perfil;
}

/**
 * Formatea una dirección
 * @param array $d Registro de la dirección
 * @param bool $long TRUE: Formato largo
 */
function format_address_print($d, $sep = '<br/>') {
	$final = array();
	if ((isset($d['cTitular']) && (trim($d['cTitular']) != ''))) {
		$final[] = str_replace(array("\n", "\r"), array($sep, ''), trim($d['cTitular']));
	}
	if ((isset($d['cCalle']) && (trim($d['cCalle']) != ''))) {
		$final[] = str_replace(array("\n", "\r"), array($sep, ''), trim($d['cCalle']));
	}

	$lineas = array();
	if ((isset($d['cCP']) && (trim($d['cCP']) != ''))) {
		$lineas[] = trim($d['cCP']);
	}
	if ((isset($d['cPoblacion']) && (trim($d['cPoblacion']) != ''))) {
		$lineas[] = trim($d['cPoblacion']);
	}
	if ((isset($d['cRegion']) && (trim($d['cRegion']) != ''))) {
		$lineas[] = trim($d['cRegion']);
	}

	if (count($lineas) > 0) {
		foreach ($lineas as $k => $v) {
			if (!isset($v) || (trim($v) == ''))
			unset($lineas[$k]);
		}
		$final[] = implode(', ', $lineas);
	}
	if ((isset($d['cPais']) && (trim($d['cPais']) != ''))) {
		$final[] = $d['cPais'];
	}
	#echo implode($sep, $final); die();
	if (count($final) > 0) {
		foreach ($final as $k => $v) {
			if (!isset($v) || (trim($v) == ''))
			unset($final[$k]);
		}
		#echo '<pre>';var_dump($final); echo '</pre>';
		return implode($sep, $final);
	}
	return '';
}

/**
 * Ajusta los decimales de un valor de moneda
 * @param float $value
 * @return float
 */
function format_decimals($value) {
	$obj = get_instance();
	return round($value, $obj->config->item('bp.currency.decimals'));
}

/**
 * Quita el IVA de un importe
 * @param float $pr Importe
 * @param float $iva Valor de IVA (en %)
 * @return float
 */
function format_quitar_iva($pr, $iva) {
	return format_decimals($pr / (1 + ($iva / 100)));
}

/**
 * Añade el IVA a un importe
 * @param float $pr Importe
 * @param float $iva Valor de IVA (en %)
 * @return float
 */
function format_add_iva($pr, $iva) {
	return format_decimals($pr * (1 + ($iva / 100)));
}

/**
 * Calcula el IVA de un importe
 * @param float $pr Importe
 * @param float $iva Valor de IVA (en %)
 * @return float
 */
function format_iva($pr, $iva) {
	return format_decimals($pr * ($iva / 100));
}

/**
 * Calcula el coste de una línea de documento
 * @param array $data Registro con los datos de la línea de documento
 * @return float
 */
function format_calculate_coste($data)
{
	if (isset($data['fPrecio']) &&
	isset($data['fDescuento']))
	{
		$coste = $data['fPrecio'] * (1 - $data['fDescuento'] / 100);
		$coste = format_decimals($coste);
		return ($coste * (isset($data['nCantidad'])?$data['nCantidad']:1));
	}
}

/**
 * Calcula los importes de una línea de documento
 * @param array $data Registro con los datos de la línea de documento
 * @return array: <ul>
 * <li><strong>fImporte</strong>: base unitario sin IVA</li>
 * <li><strong>fIVAImporte</strong>: IVA total de la línea</li>
 * <li><strong>fBase</strong>: base sin IVA del total de la línea</li>
 * <li><strong>fTotal</strong>: total con IVA de la línea
 */
function format_calculate_importes($data)
{
	if (isset($data['fPrecio']) &&
	isset($data['fDescuento']) &&
	isset($data['nCantidad']) &&
	isset($data['fIVA'])
	)
	{
		if (!isset($data['fRecargo']))
		$data['fRecargo'] = 0;
		$pvp = format_add_iva($data['fPrecio'], $data['fIVA']);
		$unitario = format_decimals($pvp * (1 - $data['fDescuento'] / 100));
		$total = format_decimals($unitario * $data['nCantidad']);
		$base = format_quitar_iva($total, $data['fIVA']);
		$iva = format_iva($base, $data['fIVA']);
		if (format_decimals($base + $iva) != $total)
		{
			$unitario -= 0.01;
			$total = format_decimals($unitario * $data['nCantidad']);
			$base = format_quitar_iva($total, $data['fIVA']);
			$iva = format_iva($base, $data['fIVA']);
		}
		$recargo = format_decimals($base * ($data['fRecargo'] / 100));
		$total += $recargo;

		$unitario2 = format_decimals($data['fPrecio'] * (1 - $data['fDescuento'] / 100));
		$base2 = format_decimals($unitario2 * $data['nCantidad']);
		$iva2 = format_iva($base2, $data['fIVA']);
		$recargo2 = format_decimals($base2 * ($data['fRecargo'] / 100));
		$total2 = format_decimals($base2 + $iva2 + $recargo2);

		return array(
            'fImporte' => $unitario,
            'fPVP' => $pvp,
            'fBase' => $base,
            'fIVAImporte' => $iva,
            'fRecargoImporte' => $recargo,
            'fTotal' => $total,
            'fImporte2' => $unitario2,
            'fBase2' => $base2,
            'fIVAImporte2' => $iva2,
            'fRecargoImporte2' => $recargo2,
            'fTotal2' => $total
		);
	}
	return array(
        'fImporte' => 0,
        'fPVP' => 0,
        'fBase' => 0,
        'fIVAImporte' => 0,
        'fRecargoImporte' => 0,
        'fTotal' => 0
	);
}

/**
 * Formatea una llamada JS para abrir una URL
 * @param string $url URL del comando
 * @return string
 */
function format_enlace_url($url, $title, $icon, $parent = FALSE) 
{
	return 'javascript:' . (($parent) ? 'parent.' : '') . "Ext.app.addTabUrl({print: true, navigation: false, export: true, icon: '{$icon}', title: '{$title}', url: '{$url}'});";
}


/**
 * Formtaa una salida para la ejecución de un comando
 * @param string $texto Texto a mostrar
 * @param string $url Comando
 * @return string
 */
function format_enlace_cmd($texto, $url, $style = null, $cmpid = null) 
{
	//$url = site_url($url);
	//$url = site_url($url);
	if (!isset($style))
	$style = 'cmd-link';
	if (isset($cmpid))
	{
		$cmpid =(isset($cmpid))?(', params: {cmpid: \''. $cmpid . '\'}'):'';		
		return "<span class='{$style}'><a href=\"javascript:parent.Ext.app.callRemote({timeout: false, url: '{$url}'{$cmpid}});\">{$texto}</a></span>";
	}
	else
	{
		return "<span class='{$style}'><a href=\"javascript:parent.Ext.app.execCmd({timeout: false, url: '{$url}'});\">{$texto}</a></span>";
	}
	#return "<span class='{$style}'><a href=\"" . format_enlace_js($url, TRUE) . "\">{$texto}</a></span>";
}

/**
 * Genera un enlace a la función de borrado de un elemento
 * @param  int $id  Id del elemento
 * @param  strng $url URL de borrado
 * @param  string $fn  Función a la que llamar cuando se elimina
 * @return string
 */
function format_js_del($id, $url, $fn = 'null') 
{
	$url .= '/del/' . $id;
	$url = site_url($url);
	$image = image_asset('s.gif', '', array('border' => 0, 'width' => '16px', 'height' => '16px'));
	$js = "parent.Ext.app.callRemoteAsk({
    	url: '{$url}',
    	askmessage: parent._s('elm-registro'),
    	fnok: function(){
    		if ({$fn} != null)
	    		{$fn}({$id});
    	}
    });";
    return format_enlace_js($image, $js, 'icon-delete');
}

/**
 * Formtaa una salida para la ejecución de un comando
 * @param string $texto Texto a mostrar
 * @param string $url Comando
 * @return string
 */
function format_enlace_js($texto, $js, $style = null) 
{
	//$url = site_url($url);
	if (!isset($style)) $style = 'cmd-link';
	return "<span class='{$style}'><a href=\"javascript:{$js};\">{$texto}</a></span>";
}

/**
 * Crea los enlaces a los documentos de un array de docuemntos de un artículo
 * @param array $reg Resgitro del documento
 * @return string
 */
function format_enlace_documentos($reg) 
{
	switch ($reg['tipo']) {
		case 'docpre':
		case 'docpedcli':
			return format_enlace_cmd($reg['id'], site_url('ventas/pedidocliente/index/' . $reg['id']));
			break;
		case 'docpedpro':
			return format_enlace_cmd($reg['id'], site_url('compras/pedidoproveedor/index/' . $reg['id']));
			break;
		case 'entdev':
		case 'saldev':
			return format_enlace_cmd($reg['id'], site_url('compras/devolucion/index/' . $reg['id']));
			break;
		case 'entalb':
			$text = '';
			if (isset($reg['id1'])) {
				$text = format_enlace_cmd("A:{$reg['id1']}", site_url('compras/albaranentrada/index/' . $reg['id1']));
			}
			if (isset($reg['id2'])) {
				$text .= '<br/>' . format_enlace_cmd("PP:{$reg['id2']}", site_url('compras/pedidoproveedor/index/' . $reg['id2']));
			}
			return $text;
			break;
		case 'entdevcmp':
		case 'salcmp':
			$text = '';
			if (isset($reg['id1'])) {
				$text = format_enlace_cmd("A:{$reg['id1']}", site_url('ventas/albaransalida/index/' . $reg['id1']));
			}
			if (isset($reg['id2'])) {
				$text .= '<br/>' . format_enlace_cmd("F:{$reg['id2']}", site_url('ventas/factura/index/' . $reg['id2']));
			}

			return $text;
			break;
		case 'liqdep':
			$text = '';
			if (isset($reg['id1'])) {
				$text = format_enlace_cmd("LD:{$reg['id1']}", site_url('compras/liquidaciondepositos/index/' . $reg['id1']));
			}
			if (isset($reg['id2'])) {
				$text .= '<br/>' . format_enlace_cmd("AE:{$reg['id2']}", site_url('compras/albaranentrada/index/' . $reg['id2']));
			}
			return $text;
			break;
		default:
			return $reg['id'];
	}
}

/**
 * Comprueba si el email es un email bien formateado.
 * @link http://www.php5script.com/isemail
 * @param string $email Email a validar
 * @return bool
 */
function is_email($email) 
{
	return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
}

/**
 * Comprueba si el string es un teléfono.
 * @param string $phone Número a comprobar
 * @return bool
 */
function is_phone($phone) 
{
	return preg_match('/^([0-9\(\)\/\+ \-\.]*)$/', $phone);
}

/**
 * Limpia un teléfono
 * @param  string $phone Número
 * @return string
 */
function clean_phone($phone)
{
	return str_replace(array(' ', '.', '-'), '', $phone);
}

/**
 * Genera un link a una imagen
 * @param  int $id   Id del artículo
 * @param  int $size Tamaño
 * @return string
 */
function format_url_cover($id, $size = null) 
{
	$sizeurl = '';
	$sizeurl = (isset($size) && ($size > 0)) ? $size : 'null';

	return site_url("catalogo/articulo/cover/{$id}/{$sizeurl}/{$id}.png");
}

/**
 * Añade una imagen a un DIV
 * @param  int $id   Id del artículo
 * @param  int $size Tamaño
 * @param  string $div  Id del DIV
 * @return string
 */
function format_cover($id, $size = null, $div = null) 
{
	$url = format_url_cover($id, $size);
	$url2 = format_url_cover($id);
	if (isset($size) && ($size > 0)) {
		$size = "width='{$size}'";
	}
	if (isset($div))
	$div = "id='{$div}'";
	return "<img border='0' src='{$url}' $div $size />";
	return "<img onclick='javascript:jQuery.facebox({image:this.src});' border='0' src='{$url}' $div $size />";
}

/**
 * Genera un enlace a una portada en la Web
 * @param  int $id Id del artículo
 * @return string
 */
function format_cover_web($id) 
{
	$obj = get_instance();
	$url = str_replace('%id%', $id, $obj->config->item('catalogo.webpage.cover'));
	return "<img border='0' src='{$url}' />";
}

/**
 * Crea un enlace a una imagen que se abre en una diálogo
 * @param  string $text Texto para abrir imagen
 * @param  string $url  Url de la imagen
 * @return string
 */
function format_facebox($text, $url) 
{
	return "<a href='#' onclick='javascript:jQuery.facebox({image:\"{$url}\"});'>{$text}</a>";
}

/**
 * Crea un enlace de a una URL que se abre en un diálogo 
 * @param  string $text Texto para abrir el enlace
 * @param  string $url  Url a abrir
 * @return string
 */
function format_lightbox($text, $url) 
{
	return "<a href='{$url}?KeepThis=true&TB_iframe=true&height=400&width=600' class='thickbox'>{$text}</a>";
}

/**
 * http://www.sweeting.org/mark/blog/2005/07/12/base64-encoded-images-embedded-in-html
 * @param $file
 */
function embed_image($file) 
{
	if ($fp = fopen($file, "rb", 0)) {
		$picture = fread($fp, filesize($file));
		fclose($fp);
		// base64 encode the binary data, then break it
		// into chunks according to RFC 2045 semantics
		$base64 = chunk_split(base64_encode($picture));
		$tag = '<img ' . "n" .
                'src="data:image/gif;base64,' . $base64 .
                '" alt="British Blog Directory" width="80" height="15" />';
		return $tag;
	}
}


function check_portes(&$data) 
{
	if (isset($data['lineas']) && isset($data['fPortes']) && $data['fPortes'] != 0) 
	{
		$item = array(
            'nIdLineaAlbaran' => -1,
            'nIdLibro' => 500,
            'nIdSeccion' => 101,
            'fIVA' => 18,
            'nCantidad' => ($data['fPortes'] < 0) ? -1 : 1,
            'fBase' => (($data['fPortes'] < 0) ? -1 : 1) * format_quitar_iva($data['fPortes'], 18),
            'fDescuento' => 0,
            'fCoste' => 0,
            'cRefCliente' => null,
            'cTitulo' => 'PORTES',
            'cSeccion' => 'GENERAL');
		$item['fPrecio'] = $item['fBase'];
		$item['fIVAImporte'] = format_iva($item['fBase'], 18);
		$item['fTotal'] = $item['fPrecio'] + $item['fIVAImporte'];
		$item['fPVP'] = $item['fTotal'];
		$item['fImporte'] = $item['fTotal'];

		$data['lineas'][] = $item;
	}
}


/**
 * Was looking for a simple way to search for a file/directory using a mask. Here is such a function.
 * By default, this function will keep in memory the scandir() result, to avoid scaning multiple time for the same directory.
 * Requires at least PHP5.
 * http://es.php.net/manual/es/function.scandir.php
 * @param string $path Path del directorio
 * @param string $mask Filtro de ficheros
 * @param bool $nocache Usar caché
 */
function sdir($path='.', $mask='*', $nocache=0) 
{
	static $dir = array();
	if (!isset($dir[$path]) || $nocache) 
	{ 
		$dir[$path] = scandir($path);
		$sdir = array();
		foreach ($dir[$path] as $i => $entry) 
		{
			if ($entry != '.' && $entry != '..' && fnmatch($mask, $entry)) 
			{
				$sdir[] = $entry;
			}
		}
		return ($sdir);
	}
}

/**
 * Diferencia en días entre dos fechas
 * @param int $startDate Fecha inicial (int o string)
 * @param int $endDate Fecha final (int o string)
 */
function daysDifference($startDate, $endDate)
{
	$diff = $endDate - $startDate;
	return round($diff / 86400);
}

/**
 * Here is how I solved the problem of missing date_diff function with php versions below 5.3.0
 * The function accepts two dates in string format (recognized by strtotime() hopefully), and returns the date difference in an array with the years as first element, respectively months as second, and days as last element.
 * It should be working in all cases, and seems to behave properly when moving through February.
 * http://php.net/manual/en/function.date-diff.php
 * @param mixed $startDate Fecha inicial (int o string)
 * @param mixed $endDate Fecha final (int o string)
 * @return array [0] -> años, [1] -> meses, [2] -> días
 */
function dateDifference($startDate, $endDate)
{
	if (is_string($startDate))
	$startDate = strtotime($startDate);
	if (is_string($endDate))
	$endDate = strtotime($endDate);
	if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
	return FALSE;

	$years = date('Y', $endDate) - date('Y', $startDate);

	$endMonth = date('m', $endDate);
	$startMonth = date('m', $startDate);

	// Calculate months
	$months = $endMonth - $startMonth;
	if ($months <= 0) {
		$months += 12;
		$years--;
	}
	if ($years < 0)
	return false;

	// Calculate the days
	$offsets = array();
	if ($years > 0)
	$offsets[] = $years . (($years == 1) ? ' year' : ' years');
	if ($months > 0)
	$offsets[] = $months . (($months == 1) ? ' month' : ' months');
	$offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

	$days = $endDate - strtotime($offsets, $startDate);
	$days = date('z', $days);

	return array($years, $months, $days);
}

/**
 * Devuelve la tarifa de un artículo
 * @param array $articulo Datos del artículo
 * @param array $tarifas Tarifas del artículo
 * @param int $idtarifa Id de la tarifa a selecconar
 * @return float
 */
function format_get_tarifa($articulo, $tarifas, $idtarifa)
{
	if (count($tarifas) == 0) return $articulo['fPrecio'];
	if (!is_numeric($idtarifa))
	{
		$obj = get_instance();
		$idtarifa = $obj->config->item('ventas.tarifas.defecto');
	}
	foreach($tarifas as $tarifa)
	{
		if ($tarifa['nIdTipoTarifa'] == $idtarifa) return $tarifa['fPrecio'];
	}
	return $articulo['fPrecio'];
}

/**
 * Redondea a 0.5
 * @param float $precio Valor a redondear
 * @return float
 */
function format_redondear05($precio)
{
	$temp = (string) ($precio * 100);
	$temp = (int)$temp[strlen($temp)-1];
	if ($temp > 5) $precio = $precio + (10.0 - $temp)/100;
	elseif ($temp > 0) $precio = $precio + (5.0 - $temp)/100;

	return $precio;
}

/**
 * Redondea a 0.5
 * @param float $precio Valor a redondear
 * @return float
 */
function format_ceronada($v)
{
	return ($v != 0)?$v:'&nbsp;';
}

/**
 * Crea los datos para poder generar una gráfica de barras de un solo dato
 * @param string $id  Id del DIV
 * @param string $label Nombre de la gráfica
 * @param array $values Valores (un solo nivel)
 * @return string
 */
function format_data_plot($id, $label, $values)
{
	$t = '';
	$data = array();
	foreach($values as $i => $v)
	{
		$data[] = '["' . $i . '", ' . $v . ']';
	}
	$data = '[' . implode(',', $data) . ']';
	$t[] = '{ label: "'. $label . '", data: ' . $data . ' }';
	$t = implode(',', $t);

	return '$.plot("#' . $id .'", [ ' . $t . '			
		], {
			series: {
				lines: { show: true },
				points: { show: true }
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}
		});';
}

/**
 * Crea los datos para poder generar una gráfica de barras de un múltiples datos
 * @param string $id  Id del DIV
 * @param array $values Valores 'label' => array
 * @return string
 */
function format_data_plot_multi($id, $values)
{
	$t = '';
	foreach ($values as $label => $value) 
	{
		$data = array();
		foreach($value as $i => $v)
		{
			$data[] = '["' . $i . '", ' . $v . ']';
		}
		$data = '[' . implode(',', $data) . ']';
		$t[] = '{ label: "'. $label . '", data: ' . $data . ' }';
	}
	$t = implode(',', $t);

	return '$.plot("#' . $id .'", [ ' . $t . '			
		], {
			series: {
				lines: { show: true },
				points: { show: true }
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}
		});';
}


/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 * http://ecapy.com/reemplazar-la-n-acentos-espacios-y-caracteres-especiales-con-php-actualizada/
 * @param string  $string la cadena a sanear
 * @return string $string saneada
 */
function sanear_string($string)
{

    $string = trim($string);
    /*
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
	*/
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             /*"#", */"@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< "/*, ";", ",", ":",
             ".", " "*/),
        '',
        $string
    );

    return $string;
}
/* End of file formatters_helper.php */
/* Location: ./system/application/helpers/formatters_helper.php */