<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package     Bibliopola 5.0
 * @subpackage  libraries
 * @category    core
 * @author      Alejandro López
 * @copyright   Copyright (c) 2008-2010, ALIBRI
 * @link        http://bibliopola.net
 * @since       Version 5.0
 * @version     $Rev: 435 $
 * @filesource
 */

/**
 * Extension del ZIP
 * @author alexl
 *
 */
class Zip2 extends ZipArchive {

    /**
     * Instancia de CI
     * @var CI
     */
    private $obj;

    /**
     * Constructor
     * @return Messages
     */
    function __construct()
    {
        $this->obj =& get_instance();
        log_message('debug', 'Zip2 Class Initialised via '.get_class($this->obj));
    }

    public function addDirectory($dir, $path = '') 
    { 
        // adds directory
        foreach(glob($dir . '*') as $file) 
        {
            if(is_dir($file))
            {
                $this->addDirectory($file, $path);
            }
            else
            {
                $this->addFile($file, str_replace($path, '', $file));
            }
        }
    }
}

/* End of file zip2.php */
/* Location: ./system/libraries/zip2.php */