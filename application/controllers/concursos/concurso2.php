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
 * Concursos old school
 *
 */
class Concurso2 extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Concurso2
	 */
	function __construct()
	{
		parent::__construct('concursos.concurso', null, TRUE, null, 'Concursos');
	}

	/**
	 * Realiza una búsqueda por palabra clave
	 *
	 * @param string $query Palabra de búsqueda
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param string $where Campos WHERE
	 */
	function search($query = null, $start = null, $limit = null, $sort = null, $dir = null, $where = null)
	{
		$concursos = $this->config->item('bp.concursos');
		$data = array();
		foreach($concursos as $c)
		{
			$data[] = array('id' => $c, 'text' => $c);
		}
		$this->out->data($data);
	}

	/**
	 * Muestra un formulario para consultar el precio e indicar que está catalogado
	 * @return FORM
	 */
	function precios()
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$this->_show_form('get_list', 'concursos/precios.js', $this->lang->line('Consulta de precios'));
	}

	/**
	 * Lee el precio y cambia el estado a CATALOGADO de una línea de pedido de una biblioteca dada
	 * @param  string $code  ISBN/EAN/Título
	 * @param  int $biblioteca Id de la biblioteca
	 * @return DATA
	 */
	function check_precio($code = null, $biblioteca = null, $catalogar = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$code = isset($code) ? $code : $this->input->get_post('code');
		$biblioteca = isset($biblioteca) ? $biblioteca : $this->input->get_post('biblioteca');
		$catalogar = isset($catalogar) ? $catalogar : $this->input->get_post('catalogar');
		$catalogar = format_tobool($catalogar);
		#var_dump($catalogar); die();

		$data['success'] = TRUE;
		if (trim($code) != '') 
		{
			$this->load->library('ISBNEAN');
			$this->load->model('concursos/m_lineapedido');
			$ean = $this->isbnean->to_ean($code);
			$isbn = $this->isbnean->to_isbn($code);
			#var_dump($code, $ean, $isbn); die();

			if ($ean || $isbn)
			{
				$isbn_e = $this->db->escape($isbn);
				$ean_e = $this->db->escape($ean);
				if ($isbn)
				{
					$where[] = "cISBN={$isbn_e}";
				}
				if ($ean)
				{
					$where[] = "cEAN={$ean}  OR cISBN={$ean_e}";
				}
				$where = implode(' OR ', $where);

				$where = "nBiblioteca = {$biblioteca} AND ({$where})";
			}
			elseif (is_numeric($code))
			{
				#var_dump($code); die();
				#$l = $this->m_lineapedido->load($code);
				#var_dump($l); die();
				#if ($l)
				#	 $l[] = $l;
				$where = 'nIdLibro='.$code;
			}
			else
			{
				$this->load->helper('parsersearch');
				$value = $this->db->escape_str($code);
				$w = boolean_sql_where($value, 'cTitulo');
				#var_dump($w);
				$where = "nBiblioteca = {$biblioteca} AND {$w}";
			}
			if ($catalogar)
				$where .= ' AND nIdEstado IN (2, 19, 20)';

			$l = $this->m_lineapedido->get(null, null, null, null, $where);
			#print(array_pop($this->db->queries)); die();
			if (count($l) > 0)
			{
				if (count($l) > 1)
				{
					$data['cTitulo'] = $this->lang->line('---VARIOS---');
					foreach ($l as $value) 
					{
						$data['cTitulo'] .=  '<br/>(' . $value['nIdLibro'] . ') ' . $value['cTitulo'] . ' [' . $value['cEstado'] . ']';
					}
				}
				else
				{
					$data = $l[0];
					$data['fPrecio'] = $l[0]['fPrecio'];
					$data['success'] = TRUE;
					if (in_array($data['nIdEstado'], array(2, 19, 20)) && $catalogar)
					{
	 					$this->m_lineapedido->update($data['nIdLibro'], array(
							'nIdEstado' => 17
						));
	 					$data['cTitulo'] = $this->lang->line('---CATALOGADO---') . '<br/>' . $data['cTitulo'];
	 				}
	 				elseif ($catalogar)
	 				{
	 					$data['cTitulo'] = $this->lang->line('---NO SE CATALOGA---') . '<br/>' . $data['cTitulo'] . ' [' . $data['cEstado'] . ']';
	 				}
	 				else
	 				{
 						$data['cTitulo'] = $data['cTitulo'] . ' [' . $data['cEstado'] . ']';
	 				}
				}
			}
			else 
			{
				$data['success'] = TRUE;	
				$data['cTitulo'] = $this->lang->line('---NO ENCONTRADO---');
			}				
		}
		$this->out->send($data);
	}

	/**
	 * Muestra un formulario para crear los teixells
	 * @return FORM
	 */
	function teixells()
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$this->_show_form('get_list', 'concursos/teixells.js', $this->lang->line('Teixells'));
	}

	/**
	 * Añade un teixell a la cola del usuario
	 * @param string $code Texto
	 * @return  MSG
	 */
	function add_teixell($code = null, $cantidad = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$code = isset($code) ? $code : $this->input->get_post('code');
		$cantidad = isset($cantidad) ? $cantidad : $this->input->get_post('cantidad');

		if (trim($code) != '') 
		{
			$this->load->model('concursos/m_teixell2');
			if (!is_numeric($cantidad) || $cantidad < 1) $cantidad = 1;
			do {
				if (!$this->m_teixell2->insert(array('cTexto' => $code)))
					$this->out->error($this->m_teixell2->error_message());
				--$cantidad;
			} while ($cantidad > 0);
		}
		$this->out->success();
	}

	/**
	 * Imprime los teixells
	 * @param  string $username Usuario
	 * @return MSG
	 */
	function imprimir_teixells($username = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$this->load->model('concursos/m_teixell2');
		$username = isset($username) ? $username : $this->input->get_post('username');
		if (empty($username)) 
			$username = $this->userauth->get_username();
		if (!empty($username)) 
		{
			$this->load->library('Configurator');
			$report = $this->configurator->user('concursos.teixells.formato');
			$title = urlencode($this->lang->line('Teixell'));
			$username = $this->db->escape($username);
			$data = $this->m_teixell2->get(null, null, 'dCreacion', 'ASC', "cCUser={$username} AND nGrupo IS NULL");
			$count = 0;
			$upd = array('nGrupo' => time());
			$this->load->library('Etiquetas');
			foreach ($data as $value) 
			{
				$html = $this->show_report(null, array('etiquetas' => array($value)), $report, null, FALSE, null, FALSE, FALSE);
				$this->etiquetas->print_direct($html, $title);
				$this->m_teixell2->update($value['nIdTeixell'], $upd);
				++$count;
			}
			$this->out->success(sprintf($this->lang->line('n-Etiquetas imprimidas'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Obtiene los teixells
	 * @param  string $username Usuario
	 * @return MSG
	 */
	function get_teixells($username = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$this->load->model('concursos/m_teixell2');
		$username = isset($username) ? $username : $this->input->get_post('username');
		if (empty($username)) 
			$username = $this->userauth->get_username();
		if (!empty($username)) 
		{
			$username = $this->db->escape($username);
			$data = $this->m_teixell2->get(null, null, 'dCreacion', 'ASC', "cCUser={$username} AND nGrupo IS NULL");
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Vuelve a dejar los teixells listos para imprimir
	 * @param  int $grupo Grupo a resetear
	 * @return MSG
	 */
	function reset_grupo($grupo = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$this->load->model('concursos/m_teixell2');
		if (is_numeric($grupo)) 
		{
			$data = $this->m_teixell2->get(null, null, 'dCreacion', 'ASC', "nGrupo={$grupo}");
			foreach ($data as $value) 
			{
				$this->m_teixell2->update($value['nIdTeixell'], array('nGrupo' => NULL));
			}
			$this->out->success(sprintf($this->lang->line('n-Etiquetas resetadas'), count($data)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file concurso2.php */
/* Location: ./system/application/controllers/concursos/concurso2.php */