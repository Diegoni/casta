<?php
require_once DOL_DOCUMENT_ROOT.'/sincronizar/class_actualizar.php';

class Actualizar_pedidos extends Actualizar
{
	var $subject		= 'pedidos'; 
	
	// tablas en base de datos para PEDIDOS
	var $table_log		= 'tms_log_pedidos'; //Guarda los cambios
	var $table_sin		= 'tms_pedidos_sin'; //Tabla de cruces 
	var $table_dol		= 'llx_commande';
	var $table_pre		= 'ps_orders';
	var $table_mod		= 'tms_mod_pedidos';
	
	var $table_sin_cli	= 'tms_clientes_sin';
	
	// campos en tablas
	var $id_sin_dol		= 'id_llx_commande';
	var $id_sin_pre		= 'id_ps_orders';
	var $id_table_dol	= 'rowid';
	var $id_table_pre	= 'id_order';
	
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
	
	 		Funcion que permite actualizar pedidos
	 
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
						$where = "`id_ps_customer`	= $objp->id_cliente";
							
						$array_sin_cliente = $this->get_registros($this->table_sin_cli, $where);
												
						$registro = array(
							'id_sin'				=> $objp->id_row,
							'fk_soc'				=> $array_sin_cliente['id_llx_societe'],
							'ref'					=> "'".$objp->reference."'",
							'total_ttc'				=> "'".$objp->total_paid."'",
							'date_creation'			=> "'".$objp->date_upd."'"
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
						$where = "`id_llx_societe`	= $objp->id_cliente";
							
						$array_sin_cliente = $this->get_registros($this->table_sin_cli, $where);
						
						$registro = array(
							'id_sin'				=> $objp->id_row,
							'id_customer'			=> $array_sin_cliente['id_ps_customer'],
							'reference'				=> "'".$objp->reference."'",
							'total_paid'			=> "'".$objp->total_paid."'",
							'date_add'				=> "'".$objp->date_upd."'"
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
						$id_registro = $this->get_id_sin($objp->id_row, $this->system_dolibar);
						
						if($id_registro != 0)
						{
							$registro =  array(
								'id_sin'				=> $objp->id_row,
								'ref'					=> "'".$objp->reference."'",
								'total_ttc'				=> "'".$objp->total_paid."'",
								'date_creation'			=> "'".$objp->date_upd."'"
							);
							
							$where = $this->id_table_dol." = ".$id_registro;
							
							$this->update_registro($this->table_dol, $registro, $where);
							
							$this->update_log($objp->id_log);
						}
					}
								
		/*----------------------------------------------------------------
				UPDATE desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
		
	 				else	
					if($objp->system == $this->system_dolibar)					
					{
						$id_registro = $this->get_id_sin($objp->id_row, $this->system_prestashop);
						
						if($id_registro > 0)
						{
							$registro =  array(
								'id_sin'				=> $objp->id_row,
								'reference'				=> "'".$objp->reference."'",
								'total_paid'			=> "'".$objp->total_paid."'",
								'date_add'				=> "'".$objp->date_upd."'"
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
			
		$this->reset_mod();
	}
		
}
