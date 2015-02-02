<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Limpieza
 *
 */
class Limpieza extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Limpieza
	 */
	function __construct()
	{
		parent::__construct('concursos.limpieza', 'concursos/M_limpieza');
	}

	/**
	 * Arregla los ISBNS
	 * @param string $db Catálogo en la bbdd
	 * @return HTML
	 */
	function isbns($db)
	{
		set_time_limit(0);
		$data = $this->reg->isbns($db);
		$this->load->library('ISBNEAN');
		echo '<pre>';
		foreach($data as $reg)
		{
			$isbn = $this->isbnean->to_isbn($reg['cISBN']);
			if (isset($isbn))
			{
				$r['cEAN'] 		= $this->isbnean->to_ean($isbn);
				$r['cISBNBase'] = $this->isbnean->clean_code($isbn);
				$r['cISBN']		= $isbn;
				$this->db->flush_cache();
				$this->db->where("nIdLibro = {$reg['nIdLibro']}");
				$this->db->update("{$db}..Diba_LineasPedido", $r);
				echo "{$reg['nIdLibro']} => {$reg['cISBN']} => {$isbn}\n";
			}
		}
		echo '</pre>';
	}

	/**
	 * Arregla las editoriales
	 * @param string $db Catálogo en la bbdd
	 * @return HTML
	 */
	function editoriales($db)
	{
		set_time_limit(0);
		$this->load->library('ISBNEAN');
		$this->load->model('catalogo/m_editorial');
		$data = $this->reg->editoriales($db);
		echo '<pre>';

		$si = $no = 0;
		foreach($data as $reg)
		{
			$ok = FALSE;
			$isbn = $this->isbnean->to_isbn($reg['cISBN']);
			if (isset($isbn))
			{
				$data['parts'] = $this->isbnean->isbnparts($isbn);
				if (isset($data['parts']))
				{
					$editorial = $this->m_editorial->search($data['parts']['publisher_id'], 0, 1);
					if (count($editorial) > 0)
					{
						$ed = $this->m_editorial->load($editorial[0]['id']);
						$r['nIdEditorial'] = $editorial[0]['id'];
						$r['nIdProveedor'] = $ed['nIdProveedor'];
						$this->db->flush_cache();
						$this->db->where("nIdLibro = {$reg['nIdLibro']}");
						$this->db->update("{$db}..Diba_LineasPedido", $r);
						echo "{$reg['nIdLibro']} => {$r['nIdEditorial']} => {$r['nIdProveedor']}\n";
						++$si;
						$ok = TRUE;
					}
				}
			}
			if (!$ok) ++$no;
		}
		echo "SI: {$si}, NO: {$no}\n";
		echo '</pre>';
	}

	function editoriales2($db)
	{
		set_time_limit(0);
		$this->load->library('ISBNEAN');
		$this->load->model('catalogo/m_editorial');
		$data = $this->reg->editoriales2($db);
		echo '<pre>';
		print_r($data);
		$editoriales = array();
		foreach($data as $reg)
		{
			$isbn = $this->isbnean->to_isbn($reg['cISBN']);
			echo "{$isbn}\n";
			if (isset($isbn))
			{
				$data['parts'] = $this->isbnean->isbnparts($isbn);
				if (isset($data['parts']))
				{
					$editorial = $this->m_editorial->search($data['parts']['publisher_id'], 0, 1);
					if (count($editorial) > 0)
					{
						$ed = $this->m_editorial->load($editorial[0]['id']);
						$editoriales[$editorial[0]['id']] = $ed;
					}
				}
			}
		}
		print_r($editoriales);
		echo '</pre>';
	}

	function descuentos($db)
	{
		set_time_limit(0);
		$data = $this->reg->descuentos($db);
		$this->load->model('catalogo/m_articulo');
		foreach($data as $reg)
		{
			$d = $this->m_articulo->get(null, null, null, null, "cISBN='{$reg['cISBN']}'");
			if (count($d) > 0)
			{
				$l = $this->m_articulo->load($d[0]['nIdLibro']);
				//$prv = $this->m_articulo->get_proveedor_habitual($l);
				//$dto = $this->m_articulo->get_descuento($l['nIdLibro'], $prv);
				$dto = (1 - $l['fPrecioCompra'] / $l['fPrecio']) * 100; 
				$this->db->flush_cache();
				$this->db->where("nIdLibro = {$reg['nIdLibro']}");
				$this->db->update("{$db}..Diba_LineasPedido", array('fDescuento' => format_tofloat($dto)));
				echo "{$reg['nIdLibro']} => {$l['nIdLibro']} => {$dto}\n";
			}
		}
	}
}

/* End of file Limpieza.php */
/* Location: ./system/application/controllers/concursos/limpieza.php */
