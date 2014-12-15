<?php
class Idioma extends CI_Controller {
 
 	public function __construct()
 	{

 		parent::__construct();

 	}

	function index(){

 		$data['cambia_idioma'] = $this->cambia_idioma();
 
 		$this->load->view('frontend/head', $data);
		$this->load->view('idioma');

	}

	public function formulario(){
		
		$this->form_validation->set_rules(lang('idioma.input_nombre'),
										  lang('idioma.label_nombre'),
										  'required|min_length[5]|max_length[12]');

		$this->form_validation->set_rules(lang('idioma.input_password'),
										  lang('idioma.label_password'),
										  'required|min_length[6]|max_length[50]|matches[repassword]');

		$this->form_validation->set_rules(lang('idioma.input_repassword'),
										  lang('idioma.label_repassword'), 
										  'required');


		$this->form_validation->set_rules(lang('idioma.input_email'),
										  lang('idioma.label_email'),  
										  'required|valid_email');

		if ($this->form_validation->run() == FALSE){
			$this->index();
		}else{

			$nombre = $this->input->post(lang('idioma.input_nombre'));
			$password = sha1($this->input->post(lang('idioma.input_password')));
			$email = $this->input->post(lang('idioma.input_email'));
			$idioma = $this->input->post('idioma');

			if($this->idioma_model->nuevo_usuario($nombre,$password,$email,$idioma)){
				$this->session->set_flashdata('registrado','El registro fue correcto, bienvenido');
				redirect(base_url($idioma.'/home'),'refresh');
			}

		}
	}

	//cambiamos el valor del select dependiendo del primer segmento de la url
	public function cambia_idioma(){
	?>
		<select class="escoge_idioma">
	 	<?php
	 	if($this->uri->segment(1) == "es"){
	 	?>
	 		<option value="<?=base_url()?>es/idioma">ES</option> 
	 		<option value="<?=base_url()?>en/idioma">EN</option>'?>
	 	<?php
	 	}else{
	 		?>
	 		<option value="<?=base_url()?>en/idioma">EN</option> 
	 		<option value="<?=base_url()?>es/idioma">ES</option>'?>
	 	<?php
	 	}
	 	?>
	 	</select>
	<?php
	}
}
 
/* End of file */