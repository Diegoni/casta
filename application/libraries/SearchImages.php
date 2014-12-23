<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
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
 * Búsqueda de imágenes
 * @author alexl
 *
 */
class SearchImages
{

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Extensiones -> mime
	 * @link http://www.w3schools.com/media/media_mimeref.asp
	 * @var array
	 */
	private $_mimetypes = array(
			'323' => 'text/h323',
			'acx' => 'application/internet-property-stream',
			'ai' => 'application/postscript',
			'aif' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'asf' => 'video/x-ms-asf',
			'asr' => 'video/x-ms-asf',
			'asx' => 'video/x-ms-asf',
			'au' => 'audio/basic',
			'avi' => 'video/x-msvideo',
			'axs' => 'application/olescript',
			'bas' => 'text/plain',
			'bcpio' => 'application/x-bcpio',
			'bin' => 'application/octet-stream',
			'bmp' => 'image/bmp',
			'c' => 'text/plain',
			'cat' => 'application/vnd.ms-pkiseccat',
			'cdf' => 'application/x-cdf',
			'cer' => 'application/x-x509-ca-cert',
			'class' => 'application/octet-stream',
			'clp' => 'application/x-msclip',
			'cmx' => 'image/x-cmx',
			'cod' => 'image/cis-cod',
			'cpio' => 'application/x-cpio',
			'crd' => 'application/x-mscardfile',
			'crl' => 'application/pkix-crl',
			'crt' => 'application/x-x509-ca-cert',
			'csh' => 'application/x-csh',
			'css' => 'text/css',
			'dcr' => 'application/x-director',
			'der' => 'application/x-x509-ca-cert',
			'dir' => 'application/x-director',
			'dll' => 'application/x-msdownload',
			'dms' => 'application/octet-stream',
			'doc' => 'application/msword',
			'dot' => 'application/msword',
			'dvi' => 'application/x-dvi',
			'dxr' => 'application/x-director',
			'eps' => 'application/postscript',
			'etx' => 'text/x-setext',
			'evy' => 'application/envoy',
			'exe' => 'application/octet-stream',
			'fif' => 'application/fractals',
			'flr' => 'x-world/x-vrml',
			'gif' => 'image/gif',
			'gtar' => 'application/x-gtar',
			'gz' => 'application/x-gzip',
			'h' => 'text/plain',
			'hdf' => 'application/x-hdf',
			'hlp' => 'application/winhlp',
			'hqx' => 'application/mac-binhex40',
			'hta' => 'application/hta',
			'htc' => 'text/x-component',
			'htm' => 'text/html',
			'html' => 'text/html',
			'htt' => 'text/webviewhtml',
			'ico' => 'image/x-icon',
			'ief' => 'image/ief',
			'iii' => 'application/x-iphone',
			'ins' => 'application/x-internet-signup',
			'isp' => 'application/x-internet-signup',
			'jfif' => 'image/pipeg',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'js' => 'application/x-javascript',
			'latex' => 'application/x-latex',
			'lha' => 'application/octet-stream',
			'lsf' => 'video/x-la-asf',
			'lsx' => 'video/x-la-asf',
			'lzh' => 'application/octet-stream',
			'm13' => 'application/x-msmediaview',
			'm14' => 'application/x-msmediaview',
			'm3u' => 'audio/x-mpegurl',
			'man' => 'application/x-troff-man',
			'mdb' => 'application/x-msaccess',
			'me' => 'application/x-troff-me',
			'mht' => 'message/rfc822',
			'mhtml' => 'message/rfc822',
			'mid' => 'audio/mid',
			'mny' => 'application/x-msmoney',
			'mov' => 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'mp2' => 'video/mpeg',
			'mp3' => 'audio/mpeg',
			'mpa' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpp' => 'application/vnd.ms-project',
			'mpv2' => 'video/mpeg',
			'ms' => 'application/x-troff-ms',
			'mvb' => 'application/x-msmediaview',
			'nws' => 'message/rfc822',
			'oda' => 'application/oda',
			'p10' => 'application/pkcs10',
			'p12' => 'application/x-pkcs12',
			'p7b' => 'application/x-pkcs7-certificates',
			'p7c' => 'application/x-pkcs7-mime',
			'p7m' => 'application/x-pkcs7-mime',
			'p7r' => 'application/x-pkcs7-certreqresp',
			'p7s' => 'application/x-pkcs7-signature',
			'pbm' => 'image/x-portable-bitmap',
			'pdf' => 'application/pdf',
			'pfx' => 'application/x-pkcs12',
			'pgm' => 'image/x-portable-graymap',
			'pko' => 'application/ynd.ms-pkipko',
			'pma' => 'application/x-perfmon',
			'pmc' => 'application/x-perfmon',
			'pml' => 'application/x-perfmon',
			'pmr' => 'application/x-perfmon',
			'pmw' => 'application/x-perfmon',
			'pnm' => 'image/x-portable-anymap',
			'pot,' => 'application/vnd.ms-powerpoint',
			'ppm' => 'image/x-portable-pixmap',
			'pps' => 'application/vnd.ms-powerpoint',
			'ppt' => 'application/vnd.ms-powerpoint',
			'prf' => 'application/pics-rules',
			'ps' => 'application/postscript',
			'pub' => 'application/x-mspublisher',
			'qt' => 'video/quicktime',
			'ra' => 'audio/x-pn-realaudio',
			'ram' => 'audio/x-pn-realaudio',
			'ras' => 'image/x-cmu-raster',
			'rgb' => 'image/x-rgb',
			'rmi' => 'audio/mid',
			'roff' => 'application/x-troff',
			'rtf' => 'application/rtf',
			'rtx' => 'text/richtext',
			'scd' => 'application/x-msschedule',
			'sct' => 'text/scriptlet',
			'setpay' => 'application/set-payment-initiation',
			'setreg' => 'application/set-registration-initiation',
			'sh' => 'application/x-sh',
			'shar' => 'application/x-shar',
			'sit' => 'application/x-stuffit',
			'snd' => 'audio/basic',
			'spc' => 'application/x-pkcs7-certificates',
			'spl' => 'application/futuresplash',
			'src' => 'application/x-wais-source',
			'sst' => 'application/vnd.ms-pkicertstore',
			'stl' => 'application/vnd.ms-pkistl',
			'stm' => 'text/html',
			'svg' => 'image/svg+xml',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc' => 'application/x-sv4crc',
			'swf' => 'application/x-shockwave-flash',
			't' => 'application/x-troff',
			'tar' => 'application/x-tar',
			'tcl' => 'application/x-tcl',
			'tex' => 'application/x-tex',
			'texi' => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tgz' => 'application/x-compressed',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'tr' => 'application/x-troff',
			'trm' => 'application/x-msterminal',
			'tsv' => 'text/tab-separated-values',
			'txt' => 'text/plain',
			'uls' => 'text/iuls',
			'ustar' => 'application/x-ustar',
			'vcf' => 'text/x-vcard',
			'vrml' => 'x-world/x-vrml',
			'wav' => 'audio/x-wav',
			'wcm' => 'application/vnd.ms-works',
			'wdb' => 'application/vnd.ms-works',
			'wks' => 'application/vnd.ms-works',
			'wmf' => 'application/x-msmetafile',
			'wps' => 'application/vnd.ms-works',
			'wri' => 'application/x-mswrite',
			'wrl' => 'x-world/x-vrml',
			'wrz' => 'x-world/x-vrml',
			'xaf' => 'x-world/x-vrml',
			'xbm' => 'image/x-xbitmap',
			'xla' => 'application/vnd.ms-excel',
			'xlc' => 'application/vnd.ms-excel',
			'xlm' => 'application/vnd.ms-excel',
			'xls' => 'application/vnd.ms-excel',
			'xlt' => 'application/vnd.ms-excel',
			'xlw' => 'application/vnd.ms-excel',
			'xof' => 'x-world/x-vrml',
			'xpm' => 'image/x-xpixmap',
			'xwd' => 'image/x-xwindowdump',
			'z' => 'application/x-compress',
			'zip' => 'application/zip',
	);
	/**
	 * mime - > extensiones
	 * @link http://www.w3schools.com/media/media_mimeref.asp
	 * @var array
	 */
	private $_extensions = array(
			'application/envoy' => 'evy',
			'application/fractals' => 'fif',
			'application/futuresplash' => 'spl',
			'application/hta' => 'hta',
			'application/internet-property-stream' => 'acx',
			'application/mac-binhex40' => 'hqx',
			'application/msword' => 'doc',
			'application/msword' => 'dot',
			'application/octet-stream' => '*',
			'application/octet-stream' => 'bin',
			'application/octet-stream' => 'class',
			'application/octet-stream' => 'dms',
			'application/octet-stream' => 'exe',
			'application/octet-stream' => 'lha',
			'application/octet-stream' => 'lzh',
			'application/oda' => 'oda',
			'application/olescript' => 'axs',
			'application/pdf' => 'pdf',
			'application/pics-rules' => 'prf',
			'application/pkcs10' => 'p10',
			'application/pkix-crl' => 'crl',
			'application/postscript' => 'ai',
			'application/postscript' => 'eps',
			'application/postscript' => 'ps',
			'application/rtf' => 'rtf',
			'application/set-payment-initiation' => 'setpay',
			'application/set-registration-initiation' => 'setreg',
			'application/vnd.ms-excel' => 'xla',
			'application/vnd.ms-excel' => 'xlc',
			'application/vnd.ms-excel' => 'xlm',
			'application/vnd.ms-excel' => 'xls',
			'application/vnd.ms-excel' => 'xlt',
			'application/vnd.ms-excel' => 'xlw',
			'application/vnd.ms-outlook' => 'msg',
			'application/vnd.ms-pkicertstore' => 'sst',
			'application/vnd.ms-pkiseccat' => 'cat',
			'application/vnd.ms-pkistl' => 'stl',
			'application/vnd.ms-powerpoint' => 'pot',
			'application/vnd.ms-powerpoint' => 'pps',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/vnd.ms-project' => 'mpp',
			'application/vnd.ms-works' => 'wcm',
			'application/vnd.ms-works' => 'wdb',
			'application/vnd.ms-works' => 'wks',
			'application/vnd.ms-works' => 'wps',
			'application/winhlp' => 'hlp',
			'application/x-bcpio' => 'bcpio',
			'application/x-cdf' => 'cdf',
			'application/x-compress' => 'z',
			'application/x-compressed' => 'tgz',
			'application/x-cpio' => 'cpio',
			'application/x-csh' => 'csh',
			'application/x-director' => 'dcr',
			'application/x-director' => 'dir',
			'application/x-director' => 'dxr',
			'application/x-dvi' => 'dvi',
			'application/x-gtar' => 'gtar',
			'application/x-gzip' => 'gz',
			'application/x-hdf' => 'hdf',
			'application/x-internet-signup' => 'ins',
			'application/x-internet-signup' => 'isp',
			'application/x-iphone' => 'iii',
			'application/x-javascript' => 'js',
			'application/x-latex' => 'latex',
			'application/x-msaccess' => 'mdb',
			'application/x-mscardfile' => 'crd',
			'application/x-msclip' => 'clp',
			'application/x-msdownload' => 'dll',
			'application/x-msmediaview' => 'm13',
			'application/x-msmediaview' => 'm14',
			'application/x-msmediaview' => 'mvb',
			'application/x-msmetafile' => 'wmf',
			'application/x-msmoney' => 'mny',
			'application/x-mspublisher' => 'pub',
			'application/x-msschedule' => 'scd',
			'application/x-msterminal' => 'trm',
			'application/x-mswrite' => 'wri',
			'application/x-netcdf' => 'cdf',
			'application/x-netcdf' => 'nc',
			'application/x-perfmon' => 'pma',
			'application/x-perfmon' => 'pmc',
			'application/x-perfmon' => 'pml',
			'application/x-perfmon' => 'pmr',
			'application/x-perfmon' => 'pmw',
			'application/x-pkcs12' => 'p12',
			'application/x-pkcs12' => 'pfx',
			'application/x-pkcs7-certificates' => 'p7b',
			'application/x-pkcs7-certificates' => 'spc',
			'application/x-pkcs7-certreqresp' => 'p7r',
			'application/x-pkcs7-mime' => 'p7c',
			'application/x-pkcs7-mime' => 'p7m',
			'application/x-pkcs7-signature' => 'p7s',
			'application/x-sh' => 'sh',
			'application/x-shar' => 'shar',
			'application/x-shockwave-flash' => 'swf',
			'application/x-stuffit' => 'sit',
			'application/x-sv4cpio' => 'sv4cpio',
			'application/x-sv4crc' => 'sv4crc',
			'application/x-tar' => 'tar',
			'application/x-tcl' => 'tcl',
			'application/x-tex' => 'tex',
			'application/x-texinfo' => 'texi',
			'application/x-texinfo' => 'texinfo',
			'application/x-troff' => 'roff',
			'application/x-troff' => 't',
			'application/x-troff' => 'tr',
			'application/x-troff-man' => 'man',
			'application/x-troff-me' => 'me',
			'application/x-troff-ms' => 'ms',
			'application/x-ustar' => 'ustar',
			'application/x-wais-source' => 'src',
			'application/x-x509-ca-cert' => 'cer',
			'application/x-x509-ca-cert' => 'crt',
			'application/x-x509-ca-cert' => 'der',
			'application/ynd.ms-pkipko' => 'pko',
			'application/zip' => 'zip',
			'audio/basic' => 'au',
			'audio/basic' => 'snd',
			'audio/mid' => 'mid',
			'audio/mid' => 'rmi',
			'audio/mpeg' => 'mp3',
			'audio/x-aiff' => 'aif',
			'audio/x-aiff' => 'aifc',
			'audio/x-aiff' => 'aiff',
			'audio/x-mpegurl' => 'm3u',
			'audio/x-pn-realaudio' => 'ra',
			'audio/x-pn-realaudio' => 'ram',
			'audio/x-wav' => 'wav',
			'image/bmp' => 'bmp',
			'image/cis-cod' => 'cod',
			'image/gif' => 'gif',
			'image/ief' => 'ief',
			'image/jpeg' => 'jpe',
			'image/jpeg' => 'jpeg',
			'image/jpeg' => 'jpg',
			'image/pipeg' => 'jfif',
			'image/png' => 'png',
			'image/svg+xml' => 'svg',
			'image/tiff' => 'tif',
			'image/tiff' => 'tiff',
			'image/x-cmu-raster' => 'ras',
			'image/x-cmx' => 'cmx',
			'image/x-icon' => 'ico',
			'image/x-portable-anymap' => 'pnm',
			'image/x-portable-bitmap' => 'pbm',
			'image/x-portable-graymap' => 'pgm',
			'image/x-portable-pixmap' => 'ppm',
			'image/x-rgb' => 'rgb',
			'image/x-xbitmap' => 'xbm',
			'image/x-xpixmap' => 'xpm',
			'image/x-xwindowdump' => 'xwd',
			'message/rfc822' => 'mht',
			'message/rfc822' => 'mhtml',
			'message/rfc822' => 'nws',
			'text/css' => 'css',
			'text/h323' => '323',
			'text/html' => 'htm',
			'text/html' => 'html',
			'text/html' => 'stm',
			'text/iuls' => 'uls',
			'text/plain' => 'bas',
			'text/plain' => 'c',
			'text/plain' => 'h',
			'text/plain' => 'txt',
			'text/richtext' => 'rtx',
			'text/scriptlet' => 'sct',
			'text/tab-separated-values' => 'tsv',
			'text/webviewhtml' => 'htt',
			'text/x-component' => 'htc',
			'text/x-setext' => 'etx',
			'text/x-vcard' => 'vcf',
			'video/mpeg' => 'mp2',
			'video/mpeg' => 'mpa',
			'video/mpeg' => 'mpe',
			'video/mpeg' => 'mpeg',
			'video/mpeg' => 'mpg',
			'video/mpeg' => 'mpv2',
			'video/quicktime' => 'mov',
			'video/quicktime' => 'qt',
			'video/x-la-asf' => 'lsf',
			'video/x-la-asf' => 'lsx',
			'video/x-ms-asf' => 'asf',
			'video/x-ms-asf' => 'asr',
			'video/x-ms-asf' => 'asx',
			'video/x-msvideo' => 'avi',
			'video/x-sgi-movie' => 'movie',
			'x-world/x-vrml' => 'flr',
			'x-world/x-vrml' => 'vrml',
			'x-world/x-vrml' => 'wrl',
			'x-world/x-vrml' => 'wrz',
			'x-world/x-vrml' => 'xaf',
			'x-world/x-vrml' => 'xof',
	);

	/**
	 * Conversor de GIF a JPEG
	 * @var string
	 */
	private $convertgif;
	/**
	 * Optimizador de PNG
	 * @var string
	 */
	private $optimpng;
	/**
	 * Optimizador de JPEG
	 * @var string
	 */
	private $optimjpeg;

	/**
	 * Constructor
	 * @return SearchImages
	 */
	function __construct()
	{
		$this->obj = &get_instance();

		$this->convertgif = $this->obj->config->item('bp.images.convert.gif');
		$this->optimjpeg = $this->obj->config->item('bp.images.optim.jpeg');
		$this->optimpng = $this->obj->config->item('bp.images.optim.png');

		log_message('debug', 'SearchImages Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Añade el dominio a la URL
	 * @param string $domain Dominio base
	 * @param string $url URL
	 * @return string
	 */
	public function add_domain($domain, $url)
	{
		$check = parse_url($url);
		return (!isset($check['host'])) ? ($domain . $url) : $url;
	}

	/**
	 * Obtien el dominio de la URL
	 * @param string $url URL
	 * @return string
	 */
	public function get_domain($url)
	{
		$parts = parse_url($url);
		$path = '/';
		if (isset($data['path']))
		{
			$path = str_replace('\\', '/', pathinfo($data['path']));
		}

		return "{$parts['scheme']}://{$parts['host']}{$path}";
	}

	/**
	 * Imagenes de un archivo HTML
	 *
	 * Examina un archivo HTML en busca de sus imagenes para
	 * luego devolver su correspondiente direccion relativa.
	 *
	 * @author  fran86       <fran86@myrealbox.com>
	 * @param   string       $archivo      Path correspondiente al HTML a examinar.
	 * @param   bool         $norepetidos  Opcional para no repetir las imagenes.
	 * @return  array|false  Array con los paths relativos de las imagenes
	 *
	 */
	private function imagenesHTML($archivo, $norepetidos = TRUE)
	{
		if (filter_var($archivo, FILTER_VALIDATE_URL) === FALSE)
		{
			return $this->google($archivo);
		}

		$contenido = $this->obj->utils->get_url($archivo);
		if (isset($contenido['headers']['content_type']))
		{
			if (strpos($contenido['headers']['content_type'], 'image') !== FALSE)
			{
				return array($archivo);
			}
		}
		$domain = $this->get_domain($archivo);

		$contenido = $contenido['response'];

		if (preg_match_all('/<img([^<>]+)>/i', $contenido, $match))
		{
			foreach ($match[1] as $atributos)
			{
				if (preg_match('/src="([^"]+)"/i', $atributos, $matchpaths))
				{
					$pathimgs[] = $this->add_domain($domain, $matchpaths[1]);
				}
				elseif (preg_match('/src=([^ ]+)/i', $atributos, $matchpaths))
				{
					$pathimgs[] = $this->add_domain($domain, $matchpaths[1]);
				}
				unset($matchpaths);
			}
		}
		if (!empty($pathimgs))
		{
			if ($norepetidos)
			{
				return array_unique($pathimgs);
			}
			else
			{
				return $pathimgs;
			}
		}
		return array();
	}

	/**
	 * Búsqueda en Google Images
	 * @param string $text EAN/ISBN/Título a buscar
	 * @return array
	 */
	private function google($text)
	{
		$text = urlencode(trim($text));
		$url = "http://www.google.es/images?hl=es&source=imghp&biw=1600&bih=681&gbv=2&aq=f&aqi=&oq=&q={$text}&tbs=isch:1";
		#echo $url;
		$contenido = $this->obj->utils->get_url($url, FALSE, null, TRUE);
		$contenido = $contenido['response'];
		#echo $contenido; die();
		#var_dump($contenido); die();
		#$contenido = file_get_contents($url);
		#echo htmlspecialchars($contenido); die();
		$pathimgs = array();
		#$regex = '/src\=\"(http:\/\/[^\"]*)\"/i';
		#$regex = '/<a.href\=\"\/url\?q\=([^\"]*)\"/i';
  		if (preg_match_all('/\"\/imgres\?(.*?)\\x26/i', $contenido, $match))
        {
                foreach ($match[1] as $atributos)
                {
                        /*if (preg_match('/imgurl=(.*?)/i', $atributos, $matchpaths))
                         {
                         var_dump($matchpaths);
                         $pathimgs[] = str_replace(array(
                         '\x3d',
                         '\x26'
                         ), '', $matchpaths[1]);
                         }
                         unset($matchpaths);*/
                        $pathimgs[] = str_replace(array(
                                        'imgurl=',
                                        '\x3d',
                                        '\x26'
                        ), '', $atributos);
                }
        }		
		return $pathimgs;
	}

	/**
	 * Búsqueda en Google normal
	 * @param string $text EAN/ISBN/Título a buscar
	 * @return array
	 */
	private function google2($text)
	{
		$text = urlencode(trim($text));
		$pathimgs = array();
		// Busca dentro de los resultados
		$url = "http://www.google.es/search?hl=es&q={$text}";
		$url = "http://www.google.es/images?hl=es&source=imghp&biw=1600&bih=681&gbv=2&aq=f&aqi=&oq=&q={$text}&tbs=isch:1";
		$contenido = $this->obj->utils->get_url($url, FALSE, null, TRUE);
		#var_dump($contenido);
		$contenido = $contenido['response'];
		#echo $contenido;
		#echo htmlspecialchars($contenido); die();
		$regex = '/imgrefurl=(.*?)\&amp/i';
		#$regex = '/<a.href=\"([^"]*?)\".onmouse/i';
		if (preg_match_all($regex, $contenido, $match))
		{
			#var_dump($match); die();
			$count = 0;
			foreach ($match[1] as $link)
			{
				$images = $this->imagenesHTML($link);
				$pathimgs = array_merge($pathimgs, $images);
				++$count;
				if ($count == 4)
					break;
			}
		}
		return $pathimgs;
	}

	/**
	 * Búsqueda en Google Images
	 * @param string $text EAN/ISBN/Título a buscar
	 * @param string $method auto: el defecto, google, google2,
	 * @return array
	 */
	function search($text, $method = null)
	{
		$data = array();
		if (isset($text) && $text != '')
		{
			if (!isset($method))
				$method = 'auto';

			if ($method == 'auto')
			{
				$images = $this->imagenesHTML($text);
			}
			elseif ($method == 'google')
			{
				$images = $this->google($text);
			}
			elseif ($method == 'google2')
			{
				$images = $this->google2($text);
			}
			if (count($images) > 0)
			{
				foreach ($images as $img)
				{
					$data[] = array(
							'name' => $img,
							'url' => urldecode($img)
					);
				}
			}
		}
		#echo '<pre>'; print_r(parse_url($text)); echo '</pre>';
		#echo '<pre>'; print_r($data); echo '</pre>';

		return $data;
	}

	/**
	 * Devuelve la extensión asociada a un tipo MIME
	 * @param string $mime Tipo MIME
	 * @return string
	 */
	function get_extension($mime)
	{
		return (isset($this->_extensions[$mime])) ? $this->_extensions[$mime] : null;
	}

	/**
	 * Devuelve el tipo MIME asociado a una extensión
	 * @param string $extension Extensión
	 * @return string
	 */
	function get_mime($extension)
	{
		return (isset($this->_mimetypes[$extension])) ? $this->_mimetypes[$extension] : null;
	}

	/**
	 * Descarga el contenido de una URL
	 * @param string $url URL
	 * @return array filename => nombre del fichero, file => Path del fichero
	 * descargado, url => URL de la descarga, ext => Extensión, mimetype => Tipo MIME
	 * del archivo
	 */
	function download($url)
	{
		$res = $this->obj->utils->get_url($url);
		
		# Algunos servidores no indicarn en content_type. Vamos a descubrilo por extension
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$mime = (!empty($ext)?$this->get_mime($ext):null);
		# Algunos servidores no indicarn en content_type. Vamos a descubrilo por extension
		if (!isset($res['headers']['content_type']) && isset($mime))
			$res['headers']['content_type'] = $mime;

		if (isset($res['headers']['content_type']))
		{
			$ext = $this->get_extension($res['headers']['content_type']);
			if (isset($ext))
			{
				$this->obj->load->library('HtmlFile');
				$filename = time() . '.' . $ext;
				$file = $this->obj->htmlfile->pathfile($filename);
				file_put_contents($file, $res['response']);
				return array(
						'filename' => $filename,
						'file' => $file,
						'url' => $url,
						'ext' => $ext,
						'mimetype' => $res['headers']['content_type'],
				);
			}
		}
		return null;
	}

	/**
	 * Crea un thumb de la imagen
	 *
	 * @param string $imgfile Fichero de imagen
	 * @param string $imgthumb Fichero de thumb
	 * @param string $extension Extensión de la imagen
	 * @param string $thumbsize Tamaño del thumb
	 * @return string Nombre del archivo thumb creado
	 */
	function thumbnail($imgfile, $imgthumb, $extension, $thumbsize)
	{
		$extension = strtolower($extension);
		list($width, $height) = getimagesize($imgfile);
		$imgratio = $width / $height;
		//if ($imgratio > 1)
		{
			$newwidth = $thumbsize;
			$newheight = $thumbsize / $imgratio;
		}
		/*else
		 {
		 $newheight = $thumbsize;
		 $newwidth = $thumbsize*$imgratio;
		 }*/
		$thumb = ImageCreateTrueColor($newwidth, $newheight);
		if (($extension == 'jpg') || ($extension == 'jpeg'))
		{
			$source = imagecreatefromjpeg($imgfile);
			if (!$source)
				$source = imagecreatefromgif($imgfile);
			if (!$source)
				$source = imagecreatefrompng($imgfile);
		}
		elseif ($extension == 'gif')
		{
			$source = imagecreatefromgif($imgfile);
			if (!$source)
				$source = imagecreatefromjpeg($imgfile);
			if (!$source)
				$source = imagecreatefrompng($imgfile);
		}
		else
		{
			$source = imagecreatefrompng($imgfile);
			if (!$source)
				$source = imagecreatefromjpeg($imgfile);
			if (!$source)
				$source = imagecreatefrogif($imgfile);
		}
		if (!$source)
		{
			return $imgfile;
		}
		else
		{
			imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			imagejpeg($thumb, $imgthumb, 100);
			return $thumb;
		}
	}

	/**
	 * Convierte una imagen a JPG
	 * @param  string $fin  Fichero de entrada
	 * @param  string $fout Fichero de salida
	 * @return FALSE si error, <file> con el nombre del archivo creado
	 */
	function to_jpeg($fin, $fout = null)
	{
		$item = pathinfo($fin);
		if (empty($fout))
		{
			$fout = $item['dirname'] . DS . $item['filename'] . '.jpg';
		}
		if (strtolower($item['extension']) == 'psd') $fin .= '[0]';
		$cmd = str_replace(array('%in', '%out'), array($fin, $fout), $this->convertgif);
		$r = exec($cmd, $result);
		return $fout;
	}

	/**
	 * Optimiza la imagen JPG o PNG
	 * @param  string $fin  Fichero de entrada
	 * @return 
	 */
	function optimize($fin)
	{
		$item = pathinfo($fin);
		#var_dump($item);
		$extension =  strtolower($item['extension']);
		if ($extension == 'png')
		{
			$cmd = str_replace(array('%in', '%out'), $fin, $this->optimpng);
		}
		elseif (in_array($extension, array('jpg', 'jpeg')))
		{
			$cmd = str_replace(array('%in', '%out'), $fin, $this->optimjpeg);
		}
		$r = exec($cmd, $result);
		return $fin;
	}
}

/* End of file searchimages.php */
/* Location: ./system/libraries/searchimages.php */
