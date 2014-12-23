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
 * Página de consola con Progress
 * @author alexl
 *
 */
class Progress {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Progress
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->uid = $this->obj->config->item('bp.asm.uid');

		log_message('debug', 'Progress Class Initialised via '.get_class($this->obj));
	}

	function start()
	{
		@ini_set('zlib.output_compression',0);
		@ini_set('implicit_flush',1);
		if (ob_get_level() == 0) 
		{
		    ob_start();
		}

		echo $this->obj->load->view('sys/progress_start', null, TRUE);
		print str_repeat(' ',1024);
		flush();
    	ob_flush();
	}

	function end()
	{
		ob_end_flush();
		$this->obj->load->view('sys/progress_end');
	}

	function step_($text, $i = 1, $max = 1)
	{
	    //This div will show loading percents
    	$n =((int) ((550 * $i / $max) / 11))*11 + 11;
    	$d = (int) (($i / $max) * 100);
    	echo "<div class='percents'>{$d}% {$text}</div>";
    	//This div will show progress bar
    	echo '<div class="blocks" style="left: '.$n.'px">&nbsp;</div>';
    	flush();
    	ob_flush();
	}

	function step($text, $i = 1, $max = 1)
	{
	    //This div will show loading percents
    	$n =((int) ((550 * $i / $max) / 11))*11 + 11;
    	$d = (int) (($i / $max) * 100);

  		echo "<div class='per'>{$d}% {$text}</div>";

		// Now, output a new 'bar', forcing its width
		// to 3 times the percent, since we have
		// defined the percent bar to be at
		// 300 pixels wide.
		echo "<div class='bar' style='width: ". $d * 3 . "px'></div>";

    	flush();
    	ob_flush();
	}

	function step2($text, $i = 1, $max = 1)
	{
	    //This div will show loading percents
    	$n =((int) ((550 * $i / $max) / 11))*11 + 11;
    	$d = (int) (($i / $max) * 100);
    	echo "<div class='percents2'>{$d}% {$text}</div>";
    	//This div will show progress bar
    	echo '<div class="blocks2" style="left: '.$n.'px">&nbsp;</div>';
    	flush();
    	ob_flush();
	}

	function text($text)
	{
    	echo $text. "\n";
    	flush();
    	ob_flush();
	}

}
/* End of file Progress.php */
/* Location: ./system/libraries/Progress.php */
