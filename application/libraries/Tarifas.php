<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */


/**
 * Gestor de tarifas
 * @author alexl
 *
 */
class Tarifas {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	var $obj;

	/**
	 * Constructor
	 * @return Task
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		//$this->obj->load->model('sys/m_tarea');
		log_message('debug', 'Tasks Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Calcula el cambio entre dos divisas, pasando siempre por la divisa por defecto
	 * @param float $precio Importe
	 * @param int $id1 ID divisa 1
	 * @param int $idID divisa 2
	 */
	function cambiar($precio, $id1, $id2 = null)
	{
		$default = $this->obj->config->item('bp.divisa.default');
		if (!isset($id2)) $id2 = $default;
		$this->obj->load->model('generico/m_divisa');
		$divisa1 = $this->obj->m_divisa->load($id1);
		$divisa2 = $this->obj->m_divisa->load($id2);

		if ($id1 == $default)
		{
			// De la divisa default a otra
			if ($id2 == $default)
			{
				// Es la misma divisa
				return array(
					'divisa1'	=> $divisa1['cDescripcion'],
					'divisa2'	=> $divisa2['cDescripcion'],
					'importe' 	=> $precio,
					'cambio'	=> 1
				);
			}
			if (isset($divisa2))
			{
				$venta = $divisa2['fCompra'];
				return array(
					'divisa1'	=> $divisa1['cDescripcion'],
					'divisa2'	=> $divisa2['cDescripcion'],
					'importe' 	=> $precio * $venta,
					'cambio'	=> $venta
				);
			}
			return null;
		}
		elseif ($id2 == $default)
		{
			// De otra divisa a la default
			$divisa1 = $this->obj->m_divisa->load($id1);
			if (isset($divisa1))
			{
				$compra = $divisa1['fCompra'];
				return array(
					'divisa1'	=> $divisa1['cDescripcion'],
					'divisa2'	=> $divisa2['cDescripcion'],
					'importe' 	=> $precio / $compra,
					'cambio'	=> $compra
				);
			}
			return null;
		}
		
		// 2 divisas distintas
		if (isset($divisa1) && isset($divisa2))
		{
			$venta = $divisa2['fCompra'];
			$compra = $divisa1['fCompra'];
			return array(
				'divisa1'	=> $divisa1['cDescripcion'],
				'divisa2'	=> $divisa2['cDescripcion'],
				'importe' 	=> ($precio * $venta) / $compra,
				'cambio'	=> $venta / $compra
			);
		}
		return null;
	}

	/**
	 * Calcula las tarifas de venta de un artículo
	 * @param float $precio Precio original en divisa
	 * @param int $divisa ID de la divisa
	 * @param float $dto Descuento (en %)
	 * @param float $portes Importe de los portes
	 * @param int $tipo ID del tipo de artículo
	 */
	function get_tarifas($precio, $divisa, $dto, $portes, $tipo)
	{
		#$this->obj->load->model('generico/m_divisa');
		$this->obj->load->model('catalogo/m_tipolibro');
		$this->obj->load->model('ventas/m_tipotarifa');
		
		$cambio = $this->cambiar($precio, $divisa);
		$default = $this->obj->config->item('bp.divisa.default');
		
		$margen_moneda = ($divisa != $default) ? $this->obj->config->item('bp.divisa.margenmoneda'): 0;
		$tipo = $this->obj->m_tipolibro->load($tipo);
		$tarifas = $this->obj->m_tipotarifa->get();
		$base = ($cambio['importe']) * (1 + ($margen_moneda / 100));
		$datos = array(
				'divisa'			=> $cambio['divisa1'],
				'divisa2'			=> $cambio['divisa2'],
				'cambio'			=> $cambio['cambio'],
				'importe'			=> $cambio['importe'],	
				'margen_moneda'		=> $margen_moneda,
				'iva'				=> $tipo['fIVA'],
		);
		
		foreach ($tarifas as $t)
		{
			$tf = array();
			$valor = $base;
			$tf['base'] 	= $valor;
			if ($t['bDescuentoProveedor']) $valor *= (1 - ($dto/100));
			$tf['base_dto'] 	= $valor;
			
			$valor /= (1 - ($t['fMargen'] / 100));
			$tf['base_margen'] 	= $valor;
			
			if ($t['bPortes']) $valor += $portes;
			$tf['base_portes'] 	= $valor;
			
			$tf['text'] 	= $t['cDescripcion'];
			$tf['margen'] 	= $t['fMargen'];
			$tf['portes'] 	= $t['bPortes'];
			$tf['dto'] 		= $t['bDescuentoProveedor'];
			$tf['importe'] 	= $valor;
			$tf['importeiva'] = $valor * (1 + ($tipo['fIVA'] / 100));
			$datos['tarifas'][] = $tf;
		}

		return $datos;
	}
}

/* End of file Tarifas.php */
/* Location: ./system/libraries/Tarifas.php */