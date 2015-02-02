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

class Solr extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('tools.solr', null, TRUE);
	}

	function libros()
	{
		$this->userauth->roleCheck(($this->auth .'.solr'));
		$this->load->library('tasks');
		$runner = $this->userauth->get_username();
		$cmd = site_url("tools/solr/libros/{$runner}");
		$id_task = $this->tasks->add(sprintf($this->lang->line('solr-task-create'), $id) , $cmd);
		$message = sprintf($this->lang->line('solr-task-cola'), $id_task);
		$success = TRUE;

		// Respuesta
		echo $this->out->message($success, $message);
	}

	function libros($runner = null)
	{
		$this->userauth->roleCheck(($this->auth .'.libros'));

		if (isset($runner))
		{
			$this->userauth->set_username($runner);
		}
		
		

		// Respuesta
		if (isset($runner))
		{
			// Envía un mensaje
			//$msg = $this->out->message($success, $message, FALSE);
			$msg = $message;
			$this->load->library('Mensajes');
			$this->userauth->set_username();
			$this->mensajes->usuario($runner, $msg);
			echo $message;
			exit;
		}
		$this->out->message($success, $message);
	}

		private function _createXML()
	{
		return 	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<add>\n";
	}

	private function _finishXML()
	{
		return 	"</add>\n";
	}

	function _clean($input)
	{
		$output = '';
		for ($i = 0; $i < strlen($input); $i++)
		{
			if (ord($input[$i])>= 32 && ord($input[$i]) <= 255)
			{
				$output .= $input[$i];
			}
		}
		return $output;
	}

		private function _create_docs($name, $tipo, $sql)
	{
		set_time_limit(0);
		echo '<pre>';

		// Fichero
		$ext = '.xml';
		$filename = DIR_FS_CACHE . $name;
		$i = 0;
		$count = 0;

		$q = $this->obj->getDatabase()->query($sql);

		// XML

		$xmldoc = $this->_createXML();

		$last_id = null;
		$act = array();
		while ($ar = $this->obj->getDatabase()->fetch_array($q))
		{
			if (isset($last_id) && ($last_id != $ar['nIdLibro']))
			{
				$xmldoc .= $this->_create($act);
				$act = array();
				$count++;
				if ($count >= MAX_ITEMS)
				{
					$xmldoc .= $this->_finishXML();
					$fl = $filename . $i . $ext;
					file_put_contents($fl, $xmldoc);
					echo $fl ."\n";
					$xmldoc = $this->_createXML();
					$count = 0;
					$i++;
				}
			}
			$ar['id'] = $tipo . $ar['id'];
			$ar['tipo'] = $tipo;
			foreach($ar as $k => $v)
			{
				$v = (trim($v));
				$v = $this->_clean(htmlspecialchars($v));
				if ($v != '')
				{
					$act[$k][$v] = $v;
				}
			}
			$last_id = $ar['nIdLibro'];
		}
		if (count($act) > 0)
		{
			$xmldoc .= $this->_create($act);

		}
		$fl = $filename . $i . $ext;
		$xmldoc .= $this->_finishXML();
		file_put_contents($fl, $xmldoc);
		echo $fl ."\n";
		echo '</pre>';
	}

	function _create($act)
	{
		$xmldoc ="<doc>\n";
		foreach($act as $k => $v)
		{
			if (is_array($v))
			{
				foreach($v as $v2)
				{
					$xmldoc .= "<field name=\"{$k}\">{$v2}</field>\n";
				}
			}
			else
			{
				$xmldoc .= "<field name=\"{$k}\">{$v}</field>\n";
			}
		}

		$xmldoc .= "</doc>\n";
		return $xmldoc;
	}
	
}
/* End of file mailing.php */
/* Location: ./system/application/controllers/mailing/mailing.php */
