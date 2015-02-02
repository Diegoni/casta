<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Festivos
 *
 */
class Festivo extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Festivo
	 */
	function __construct()	
	{
		parent::__construct('calendario.festivo', 'calendario/M_Festivo', true, null, 'Festivos');
	}

	function importar($text = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$isbns = isset($isbns)?$isbns:$this->input->get_post('isbns');
		if (!empty($isbns))
		{
			set_time_limit(0);
			$dias = explode("\n", $isbns);
			$this->db->trans_begin();
			$count = 0;
			foreach ($dias as $value) 
			{
				$dia = explode(',', $value);
				if (count($dia) >= 2)
				{
					$fecha = explode('-', $dia[0]);
					$data = array(
						'dDia' => mktime(0, 0, 0, $fecha[1], $fecha[0], $fecha[2]),
						'cDescripcion'	=> trim($dia[1])
						);					
					if (!($id=$this->reg->insert($data)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('importar-festivos-ok'), $count));
		}
		$data = array(
			'title' => $this->lang->line('Importar festivos'),
			'icon' 	=> 'icon-tool',
			'url'	=> 'calendario/festivo/importar',
			'stock' => 'false'
		);
		$this->_show_js('upd', 'catalogo/check.js', $data);
	}
}

/* End of file festivo.php */
/* Location: ./system/application/controllers/calendario/festivo.php */