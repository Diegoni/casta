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
	var $environment		= 'development';
	//var $environment		= 'production';
		
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
		
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";	
		}			
							
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
		$updates	= "";
		
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
		
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";
		}			
							
		$this->db->query($sql);
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
		
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";
		}			
							
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
		
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";
		}	
						
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
		
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";
		}
		
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
		
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";
		}		
			
		$this->db->query($sql);	
	}
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion para borrar log repetidos
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function reset_mod()
	{
		$dolibar = $this->subject.'_dolibar';
		$prestashop = $this->subject.'_prestashop';
		
		$sql = 
		"UPDATE `$this->table_mod` 
			SET 
				`$dolibar`		= 0, 
				`$prestashop`	= 0 
			WHERE 
				`id_row` = 1";
		
		if($this->environment == 'development')
		{
			echo $sql."<br><hr>";
		}	
	
		$this->db->query($sql);	
	}	

}
