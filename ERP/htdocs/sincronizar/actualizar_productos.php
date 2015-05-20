<?php
require_once DOL_DOCUMENT_ROOT.'/sincronizar/class_actualizar.php';

class Actualizar_productos extends Actualizar
{
	var $subject		= 'productos'; 
	
	// tablas en base de datos para PRODUCTOS
	var $table_log		= 'tms_log_productos'; //Guarda los cambios
	var $table_sin		= 'tms_productos_sin'; //Tabla de cruces 
	var $table_dol		= 'llx_product';
	var $table_pre		= 'ps_product';
	var $table_mod		= 'tms_mod_productos';
	
	// campos en tablas
	var $id_sin_dol		= 'id_llx_product';
	var $id_sin_pre		= 'id_ps_product';
	var $id_table_dol	= 'rowid';
	var $id_table_pre	= 'id_product';
	
	function __construct($db)
	{
		$this->db = $db;
		
		parent::__construct(
				$db				= $this->db, 
				$table_log		= $this->table_log,
				$table_sin		= $this->table_sin,
				$id_sin_pre		= $this->id_sin_pre,
				$id_sin_dol		= $this->id_sin_dol,
				$table_mod		= $this->table_mod, 
				$subject		= $this->subject
		);
	}
		
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion que permite actualizar productos
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function actualizar()
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
							`id_sin`,
							`ref`,
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
							'$objp->ref',
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
							'$objp->date_add'
						);";
						
						$this->db->query($sql_insert);
	
						$id_registro = $this->db->last_insert_id("$this->table_dol");
						
						$this->insert_sin($objp->id_row, $id_registro);
						
						$this->update_log($objp->id_log);
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
							`reference`,
							`price`,
							`wholesale_price`,
							`ean13`,
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
							'$objp->ref',
							'$objp->price',
							'$objp->price_min',
							'$objp->code_sell',
							'$objp->barcode',
							'$objp->weight',
							'$objp->width',
							'$objp->height',
							'$objp->depth',
							'$objp->active',
							'$objp->tva',
							'$objp->date_add'
						);";
						
						echo $sql_insert."<br>";
						
						$this->db->query($sql_insert);
	
						$id_registro = $this->db->last_insert_id("$this->table_pre");
						
						$this->insert_sin($id_registro, $objp->id_row);
						
						$this->update_log($objp->id_log);						
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
						$id_registro = $this->get_id_sin($objp->id_row, 'dolibar');
						
						if($id_registro != 0)
						{				
							$sql_update = 
							"UPDATE `$this->table_dol`  
								SET
									`id_sin`	= $objp->id_row,
									`ref`		= '$objp->ref',
									`label`		= '$objp->name',
									`description` = '$objp->description_short',
									`price`		= '$objp->price',
									`price_min`	= '$objp->price_min',
									`accountancy_code_sell` = '$objp->code_sell',
									`accountancy_code_buy` = '$objp->code_buy',
									`barcode`	= '$objp->barcode',
									`weight`	= '$objp->weight',
									`length`	= '$objp->width',
									`surface`	= '$objp->height',
									`volume`	= '$objp->depth',
									`tosell`	= '$objp->active',
									`tva_tx`	= '$objp->tva',
									`datec`		= '$objp->date_add'
							WHERE 
									`$this->table_dol`.`$this->id_table_dol` = $id_registro";
									
							echo $sql_update."<br>";
								
							$this->db->query($sql_update);
							
							$this->update_log($objp->id_log);
						}
					}
								
		/*----------------------------------------------------------------
				UPDATE desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
		
	 				else	
					if($objp->system == $this->system_dolibar)					
					{
						$id_registro = $this->get_id_sin($objp->id_row, 'prestashop');
						
						if($id_registro > 0)
						{				
							$sql_update = 
							"UPDATE `$this->table_pre`  
								SET
									`id_sin`	= $objp->id_row,
									`reference`	= '$objp->ref',
									`price`		= '$objp->price',
									`wholesale_price` = '$objp->price_min',
									`ean13`		= '$objp->code_sell',
									`upc`		= '$objp->barcode',
									`weight`	= '$objp->weight',
									`width`		= '$objp->width',
									`height`	= '$objp->height',
									`depth`		= '$objp->depth',
									`active`	= '$objp->active',
									`id_tax_rules_group` = '$objp->tva',
									`date_upd`	= '$objp->date_add' 
							WHERE 
									`$this->table_dol`.`$this->id_table_pre` = $objp->id_log;";
									
							echo $sql_update."<br>";
								
							$this->db->query($sql_update);
							
							$this->update_log($objp->id_log);
						}
		
					}
				}
							
				 
				$i++;
			}
		}
	
		$this->delete_log();
			
		$this->reset_mod();
	}
		
}
