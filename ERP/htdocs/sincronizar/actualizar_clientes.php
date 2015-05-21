<?php
require_once DOL_DOCUMENT_ROOT.'/sincronizar/class_actualizar.php';

class Actualizar_clientes extends Actualizar
{
	var $subject		= 'clientes'; 
	
	// tablas en base de datos para CLIENTES
	var $table_log		= 'tms_log_clientes'; //Guarda los cambios
	var $table_sin 		= 'tms_clientes_sin'; //Tabla de cruces 
	var $table_dol		= 'llx_societe';
	var $table_pre		= 'ps_customer';
	var $table_mod		= 'tms_mod_clientes';
	
	// campos en tablas
	var $id_sin_dol		= 'id_llx_societe';
	var $id_sin_pre		= 'id_ps_customer';
	var $id_table_dol	= 'rowid';
	var $id_table_pre	= 'id_customer';
	
	// tablas en base de datos para DIRECCIONES
	var $table_dir_pre	= 'ps_address';
	var $table_log_dir	= 'tms_log_direccion'; //Guarda los cambios direcciones
	
	// campos en tablas
	var $id_sin_dir_pre	= 'id_ps_address';
	
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
	
	 		Funcion que permite actualizar terceros
	 
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
	
						$id_registro = $this->db->last_insert_id("$this->table_pre");
						
						if($objp->address != '')
						{
							$ciudad = 'Mendoza'; //Mejorar esta parte
							
							$sql_insert = 
							"INSERT INTO `$this->table_dir_pre` (
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
							
							$id_address = $this->db->last_insert_id("$this->table_dir_pre");
						}
						else
						{
							$id_address = 0;
						}
						
						$extra_field[$this->id_sin_dir_pre] = $id_address;
						
						$this->insert_sin($id_registro, $objp->id_row , $extra_field);
						
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
						$id_registro = $this->get_id_sin($objp->id_row, $this->system_dolibar);
						
						if($id_registro != 0)
						{				
							$sql_update = 
							"UPDATE `$this->table_dol` 
								SET 
									`email` 	= '$objp->email',
									`url` 		= '$objp->website',
									`note_private` = '$objp->note',
									`siren` 	= '$objp->cuil',
									`nom`		= '$objp->nombre',
									`datec` 	= '$objp->date_upd',
									`status` 	= $objp->active,
									`id_sin` 	= $objp->id_row
								WHERE 
									`$this->table_dol`.`$this->id_table_dol` = $id_registro;";
							
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
						$id_registro = $this->get_id_sin($objp->id_row, $this->system_prestashop, $this->id_sin_dir_pre);
						
						if(is_array($id_registro))
						{
							$id_cliente = $id_registro[$this->id_sin_pre];
							
							$sql_registro = 
							"UPDATE `$this->table_pre` 
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
									`$this->table_pre`.`$this->id_table_pre` = $id_cliente;";
							
							$this->db->query($sql_registro);
							
							if($objp->address != NULL)
							{	
								if($id_registro[$this->id_sin_dir_pre] == 0)
								{
									$ciudad = 'Mendoza'; //Mejorar esta parte
								
									$sql_insert = 
									"INSERT INTO `$this->table_dir_pre` (
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
										'$ciudad',
										'$objp->phone',
										'$objp->date_upd',
										'$objp->date_upd',
										1,
										0
									);";
										
									$this->db->query($sql_insert);	
										
									$id_address = $this->db->last_insert_id("$this->table_dir_pre");
									
									$sql_clientes_sin = 
									"UPDATE `$this->table_sin`
										SET  
											`$this->id_sin_dir_pre` = $id_address
										WHERE 
											`$this->id_sin_dol` = $objp->id_row";
									
									$this->db->query($sql_clientes_sin);
								}
								else
								{
									$sql_update =
									"UPDATE `$this->table_dir_pre`
										SET 
											`address1`	= '$objp->address',
											`postcode`	= '$objp->postcode',
											`city`		= '$objp->city',
											`phone`		= '$objp->phone',
											`id_sin`	= '$objp->id_cliente'
										WHERE 
											`id_address` = $id_registro[id_ps_address];";
									
									$this->db->query($sql_update);	
								}
								
							}
							
							$this->update_log($objp->id_log);
						}	
					}
				}

				$i++;
			}
		}
	
		$this->delete_log();
		$this->delete_log($this->tms_log_direccion);
			
		$this->reset_mod();
	}	
}
