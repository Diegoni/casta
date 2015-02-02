<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Boletines
 * @author alexl
 *
 */
class Boletin extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Boletin
	 */
	function __construct()
	{
		parent::__construct('mailing.boletin', 'mailing/M_boletin', TRUE, 'mailing/boletin.js', 'Boletín');

	}

	/**
	 * Genera un listado de artículos que no tienen port
	 * @param int $id Id del boletín
	 * @return HTML_FILE
	 */
	function sinportada($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id 		= isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$data = $this->reg->sinportada($id);
			if (count($data) > 0)
			{
				$data['articulos'] = $data;
				$message = $this->load->view('mailing/sinportada', $data, TRUE);
				$this->out->html_file($message, $this->lang->line('Artículos sin portada en boletín') . ' ' . $id, 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-sinportada'));

		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		//Cargar los datos de cada libro
		$this->load->model('catalogo/m_articulo');
		$libros = array();
		foreach ($data['libros'] as $libro)
		{
			$libros[] = $this->m_articulo->load($libro['id'], TRUE);
		}
		$data['libros'] = $libros;
		$css = $this->config->item('bp.mailing.css');
		return TRUE;
	}

	/**
	 * Genera un mailing a partir de un boletín
	 * @param int $id Id del boletín
	 * @param string $report Nombre del report base
	 * @return JSON
	 */
	function mailing($id = null, $report = null)
	{
		$this->userauth->roleCheck(($this->auth .'.mailing'));
		$id = isset($id)?$id:$this->input->get_post('id');
		$report = isset($report)?$report:$this->input->get_post('report');
		if ($id && $report)
		{
			//Los datos
			$data = $this->reg->load($id);
			$text = $this->printer($id, $report, null, FALSE);

			// Crea el mailing
			$this->load->model('mailing/m_mailing');
			$mailing = array(
				'cDescripcion'	=> $data['cDescripcion'],
				'cAsunto'		=> $data['cDescripcion'],
				'cBody'			=> $text
			);
			$idm = $this->m_mailing->insert($mailing);
			$cmd = 'mailing/mailing.index;' . $idm;
			$this->out->cmd($cmd);
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Promociona en la web los artículos de un boletín
	 * @param int $id Id del boletín
	 * @return MSG
	 */
	function promocionar($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.promocionar'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			//Los datos
			$data = $this->reg->load($id, TRUE);
			$this->load->model('catalogo/m_promocion');
			$count = 0;
			foreach ($data['libros'] as $libro)
			{				
				$reg = array(
					'nIdLibro' 			=> $libro['id'],
					'cDescripcion' 		=> $data['cDescripcion'],
					'nIdTipoPromocion' 	=> $this->config->item('bp.promocion.idweb'),
					'dInicio' 			=> time(),
					'dFinal' 			=> strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " +30 day")
				);
				$id = $this->m_promocion->insert($reg);
				if ($id < 0)
				{
					$this->out->error($this->m_promocion->error_message());
				}
				
				++$count;
			}
			$this->out->success(sprintf($this->lang->line('boletin-promocion-ok'), $count));
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}	
	} 	

	/**
	 * Publica el boletín en el blog de ALIBRI
	 * @param int $id Id del boletín
	 * @return MSG
	 */
	function publicar($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.publicar'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			# Se logea en la web
			$this->load->library('Webshop');
			$server = $this->config->item('bp.webshop.server');
			$username = $this->config->item('bp.webshop.username');
			$pasword = $this->config->item('bp.webshop.password');
			$res = $this->webshop->login($server, $username, $pasword);

			if (!$res)
			{
				$this->out->error($this->lang->line('webshop-error-login'));
			}

			# Los datos
			$data = $this->reg->load($id, TRUE);
			$libros = array();
			foreach ($data['libros'] as $value) 
			{
				$libros[] = $value['id'];
			}
			
			# Crea el newsletter
			$filter = array(
				'title' => $data['cDescripcion'],
				'description' => $data['cDescripcionCorta'],
				'books' => implode(';', $libros)
			);
			# La llamada
			#$this->webshop->debug = TRUE;
			$res = $this->webshop->action('api/blog/newsletter', $filter);
			if (!$res)
			{				
				$this->out->error($this->webshop->get_error());
			}
			if ($res['success'])
			{
				$url = "<a href='javascript:Ext.app.addTabJSONHTMLFILE({
								html_file : \"{$res['url']}\",
								icon : \"iconoWebTab\",
								title : \"{$data['cDescripcion']}\"
							});'>{$res['url']}</a>";
				$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('boletin-publicar-history'));
				$this->out->success(sprintf($this->lang->line('boletin-publicar-ok'), $url));
			}
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}	
	}
}

/* End of file boletin.php */
/* Location: ./system/application/controllers/mailing/boletin.php */