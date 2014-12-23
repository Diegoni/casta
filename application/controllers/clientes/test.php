<?php
class Test extends MY_Controller{
  	
  function __construct(){
    parent::__construct();
    
  }
  
  function index(){
  	echo "test";
  }
  
  function cuenta($tipo = null)
	{
		//$this->userauth->roleCheck(($this->auth.'.cuenta'));

		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if ($tipo)
		{

			$this->load->model('clientes/m_tipocliente');
			$tipo = $this->m_tipocliente->load($tipo);
			$base = $tipo['nCuenta'];
			$digitos = $this->config->item('bp.clientes.digitoscuenta');
			$min = (float)($base . str_repeat('0', $digitos - strlen($base)));
			$max = (float)($base . str_repeat('9', $digitos - strlen($base)));
			$cuenta = $this->reg->next_cuenta($min, $max);
			$this->out->success($cuenta);
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}
}
