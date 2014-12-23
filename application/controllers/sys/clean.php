<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Limpieza
 * @author alexl
 *
 */
class Clean extends MY_Controller
{

	private $_count = 0;

	private $_blacklist = array();

	/**
	 * Constructor
	 *
	 * @return Test
	 */
	function __construct()
	{
		parent::__construct(null, null, FALSE);

		$this->_blacklist = $this->config->item('temp.blacklist');
	}

	/**
	 * Limpia los emails de clientes incorrectos
	 * @return CLI
	 */
	function clean_emails_clientes()
	{
		if (isset($_SERVER['REMOTE_ADDR'])) {
			die($this->lang->line('task-runner-cmd-only'));
		}

		set_time_limit(0);

		$this->load->helpers('formatters');
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_contacto');
		$ar = $this->m_email->get();
		//print_r($ar);
		$count = 0;
		$error = 0;
		$total = count($ar);
		print "Analizando {$total} emails Emails\n";
		foreach ($ar as $a)
		{
			$count++;
			if (!valid_email(trim($a['cEMail'])))
			{
				$error++;
				print "{$total}/{$count} - {$error} - NO {$a['cEMail']}\n";
				$a['cNombre'] = $a['cEMail'];
				$this->m_contacto->insert($a);
				$this->m_email->delete($a['nIdEmail']);
			}
			else
			{
				print "{$total}/{$count} - SI {$a['cEMail']}\n";
			}
		}
	}

	/**
	 * Limpia los emails de contactos incorrectos
	 * @return CLI
	 */
	function clean_emails_contacto()
	{
		if (isset($_SERVER['REMOTE_ADDR'])) {
			die($this->lang->line('task-runner-cmd-only'));
		}

		set_time_limit(0);

		$this->load->helpers('formatters');
		$this->load->model('perfiles/m_email');
		$this->load->model('perfiles/m_contacto');
		$this->load->model('mailing/m_contactoemail');
		$this->load->model('mailing/m_contactocontacto');
		$ar = $this->m_email->get();
		//print_r($ar);
		$count = 0;
		$error = 0;
		$total = count($ar);
		print "Analizando {$total} emails Contactos\n";
		foreach ($ar as $a)
		{
			if (!valid_email(trim($a['cEMail'])))
			{
				print "{$total}/{$count} - {$error} - NO {$a['cEMail']}\n";
				$a['cNombre'] = $a['cEMail'];
				$co = $this->m_contactoemail->get(null, null, null, null, "nIdEmail = {$a['nIdEmail']}");
				if (isset($co[0]))
				{
					$idc = $co[0]['nIdContacto'];
					$id = $this->m_contacto->insert($a);
					print "{$id}\n";
					$this->m_contactocontacto->insert(array(
						'nIdContactoMailing' 	=> $id, 
						'nIdContacto'			=> $idc)
					);
				}
				else
				{
					print "No está vinculado\n";
				}
				$this->m_contactoemail->delete_by("nIdEmail = {$a['nIdEmail']}");
				$this->m_email->delete($a['nIdEmail']);

				$error++;
			}
			else
			{
				print "{$total}/{$count} - SI {$a['cEMail']}\n";
			}
			$count++;
		}
	}

	/**
	 * Limpia los emails de newsletter web incorrectos
	 * @return CLI
	 */
	function clean_emails_web()
	{
		if (isset($_SERVER['REMOTE_ADDR'])) {
			die($this->lang->line('task-runner-cmd-only'));
		}

		set_time_limit(0);

		$this->load->helpers('formatters');
		$this->load->model('web/m_newsletter');
		$ar = $this->m_newsletter->get();
		$count = 0;
		$error = 0;
		$total = count($ar);
		print "Analizando {$total} emails Web\n";
		foreach ($ar as $a)
		{
			$count++;
			if (!valid_email(trim($a['cEmail'])))
			{
				$error++;
				print "{$total}/{$count} - {$error} - NO {$a['cEmail']}\n";
				$this->m_newsletter->delete($a['nIdNewsletter']);
			}
			else
			{
				print "{$total}/{$count} - SI {$a['cEmail']}\n";
			}
		}
	}

	/**
	 * Borra recursivamente un directorio. No elimina los archivos indicados en el blacklist de la configuración
	 * 
	 * @param  string $dir  Directorio a limpiar
	 * @param  int $time Fecha mínima a mantener los archivos
	 * @return NULL
	 */
	private function _clean_dir($dir, $time)
	{
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					$type = filetype($dir . $file);
					$time2 = filectime($dir . $file);
					if ($type == 'file')
					{
						#echo "filename: {$file}: filetype: {$time2}\n";
						if (datediff('h', $time2, time(), TRUE) > $time)
						{
							#echo "Eliminando filename: {$file}\n";
							++$this->_count;
							unlink($dir . $file);
						}
					}
					else if (($type == 'dir') && ($file != '.') && ($file != '..') && (!in_array($file, $this->_blacklist)))
					{
						$this->_clean_dir($dir . $file . DS, $time);
					}
				}
				closedir($dh);
			}
		}
	}

	/**
	 * Limpia lso ficheros temporales
	 * @return CLI
	 */
	function clean_temp()
	{
		/*if (isset($_SERVER['REMOTE_ADDR'])) {
			die($this->lang->line('task-runner-cmd-only'));
		}*/

		set_time_limit(0);

		$this->_count = 0;

		$time = $this->config->item('bp.temporal.time');
		$this->_clean_dir(DIR_TEMP_PATH, $time);

		$this->out->success("Se han eliminado {$this->_count} ficheros temporales");
	}

	/**
	 * Minimiza tamaños ficheros Javascript
	 */
	function jsmin()
	{
		$this->load->helper('asset');
		$this->load->plugin('jsmin');
		
		$jsall = js_asset_path('lib.js');
		$stamp = js_asset_path('jsmin.stamp');
		$all = '';
		$stamps = array();
		if (file_exists($stamp))
		{
			$stamps = file_get_contents($stamp);
			$stamps = unserialize($stamps);
		}

		print '<pre>';
		print "Comprimiendo archivos JS:\n";
		$js_files = $this->config->item('js_files');
		#$dbg = $this->config->item('js.debug');
		foreach($js_files as $js_file)
		{
			if (!isset($js_file[1]))
			{
				$file = js_asset_path($js_file[0]. '.js');
				print "$file\n";
				if (file_exists($file))
				{
					$time = filemtime($file);
					$file2 = str_replace('.js' ,'.min.js', $file);
					if ((isset($stamps[$file]) && ($stamps[$file] != $time))||(!isset($stamps[$file])))
					{
						print " ->$file2\n";
						file_put_contents($file2, JSMin::minify(file_get_contents($file)));
						$stamps[$file] = $time;
					}
					else
					{
						print " -> SIN CAMBIOS\n";
					}
					$all .= file_get_contents($file2);
				}
				else
				{
					print " -> NO EXISTE FICHERO\n";
				}
			}
			else
			{
				$all .= file_get_contents(js_asset_path($js_file[1]. '.js'));
			}
		}
		file_put_contents($stamp, serialize($stamps));
		file_put_contents($jsall, $all);
		print "----> $jsall\n";
		print '</pre>';
	}

	/**
	 * Minimiza tamaños ficheros Javascript
	 */
	function cssmin()
	{
		$this->load->helper('asset');
		$this->load->plugin('cssmin');
		
		$cssall = css_asset_path('styles.css');
		$stamp = css_asset_path('css.stamp');
		$all = '';
		$stamps = array();
		if (file_exists($stamp))
		{
			$stamps = file_get_contents($stamp);
			$stamps = unserialize($stamps);
		}

		print '<pre>';
		print "Comprimiendo archivos CSS:\n";
		$css_files = $this->config->item('css_files');
		#$dbg = $this->config->item('js.debug');
		foreach($css_files as $css_file)
		{
			$file = css_asset_path($css_file[0]. '.css', isset($css_file[1])?$css_file[1]:null);
			print "$file\n";
			if (file_exists($file))
			{
				$time = filemtime($file);
				$file2 = str_replace('.css' ,'.min.css', $file);
				if ((isset($stamps[$file]) && ($stamps[$file] != $time))||(!isset($stamps[$file])) || (!file_exists($file2)))
				{
					print " ->$file2\n";
					file_put_contents($file2, CssMin::minify(file_get_contents($file)));
					$stamps[$file] = $time;
				}
				else
				{
					print " -> SIN CAMBIOS\n";
				}
				$all .= file_get_contents($file2);
			}
			else
			{
				print " -> NO EXISTE FICHERO\n";
			}
		}
		file_put_contents($stamp, serialize($stamps));
		file_put_contents($cssall, $all);
		print "----> $cssall\n";
		print '</pre>';
	}
}

/* End of file clean.php */
/* Location: ./system/application/controllers/sys/clean.php */