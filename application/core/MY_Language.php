<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Language Class
 *
 * @package		Bibliopola 5.0
 * @subpackage	Libraries
 * @category	code
 * @author		Alejandro López
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Modificación de la librería de lenguaje
 *
 */
class MY_Language extends CI_Language {

	/**
	 * Crea los textos si no existen
	 * @var bool
	 */
	var $create = FALSE;

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function __construct()
	{
		parent::__construct();
	}
	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	$line 	the language line
	 * @return	string
	 */
	function line($line = '')
	{
		$old = $line;
		$line = parent::line($line);
		if ($line===FALSE && $old!='' && isset($old))
		{
			$t = $old;
			if (LANGUAGE_CREATE)
			{
				$dir = __DIR__;
				$file = $dir . '/../language/spanish/bibliopola_lang.php';
				$f = fopen($file, 'a+');
				$text = "\$lang['$old']";
				fwrite($f, $text);
				fclose($f);
				$this->language[$old] = "*{$old}*";
				$t = "*{$old}*";
			}
		}
		else
		{
			$t = $line;
		}

		return $t;
	}

	/**
	 * Devuelve todas las cadenas de texto
	 * @return array
	 */
	function get_texts()
	{
		return $this->language;
	}
}

/* End of file My_Language.php */
/* Location: ./system/libraries/My_Language.php */
