<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

class Actualizar_productos extends CommonObject
{
	var $db;
	// acciones
	var $action_insert		= 'insert';
	var $action_update		= 'update';
	
	// sistemas
	var $system_dolibar		= 'dolibar';
	var $system_prestashop	= 'prestashop';
	
	// tablas en base de datos para PRODUCTOS
	var $table_log	= 'tms_log_productos'; //Guarda los cambios
	var $table_sin	= 'tms_productos_sin'; //Tabla de cruces 
	var $table_dol	= 'llx_societe';
	var $table_pre	= 'ps_customer';
	
	var $table_mod	= 'tms_mod_pro';
	
	// campos en las tablas
	var $id_sin_dol = 'id_llx_societe';
	var $id_sin_pre = 'id_ps_customer';
	
	function __construct($db)
	{
		$this->db = $db;
	}
		
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion que permite actualizar productos
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function actualizar_productos()
	{
		$sql = "SELECT * FROM `$this->table_log` WHERE id_estado = 0";
	
		$resql = $this->db->query($sql);
		$numr = $this->db->num_rows($resql);
		$i = 0;
		
		if($numr > 0)
		{				
			while ($i < $numr)
			{
				$objp = $this->db->fetch_object($resql);
				
		/*----------------------------------------------------------------
				INSERT desde PRESTASHOP actualizo DOLIBAR
		----------------------------------------------------------------*/
	 			
				if($objp->action == $this->action_insert)
				{
					if($objp->system == $this->system_prestashop)
					{
						$sql_insert = 
						"INSERT INTO `$this->table_dol`(  
							`is_sin`,
							`label`,
							`description`,
							`price`,
							`price_min`,
							`accountancy_code_sell`,
							`accountancy_code_buy`,
							`barcode`,
							`weight`,
							`length`,
							`surface`,
							`volume`,
							`tosell`,
							`tva_tx`,
							`datec`
						)VALUES	(
							$objp->id_row,
							'$objp->name',
							'$objp->description_short',
							'$objp->price',
							'$objp->price_min',
							'$objp->code_sell',
							'$objp->code_buy',
							'$objp->barcode',
							'$objp->weight',
							'$objp->width',
							'$objp->height',
							'$objp->depth',
							'$objp->active',
							'$objp->tva',
							'$objp->date_add',
						);";
						
						echo $sql_insert."<br>";
						
						$this->db->query($sql_insert);
	
						$id_registro = $this->db->last_insert_id("$this->table_dol");
						
						$sql_insert = 
						"INSERT INTO `$this->table_sin` (
							`$this->id_sin_pre`,
							`$this->id_sin_dol`
						)VALUES(
							$objp->id_row,
							$id_registro
						);";
						
						echo $sql_insert."<br>";
						
						$this->db->query($sql_insert);
						
						$sql_update = 
						"UPDATE `$this->table_log` 
							SET 
								`id_estado` = 1
							WHERE 
								`$this->table_log`.`id_log` = $objp->id_log;";
							
						$this->db->query($sql_update);
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
						$sql_insert = 
						"INSERT INTO `$this->table_pre`(  
							`id_sin`,
							`price`,
							`wholesale_price`,
							`ean13`,
							`upc`,
							`upc`,
							`weight`,
							`width`,
							`height`,
							`depth`,
							`active`,
							`id_tax_rules_group`,
							`date_upd`
						)VALUES	(
							$objp->id_row,
							'$objp->price',
							'$objp->price_min',
							'$objp->code_sell',
							'$objp->code_buy',
							'$objp->barcode',
							'$objp->weight',
							'$objp->width',
							'$objp->height',
							'$objp->depth',
							'$objp->active',
							'$objp->tva',
							'$objp->date_add',
						);";
						
						/*
						'$objp->name',
						'$objp->description_short',
						*/	
						
						echo $sql_insert."<br>";
						
						$this->db->query($sql_insert);
	
						$id_registro = $this->db->last_insert_id("$this->table_pre");
						
						$sql_insert = 
						"INSERT INTO `$this->table_sin` (
							`$this->id_sin_pre`,
							`$this->id_sin_dol`
						)VALUES(
							$id_registro,
							$objp->id_row
						);";
						
						echo $sql_insert."<br>";
						
						$this->db->query($sql_insert);
						
						$sql_update = 
						"UPDATE `$this->table_log` 
							SET 
								`id_estado` = 1
							WHERE 
								`$this->table_log`.`id_log` = $objp->id_log;";
							
						$this->db->query($sql_update);						
					}						
				}
				
		/*----------------------------------------------------------------
				UPDATE desde PRESTASHOP actualizo DOLIBAR
		----------------------------------------------------------------*/
	 
				else 
				if($objp->action == $this->action_update)	
				{
					if($objp->system == $this->system_prestashop)
					{
						 
					}
								
		/*----------------------------------------------------------------
				UPDATE desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
		
	 				else	
					if($objp->system == $this->system_dolibar)					
					{
						
					}
				}
							
				 
				$i++;
			}
		}
	/*
		$sql = "DELETE FROM `$this->table_log` WHERE `id_estado` = 0";
	
		$this->db->query($sql);	
		
		$sql = 
		"UPDATE `$this->table_mod` 
			SET 
				`productos_dolibar`		= 0, 
				`productos_prestashop`	= 0 
			WHERE 
				`id_row` = 1";
	
		$this->db->query($sql);
	  
	*/
	}
	
}
