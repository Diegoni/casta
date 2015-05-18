<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

class Actualizar extends CommonObject
{
	var $db;
	// acciones
	var $action_insert		= 'insert';
	var $action_update		= 'update';
	
	// sistemas
	var $system_dolibar		= 'dolibar';
	var $system_prestashop	= 'prestashop';
	
	// tablas en base de datos para CLIENTES
	var $table_log_clientes	= 'tms_log_clientes'; //Guarda los cambios
	var $table_clientes_sin = 'tms_clientes_sin'; //Tabla de cruces 
	var $table_clientes_dol	= 'llx_societe';
	var $table_clientes_pre	= 'ps_customer';
	
	// tablas en base de datos para DIRECCIONES
	var $table_log_dir	= 'tms_log_direccion'; //Guarda los cambios
	var $table_dir_sin	= 'tms_direccion_sin'; //Tabla de cruces 
	var $table_dir_dol	= 'llx_socpeople';
	var $table_dir_pre	= 'ps_address';
		
	var $table_mod_clientes	= 'tms_mod_clientes';
	
	function __construct($db)
	{
		$this->db = $db;
	}
		
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion que permite actualizar terceros
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function actualizar_terceros()
	{
		$sql = "SELECT * FROM `$this->table_log_clientes` WHERE id_estado = 0";
	
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
						"INSERT INTO `$this->table_clientes_dol`(  
							`email`,
							`url`,
							`note_private`,
							`siren`,
							`nom`,
							`datec`,
							`status`,
							`client`,
							`id_sin`
						)VALUES	(
							'$objp->email',
							'$objp->website',
							'$objp->note',
							'$objp->cuil',
							'$objp->nombre',
							'$objp->date_upd',
							$objp->active,
							1,
							$objp->id_row
						);";
						
						$this->db->query($sql_insert);
	
						$id_registro = $this->db->last_insert_id("$this->table_clientes_dol");
						
						$sql_insert = 
						"INSERT INTO `$this->table_clientes_sin` (
							`id_ps_customer`,
							`id_llx_societe`
						)VALUES(
							$objp->id_row,
							$id_registro
						);";
						
						$this->db->query($sql_insert);
						
						$sql_update = 
						"UPDATE `$this->table_log_clientes` 
							SET 
								`id_estado` = 1
							WHERE 
								`$this->table_log_clientes`.`id_log` = $objp->id_log;";
							
						$this->db->query($sql_update);
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
						$sql_insert = 
						"INSERT INTO `$this->table_clientes_pre`(  
							`email`,
							`website`,
							`note`,
							`cuil`,
							`firstname`,
							`secure_key`,
							`date_add`,
							`date_upd`,
							`active`,
							`id_sin`
						)VALUES	(
							'-',
							'$objp->website',
							'$objp->note',
							'-',
							'$objp->nombre',
							'$objp->secure_key',
							'$objp->date_upd',
							'$objp->date_upd',
							'$objp->active',
							'$objp->id_row'
						);";
						
						$this->db->query($sql_insert);
	
						$id_registro = $this->db->last_insert_id("$this->table_clientes_pre");
						
						if($objp->address != '')
						{
							$ciudad = 'Mendoza'; //Mejorar esta parte
							
							$sql_insert = 
							"INSERT INTO `ps_address` (
								`id_country`, 
								`id_state`, 
								`id_customer`, 
								`address1`, 
								`postcode`, 
								`city`, 
								`phone`, 
								`date_add`, 
								`date_upd`, 
								`active`, 
								`deleted`
							) VALUES (
								44, 
								111, 
								$id_registro, 
								'$objp->address',
								'$objp->postcode',
								$ciudad,
								'$objp->phone',
								'$objp->date_upd',
								'$objp->date_upd',
								1,
								0
							);";
							
							$id_address = $this->db->last_insert_id("ps_address");
						}
						else
						{
							$id_address = 0;
						}
											
						$sql_insert = 
						"INSERT INTO `$this->table_clientes_sin` (
							`id_ps_customer`,
							`id_ps_address`,
							`id_llx_societe`
						)VALUES(
							$id_registro,
							$id_address,
							$objp->id_row
						);";
						
						$this->db->query($sql_insert);
						
						$sql_update = 
						"UPDATE `$this->table_log_clientes` 
							SET 
								`id_estado` = 1
							WHERE 
								`$this->table_log_clientes`.`id_log` = $objp->id_log;";
							
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
						$sql_update = "SELECT `id_llx_societe` FROM `$this->table_clientes_sin` WHERE `id_ps_customer` = $objp->id_row";
						$resql_update = $this->db->query($sql_update);
						
						$numr_update = $this->db->num_rows($resql_update);
						
						if($numr_update > 0)
						{				
							$objp_update = $this->db->fetch_array($resql_update);
							
							$sql_update = 
							"UPDATE `$this->table_clientes_dol` 
								SET 
									`email` 	= '$objp->email',
									`url` 		= '$objp->website',
									`note_private` 	= '$objp->note',
									`siren` 	= '$objp->cuil',
									`nom`		= '$objp->nombre',
									`datec` 	= '$objp->date_upd',
									`status` 	= $objp->active,
									`id_sin` 	= $objp->id_row
								WHERE 
									`$this->table_clientes_dol`.`rowid` = $objp_update[id_llx_societe];";
							
							$this->db->query($sql_update);
							
							$sql_update = 
							"UPDATE `$this->table_log_clientes` 
								SET 
									`id_estado` = 1
								WHERE 
									`$this->table_log_clientes`.`id_log` = $objp->id_log;";
							
							$this->db->query($sql_update);
						}
						 
					}
								
		/*----------------------------------------------------------------
				UPDATE desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
		
	 				else	
					if($objp->system == $this->system_dolibar)					
					{
						$sql_update = "SELECT * FROM `$this->table_clientes_sin` WHERE `id_llx_societe` = $objp->id_row";
						$resql_update = $this->db->query($sql_update);
						
						$numr_update = $this->db->num_rows($resql_update);
						
						if($numr_update > 0)
						{				
							$objp_update = $this->db->fetch_array($resql_update);
							
							$sql_registro = 
							"UPDATE `$this->table_clientes_pre` 
								SET 
									`email` 	= '$objp->email' ,
									`website` 	= '$objp->website',
									`note` 		= '$objp->note',
									`cuil` 		= '$objp->cuil',
									`firstname` = '$objp->nombre',
									`date_upd` 	= '$objp->date_upd',
									`active` 	= $objp->active,
									`id_sin` 	= $objp->id_row
								WHERE 
									`id_customer` = $objp_update[id_ps_customer];";
							
							$this->db->query($sql_registro);
							
							if($objp->address != NULL)
							{
								if($sql_update['id_ps_address'] == 0)
								{
									$ciudad = 'Mendoza'; //Mejorar esta parte
								
									$sql_insert = 
									"INSERT INTO `ps_address` (
										`id_country`, 
										`id_state`, 
										`id_customer`, 
										`address1`, 
										`postcode`, 
										`city`, 
										`phone`, 
										`date_add`, 
										`date_upd`, 
										`active`, 
										`deleted`
									) VALUES (
										44, 
										111, 
										$objp_update[id_ps_customer], 
										'$objp->address',
										'$objp->postcode',
										'$ciudad',
										'$objp->phone',
										'$objp->date_upd',
										'$objp->date_upd',
										1,
										0
									);";
										
									$this->db->query($sql_insert);	
										
									$id_address = $this->db->last_insert_id("ps_address");
									
									$sql_clientes_sin = 
									"UPDATE `$this->table_clientes_sin`
										SET  
											id_ps_address = $id_address
										WHERE 
											id_llx_societe = $objp->id_row";
									
									$this->db->query($sql_clientes_sin);
									
									echo "<br>".$sql_clientes_sin;
								}
							
								
							}
							
							$sql_update = 
							"UPDATE `$this->table_log_clientes` 
								SET 
									`id_estado` = 1
								WHERE 
									`$this->table_log_clientes`.`id_log` = $objp->id_log;";
							
							$this->db->query($sql_update);
						}	
					}
				}
							
				 
				$i++;
			}
		}
	
		$sql = "DELETE FROM `$this->table_log_clientes` WHERE `id_estado` = 0";
	
		$this->db->query($sql);	
		
		$sql = 
		"UPDATE `$this->table_mod_clientes` 
			SET 
				`clientes_dolibar` = 0, 
				`clientes_prestashop` = 0 
			WHERE 
				`id_row` = 1";
	
		$this->db->query($sql);
	}
	
	
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion que permite actualizar direcciones
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function actualizar_direcciones()
	{
		$sql = "SELECT * FROM `$this->table_log_dir` WHERE `id_estado` = 0";
	
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
							"INSERT INTO `$this->table_dir_dol`(
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
							
							$id_registro = $this->db->last_insert_id("$this->table_dir_dol");	
														
							$sql_insert = 
							"INSERT INTO `$this->table_dir_sin` (
								`id_ps_address`,
								`id_llx_socpeople`
							)VALUES(
								$objp->id_row,
								$id_registro
							);";
							
							$this->db->query($sql_insert);
							
							$sql_update = 
							"UPDATE `$this->table_log_dir` 
								SET 
									`id_estado` = 1
								WHERE 
									`$this->table_log_dir`.`id_log` = $objp->id_log;";
								
							$this->db->query($sql_update);
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
							
							$sql_insert = 
							"UPDATE `$this->table_clientes_sin`
								SET 
									`id_ps_address` = $objp->id_row
								WHERE
									`id_llx_societe` = $objp_id_row[id_llx_societe];";

							
							$this->db->query($sql_insert);
							
							$sql_update = 
							"UPDATE `$this->table_log_dir` 
								SET 
									`id_estado` = 1
								WHERE 
									`$this->table_log_dir`.`id_log` = $objp->id_log;";
									
							echo $sql_update."<br>";			
								
							$this->db->query($sql_update);							
						}
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
					
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
	
		$sql = "DELETE FROM `$this->table_log_dir` WHERE `id_estado` = 0";
	
		$this->db->query($sql);	
		
		$sql = 
		"UPDATE `$this->table_mod_clientes` 
			SET 
				`direcciones_dolibar` = 0, 
				`direcciones_prestashop` = 0 
			WHERE 
				`id_row` = 1";
	
		$this->db->query($sql);
		
	}

}
