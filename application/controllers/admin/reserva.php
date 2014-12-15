<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reserva extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('menu');
		$this->load->model('reserva_habitacion_model');
		$this->load->model('mensajes_model');
		$this->load->model('huespedes_model');
		$this->load->model('habitaciones_model');
		$this->load->model('emails_huesped_model');
		$this->load->model('telefonos_huesped_model');
		$this->load->model('notas_model');
		$this->load->model('estados_reserva_model');
		$this->load->model('disponibilidades_model');
		$this->load->model('disponibilidad_habitacion_model');
		$this->load->model('reservas_model');
		$this->load->model('tarjetas_model');
		$this->load->model('reserva_habitacion_model');
		$this->load->model('vuelos_model');
		$this->load->model('aerolineas_model');
		$this->load->helper('form');
		$this->load->library('grocery_CRUD');
		//$this->load->library('image_CRUD');
	}


	public function _example_output($output = null)
	{
		if($this->session->userdata('logged_in')){
			$reservas=buscarReservas();
			$mensajes=buscarMensajes();
			
			$db=array_merge($reservas, $mensajes);
						
			$this->load->view('backend/head.php',$output);
			$this->load->view('backend/menu.php', $db);	
			$this->load->view('backend/modal.php');
			$this->load->view('backend/reservas.php');
			$this->load->view('backend/footer.php');
		}else{
			redirect('/admin/home/logout/','refresh');
		}
	}

	public function index()
	{
		$this->_example_output2((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	
	}


/**********************************************************************************
 **********************************************************************************
 * 
 * 			Reserva formulario con hoteles y cantidades
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	
	public function reservas_formulario($id=NULL)
	{
		$mensaje="";
		$nuevas=0;
		
		if($this->input->post('aceptar')){
			if($this->input->post('id_nota')!=0){
					
				$nota=array('nota'			=> $this->input->post('nota'),
							'id_nota'		=> $this->input->post('id_nota'));
				$this->notas_model->updateNota($nota);
				$id_nota=$this->input->post('id_nota');
				
			}else if($this->input->post('nota')!=""){
					
				$nota=array('nota'			=> $this->input->post('nota'));
				$id_nota=$this->notas_model->insertNota($nota);
				
			}else{
				
				$id_nota=0;
				
			}
			
			$array_entrada = explode("/", $this->input->post('entrada')); 
			$entrada=$array_entrada[2]."/".$array_entrada[1]."/".$array_entrada[0];
			$salida_array = explode("/", $this->input->post('salida'));	
			$salida=$salida_array['2'].'/'.$salida_array['1'].'/'.$salida_array['0'];
			
			$reserva=array(	'id_reserva'	=> $id,
							'id_huesped'	=> $this->input->post('id_huesped'),
							'entrada'		=> $entrada,
							'salida'		=> $salida,
							'adultos'		=> $this->input->post('adultos'),
							'menores'		=> $this->input->post('menores'),
							'total'			=> $this->input->post('total'),
							'id_estado_reserva'=> $this->input->post('id_estado_reserva'),
							'id_nota'		=> $id_nota
							);
			$this->reservas_model->updateReserva($reserva);
			
			foreach ($this->input->post('id_habitaciones') as $key => $value) {
				$habitaciones[]=$value;
			}
			
			$nuevas=$this->reserva_habitacion_model->cambioHabitaciones($habitaciones, $id);
			$mensaje="La actualización se ha realizado correctamente";	
			
			$_COOKIE['tabla']='reservas';
			$_COOKIE['id']='id_reserva';
			$this->update_log($reserva, $id);
			
		}else if($this->input->post('cantidad')){
				
			$reserva_habitacion=$this->reserva_habitacion_model->getReserva($id);
			
			foreach ($reserva_habitacion as $row) {
				$registro=array(
							'id_reserva' 		=> $id,
							'id_habitacion'		=> $row->id_habitacion,
							'cantidad'			=> $this->input->post('id_habitacion'.$row->id_habitacion));
				$this->db->update('reserva_habitacion', $registro, array('id_reserva_habitacion' => $row->id_reserva_habitacion));
			}
			
			$mensaje="Las cantidades se han cargado correctamente";
			
		}else if($this->input->post('reenviar_correo')){
				
			$huespedes=$this->huespedes_model->getHuesped($this->input->post('id_huesped'));
			$emails_huesped=$this->emails_huesped_model->getEmail($this->input->post('id_huesped'));
			$telefonos_huesped=$this->telefonos_huesped_model->getTelefono($this->input->post('id_huesped'));
			$tarjetas_huesped=$this->tarjetas_model->getTarjeta($this->input->post('id_huesped'));
			$vuelos_huesped=$this->vuelos_model->getVuelo($id);
			
			foreach ($huespedes as $value) {
				$huesped['nombre']			= $value->nombre;
				$huesped['apellido']		= $value->apellido;
				$huesped['titulo']			= 'Reeenvio de datos de reserva';//Cambiar esto, mejorarlo
			}
			
			foreach ($emails_huesped as $value) {
				$huesped['email']			= $value->email;
			}
			
			foreach ($telefonos_huesped as $value) {
				$huesped['telefono']		= $value->telefono;
			}
			
			foreach ($tarjetas_huesped as $value) {
				$tarjeta['id_tipo_tarjeta']	= $value->id_tipo_tarjeta;
				$tarjeta['tarjeta']			= $value->tarjeta;
				$tarjeta['pin']				= $value->pin;
				$tarjeta['vencimiento']		= $value->vencimiento;
			}

			foreach ($vuelos_huesped as $value) {
				$vuelo['nro_vuelo']			= $value->nro_vuelo;
				$vuelo['horario_llegada']	= $value->horario_llegada;
			}
			
			$aerolineas=$this->aerolineas_model->getAerolinea($value->id_aerolinea);
			
			foreach ($aerolineas as $aerolinea) {
				$vuelo['aerolinea']			= $aerolinea->aerolinea;
			}
						
			$this->hoteles_email_model->correoReserva($huesped, $tarjeta, $this->reserva_habitacion_model->getReserva($id), $vuelo, 2);
			
			$mensaje="El correo ha sido reenviado al huésped";
			
		}
		$reservas=buscarReservas();
		$mensajes=buscarMensajes();
		$db=array_merge($reservas, $mensajes);
		
		$db['huespedes']			= $this->huespedes_model->getHuespedes();
		$db['estados']				= $this->estados_reserva_model->getEstados();
		$db['habitaciones']			= $this->habitaciones_model->getHabitaciones();
		$db['reservas']				= $this->reserva_habitacion_model->getReserva($id);
		$db['reserva_habitacion']	= $this->reservas_model->getReserva($id);
		$db['mensaje']				= $mensaje;
		$db['nuevas']				= $nuevas;	
		
		$this->load->view('backend/head.php');
		$this->load->view('backend/menu.php', $db);	
		$this->load->view('backend/modal.php');
		$this->load->view('backend/reservas_formulario.php');
		$this->load->view('backend/footer.php');
	}
	
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 			Cierre de ventas
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	
	public function cierre_ventas_formulario($id=NULL)
	{
		$reservas=buscarReservas();
		$mensajes=buscarMensajes();
		$db=array_merge($reservas, $mensajes);
		
		if($this->input->post('aceptar')){
			$array_entrada = explode("/", $this->input->post('comienzo')); 
			$entrada=$array_entrada[2]."/".$array_entrada[1]."/".$array_entrada[0];
			$salida_array = explode("/", $this->input->post('final'));	
			$salida=$salida_array['2'].'/'.$salida_array['1'].'/'.$salida_array['0'];
			
			$registro=array('disponibilidad' 	=> $this->input->post('descripcion'),
							'entrada'			=> $entrada,
							'salida'			=> $salida,
							'delete'			=> 0);
			
			$id=$this->disponibilidades_model->insertDisponibilidad($registro);
			
			foreach ($this->input->post('id_habitaciones') as $key => $value) {
				$habitaciones[]=$value;
			}
			
			$db['disponibilidad_habitacion']=$this->disponibilidad_habitacion_model->insertHabitaciones($habitaciones, $id);
			$db['registro']=$registro;
			$db['cargas']=$this->disponibilidad_habitacion_model->getDisponibilidadNombre($registro['disponibilidad']);
			
			$_COOKIE['tabla']='disponibilidades';
			$_COOKIE['id']='id_disponibilidad';
			$this->insert_log($registro, $id);
			
			$db['mensaje']="La carga se ha realizado con éxito";
		}else{
			$registro=$this->disponibilidades_model->getDisponibilidadId($id);
			
			foreach ($registro as $row) {
				$registro=array('disponibilidad' 	=> $row->disponibilidad);
			}
			$db['cargas']=$this->disponibilidad_habitacion_model->getDisponibilidadNombre($registro['disponibilidad']);
			$db['disponibilidad_habitacion']=$this->disponibilidad_habitacion_model->getDisponibilidadID($id);
			$db['registro']=$registro;
			
			
		}

		$db['habitaciones']	=$this->habitaciones_model->getHabitaciones();
		
		$this->load->view('backend/head.php');
		$this->load->view('backend/menu.php', $db);	
		$this->load->view('backend/modal.php');
		$this->load->view('backend/cierre_ventas_formulario.php');
		$this->load->view('backend/footer.php');
	}
	

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de reservas
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function reservas_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->where('reservas.delete', 0);
			$crud->set_table('reservas');
			
			//'habitaciones INNER JOIN hoteles ON(habitaciones.id_hotel=hoteles.id_hotel)'
			
			$crud->set_relation_n_n('habitaciones', 'reserva_habitacion', 'habitaciones', 'id_reserva', 'id_habitacion', 'habitacion', 'prioridad',  'delete = 0');
			
			$crud->columns(	'id_reserva',
							'habitaciones',
							'id_huesped',
							'entrada',
							'salida',
							'id_estado_reserva');
							
			$crud->display_as('id_reserva','ID')
				 ->display_as('id_habitacion','Habitación')
				 ->display_as('id_huesped','Huesped')
				 ->display_as('entrada','Entrada')
				 ->display_as('salida','Salida')
				 ->display_as('adultos','Adultos')				 
				 ->display_as('menores','Menores')
				 ->display_as('id_nota','Nota')
				 ->display_as('id_estado_reserva','Estado')
				 ->display_as('fecha_alta','Fecha alta');
			
			$crud->fields(	'id_huesped',
							'entrada',
							'salida',
							'adultos',
							'menores',
							'total',
							'id_nota',
							'id_estado_reserva',
							'fecha_alta',
							'habitaciones');
			
			$crud->set_subject('reserva');
			
			$crud->unset_edit();
			
			$crud->field_type('fecha_alta', 'readonly');
			
			$crud->set_relation('id_huesped','huespedes','{apellido} {nombre}');
			$crud->set_relation('id_nota','notas','nota');
			$crud->set_relation('id_estado_reserva','estados_reserva','estado_reserva');		
						
			$crud->required_fields('id_habitacion','id_huesped','entrada', 'salida', 'adultos', 'menores');
			
			$crud->add_action('Vuelos', '', '','icon-plane', array($this,'buscar_vuelos'));
			$crud->add_action('Editar reserva', '', '','icon-edit', array($this,'edit_reserva'));
			
			$_COOKIE['tabla']='reservas';
			$_COOKIE['id']='id_reserva';	
			
			$crud->callback_after_insert(array($this, 'insert_log'));
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));	
			
			$output = $crud->render();

			$this->_example_output($output);
	}


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de reservas
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function reservas_nuevas(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->where('reservas.id_estado_reserva', 1);
			$crud->set_table('reservas');
			
			$crud->columns(	'id_reserva',
							'id_habitacion',
							'id_huesped',
							'entrada',
							'salida',
							'id_estado_reserva');
							
			$crud->display_as('id_reserva','ID')
				 ->display_as('id_habitacion','Habitación')
				 ->display_as('id_huesped','Huesped')
				 ->display_as('entrada','Entrada')
				 ->display_as('salida','Salida')
				 ->display_as('adultos','Adultos')				 
				 ->display_as('menores','Menores')
				 ->display_as('id_nota','Nota')
				 ->display_as('id_estado_reserva','Estado')
				 ->display_as('fecha_alta','Fecha alta')
				 ->display_as('fecha_modificacion','Última modificación') ;
			
			$crud->set_subject('reserva');
			
			$crud->set_relation('id_habitacion','habitaciones','habitacion');
			$crud->set_relation('id_huesped','huespedes','{apellido} {nombre}');
			$crud->set_relation('id_nota','notas','nota');
			$crud->set_relation('id_estado_reserva','estados_reserva','estado_reserva');
					
			$crud->required_fields('id_habitacion','id_huesped','entrada', 'salida', 'adultos', 'menores');
			
			$crud->field_type('fecha_alta', 'readonly');
			$crud->field_type('fecha_modificacion', 'readonly');
			
			$crud->callback_after_update(array($this, 'update_reserva'));
			
			$output = $crud->render();

			$this->_example_output($output);
	}

	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de disponibilidades
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function disponibilidades_abm(){
			$crud = new grocery_CRUD();
			
			$crud->where('disponibilidades.delete', 0);
			
			$crud->set_table('disponibilidades');
			
			$crud->set_relation_n_n('habitaciones', 'disponibilidad_habitacion', 'habitaciones', 'id_disponibilidad', 'id_habitacion', '{habitacion} - {id_hotel}', 'prioridad', 'delete = 0');
			
			$crud->columns(	'id_disponibilidad',
							'habitaciones',
							'disponibilidad',
							'entrada',
							'salida');
							
			$crud->display_as('id_disponibilidad','ID')
				 ->display_as('disponibilidad','Descripción')
				 ->display_as('entrada','Comienzo')
				 ->display_as('salida','Final')
				 ->display_as('habitaciones','Habitaciones');
			
			$crud->set_subject('cierre de ventas');
			
			$crud->fields('disponibilidad','entrada','salida', 'habitaciones');					
			$crud->required_fields('disponibilidad','entrada','salida', 'id_habitacion');
			
			$crud->field_type('fecha_alta', 'readonly');
			$crud->field_type('fecha_modificacion', 'readonly');
			
			$crud->add_action('Insertar multiples', '', '','icon-databaseadd', array($this,'insert_disponibilidad'));
			
			$_COOKIE['tabla']='disponibilidades';
			$_COOKIE['id']='id_disponibilidad';	
			
			$crud->callback_after_insert(array($this, 'insert_log'));
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));	
			
			$output = $crud->render();

			$this->_example_output($output);
	}





/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de Estados las reservas
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function estados_reserva(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('estados_reserva');
			
			$crud->columns(	'id_estado_reserva',
							'estado_reserva', 
							'reserva_lugar');
			
			$crud->display_as('id_estado_reserva','ID')
				 ->display_as('estado_reserva','Estado')
				 ->display_as('reserva_lugar','Reserva lugar');
				 
			$crud->field_type('reserva_lugar', 'true_false');
			
			$crud->set_subject('estado');
			$crud->unset_delete();
			$crud->unset_export();
			$crud->unset_add();
			$crud->unset_read();
			
			$_COOKIE['tabla']='estados_reserva';
			$_COOKIE['id']='id_estado_reserva';	
			
			$crud->callback_after_update(array($this, 'update_log'));
							
			$crud->required_fields('estado_reserva');
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de Vuelos
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function vuelos_abm(){
			$crud = new grocery_CRUD();
			
			$crud->where('vuelos.delete', 0);
			
			$crud->set_table('vuelos');
			
			$crud->columns(	'id_vuelo',
							'id_reserva',
							'id_huesped',
							'id_aerolinea',
							'nro_vuelo',
							'horario_llegada');
							
			$crud->display_as('id_vuelo','ID')
				 ->display_as('id_reserva','Reserva')
				 ->display_as('id_huesped','Huesped')
				 ->display_as('id_aerolinea','Aerolinea')
				 ->display_as('nro_vuelo','Número vuelo')
				 ->display_as('horario_llegada','Horario de llegada');
			
			$crud->fields('id_reserva', 'id_huesped', 'id_aerolinea', 'nro_vuelo', 'horario_llegada');
			
			$crud->set_subject('vuelos');
			
			$crud->set_relation('id_huesped','huespedes','{apellido} {nombre}');
			$crud->set_relation('id_reserva','reservas','id_reserva', 'delete = 0');			
			$crud->set_relation('id_aerolinea','aerolineas','aerolinea', 'delete = 0');
			
			//$crud->add_action('Reserva', '', '','icon-tagalt-pricealt', array($this,'buscar_reservas'));
			
			$_COOKIE['tabla']='vuelos';
			$_COOKIE['id']='id_vuelo';	
			
			$crud->callback_after_insert(array($this, 'insert_log'));
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));	
			
			$output = $crud->render();

			$this->_example_output($output);
	}




	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Funciones logs
 * 
 * ********************************************************************************
 **********************************************************************************/

	
	function insert_control_fechas($datos, $id){
		if($datos['entrada']>$datos['salida']){
			return false;
		}else{
			return true;	
		} 
	}
	

	function insert_log($datos, $id){
		$session_data = $this->session->userdata('logged_in');
		
	    $registro = array(
	        "tabla"		=> $_COOKIE['tabla'],
	        "id_tabla"	=> $id,
	        "id_accion"	=> 1,
	        "fecha"		=> date('Y-m-d H:i:s'),
	        "id_usuario"=> $session_data['id_usuario']
	    );
	 
	    $this->db->insert('logs_reservas',$registro);
	 
	    return true;
	}
	
	
	function update_log($datos, $id){
		$session_data = $this->session->userdata('logged_in');
		
    	$registro = array(
	        "tabla"		=> $_COOKIE['tabla'],
	        "id_tabla"	=> $id,
	        "id_accion"	=> 2,
	        "fecha"		=> date('Y-m-d H:i:s'),
	        "id_usuario"=> $session_data['id_usuario']
	    );
 
    	$this->db->insert('logs_reservas',$registro);
 
    	return true;
	}
	
	
	public function delete_log($id){
    	$session_data = $this->session->userdata('logged_in');
		
		$registro = array(
	        "tabla"		=> $_COOKIE['tabla'],
	        "id_tabla"	=> $id,
	        "id_accion"	=> 3,
	        "fecha"		=> date('Y-m-d H:i:s'),
	        "id_usuario"=> $session_data['id_usuario']
	    );
 
    	$this->db->insert('logs_reservas',$registro);
			
    	return $this->db->update($_COOKIE['tabla'], array('delete' => 1), array($_COOKIE['id'] => $id));
	}

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Actulizar nuevas
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	
	function actualizar_nuevas(){
		$cant_reservas=$this->reservas_model->getCantNuevas();	
		if($cant_reservas>0){
			$reservas=$this->reservas_model->getNuevas();
		
			foreach ($reservas as $reserva) {
				$id=$reserva->id_reserva;
				if($id>0){
					$reserva = array(
        				"id_reserva" => $this->input->post('id_reserva'.$id),
        				"id_estado_reserva" => $this->input->post('estado'.$id)
    				);	
				}else{
					//echo "cero";
				}
				
 
				$this->db->update('reservas', $reserva, array('id_reserva' => $id));
				$_COOKIE['tabla']='reservas';
				$_COOKIE['id']='id_reserva';
				
				$this->update_log($reserva, $id);
			}
				
		}
		redirect('admin/reserva/reservas_abm/success', 'refresh');		
	}
	
	
	
	function buscar_vuelos($id){
		$query = $this->db->query("SELECT id_vuelo FROM vuelos WHERE id_reserva='$id' ");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$id_vuelo = $fila->id_vuelo;
			}
			
			return site_url('admin/reserva/vuelos_abm/read').'/'.$id_vuelo;	
		}else{
			return site_url('no_fly').'/';
		}
	}
	
	function edit_reserva($id){
		return site_url('admin/reserva/reservas_formulario').'/'.$id;
	}
	
	function insert_disponibilidad($id){
		return site_url('admin/reserva/cierre_ventas_formulario').'/'.$id;
	}
	
	
	function buscar_reservas($id){
		$query = $this->db->query("SELECT id_reserva FROM vuelos WHERE id_vuelo='$id' ");
			foreach ($query->result() as $fila){
				$id_reserva = $fila->id_reserva;
			}
			
			return site_url('admin/reserva/reservas_abm/read').'/'.$id_reserva;	
	}
	
	
}
//script para poner disabled los que no tengan vuelo
?>
<script>
	$(document).ready(function(){
 		$('a[href*="no_fly"]').addClass('disabled', true);
	});
</script>