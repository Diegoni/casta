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
			//*, $lines = null*/
	)
	{
		parent::__construct();
		// Librerías
		$this->load->helper('utf8');
		$this->load->helper('formatters');
		$this->load->library('Cache');
		$this->DATA_MODE_CACHE_TYPE = CACHE_MEMORY;
		$this->DATA_MODE_CACHE_TIME = PG_CACHE_NOLIMIT;

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

		//Si se audita añade al modelo los datos del usuario y fecha
		if ($audit)
		{
			$this->load->library('Userauth');
			$data_model['dCreacion'] 	=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME, DATA_MODEL_READONLY => TRUE);
			$data_model['cCUser'] 		=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_STRING, DATA_MODEL_READONLY => TRUE);
			$data_model['dAct'] 		=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME, DATA_MODEL_READONLY => TRUE);
			$data_model['cAUser'] 		=  array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_STRING, DATA_MODEL_READONLY => TRUE);
		}

		$this->_data_model = $this->_process_data_model($data_model);
		$this->_audit = $audit;
		$this->_global_cache = $this->config->item('bp.cache.models');

		$this->load->database('default');

		$this->obj = get_instance();

		log_message('debug', 'MY_model ' . get_class($this) . ' Loaded');
	}

	/**
	 * Procesa el modelo de datos añadiendo los datos por defecto
	 * @param array $data_model Modelo de datos
	 * @return array
	 */
	protected function _process_data_model($data_model)
	{
		$new = array();
		if (isset($data_model))
		{
			foreach($data_model as $k => $v)
			{
				if (!isset($v)) $v = array();
				if (!isset($v[DATA_MODEL_FIELD])) $v[DATA_MODEL_FIELD] = $k;
				if (!isset($v[DATA_MODEL_REQUIRED])) $v[DATA_MODEL_REQUIRED] = DATA_MODEL_DEFAULT_REQUIRED;
				if (!isset($v[DATA_MODEL_TYPE])) $v[DATA_MODEL_TYPE] = DATA_MODEL_DEFAULT_TYPE;
				$new[$k] = $v;
			}
		}
		return $new;
	}

	/**
	 * Aplica alias a los sort
	 * @param array $alias Alias
	 * @param string $sort Orden
	 * @return string
	 */
	protected function _fix_sort(&$sort = null, $alias = null)
	{
		if (!isset($sort)|| trim($sort) == '') return;
		if (!isset($alias) && !isset($this->_alias)) return;
		$sort = preg_split('/,/', $sort);
		foreach($sort as $k => $s)
		{
			$s = trim($s);
			$base = (strpos($s, '.')===FALSE)?($this->_tablename . '.'):'';
			$sort[$k] = (isset($alias)?((isset($alias[$s]))?$alias[$s]:$base . $s):
			((isset($this->_alias[$s]))?$this->_alias[$s][0]:$base . $s));
		}
		$sort = implode(',', $sort);
	}

	/**
	 * Asigna un mensaje de error
	 * @param string $msg Mensaje
	 */
	protected function _set_error_message($msg = null)
	{
		$this->_error_message = $msg;
	}

	/**
	 * Crea un array con el resultado del query teniendo en cuenta los límites
	 *
	 * @param CI_DB_result $query Resultado
	 * @param int $start Inicio
	 * @param int $limit Límite
	 * @return array
	 */
	protected function _get_results($query, $start = null, $limit = null)
	{
		return $this->db->get_results($query, $start, $limit);
	}

	/**
	 * Establece los límites y el orden de una consulta
	 *
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 */
	protected function _limits_sort($start = null, $limit = null, $sort = null, $dir = null)
	{
		if (!$start) $start = 0;
		if ($limit) $this->db->limit($limit, $start);
		if ($sort)
		{
			$sort = $this->_apply_alias($sort);
			if (isset($this->_data_model[$sort])) $sort = $this->_tablename . '.' . trim($sort);
			$this->db->order_by($sort, $dir);
		}
		else if ($this->_order)
		{
			$sort = $this->_apply_alias($this->_order);
			$this->db->order_by($this->_order);
		}
	}

	/**
	 * Limpia la caché de este modelo de datos
	 */
	function clear_cache()
	{
		if ($this->use_cache())
		{
			#echo 'Borrando CACHE<br/>';
			$this->cache->delete($this->_tablename, null, $this->DATA_MODE_CACHE_TYPE);
			log_message('info', 'MY_Model -> ' . $this->_tablename . ' -> cache delete');
		}
	}

	/**
	 * Añade los alias de la tablas a los campos
	 * @param string $field Campo
	 * @return string, Campo con tabla
	 */
	protected function _complete_field($field)
	{		
		if (isset($this->_alias) && (isset($this->_alias[$field])))
			return $this->_alias[$field][0];
		if (strpos($field, '.')!==FALSE) return $field;
		return $this->_tablename . '.' . $field;
	}

	/**
	 * Convierte el texto de búsqueda en algo reconocible por la máquina
	 * @param string $where Filtro de búsquda
	 */
	function parse_where($where)
	{
		$this->load->helper('parsersearch');
		if (is_array($where))
		{
			$fields = $where;
		}
		else
		{
			parse_str($where, $fields);
		}
		#var_dump($fields);
		$this->onParseWhere($fields);
		#var_dump($fields);
		$filter = array();
		foreach($fields as $field => $value)
		{
			#var_dump($value);
			#$value = $this->db->escape_str($value);
			if (is_numeric($field))
			{
				$w = $value;
			}
			else
			{
				$value = $this->db->escape_str($value);
				$w = boolean_sql_where($value, $this->_complete_field($field), $this->_get_type_parser($field));
			}
			if (isset($w) && ($w != ''))
			{
				$filter[] = '(' . $w .')';
			}
		}
		#var_dump($filter); 
		#echo $r;
		#die();
		return (isset($filter))?implode(' AND ', $filter):null;
	}

	/**
	 * Parsea las palabras de búsqueda, las limpia y crea los and y or
	 *
	 * @param string $search_str Palabras a buscar
	 * @return array
	 */

	protected function _parse_search_string($search_str)
	{
		$search_str = trim(strtolower($search_str));

		// Break up $search_str on whitespace; quoted string will be reconstructed later
		$pieces = preg_split('/[[:space:]]+/', $search_str);
		$objects = array();
		$tmpstring = '';
		$flag = '';

		for ($k=0; $k<count($pieces); $k++)
		{
			while (substr($pieces[$k], 0, 1) == '(')
			{
				$objects[] = '(';
				if (strlen($pieces[$k]) > 1) {
					$pieces[$k] = substr($pieces[$k], 1);
				} else {
					$pieces[$k] = '';
				}
			}

			$post_objects = array();

			while (substr($pieces[$k], -1) == ')')  {
				$post_objects[] = ')';
				if (strlen($pieces[$k]) > 1) {
					$pieces[$k] = substr($pieces[$k], 0, -1);
				} else {
					$pieces[$k] = '';
				}
			}

			// Check individual words
			if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') )
			{
				$objects[] = trim($pieces[$k]);

				for ($j=0; $j<count($post_objects); $j++) {
					$objects[] = $post_objects[$j];
				}
			}
			else
			{
				/* This means that the $piece is either the beginning or the end of a string.
				 So, we'll slurp up the $pieces and stick them together until we get to the
				 end of the string or run out of pieces.
				 */

				// Add this word to the $tmpstring, starting the $tmpstring
				$tmpstring = trim(str_replace('"', ' ', $pieces[$k]));

				// Check for one possible exception to the rule. That there is a single quoted word.
				if (substr($pieces[$k], -1 ) == '"') {
					// Turn the flag off for future iterations
					$flag = 'off';

					$objects[] = trim($pieces[$k]);

					for ($j=0; $j<count($post_objects); $j++) {
						$objects[] = $post_objects[$j];
					}

					unset($tmpstring);

					// Stop looking for the end of the string and move onto the next word.
					continue;
				}

				// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
				$flag = 'on';

				// Move on to the next word
				$k++;

				// Keep reading until the end of the string as long as the $flag is on

				while ( ($flag == 'on') && ($k < count($pieces)) )
				{
					while (substr($pieces[$k], -1) == ')') {
						$post_objects[] = ')';
						if (strlen($pieces[$k]) > 1) {
							$pieces[$k] = substr($pieces[$k], 0, -1);
						} else {
							$pieces[$k] = '';
						}
					}

					// If the word doesn't end in double quotes, append it to the $tmpstring.
					if (substr($pieces[$k], -1) != '"')
					{
						// Tack this word onto the current string entity
						$tmpstring .= ' ' . $pieces[$k];

						// Move on to the next word
						$k++;
						continue;
					}
					else
					{
						/* If the $piece ends in double quotes, strip the double quotes, tack the
						 $piece onto the tail of the string, push the $tmpstring onto the $haves,
						 kill the $tmpstring, turn the $flag "off", and return.
						 */
						$tmpstring .= ' ' . trim(str_replace('"', ' ', $pieces[$k]));

						// Push the $tmpstring onto the array of stuff to search for
						$objects[] = trim($tmpstring);

						for ($j=0; $j<count($post_objects); $j++) {
							$objects[] = $post_objects[$j];
						}

						unset($tmpstring);

						// Turn off the flag to exit the loop
						$flag = 'off';
					}
				}
			}
		}

		// add default logical operators if needed
		$temp = array();
		for($i=0; $i<(count($objects)-1); $i++)
		{
			$temp[] = $objects[$i];
			if ( ($objects[$i] != 'and') && ($objects[$i] != 'or') &&
			($objects[$i] != '(') && ($objects[$i+1] != 'and') &&
			($objects[$i+1] != 'or') &&	($objects[$i+1] != ')') )
			{
				$temp[] = SEARCH_DEFAULT_OPERATOR;
			}
		}
		$temp[] = $objects[$i];
		$objects = $temp;

		$keyword_count = 0;
		$operator_count = 0;
		$balance = 0;
		for($i=0; $i<count($objects); $i++)
		{
			if ($objects[$i] == '(') $balance --;
			if ($objects[$i] == ')') $balance ++;
			if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') )
			{
				$operator_count ++;
			}
			elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') )
			{
				$keyword_count ++;
			}
		}

		if ( ($operator_count < $keyword_count) && ($balance == 0) )
		{
			return $objects;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Crea la instrucción LIKE de un campo por palabras
	 *
	 * @param string $keywords Palabras a buscar
	 * @param string $field Campo
	 * @return string
	 */
	protected function _create_like($keywords, $field)
	{
		$this->load->helper('parsersearch');
		return boolean_sql_where($keywords, $field, 'string');
	}

	/**
	 * Devuelve el tipo de datos del campo
	 * @param string $field Campo
	 * @return int: tipo, null si no existe el campo en la definición
	 */
	protected function _get_type($field)
	{
		$field = trim($field);

		if (isset($this->_data_model) && isset($this->_data_model[$field]))
		{
			$v = $this->_data_model[$field];
			if (isset($v[DATA_MODEL_TYPE]))
			{
				return $v[DATA_MODEL_TYPE];
			}

		}
		if (isset($this->_alias))
		{
			return ((isset($this->_alias[$field]))?$this->_alias[$field][1]:DATA_MODEL_TYPE_STRING);
		}
		return DATA_MODEL_TYPE_STRING;
	}

	/**
	 * Devuelve el tipo de datos del campo
	 * @param string $field Campo
	 * @return string (number, string, date)
	 */
	protected function _get_type_parser($field)
	{
		$types = array(
		DATA_MODEL_TYPE_INT			=> 'number',
		DATA_MODEL_TYPE_STRING		=> 'string',
		DATA_MODEL_TYPE_DATETIME	=> 'date',
		DATA_MODEL_TYPE_DATE		=> 'date',
		DATA_MODEL_TYPE_TIME		=> 'date',
		DATA_MODEL_TYPE_DOUBLE		=> 'number',
		DATA_MODEL_TYPE_FLOAT		=> 'number',
		DATA_MODEL_TYPE_BOOLEAN		=> 'number',
		DATA_MODEL_TYPE_BOOL		=> 'number',
		DATA_MODEL_TYPE_MONEY		=> 'number',
		DATA_MODEL_TYPE_ALIAS		=> 'string',
		);
		#var_dump($field);
		return ($field == $this->get_id() || $field == 'id')?'number':$types[$this->_get_type($field)];
	}

	/**
	 * Indica si un tipo es numérico
	 * @param int $type Tipo de datos
	 * @return bool
	 */
	protected function _is_numeric($type)
	{
		$numeric = array(
		DATA_MODEL_TYPE_INT,
		DATA_MODEL_TYPE_DOUBLE,
		DATA_MODEL_TYPE_BOOLEAN,
		DATA_MODEL_TYPE_BOOL,
		DATA_MODEL_TYPE_FLOAT,
		DATA_MODEL_TYPE_MONEY);

		return in_array($type, $numeric);
	}

	/**
	 * Prepara un campo date para ser leído de la base de datos
	 * @param string $field Campo
	 * @param string $alias Alias
	 * @return string
	 */
	public function _date_field($field, $alias = null)
	{
		return $this->db->date_field($field, $alias);
	}

	/**
	 * Obtiene los campos del modelo de datos
	 * @param bool $all TRUE: Muestra hasta los campos que no se listan
	 * @return string Los campos en formato SQL
	 */
	protected function _get_fields($all = TRUE)
	{
		// si hay modelo comprueba
		$fields = array();
		if (isset($this->_id))
		{
			$fields[] = $this->_tablename . '.' . $this->_id;
			$fields[] = $this->_tablename . '.' . $this->_id. ' id';
		}
		//$fields[] = $this->get_text() . ' text';
		if (isset($this->_data_model))
		{
			foreach($this->_data_model as $k => $v)
			{
				if ((isset($v[DATA_MODEL_TYPE]) && $v[DATA_MODEL_TYPE] != DATA_MODEL_TYPE_ALIAS)||(!isset($v[DATA_MODEL_TYPE])))
				{
					$add = (isset($v[DATA_MODEL_NO_LIST])?FALSE:TRUE) || $all;
	
					// Comprueba si cumple el tipo y lo formatea si es necesario
					if (isset($v[DATA_MODEL_TYPE]) && $add)
					{
						switch($v[DATA_MODEL_TYPE]) {
							case DATA_MODEL_TYPE_DATE:
							case DATA_MODEL_TYPE_DATETIME:
								$fields[] = $this->_date_field($this->_tablename. '.' . $v[DATA_MODEL_FIELD], $v[DATA_MODEL_FIELD]);
								break;
							default:
								$fields[] = $this->_tablename . '.' . $v[DATA_MODEL_FIELD];
						}
					}
				}
			}
			/*$fields= implode(',', $fields);*/
		}
		else
		{
			$fields = "{$this->_tablename}.*";
		}
		#die($fields);
		return $fields;
	}

	/**
	 * Convierte un valor a entero para ser procesado por la base de datos
	 * @param mixed $value Valor a convertir
	 * @return int, FALSE si no se puede convertir
	 */
	protected function _toint($value)
	{
		return format_toint($value);
	}

	/**
	 * Convierte un valor a punto flotante para ser procesado por la base de datos
	 * @param mixed $value Valor a convertir
	 * @return float, FALSE si no se puede convertir
	 */
	protected function _tofloat($value)
	{
		return format_tofloat($value);
	}

	/**
	 * Convierte un valor a boleano para ser procesado por la base de datos
	 * @param mixed $value Valor a convertir
	 * @return 0, 1, FALSE si no se puede convertir
	 */
	protected function _tobool($value)
	{
		return format_tobool($value);
	}

	/**
	 * Convierte un valor a fecha para ser procesado por la base de datos
	 * @param mixed $value Valor a convertir
	 * @return int, FALSE si no se puede convertir
	 */
	protected function _todate($value)
	{
		return format_todate($value);
	}

	/**
	 * Filtra los campos a insertar y añade los defalut, si están
	 * @param array $data Campos
	 * @param bool $insert TRUE: es para un INSERT, FALSE: para un UPDATE
	 * @return array FALSE: fallan los valores
	 */
	protected function _filtra_datos($data, $insert = TRUE, $preformat = TRUE)
	{
		// si hay modelo comprueba
		#echo "_filtra_datos INSERT "; var_dump($insert); echo $this->_tablename;
		if (isset($this->_data_model))
		{
			$format = TRUE;
			$check = TRUE;
			$checks = array();
			$formats = array();
			$model = $this->_data_model;
			if ($insert)
			{
				$model[$this->_id] = array(DATA_MODEL_FIELD => $this->_id, DATA_MODEL_REQUIRED => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT);
			}
			foreach($model as $k => $v)
			{
				$value = null;
				//Copia el valor
				if (array_key_exists($v[DATA_MODEL_FIELD], $data))
				{
					$value = $data[$v[DATA_MODEL_FIELD]];
					if (isset($value))
					{
						#echo $v[DATA_MODEL_FIELD] . ' ';
						// Comprueba si cumple el tipo y lo formatea si es necesario
						if (isset($v[DATA_MODEL_TYPE]))
						{
							switch($v[DATA_MODEL_TYPE]) {
								case DATA_MODEL_TYPE_INT:
									$value = $this->_toint($value);
									if ($value===FALSE)
									{
										$formats[] = $k;
										$format = FALSE;
									}
									break;
								case DATA_MODEL_TYPE_FLOAT:
								case DATA_MODEL_TYPE_DOUBLE:
								case DATA_MODEL_TYPE_MONEY:
									$value = $this->_tofloat($value);
									if ($value===FALSE)
									{
										$formats[] = $k;
										$format = FALSE;
									}
									break;
								case DATA_MODEL_TYPE_BOOL:
									$value = $this->_tobool($value);
									if ($value===FALSE)
									{
										$formats[] = $k;
										$format = FALSE;
									}
									break;
								case DATA_MODEL_TYPE_DATE:
								case DATA_MODEL_TYPE_DATETIME:
									if ($preformat)
									{
										$value = $this->_todate($value);
										if ($value===FALSE)
										{
											$formats[] = $k;
											$format = FALSE;
										}
									}
									break;
								case DATA_MODEL_TYPE_STRING:
									$value = $this->db->decode($value); # string_decode
									break;
							}
						}
					}
				}
				//Asigna valor por defecto si es insert
				if (!isset($value))
				{
					if(isset($v[DATA_MODEL_DEFAULT_VALUE]) && $insert)
					{
						$value = $v[DATA_MODEL_DEFAULT_VALUE];
					}
				}

				//if (!$format) break;

				// Comprueba que el campo sea obligatorio, si insert
				if (!isset($value) && $v[DATA_MODEL_REQUIRED] === TRUE && $insert)
				{
					$check = FALSE;
					#echo 'FAIL ' . $k;
					$checks[] = $k;
					//break;
				}
				if (array_key_exists($v[DATA_MODEL_FIELD], $data) || isset($value))
				{
					$data2[$v[DATA_MODEL_FIELD]] = $value;
				}
			}
			// Añade el modelo de datos para el Id
			if (!$check && $insert)
			{
				// Error faltan datos
				foreach($checks as $v => $k)
				{
					$checks[$v] = $this->lang->line($k);
				}
				$ch = '"' . implode(',', $checks) . '"';
				$this->_set_error_message(sprintf($this->lang->line('mensaje_faltan_datos_fields'), $ch));
				return FALSE;
			}
			if (!$format)
			{
				// Error de formato
				foreach($formats as $v => $k)
				{
					$formats[$v] = $this->lang->line($k);
				}
				$ch = '"' . implode(',', $formats) . '"';
				$this->_set_error_message(sprintf($this->lang->line('formato_datos_erroneo_fields'), $ch));
				#echo 'formato erroneo';
				return FALSE;
			}
		}
		else
		{
			$data2  = $data;
		}
		return $data2;
	}

	/**
	 * Indica si se debe usar cache o no
	 * @param bool $use TRUE: se usa caché, FALSE: no se usa caché
	 * @return bool Indica si se usa o no la cache
	 */
	function use_cache($use = null)
	{
		if (isset($use))
		{
			if (is_bool($use)) $this->_cache = $use;
			if (is_numeric($use)) $this->DATA_MODE_CACHE_TYPE = $use;
		}

		return ($this->_cache && $this->_global_cache);
	}


	/**
	 * Inserta un registro
	 *
	 * @param array $data Datos
	 * @return int -1, ha habido error, >0 Id del registro
	 */
	function insert($data)
	{
		$this->_set_error_message();

		if (isset($data[DATA_MODEL_DELETE_FIELD]) && (is_numeric($data[DATA_MODEL_DELETE_FIELD])))
		{
			return $this->delete($data[DATA_MODEL_DELETE_FIELD]);
		}

		$this->db->flush_cache();
		#echo '<pre>insert de ' . get_class($this).'</pre>';

		// Comprueba los datos
		#print '<pre>'; print_r($data); print '</pre>';
		$data2 = array();
		if (($data2 = $this->_filtra_datos($data, TRUE, FALSE)) === FALSE)
		{
			return -1;
		}
		foreach($data2 as $k => $v)
		{
			if (!isset($v)) unset($data2[$k]);
		}
		#print '<pre>'; print_r($data2); print '</pre>';
		// TRIGGER BEFORE
		if ($this->triggers_status() && !$this->onBeforeInsert($data2)) return -1;
		if (($data2 = $this->_filtra_datos($data2, TRUE)) === FALSE)
		{
			return -1;
		}
		#print '<pre>'; print_r($data2); print '</pre>';
		#die();

		//Inserta el registro
		$this->db->trans_begin();
		if (!$this->db->insert($this->_tablename, $data2))
		{
			$this->_set_error_message($this->db->_error_message());
			$this->db->trans_rollback();
			return FALSE;
		}

		//print 'N ' . $this->db->_error_number() . ' S.' . $this->db->_error_message();

		$id = $this->db->insert_id();

		// Copia las relaciones
		foreach($data as $k => $v)
		{
			if (!isset($data2[$k])) $data2[$k] = $v;
		}

		// TRIGGER AFTER
		if ($this->triggers_status() && !$this->onAfterInsert($id, $data2))
		{
			$this->db->trans_rollback();
			return -1;
		}

		$this->db->trans_commit();
		$this->clear_cache();

		return $id;
	}

	/**
	 * Actualiza un registros
	 *
	 * @param int $id Id del registro a actualizar
	 * @param array $data Datos
	 */
	function update($id, $data)
	{
		$this->_set_error_message();
		#echo '<pre>Update de ' . get_class($this) . '</pre>';

		if (count($data) > 0)
		{
			if (isset($data[DATA_MODEL_DELETE_FIELD]) && (is_numeric($data[DATA_MODEL_DELETE_FIELD])))
			{
				#echo 'es un borrar';
				$res = $this->delete($data[DATA_MODEL_DELETE_FIELD]);
				#var_dump($res);
				return $res;
			}
			else
			{
				#echo 'es un actualizar';
				// Actualiza el registro
				$this->db->flush_cache();
				$this->db->trans_begin();

				// TRIGGER BEFORE
				if ($this->triggers_status()  && !$this->onBeforeUpdate($id, $data))
				{
					$this->db->trans_rollback();
					return FALSE;
				}


				// Comprueba los datos
				if (($data2 = $this->_filtra_datos($data, FALSE)) === FALSE)
				{
					return FALSE;
				}
				#var_dump($data, $data2); die();

				$this->db->flush_cache();
				$this->db->where($this->_id, (int)$id);
				if (!$this->db->update($this->_tablename, $data2))
				{
					$this->_set_error_message($this->db->_error_message());
					$this->db->trans_rollback();
					return FALSE;
				}

				#print 'UPDATE ' . $id;
				// TRIGGER AFTER
				foreach($data as $k => $v)
				{
					if (!isset($data2[$k])) $data2[$k] = $v;
				}
				if ($this->triggers_status()  && !$this->onAfterUpdate($id, $data2))
				{
					$this->db->trans_rollback();
					return FALSE;
				}
				$this->db->trans_commit();
				$this->clear_cache();
			}
		}
		return TRUE;
	}

	/**
	 * Elimina un registro por Id
	 *
	 * @param int $id identificador
	 * @return bool TRUE: hay error, FALSE hay un error
	 */
	function delete($id = null, $data = null)
	{
		$this->_set_error_message();
		$this->db->flush_cache();
		$this->db->trans_begin();

		#echo '<pre>Borrando ' . get_class($this) . '</pre>';
		// TRIGGER BEFORE
		if ($this->triggers_status()  && !$this->onBeforeDelete($id)) 
			return FALSE;

		if (isset($data) && isset($this->_relations))
		{
			// Borra datos de las relaciones
			foreach($this->_relations as $k => $rel)
			{
				if (isset($data[$k]))
				{
					#print 'DELETE DE ' . $k;
					$rel = $this->_relations[$k];
					if (isset($rel['ref']) && (isset($rel['fk'])))
					{
						$type = isset($rel['type'])?$rel['type']:DATA_MODEL_RELATION_11;
						switch ($type)
						{
							case DATA_MODEL_RELATION_1N:
								$tablename = preg_split('/\//', $rel['ref']);
								$tablename = $tablename[count($tablename) - 1];
								$obj = get_instance();
								$obj->load->model($rel['ref']);
								foreach($data[$k] as $linea)
								{
									if (isset($linea[$obj->$tablename->get_id()]))
									{
										#echo 'DELETE  RELATION';
										if (!$obj->$tablename->delete($linea[$obj->$tablename->get_id()]))
										{
											$this->db->trans_rollback();
											return FALSE;
										}
									}
								}
								break;
						}
					}
				}
			}
		}
		else
		{
			$this->db->flush_cache();
			#$this->db->where(isset($id)?"{$this->_id}={$id}":'1=1');
			$this->db->delete($this->_tablename, isset($id)?"{$this->_id}={$id}":'1=1');
			if ($this->_check_error())
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// TRIGGER AFTER
		if ($this->triggers_status() && !$this->onAfterDelete($id))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		$this->clear_cache();
		return TRUE;
	}

	/**
	 * Elimina un registro por filtro
	 *
	 * @param string $where Where de la eliminación
	 * @return bool TRUE: hay error, FALSE hay un error
	 */
	function delete_by($where)
	{
		#echo '<pre>delete_by ' . get_class($this) . '</pre>';

		$this->_set_error_message();
		// TRIGGER BEFORE
		if ($this->triggers_status() && !$this->onBeforeDeleteBy($where)) return FALSE;

		$data = $this->get(null, null, null, null, $where);
		$count = 0;
		foreach ($data as $value) 
		{
			if (!$this->delete($value[$this->_id]))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			++$count;
		}

		$this->_count = $count;

		/*$this->db->flush_cache();
		$this->db->where($where);
		$this->db->delete($this->_tablename);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}*/

		$this->clear_cache();
		if ($this->triggers_status() && !$this->onAfterDeleteBy($where)) return FALSE;

		return TRUE;
	}

	protected function _apply_alias($where)
	{
		#var_dump($this->_alias);
		#$where = str_replace($tablename . '.' .'id', '__TEMPID__', $where);
		if (isset($this->_alias))
		{
			if (is_array($where))
			{
				foreach($where as $k => $v)
				{
					$where[$k] = $this->_apply_alias($v);
				}
			}
			else
			{
				foreach($this->_alias as $k => $alias)
				{
					$where = str_replace($k, $alias[0], $where);
				}
			}
		}
		// Añade la tabla a cada campo
		if (isset($this->_data_model))
		{
			$model = $this->_data_model;
			$model[$this->_id] = array(DATA_MODEL_FIELD => $this->_id, DATA_MODEL_REQUIRED => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT);
			$t = $this->get_tablename();
			foreach($model as $k => $v)
			{
				$field = $v[DATA_MODEL_FIELD];
				$where = str_replace('.' . $field, '__TEMP__', $where);
				$where = str_replace($field, $t . '.' . $field, $where);
				$where = str_replace('__TEMP__', '.' . $field, $where);
			}
		}
		$where = str_replace($this->get_tablename() . '.' .'id', $this->get_tablename() . '.' . $this->get_id(), $where);
		#var_dump($where);
		return $where;
	}
	/**
	 * Obtiene resultados
	 *
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $where Condiciones de la consulta
	 * @param string $fields Campos a mostrar
	 * @return array
	 */
	function get($start = null, $limit = null, $sort = null, $dir = null, $where = null, $fields = null, $query = null)
	{
		$this->_set_error_message();
		if ($limit <= 0)
		{
			$limit = null;
		}
		if ($where == '') $where = null;
		#var_dump($where);
		if (isset($query))
		{
			$where2 = $this->_query_to_where($query);
			if (isset($where))
			{
				if (is_array($where)) 
					$where[] = $where2; 
				else 
					$where = array($where, $where2);
			}
			else
			{
				$where = $where2;
			}
		}
		#var_dump($where); #die();
		$where = preg_replace('/\s*\=\s*NULL/', ' IS NULL', $where);

		if ($this->use_cache())
		{
			$cache_id = $start .  $limit . $sort . $dir . serialize($where) . serialize($fields);
			if ($cache = $this->cache->fetch($this->_tablename, $cache_id, $this->DATA_MODE_CACHE_TYPE))
			{
				log_message('info', 'MY_Modelo::get - en cache');
				#print "MY_Model::get - en cache\n";
				$this->_count = $cache['count'];
				return $cache['data'];
			}
		}
		#print "MY_Model::get - NO en cache\n";

		//Contador
		$fields = (isset($fields)&&($fields != '')?$fields:$this->_get_fields(FALSE));
		if (isset($this->_id) && ($this->_id != ''))
		{
			if (is_array($fields))
			{
				$fields[] = $this->_tablename . '.' .$this->_id . ' id';
			}
			else
			{
				$fields .=', ' . $this->_tablename . '.' .$this->_id . ' id';
			}
		}

		$this->db->flush_cache();
		$this->db->start_cache();

		// TRIGGER BEFORE
		if ($where)
		{
			$where = $this->_apply_alias($where);
		}
		#print '<pre>'; var_dump($fields); print '</pre>'; die();
		$this->db->select($fields)
		->from($this->_tablename);
		if ( !$this->onBeforeSelect(null, $sort, $dir, $where) ) return FALSE;

		if (is_array($where))
		{
			$dos = array();
			foreach ($where as $key => $value) 
			{
				if (is_numeric($key))
				{
					$dos[] = $value;
					unset($where[$key]);
				}
			}
			$tres = '';
			if (count($where) > 0)
				$this->db->where($where);
			if (count($dos) > 0)
				$this->db->where(implode(' AND ', $dos));
		}
		elseif (!empty($where))
		{
			$this->db->where($this->db->decode($where));
		}

		#var_dump($this->db->encode($where)); die();

		$this->_count = $this->db->count_all_results();

		//Consulta
		$this->_limits_sort($start, $limit, $sort, $dir);
		$query = $this->db->get();
		$this->db->flush_cache();
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();

		$data = $this->_get_results($query, $start, $limit);
		if ($query)
			$query->free_result();
		#echo '<pre>'; print_r(array_pop($this->db->queries)); echo '</pre>'; die();
		#var_dump($data); die();

		// TRIGGER AFTER
		foreach($data as $k => $v)
		{
			$r = $data[$k];
			if ( !$this->onAfterSelect($r) ) return FALSE;
			$data[$k] = $r;
		}
		// Caché
		if ($this->use_cache())
		{
			log_message('info', 'MY_Modelo::get - añadiendo caché');
			$cache['data'] = $data;
			$cache['count'] = $this->_count;
			$this->cache->store($this->_tablename, $cache_id, $cache, $this->DATA_MODE_CACHE_TIME, $this->DATA_MODE_CACHE_TYPE);
		}
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
		return $data;
	}
	/**
	 * Convierte un query a where
	 * @param string $query Expresión de búsqueda
	 * @return string
	 */
	protected function _query_to_where($query)
	{
		$where = null;
		$forcetext = FALSE;

		$query = str_replace(array('(', ')'), array('_.._', '_::_'), $query);
		// Si es un número entre comillas " se interpreta como texto
		if (preg_match('/\"\d*?\"/', $query))
		{
			$query = str_replace('"', '', $query);
			$forcetext = TRUE;
		}
		// WHERE
		if (isset($this->_name) && ($this->_name !=''))
		{
			// Búsquedas de texto
			$c  = array();
			foreach ($this->_name as $k)
			{
				$type = $this->_get_type($k);
				if (is_numeric($query) && !$forcetext && $this->_is_numeric($type))
				{
					$where .= (isset($where)?' OR ':'') . ((strpos($k, '.') === FALSE)?($this->_tablename . '.'):'') ."{$k} = {$query})";
				}
				else if ((!is_numeric($query) OR $forcetext) && ($type == DATA_MODEL_TYPE_STRING))
				{
					$c[] = ((strpos($k, '.') === FALSE)?($this->_tablename . '.'):'') . $k;
				}
			}
			#var_dump($c); die();
			if (count($c) > 0)
			{
				$c = $this->db->concat($c); #implode($c, ' + ');
				$query = str_replace('\'', '#_#', $this->db->escape_str($query));
				$w = $this->_create_like(($query), '#$$#');
				$w = str_replace('#$$#', $c, $w);
				if (isset($w) && ($w != ''))
				{
					$w = str_replace('#_#', '\'', $w);
					$where .= (isset($where)?' OR ':'') . "({$w})";
				}
			}
		}
		if (is_numeric($query) && !$forcetext) $where .= (isset($where)?' OR ':'') . "{$this->_tablename}.{$this->_id} = {$query}";
		$where = str_replace(array('_.._', '_::_'), array('(', ')'), $where);
		return $where;
	}

	/**
	 * Realiza una búsqueda por palabra clave.
	 * Si no se indica nada devuelve todos.
	 *
	 * @param string $query Palabra de búsqueda
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param string $where_2 Campos WHERE
	 * @return array
	 */
	function search($query = null, $start = null, $limit = null, $sort = null, $dir = null, $where_2 = null)
	{
		$where = $this->_query_to_where($query);

		$text = null;
		$fields = null;

		$text = $this->get_text();

		// SELECT
		if (isset($this->_id))
		{
			$fields = $this->_tablename . '.' . $this->_id .' id';
			$id = $this->db->id_text($this->_tablename . '.' . $this->_id);#str_replace('%1', $this->_tablename . '.' . $this->_id, $this->config->item('bp.data.id_text'));
			#var_dump($text);
			#$text = (isset($text))? $this->db->concat(array($text, $id)): $id;
		}

		#if (isset($text)) $fields = (isset($field)? $fields . ', ':'') . $text . ' text';
		#var_dump($fields);
		if (!empty($text)) $fields = (!empty($fields)?($fields . ', '):'') . implode(',', $text);
		#var_dump($fields); #die();
		if (isset($where_2) && ($where_2 != ''))
		{
			$where = "({$where_2})" . (!empty($where) ? " AND ({$where})" :'' );
		}
		#var_dump($fields); die();

		#print '<pre>'; var_dump($where); print '</pre>';
		#print '<pre>'; var_dump($query); print '</pre>';

		if ( !$this->onBeforeSearch($query, $where, $fields) ) return FALSE;
		#print '<pre>'; var_dump($where); print '</pre>';
		$data = $this->get($start, $limit, $sort, $dir, $where, $fields);
		#var_dump($data); die();
		#Asigna el text
		$text = $this->get_text(TRUE);
		foreach ($data as $k => $v) 
		{
			$text_field = array();
			foreach ($text as $i)
			{
				#var_dump($i, $v);
				if (isset($v[$i]) && ((!empty($v[$i]) || is_numeric($v[$i])) && trim($v[$i])!='')) $text_field[] = trim($v[$i]);
			}
			$data[$k]['text'] = implode($this->config->item('bp.data.separator'), $text_field) .
					str_replace('%1', $v['id'] , $this->config->item('bp.data.id_text'));
		}
		#die();
		return $data;
		#return (trim($where) != '')?$this->get($start, $limit, $sort, $dir, $where, $fields):array();
		//}
		//return null;
	}

	/**
	 * Obtiene un solo resgistro
	 *
	 * @param int $id Identificador
	 * @return array, FALSE si no lo ha encontrado
	 */
	function load($id, $relations = null)
	{
		$this->_set_error_message();

		$load = TRUE;
		if ($this->use_cache())
		{
			$cache_id = $id;
			if ($cache = $this->cache->fetch($this->_tablename, $cache_id, $this->DATA_MODE_CACHE_TYPE))
			{
				log_message('info', 'MY_Model::load - en cache');
				#print "MY_Model::load - en cache\n";
				#$this->_count = $cache['count'];
				$data = $cache;
				$load = FALSE;
			}
		}
		if ($load)
		{
			$this->db->flush_cache();
			$this->db->select($this->_get_fields(TRUE))
			->from($this->_tablename)
			->where($this->_tablename . '.' . $this->_id, (int)$id);

			// TRIGGER BEFORE
			if ( !$this->onBeforeSelect($id) ) return FALSE;

			$query = $this->db->get();
			// TODO Control de errores

			if ($query)
			{
				#echo '<pre>'; var_dump($query->row_array()); print '</pre>'; die();
				$data = $this->db->encode($query->row_array());
				#echo '<pre>'; var_dump($data); print '</pre>'; die();
				$query->free_result();
				if ($this->use_cache())
				{
					log_message('info', 'MY_Model::load - añadiendo caché');
					#print "MY_Model::load - añadiendo caché\n";
					$this->cache->store($this->_tablename, $cache_id, $data, $this->DATA_MODE_CACHE_TIME, $this->DATA_MODE_CACHE_TYPE);
				}
			}
		}
		if (isset($data) && (count($data) > 0))
		{
			// Relaciones
			if (isset($this->_relations))
			{
				// Si TRUE, se muestran todas
				if ($relations === TRUE)
				{
					$relations = null;
					foreach($this->_relations as $k => $v)
					{
						$relations[] = $k;
					}
				}
				else if ((isset($relations)) && !is_array($relations))
				{
					$relations = array($relations);
				}
				if (is_array($relations))
				{
					foreach($relations as $k)
					{
						$data[$k] = $this->get_relation($id, $k, $data);
					}
				}
			}
			// TRIGGER AFTER
			if ( !$this->onAfterSelect($data, $id) ) return FALSE;

			return (count($data)>0) ? $data : FALSE;
		}
		return FALSE;
	}

	/**
	 * Obtiene el número de registros afectados
	 *
	 * @return int
	 */
	function get_count()
	{
		return $this->_count;
	}

	/**
	 * Devuelve el modelo de datos
	 * @param array $exclude Campos al excluir de la definición
	 * @return array
	 */
	function get_data_model($exclude =  null)
	{
		$data = array();
		foreach ($this->_data_model as $k => $v)
		{
			if (isset($exclude))
			{
				if (!in_array($k, $exclude))
				{
					$data[$k] = $v;
				}
			}
			else
			{
				$data[$k] = $v;
			}
		}
		return $data;
	}

	/**
	 * Devuelve el campo Identificador
	 * @return string
	 */
	function get_id()
	{
		return $this->_id;
	}

	/**
	 * Devuelve el último Id de la tabla
	 * @return string
	 */
	function get_last()
	{
		$this->db->flush_cache();
		$this->db->select_max($this->_id)
		->from($this->_tablename);
		$query = $this->db->get();
		$data = $query->row_array();

		return isset($data[$this->_id])?$data[$this->_id]:0;
	}

	/**
	 * Comprueba si se ha producido un error
	 * @return bool TRUE: Ha habido error, FALSE: no lo ha habido
	 */
	function _check_error()
	{
		$str = $this->db->_error_message();
		if ($str != '' && strpos($str, 'contexto')===FALSE && strpos($str, 'Change')===FALSE)
		{
			$this->_set_error_message(string_encode($str));
			return TRUE;
		}
		return FALSE;
	}
	/**
	 * Devuelve el último mensaje de error
	 * @return string
	 */
	function error_message()
	{
		if (isset($this->_error_message) && $this->_error_message != '')
		{

			return $this->_error_message;
		}
		else
		{
			return utf8_encode($this->db->_error_message());
		}
		$message = $this->_error_message;
		$this->_error_message = null;
		if (!isset($message))
		{
			return $this->db->_error_message();
		}
		else
		{
			return $message;
		}
	}

	function get_tablename()
	{
		return $this->_tablename;
	}

	/*function get_text()
	{
		foreach ($this->_name as $c)
		{
			$text[] = $this->_tablename . '.' . $c;
			$text[] = "'" . $this->config->item('bp.data.separator') . "'";
		}
		// Convertir a texto
		unset ($text[count($text)-1]);
		$text = $this->db->concat($text);									
		return $text;
	}*/

	function get_text($clean = FALSE)
	{
		foreach ($this->_name as $c)
		{
			$base = (!$clean?($this->_tablename . '.'):'');
			if (strpos($c, '.')!==FALSE) $base = '';
			$text[] =  $base . $c;
		}
		return $text;
	}

	/**
	 * Devuelve las relaciones del modelo de datos
	 * @return array
	 */
	function get_relations()
	{
		$data = array();
		if (isset($this->_relations))
		{
			foreach($this->_relations as $k => $v)
			{
				$data[] = $k;
			}
		}
		return $data;
	}

	/**
	 * Carga las relaciones cruzadas
	 * @param int $id Id del registro
	 * @param string $relation Nombre de la relación
	 * @return array Los datos cruzadas
	 */
	function get_relation($id, $relation, $data)
	{
		if (isset($this->_relations[$relation]))
		{
			$rel = $this->_relations[$relation];
			$obj = get_instance();
			$tablename = preg_split('/\//', $rel['ref']);
			$tablename = $tablename[count($tablename) - 1];
			$obj->load->model($rel['ref']);

			$t = $obj->$tablename->get_tablename();
			$text = $obj->$tablename->get_text();
			$f = implode(',', $text);
			$i = $t . '.' . $obj->$tablename->get_id();
			if (isset($rel['table']))
			{
				//TODO Que no use SQL directo, sino el acceso a los modelos, para poder usar caché
				$this->db->flush_cache();
				$this->db->select("$f text, $i id");
				$this->db->from($t);
				$this->db->join($rel['table'], "{$rel['table']}.{$rel['fk']} = {$i}");
				$this->db->where("{$rel['table']}.{$this->_id} = {$id}");

				$query = $this->db->get();
				$data = $this->_get_results($query);
				foreach ($data as $k => $v) 
				{
					$text_field = array();
					foreach ($text as $i)
					{
						if (!empty($v[$i])) $text_field[] = $v[$i];
					}
					$data[$k]['text'] = implode(' / ', $text_field);
				}
				return $data;
			}
			else if (isset($rel['ref']) && (isset($rel['fk'])))
			{
				$type = isset($rel['type'])?$rel['type']:DATA_MODEL_RELATION_11;
				switch ($type)
				{
					case DATA_MODEL_RELATION_11:
						#echo "$relation - 11 - {$rel['fk']} - {$data[$rel['fk']]}\n";
						if (isset($data[$rel['fk']]))
						{
							return $obj->$tablename->load($data[$rel['fk']]);
						}
						break;
					case DATA_MODEL_RELATION_1N:
						if (isset($data[$rel['fk']]))
						{
							$t = (isset($rel['fk_table'])?$rel['fk_table']:$obj->$tablename->get_tablename());
							if (!isset($rel['fk_other'])) $rel['fk_other'] = $rel['fk'];
							return $obj->$tablename->get(null, null, null, null,"{$t}.{$rel['fk_other']} = {$data[$rel['fk']]}");
						}
						break;
				}
			}
		}
		return null;
	}

	/**
	 * Añade los registros relacionados
	 * @param int $id Id de la relación principal
	 * @param array $data Datos completos del registro principal
	 * @return TRUE: no hay errores, int < 0 error
	 */
	protected function _insert_relations($id, &$data)
	{
	}

	/**
	 * Añade los campos de auditoria UPDATE
	 * @param array $data Datos
	 */
	protected function _audit_upd(&$data)
	{
		if (!isset($data['cAUser'])) $data['cAUser'] = $this->userauth->get_username();
		if (!isset($data['dAct'])) $data['dAct'] = time();
	}

	/**
	 * Añade los campos de auditoria INSERT
	 * @param array $data Datos
	 */
	protected function _audit_add(&$data)
	{
		if (!isset($data['cCUser'])) $data['cCUser'] = $this->userauth->get_username();
		if (!isset($data['dCreacion'])) $data['dCreacion'] = time();
	}

	/**
	 * Trigger llamado Antes de insertar los datos
	 * @param array $data Registro a insertar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeInsert(&$data)
	{
		//Añade los campos de auditoría
		if ($this->_audit)
		{
			$this->_audit_add($data);
			$this->_audit_upd($data);
		}

		return TRUE;
	}

	/**
	 * Trigger llamado después de insertar los datos
	 * @param int $id Id del registro insertado
	 * @param array $data Registro
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onAfterInsert($id, &$data)
	{
		if (!isset($this->_relations)) return TRUE;

		#print "Insertando relaciones\n";
		foreach($this->_relations as $k => $rel)
		{
			#print "relacion {$k}\n";
			if (isset($data[$k]))
			{
				//$rel = $this->_relations[$k];
				#var_dump($rel);
				if (isset($rel['ref']) && (isset($rel['fk'])))
				{
					$type = isset($rel['type'])?$rel['type']:DATA_MODEL_RELATION_11;
					#print "Type {$type}\n";
					switch ($type)
					{
						case DATA_MODEL_RELATION_1N:
							$tablename = preg_split('/\//', $rel['ref']);
							$tablename = $tablename[count($tablename) - 1];
							#print "cargado modelo {$rel['ref']}\n";
							$obj = get_instance();
							$obj->load->model($rel['ref']);
							foreach($data[$k] as $linea)
							{
								$linea[$rel['fk']] = $id;
								#print "insertando relacion {$k}\n";
								#var_dump($linea);
								$id2 = $obj->$tablename->insert($linea);
								#print "{$k} id -> {$id2}\n";
								if ($id2 < 0)
								{
									$this->_set_error_message($obj->$tablename->error_message());
									return FALSE;
								}
							}
							break;
						case DATA_MODEL_RELATION_11:
							$tablename = preg_split('/\//', $rel['ref']);
							$tablename = $tablename[count($tablename) - 1];
							#print "cargado modelo {$rel['ref']}\n";
							$obj = get_instance();
							$obj->load->model($rel['ref']);
							$linea = $data[$k];
							$linea[$rel['fk']] = $id;
							#print "insertando relacion {$k}\n";
							#var_dump($linea);
							$id2 = $obj->$tablename->insert($linea);
							#print "{$k} id -> {$id2}\n";
							if ($id2 < 0)
							{
								$this->_set_error_message($obj->$tablename->error_message());
								return FALSE;
							}
							break;												
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 * Trigger llamado antes de actualizar los datos
	 * @param int $id Id del registro actualizado
	 * @param array $data Registro
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		// Datos de auditoría
		if ($this->_audit)
		{
			$this->_audit_upd($data);
		}

		return TRUE;
	}

	/**
	 * Trigger llamado antes de actualizar los datos
	 * @param int $id Id del registro actualizado
	 * @param array $data Registro
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onAfterUpdate($id, &$data)
	{
		if (!isset($this->_relations)) return TRUE;

		foreach($this->_relations as $k => $rel)
		{
			if (isset($data[$k]))
			{
				#print 'UPDATE DE ' . $k;
				$rel = $this->_relations[$k];
				if (isset($rel['ref']) && (isset($rel['fk'])))
				{
					$type = isset($rel['type'])?$rel['type']:DATA_MODEL_RELATION_11;
					switch ($type)
					{
						case DATA_MODEL_RELATION_1N:
							$tablename = preg_split('/\//', $rel['ref']);
							$tablename = $tablename[count($tablename) - 1];
							$obj = get_instance();
							$obj->load->model($rel['ref']);
							foreach($data[$k] as $linea)
							{
								if (isset($linea[$obj->$tablename->get_id()]) && is_numeric($linea[$obj->$tablename->get_id()]))
								{
									#echo 'UPDATE RELATION ' . $k;
									$id2 = $obj->$tablename->update($linea[$obj->$tablename->get_id()], $linea);
									if ($id2 !== TRUE)
									{
										$this->_set_error_message($obj->$tablename->error_message());
										return FALSE;
									}
								}
								else
								{
									#echo 'INSERT RELATION';
									$linea[$rel['fk']] = $id;
									#print '<pre>'; print_r($linea); echo '</pre>';
									$id2 = $obj->$tablename->insert($linea);
									#print "ID $id2";
									if ($id2 < 0 || $id2 === FALSE)
									{
										$res = $obj->$tablename->error_message();
										#echo $res;
										$this->_set_error_message($res);
										return FALSE;
									}
								}
							}
							break;
						case DATA_MODEL_RELATION_11:
							$tablename = preg_split('/\//', $rel['ref']);
							$tablename = $tablename[count($tablename) - 1];
							$obj = get_instance();
							$obj->load->model($rel['ref']);
							#echo "{$tablename} {$id}";
							$pre = $obj->$tablename->load($id);
							if ($pre)
							{
								if (!$obj->$tablename->update($id, $data[$k]))
								{
									$this->_set_error_message($obj->$tablename->error_message());
									return FALSE;
								}								
							}
							else
							{
								#echo 'INSERT RELATION';
								$linea = $data[$k];
								$linea[$rel['fk']] = $id;
								#$linea = array_merge($pre, $linea);
								#print '<pre>'; print_r($linea); echo '</pre>';
								$id2 = $obj->$tablename->insert($linea);
								#print "ID $id2";
								if ($id2 < 0)
								{
									$this->_set_error_message($obj->$tablename->error_message());
									return FALSE;
								}
							}
							break;
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 * Trigger llamado antes de borrar los datos
	 * @param int $id Id del registro a borrar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeDelete($id)
	{
		if (isset($this->_relations))
		{
			$obj = get_instance();
			foreach ($this->_relations as $relation => $rel )
			{
				#echo 'Revisando ' . $relation;
				if (isset($rel['cascade']) && ($rel['cascade']))
				{
					if (isset($rel['table']))
					{
						#print 'Borrando ' . $rel['table'];
						$this->db->flush_cache();
						$this->db->where("{$this->_id} = {$id}");
						$this->db->delete($rel['table']);
						if ($this->_check_error())
						{
							$this->db->trans_rollback();
							return FALSE;
						}
					}
					else
					{
						$tablename = preg_split('/\//', $rel['ref']);
						$tablename = $tablename[count($tablename) - 1];
						$obj->load->model($rel['ref']);

						$field = $this->get_id();
						#var_dump($tablename); die();
						$obj->$tablename->delete_by("{$field} = {$id}");
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 * Trigger llamado después de borrar los datos
	 * @param int $id Id del registro borrado
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onAfterDelete($id)
	{
		if ($this->_deleted)
		{
			$ins = array(
				'nIdRegistro' => (int)$id,
				'cTabla' =>  $this->db->escape_str($this->get_tablename()),
				'cCUser' => $this->userauth->get_username(),
				'dCreacion' => $this->_todate(time())
			);
			if (!$this->db->insert('Gen_Deleted', $ins))
			{
				$this->_set_error_message($this->db->_error_message());
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Trigger llamado antes de borrar los datos por where
	 * @param string $where
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeDeleteBy($where)
	{
		return TRUE;
	}
	/**
	 * Trigger llamado después de borrar los datos por where
	 * @param string $where
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onAfterDeleteBy($where)
	{
		return TRUE;
	}

	/**
	 * Trigger llamado antes del SELECT
	 * @param int $id Id del registro borrado
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		return TRUE;
	}

	/**
	 * Trigger llamado antes del SELECT
	 * @param array $data Registros
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		return TRUE;
	}

	/**
	 * Trigger llamado antes del SEARCH
	 * @param string $query Query original
	 * @param string $where Where del SQL
	 * @param string $fields Campos del SQL
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeSearch($query, &$where, &$fields)
	{
		if ($this->_audit)
		{
			if (!is_array($fields))
			{
				$fields = array($fields);
			}
			$fields[] = $this->_date_field("{$this->_tablename}.dCreacion", 'dCreacion');
			$fields[] = $this->_date_field("{$this->_tablename}.dAct", 'dAct');
			$fields[] = "{$this->_tablename}.cCUser";
			$fields[] = "{$this->_tablename}.cAUser";
		}

		return TRUE;
	}

	/**
	 * Trigger antes de parsear una consulta
	 * @param array $where Campos WHERE
	 */
	protected function onParseWhere(&$where)
	{
		return TRUE;
	}

	/**
	 * Habilita los triggers del modelo
	 */
	function triggers_enable()
	{
		$this->triggers_enabled = TRUE;
	}

	/**
	 * Deshabilita los triggers del modelo
	 */
	function triggers_disable()
	{
		$this->triggers_enabled = FALSE;
	}

	/**
	 * Consulta el estado de los triggers del modelo
	 */
	function triggers_status()
	{
		return $this->triggers_enabled;
	}

	/**
	 * Notas del documento
	 *
	 * @param int $id Id del registro
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return array
	 */
	function get_notas($id, $start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		$this->obj->load->model('generico/m_nota');
		$tabla = $this->db->escape_str($this->get_tablename());
		$where = "nIdRegistro={$id} AND cTabla ='{$tabla}'";
		return $this->obj->m_nota->get($start, $limit, $sort, $dir, $where, null, $query);		
	} 

	/**
	 * Notas del documento
	 *
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return array
	 */
	function get_deleted($start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		$this->obj->load->model('generico/m_deleted');
		$tabla = $this->db->escape_str($this->get_tablename());
		$where = "cTabla ='{$tabla}'";
		if (!empty($query)) $where .= ' AND ' . $query;
		return $this->obj->m_deleted->get($start, $limit, $sort, $dir, $where);
	}

	/**
	 * Genera un registro aleatorio de la tabla 
	 * @return array
	 */
	function random_data()
	{
		$this->obj->load->helper('LoremIpsum');
		$generator = new LoremIpsumGenerator;

		$data = array();
		foreach ($this->_data_model as $field)
		{
			if (isset($field[DATA_MODEL_DEFAULT_VALUE]))
				$data[$field[DATA_MODEL_FIELD]] = $field[DATA_MODEL_DEFAULT_VALUE];
			else
			{
				switch ($field[DATA_MODEL_TYPE]) 
				{
					case DATA_MODEL_TYPE_INT:
						$v = (int) rand(PHP_INT_MAX);
						break;
					case DATA_MODEL_TYPE_STRING:
						$v = trim($generator->getContent(30, 'txt'));
						break;
					case DATA_MODEL_TYPE_DATETIME:
					case DATA_MODEL_TYPE_DATE:
					case DATA_MODEL_TYPE_TIME:
						$v = time();
						break;
					case DATA_MODEL_TYPE_DOUBLE:
					case DATA_MODEL_TYPE_FLOAT:
					case DATA_MODEL_TYPE_MONEY:
						$v = (float) mt_rand() / mt_getrandmax();
						break;
					case DATA_MODEL_TYPE_BOOLEAN:
					case DATA_MODEL_TYPE_BOOL:
						$v = .01 * rand(0, 100) >= .5;
						break;
					default:
						$v = time();
						break;
				}
				$data[$field[DATA_MODEL_FIELD]] = $v;
			}
		}

		return $data;
	}

	/**
	 * Test unitario del modelo de datos
	 * @param  Tester $tester Controlador del test
	 * @return null
	 */
	function test($tester)
	{
		$data = $this->random_data();
		#var_dump($this->_data_model, $data); die();
		#Prueba el GET
		$res = $this->get(0, 10);
		$tester->assertEqual($this->error_message(), null, get_class($this) .' GET - ' . $this->error_message());
		#Prueba LOAD
		if (count($res)>0)
		{
			$reg = $this->load($res[0]['id']);
			$tester->assertEqual($this->error_message(), null, get_class($this) .' LOAD - ' . $this->error_message());
			#var_dump($reg);
		}
		#Prueba SEARCH
		if (isset($reg))
		{
			$s = $this->search($reg['id']);
			$tester->assertEqual($this->error_message(), null, get_class($this) .' SEARCH ID - ' . $this->error_message());
			#var_dump($s);
		}
		#Prueba INSERT
		$this->db->trans_begin();
		$id = $this->insert($data);
		$tester->assertEqual($this->error_message(), null, get_class($this) .' INSERT 1 - ' . $this->error_message());
		$tester->assertTrue($id > 0, get_class($this) .' INSERT 2');
		#Prueba UPDATE
		$data = $this->random_data();
		$tester->assertTrue($this->update($id, $data), get_class($this) .' UPDATE 1');
		$tester->assertEqual($this->error_message(), null, get_class($this) .' UPDATE 2 - ' . $this->error_message());
		#Prueba DELETE
		$tester->assertTrue($this->delete($id), get_class($this) .' DELETE 1');
		$tester->assertEqual($this->error_message(), null, get_class($this) .' DELETE 2 - ' . $this->error_message());
		$this->db->trans_rollback();
	}

}

/* End of file My_Model.php */
/* Location: ./system/libraries/My_Model.php */
