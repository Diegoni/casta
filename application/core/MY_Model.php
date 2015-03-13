<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('DATA_MODEL_FIELD', 				1);
define('DATA_MODEL_REQUIRED', 			2);
define('DATA_MODEL_TYPE', 				3);
define('DATA_MODEL_DESCRIPTION', 		4);
define('DATA_MODEL_EDITOR', 			5);
define('DATA_MODEL_DEFAULT', 			6);
define('DATA_MODEL_READONLY', 			7);
define('DATA_MODEL_DEFAULT_VALUE',		8);
define('DATA_MODEL_NO_LIST',			9);
define('DATA_MODEL_GRID',				10);
define('DATA_MODEL_NO_GRID',			11);
define('DATA_MODEL_SEARCH',				12);
define('DATA_MODEL_DUPLICATE',			13);

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




class MY_Model extends CI_Model {
	protected $_tablename	= '';
	protected $_id			= '';
	protected $_order		= '';
	protected $_name		= '';
	protected $_data_model	= array();

	function __construct(
				$tablename = null, 
				$id = null,
				$order = null, 
				$name = null,
				$data_model =null 
				)
	{
		$this->_tablename	= $tablename;
		$this->_id			= $id;
		$this->_order		= $order;
		$this->_name		= $name;
		$this->_data_model	= $data_model;
		parent::__construct();
	}


//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------
// Hace el insert de los datos, se debe enviar el post del formulario y se recorre $data_model buscando coincidencia
//------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------

	
	function insert()
	{
		$datos = $this->input->post();
		$bandera_error = 0;
		
		foreach ($this->_data_model as $key => $value) {
			if(isset($datos[$key]))//Controlamos que el dato enviado pertenesca al modelo
			{
				// Control para registros duplicados 
				if(isset($value[DATA_MODEL_DUPLICATE]))
				{
					$query = $this->db->query("SELECT *
								FROM `$this->_tablename` 
								WHERE $this->_tablename.$key = '$datos[$key]'");
					if($query->num_rows() > 0)
					{	
						$bandera_error = -1;
					}
				}
				// Control que los datos requeridos no sean vacios
				else if(isset($value[DATA_MODEL_REQUIRED]) && $datos[$key]=='')
				{
					$bandera_error = -2;
				}
				else
				{
					$registro[$key]	= $datos[$key];					
				}
			}
		}
		
		if($bandera_error==0)//Si no hay errores
		{
			$this->db->insert($this->_tablename, $registro);
			
			$id = $this->db->insert_id();	
		}
		else
		{
			$id = $bandera_error;	
		}
	 	
		return $id;
	}


//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------
// Forma el array para usar en los select de las vistas
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------


	function getSelect($column=NULL)
	{
		if($column==NULL)
		{
			if(is_array($this->_name))
			{
				foreach ($this->_name as $key => $value) 
				{
					$valor = $value;
				}
			}
			else
			{
				$valor = $this->_name;
			}	
		}
		else 
		{
			$valor = $column;
		}
		
		if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM $this->_tablename LIKE 'id_lang' ")) == 1 )
		{
			$query = $this->db->query("SELECT 
				$valor as descripcion,
				$this->_id as id_tabla
				FROM `$this->_tablename`
				WHERE id_lang = 1
				ORDER BY $this->_order");//Mejorar esto
		}
		else 
		{
			$query = $this->db->query("SELECT 
				$valor as descripcion,
				$this->_id as id_tabla
				FROM `$this->_tablename`
				ORDER BY $this->_order");//Mejorar esto
		}
		
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------
// Busca elemento por id
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------


	function getID($id, $array=NULL)
	{
		$query = $this->db->query("SELECT 
				*
				FROM `$this->_tablename` 
				WHERE $this->_tablename.$this->_id = $id");
			
		if($query->num_rows() > 0){
			
			if($array == NULL || $array == FALSE)
			{
				foreach ($query->result() as $fila){
					$data[] = $fila;
				}
				return $data;	
			}
			else
			{
				return $query->row_array();	
			}		
						
		}else{
			return FALSE;
		}
	}
	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------
// Devuelve el mensaje para las acciones	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------
	
	
	function getMensaje($action, $state, $id)
	{
		if(is_array($this->_name))
		{
			$name = $this->_name[0];
		}
		else 
		{
			$name = $this->_name;
		}	
			
		$query = $this->db->query("SELECT 
				$name as descripcion
				FROM `$this->_tablename` 
				WHERE $this->_tablename.$this->_id=$id");
		$row = $query->row(); 
		
		if($state=='ok')
		{
			return "<div id='dialog-message-ok' title='$action $state' style='display:none;'>
  					<p>
    					<i class='fa fa-check-square'></i>
    					El $action del registro $row->descripcion fue correcto
  					</p>
  					</div>";	
		}
		else 
		{
			$error = $this->getError($id);
			
			return "<div id='dialog-message-error' title='$action $state' style='display:none;'>
  					<p>
    					<i class='fa fa-exclamation-triangle'></i>
    					El $action del registro dio error
    					$error
  					</p>
  					</div>";
		}
	}
	
	function getError($id)
	{
		switch ($id) 
		{
			case -1:
				return "Registro repetido";
				break;
	   		case -2:
				return "Faltan completar campos";
				break;
			default:
				return "";
				break;
		}
	}
	
	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
// Devuelve todos los registros que no esten dados de baja
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
	
	
	function getRegistros($where = NULL)
	{
		$condicion = 'WHERE ';
		
		// Where que puede poner en la función 
		if($where != NULL)
		{
			$condicion .= $where;
		}
		
		$condicion = $this->addCondicion($condicion);
				
		if($condicion != 'WHERE ')
		{
			$query = "SELECT * FROM `$this->_tablename` $condicion";
		}
		else 
		{
			$query = "SELECT * FROM `$this->_tablename`";
		}
		

		$query = $this->db->query($query);
		
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}		
	}
	
	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
// Devuelve condiciones comunes para el select
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
	
	
	function addCondicion($where=NULL, $tablename=NULL)
	{
		if($tablename==NULL)
		{
			 $tablename = $this->_tablename;
		}
		
		if($where==NULL || $where=='')
		{
			$condicion = 'WHERE ';
		}
		else
		{
			$condicion = $where;	
		}
		
		
		// Comprobamos que este el campo delete, si esta no hay que seleccionar los que estan eliminados
		if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM $tablename LIKE 'delete' ")) == 1 )
		{
			if($condicion == 'WHERE ')
			{
				$condicion .= "$tablename.delete = 0";	
			}
			else
			{
				$condicion .= " AND $tablename.delete = 0";
			}
		}
		
		// Comprobamos que este el campo id_lang, para seleccionar solo un idioma, hay que mejorar esta consulta
		if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM $tablename LIKE 'id_lang' ")) == 1 )
		{
			if($condicion == 'WHERE ')
			{
				$condicion .= "$tablename.id_lang = 1";	
			}
			else
			{
				$condicion .= " AND $tablename.id_lang = 1";
			}
		}
		
		// Comprobamos que este el campo id_shop, para seleccionar solo una tienda
		if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM $tablename LIKE 'id_shop' ")) == 1 )
		{
			if($condicion == 'WHERE ')
			{
				$condicion .= "$tablename.id_shop = 1";	
			}
			else
			{
				$condicion .= " AND $tablename.id_shop = 1";
			}
		}
		
		return $condicion;
	}
	
	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
// Get data_model
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	

	
	function getData_model()
	{
		return $this->_data_model;
	}
	
	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
// Devuelve tabla
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	

	
	function getTable()
	{
		return $this->_tablename;
	}
	
	
	
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
// Devuelve ID tabla 
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	

	
	function getId_table()
	{
		return $this->_id;
	}
	
		
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
// Update
//------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------	
	
	function update($id)
	{
		$datos = $this->input->post();
		
		foreach ($datos as $key => $value) {
			if(array_key_exists($key, $this->_data_model)){
				$registro[$key] = $value;
			}
		}
		
		$this->db->update(
					$this->_tablename, 
					$registro, 
					array($this->_id => $id)
		);
	}
	
	
	
	
}//Finaliza My_Model

	

/* End of file My_Model.php */
/* Location: ./system/libraries/My_Model.php */
