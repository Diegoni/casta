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
 * Test del sistema
 * @author alexl
 *
 */
class Test extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Test
	 */
	function __construct()
	{
		parent::__construct(null, null, FALSE);
	}

	function index()
	{
		echo 'Soy el test';
	}

	function cli($p1 = null, $p2 = null, $p3 = null)
	{
		echo "TEST CLI OK:\nP1 = {$p1}\nP2 = {$p2}\nP3 = {$p3}\n";
	}

	function email()
	{

		$data['title'] = $this->lang->line('Unificar editorial');
		$data['icon'] = 'iconoUnficarEditorialTab';
		$data['url_search'] = site_url('catalogo/editorial/search');
		$data['url'] = site_url('catalogo/editorial/unificar');
		$this->_show_form(null, 'test/email.js', $this->lang->line('Email'), null, null, null, $data);

		$this->load->helper('asset');
		$this->load->helper('extjs');
		/*$js[] = array('ux/miframe-min.js');
		 $js[] = array('tiny_mce/lang/es.js');
		 $js[] = array('tiny_mce/tiny_mce.js');
		 $js[] = array('Ext.ux.TinyMCE.js');
		 $datos['js_include'] = $js;*/
		$datos['title'] = $this->lang->line('Test');
		$datos['script'] = $this->load->view('test/email.js', '', true);
		$this->load->view('main/main', $datos);
	}

	function albaran()
	{
		$this->load->helper('asset');
		$this->load->helper('extjs');
		//$js[] = array('ux/miframe-min.js');
		//$js[] = array('tiny_mce/lang/es.js');
		//$js[] = array('tiny_mce/tiny_mce.js');
		//$js[] = array('Ext.ux.TinyMCE.js');
		//$datos['js_include'] = $js;
		$datos['title'] = $this->lang->line('Albarán Entrada');
		$datos['script'] = $this->load->view('test/albaran.js', '', true);
		$this->load->view('main/main', $datos);
	}

	function isbn2ean($isbn = null)
	{
		$codes[] = '9788408081180';
		//$codes[] = '9788408043645';
		$codes[] = '978-0-88033-371-9';
		$codes[] = '978-0-674-99172-9';
		$codes[] = '977-0-88033-371-9';
		$codes[] = '977-0-674-99172-9';
		$codes[] = '978-0-8803';
		$codes[] = '978-0-674-99172';
		$codes[] = '878-0-674-99172';
		$codes[] = '0-88033-371-5';
		$codes[] = '0-88033-371-';
		$codes[] = '0-88033-371-5';
		$codes[] = '0880333715';
		$codes[] = '0-8803337';
		//print_r($codes);
		if (isset($isbn))
		{
			$codes[] = $isbn;
		}

		$this->load->library('ISBNEAN');

		print '<pre>';
		foreach ($codes as $code)
		{
			$isbn = $this->isbnean->to_isbn($code, TRUE);
			$isbn10 = $isbn['isbn10'];
			$isbn13 = $isbn['isbn13'];
			$ean = $this->isbnean->to_ean($code);
			$ean1 = $this->isbnean->to_ean($isbn10);
			$ean2 = $this->isbnean->to_ean($isbn13);
			$is_ean = $this->isbnean->is_ean($code) ? 'TRUE' : 'FALSE';
			$is_isbn10 = $this->isbnean->is_isbn($code, TRUE) ? 'TRUE' : 'FALSE';
			$is_isbn13 = $this->isbnean->is_isbn($code) ? 'TRUE' : 'FALSE';
			echo "CODIGO: {$code}\n";
			echo "--ES EAN: {$is_ean}\n--ES ISBN10: {$is_isbn10}\n--ES ISBN13: {$is_isbn13}\n";
			echo "--ISBN10: {$isbn10}\n--ISBN13: {$isbn13}\n";
			echo "--EAN: {$ean}\n--ISBN10->EAN: {$ean1}\n--ISBN13->EAN: {$ean2}\n";
			var_dump($this->isbnean->isbnparts($isbn13));
			echo "\n";

			/*echo "\$codes[] = array('{$code}', ";
			 echo "{$is_ean}, {$is_isbn10}, {$is_isbn13}, ";
			 echo "'{$isbn10}', '{$isbn13}', ";
			 echo "'{$ean}', '{$ean1}', '{$ean2}');\n";*/

		}
		print '</pre>';
	}

	function buscadorlibros()
	{
		/*$this->load->plugin('SearchRobot');
		 $this->load->plugin('Books');
		 $this->load->library('Cache');

		 $this->dir_robots = BASEPATH .'plugins/SearchRobots';
		 $r = SearchRobot::loadAll($this->dir_robots);
		 $id_c = 'Robots1';
		 apc_store($id_c, serialize($r), CACHE_DAY);
		 $r2 = unserialize(apc_fetch($id_c));
		 var_dump($r2); die();*/
		#echo '<pre>';
		#print_r(apc_sma_info());
		#echo '</pre>';
		#exit;
		//$this->output->enable_profiler(TRUE);
		//$this->load->plugin('SearchRobot');

		/*$this->load->plugin('Books');
		 $this->load->library('Cache');
		 */
		$this->load->model('M_buscadorlibros', 'reg');
		$codes = '9788408081180';
		$data = $this->reg->search($codes);
		//$data = $data[0]['codes'];
		echo '<pre>';
		print_r($data);
		echo '</pre>';

	}

	function svn()
	{
		/*
		 error_reporting(E_ALL); ini_set('display_errors', '1');
		 echo '<pre>';
		 require_once 'VersionControl/SVN.php';

		 // Setup error handling -- always a good idea!
		 $svnstack = &PEAR_ErrorStack::singleton('VersionControl_SVN');

		 //var_dump($svnstack);

		 // Set up runtime options.
		 $options = array(
		 //'url'		=> 'https://pacifico:8443/svn',
		 //'path'		=> 'bibliopola/branches/5.0/app',
		 //'username'	=> 'bibliopola',
		 //'svn_path'	=> $this->config->item('svn.path'),
		 'fetchmode'	=> VERSIONCONTROL_SVN_FETCHMODE_ARRAY
		 );

		 // Request list class from factory
		 $svn = VersionControl_SVN::factory('list', $options);
		 $svn->svn_path = $this->config->item('svn.path');

		 // Define any switches and aguments we may need
		 $switches = array(
		 'username'	=> 'bibliopola',
		 //'svn_path'	=> $this->config->item('svn.path')
		 );
		 $args = array('https://pacifico:8443/svn/bibliopola/branches/5.0/app');

		 // Run command
		 if ($output = $svn->run($args, $switches)) {
		 //echo 'OUT';
		 var_dump($output);
		 } else {
		 //echo 'ERRORS';
		 //var_dump($svnstack->getErrors());
		 if (count($errs = $svnstack->getErrors())) {
		 foreach ($errs as $err) {
		 echo '<br />'.$err['message']."<br />\n";
		 echo "Command used: " . $err['params']['cmd'];
		 }
		 }
		 }
		 echo '</pre>'; die();*/

		//$file = DIR_TEMP_PATH . '/svn.bat';
		$svn = $this->config->item('svn.cmd');
		$src = $this->config->item('svn.src');
		//$code = "CD {$src}\n{$svn}\n";
		//file_put_contents($file, $code);
		chdir($src);
		//echo getcwd();
		//die();
		ob_start();
		set_time_limit(0);
		passthru($svn, $res);
		$var = ob_get_contents();
		ob_end_clean();

		echo '<pre>';
		//echo $code;
		//echo $file . "\n";
		//echo $src. "\n";
		//echo $svn. "\n";
		//echo getcwd() ."\n";
		//echo "RES: {$res}\n";
		echo $var;
		echo '</pre>';
	}

	function test_phpmailer()
	{
		$this->load->plugin('phpmailer');
		$this->load->helper('asset');

		//Puede tardar
		set_time_limit(0);

		$config['Host'] = $this->config->item('bp.mailing.host');
		$config['SMTPAuth'] = $this->config->item('bp.mailing.auth');
		$config['Password'] = $this->config->item('bp.mailing.pass');
		$config['Username'] = $this->config->item('bp.mailing.user');
		// Se se indica email se usa como nombre
		$config['From'] = $this->config->item('bp.mailing.from');
		$config['FromName'] = $this->config->item('bp.mailing.fromname');

		$config['Mailer'] = $this->config->item('bp.mailing.protocol');
		$mail = new PHPmailerEx();
		$count = $mail->SendList(array('alex@alibri.es'), 'Test', 'Prueba de Email', $config);
		if ($count == 1)
		{
			echo 'OK';
		}
		else
		{
			echo $mail->ErrorInfo;
		}
	}

	function sleep($seconds, $title)
	{
		#echo DIR_TEMP_PATH .'threat1.txt';
		#$f = fopen(DIR_TEMP_PATH .'threat1.txt', 'a+');
		print "{$title} BEGIN " . date('h:i:s');

		// sleep for 10 seconds
		sleep($seconds);

		// wake up !
		print " END " . date('h:i:s');
	}

	function threat1()
	{
		$this->sleep(2, 'Thread ' . time());
	}

	function threat2()
	{
		$this->sleep(10, 'Thread ' . time());
	}

	function threat3($time)
	{
		header('Access-Control-Allow-Origin: *');
		$this->sleep($time, 'Thread3 ' . $time);
	}

	function threat4($time)
	{
		header('Access-Control-Allow-Origin: *');
		$this->sleep($time, 'Thread4 ' . $time);
	}

	function check_email($email)
	{
		$this->load->helpers('formatters');
		print valid_email($email) ? "VÁLIDO\n" : "NO VALIDO\n";
	}

	function test_email()
	{
		$this->load->plugin('swift');
		$config['Host'] = $this->config->item('bp.mailing.host');
		$config['SMTPAuth'] = $this->config->item('bp.mailing.auth');
		$config['Password'] = $this->config->item('bp.mailing.pass');
		$config['Username'] = $this->config->item('bp.mailing.user');
		$config['From'] = $this->config->item('bp.mailing.from');
		$config['FromName'] = $this->config->item('bp.mailing.fromname');
		$mail = new Mailer($config);

		set_time_limit(0);

		$list[] = array('cEmail' => 'alex@alibri.es');
		$list[] = array('cEmail' => 'alexaa@alibri.es');
		$list[] = array('cEmail' => 'alex@alibrias.es');
		$list[] = array('cEmail' => '  alex@alibri.es');
		$list[] = array('cEmail' => 'alex');
		// Envio
		$res = $mail->send($list, 'Test', 'Prueba de mensaje', null);
	}

	function clean_lines()
	{
		$dir = BASEPATH . 'application' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
		$this->clean_dir($dir);
	}

	private function clean_dir($dir)
	{
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					$type = filetype($dir . $file);
					echo "filename: {$file}: filetype: {$type}\n";
					if ($type == 'file')
					{
						$this->clean_file($dir . $file);
					}
					else
					if ($type == 'dir')
					{
						$this->clean_dir($dir . $file . DIRECTORY_SEPARATOR);
					}
				}
				closedir($dh);
			}
		}
	}

	function test_clean()
	{
		$file = BASEPATH . 'application' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'mailing.js';
		$this->clean_file($file);
	}

	private function clean_file($file)
	{
		$text = file_get_contents($file);
		$r = preg_match_all('/\"\<\?php.echo.\$this\-\>lang\-\>line\(\'(.*)\'\);.\?\>\"/', $text, $result);
		print_r($result);
		$r = preg_match_all('/\"\<\?php.echo.\$this\-\>lang\-\>line\(\'(.*)\'\);\?\>\"/', $text, $result);
		print_r($result);
		$text = preg_replace('/\"\<\?php.echo.\$this\-\>lang\-\>line\(\'(.*)\'\);.\?\>\"/', '_s(\'${1}\')', $text);
		file_put_contents($file, $text);
	}

	function clean_html()
	{
		$html = '<span class="azul2">Resumen del libro</span></div>
    <div class="txt_resumen prela">
        <strong>Primero</strong> nos explicó la crisis y ahora nos da las claves para superarla. <strong>Leopoldo Abadía</strong>&nbsp;nos propone en su nuevo libro, <em>La hora de los sensatos</em>,&nbsp;las mejores soluciones para salir de la crisis, y lo hace desde un punto de vista positivo y optimista. <br><br>Un texto fácil, ameno y cargado detalles de la vida cotidiana con el que el lector se identificará. Abadía demuestra en <em>La hora de los sensatos</em> que no hay que ser un gurú de la economía para entender que el sentido común es clave para salir adelante. <br><br>Después de <em>La crisis ninja</em>, el libro español de no ficción más vendido en el año 2009, Leopoldo Abadía lanza, con su personal tono cercano y desenfadado, una mirada reflexiva y crítica, en la que aparte de la economía, analiza también el mundo de la política y la sociedad.
    </div>';
		$this->load->plugin('html2text');
		$html2text = new html2text($html);
		echo '<pre>';
		echo $html2text->get_text();
		echo '</pre>';
		#$this->load->plugin('htmlcleaner');
		#$htmlclean = new HtmlCleaner($html);
		#$htmlclean->allowedTags($this->config->item('bp.boletin.allowtags'));
		#echo $htmlclean->GetCleanedHtml();
		echo strip_tags_attributes($html, $this->config->item('bp.boletin.allowtags.text'));
		#echo strip_html_tags($html);

		//lecho $this->out->html($message, $this->lang->line('Test'), 'iconReportTab');
	}

	function devolucion($id)
	{
		echo 'LOAD';
		$this->load->model('compras/m_devolucion');
		echo 'LOADED';
		$d = $this->m_devolucion->load($id, TRUE);
		echo '<pre>';
		print_r($d);
		echo '</pre>';
	}

	function cmp()
	{

		$a = array(
				'a',
				'A',
				'à',
				'á',
				'b',
				'B',
				'ö'
		);

		print '<pre>';

		/*setlocale (LC_COLLATE, 'C');
		 usort ($a, 'strcoll');
		 print_r ($a);

		 setlocale (LC_COLLATE, 'es_ES');
		 usort ($a, 'strcoll');
		 print_r ($a);

		 setlocale (LC_COLLATE, 'Spanish');*/
		usort($a, 'strcoll');
		print_r($a);

		print '</pre>';
		//setlocale(LC_LOCALE, 'Spanish');
		echo '<pre>';
		echo "Angel -> Ánhgel\n";
		echo strcoll("Angel", "Ángel") . "\n";
		echo "Bngel -> Ánhgel\n";
		echo strcoll("Bngel", "Ángel") . "\n";
		echo "Bngel -> Anhgel\n";
		echo strcoll("Bngel", "Angel") . "\n";
		print '</pre>';
	}

	function articulo($id = null)
	{
		if ($id != null)
		{
			$this->load->model('catalogo/m_articulo');
			$data = $this->m_articulo->load($id, TRUE);
			echo '<pre>';
			print_r($data);
			echo '</pre>';
		}
	}

	function busqueda()
	{
		$this->_show_form(null, 'mailing/busqueda.js', 'Búsqueda');
	}

	function parser()
	{
		$this->load->helper('parsersearch');
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';

		$search_string = 'george (governor,president) !responsibility';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'cNombre') . '</ul><br/>';
		$search_string = 'george    (governor,   president) !  responsibility';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'cNombre') . '</ul><br/>';
		$search_string = 'george and (governor or president) and not responsibility';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'cNombre') . '</ul><br/>';
		$search_string = 'george & (governor | president) & ! responsibility';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'cNombre') . '</ul><br/>';
		$search_string = '"george bush" & (governor | president) & ! responsibility';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'cNombre') . '</ul><br/>';
		$search_string = '"george and bush & (lo)" & (governor | president) & ! responsibility';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'cNombre') . '</ul><br/>';
		$search_string = 'L\'observaöáèàòìùtión coño';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'cNombre') . '</ul><br/>';
		$search_string = '<10 and >5 and <=9 | >=18';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'nLibros', 'number') . '</ul><br/>';
		$search_string = '<10.10 and >5.0 and <=9 | >=-5 -9';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'nLibros', 'number') . '</ul><br/>';
		$search_string = '<10/12/2009 and >5/9/2000 and <=9/1/1999 | >=18/11/99 !12/10/1998';
		echo "{$search_string}<br/><ul>" . boolean_sql_where($search_string, 'dCreacion', 'date') . '</ul><br/>';
		echo '</body></html>';
	}

	function proveedores($id = null, $idp = null)
	{
		if (isset($id))
		{
			echo '<pre>';
			print "Proveedores {$id}\n";
			$this->load->model('catalogo/m_articulo');
			$data = $this->m_articulo->get_proveedores($id);
			print_r($data);
			print "Descuento defecto\n";
			$dto = $this->m_articulo->get_descuento($id);
			var_dump($dto);
			if (isset($idp))
			{
				print "Descuento-Proveedor {$idp}\n";
				$dto = $this->m_articulo->get_descuento($id, $idp);
				var_dump($dto);
			}
			echo '</pre>';

		}
	}

	function articulos()
	{
		print "Articulos\n";
		$this->load->model('catalogo/m_articulo');
		$data = $this->m_articulo->load(121212, TRUE);
		print $data['fPVP'];
		//print_r($data);
	}

	function view()
	{
		#print_r($this);
		print $this->load->_ci_view_path;
	}

	function docs()
	{
		$this->load->model('catalogo/m_articulo');
		$id = 319128;
		$art = $this->m_articulo->load($id);
		$docs = $this->m_articulo->get_pedidos_cliente($id, null, null, TRUE);

		if (count($docs) > 0)
			sksort($docs, 'dFecha');
		$data['articulo'] = $art;
		$data['docs'] = $docs;
		$data['pendientes'] = TRUE;
		$message = $this->load->view('catalogo/pedidoscliente', $data, TRUE);
		//print $message;
		// Respuesta
		$this->out->html_file($message, $this->lang->line('pedidos_cliente_articulo') . " {$id}", 'iconoReportTab');

	}

	function sphinx($query = null)
	{
		$this->load->library('Sphinx');
		$data = $this->sphinx->search($query);
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		$ids = array();
		foreach ($data['matches'] as $id => $v)
		{
			$ids[] = $id;
		}
		if (count($ids) > 0)
		{
			$where = 'nIdLibro IN ( ' . implode(',', $ids) . ')';
			$this->load->model('catalogo/m_articulo');
			$articulos = $this->m_articulo->get(null, null, null, null, $where);
			echo '<pre>';
			print_r($articulos);
			echo '</pre>';
		}
	}

	function timeout()
	{
		ini_set('mssql.timeout', 1);
	}

	function locale()
	{
		echo '<pre>';
		echo setlocale(LC_ALL, array(
				'es_ES.utf8',
				'es_ES.UTF-8',
				'Spanish',
				'es_ES'
		));
		echo "\n";
		$dia = 1276646400;
		echo date(DATE_RFC822, $dia) . "\n";
		echo strftime('%A', $dia) . "\n";
		echo date('N', $dia) . "\n";
		echo strftime('%B', $dia) . "\n";
		echo date('m', $dia) . "\n";
		echo '</pre>';
	}

	function modelcache()
	{
		$this->load->model('generico/m_seccion');
		$data = array();

		$this->m_seccion->clear_cache();
		$this->m_seccion->use_cache(TRUE);
		$this->m_seccion->use_cache(CACHE_MEMORY);
		$this->_cachemodel_search($data, 'MEM', $this->m_seccion);

		$this->m_seccion->clear_cache();
		$this->m_seccion->use_cache(TRUE);
		$this->m_seccion->use_cache(CACHE_FILE);
		$this->_cachemodel_search($data, 'FILE', $this->m_seccion);

		$this->m_seccion->use_cache(FALSE);
		$this->_cachemodel_search($data, 'SIN', $this->m_seccion);

		$this->m_seccion->clear_cache();
		$this->m_seccion->use_cache(TRUE);
		$this->m_seccion->use_cache(CACHE_MEMORY);
		$this->_cachemodel_load($data, 'MEM', $this->m_seccion, 215);

		$this->m_seccion->clear_cache();
		$this->m_seccion->use_cache(TRUE);
		$this->m_seccion->use_cache(CACHE_FILE);
		$this->_cachemodel_load($data, 'FILE', $this->m_seccion, 215);

		$this->m_seccion->use_cache(FALSE);
		$this->_cachemodel_load($data, 'SIN', $this->m_seccion, 215);

		$this->m_seccion->clear_cache();
		$this->m_seccion->use_cache(TRUE);
		$this->m_seccion->use_cache(CACHE_MEMORY);
		$this->_cachemodel_load_update($data, 'MEM', $this->m_seccion, 215, 'cNombre');

		$this->m_seccion->clear_cache();
		$this->m_seccion->use_cache(TRUE);
		$this->m_seccion->use_cache(CACHE_FILE);
		$this->_cachemodel_load_update($data, 'FILE', $this->m_seccion, 215, 'cNombre');

		$this->m_seccion->use_cache(FALSE);
		$this->_cachemodel_load_update($data, 'SIN', $this->m_seccion, 215, 'cNombre');

		#print '<pre>'; print_r($data); print '</pre>';
		print '<table>';
		print '<tr><th>TEST</th><th>CACHE MEM</th><th>CACHE FILE</th><th>SIN CACHE</th></tr>';
		foreach ($data['search'] as $k => $v)
		{
			print "<tr><td>SEARCH {$k}</td><td align='right'>{$v['MEM']}</td><td align='right'>{$v['FILE']}</td><td align='right'>{$v['SIN']}</td></tr>\n";
		}
		foreach ($data['load'] as $k => $v)
		{
			print "<tr><td>LOAD {$k}</td><td align='right'>{$v['MEM']}</td><td align='right'>{$v['FILE']}</td><td align='right'>{$v['SIN']}</td></tr>\n";
		}
		foreach ($data['loadupdate'] as $k => $v)
		{
			print "<tr><td>LOAD/UPDATE {$k}</td><td align='right'>{$v['MEM']}</td><td align='right'>{$v['FILE']}</td><td align='right'>{$v['SIN']}</td></tr>\n";
		}
		print "</table>\n";
	}

	private function _cachemodel_search(&$data, $type, $model)
	{
		$searchs = array(
				1,
				10,
				100,
				1000
		);
		foreach ($searchs as $s)
		{
			$tstart = microtime(true);
			for ($i = 0; $i < $s; $i++)
			{
				$model->search();
			}
			$time = microtime(true) - $tstart;
			$data['search'][$s][$type] = $time;
		}
	}

	private function _cachemodel_load_update(&$data, $type, $model, $id, $field)
	{
		$searchs = array(
				1,
				10,
				100,
				1000
		);
		foreach ($searchs as $s)
		{
			$tstart = microtime(true);
			for ($i = 0; $i < $s; $i++)
			{
				$record = $model->load($id);
				#print '<pre>'; print_r($record); print '</pre>'; die();
				$model->update($id, array($field => $record[$field]));
			}
			$time = microtime(true) - $tstart;
			$data['loadupdate'][$s][$type] = $time;
		}
	}

	private function _cachemodel_load(&$data, $type, $model, $id)
	{
		$searchs = array(
				1,
				10,
				100,
				1000
		);
		foreach ($searchs as $s)
		{
			$tstart = microtime(true);
			for ($i = 0; $i < $s; $i++)
			{
				$record = $model->load($id);
			}
			$time = microtime(true) - $tstart;
			$data['load'][$s][$type] = $time;
		}
	}

	function autores()
	{
		$this->load->model('catalogo/m_autor');
		$nombres[] = "MVRDV (Equip d'arquitectes)";
		$nombres[] = "Julio Verne";
		$nombres[] = "Bárbara PARK";

		print '<pre>';
		$this->m_autor->clear_cache();
		$this->db->trans_begin();
		$nombre = 'Lo que queda del dia';
		$res = $this->m_autor->search($nombre);
		print "\nBUSQUEDA\n---------------------------\n";
		var_dump($res);
		$aut['cNombre'] = ucwords(trim($nombre));
		$id_autor = $this->m_autor->insert($aut);
		print "\nID\n---------------------------\n";
		var_dump($id_autor);
		$res = $this->m_autor->search($nombre, 0, 1);
		print "\nBUSQUEDA\n---------------------------\n";
		var_dump($res);
		$this->db->trans_rollback();
		print '</pre>';

		foreach ($nombres as $nombre)
		{
			print '<pre>';
			print_r($nombre);
			print '</pre>';
			$res = $this->m_autor->search($nombre);
			print '<pre>';
			print_r($res);
			print '</pre>';
		}
		$this->load->model('catalogo/m_articulo');
		$this->m_articulo->clear_cache();
		$isbn = '978-84-315-3876-7';
		$res = $this->m_articulo->search($isbn);
		print '<pre>';
		print_r($res);
		print '</pre>';
	}

	function log()
	{
		$this->load->library('Logger');
		$this->logger->log('Creando Log General');
		$this->logger->log('Creando Log Privado', 'privado');

		$list = ($this->logger->get_list());
		$list2 = ($this->logger->get_list('privado'));
		print '<pre>';
		var_dump($this->logger->get_list());
		var_dump($this->logger->get_list('privado'));
		print $this->logger->output($list[count($list) - 1][1]);
		print $this->logger->output($list2[count($list2) - 1][1], 'privado');
		print '</pre>';
	}

	function date()
	{
		print '<pre>';
		$time = mktime(18, 36, 51, 7, 8, 2010);
		$bp = 1260230400;
		echo $time . "\n";
		echo date('c', $time) . "\n";
		print format_datetime($time) . "\n";

		print $bp . "\n";
		echo date('c', $bp) . "\n";
		print format_datetime($bp) . "\n";

		$diff = $bp - $time;
		echo $diff . "\n";
		echo date("d/m/Y G:i:s", $diff) . "\n";
		
		$fecha = '29/02/2012';
		echo "TEXTO\n";
		var_dump($fecha);
		$fecha = to_date($fecha);
		$fecha2 = strtotime ( '-1 year' , $fecha ) ;
		echo "-1 year\n";
		var_dump($fecha2);
		$fecha2 = format_date($fecha2);
		var_dump($fecha2);
		echo "year before\n";
		$fecha2 = $this->utils->yearbefore($fecha);
		var_dump($fecha2);
		$fecha2 = format_date($fecha2);
		var_dump($fecha2);

		print '</pre>';
	}

	function reports()
	{
		$this->load->library('Reports');
		$this->load->language('report');
		echo '<h1>Mailing</h1>';
		$reports = $this->reports->get_list('mailing.mailing');
		echo '<pre>';
		print_r($reports);
		echo '</pre>';

		echo '<h1>Facturas</h1>';
		$reports = $this->reports->get_list('ventas.factura');
		echo '<pre>';
		print_r($reports);
		echo '</pre>';

		echo '<h1>Boletines</h1>';
		$reports = $this->reports->get_list('mailing.boletin');
		echo '<pre>';
		print_r($reports);
		echo '</pre>';

		/*		$this->load->library('Reports');
		 $l = $this->reports->get('mailing.boletin', 'General');
		 var_dump($l);*/

	}

	function secciones()
	{
		$texto = 'SECCIONES.ITEM1=215
SECCIONES.ITEM2=811
SECCIONES.ITEM3=627
SECCIONES.ITEM4=628
SECCIONES.ITEM5=522
SECCIONES.ITEM6=106
SECCIONES.ITEM7=891
SECCIONES.ITEM8=898
SECCIONES.ITEM9=629
SECCIONES.ITEM10=901
SECCIONES.ITEM11=886
SECCIONES.ITEM12=878
SECCIONES.ITEM13=103
SECCIONES.ITEM14=887
SECCIONES.ITEM15=710
SECCIONES.ITEM16=885
SECCIONES.ITEM17=309
SECCIONES.ITEM18=102
SECCIONES.ITEM19=873
SECCIONES.ITEM20=899
SECCIONES.ITEM21=101
SECCIONES.ITEM22=523
SECCIONES.ITEM23=625
SECCIONES.ITEM24=313
SECCIONES.ITEM25=903
SECCIONES.ITEM26=420
SECCIONES.ITEM27=524
SECCIONES.ITEM28=895
SECCIONES.ITEM29=875
SECCIONES.ITEM30=900
SECCIONES.ITEM31=888
SECCIONES.ITEM32=889
SECCIONES.ITEM33=311
SECCIONES.ITEM34=631
SECCIONES.ITEM35=893
SECCIONES.ITEM36=880
SECCIONES.ITEM37=897
SECCIONES.ITEM38=882
SECCIONES.ITEM39=884
SECCIONES.ITEM40=894
SECCIONES.ITEM41=879
SECCIONES.ITEM42=896
SECCIONES.ITEM43=881
SECCIONES.ITEM44=883
SECCIONES.ITEM45=104
SECCIONES.ITEM46=105
SECCIONES.ITEM47=877
SECCIONES.ITEM48=831
SECCIONES.ITEM49=892
SECCIONES.ITEM50=417
SECCIONES.ITEM51=630
SECCIONES.ITEM52=876
SECCIONES.ITEM53=419
SECCIONES.ITEM54=418
SECCIONES.ITEM55=521
SECCIONES.ITEM56=312
SECCIONES.ITEM57=890
SECCIONES.ITEM58=107
SECCIONES.ITEM59=626
SECCIONES.ITEM60=626

SECCIONESVEDADAS.ITEM24=869
SECCIONESVEDADAS.ITEM25=837
SECCIONESVEDADAS.ITEM26=803
SECCIONESVEDADAS.ITEM1=802
SECCIONESVEDADAS.ITEM2=835
SECCIONESVEDADAS.ITEM3=907
SECCIONESVEDADAS.ITEM4=841
SECCIONESVEDADAS.ITEM5=816
SECCIONESVEDADAS.ITEM6=863
SECCIONESVEDADAS.ITEM7=806
SECCIONESVEDADAS.ITEM8=808
SECCIONESVEDADAS.ITEM9=871
SECCIONESVEDADAS.ITEM10=906
SECCIONESVEDADAS.ITEM11=804
SECCIONESVEDADAS.ITEM12=845
SECCIONESVEDADAS.ITEM13=866
SECCIONESVEDADAS.ITEM14=852
SECCIONESVEDADAS.ITEM15=842
SECCIONESVEDADAS.ITEM16=843
SECCIONESVEDADAS.ITEM17=809
SECCIONESVEDADAS.ITEM18=807
SECCIONESVEDADAS.ITEM19=867
SECCIONESVEDADAS.ITEM20=860
SECCIONESVEDADAS.ITEM21=905
SECCIONESVEDADAS.ITEM22=818
SECCIONESVEDADAS.ITEM23=832
		';
		preg_match_all('/SECCIONES\.ITEM.*\=(.*)\n/', $texto, $res);
		#echo '<pre>';
		echo "DEFECTO<br/>";
		$defecto = array();
		foreach ($res[1] as $s)
		{
			$defecto[$s] = $s;
		}
		echo implode(';', $defecto);
		echo preg_match_all('/SECCIONESVEDADAS\.ITEM.*\=(.*)\n/', $texto, $res);
		$defecto = array();
		foreach ($res[1] as $s)
		{
			$defecto[$s] = $s;
		}
		echo "<br/>VEDADADAS<br/>";
		echo implode(';', $defecto);
		#echo '</pre>';
	}

	function codebar()
	{
		$this->load->library('CodebarLib');
		$type = 129;
		#echo $this->codebar->url('12345678', $type);
		#die();
		$url = $this->codebarlib->out('12345678', $type);
		#echo $url;

		#print '<img src="' . $url . '"/><br/>';
	}

	function super()
	{
		$this->load->model('concursos/m_editorialconcurso');
		$data['nIdEditorial'] = 100000;
		$data['nIdProveedor'] = 100000;
		$data['cEditorial'] = 'TEST';
		echo $this->m_editorialconcurso->insert($data);
	}

	function ivas()
	{
		$iva = 4;
		echo '<table border="1">';
		echo '<tr><th>#</th><th>PVP</th><th>Base</th><th>IVA</th><th>Base+IVA</th></tr>';
		$count = 0;
		$total = 30000;
		for ($i = 0; $i < $total; $i++)
		{
			$pvp = $i / 100.0;
			$importes = format_calculate_importes(array(
					'fPrecio' => format_quitar_iva($pvp, $iva),
					'nCantidad' => 1,
					'fRecargo' => 0,
					'fIVA' => $iva,
					'fDescuento' => 0
			));
			$s = ($importes['fIVAImporte'] + $importes['fBase']);
			if ((string)($s * 100) != (string)($pvp * 100))
			{
				$count++;
				echo "<tr><td>{$count}</td><td>{$pvp}</td><td>{$importes['fBase']}</td><td>{$importes['fIVAImporte']}</td><td>{$s}</td></tr>\n";
			}
		}
		echo '</table>';
		echo format_percent(($count / $total) * 100);
	}

	function getemail($id = null)
	{
		if (!isset($id))
			$id = 4795;
		$this->load->model('clientes/m_cliente');
		echo '<pre>';
		$email = $this->m_cliente->get_email($id);
		echo "GENERAL\n";
		var_dump($email);
		$email = $this->m_cliente->get_email($id, 1);
		echo "GENERAL FORZADO\n";
		var_dump($email);
		$email = $this->m_cliente->get_email($id, 9);
		echo "SUSCRIPCIONES\n";
		var_dump($email);
		$email = $this->m_cliente->get_email($id, 7);
		echo "OTRO\n";
		var_dump($email);
		echo '</pre>';
	}

	function request()
	{
		echo 'RECIBIDO';
		exit();
	}

	function servers()
	{
		echo '<pre>';
		echo site_url('catalogo/articulo/get/10') . "\n";
		echo site_url('catalogo/articulo/get/11') . "\n";
		echo site_url('catalogo/articulo/get/12') . "\n";
		echo site_url('catalogo/articulo/get/13') . "\n";
		echo site_url('catalogo/articulo/get/14') . "\n";
		echo site_url('sys/app/js_menu');
		echo '</pre>';
	}

	function session()
	{
		$name = 'TestCookie3';
		echo '<pre>';
		$data = $this->session->userdata($name);
		if (!isset($data) || ($data == ''))
		{
			$data = time();
			echo "Save SESSION:\n" . $data;
			$this->session->set_userdata($name, $data);
		}
		else
		{
			echo "Load SESSION = {$data}";
		}
		echo '</pre>';
	}

	function tareas()
	{
		$this->load->model('sys/m_tarea');
		print '<pre>';
		print_r($this->m_tarea->get_first());
		print '</pre>';
		return;
		$this->cron->debug = FALSE;
		$this->cron->run();
	}

	function convert($file)
	{
		$file = DIR_TEMP_PATH . $file;
		putenv("XFC_DIR=/opt/xfc_perso_java/bin");
		$this->load->library('Convert');
		echo '<pre>';
		echo "CONVERT\n";
		echo $this->convert->docx($file, FALSE);
		echo "\n";
		echo $this->convert->odt($file, FALSE);
		echo "\n";
		echo $this->convert->rtf($file, FALSE);
		echo "\n";
		echo '</pre>';

		$exec = "html-convert --format odt-xfc --output {$file}.odt {$file}";
		$r = system($exec, $result);
		echo '<pre>system ' . $exec . '</pre>';
		echo '<pre>' . var_dump($result) . '</pre>';
		echo '<pre>' . var_dump($r) . '</pre>';
		die();
		$r = exec($exec, $result);
		echo '<pre>exec ' . $exec . '</pre>';
		echo '<pre>' . var_dump($result) . '</pre>';
		echo '<pre>' . var_dump($r) . '</pre>';

		$r = passthru($exec, $result);
		echo '<pre>passthru ' . $exec . '</pre>';
		echo '<pre>' . var_dump($result) . '</pre>';
		echo '<pre>' . var_dump($r) . '</pre>';

		die();
	}

	function crear()
	{
		$isbn = '9788499170800 ';
		$this->load->library('ISBNEAN');
		$this->load->model('catalogo/m_editorial');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('catalogo/m_autor');
		echo '<pre>';
		//Editor y proveedor
		$isbn = $this->isbnean->to_isbn($isbn);
		$parts = $this->isbnean->isbnparts($isbn);
		print_r($parts);
		if (isset($parts['publisher_id']))
		{
			$editorial = $this->m_editorial->search($parts['publisher_id'], 0, 1);
		}
		print_r($editorial);
		print '<pre>';
	}

	function dialog()
	{
		$this->out->dialog(TRUE, 'Bienvenidos a <b>casa</b>. Son las ' . format_datetime(time()));
	}

	function cron()
	{
		$this->load->library('cron');
		$this->cron->debug = 2;
		$bisiesto = checkdate(02,29,date('Y'));
		echo '<pre>';
		echo 'Bisiesto ' . ($bisiesto?'SI':'NO') . "\n";		
		echo "Primera llamada...\n";
		$process = $this->cron->get_info();
		var_dump($process);
		print $this->cron->getTextDebug();
		foreach($process as $job)
		{
			$time = time();
			$si = ($job["lastScheduled"] < time())?'SI':'NO';
			echo "{$job[7]} - {$job[8]} - {$job["lastActual"]} -{$job["lastScheduled"]} - {$time} - {$si}\n";
		}
		/*echo "Segunda llamada...\n";
		$process = $this->cron->get_to_run();
		var_dump($process);
		print $this->cron->getTextDebug();*/
		echo '</pre>';
	}

	function fix()
	{
		$this->load->helper('asset');
		$this->load->helper('extjs');
		//$js[] = array('ux/miframe-min.js');
		//$js[] = array('tiny_mce/lang/es.js');
		//$js[] = array('tiny_mce/tiny_mce.js');
		//$js[] = array('Ext.ux.TinyMCE.js');
		//$datos['js_include'] = $js;
		$datos['title'] = $this->lang->line('Nada');
		$datos['form'] = $this->load->view('test/void.js', '', true);
		$this->_show_form(null, 'test/void.js', 'main/main_app.js');
	}

	function test_email2()
	{
		$this->load->plugin('swift');
		$config['Host'] = 'localhost';
		$config['SMTPAuth'] = FALSE;
		$config['From'] = $this->config->item('bp.mailing.from');
		$config['FromName'] = $this->config->item('bp.mailing.fromname');
		$mail = new Mailer($config);

		set_time_limit(0);

		$list[] = 'alex@alibri.es';
		$list[] = 'alopez@alibri.cat';
		// Envio
		$mail->prepare('Test', '<strong>Prueba de mensaje</strong>');
		$res = $mail->send($list);
		echo '<pre>';
		var_dump($res);
		echo '</pre>';
	}

	/**
	 * Muestra la ventana de login
	 *
	 */
	function errorjs()
	{
		$this->session->keep_flashdata('uri');
		$url = site_url($this->session->flashdata('uri'));

		// Formulario
		$this->load->helper('asset');
		$this->load->helper('extjs');
		$datos['title'] = $this->config->item('bp.application.name');
		// . ' - ' .$this->lang->line('Login');

		$datos['url'] = $url;
		$this->load->view('test/errorjs', $datos);
	}

	function errordb($id)
	{
		echo '<pre>';
		$this->load->database('default');
		$this->db->trans_begin();
		$this->db->where("nIdLinea={$id}");
		$res = $this->db->delete('Doc_LineasPedidoProveedor');
		var_dump($res);
		echo mssql_get_last_message() . "\n";
		echo $this->db->_error_message() . "\n";
		$this->db->trans_rollback();

		$res = $this->db->query("SELECT * FROM Doc_LineasPedidoProveedor WHERE nIdLinea={$id}");
		var_dump($res);
		echo mssql_get_last_message() . "\n";
		echo $this->db->_error_message() . "\n";
	}

	function sinli()
	{
		$url = 'http://app.alibri.es/system/cache/sinli/0006014280.TXT';
		$data = file_get_contents($url);
		/*var_dump($data);
		echo $data; echo '<br/>';
		echo mb_detect_encoding($data);
		$data2 = imap_binary($data);
		var_dump($data2);		
		$data2 = utf8_decode($data);	
		var_dump($data2);		
		echo $data2;
		$data2 = base64_decode($data2);
		var_dump($data2);
		$data2 = (string) $data;		
		var_dump($data2);
		$data2 = utf8_encode($data);		
		var_dump($data2);		
		echo $data; echo '<br/>';
		#$data2 = utf8_decode($data);	
		$data2 = mb_convert_encoding($data, 'UTF-8', 'UTF-16');
		var_dump($data2);
		echo $data2;		
		die();*/
		#$this->load->model('compras/m_pedidoproveedor');
		$this->load->library('SinliLib');

		$res = $this->sinlilib->check();
		echo '<pre>';
		echo count($res) . "\n";
		print_r($res);
		echo '</pre>';
		exit;
		
		$url  = 'http://app.alibri.es/system/cache/sinli/0006014280.TXT';
		$url = 'http://app.alibri.es/system/cache/sinli/0010338284.txt';
		#$data = file_get_contents($url);
		#$data = utf8_decode($data);
		#echo ($data);
		
		$data = array(
			'filename' => $url,
			'date' => time(),
			'subject' => 'test'
		);
		$res = $this->sinlilib->process($data);
		var_dump($res);
		die();
		$data = file_get_contents($url);
		echo utf8_decode($data);
		die();
		$pedido = $this->m_pedidoproveedor->load(159902, 'lineas');
		$res = $this->sinlilib->send('PEDIDO', $pedido, 'LIB00078', 'alopez@alibri.cat', 'ALEX');
		var_dump($res); die();
		$res = $this->sinlilib->crear_identificacion('ENVIO', 'LIB00078', 'sinli@pro.es', 'DESTINO', '03', 1212);
		#$res = $this->sinlilib->check(4000);
		/*$data = array(
			'filename' => DIR_SINLI_PATH . 'ALB1036109.snl',
			'date' => time(),
			'subject' => 'test abono'
		);
		$res = $this->sinlilib->process($data);
		var_dump($res);*/
		/*echo '<pre>';
		echo count($res) . "\n";
		print_r($res);
		echo '</pre>';*/
	}

	function todate()
	{
		$var = 1314878233;
		$var2 = "01/09/2011 13:57:13";
		$var3 = "01/09/2011";
		$var4 = "2011-09-01 13:57:13";
		$var5 = "2011-09-01 13:57:13";
		echo '<pre>';
		echo "$var\n";
		echo format_datetime($var) . "\n";
		echo to_date($var2) . "\n";
		echo to_date($var3) . "\n";
		echo to_date($var5) . "\n";
		echo to_date($var3) . "\n";
		$where = '="' . $var3 . '"';
		$this->load->helper('parsersearch');
		var_dump(boolean_sql_where($where, 'dAct', 'date'));
	}

	function mailing()
	{
		$this->load->model('mailing/m_mailing');
		$res = $this->m_mailing->get();
		foreach ($res as $item)
		{

			$data = $this->m_mailing->load($item['id']);
			$body = str_replace('http://www.alibri.es/components/com_commerce/catalog/includes/languages/spanish/images/buttons/', 'http://www.alibri.es/templates/alibri/images/', $data['cBody']);
			$body = str_replace('http://www.alibri.es/components/com_commerce/catalog/images/books/portada.php?id=', 'http://www.alibri.es/index.php?route=product/product/cover&id=', $body);
			$body = str_replace('&amp;op=l', '', $body);

			$this->m_mailing->update($item['id'], array('cBody' => $body));
			echo "{$data['id']}<br />";
		}
	}

	function searchimages()
	{
		/*$data =
		 * parse_url('http://www.rerumnatura.es/catalogo/info_producto.asp?ID=81130&am');
		 var_dump($data);
		 var_dump(pathinfo($data['path']));
		 $data = parse_url('http://www.rerumnatura.es');
		 var_dump($data);
		 var_dump(pathinfo($data['path']));
		 $data = parse_url('http://www.rerumnatura.es/');
		 var_dump($data);
		 var_dump(pathinfo($data['path']));
		 $data = parse_url('http://www.rerumnatura.es/catalogo');
		 var_dump($data);
		 var_dump(pathinfo($data['path']));
		 $data = parse_url('http://www.rerumnatura.es/catalogo/');
		 var_dump($data);
		 var_dump(pathinfo($data['path']));
		 die();*/
		$text = '9788493943301';
		$text = '9788466622998';
		$this->load->library('SearchImages');
		$data = $this->searchimages->search($text, 'google2');
		var_dump($data);
	}

	function totales($id = null)
	{
		set_time_limit(0);
		$this->load->model('ventas/m_albaransalida');
		if (isset($id))
		{
			echo $id . "\n";
			$this->m_albaransalida->set_total($id);
		}
		else
		{
			$this->db->flush_cache();
			$this->db->select('nIdAlbaran')
			->from('Doc_AlbaranesSalida')
			->where('nIdBiblioteca IS NOT NULL');
			#->where("Doc_AlbaranesSalida.bNoFacturable=1 AND Doc_AlbaranesSalida.dCreacion >= {d '2012-01-01'}");
			$query = $this->db->get();
			$data = $query->result_array();
			#$data = $this->m_albaransalida->get(null, null, null, null, 'fTotal IS NULL');
			echo '<pre>';
			foreach ($data as $d)
			{
				echo $d['nIdAlbaran'] . "\n";
				$this->m_albaransalida->set_total($d['nIdAlbaran']);
			}
			echo '</pre>';
		}
	}
	
	function screenshot()
	{
		$url = 'http://www.google.es';
		$this->load->library('Screenshot');
		$img = $this->screenshot->url($url);
		#python /var/www/app/system/bin/unix/webkit2png.py --xvfb 500 500 "http://www.siruela.com/novedades.php?&id_libro=1747" -o e747b973db495c6466c9df9397049a3f.png
		$this->load->library('SearchImages');
		$mime = $this->searchimages->get_mime('png');
		Header("Content-type: {$mime}\n");
		Header("Content-Transfer-Encoding: binary\n");
		readfile($img);
	}

	function scribd()
	{
		$url = 'http://es.scribd.com/doc/81584230?access_key=key-2kcqnxm1p7hds2nfqe8l';		
		$images = $this->utils->get_url($url);
		var_dump($images);
	}
	
	function dilve($isbn = null)
	{
		if (empty($isbn))
		{
			$message = 'NO ISBN';
		}
		else
		{
			$this->load->library('ISBNEAN');
			$this->load->library('Importador');
			$this->load->library('Dilve');
		
			$isbn = $this->isbnean->to_isbn($isbn);
			$data = $this->dilve->get($isbn);
			if ($data)
			{
				if (isset($data['OtherText']))
				{
					foreach ($data['OtherText'] as $t)
					{
						#echo $t['Text'] . '<br/><br/>';
					}
				}
				$message = '<h1>' . $isbn .'</h1><pre>';
				$message .= print_r($data, TRUE);
				$res = $this->importador->onix(array_pop($data));
				$message .=print_r($res, TRUE);
				$message .= '</pre>';
			}
			else
			{
				$message = $this->dilve->get_error();		
			}
		}
		$this->out->html($message, $isbn);
	}
	
	function screenshot_pdf()
	{
		$url = 'http://www.diannammarques.com/coma/img/3capisCOMA.pdf';
		$this->load->library('Screenshot');
		echo '<pre>';
		$img = $this->screenshot->pdf($url);
		var_dump($img);
		echo '</pre>';
	}

	function internet($isbn = null)
	{
		if (empty($isbn))
		{
			$message = 'NO ISBN';
		}
		else
		{
			$this->load->library('SearchInternet');
			$data = $this->searchinternet->amazon($isbn);
			if ($data)
			{
				$message = '<h1>' . $isbn .'</h1><pre>';
				$message .= print_r($data, TRUE);
				$message .= '</pre>';
			}
		}
		echo $message; die();
		$this->out->html($message, $isbn);
	} 
	
	function fusion($count = 20)
	{
		set_time_limit(0);
		$this->load->model('catalogo/m_articulo');
		$this->db->flush_cache();
		$this->db->select('nIdLibro, nIdLibroEdicionAnterior')
		->from('Cat_Fondo')
		->where('nIdLibroEdicionAnterior IS NOT NULL')
		->limit($count);
		$query = $this->db->get();
		$data = $query->result_array();
		$count = 0;
		$message  ='<pre>';
		foreach ($data as $l)
		{
			$message .= "{$l['nIdLibroEdicionAnterior']} -> {$l['nIdLibro']}\n";
			if ($this->m_articulo->update($l['nIdLibro'], array('nIdLibroEdicionAnterior' => null)))
			{
				if (!$this->m_articulo->unificar($l['nIdLibro'], array($l['nIdLibroEdicionAnterior'])))
				{
					$message .= '   ERROR: ' . $this->m_articulo->error_message();
				}
				else
				{
					++$count;
				}				
			}
			else 
			{
				$message .= '   ERROR: ' . $this->m_articulo->error_message();				
			}
		}
		$message  .= "{$count} unificados</pre>";
		#echo $message; die();
		$this->out->html($message, $fusion);		
	}
	
	function onix_file()
	{
		$file = '/home/alibri/Descargas/libreria-alibri_20111014050005_parcial/catalogo.xml';
		$src = file_get_contents($file);
		echo '<pre>'; echo htmlentities($src); echo '</pre>';
		$this->load->library('Importador');
		$data = $this->importador->onix_file($file);
		foreach ($data as $reg)
		{
			$libro = $this->importador->onix($reg);
			var_dump($reg);
			var_dump($libro);
		}
	}

	function fax()
	{

		/**************** Settings begin **************/
		 
		$username          = 'alibri';  // Insert your InterFAX username here
		$password          = 'Spain1290';  // Insert your InterFAX password here
		$faxnumber         = '0034934122702';  // Enter the destination fax number here, e.g. +497116589658
		$filename          = '1336723429.pdf'; // A file in your filesystem
		$filetype          = 'PDF'; // File format; supported types are listed at 
		                   // http://www.interfax.net/en/help/supported_file_types 
		$this->obj->load->library('HtmlFile');
		//die();
				
		/**************** Settings end ****************/
		#echo $this->htmlfile->pathfile($filename); die();
		// Open File
		if( !($fp = fopen($this->htmlfile->pathfile($filename), "r")))
		{
		    // Error opening file
		    echo "Error opening file";
		    exit;
		}
		echo $filename;

		// Read data from the file into $data
		$data = "";
		while (!feof($fp)) $data .= fread($fp,1024);
		fclose($fp);
		 		 
		$client = new SoapClient("http://ws.interfax.net/dfs.asmx?WSDL");
		 
		$params = array (
			'Username'  => $username,
			'Password'  => $password,
			'FaxNumber' => $faxnumber,
			'FileData'  => $data,
			'FileType'  => $filetype
			);

		die();
		$result = $client->Sendfax($params);
		echo $result->SendfaxResult; // returns the transactionID if successful
		                             // or a negative number if otherwise
	}

	function asm($id = null)
	{
		if (!isset($id)) $id = 143404;

		$this->load->library('ASM');
		/*$id = '61771000645500';
		$res = $this->asm->etiqueta($id);
		$this->load->library('HtmlFile');
		echo  $this->htmlfile->url($res);
		die();*/

		$this->load->model('ventas/m_pedidocliente');
		$this->load->model('clientes/m_direccioncliente');
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_telefono');
		$pd = $this->m_pedidocliente->load($id, 'cliente');
		$dir = $this->m_direccioncliente->load($pd['nIdDirEnv']);
		$emails = $this->m_email->get_list($pd['nIdCliente']);

		$em = $this->utils->get_profile($emails, PERFIL_ENVIO);
		$tels = $this->m_telefono->get_list($pd['nIdCliente']);
		$tf = $this->utils->get_profile($tels, PERFIL_ENVIO);
		#var_dump($dir, $pd['cliente'], $em['text'], $tf['text']); die();
		$ref = $id . substr(time(), 7);

		if (!($id = $this->asm->enviar($ref, $dir, $pd['cliente'], $em['text'], $tf['text'])))
		{
			echo '<pre>' . $this->asm->get_error() . '</pre>';
			die();
		}
		echo 'ID: ' . $id . "\n";
		
		$res = $this->asm->etiqueta($id);
		$this->load->library('HtmlFile');
		echo  $this->htmlfile->url($res);
		die();
	}

	function etiqueta()
	{
		$id = 82631;
		$this->load->model('clientes/m_direccioncliente');
		$dir = $this->m_direccioncliente->load($id);
		$tipo = 5;
		$this->load->library('Etiquetas');
		$etq = $this->etiquetas->etiqueta($dir, $tipo);

		$url = $this->etiquetas->paper(array($etq,$etq,$etq,$etq), 3, 1, 1);
		$this->out->url($url, 'Etiquetas', 'iconoReportTab');
	}

	function lastid()
	{
		$this->load->model('clientes/m_cliente');
		echo $this->m_cliente->get_last();
	}

	function sqlupdate()
	{
		$this->load->model('clientes/m_cliente');
		$updfile = __DIR__ . "/export.catalogo.update.sql";
		$sql = file_get_contents($updfile);
        if (!$this->db->query($sql))
        {
			echo  $this->obj->db->_error_message();
        }
	}

	function gene()
	{
		set_time_limit(0);
		echo '<p style="font: Courier New;">';
		$file = __DIR__ . '/../../../../tools/lib/data/BIBLARIA.xls';
		echo '<pre>';
		echo "Running...<strong>{$file}</strong>\n";
		require_once DIR_CONTRIB_PATH . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objReader = PHPExcel_IOFactory::createReaderForFile($file);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($file);
		$this->load->library('ISBNEAN');
		$this->load->model('concursos/m_pedidoconcursolinea');
		$this->load->model('concursos/m_estadolineaconcurso');
		$res = array();
		foreach ($objPHPExcel->getAllSheets() as $sheet) 
		{
			$ok_isbn = 0;
			$nook_isbn = 0;
			$found = 0;
			$name = $sheet->getTitle();
			echo "Hoja...<strong>{$name}</strong><br/>";
			$sheetData = $sheet->toArray(null, FALSE, TRUE, TRUE);
			$count = 1;
			foreach ($sheetData as $value) 
			{
				$ean = $this->isbnean->to_ean($value['E']);
				if ($ean)
				{
					++$ok_isbn;					
					$arts = $this->m_pedidoconcursolinea->get(null, null, null, null, "nEAN={$ean} AND nIdBiblioteca={$value['A']} AND nIdEstado=".CONCURSOS_ESTADO_LINEA_EN_PROCESO);
					if (count($arts) > 0) 
					{
						$arts = $this->m_pedidoconcursolinea->update($arts[0]['nIdLineaPedidoConcurso'], array('nIdEstado' => CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR));
						++$found;
					}
					else
					{
						echo "NO FOUND {$count} [{$value['A']}]- {$value['E']} - {$value['F']}\n";
					}
				}
				else					
				{
					echo "NOOK {$value['D']}\n";
					++$nook_isbn;
				}
				++$count;
			}

			$res[$name] = array(
				'OK' 	=> $ok_isbn,
				'NOOK' 	=> $nook_isbn,
				'FOUND'	=> $found
				);
		}
		var_dump($res);
		echo '</pre>';
		echo '</p>';
		return TRUE;
	}

	function excel()
	{
		$file = '1352379133.html';
		$this->load->library('HtmlFile');
		$this->load->library('ExcelData');
		$fout = $this->htmlfile->pathfile($file);
		$html = file_get_contents($fout);
		$tablevar = 'Hoja';
		$limit = 12;                            // maximum number of Excel tabs to create, optional
		$debug = false;
		$debug = true;
		$user = 'alexl';
		$file = $fout . '.xlsx';
		$company = 'ALIBRI';
		$title = 'Lo que sea';

		$res = $this->exceldata->table2excel($html, $file, $title, $user, $company, $tablevar, 'Excel2007', $limit, $debug);
		var_dump($file, $res);
	}

	function limpiar()
	{
		set_time_limit(0);
		echo '<pre>';
		$count = 0;
		$this->load->model('catalogo/m_articulo');
		/*$data = $this->m_articulo->get(null, null, null, null, "nIdLibro >=554548 And nIdLibro <=558128 and cCUser = 'alexl'");
		if (count($data) > 0)
		{
			foreach ($data as $value) 
			{
				echo "{$value['nIdLibro']}\n";
				$this->m_articulo->delete($value['nIdLibro']);
				++$count;
			}
		}*/
		$data = $this->m_articulo->get(null, null, null, null, "nIdLibro NOT IN (SELECT Ext_LineasPedidoConcurso.nIdLibro FROM Ext_LineasPedidoConcurso)
			AND dCreacion >= {d '2012-10-01'}
				AND nIdLibro NOT IN (SELECT Cat_Secciones_Libros.nIdLibro FROM Cat_Secciones_Libros)	
				AND (cCUser = 'alexl' OR cCUser='lvidal')");
		echo '<pre>'; print_r($this->db->queries); echo '</pre>';
		if (count($data) > 0)
		{
			foreach ($data as $value) 
			{
				echo "{$value['nIdLibro']}\n";
				$this->m_articulo->delete($value['nIdLibro']);
				++$count;
			}
		}
		$this->load->model('catalogo/m_aunificar');
		$data = $this->m_aunificar->get();
		foreach ($data as $reg)
		{
			echo "{$reg['nIdBueno']} -> {$reg['nIdMalo']}\n";
			if ($this->m_articulo->unificar($reg['nIdBueno'], array($reg['nIdMalo'])))
			{
				$this->m_aunificar->delete($reg['nIdUnificar']);
				++$count;
			}
		}
		echo "$count registros\n";
		echo '</pre>';
	}

	function sql()
	{
		$this->load->model('ventas/m_factura');
		$sql = "SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";
CREATE TABLE Ext_Fax_Tipos_new LIKE Ext_Fax_Tipos;
RENAME TABLE Ext_Fax_Tipos TO Ext_Fax_Tipos_old, Ext_Fax_Tipos_new TO Ext_Fax_Tipos;
DROP TABLE Ext_Fax_Tipos_old;
INSERT INTO Ext_Fax_Tipos (nIdTipoFax,cDescripcion,cCUser,dCreacion,cAUser,dAct) VALUES (1,'Pedido Proveedor','alibrilibreria\\alexl','2005-05-26 14:48:08','alibrilibreria\\alexl','2005-05-26 14:48:08');
SET FOREIGN_KEY_CHECKS=1;
";
		echo '<pre>';
		var_dump($sql);
		$res =  @mysqli_multi_query($this->db->conn_id, $sql);
		do 
		{
	        $result = mysqli_store_result($this->db->conn_id);
	        {
	        	var_dump($result);
	            mysqli_free_result($result);
            }
        }
        while (mysqli_next_result($this->db->conn_id));
        echo $this->db->_error_message() . "\n";
        /*if (!$this->db->multi_query($sql))
        {
			echo $this->db->_error_message();
			return;
        }*/
        echo "OK\n";
        echo  '</pre>';
        return;

	}

	function lleida()
	{
		set_time_limit(0);
		$fechainventario = '11/01/2013';
		$fecharetroceso = '31/12/2012';

		$this->load->model('stocks/m_antiguedadstock');
		$data = $this->m_antiguedadstock->documentos_seccion(912, $fecharetroceso, $fechainventario);
		var_dump($data);

	}

	function cell($pdf, $row, $column, $w, $h, $text)
	{
		$pdf->SetY(8 + ($row-1) * $h);
		$pdf->SetX(8 + ($column-1) * $w);
		$pdf->Cell($w, $h, utf8_decode($text), 'ltrb', 0, 'C');
	}

	function pdf()
	{

		require(DIR_CONTRIB_PATH . 'fpdf17/cellpdf.php');

		/*$pdf=new CellPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',12);

		$pdf->VCell(15,50,"Text at\nbottom",1,0,'D');
		$pdf->VCell(10,50,'Centered text',2,0,'C');
		$pdf->VCell(15,50,"Text\non top",1,0,'U');

		$pdf->Cell(50,50,"Text on\nthe left",'lbtR',0,'L');
		$pdf->Cell(50,50,'This line is very long and gets compressed','LtRb',0,'C');
		$pdf->Cell(50,50,"Text on\nthe right",'Ltrb',0,'R');

		$pdf->Output();
		return;
		require(DIR_CONTRIB_PATH . 'fpdf17/fpdfex.php');*/
		#pdf=new CellPDF();
		$pdf = new CellPDF('L','mm','A4');
		$w = 25.4;
		$h = 48.5;
		$pdf->AddPage();
		$pdf->SetMargins(0, 0);
		$pdf->SetAutoPageBreak(FALSE);
		$pdf->SetFont('Arial', 'B', 12);
		error_reporting(E_ERROR);
		for ($i=1; $i <= 4; $i++)
		{
			for ($j=1; $j <= 11; $j++)	
			{
				$this->cell($pdf, $i, $j, $w, $h, "Fila {$i}\nColumna {$j}");		
			}
		}
		#$pdf->AddPage('P');
		$pdf->AddPage();
		$pdf->SetMargins(0, 0);		
		$pdf->SetAutoPageBreak(FALSE);
		/*$pdf->SetFont('Arial', 'B', 16);
		$h = 25.4;
		$w = 48.5;
		for ($i=1; $i <= 11; $i++)
		{
			for ($j=1; $j <= 4; $j++)	
			{
				$this->cell($pdf, $i, $j, $w, $h, "Fila {$i}\nColumna {$j}");		
			}
		}*/
		$pdf->SetFont('Arial', '', 20);
		$pdf->RotatedText(100, 60, 'Hello', 45);

		$pdf->Output();
		//echo 'OK';
	}

	function numeros()
	{
		var_dump(is_integer("129.12"));
		var_dump(is_float("129.12"));
	}

	function isbns()
	{
		$isbns = array(
			array('9783468495076', '9783126064842'),
			array('9783468496851', '9783126064491'),
			array('9783468477171', '9783126064279'),
			array('9783468477232', '9783126064323'),
			array('9783468474712', '9783126060011'),
			array('9783468474743', '9783126060042'),
			array('9783468474767', '9783126060066'),
			array('9783468474750', '9783126060059'),
			array('9783468474729', '9783126060028'),
			array('9783468474736', '9783126060035'),
			array('9783468474781', '9783126060073'),
			array('9783468474798', '9783126060080'),
			array('9783468474811', '9783126060097'),
			array('9783468474842', '9783126060127'),
			array('9783468474866', '9783126060141'),
			array('9783468474859', '9783126060134'),
			array('9783468474828', '9783126060103'),
			array('9783468474897', '9783126069854'),
			array('9783468474835', '9783126060110'),
			array('9783468474873', '9783126060158'),
			array('9783468474880', '9783126060165'),
			array('9783468474910', '9783126060172'),
			array('9783468474941', '9783126060202'),
			array('9783468474965', '9783126060226'),
			array('9783468474958', '9783126060219'),
			array('9783468474927', '9783126060189'),
			array('9783468474934', '9783126060196'),
			array('9783468474972', '9783126060233'),
			array('9783468474989', '9783126060240'),
			array('9783468498107', '9783126064859'),
			array('9783468477256', '9783126064347'),
			array('9783468477300', '9783126064392'),
			array('9783468496943', '9783126064583'),
			array('9783468497056', '9783126064637'),
			array('9783468497155', '9783126064712'),
			array('9783468472015', '9783126060257'),
			array('9783468472046', '9783126060288'),
			array('9783468472039', '9783126060271'),
			array('9783468473340', '9783126060837'),
			array('9783468472169', '9783126060370'),
			array('9783468472060', '9783126060301'),
			array('9783468472176', '9783126060387'),
			array('9783468472152', '9783126060363'),
			array('9783468472091', '9783126060325'),
			array('9783468472084', '9783126060318'),
			array('9783468472053', '9783126060295'),
			array('9783468472619', '9783126060653'),
			array('9783468472657', '9783126060677'),
			array('9783468472626', '9783126060660'),
			array('9783468472664', '9783126060684'),
			array('9783468472244', '9783126060424'),
			array('9783468472213', '9783126060394'),
			array('9783468472237', '9783126060417'),
			array('9783468472367', '9783126060516'),
			array('9783468472381', '9783126060530'),
			array('9783468472268', '9783126060448'),
			array('9783468472398', '9783126060554'),
			array('9783468472350', '9783126060509'),
			array('9783468472299', '9783126060462'),
			array('9783468472282', '9783126060455'),
			array('9783468472251', '9783126060431'),
			array('9783468472718', '9783126060691'),
			array('9783468472756', '9783126060714'),
			array('9783468472725', '9783126060707'),
			array('9783468472763', '9783126060721'),
			array('9783468472435', '9783126060585'),
			array('9783468472442', '9783126060592'),
			array('9783468472411', '9783126060561'),
			array('9783468472565', '9783126060639'),
			array('9783468472589', '9783126060646'),
			array('9783468472497', '9783126060622'),
			array('9783468472480', '9783126060615'),
			array('9783468472459', '9783126060608'),
			array('9783468472817', '9783126060738'),
			array('9783468472855', '9783126060752'),
			array('9783468472824', '9783126060745'),
			array('9783468472862', '9783126060769'),
			array('9783468473029', '9783126060790'),
			array('9783468473005', '9783126060776'),
			array('9783468479014', '9783126061124'),
			array('9783468479038', '9783126061148'),
			array('9783468479045', '9783126061155'),
			array('9783468479113', '9783126061186'),
			array('9783468479137', '9783126061209'),
			array('9783468479120', '9783126061193'),
			array('9783468478307', '9783126060851'),
			array('9783468478345', '9783126060882'),
			array('9783468478369', '9783126060899'),
			array('9783468478475', '9783126060936'),
			array('9783468473418', '9783126060844'),
			array('9783468478376', '9783126060905'),
			array('9783468478321', '9783126060875'),
			array('9783468478635', '9783126061018'),
			array('9783468478505', '9783126060943'),
			array('9783468478512', '9783126060950'),
			array('9783468478543', '9783126060974'),
			array('9783468478567', '9783126060981'),
			array('9783468478659', '9783126061025'),
			array('9783468478574', '9783126060998'),
			array('9783468478529', '9783126060967'),
			array('9783468478703', '9783126061032'),
			array('9783468478710', '9783126061049'),
			array('9783468478741', '9783126061063'),
			array('9783468478765', '9783126061070'),
			array('9783468478796', '9783126061094'),
			array('9783468472596', '9783126061988'),
			array('9783468478789', '9783126061087'),
			array('9783468478727', '9783126061056'),
			array('9783468473043', '9783126060813'),
			array('9783468473043', '9783126060813'),
			array('9783468473012', '9783126060783'),
			array('9783468479021', '9783126061131'),
			array('9783468478994', '9783126061100'),
			array('9783468479007', '9783126061117'),
			array('9783468479144', '9783126061216'),
			array('9783468479106', '9783126061179'),
			array('9783468479090', '9783126061162'),
			array('9783468478451', '9783126060929'),
			array('9783468471223', '9783126064156'),
			array('9783468496882', '9783126064521'),
			array('9783468495083', '9783126063784'),
			array('9783468498206', '9783126063906'),
			array('9783468498183', '9783126063890'),
			array('9783468967023', '9783126064828'),
			array('9783468471513', '9783126064194'),
			array('9783468497483', '9783126064064'),
			array('9783468472916', '9783126064224'),
			array('9783468988356', '9783126062084'),
			array('9783468988264', '9783126061933'),
			array('9783468988219', '9783126061995'),
			array('9783468988318', '9783126062060'),
			array('9783468988202', '9783126061971'),
			array('9783468988301', '9783126062046'),
			array('9783468477263', '9783126064354'),
			array('9783468477317', '9783126064408'),
			array('9783468495274', '9783126061797'),
			array('9783468495298', '9783126061810'),
			array('9783468495281', '9783126061803'),
			array('9783468495533', '9783126065252'),
			array('9783468495571', '9783126065269'),
			array('9783468471216', '9783126064132'),
			array('9783468495809', '9783126065221'),
			array('9783468495854', '9783126065238'),
			array('9783468497001', '9783126064606'),
			array('9783468477287', '9783126064378'),
			array('9783468477324', '9783126064415'),
			array('9783468497025', '9783126064613'),
			array('9783468497193', '9783126064750'),
			array('9783468465048', '9783126063968'),
			array('9783468496875', '9783126064514'),
			array('9783468494963', '9783126063685'),
			array('9783468961953', '9783126063708'),
			array('9783468477201', '9783126064293'),
			array('9783468477140', '9783126064255'),
			array('9783468496998', '9783126064590'),
			array('9783468497179', '9783126064736'),
			array('9783468498893', '9783126063807'),
			array('9783468494697', '9783126065207'),
			array('9783468496844', '9783126064484'),
			array('9783468494888', '9783126064835'),
			array('9783468496868', '9783126064507'),
			array('9783468496790', '9783126065153'),
			array('9783468496783', '9783126065146'),
			array('9783468496769', '9783126065139'),
			array('9783468496233', '9783126064927'),
			array('9783468496752', '9783126065122'),
			array('9783468496745', '9783126065115'),
			array('9783468496721', '9783126065108'),
			array('9783468496226', '9783126064910'),
			array('9783468496608', '9783126065061'),
			array('9783468496660', '9783126065092'),
			array('9783468496202', '9783126064897'),
			array('9783468496646', '9783126065085'),
			array('9783468496172', '9783126064873'),
			array('9783468496165', '9783126064866'),
			array('9783468496639', '9783126065078'),
			array('9783468496387', '9783126064965'),
			array('9783468496561', '9783126065054'),
			array('9783468496554', '9783126065047'),
			array('9783468496219', '9783126064903'),
			array('9783468496547', '9783126065030'),
			array('9783468496523', '9783126065023'),
			array('9783468496516', '9783126065016'),
			array('9783468496189', '9783126064880'),
			array('9783468496271', '9783126064934'),
			array('9783468496486', '9783126065009'),
			array('9783468496462', '9783126064996'),
			array('9783468496455', '9783126064989'),
			array('9783468496431', '9783126064972'),
			array('9783468496370', '9783126064958'),
			array('9783468497247', '9783126064811'),
			array('9783468495038', '9783126069632'),
			array('9783468495007', '9783126069618'),
			array('9783468495045', '9783126069649'),
			array('9783468495021', '9783126069625'),
			array('9783468495113', '9783126065214'),
			array('9783468497452', '9783126064033'),
			array('9783468475504', '9783126062299'),
			array('9783468475542', '9783126062329'),
			array('9783468475566', '9783126062336'),
			array('9783468475573', '9783126062343'),
			array('9783468475467', '9783126062275'),
			array('9783468475511', '9783126062305'),
			array('9783468967139', '9783126062671'),
			array('9783468475900', '9783126062589'),
			array('9783468475634', '9783126062404'),
			array('9783468475597', '9783126062367'),
			array('9783468967306', '9783126062732'),
			array('9783468967337', '9783126062763'),
			array('9783468967313', '9783126062749'),
			array('9783468475528', '9783126062312'),
			array('9783468475580', '9783126062350'),
			array('9783468475450', '9783126062268'),
			array('9783468475481', '9783126062282'),
			array('9783468475702', '9783126062442'),
			array('9783468475740', '9783126062473'),
			array('9783468475764', '9783126062480'),
			array('9783468475771', '9783126062497'),
			array('9783468475870', '9783126062572'),
			array('9783468475719', '9783126062459'),
			array('9783468967184', '9783126062718'),
			array('9783468967078', '9783126062626'),
			array('9783468475917', '9783126062596'),
			array('9783468475832', '9783126062558'),
			array('9783468475795', '9783126062510'),
			array('9783468475726', '9783126062466'),
			array('9783468475788', '9783126062503'),
			array('9783468475306', '9783126062152'),
			array('9783468475344', '9783126062183'),
			array('9783468475368', '9783126062190'),
			array('9783468475375', '9783126062206'),
			array('9783468475313', '9783126062169'),
			array('9783468967429', '9783126062787'),
			array('9783468475399', '9783126062220'),
			array('9783468475320', '9783126062176'),
			array('9783468475382', '9783126062213'),
			array('9783468471001', '9783126062800'),
			array('9783468471049', '9783126062848'),
			array('9783468471186', '9783126062923'),
			array('9783468471254', '9783126062954'),
			array('9783468471025', '9783126062824'),
			array('9783468471018', '9783126062817'),
			array('9783468471131', '9783126062893'),
			array('9783468471087', '9783126062862'),
			array('9783468471308', '9783126062961'),
			array('9783468471346', '9783126063043'),
			array('9783468471469', '9783126063074'),
			array('9783468471483', '9783126063029'),
			array('9783468471537', '9783126063104'),
			array('9783468471322', '9783126062985'),
			array('9783468471315', '9783126062978'),
			array('9783468471445', '9783126063050'),
			array('9783468471339', '9783126062992'),
			array('9783468471384', '9783126063005'),
			array('9783468471162', '9783126062909'),
			array('9783468471032', '9783126062831'),
			array('9783468471056', '9783126062855'),
			array('9783468471353', '9783126069830'),
			array('9783468472923', '9783126064231'),
			array('9783468496837', '9783126064477'),
			array('9783468497186', '9783126064743'),
			array('9783468494932', '9783126063654'),
			array('9783468494970', '9783126063692'),
			array('9783468491764', '9783126063609'),
			array('9783468494772', '9783126063623'),
			array('9783468494871', '9783126063647'),
			array('9783468494796', '9783126063630'),
			array('9783468497032', '9783126064620'),
			array('9783468497520', '9783126064095'),
			array('9783468494192', '9783126063753'),
			array('9783468988257', '9783126061926'),
			array('9783468988233', '9783126062022'),
			array('9783468988288', '9783126061957'),
			array('9783468988240', '9783126061919'),
			array('9783468988219', '9783126061902'),
			array('9783468988226', '9783126062015'),
			array('9783468988271', '9783126061940'),
			array('9783468988332', '9783126062091'),
			array('9783468988349', '9783126062114'),
			array('9783468988325', '9783126062077'),
			array('9783468988400', '9783126062121'),
			array('9783468988431', '9783126062145'),
			array('9783468988417', '9783126062053'),
			array('9783468988424', '9783126065191'),
			array('9783468496936', '9783126064576'),
			array('9783468496899', '9783126064538'),
			array('9783468497087', '9783126064668'),
			array('9783468497124', '9783126064682'),
			array('9783468494703', '9783126061827'),
			array('9783468494710', '9783126061834'),
			array('9783468465055', '9783126063975'),
			array('9783468465079', '9783126063999'),
			array('9783468494291', '9783126063760'),
			array('9783468497209', '9783126064774'),
			array('9783468497216', '9783126064781'),
			array('9783468465093', '9783126064019'),
			array('9783468491757', '9783126063593'),
			array('9783468491771', '9783126063616'),
			array('9783468491559', '9783126063814'),
			array('9783468491818', '9783126063715'),
			array('9783468491825', '9783126063722'),
			array('9783468497506', '9783126064088'),
			array('9783468497223', '9783126064798'),
			array('9783468497230', '9783126064804'),
			array('9783468498176', '9783126063883'),
			array('9783468498152', '9783126063876'),
			array('9783468967924', '9783126063920'),
			array('9783468967900', '9783126063913'),
			array('9783468477157', '9783126064262'),
			array('9783468477218', '9783126064309'),
			array('9783468905162', '9783126061896'),
			array('9783468495373', '9783126063791'),
			array('9783468497551', '9783126064125'),
			array('9783468494956', '9783126063678'),
			array('9783468477225', '9783126064316'),
			array('9783468465031', '9783126063951'),
			array('9783468472930', '9783126064248'),
			array('9783468474217', '9783126063197'),
			array('9783468474248', '9783126063227'),
			array('9783468474262', '9783126063241'),
			array('9783468474293', '9783126063258'),
			array('9783468474224', '9783126063203'),
			array('9783468474255', '9783126063234'),
			array('9783468474316', '9783126063272'),
			array('9783468474309', '9783126063265'),
			array('9783468474415', '9783126063289'),
			array('9783468474446', '9783126063319'),
			array('9783468474460', '9783126069847'),
			array('9783468474491', '9783126063333'),
			array('9783468474422', '9783126063296'),
			array('9783468474453', '9783126063326'),
			array('9783468474439', '9783126063302'),
			array('9783468474507', '9783126063340'),
			array('9783468474606', '9783126063357'),
			array('9783468474644', '9783126063388'),
			array('9783468474613', '9783126063364'),
			array('9783468474668', '9783126063395'),
			array('9783468474637', '9783126063371'),
			array('9783468494086', '9783126065160'),
			array('9783468496820', '9783126064460'),
			array('9783468497162', '9783126064729'),
			array('9783468497063', '9783126064644'),
			array('9783468497100', '9783126064675'),
			array('9783468465017', '9783126063937'),
			array('9783468499951', '9783126065245'),
			array('9783468496905', '9783126064545'),
			array('9783468494093', '9783126065177'),
			array('9783468497278', '9783126064217'),
			array('9783468496929', '9783126064569'),
			array('9783468477270', '9783126064361'),
			array('9783468477331', '9783126064422'),
			array('9783468467998', '9783126061285'),
			array('9783468468001', '9783126061292'),
			array('9783468468056', '9783126061346'),
			array('9783468468124', '9783126061360'),
			array('9783468468018', '9783126061308'),
			array('9783468468162', '9783126061407'),
			array('9783468468049', '9783126061339'),
			array('9783468468193', '9783126061414'),
			array('9783468468131', '9783126061384'),
			array('9783468468025', '9783126061315'),
			array('9783468468032', '9783126061322'),
			array('9783468468247', '9783126061438'),
			array('9783468468230', '9783126061421'),
			array('9783468497469', '9783126064040'),
			array('9783468496813', '9783126064453'),
			array('9783468497148', '9783126064705'),
			array('9783468465086', '9783126064002'),
			array('9783468496912', '9783126064552'),
			array('9783468497131', '9783126064699'),
			array('9783468470011', '9783126061445'),
			array('9783468470059', '9783126061476'),
			array('9783468470219', '9783126061544'),
			array('9783468470028', '9783126061452'),
			array('9783468470172', '9783126061520'),
			array('9783468470202', '9783126061537'),
			array('9783468470035', '9783126061469'),
			array('9783468470110', '9783126061483'),
			array('9783468470226', '9783126061551'),
			array('9783468470264', '9783126061568'),
			array('9783468470318', '9783126061575'),
			array('9783468470356', '9783126061605'),
			array('9783468470486', '9783126061674'),
			array('9783468470325', '9783126061582'),
			array('9783468470431', '9783126061650'),
			array('9783468470479', '9783126061667'),
			array('9783468470332', '9783126061599'),
			array('9783468470394', '9783126061612'),
			array('9783468470615', '9783126061681'),
			array('9783468470653', '9783126061711'),
			array('9783468470691', '9783126061742'),
			array('9783468470622', '9783126061698'),
			array('9783468470677', '9783126061735'),
			array('9783468470639', '9783126061704'),
			array('9783468470660', '9783126061728'),
			array('9783468491672', '9783126061223'),
			array('9783468491696', '9783126061247'),
			array('9783468491689', '9783126061230'),
			array('9783468967290', '9783126061254'),
			array('9783468494598', '9783126063838'),
			array('9783468497667', '9783126063869'),
			array('9783468497643', '9783126063852'),
			array('9783468465062', '9783126063982'),
			array('9783468494109', '9783126065184'),
			array('9783468495106', '9783126063845'),
			array('9783468465024', '9783126063944'),
			array('9783468497490', '9783126064071'),
			array('9783468491566', '9783126063821'),
			array('9783468497476', '9783126064057'),
			array('9783468465109', '9783126064026'),
			array('9783468499883', '9783126065283'),
			array('9783468477294', '9783126064385'),
			array('9783468477348', '9783126064439'),
			array('9783468497544', '9783126064118'),
			array('9783468477188', '9783126064286'),
			array('9783468477249', '9783126064330'),
			array('9783468497070', '9783126064651'),
			array('9783468498411', '9783126061841'),
			array('9783468471506', '9783126064170'),
			array('9783468496806', '9783126064446'),
			array('9783468476365', '9783126066396'),
			array('9783468472947', '9783126064200'),
			array('9783468494949', '9783126063661'),
			array('9783468497537', '9783126064101'),
			array('9783468499913', '9783126065290'),
			array('9783468499739', '9783126065276'),
			array('9783468498695', '9783126061858'),
			array('9783468904653', '9783126061865'),
			array('9783468904677', '9783126061872'),
			array('9783468904684', '9783126061889'),
			array('9783468499937', '9783126063777'),
			array('9783468496950', '9783126064767'),
			array('9783468495175', '9783126066402'),
			array('9783468495199', '9783126066419'),
			array('9783468494338', '9783126061773'),
			array('9783468494345', '9783126061780'),
		);

		echo '<pre>';
		$this->load->model('catalogo/m_articulocodigo');
		foreach($isbns as $par)
		{
			echo "BUSCANDO {$par[1]}\n";
			$data = $this->m_articulocodigo->get(null, null, null, null, "nCodigo={$par[1]}");
			if (count($data) == 0)
			{
				$data = $this->m_articulocodigo->get(null, null, null, null, "nCodigo={$par[0]}");
				if (count($data) > 0)
				{
					foreach ($data as $reg)
					{
						$ins = array(
							'nIdLibro' 	=> $reg['nIdLibro'],
							'nCodigo'	=> $par[1]
							);
						if ($this->m_articulocodigo->insert($ins) < 0)
						{
							echo "{$par[0]} -> {$par[1]} ERROR {$this->m_articulocodigo->error_message()}\n";
						}
						else
						{
							echo "{$par[0]} -> {$par[1]} OK\n";
						}						
					}
				}
				else
				{
					echo "{$par[0]} -> {$par[1]} NO ESTA EL ANTIGUO\n";
				}
			}
			else
			{
				echo "{$par[0]} -> {$par[1]} YA ESTABA CREADO\n";
			}
		}
		echo '</pre>';
	}

	function lh()
	{
		$this->load->model('ventas/m_factura');
		$fecha = format_mssql_date(mktime(0, 0, 0, 9, 1, 2013));
	
		$data = $this->m_factura->get(null, null, 'dCreacion', 'ASC', "nIdSerie=31 AND dCreacion>={$fecha}");
		$repes = array();
		foreach($data as $f)
		{
			if (isset($last['id'])
				&& (($f['dCreacion'] - $last['dCreacion']) <= 30) 
				&& ($f['nLibros'] == $last['nLibros'])
				&& ($f['_fTotal'] == $last['_fTotal']))
			{
				$repes[] = array(
					'f1' => $last,
					'f2' => $f					
					);
				var_dump($f['dCreacion'] - $last['dCreacion'], format_datetime($last['dCreacion']), format_datetime($f['dCreacion']), $repes); die();
			} 
			else
			{
				$last = $f;
			}
		}
	}

	function precios($isbn = null)
	{		
		$timer_global = microtime(true);
		$this->load->library('SearchInternet');
		var_dump($this->searchinternet->precios($isbn , 'comein'));
		echo ((microtime(true)-$timer_global)) . "\n";
	}

	private function console_init()
	{
		# Sin compresión. Debe ir enviando los pasos
		@ini_set('zlib.output_compression',0);
		@ini_set('implicit_flush',1);
		if (ob_get_level() == 0) 
		{
		    ob_start();
		}

		print str_repeat(' ',1024);
	}

	private function flush($texto = null)
	{
		if (!empty($texto)) echo $texto;
		flush();
		ob_flush();
	}

	function diba()
	{
		set_time_limit(0);
		require_once DIR_CONTRIB_PATH . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php';
		$this->load->library('ISBNEAN');

		# Lee las palabras de los ISBNS
		$path = BASEPATH . 'application' . DS . 'controllers' . DS . 'concursos' . DS;;

		$mandan = array_filter(explode("\n", file_get_contents($path . 'mandan.txt')));
		$todos = array_filter(explode("\n", file_get_contents($path . 'todos.txt')));

		$path = '/home/alibri/Descargas/DIBA/';
		$files = scandir($path);
		foreach ($files as $key => $file) 
		{
			if (is_file($path . $file))
			{
				$page = basename($file);
				$page = pathinfo($page);
				$ext = $page['extension'];

				if ( !in_array(strtolower($ext), array('xls', 'xlsx')))
					unset($files[$key]);
			}
			else
				unset($files[$key]);
		}
		$this->console_init();
		$this->flush('<pre>');
		$this->flush(print_r($files, TRUE) . "\n");

		$this->flush(print_r($mandan, TRUE) . "\n");
		$this->flush(print_r($todos, TRUE) . "\n");

		$words = array();
		$count = 0;
		foreach ($files as $file)
		{
			$this->flush("{$file}\n");

			$filename = ($path . $file);
			$objPHPExcel = new PHPExcel();
			$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($filename);
			$campos = array(
				'QT'		=> 'A',
				'AUT'		=> 'B',
				'TIT'		=> 'C',
				'EDIT'		=> 'D',
				'EDIC'		=> 'E',
				'COL'		=> 'F',
				'ISBN'		=> 'G',
				'PRECIO'	=> 'H',
				'CDU'		=> 'I',
				'CDU2'		=> 'J'
				);
			foreach ($objPHPExcel->getAllSheets() as $sheet) 
			{
				$name = $sheet->getTitle();
				$this->flush("	Hoja {$name}\n");
				$sheetData = $sheet->toArray(null, FALSE, TRUE, TRUE);		
				# Lee la cabecera
				$cab = $sheetData[1];
				foreach ($cab as $key => $value) 
				{
					$campos[$value] = $key;
				}
				#Fuera cabecera
				for($i=2; $i <= count($sheetData); $i++) 
				{
					$value = $sheetData[$i];
					$original = $value[$campos['ISBN']];
					if (!empty($original))
					{
						$isbns = explode(';', $original);
						$res = null;
						if (count($isbns) == 1)
						{
							$res = array('type' => 'unique',  'isbns' => $isbns);
						}
						elseif (count($isbns) > 1)
						{
							#var_dump($isbns);
							foreach ($isbns as $isbn) 
							{
								preg_match_all('/\(([^)]*)\)/', $isbn, $matches);
								# Tiene textos?
								if (isset($matches[1][0]))
								{
									foreach ($matches[1] as $v) 
									{
										$value = trim($v);
										if (in_array($v, $mandan))
										{
											$isbn = preg_replace('/(\([^)]*\))/', '', $isbn);
											$res = array('type' => 'unique',  'isbns' => array(trim($isbn)));
											#$this->flush($original ."\n");
											#$this->flush(print_r($res, TRUE)); 
											#die();
											break;
										}
										if (in_array($v, $todos))
										{
											$res = array('type' => 'all',  'isbns' => $isbns);
											break;
										}

										$res = array('type' => 'onlyone',  'isbns' => $isbns);
									}
								}
								else
								{
									# No tiene palabras
									$res = array('type' => 'onlyone',  'isbns' => $isbns);
								}
							}
						}
						if ($res['type'] != 'all')
						{
							foreach ($res['isbns'] as $key => $value) 
							{
								$res['isbns'][$key] = preg_replace('/(\([^)]*\))/', '', $value);
							}
							#if ($count == 10) die();
						}
						else
						{
							$this->flush($original ."\n");
							$this->flush(print_r($res, TRUE)); 
						}
					}
					#var_dump($original); die();
					#$isbn = $this->isbnean->to_isbn($original);
				}
			}
		}
		foreach ($words as $key => $value) 
		{
			$this->flush($value . "\n");	
		}
	}

	function mover()
	{
		$datos = array(
			array(88183,1),
			array(88525,1),
		);
		$this->load->model('catalogo/m_movimiento');
		#$this->db->trans_begin();
		foreach($datos as $reg)
		{
			$id_n = $this->m_movimiento->mover($reg[0], 816, 835, $reg[1]);
			var_dump($id_n);
		}
		#$this->db->trans_rollback();
	}

	function portadas()
	{
		error_reporting(E_ALL);
		$muestra = 1000;
		$this->load->library('Dilve');
		$this->load->library('Color2');
		$this->load->library('ISBNEAN');
		$this->load->library('SearchInternet');
		$this->load->library('WebSave');
		$this->load->model('catalogo/m_articulo');
		$timer_global = microtime(true);
		$this->color2->head();
		$this->color2->title('Portadas');
		$this->color2->head();
		$id = 562310;

		set_time_limit(0);

		$this->color2->info("Leyendo artículos {$muestra} a partir de {$id}");
		$data = $this->m_articulo->sinportada(525746, 562310);
		$portadas = 0;
		$count = 0;
		foreach ($data as $reg)
		{
			++$count;
			$local = $this->websave->get_cover($reg['nEAN']);
			if (isset($local))
			{
				++$portadas;
				#$this->color2->line("($portadas):[{$reg['nIdLibro']}] - %gLOCAL%_ - %_{$local}");		
				echo "{$reg['cTitulo']} - {$reg['nEAN']}<br/><img src='{$local}' width='200' /><br/>";
				if ($portadas >= $muestra)
					exit;
			}
		}
		$this->color2->line("Se han encotrado %_{$portadas}/{$count}%_ ".sprintf(" en : %%_%fs%%n", microtime(true)-$timer_global));
	}

	function phone()
	{
		$data = array(
			'93 317 90 91',
			'+4509912122',
			'609735148',
			'933.65666.3',
			'58552-555528',
			'caca',
			'1'
			);
		foreach ($data as $k)
		{
			var_dump($k, is_phone($k));
		}
	}

	function diba_menor()
	{
		set_time_limit(0);

		$timer_global = microtime(true);

		$this->load->library('ISBNEAN');
		$this->load->library('Importador');
		$this->load->library('Dilve');
		$this->load->library('SearchInternet');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('concursos/m_pedidoconcursolinea');

		$top = null;
		$muestra = 50;
		$esta = array();
		$regs = array();

		echo "Leyendo en artículos pedido...\n";
		$data = $this->m_pedidoconcursolinea->get(null, $top, null, null, 'nIdBiblioteca=12');
		echo ((microtime(true)-$timer_global)) . "s\n";
		echo count($data) . " registros\n";

		# Solo busca los que no tienen precio...
		$codes = array();
		foreach ($data as $reg) 
		{
			#var_dump($reg); die();
			$isbn = $this->isbnean->to_isbn($reg['cISBN2']);
			if ($reg['fPVP2'] > 0)
			{
				$res['modo'] = 'bp';
				$reg['price'] = $reg['fPVP2'];
				$esta[$isbn] = $reg;
			}
			else
			{
				$codes[] = $isbn;
				$regs[$isbn] = $reg;
			}
		}

		echo count($regs) . " artículos sin precio\n"; 
		$articulos = array_chunk($regs, $muestra);

		echo "Leyendo en DILVE...\n";
		foreach ($articulos as $trozo) 
		$dilve = $this->dilve->get($codes);
		if (count($dilve) > 0)
		{
			foreach ($dilve as $isbn => $d)
			{
				if (!empty($d))
				{
					$res = $this->importador->onix($d);
					$regs[$isbn]['modo'] = 'dilve';
					$regs[$isbn]['extra'] = $res;
					$regs[$isbn]['price'] = $res['price'];
					$esta[$isbn] = $regs[$isbn];
					unset($regs[$isbn]);
				}
			}			
		}

		echo ((microtime(true)-$timer_global)) . "s\n";
		echo 'Se han encontrado ' .count($esta) . ' de ' . count($data) . "\n";

		$motores = array('casadellibro', 'amazon');
		foreach ($motores as $motor)
		{
			echo "Buscando en [{$motor}] " . count($regs) . " registros\n";
			foreach ($regs as $isbn => $reg)
			{
				$res = $this->searchinternet->precios($isbn , $motor);
				#var_dump($isbn, $res, $res[0]['price']); die();
				if (!empty($res[0]['price']))
				{
					$regs[$isbn]['modo'] = $motor;
					$regs[$isbn]['extra'] = $res[0];
					$regs[$isbn]['price'] = $res[0]['price'];
					$esta[$isbn] = $regs[$isbn];
					unset($regs[$isbn]);
				}
			}			
			echo "[{$motor}] - Se han encontrado " .count($esta) . ' de ' . count($data) . "\n";
			if (count($esta) == count($data)) break;
		}
		echo "Calculando precio...\n";
		$bp = array('total' => 0);
		$otros = array('total' => 0);
		foreach ($esta as $key => $reg) 
		{
			#echo "{$reg['cSala']} - ";
			if (!isset($bp[$reg['cSala']]))
			{
				$bp[$reg['cSala']] = 0;
				$otros[$reg['cSala']] = 0;
			}
			$precio = $reg['price'];
			$pvp = $reg['fPVP2'];
			if ($pvp <=0 )
				$pvp = $precio;
			$bp['total'] += $pvp;
			$otros['total'] += $precio;
			$bp[$reg['cSala']] += $pvp;
			$otros[$reg['cSala']] += $precio;
		}
		#var_dump($bp);
		echo "TOTAL:\n------\nSala\tBP\tOTROS\n";
		foreach($bp as $k => $v)
		{
			echo "{$k}\t" . format_price($bp[$k], FALSE) . "\t" .format_price($otros[$k], FALSE) . "\n";
		}
		echo ((microtime(true)-$timer_global)) . "s\n";
	}

	function diba_menor2()
	{
		set_time_limit(0);

		$timer_global = microtime(true);

		$this->load->library('ISBNEAN');
		$this->load->library('Importador');
		$this->load->library('Dilve');
		$this->load->library('SearchInternet');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('concursos/m_pedidoconcursolinea');

		$top = null;
		$muestra = 50;
		$esta = array();
		$regs = array();

		echo "Leyendo en artículos pedido...\n";
		$data = $this->m_pedidoconcursolinea->get(null, $top, null, null, 'nIdBiblioteca=12');
		echo ((microtime(true)-$timer_global)) . "s\n";
		echo count($data) . " registros\n";

		# Solo busca los que no tienen precio...
		$codes = array();
		foreach ($data as $reg) 
		{
			#var_dump($reg); die();
			$isbn = $this->isbnean->to_isbn($reg['cISBN2']);
			/*if ($reg['fPVP2'] > 0)
			{
				$res['modo'] = 'bp';
				$reg['price'] = $reg['fPVP2'];
				$esta[$isbn] = $reg;
			}
			else*/
			{
				$codes[] = $isbn;
				$regs[$isbn] = $reg;
			}
		}

		echo count($regs) . " artículos sin precio\n"; 
		$articulos = array_chunk($regs, $muestra);

		echo "Leyendo en DILVE...\n";
		foreach ($articulos as $trozo) 
		$dilve = $this->dilve->get($codes);
	#var_dump($dilve); die();
		if (count($dilve) > 0)
		{
			foreach ($dilve as $isbn => $d)
			{
				if (!empty($d))
				{
					$res = $this->importador->onix($d);
					$regs[$isbn]['modo'] = 'dilve';
					$regs[$isbn]['extra'] = $res;
					$regs[$isbn]['price'] = $res['precio'];
					$esta[$isbn] = $regs[$isbn];
					unset($regs[$isbn]);
				}
			}			
		}

		echo ((microtime(true)-$timer_global)) . "s\n";
		echo 'Se han encontrado ' .count($esta) . ' de ' . count($data) . "\n";

		$motores = array('casadellibro', 'amazon');
		foreach ($motores as $motor)
		{
			echo "Buscando en [{$motor}] " . count($regs) . " registros\n";
			foreach ($regs as $isbn => $reg)
			{
				$res = $this->searchinternet->precios($isbn , $motor);
				#var_dump($isbn, $res, $res[0]['price']); die();
				if (!empty($res[0]['price']))
				{
					$regs[$isbn]['modo'] = $motor;
					$regs[$isbn]['extra'] = $res[0];
					$regs[$isbn]['price'] = $res[0]['price'];
					$esta[$isbn] = $regs[$isbn];
					unset($regs[$isbn]);
				}
			}			
			echo "[{$motor}] - Se han encontrado " .count($esta) . ' de ' . count($data) . "\n";
			if (count($esta) == count($data)) break;
		}
		echo "Calculando precio...\n";
		$bp = array('total' => 0);
		$otros = array('total' => 0);
		foreach ($esta as $key => $reg) 
		{
			#echo "{$reg['cSala']} - ";
			if (!isset($bp[$reg['cSala']]))
			{
				$bp[$reg['cSala']] = 0;
				$otros[$reg['cSala']] = 0;
			}
			$this->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], array('fPrecio' => $reg['price']));
			echo "{$reg['nIdLineaPedidoConcurso']} => {$reg['price']}\n";
			$precio = $reg['price'];
			$pvp = $reg['fPVP2'];
			if ($pvp <=0 )
				$pvp = $precio;
			$bp['total'] += $pvp;
			$otros['total'] += $precio;
			$bp[$reg['cSala']] += $pvp;
			$otros[$reg['cSala']] += $precio;
		}
		#var_dump($bp);
		echo "TOTAL:\n------\nSala\tBP\tOTROS\n";
		foreach($bp as $k => $v)
		{
			echo "{$k}\t" . format_price($bp[$k], FALSE) . "\t" .format_price($otros[$k], FALSE) . "\n";
		}
		echo ((microtime(true)-$timer_global)) . "s\n";
	}

	function clear_images()
	{
		$this->load->model('catalogo/m_articulo');
		
		$this->db->flush_cache();
		$this->db->select('nIdRegistro')
		->from('Fotos')
		->where("cExtension = 'stm' OR cExtension = 'bmp'");
		$query = $this->db->get();
		$data = $query->result_array();

		foreach ($data as $reg)
		{
			echo $reg['nIdRegistro'] . "\n";
			$this->m_articulo->set_portada($reg['nIdRegistro']);
			$this->db->where('nIdRegistro='.$reg['nIdRegistro'])->delete('Fotos');
		}
	}

	function catalogados()
	{
		/*
		select * 
from [dbo].[Ext_CambiosEstadoLineaConcurso]
where nIdLineaPedidoConcurso IN (
select [nIdLineaPedidoConcurso]
from [dbo].[Ext_LineasPedidoConcurso]
where nIdBiblioteca = 17
	and nIdEstado = 2
)
*/
		$this->load->model('catalogo/m_articulo');
		$this->load->model('concursos/m_pedidoconcursolinea');
		$this->load->model('concursos/m_cambioestado');
		$this->load->model('ventas/m_albaransalida');
		$data = $this->m_pedidoconcursolinea->get(null, null, null, null, 'nIdBiblioteca = 11 AND nIdEstado = 17');
		echo '<pre>';
		foreach ($data as $reg)
		{
			$estados = $this->m_cambioestado->get(null, null, null, null, 'nIdEstado=17 AND nIdLineaPedidoConcurso=' . $reg['nIdLineaPedidoConcurso']);

			$fecha = format_date($estados[0]['dCreacion']);
			echo "{$reg['nIdLineaPedidoConcurso']} {$reg['cSala']} -[{$fecha}] : {$reg['cTitulo2']}\n";
			$fecha = format_mssql_date($estados[0]['dCreacion']);

			$this->db->flush_cache();
			$this->db->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
			->from('Ext_LineasPedidoConcurso')
			->join('Ext_CambiosEstadoLineaConcurso', 'Ext_CambiosEstadoLineaConcurso.nIdLineaPedidoConcurso=Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
			->where('Ext_CambiosEstadoLineaConcurso.nIdLineaPedidoConcurso<>' . $reg['nIdLineaPedidoConcurso'])
			->where('Ext_LineasPedidoConcurso.nIdBiblioteca='.$reg['nIdBiblioteca'])
			->where('Ext_LineasPedidoConcurso.nIdSala='.$reg['nIdSala'])
			->where('Ext_CambiosEstadoLineaConcurso.nIdEstado=17')
			->where('Ext_CambiosEstadoLineaConcurso.dCreacion > ' . $this->db->dateadd('hh', -3, $fecha))
			->where('Ext_CambiosEstadoLineaConcurso.dCreacion < ' . $this->db->dateadd('hh', +3, $fecha));
			$query = $this->db->get();
			$data2 = $query->result_array();
			$albs = array();
			foreach ($data2 as $reg2)
			{
				$d = $this->m_pedidoconcursolinea->load($reg2['nIdLineaPedidoConcurso']);
				if (isset($d['nIdAlbaranSalida']))
				{
					if (isset($albs[$d['nIdAlbaranSalida']]))
						$albs[$d['nIdAlbaranSalida']] += 1;
					else
						$albs[$d['nIdAlbaranSalida']] = 1;
				}
			}
			foreach ($albs as $k => $v)				
			{
				$alb = $this->m_albaransalida->load($k);
				#var_dump($alb); die();
				echo "    -> {$k} ({$v}) - {$alb['cRefCliente']} - {$alb['cSala']} - [" . format_datetime($alb['dCreacion']) . "]\n";
			}
		}		
	}

	/**
	 * Libros en las devoluciones de Sant Jordi que se pueden entregar a la DDGi
	 * @return HTML
	 */
	function sj()
	{
		$this->load->model('catalogo/m_articulo');
		$data = $this->m_articulo->sj();
		$body = $this->load->view('test/sj', array('titulos' => $data, 'seccion' => FALSE), TRUE);

		$this->out->html_file($body, $this->lang->line('Comprobación artículos'), 'iconoCheckArticulosTab');
	}

	/**
	 * Libros de toda la tienda se pueden entregar a la DDGi
	 * @return HTML
	 */
	function sj2()
	{
		$this->load->model('catalogo/m_articulo');
		$data = $this->m_articulo->sj2();
		$esta = array();
		foreach ($data as $key => $value) 
		{
			$k = $value['nIdPedido'] . '_' . $value['nIdLibro'];
			if (isset($esta[$k]))
			{
				unset($data[$key]);
			}
			else
			{
				$esta[$k] = $k;
			}
		}
		$body = $this->load->view('test/sj', array('titulos' => $data, 'seccion' => TRUE), TRUE);

		$this->out->html_file($body, $this->lang->line('Comprobación artículos'), 'iconoCheckArticulosTab');
	}

	/**
	 * Crea un EXCEL con las tarifas de correos
	 * @param  string $tarifas Fichero con las tarifas descargadas
	 * @param  string $datos   Fichero con la regiones
	 * @param  string $out     Fichero de salida
	 * @param  string $title   Título de la hoja de EXCEL
	 * @return null
	 */
	private function _correos($tarifas, $datos, $out, $title)
	{
		$path = __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS . 'tools' . DS;
		$nacional = unserialize(file_get_contents($path . $tarifas));
		$data = file_get_contents($path . $datos);
		$regex = '/<option value="(.*)">(.*)<\/option>/';
		preg_match_all($regex, $data, $matches);
		$regs = array();
		$reg = '';
		foreach ($matches[1] as $k => $v)
		{
			if (empty($column))
			{
				$column = '<tr><th>ZONA</th>';
				foreach ($nacional[$v] as $k2 => $v2)
				{
					$column .= '<th>' . $k2 . '</th>';
				}
				$column .= '</tr>' . "\n";
			}
			$reg .= '<tr><td>' . $matches[2][$k] . '</td>';
			foreach ($nacional[$v] as $v2)
			{
				$reg .= '<td>' . $v2 . '</td>';
			}
			$reg .= '</tr>' . "\n";
			#$regs[$matches[2][$k]] = $nacional[$v];
		}
		$table = '<table>' . "\n" . $column . "\n" . $reg  . "\n" . '</table>';

		$this->load->library('ExcelData');
		$this->exceldata->table2excel($table, $path . $out, $title, 'alexl', 'alibri', $title);
		#echo $table;
	}

	/**
	 * Obtiene las tarifas de correos por zonas
	 * @param  string $file   Fichero destino
	 * @param  array $zonas  Array de zonas por nombre
	 * @param  array $zonas2 Array de zonas por ID
	 * @return null
	 */
	private function _correoszonas($file, &$zonas, &$zonas2)
	{
		#$this->_correos('tarifas-nac.dat', 'correos-nac.txt', 'nacional.xls', 'Nacional');
		#$this->_correos('tarifas.dat', 'correos.txt', 'internacional.xls', 'Internacional');
		$this->load->library('ExcelData');
		$this->load->model('perfiles/m_region');
		$this->load->model('perfiles/m_pais');
		$path = __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS . 'tools' . DS;
		$file = $path . $file;
		$excel = $this->exceldata->read($file);
		foreach ($excel['cells'] as $key => $value) 
		{
			$region = $value[0];
			$zonas[$region] = array();
			$res = $this->m_pais->search($region);
			if ($res)
			{
				foreach ($res as $v) 
				{
					$r = $this->m_pais->load($v['id']);
					if (isset($r['nIdZona']))
					{
						if (!isset($zonas[$region][$r['nIdZona']]))
							$zonas[$region][$r['nIdZona']] = 0;
						++$zonas[$region][$r['nIdZona']];
						$zonas2[$r['nIdZona']][] = $value;
					}
				}
			}
			$res = $this->m_region->search($region);		
			if ($res)
			{
				foreach ($res as $v) 
				{
					$r = $this->m_region->load($v['id']);
					if (isset($r['nIdZona']))
					{
						if (!isset($zonas[$region][$r['nIdZona']]))
							$zonas[$region][$r['nIdZona']] = 0;
						++$zonas[$region][$r['nIdZona']];
						$zonas2[$r['nIdZona']][] = $value;
					}
				}
			}
		}
	}

	/**
	 * Obtiene las tarifas de correos desde la página Web
	 */
	function correos()
	{
		#$this->_correos('tarifas-nac.dat', 'correos-nac.txt', 'nacional.xls', 'Nacional');
		#$this->_correos('tarifas.dat', 'correos.txt', 'internacional.xls', 'Internacional');
		$zonas = array();
		$zonas2 = array();
		$this->_correoszonas('nacional.xls', $zonas, $zonas2);
		$this->_correoszonas('internacional.xls', $zonas, $zonas2);
		$this->load->model('perfiles/m_zona');
		$zonas3 = array();
		foreach ($zonas2 as $k => $v)
		{
			$zona = $this->m_zona->load($k);
			foreach ($v as $v2)
			{

				$v3 = array($zona['cNombre'], $zona['cDescripcion'], $v2[0]);
				for ($i = 1; $i < count($v2); $i++)
				{
					$v3[] = (float) str_replace(',', '.', $v2[$i]);
				}
				$zonas3[] = $v3;
			}		
		}
		$this->load->library('ExcelData');
		$wb = $this->exceldata->create();
		$this->exceldata->add($wb, $zonas3, 'Zonas');
		$this->exceldata->close($wb);
		$file = $this->htmlfile->pathfile($this->exceldata->get_filename($wb));
		$path = __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS . 'tools' . DS;
		$xls = $path . 'zonas.xls';
		copy($file, $xls);
		var_dump($xls); 
	}
}

/* End of file test.php */
/* Location: ./system/application/controllers/sys/test.php */