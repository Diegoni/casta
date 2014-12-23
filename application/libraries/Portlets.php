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

define('PORTLETS_DIRECTORY', APPPATH . 'views' . DS . 'sys' . DS . 'portlets' . DS);

/**
 * Gestor de Portlets de la aplicación
 * @author alexl
 *
 */
class Portlets {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Portles
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		log_message('debug', 'Portlets Class Initialised via '.get_class($this->obj));
	}

	function get_portlets_js()
	{
		$this->obj->load->library('Configurator');
		$portlets = $this->obj->configurator->user('bp.portal.portlets');
		$portlets = preg_split('/;/', $portlets);
		$js = '';
		foreach($portlets as $portlet)
		{
			$file = PORTLETS_DIRECTORY . $portlet . '.js';
			if (file_exists($file))
			{
				$js .= file_get_contents($file);
			}
		}
		return $js;
	}

	function get_portlets_user($var)
	{
		$this->obj->load->library('Configurator');
		$columns = $this->obj->configurator->user('bp.portal.columns');
		$width = (int) (100 / $columns);

		$js = '';
		// Columnas
		$columnas = array();
		$objs = 0;
		for($i = 0; $i < $columns; $i++)
		{
			// Items en las columnas
			$items = $this->obj->configurator->user('bp.portal.portlets.' . ($i + 1));
			$items = preg_split('/;/', $items);
			#print_r($items);
			if (count($items) > 0)
			{
				//print_r($items);
				$lines = array();
				foreach($items as $it)
				{
					$params = preg_split('/\#/', $it);
					#var_dump($params);
					if (count($params) > 1)
					{
						if (isset($params[2]))
						{
							$a_params = preg_split('/\:\:/', $params[2]);
							$fn_params = '[' . implode(',', $a_params) . ']';
						}
						else
						{
							$fn_params = 'null';
						}
						//print_r($params);
						$js .= "var obj_{$objs} = new Portlet_{$params[1]}();\n";
						$lines[] = "{
		                    title: '{$params[0]}',
		                    layout: 'fit',
		                    tools: obj_{$objs}.tools(general_tools, {$fn_params}),
		                    //html: Portlet_{$params[1]}_html({$fn_params}),
		                    //items: Portlet_{$params[1]}_init({$fn_params})
		                    html: obj_{$objs}.html({$fn_params}),
		                    items: obj_{$objs}.init({$fn_params})
		                }" ;
						$objs++;
					}
				}

				if (count($lines) > 0)
				{
					$all = implode(',', $lines);
					$columnas[] ="{
		                columnWidth: .{$width},
		                style: 'padding:10px 0 10px 10px',
		                items: [{$all}]
		            	}";
				}
			}
		}
		$all = implode(',', $columnas);
		$texto = "$js\nvar {$var} = {
            xtype: 'portal',
            region: 'center',
            items: [{$all}]
		}\n";

		return $texto;
	}
}

/* End of file logger.php */
/* Location: ./system/libraries/logger.php */