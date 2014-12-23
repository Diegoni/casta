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
 * Definiciones SQL
 */
define('SQL_EXPORT_COUNT', 'SELECT COUNT(*) count FROM %1 %2');
define('SQL_EXPORT_SELECT', 'SELECT %2 %4 FROM %1 %3');
define('SQL_EXPORT_SELECT_MYSQL', 'SELECT %4 FROM %1 %3 %2');
define('SQL_EXPORT_SELECT_FIELDS', 'SELECT TOP 1 * FROM %1');
define('SQL_EXPORT_SELECT_FIELDS_MYSQL', 'SELECT * FROM %1 LIMIT 1');
define('SQL_EXPORT_COLUMNS', "SELECT column_name 'name',
	data_type 'type'
	FROM information_schema.columns
	WHERE table_name = '%1'");

define('SQL_EXPORT_PRE', 'SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";');

define('SQL_EXPORT_POST', 'SET FOREIGN_KEY_CHECKS=1;');

define('SQL_TABLES' , 'SELECT name FROM sys.Tables');
define('SQL_TABLES_REFERENCES', 'SELECT f.name AS ForeignKey,
		OBJECT_NAME(f.parent_object_id) AS TableName,
		OBJECT_NAME (f.referenced_object_id) AS ReferenceTableName
		FROM sys.foreign_keys AS f
		INNER JOIN sys.foreign_key_columns AS fc ON f.OBJECT_ID = fc.constraint_object_id
		INNER JOIN sys.objects AS o ON o.OBJECT_ID = fc.referenced_object_id
		WHERE OBJECT_NAME(f.parent_object_id)=\'%1\'');

define('SQL_DELETE_TABLE', 
'CREATE TABLE %1_new LIKE %1;
RENAME TABLE %1 TO %1_old, %1_new TO %1;
DROP TABLE %1_old');
#	'DELETE FROM %1 WHERE 1=1');
define('SQL_IMPORT' ,'USE %d%;SET @@global.max_allowed_packet=167772160;SOURCE %1;');
define('PHP_IMPORT' ,'php migrateuno.php %1 -user %u1% -pass %p1% -server %s1% -db %d1% -user2 %u2% -pass2 %p2% -server2 %s2% -db2 %d2% -o %o% -n %2');

/**
 * Comando SHELL para importar con mysql
 */
define('MYSQL_IMPORT' ,'mysql --user=%u% --password=%p% --host=%s% %d% < \'%1\'');
define('MYSQL_IMPORT_PRE' ,'mysql --max_allowed_packet=128M --user=%u% --password=%p% --host=%s%');

/**
 * Exportación / Importación de tablas de datos
 */
class ExportTables 
{
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Numero de tabla (para generar el archivo ordenado)
	 * @var int
	 */
	private $_number;
	/**
	 * Último mensaje de error
	 * @var string
	 */
	private $_last_error = null;
	/**
	 * Número de registros a leer de golpe
	 * @var int
	 */
	private $_count;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		$this->_count = $this->obj->config->item('bp.export.numregs');

		log_message('debug', 'ExportImport Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Crea el fichero SQL con las primeras instrucciones
	 * @param string $filename Fichero
	 * @param string $table Nombre de la tabla
	 * @param bool $delete TRUE: Borra los registros
	 * @return file
	 */
	private function start_file($filename, $table, $delete = FALSE)
	{
		$f = fopen($filename, 'w'); 
		fwrite($f, utf8_encode(SQL_EXPORT_PRE . "\r\n"));
		if ($delete) fwrite($f, utf8_encode(str_replace('%1', $table, SQL_DELETE_TABLE)) . ";\r\n");
		return $f;
	}

	/**
	 * Finaliza el archivo SQL y añade las instrucciones SQL finales
	 * @param string $filename Nombre del fichero
	 * @param file $f 
	 * @return null
	 */
	private function end_file($filename, $f)
	{
		fwrite($f, utf8_encode(';' . "\r\n"));
		fwrite($f, utf8_encode(SQL_EXPORT_POST . "\r\n"));
		fclose($f);
		return TRUE;
	}

	/**
	 * Transforma los campos y valores en una instrucción SQL
	 * @param array $query Campos
	 * @param array $params Valores
	 * @return string
	 */
	private function showQuery($query, $params)
    {
        $keys = array();
        $values = array();
        
        # build a regular expression for each parameter
        foreach ($params as $key => $value)
        {
            if (is_string($key))
            {
                $keys[] = $key;
            }
            else
            {
                $keys[] = '/[?]/';
            }
            
            if(is_float($value))
            {
                $values[] = floatval($value);
            }
            elseif(is_int($value))
            {
                $values[] = intval($value);
            }
            elseif (is_null($value))
            {
				$values[] = 'NULL';
            }
            else
            {
            	$value = str_replace("\\", "\\\\", $value);
            	$value = str_replace("'", "\\'", $value);
                $values[] = '\''. $value .'\'';
            }
        }
        #var_dump($query, $keys, $values);
        
        #$query = preg_replace($keys, $values, $query, 1, $count);
        $query = str_replace($keys, $values, $query);
        #echo $query; die();
        return $query;
    }

    /**
     * Devuelve el nombre del fichero SQL siguiendo la secuencia
     * @param string $filename Patrón
     * @param int $num Número de parte
     * @param string $table Nombre de la tabla
     * @return string
     */
	private function get_filename($filename, $num, $table)
	{
		return sprintf($filename, 
			str_pad($this->_number, 3, '0', STR_PAD_LEFT), 
			str_pad($num, 3, '0', STR_PAD_LEFT), 
			$table);
	}

	/**
	 * Devuelve el nombre del path completo donde se han volcado los datos
	 * @param string $output Directorio base
	 * @return string
	 */
	function get_directory($output)
	{
		return DIR_TEMP_PATH . "export/{$output}";
	}

	/**
	 * Vuelca una base de datos en el directorio indicado
	 * @param string $table Nombre de la tabla
	 * @param string $output Directorio donde volcar
	 * @param string $filter Filtro sobre la tabla
	 * @param string $driver Base de datos (mssql o mysqli)
	 * @return bool
	 */
	function table($table, $output = null, $filter = null, $fields_ini = null, $driver = 'mssql')
	{
		set_time_limit(0);

		$start_process = microtime(TRUE);

		# MSSQL
		$username = $this->obj->db->username;
		$password = $this->obj->db->password;
		$server = $this->obj->db->hostname;
		$database = $this->obj->db->database;

		#date_default_timezone_set('Europe/Madrid');
		gc_enable();

		$link = ($driver == 'mssql')?mssql_connect($server, $username, $password):mysqli_connect($server, $username, $password);
		($driver == 'mssql')?mssql_select_db($database, $link):mysqli_select_db($link, $database);

		# Crea el Script SQL
		$sql = str_replace('%1', $table, SQL_EXPORT_COUNT);
		$filter = !empty($filter)?(' WHERE (' . $filter . ')'):'';
		if (($driver != 'mssql'))
			$filter = str_replace(array('GETDATE', 'DATEDIFF(d,'), array('NOW', 'DATEDIFF('), $filter);
				
		$sql = str_replace('%2', $filter, $sql);
		#echo $sql . "<br/>";
		$query = ($driver == 'mssql')?mssql_query($sql):mysqli_query($link, $sql);

		$fields_ok = null;
		#var_dump($fields_ini);
		if (isset($fields_ini))
		{
			$fields = explode(',', $fields_ini);
			foreach ($fields as $value) 
			{
				if (trim($value) != '') $fields_ok[] = trim($value);
			}
		}

		if ($query)
		{
			$row = ($driver == 'mssql')?mssql_fetch_assoc($query):mysqli_fetch_assoc($query); 
			$count = 0;
			$total = $row['count'];
			if ($total > 0)
			{
				$types = array();
				$sql = str_replace('%1', $table, SQL_EXPORT_COLUMNS);
				$query = ($driver == 'mssql')?mssql_query($sql):mysqli_query($link, $sql);
				while ($row = (($driver == 'mssql')?mssql_fetch_assoc($query):mysqli_fetch_assoc($query)))
				{
					$types[$row['name']] = $row['type'];
				}

				# Campos
				$sql = str_replace('%1', ($driver == 'mssql')?"$database..$table":"$database.$table", ($driver == 'mssql')?SQL_EXPORT_SELECT_FIELDS:SQL_EXPORT_SELECT_FIELDS_MYSQL);
				$query = ($driver == 'mssql')?mssql_query($sql):mysqli_query($link, $sql);
				$row = ($driver == 'mssql')?mssql_fetch_assoc($query):mysqli_fetch_assoc($query); 
				$fields = array();
				$values = array();
				foreach ($row as $k => $r)
				{
					if (!isset($fields_ok) || (isset($fields_ok) && in_array($k, $fields_ok)))
					{
						$fields[] = $k;
						$values[] = ':' . $k . ':';
					}
				}
				$sql5 = "INSERT INTO {$table} (" . implode(',', $fields) . ') VALUES ';
				$sql4 = '(' . implode(',', $values) . ')';
					
				# Datos
				$top = (!empty($this->config['max']))
					?(($driver == 'mssql')?(' TOP ' . $this->config['max']):(' LIMIT ' . $this->config['max']))
					:'';
				$sql = str_replace(array('%1', '%2'), array(($driver == 'mssql')?"$database..$table":"$database.$table", $top), ($driver == 'mssql')?SQL_EXPORT_SELECT:SQL_EXPORT_SELECT_MYSQL);
				$sql = str_replace('%3', $filter, $sql);
				$sql = str_replace('%4', isset($fields_ok)?$fields_ini:'*', $sql);
				$query2 = ($driver == 'mssql')?mssql_query($sql):mysqli_query($link, $sql);
				if (!is_dir(DIR_TEMP_PATH . 'export')) 
						mkdir(DIR_TEMP_PATH . 'export', 0777);
				if (isset($output))
				{
					if (!is_dir(DIR_TEMP_PATH . "export/{$output}")) 
							mkdir(DIR_TEMP_PATH . "export/{$output}", 0777);
					$filename = DIR_TEMP_PATH . "export/{$output}/%s_%s_%s.sql";
				}
				else
				{
					$filename = DIR_TEMP_PATH . 'export/export.sql';
				}

				$primero = TRUE;
				$delete = TRUE;
				$count = $group = 0;
				$numfiles = 0;

				# Lee todos
				while ($row2 = (($driver == 'mssql')?mssql_fetch_assoc($query2):mysqli_fetch_assoc($query2)))
				{
					$values = array();
					foreach ($row2 as $key => $value) 
					{
						# Convierte las fechas
						if (!isset($types[$key]))
						{
							$this->_last_error = sprintf($this->obj->lang->line('export-table-field-error'), $table, $key);
							return FALSE;
						}

						if ($types[$key] == 'datetime') 
						{							
							if (!empty($value))
							{
								$phpdate = strtotime( $value );				
								$value = date( 'Y-m-d H:i:s', $phpdate );
							}	
						}
						elseif (in_array($types[$key], array('double', 'float', 'numeric')))
						{							
							if (!empty($value))
							{
								$value = str_replace(',' ,'.', (string) $value);
							}
						}
						$values[':' . $key. ':'] = $value;	
					}
					# Crea el fichero, si no se había creado antes
					if ($primero)
					{
						$f = $this->start_file($this->get_filename($filename, $numfiles, $table), $table, $delete);
						$delete = FALSE;
					}
					$sentence = ((!$primero)?"\r\n,":"{$sql5}\r\n") .$this->showQuery($sql4, $values);
					#$sentence = $sql5 . $this->showQuery($sql4, $values) .";\r\n";
					$primero = FALSE;
					fwrite($f, ($sentence));
					++$count;

					# Agrupación de registros
					++$group;
					if ($group > $this->_count)
					{
						$group = 0;
						if (!$this->end_file($this->get_filename($filename, $numfiles, $table), $f))
						{
							$this->_last_error = $this->obj->lang->line('mysql-import-error');
							return FALSE;
						}
						++$numfiles;
						$primero = TRUE;
					}
					gc_collect_cycles();
				}
				if ($group > 0)
				{
					if (!$this->end_file($this->get_filename($filename, $numfiles, $table), $f))
					{
						$this->_last_error = $this->obj->lang->line('mysql-import-error');
						return FALSE;
					}
					++$numfiles;
				}
			}
		}
		else
		{
			# La tabla no existe...
			
			$this->_last_error = ($driver == 'mssql')?mssql_get_last_message():mysqli_error($link);
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Indica el orden de la tabla para crear la secuencia
	 * @param int $num Número de secuencia
	 * @return null
	 */
	function setNumber($num)
	{
		$this->_number = $num;
	}

	/**
	 * Devuelve el último error generado por la librería
	 * @return string
	 */
	function get_last_error()
	{
		return $this->_last_error;
	}

	/**
	 * Llama a MySQL para importar los datos
	 * @param string $filename Fichero SQL a importar
	 * @param  Progress $progress Gestor de consola
	 * @return int >0 error
	 */
	private function mysql($filename, $progress = null)
	{
		$sql = utf8_encode(file_get_contents($filename));

		#$progress->text($sql);
        if (!$this->obj->db->multi_query($sql))
        {
			$this->_last_error = $this->obj->db->_error_message();
			return 1;
        }
		return 0;

		if (strpos($filename, '_DIRECT_')!==FALSE)
		{
			if ($progress) $progress->text($sql);
	        // Perform the query
	        if (!$this->obj->db->query($sql))
	        {
				$this->_last_error = $this->obj->db->_error_message();
				return 1;
	        }
			return 0;
		}
		$file2 = str_replace('.sql', '.2.sql', $filename);
		file_put_contents($file2, $sql);

		$templine = '';
		// Read in entire file
		$lines = file($file2);
		// Loop through each line
		$count = 1;
		foreach ($lines as $line)
		{
			if ($progress)
   					$progress->step2('SQL', $count, count($lines));
		    // Skip it if it's a comment
		    if (substr($line, 0, 2) == '--' || $line == '')
		        continue;
		 
		    // Add this line to the current segment
		    $templine .= $line;
		    // If it has a semicolon at the end, it's the end of the query
		    if (substr(trim($line), -1, 1) == ';')
		    {
		     
		     	$templine = trim($templine);
		     	if (substr(trim($line), strlen($templine) - 2, 2) == ';;')
		     		$templine = substr($templine, 0, strlen($templine) - 1);

		        // Perform the query

		        if (!$this->obj->db->query($templine))
		        {
					$this->_last_error = $this->obj->db->_error_message();
					return 1;
		        }

		        // Reset temp variable to empty
		        $templine = '';		   
		    }
		    ++$count;
		}
		return 0;
	}

	/**
	 * Importa las tablas que se encuentran en el fichero ZIP indicado
	 * @param string $zipfile Nombre del fichero
	 * @param  Progress $progress Gestor de consola
	 * @return bool
	 */
	function import_zip($zipfile, $progress = null)
	{
		set_time_limit(0);

		/*if (!isset($this->_path_mysql))
		{
			$this->_last_error = $this->obj->lang->line('no-mysql-bin');
			return FALSE;
		}*/
		if ($progress) $progress->text('Leyendo ZIP ' . $zipfile);

		$dir = time();		
		$path = DIR_TEMP_PATH . $dir;
		mkdir($path);
		if ($progress) $progress->text('Se descomprime en ' . $path);
		$this->obj->load->library('Zip2');
		if ($this->obj->zip2->open($zipfile) === TRUE)
		{
			$this->obj->zip2->extractTo($path);
			$this->obj->zip2->close();
			$files = array();
	        foreach(glob($path . '/*') as $file) 
	        {
	            if(!is_dir($file))	            
	            {
	            	$files[] = str_replace($path . '/', '', $file);
	            }
	            sort($files);
	        }

	        $count = 1;
	        foreach ($files as $file)
	        {
				if ($progress) 
					$progress->step($file, $count, count($files));
	        	$ext = pathinfo($path . '/' . $file, PATHINFO_EXTENSION);
	        	if ($ext == 'php')
	        	{
					if ($progress) $progress->text('Leyendo PHP ' . $file);
	        		require($path . '/' . $file);
	        	}
	        	else
	        	{
					if ($progress) $progress->text('Ejecutando SQL ' . $file);
		        	if ($this->mysql($path . '/' . $file, $progress) > 0)
		        	{
						$this->obj->utils->rrmdir(__DIR__ . '/temp/' . $dir);
		        		$this->_last_error = $this->obj->lang->line('mysql-import-error') . ' ' . $this->_last_error;
		        		return FALSE;
		        	}
		        }
		        ++$count;
	        }
			if ($progress) $progress->text('Borrando directorio temporal');
			$this->obj->utils->rrmdir(__DIR__ . '/temp/' . $dir);
		}
		else
		{				
			$this->_last_error = $this->obj->lang->line('zip-import-error');;
			return FALSE;
		}
		return TRUE;
	}

}
/* End of file ExportTables.php */
/* Location: ./system/libraries/ExportTables.php */