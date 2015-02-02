<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	proceso
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Procesos
 *
 */
class M_Proceso extends MY_Model
{
	/**
	 * Constructoir
	 * @return M_Proceso
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Actualiza los precios de compra que son negativos
	 * @return int número de registros actualizados
	 */
	function precioscompra()
	{

		$this->db->trans_begin();
		#$sql = 'ALTER TABLE Cat_Fondo DISABLE TRIGGER ALL';
		$this->db->query($sql);
		$sql = 'UPDATE Cat_Fondo
			SET fPrecioCompra = dbo.CalcularCosteLibro(nIdLibro, NULL)
			WHERE fPrecioCompra < 0';
		$this->db->query($sql);
		$count = $this->db->affected_rows();

		$sql = 'UPDATE Cat_Fondo
			SET fPrecioCompra = 0
			WHERE fPrecioCompra < 0';
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		#$sql = 'ALTER TABLE Cat_Fondo ENABLE TRIGGER ALL';
		$this->db->query($sql);
		$this->db->trans_commit();

		return $count;
	}

	/**
	 * Ejecuta un proceso de la base de datos
	 * @param string $proc Nombre del proceso
	 */
	function exec($proc)
	{
		$this->db->query("EXEC {$proc}");
	}
}

/* End of file M_Proceso.php */
/* Location: ./system/application/models/sys/M_Proceso.php */
