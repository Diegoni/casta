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
/**
 *
 * You need to specify atleast one input file, and exactly one output file
 * Use - for stdin or stdout
 * Usage: wkhtmltopdf [OPTION]... <input file> [more input files] <output file> converts htmlpages into a pdf
 * Options:
 *   -b, --book                      Set the options one would usualy set when printing
 *                                   a book.
 *       --cover <url>               Use html document as cover. It will be inserted before
 *                                   the toc with no headers and footers.
 *   -H, --default-header            Add a default header, with the name of the page to
 *                                   the left, and the page number to the right, this is
 *                                   short for: --header-left='[webpage]' --header-right='[page]/[toPage]'
 *                                   * --top 2cm --header-line.
 *   -n, --disable-javascript        Do not allow webpages to run javascript.
 *   -d, --dpi <dpi>                 Change the dpi explicitly.
 *       --encoding <encoding>       Set the default text encoding, for input.
 *       --footer-center <text>      Centered footer text.
 *       --footer-font-name <name>   Set footer font name (default Arial).
 *       --footer-font-size <size>   Set footer font size (default 11).
 *       --footer-left <text>        Left aligned footer text.
 *       --footer-line               Display line above the footer.
 *       --footer-right <text>       Right aligned footer text.
 *   -g, --grayscale                 PDF will be generated in grayscale.
 *       --header-center <text>      Centered header text.
 *       --header-font-name <name>   Set header font name (default Arial).
 *       --header-font-size <size>   Set header font size (default 11).
 *       --header-left <text>        Left aligned header text.
 *       --header-line               Display line below the header.
 *       --header-right <text>       Right aligned header text.
 *   -h, --help                      Display help.
 *   -l, --lowquality                Generates lower quality pdf/ps. Useful to shrink the
 *                                   result document space.
 *   -B, --margin-bottom <unitread>  Set the page bottom margin (default 10mm).
 *   -L, --margin-left <unitread>    Set the page left margin (default 10mm).
 *   -R, --margin-right <unitread>   Set the page right margin (default 10mm).
 *   -T, --margin-top <unitread>     Set the page top margin (default 10mm).
 *       --no-background             Do not print background.
 *   -O, --orientation <orientation> Set orientation to Landscape or Portrait.
 *       --outline                   Put an outline into the pdf.
 *       --outline-depth <level>     Set the depth of the outline (default 4).
 *   -s, --page-size <size>          Set pape size to: A4, Letter, ect..
 *       --print-media-type          Use print media-type instead of screen.
 *   -p, --proxy <proxy>             Use a proxy.
 *   -q, --quit                      Be less verbose.
 *       --redirect-delay <msec>     Wait some miliseconds for js-redirects (default 200).
 *   -t, --toc                       Insert a table of content in the beginning of the document.
 *       --toc-depth <level>         Set the depth of the toc (default 3).
 *       --toc-font-name <name>      Set the font used for the toc (default Arial).
 *
 *       --toc-header-fs <size>      The font size of the toc header (default 15).
 *       --toc-header-text <text>    The header text of the toc (default Table Of Contents).
 *       --toc-l1-font-size <size>   Set the font size on level 1 of the toc (default 12).
 *       --toc-l1-indentation <num>  Set indentation on level 1 of the toc (default 0).
 *       --toc-l2-font-size <size>   Set the font size on level 2 of the toc (default 10).
 *       --toc-l2-indentation <num>  Set indentation on level 2 of the toc (default 20).
 *       --toc-l3-font-size <size>   Set the font size on level 3 of the toc (default 8).
 *       --toc-l3-indentation <num>  Set indentation on level 3 of the toc (default 40).
 *       --toc-no-dots               Do not use dots, in the toc.
 *       --user-style-sheet <url>    Specify a user style sheet, to load with every page.
 *   -V, --version                   Output version information an exit.
 *
 * Proxy:
 *   By default proxy information will be read from the environment
 *   variables: proxy, all_proxy and http_proxy, proxy options can
 *   also by specified with the -p switch
 *   <type> := "http://" | "socks5://"
 *   <userinfo> := <username> (":" <password>)? "@"
 *   <proxy> := "None" | <type>? <userinfo>? <host> (":" <port>)?
 *
 * Header and footer text:
 * In a header or footer text the following variables can be used
 *  * [page]       Replaced by the number of the pages currently beeing printed
 *  * [fromPage]   Replaced by the number of the first page to be printed
 *  * [toPage]     Replaced by the number of the last page to be printed
 *  * [webpage]    Replaced by the url of the page beeing printed
 *  * [section]    Replaced by the name of the current section
 *  * [subsection] Replaced by the name of the current subsection
 *
 * Mail bug reports and suggestions to <antialze@gmail.com>.
 */

/**
 * Conversor de PDF
 * @author alexl
 *
 */
class PdfLib {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return PdfLib
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		log_message('debug', 'PDF Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Convierte un fichero HTML a PDF
	 * @param string $fin Fichero HTML de entrada
	 * @param string $fout Fichero PDF de salida
	 * @param string $papersize Tamaño del papel (A3, A4, etc)
	 * @param string $orientation Orientación
	 * @param bool $attached TRUE: se envía como fichero adjunto, FALSE: se envía como datos
	 * @return file
	 */
	function create($fin, $fout = null, $papersize = null, $orientation = null, $stream = TRUE, $attached = TRUE, $margins = NULL)
	{
		$this->obj->load->library('HtmlFile');

		// Si el archivo no está indicado se inventa uno temporal
		if (!isset($fout))
		{
			$name = pathinfo($fin);
			$name = $name['filename'] . '.pdf';
			$fout = $this->obj->htmlfile->pathfile($name);
		}
		else
		{
			$name = pathinfo($fout);
			$name = $name['basename'];
		}

		#if (!file_exists($fout))
		{
			// Obtiene la orientación y page-size del fichero
			$html = file_get_contents($fin);
			$o = $this->obj->htmlfile->get_orientation($html);
			$ps = $this->obj->htmlfile->get_page_size($html);
			$m = $this->obj->htmlfile->get_margins($html);

			// Parámetros por defecto
			$execpath = $this->obj->config->item('pdf.path');
			$default = $this->obj->config->item('pdf.parameters');
			$papersize = isset($ps)?$ps:(isset($papersize)?$papersize:$this->obj->config->item('pdf.papersize'));
			$orientation = isset($o)?$o:(isset($orientation)?$orientation:$this->obj->config->item('pdf.orientation'));
			$margins = isset($m)?$m:(isset($margins)?$margins:array($this->obj->config->item('pdf.topmargin'), 
				$this->obj->config->item('pdf.leftmargin'),
				$this->obj->config->item('pdf.bottommargin'),
				$this->obj->config->item('pdf.rightmargin')));

			// Aplica los replaces
			$replaces = $this->obj->config->item('pdf.replaces');
			if (isset($replaces))
			{
				foreach ($replaces as $k => $v)
				{
					$html = str_replace($k, $v, $html);
				}
				$name2 = pathinfo($fin);
				$name2 = $name2['filename'] . '.rep.html';
				$fin = $this->obj->htmlfile->pathfile($name2);
				file_put_contents($fin, $html);
			}
			// Elimina los no-print
			$regex = '/<.*?"no-print"[^>]*>(.*)<\//';
			$html = preg_replace($regex, '', $html);
			if ($html != $html) 
			{
				$name2 = pathinfo($fin);
				$name2 = $name2['filename'] . '.rep2.html';
				$fin = $this->obj->htmlfile->pathfile($name2);
				file_put_contents($fin, $html);
			}

			// Comando a ejecutar
			$params = "-s \"{$papersize}\" -O \"{$orientation}\" -B \"{$margins[2]}\" -L \"{$margins[1]}\" -R \"{$margins[3]}\"  -T \"{$margins[0]}\" {$default} \"{$fin}\" \"{$fout}\"";
			$exec = $execpath . ' ' . $params;

			// Sin límite de tiempo
			set_time_limit(0);
			$result = null;
			$r = system($exec, $result);
		}
		// Devuelve el archivo como un un adjunto si así se indica o simplemente deja el archivo creado
		if ($stream)
		{
			if ($attached)
			{
				header("Content-type: application/pdf; charset=UTF-8");
				header("Content-Disposition: attachment; filename={$name}");
				header("Pragma: no-cache");
				header("Expires: 0");
				//unlink($fout);
				readfile($fout);
			}
			else
			{
				redirect($this->obj->htmlfile->url($name));
			}
		}

		return $name;
	}
}

/* End of file pdflib.php */
/* Location: ./system/libraries/pdflib.php */