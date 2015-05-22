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
	
	// campos en tablas
	var $id_sin_dol		= 'id_llx_socpeople';
	var $id_sin_pre		= 'id_ps_address';
	var $id_table_dol	= 'rowid';
	var $id_table_pre	= 'id_address';
	
	// tablas en base de datos para CLIENTES
	var $table_clientes_sin = 'tms_clientes_sin'; //Tabla de cruces
	var $table_log_clientes	= 'tms_log_clientes'; //Guarda los cambios direcciones 
	var $table_clientes_dol	= 'llx_societe';
	var $table_clientes_pre	= 'ps_customer';
	
	// campos en tablas
	var $id_sin_cliente_dol	= 'id_llx_societe';
	var $id_sin_cliente_pre	= 'id_ps_customer';
			
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
						$where = $this->id_sin_cliente_pre." = ".$objp->id_cliente;
							
						$objp_id_row = $this->get_registros($this->table_clientes_sin, $where);
																			
						if($objp_id_row[$this->id_sin_pre] != 0)
						{
							$registro = array(
								'fk_soc'		=> $objp_id_row[$this->id_sin_cliente_dol],
								'id_sin'		=> $objp->id_row,
								'firstname'		=> "'".$objp->firstname."'",
								'lastname'		=> "'".$objp->lastname."'",
								'address'		=> "'".$objp->address."'",
								'zip'			=> "'".$objp->postcode."'",
								'town'			=> "'".$objp->city."'",
								'phone'			=> "'".$objp->phone."'",
								'phone_mobile'	=> "'".$objp->phone_mobile."'",
								'datec'			=> "'".$objp->date_udp."'",
								'poste'			=> "'".$objp->alias."'",
								'statut'		=> $objp->active,
								'fk_user_creat'	=> 1
							);
							
							$id_registro = $this->insert_registro($this->table_dol, $registro);	
							
							$this->insert_sin($objp->id_row, $id_registro);
						}
						else
						{
							$registro = array(
								'address'	=> "'".$objp->address."'",
								'zip'		=> "'".$objp->postcode."'",
								'town'		=> "'".$objp->city."'",
								'phone'		=> "'".$objp->phone."'",
								'id_sin'	=> $objp->id_row							
							);
							
							$where = "rowid = ".$objp_id_row['id_llx_societe'];
							
							$this->update_registro($this->table_clientes_dol, $registro, $where);
							
							$registro = array(
								$this->id_sin_pre	=> "'".$objp->id_row."'"					
							);
							
							$where = $this->id_sin_cliente_dol." = ".$objp_id_row['id_llx_societe'];
						
							$this->update_registro($this->table_clientes_sin, $registro, $where);
						}
						
						$this->update_log($objp->id_log);
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
						$where = $this->id_sin_cliente_dol." = ".$objp->id_cliente;
						
						$objp_id_row = $this->get_registros($this->table_clientes_sin, $where);
						
						$registro = array(
							'id_customer'	=> $objp_id_row[$this->id_sin_cliente_pre],
							'id_sin'		=> $objp->id_row,
							'firstname'		=> "'".$objp->firstname."'",
							'lastname'		=> "'".$objp->lastname."'",
							'id_country'	=> 44,
							'id_state'		=> 111,
							'address1'		=> "'-'",
							'postcode'		=> "'".$objp->postcode."'",
							'city'			=> "'".$objp->city."'",
							'phone' 		=> "'".$objp->phone."'",
							'phone_mobile'	=> "'".$objp->phone_mobile."'", 
							'date_add'		=> "'".$objp->date_udp."'",
							'date_udp'		=> "'".$objp->date_udp."'",
							'alias'			=> "'-'",
							'active' 		=> "'".$objp->active
						);
						
						$id_registro = $this->insert_registro($this->table_pre, $registro);
						
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
						$where = $this->id_sin_pre." = ".$objp->id_row;
												
						$objp_id_row = $this->get_registros($this->table_clientes_sin, $where);
						
						if(is_array($objp_id_row))
						{
							$registro = array(
								'address'	=> "'".$objp->address."'",
								'zip'		=> "'".$objp->postcode."'",
								'town'		=> "'".$objp->city."'",
								'phone'		=> "'".$objp->phone."'",
								'id_sin'	=> "'".$objp->id_cliente."'"
							);
							
							$where = "rowid	= ".$objp_id_row[$this->id_sin_cliente_dol];	
							
							$this->update_registro($this->table_clientes_dol, $registro, $where);							
							
							$this->update_log($objp->id_log);
						}
						else
						{
							$id_registro = $this->get_id_sin($objp->id_row, $this->system_dolibar);
																					
							if($numr_row > 0)
							{
								$registro = array(
									'id_sin'	=> $objp->id_row, 
									'firstname'	=> "'".$objp->firstname."'", 
									'lastname'	=> "'".$objp->lastname."'",
									'address'	=> "'".$objp->address."'", 
									'zip'		=> "'".$objp->postcode."'", 
									'town'		=> "'".$objp->city."'", 
									'phone'		=> "'".$objp->phone."'", 
									'phone_mobile'	=> "'".$objp->phone_mobile."'", 
									'datec'		=> "'".$objp->date_udp."'",
									'poste'		=> "'".$objp->alias."'",
									'statut'	=> $objp->active
								);
								
								$where = $this->id_table_dol." = ".$id_registro;
								
								$this->update_registro($this->table_dol, $registro, $where);	
								
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
						$id_registro = $this->get_id_sin($objp->id_row, $this->system_prestashop);
						
						if($numr_row > 0)
						{
							$registro = array(
								'id_sin'		=> $objp->id_row, 
								'firstname'		=> "'".$objp->firstname."'", 
								'lastname'		=> "'".$objp->lastname."'",
								'id_country'	=> 44, 
								'id_state'		=> 111,
								'address1'		=> "'".$objp->address."'",
								'postcode'		=> "'".$objp->postcode."'",
								'city'			=> "'".$objp->city."'",
								'phone'			=> "'".$objp->phone."'",
								'phone_mobile'	=> "'".$objp->phone_mobile."'", 
								'date_udp'		=> "'".$objp->date_udp."'",
								'alias'			=> "'".$objp->alias."'",
								'active'		=> $objp->active 								
							);
							
							$where = $this->id_table_pre." = ".$id_registro; 
								
							$this->update_registro($this->table_pre, $registro, $where);	
								
							$this->update_log($objp->id_log);	
						}	
					}
				}
				
				$i++;
			}
		}
	
		$this->delete_log();
		$this->delete_log($this->table_log_clientes);
					
		$this->reset_mod();
	}

}
