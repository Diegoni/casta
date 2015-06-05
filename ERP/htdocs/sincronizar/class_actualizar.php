<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

class Actualizar extends CommonObject
{
	// acciones
	var $action_insert	= 'insert';
	var $action_update	= 'update';
	
	// sistemas
	var $system_dolibar	= 'dolibar';
	var $system_prestashop	= 'prestashop';
	
	// ambientes
	var $environment	= 'development';
	//var $environment	= 'production';
	
	// tabla 
	var $table_error	= 'tms_log_errores';
	var $table_pais_dol	= 'llx_c_country';
	var $table_pais_pre	= 'ps_country';
	var $table_dep_dol	= 'llx_c_departements';
	var $table_dep_pre	= 'ps_state';
		
	function __construct(
		$db, 
		$table_log,
		$table_sin,
		$id_sin_pre,
		$id_sin_dol,
		$table_mod,
		$subject
	)
	{
		$this->db			= $db;
		$this->table_log	= $table_log;
		$this->table_sin	= $table_sin;
		$this->id_sin_pre	= $id_sin_pre;
		$this->id_sin_dol	= $id_sin_dol;  
		$this->table_mod	= $table_mod;
		$this->subject		= $subject;
	}
		
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para hacer insert
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
		
	function insert_registro($table, $registro)
	{
		$campos		= "";
		$valores	= "";
		
		foreach ($registro as $key => $value) {
			$campos		.= "`".$key."` ,"; 
			$valores	.= $value." ,";		
		}
		
		$campos		= substr($campos, 0, -1);
		$valores	= substr($valores, 0, -1);
		
		$sql = 
		"INSERT INTO `$table`(
			$campos
		)VALUES	(
			$valores
		);";
		
		$this->view_sql($sql);
							
		$this->db->query($sql);
		
		$id_insert = $this->db->last_insert_id($table);
		
		return $id_insert;
	}
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para hacer update
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
		
	function update_registro($table, $registro, $where = NULL)
	{
		$updates	= " ";
		
		foreach ($registro as $key => $value) {
			$updates		.= "`".$key."` = ".$value." ,"; 
		}
		
		$updates = substr($updates, 0, -1);
		
		if($where == NULL)
		{
			$where = 1;
		}
		
		$sql = 
			"UPDATE `$table` 
				SET 
					$updates
				WHERE 
					$where";
		
		$this->view_sql($sql);		
							
		$this->db->query($sql);
	}
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para hacer select
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
		
	function get_registros($table, $where = NULL, $campos = NULL)
	{
		if($campos == NULL)
		{
			$campo_sql = '*';
		}
		else
		{
			if(is_array($campos))
			{
				$campo_sql = ' ';
				
				foreach ($campos as $value) {
					$campo_sql = "`".$value."` ,";					
				}
				
				$campo_sql = substr($campo_sql, 0, -1);
			}
			else
			{
				$campo_sql = $campos;
			}
			
		}
		
		if($where == NULL)
		{
			$where_sql = 1;
		}
		else
		{
			$where_sql = $where;
		}
		
		
		$sql =
		"SELECT $campo_sql FROM `$table`
			WHERE
				$where_sql
		;";
		
		$this->view_sql($sql);
							
		$resql = $this->db->query($sql);	
		$numr = $this->db->num_rows($resql);					
		
		if($numr > 0)
		{
			$registros = $this->db->fetch_array($resql);	
		}
		else
		{
			$registros = 0;
		}
		
		return $registros;
	}
				
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion que cambia el log
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
		
	function update_log($id)
	{
		$sql = 
			"UPDATE `$this->table_log` 
				SET 
					`id_estado` = 1
				WHERE 
					`$this->table_log`.`id_log` = $id;";
		
		$this->view_sql($sql);		
							
		$this->db->query($sql);
	}
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para insertar la sincronización
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
		
	function insert_sin($id_presta, $id_doli, $extra_field = NULL)
	{
		if($extra_field == NULL)
		{
			$sql = 
			"INSERT INTO `$this->table_sin` (
				`$this->id_sin_pre`,
				`$this->id_sin_dol`
			)VALUES(
				$id_presta,
				$id_doli
			);";			
		}
		else
		{
			if(is_array($extra_field))
			{
				$campos = "`".$this->id_sin_pre."`,";
				$valores = $id_presta." ,";
				
				foreach ($extra_field as $key => $value)
				{
					$campos .= "`".$key."`,";
					$valores .= $value." ,";
				}
				
				$campos .= "`".$this->id_sin_dol."`";
				$valores .= $id_doli;
				
				$sql = 
				"INSERT INTO `$this->table_sin` (
					$campos
				)VALUES(
					$valores
				);";	
			}
		}
		
		$this->view_sql($sql);
						
		$this->db->query($sql);
	}
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para insertar la sincronización
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function get_id_sin($id, $system, $extra_field = NULL)
	{
		if($system == 'dolibar' || $system == 'dol' || $system == 'doli')
		{
			$id_referencia	= $this->id_sin_pre;
			$id_buscado		= $this->id_sin_dol;
		}
		else
		if($system == 'prestashop' || $system == 'presta' || $system == 'pre')
		{
			$id_referencia	= $this->id_sin_dol;
			$id_buscado		= $this->id_sin_pre;
		}
		
		if($extra_field == NULL)
		{
			$sql = "SELECT `$id_buscado` FROM `$this->table_sin` WHERE `$id_referencia` = $id;";	
		}
		else
		{
			if(is_array($extra_field))
			{
				//Falta hacer para varios extra field	
			}
			else
			{
				$sql = "SELECT `$id_buscado`, `$extra_field` FROM `$this->table_sin` WHERE `$id_referencia` = $id;";
			}			
		}
		
		$this->view_sql($sql);
		
		$resql_update = $this->db->query($sql);
		
		$numr_update = $this->db->num_rows($resql_update);
						
		if($numr_update > 0)
		{				
			$objp_update = $this->db->fetch_array($resql_update);
			
			if($extra_field == NULL)
			{
				$id_return = $objp_update[$id_buscado];	
			}
			else
			{
				$id_return = $objp_update;
			}			
		}
		else
		{
			$id_return = 0;
		}	
		
		return $id_return;
						
	}

	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para sincronizar paises
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function getID_direccion($id, $system, $state)
	{
		if($system == $this->system_dolibar && $id == 23 && $state == 'country')
		{
			return 44;
		}
		else
		if($system == $this->system_prestashop && $id == 44 && $state == 'country')
		{
			return 23;
		}
		else
		{
			if($state == 'country')
			{
				if($system == $this->system_dolibar)
				{
					$datos = array(
						'table_bus'			=> $this->table_pais_dol,
						'table_return'		=> $this->table_pais_pre,
						'cadena_busqueda'	=> 'code',
						'id_busqueda'		=> 'rowid',
						'cadena_comparacion'=> 'iso_code',
						'id_return'			=> 'id_country',
						'return'			=> 44		//id de Argentina, en caso de no encontrarlo va como default
					);
				}
				else
				if($system == $this->system_prestashop)
				{
					$datos = array(
						'table_bus'			=> $this->table_pais_pre,
						'table_return'		=> $this->table_pais_dol,
						'cadena_busqueda'	=> 'iso_code',
						'id_busqueda'		=> 'id_country',
						'cadena_comparacion'=> 'code',
						'id_return'			=> 'rowid',
						'return'			=> 23		//id de Argentina, en caso de no encontrarlo va como default
					);
				}
			}	
			else
			if($state == 'state')
			{
				if($system == $this->system_dolibar)
				{
					$datos = array(
						'table_bus'			=> $this->table_dep_dol,
						'table_return'		=> $this->table_dep_pre,
						'cadena_busqueda'	=> 'nom',
						'id_busqueda'		=> 'rowid',
						'cadena_comparacion'=> 'name',
						'id_return'			=> 'id_state',
						'return'			=> 111		//id de Mendoza, en caso de no encontrarlo va como default
					);
				}
				else
				if($system == $this->system_prestashop )
				{
					$datos = array(
						'table_bus'			=> $this->table_dep_pre,
						'table_return'		=> $this->table_dep_dol,
						'cadena_busqueda'	=> 'name',
						'id_busqueda'		=> 'id_state',
						'cadena_comparacion'=> 'nom',
						'id_return'			=> 'rowid',
						'return'			=> 23		//id de Mendoza, en caso de no encontrarlo va como default
					);
				}
			}
							
			// 01 - Buscamos cadena de comparacion  	
				
			$sql = "SELECT $datos[cadena_busqueda] FROM `$datos[table_bus]` WHERE $datos[id_busqueda] = $id";	
			
			$this->view_sql($sql);
				
			$resql_code = $this->db->query($sql);
			
			$numr_code = $this->db->num_rows($resql_code);
				
			if($numr_code > 0)
			{
				$objp_code = $this->db->fetch_array($resql_code);
				
				$cadena = $objp_code[$datos['cadena_busqueda']];
				
				// 02 - Buscamos en la otra tabla la cadena 
				
				$sql = "SELECT $datos[id_return] FROM `$datos[table_return]` WHERE $datos[cadena_comparacion] = '$cadena'";
				
				$this->view_sql($sql);
							
				$resql_country = $this->db->query($sql);
				
				$numr_country = $this->db->num_rows($resql_country);
					
				if($numr_country > 0)
				{
					$objp_country = $this->db->fetch_array($resql_country);
					
					$datos['return'] = $objp_country[$datos['id_return']];
				}
				else
				{
					$this->log_error('cero_sql', $sql);
				}
			}
			else
			{
				$this->log_error('cero_sql', $sql);
			}
				
	
			return $datos['return'];		
		}		
	}
	
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para borrar log repetidos
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function delete_log($table = NULL)
	{
		if($table == NULL)
		{
			$sql = "DELETE FROM `$this->table_log` WHERE `id_estado` = 0";	
		}
		else
		{
			$sql = "DELETE FROM `$table` WHERE `id_estado` = 0";	
		}
		
		$this->view_sql($sql);	
			
		$this->db->query($sql);	
	}
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para insert de errores
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function log_error($error, $objp = NULL)
	{
		$date = date('Y/m/d H:s:i');
		
		if($error == 'no_sin')
		{
			if($objp->system == $this->system_prestashop)
			{
				$campo = $this->id_sin_pre;
			}
			else
			{
				$campo = $this->id_sin_dol;
			}
			
			$error = 'No hay se ha encotrado '.$campo.' = '.$objp->id_row;
			$error .= ' , en la tabla '.$this->table_sin;
			$error .= ' cuando se actualizó '.$this->subject.' en '.$objp->system;
		}
		else
		if($error == 'cero_sql')
		{
			$objp = str_replace("'", "`", $objp);
			$objp = str_replace('"', '`', $objp);
			$error = 'La consulta " '.$objp.' " no encontró registros"';
		}
		
		
		$sql = 
		"INSERT INTO `$this->table_error`(
			error,
			date_add,
			date_upd,
			id_estado
		)VALUES	(
			'$error',
			'$date',
			'$date',
			1
		);";
		
		$this->view_sql($sql);	
			
		$this->db->query($sql);	
	}
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para reset las tablas mod
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function reset_mod()
	{
		$dolibar	= $this->subject.'_'.$this->system_dolibar;
		$prestashop	= $this->subject.'_'.$this->system_prestashop;
		
		$sql = 
		"UPDATE `$this->table_mod` 
			SET 
				`$dolibar`		= 0, 
				`$prestashop`	= 0 
			WHERE 
				`id_row` = 1";
		
		$this->view_sql($sql);
	
		$this->db->query($sql);	
	}	
		
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para ver las consultas a base de datos
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function view_sql($sql)
	{
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";
		}
	}

}
