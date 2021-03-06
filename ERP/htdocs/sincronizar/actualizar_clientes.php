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
	
	 		Función que permite actualizar terceros
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function actualizar()
	{
		$sql	= "SELECT * FROM `$this->table_log` WHERE id_estado = 0";
	
		$resql	= $this->db->query($sql);
		$numr	= $this->db->num_rows($resql);
		$i		= 0;
		
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
						$nombre = $this->armar_nombre($objp->nombre, $objp->apellido); 
						
						$registro = array(
							'email'			=> "'".$objp->email."'",
							'url'			=> "'".$objp->website."'",
							'note_private'	=> "'".$objp->note."'",
							'siren'			=> "'".$objp->cuil."'",
							'nom'			=> "'".$nombre['nombre']."'",
							'nom_cant'		=> "'".$nombre['nombre_cantidad']."'",
							'datec'			=> "'".$objp->date_upd."'",
							'status'		=> $objp->active,
							'client'		=> 1,
							'id_sin'		=> $objp->id_row
						);
						
						$id_registro = $this->insert_registro($this->table_dol, $registro);
						
						$this->insert_sin($objp->id_row, $id_registro);
						
						$this->update_log($objp->id_log);
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
						if($objp->email == NULL)
						{
							$email = '-';
						}
						else
						{
							$email = strtolower($objp->email);	
						}						
						
						$registro = array(
							'email'			=> "'".$email."'",
							'website'		=> "'".$objp->website."'",
							'note'			=> "'".$objp->note."'",
							'cuil'			=> "'-'",
							'firstname'		=> "'".$objp->nombre."'",
							'secure_key'	=> "'".$objp->secure_key."'",
							'date_add'		=> "'".$objp->date_upd."'",
							'date_upd'		=> "'".$objp->date_upd."'",
							'active'		=> "'".$objp->active."'",
							'id_sin'		=> "'".$objp->id_row."'"
						);
						
						$id_registro = $this->insert_registro($this->table_pre, $registro);
						
						if($objp->address != '')
						{
							$ciudad = 'Mendoza'; //Mejorar esta parte
							
							$id_country = $this->getID_direccion($objp->id_country, $this->system_prestashop, 'country');
							$id_state	= $this->getID_direccion($objp->id_state, $this->system_prestashop, 'state');
									
							$registro = array(
								'id_country'	=> $id_country,
								'id_state'		=> $id_state,
								'id_customer'	=> $id_registro, 
								'address1'		=> "'".$objp->address."'",
								'postcode'		=> "'".$objp->postcode."'",
								'city'			=> $ciudad,
								'phone'			=> "'".$objp->phone."'",
								'date_add'		=> "'".$objp->date_upd."'",
								'date_upd'		=> "'".$objp->date_upd."'",
								'active'		=> 1,
								'deleted'		=> 0
							);
							
							$id_address = $this->insert_registro($this->table_dir_pre, $registro);
						}
						else
						{
							$id_address = 0;
						}
						
						$extra_field[$this->id_sin_dir_pre] = $id_address;
						
						$this->insert_sin($id_registro, $objp->id_row, $extra_field);
						
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
							$nombre = $this->armar_nombre($objp->nombre, $objp->apellido);
							
							$registro = array(
								'email' 		=> "'".$objp->email."'",
								'url' 			=> "'".$objp->website."'",
								'note_private'	=> "'".$objp->note."'",
								'siren' 		=> "'".$objp->cuil."'",
								'nom'			=> "'".$nombre['nombre']."'",
								'nom_cant'		=> "'".$nombre['nombre_cantidad']."'",
								'datec' 		=> "'".$objp->date_upd."'",
								'status' 		=> $objp->active,
								'id_sin' 		=> $objp->id_row
							);
							
							$where = $this->id_table_dol." = ".$id_registro;
							
							$this->update_registro($this->table_dol, $registro, $where);
							
							$this->update_log($objp->id_log);
						}
						else
						{
							$this->log_error('no_sin' , $objp);
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
							if($objp->is_client > 0 && $objp->email != NULL) // Controlamos que lo que se modifico fue un cliente
							{
								$email = strtolower($objp->email);
								
								$where = "email ='".$objp->email."' AND ".$this->id_table_pre." != ".$id_registro[$this->id_sin_pre];
									
								$cant_email = $this->get_registros($this->table_pre, $where);
								
								if($cant_email == 0) // Controlamos que el email no este repetido
								{
									$nombre = $this->armar_nombre($objp->nombre, $objp->apellido);
									
									$registro = array(
										'email' 	=> "'".$email."'",
										'website' 	=> "'".$objp->website."'",
										'note' 		=> "'".$objp->note."'",
										'cuil' 		=> "'".$objp->cuil."'",
										'firstname' => "'".$nombre['nombre']."'",
										'lastname'	=> "'".$nombre['apellido']."'",
										'date_upd' 	=> "'".$objp->date_upd."'",
										'active' 	=> $objp->active,
										'id_sin' 	=> $objp->id_row
									);
									
									$where = $this->id_table_pre." = ".$id_registro[$this->id_sin_pre];
									
									$this->update_registro($this->table_pre, $registro, $where);
									
									if($objp->address != NULL)
									{	
										if($id_registro[$this->id_sin_dir_pre] == 0)
										{
											$ciudad = 'Mendoza'; //Mejorar esta parte
											
											$id_country = $this->getID_direccion($objp->id_country, $this->system_prestashop, 'country');
											$id_state	= $this->getID_direccion($objp->id_state, $this->system_prestashop, 'state');
											
											$registro = array(
												'id_country'	=> $id_country,
												'id_state'		=> $id_state,
												'id_customer'	=> $id_registro[$this->id_sin_pre],
												'address1'		=> "'".$objp->address."'",
												'postcode'		=> "'".$objp->postcode."'",
												'city'			=> "'".$ciudad."'",
												'phone'			=> "'".$objp->phone."'",
												'date_add'		=> "'".$objp->date_upd."'",
												'date_upd'		=> "'".$objp->date_upd."'",
												'active'		=> 1,
												'deleted'		=> 0
											);
										
											$id_address = $this->insert_registro($this->table_dir_pre, $registro);
											
											$registro = array(
												$this->id_sin_dir_pre => $id_address
											);
											
											$where = $this->id_sin_dol." = ".$objp->id_row; 
												
											$this->update_registro($this->table_sin, $registro, $where);		
										}
										else
										{
											$registro = array(
												'address1'	=> "'".$objp->address."'",
												'postcode'	=> "'".$objp->postcode."'",
												'city'		=> "'".$objp->city."'",
												'phone'		=> "'".$objp->phone."'",
												'id_sin'	=> "'".$objp->id_cliente."'"
											);
											
											$where = "id_address = ".$id_registro['id_ps_address'];
											
											$this->update_registro($this->table_dir_pre, $registro, $where);
										}
									}	
								}
								else
								{
									$error = 'En la tabla '.$this->table_pre.' ya existe el email '.$objp->email;
									$error .= '. ID usuario de error '.$objp->id_row.' en la tabla '.$this->table_dol;
									$this->log_error('cadena' , $error);
								}
							}
							else // Si no es cliente lo damos de baja
							{
								$registro = array(
									'active' 	=> 0
								);
								
								$where = $this->id_table_pre." = ".$id_registro[$this->id_sin_pre];
								
								$this->update_registro($this->table_pre, $registro, $where);											
							}
							
							$this->update_log($objp->id_log);
						}
						else
						{
							$this->log_error('no_sin' , $objp);
						}
					}
				}

				$i++;
			}
		}
		
		$this->delete_log();
		$this->delete_log($this->table_log_dir);
			
		$this->reset_mod();
	}	
}
