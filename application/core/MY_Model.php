<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * My_Model Class
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	core
 * @author		Alejandro López
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Índices de la definición de un modelo de datos
 * @var int
 */
define('DATA_MODEL_FIELD', 				0);
define('DATA_MODEL_REQUIRED', 			1);
define('DATA_MODEL_TYPE', 				2);
define('DATA_MODEL_DESCRIPTION', 		3);
define('DATA_MODEL_EDITOR', 			4);
define('DATA_MODEL_DEFAULT', 			5);
define('DATA_MODEL_READONLY', 			6);
define('DATA_MODEL_DEFAULT_VALUE',		7);
define('DATA_MODEL_NO_LIST',			8);
define('DATA_MODEL_GRID',				9);
define('DATA_MODEL_NO_GRID',			10);
define('DATA_MODEL_SEARCH',				11);

/**
 * Tipos de datos de los camnpos
 * @var unknown_type
 */
define('DATA_MODEL_TYPE_INT',			'int');
define('DATA_MODEL_TYPE_STRING',		'string');
define('DATA_MODEL_TYPE_DATETIME',		'datetime');
define('DATA_MODEL_TYPE_DATE',			'date');
define('DATA_MODEL_TYPE_TIME',			'time');
define('DATA_MODEL_TYPE_DOUBLE',		'double');
define('DATA_MODEL_TYPE_FLOAT',			'float');
define('DATA_MODEL_TYPE_BOOLEAN',		'bool');
define('DATA_MODEL_TYPE_BOOL',			'bool');
define('DATA_MODEL_TYPE_MONEY',			'money');
define('DATA_MODEL_TYPE_ALIAS',			'alias');

define('DATA_MODEL_EDITOR_COMBO',		'combo');
define('DATA_MODEL_EDITOR_SEARCH',		'search');
define('DATA_MODEL_EDITOR_TYPE',		0);
define('DATA_MODEL_EDITOR_PARAM1',		1);
define('DATA_MODEL_EDITOR_PARAM2',		2);

define('DATA_MODEL_DEFAULT_TYPE', 		DATA_MODEL_TYPE_STRING);
define('DATA_MODEL_DEFAULT_REQUIRED', 	FALSE);

define('DATA_MODEL_RELATION_1N', 		'1n');
define('DATA_MODEL_RELATION_11', 		'11');

define('DATA_MODEL_DELETE_FIELD', 		'delete');

/**
 * Extensión del modelo de datos con métodos de ayuda
 *
 */
class MY_Model extends CI_Model {
		/**
	 * Nombre de la tabla
	 *
	 * @var string
	 */
	protected $_tablename = '';

	/**
	 * Campo identificador en la tabla
	 *
	 * @var string
	 */
	protected $_id = '';

	/**
	 * Orden por defecto
	 * @var string
	 */
	protected $_order = null;

	/**
	 * Contador de registros afectados
	 *
	 * @var unknown_type
	 */
	protected $_count = 0;

	/**
	 * Campo de búsqueda por defecto
	 * @var array
	 */
	protected $_name = null;

	/**
	 * Modelo de datos
	 * @var array
	 */
	protected $_data_model = null;

	/**
	 * Último error
	 * @var string
	 */
	protected $_error_message = null;

	/**
	 * Indica si se audita la base de datos
	 * @var bool
	 */
	protected $_audit = FALSE;

	/**
	 * Relaciones a otras tablas
	 * @var array
	 */
	protected $_relations = null;

	/**
	 * Indica que el modelo dispone de un modelo de datos extra para las líneas (típico de documentos)
	 * @var bool
	 */
	//protected $_lines = false;

	/**
	 * Usa caché para los datos
	 * @var bool
	 */
	protected $_cache = FALSE;

	/**
	 * Alias de los campos
	 * @var array
	 */
	protected $_alias = null;
	/**
	 * Usar caché según sistema
	 * @var bool
	 */
	private $_global_cache = TRUE;
	/**
	 * Tipo de caché a usar. Por defecto la del sistema
	 * @var int
	 */
	protected $DATA_MODE_CACHE_TYPE;
	/**
	 * Tiempo en segundos de vida en la caché. Por defecto la del sistema
	 * @var int
	 */
	protected $DATA_MODE_CACHE_TIME;

	/**
	 * Acceso a la instancia. Por error se creó el método load y se carga el load del CI
	 * @var CI
	 */
	protected $obj;

	/**
	 * Permite la ejecución de triggers
	 * @var bool
	 */
	protected $triggers_enabled = TRUE;

	/**
	 * Indica que se tiene que auditar los eliminados
	 * @var boolean
	 */
	protected $_deleted = FALSE;

	/**
	 * Constructor
	 *
	 * @param string $tablename Tabla
	 * @param string $id Identificador
	 * @param string $order Campo de orden por defecto
	 * @param mixed $name nombre (string) o nombres (array) del/de los campo/s de búsqueda
	 * @param array $data_model Modol de datos
	 * @param bool $audit Utiliza campos de auditoría (cUser, dAct, etc)
	 * @param string $lines Modelo de datos que devuelve las líneas
	 * @return MY_Model
	 */
	function __construct(
			$tablename = null, 
			$id = null, 
			$order = null, 
			$name = null, 
			$data_model = null, 
			$audit = false
	)
	{
		parent::__construct();
		// Librerías
		$this->load->helper('utf8');
		$this->load->helper('formatters');
		//$this->load->library('Cache');
		//$this->DATA_MODE_CACHE_TYPE = CACHE_MEMORY;
		//$this->DATA_MODE_CACHE_TIME = PG_CACHE_NOLIMIT;
		
		$this->_tablename = $tablename;
		$this->_id = $id;
		if (isset($order))
		{
			$o = preg_split('/,/' ,$order);
			if (count($o) > 0)
			{
				foreach($o as $k => $v)
				{
					if (strpos($v, '.') === FALSE) $o[$k] = $tablename . '.' . trim($v);
				}
				$order = implode(', ', $o);
			}
			$this->_order = $order;
		}
		
		// Siempre un array
		if (is_array($name))
		{
			$this->_name = $name;
		}
		else
		{
			$this->_name = array($name);
		}
		
		if ($audit)
		{
			$this->load->library('Userauth');
			$data_model['dCreacion'] 	=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME, DATA_MODEL_READONLY => TRUE);
			$data_model['cCUser'] 		=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_STRING, DATA_MODEL_READONLY => TRUE);
			$data_model['dAct'] 		=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME, DATA_MODEL_READONLY => TRUE);
			$data_model['cAUser'] 		=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_STRING, DATA_MODEL_READONLY => TRUE);
		}
		
		//$this->_data_model = $this->_process_data_model($data_model);
		$this->_audit = $audit;
		$this->_global_cache = $this->config->item('bp.cache.models');
		
		//$this->load->database('default');

		$this->obj = get_instance();

		log_message('debug', 'MY_model ' . get_class($this) . ' Loaded');
		
	}
}

/* End of file My_Model.php */
/* Location: ./system/libraries/My_Model.php */
