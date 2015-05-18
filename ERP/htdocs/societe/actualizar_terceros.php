<?php
function actualizar_terceros($db)
{
	$sql = "SELECT * FROM `tms_log_clientes` WHERE id_estado = 0";

	$resql = $db->query($sql);
	$numr = $db->num_rows($resql);
	$i = 0;
	
	$action_insert		= 'insert';
	$action_update		= 'update';
	
	$system_dolibar		= 'dolibar';
	$system_prestashop	= 'prestashop';
	
	if($numr > 0)
	{				
		while ($i < $numr)
		{
			$objp = $db->fetch_object($resql);
			
/*----------------------------------------------------------------
		INSERT desde PRESTASHOP 
----------------------------------------------------------------*/
 			
			if($objp->action == $action_insert)
			{
				if($objp->system == $system_prestashop)
				{
					$sql_insert = 
					"INSERT INTO `llx_societe`(  
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
					
					$db->query($sql_insert);

					$id_registro = $db->last_insert_id("llx_societe");
					
					$sql_insert = 
					"INSERT INTO `tms_clientes_sin` (
						`id_ps_customer`,
						`id_llx_societe`
					)VALUES(
						$objp->id_row,
						$id_registro
					);";
					
					$db->query($sql_insert);
					
					$sql_update = 
					"UPDATE `tms_log_clientes` SET 
						`id_estado` = 1
					WHERE `tms_log_clientes`.`id_row` = $objp->id_row;";
						
					$db->query($sql_update);
				}
				
/*----------------------------------------------------------------
		INSERT desde DOLIBAR 
----------------------------------------------------------------*/
 				
				else	
				if($objp->system == $system_dolibar)					
				{
					$sql_insert = 
					"INSERT INTO `ps_customer`(  
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
					
					$db->query($sql_insert);

					$id_registro = $db->last_insert_id("ps_customer");
					
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
						
						$id_address = $db->last_insert_id("ps_address");
					}
					else
					{
						$id_address = 0;
					}
										
					$sql_insert = 
					"INSERT INTO `tms_clientes_sin` (
						`id_ps_customer`,
						`id_ps_address`,
						`id_llx_societe`
					)VALUES(
						$id_registro,
						$id_address,
						$objp->id_row
					);";
					
					$db->query($sql_insert);
					
					$sql_update = 
					"UPDATE `tms_log_clientes` SET 
						`id_estado` = 1
					WHERE `tms_log_clientes`.`id_row` = $objp->id_row;";
						
					$db->query($sql_update);
				}
			}
			
/*----------------------------------------------------------------
		UPDATE desde PRESTASHOP 
----------------------------------------------------------------*/
 
			else 
			if($objp->action == $action_update)	
			{
				if($objp->system == $system_prestashop)
				{
					$sql_update = "SELECT id_llx_societe FROM `tms_clientes_sin` WHERE id_ps_customer = $objp->id_row";
					$resql_update = $db->query($sql_update);
					
					$numr_update = $db->num_rows($resql_update);
					
					if($numr_update > 0)
					{				
						$objp_update = $db->fetch_array($resql_update);
						
						$sql_update = 
						"UPDATE `llx_societe` SET 
							`email` 	= '$objp->email',
							`url` 		= '$objp->website',
							`note_private` 	= '$objp->note',
							`siren` 	= '$objp->cuil',
							`nom`		= '$objp->nombre',
							`datec` 	= '$objp->date_upd',
							`status` 	= $objp->active,
							`id_sin` 	= $objp->id_row
						WHERE `llx_societe`.`rowid` = $objp_update[id_llx_societe];";
						
						$db->query($sql_update);
						
						$sql_update = 
						"UPDATE `tms_log_clientes` SET 
							`id_estado` = 1
						WHERE `tms_log_clientes`.`id_row` = $objp->id_row;";
						
						$db->query($sql_update);
					}
					 
				}
							
/*----------------------------------------------------------------
		UPDATE desde DOLIBAR 
----------------------------------------------------------------*/
 				else	
				if($objp->system == $system_dolibar)					
				{
					$sql_update = "SELECT * FROM `tms_clientes_sin` WHERE id_llx_societe = $objp->id_row";
					$resql_update = $db->query($sql_update);
					
					$numr_update = $db->num_rows($resql_update);
					
					if($numr_update > 0)
					{				
						$objp_update = $db->fetch_array($resql_update);
						
						$sql_registro = 
						"UPDATE `ps_customer` SET 
							`email` 	= '$objp->email' ,
							`website` 	= '$objp->website',
							`note` 		= '$objp->note',
							`cuil` 		= '$objp->cuil',
							`firstname` = '$objp->nombre',
							`date_upd` 	= '$objp->date_upd',
							`active` 	= $objp->active,
							`id_sin` 	= $objp->id_row
						WHERE `id_customer` = $objp_update[id_ps_customer];";
						
						$db->query($sql_registro);
						
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
									
								$db->query($sql_insert);	
									
								$id_address = $db->last_insert_id("ps_address");
								
								$sql_clientes_sin = 
								"UPDATE `tms_clientes_sin`
								SET  id_ps_address = $id_address
								WHERE 
								id_llx_societe = $objp->id_row";
								
								$db->query($sql_clientes_sin);
								
								echo "<br>".$sql_clientes_sin;
							}
						
							
						}
						
						$sql_update = 
						"UPDATE `tms_log_clientes` SET 
							`id_estado` = 1
						WHERE `tms_log_clientes`.`id_row` = $objp->id_row;";
						
						$db->query($sql_update);
					}	
				}
			}
						
			 
			$i++;
		}
	}

	$sql = "DELETE FROM `tms_log_clientes` WHERE id_estado = 0";

	$resql = $db->query($sql);	
	
	$sql = "UPDATE `tms_mod_clientes` SET clientes_dolibar = 0, clientes_prestashop = 0 WHERE id_row = 1";

	$resql = $db->query($sql);
}
