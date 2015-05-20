<?php
require_once DOL_DOCUMENT_ROOT.'/sincronizar/class_actualizar.php';

class Actualizar_direcciones extends Actualizar
{
	var $subject		= 'direcciones';
	
	// tablas en base de datos para DIRECCIONES
	var $table_log		= 'tms_log_direccion'; //Guarda los cambios
	var $table_sin		= 'tms_direccion_sin'; //Tabla de cruces 
	var $table_dol		= 'llx_socpeople';
	var $table_pre		= 'ps_address';
	var $table_mod		= 'tms_mod_clientes';
	
	// tablas en base de datos para CLIENTES
	var $table_clientes_sin = 'tms_clientes_sin'; //Tabla de cruces 
	var $table_clientes_dol	= 'llx_societe';
	var $table_clientes_pre	= 'ps_customer';
		
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
	
	 		Funcion que permite actualizar direcciones
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function actualizar()
	{
		$sql = "SELECT * FROM `$this->table_log` WHERE `id_estado` = 0";
	
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
						$sql_sin = "SELECT * FROM `$this->table_clientes_sin` WHERE `id_ps_customer` = $objp->id_cliente";
						
						$resql_sin = $this->db->query($sql_sin);
						$objp_id_row = $this->db->fetch_array($resql_sin);
																		
						if($objp_id_row['id_ps_address'] != 0)
						{
							$sql_id_row = "SELECT `id_llx_societe` FROM `$this->table_clientes_sin` WHERE `id_ps_customer` = $objp->id_cliente";
							
							$resql_id_row = $this->db->query($sql_id_row);
							
							$objp_id_row = $this->db->fetch_array($resql_id_row);
							
							$sql_insert =
							"INSERT INTO `$this->table_dol`(
								`fk_soc`,
								`id_sin`, 
								`firstname`, 
								`lastname`, 
								`address`, 
								`zip`, 
								`town`, 
								`phone`, 
								`phone_mobile`, 
								`datec`, 
								`poste`, 
								`statut`,
								`fk_user_creat`
							)VALUES(
								$objp_id_row[id_llx_societe],
								$objp->id_row,
								'$objp->firstname',
								'$objp->lastname',
								'$objp->address',
								'$objp->postcode',
								'$objp->city',
								'$objp->phone',
								'$objp->phone_mobile',
								'$objp->date_add',
								'$objp->alias',
								$objp->active,
								1
							);";
							
							$this->db->query($sql_insert);
							
							$id_registro = $this->db->last_insert_id("$this->table_dol");	
							
							$this->insert_sin($objp->id_row, $id_registro);
							
							$this->update_log($objp->id_log);
						}
						else
						{
							$sql_insert =
							"UPDATE `$this->table_clientes_dol`
								SET 
									`address`	= '$objp->address',
									`zip`		= '$objp->postcode',
									`town`		= '$objp->city',
									`phone`		= '$objp->phone',
									`id_sin`	= $objp->id_row
								WHERE 
									`rowid` 	= $objp_id_row[id_llx_societe];";
							
							$this->db->query($sql_insert);
							
							$sql_insert_sin = 
							"UPDATE `$this->table_clientes_sin`
								SET 
									`id_ps_address` = $objp->id_row
								WHERE
									`id_llx_societe` = $objp_id_row[id_llx_societe];";
														
							$this->db->query($sql_insert_sin);
							
							$this->update_log($objp->id_log);						
						}
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
						$sql_id_row = "SELECT `id_ps_customer` FROM `$this->table_clientes_sin` WHERE `id_llx_societe` = $objp->id_cliente";
							
						$resql_id_row = $this->db->query($sql_id_row);
							
						$objp_id_row = $this->db->fetch_array($resql_id_row);
						
						$sql_insert =
						"INSERT INTO `$this->table_pre`(
							`id_customer`,
							`id_sin`, 
							`firstname`, 
							`lastname`,
							`id_country`, 
							`id_state`,
							`address1`, 
							`postcode`, 
							`city`, 
							`phone`, 
							`phone_mobile`, 
							`date_add`, 
							`alias`, 
							`active` 
						)VALUES(
							$objp_id_row[id_ps_customer],
							$objp->id_row,
							'$objp->firstname',
							'$objp->lastname',
							44,
							111,
							'-',
							'$objp->postcode',
							'$objp->city',
							'$objp->phone',
							'$objp->phone_mobile',
							'$objp->date_add',
							'-',
							$objp->active
						);";
						 						
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
						$sql_id_row = "SELECT `id_llx_societe` FROM `$this->table_clientes_sin` WHERE `id_ps_address` = $objp->id_row";
													
						$resql_id_row = $this->db->query($sql_id_row);
						$numr_id_row = $this->db->num_rows($resql_id_row);
						
						if($numr_id_row > 0)
						{
							$objp_id_row = $this->db->fetch_array($resql_id_row);	
							
							$sql_update =
							"UPDATE `$this->table_clientes_dol`
								SET 
									`address`	= '$objp->address',
									`zip`		= '$objp->postcode',
									`town`		= '$objp->city',
									`phone`		= '$objp->phone',
									`id_sin`	= '$objp->id_cliente'
								WHERE 
									`rowid` 	= $objp_id_row[id_llx_societe];";
							
							$this->db->query($sql_update);	
							
							$this->update_log($objp->id_log);
						}
						else
						{
							$id_registro = $this->get_id_sin($objp->id_row, 'dolibar');
																					
							if($numr_row > 0)
							{
								$sql_update =
								"UPDATE `$this->table_dol`
									SET
										`id_sin`	= $objp->id_row, 
										`firstname`	= '$objp->firstname', 
										`lastname`	= '$objp->lastname', 
										`address`	= '$objp->address', 
										`zip`		= '$objp->postcode', 
										`town`		= '$objp->city', 
										`phone`		= '$objp->phone', 
										`phone_mobile` = '$objp->phone_mobile', 
										`datec`		= '$objp->date_add', 
										`poste`		= '$objp->alias', 
										`statut`	= $objp->active
									WHERE
										`rowid` 	= $id_registro;";
								
								$this->db->query($sql_update);	
								
								$this->update_log($objp->id_log);		
							}	
						}	
					}
								
		/*----------------------------------------------------------------
				UPDATE desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
		
	 				else	
					if($objp->system == $this->system_dolibar)					
					{
						$id_registro = $this->get_id_sin($objp->id_row, 'prestashop');
						
						if($numr_row > 0)
						{
							$sql_update =
							"UPDATE `$this->table_pre`
								SET	
									`id_sin`		= $objp->id_row, 
									`firstname`		= '$objp->firstname', 
									`lastname`		= '$objp->lastname',
									`id_country`	= 44, 
									`id_state`		= 111,
									`address1`		= '$objp->address', 
									`postcode`		= '$objp->postcode',
									`city`			= '$objp->city', 
									`phone`			= '$objp->phone', 
									`phone_mobile`	= '$objp->phone_mobile', 
									`date_add`		= '$objp->date_add', 
									`alias`			= '$objp->alias', 
									`active`		= $objp->active 
								WHERE
									`id_address` 	= $id_registro;";
								
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
