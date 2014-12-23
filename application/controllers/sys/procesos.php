<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador principal de la aplicación
 *
 */
class Tarea extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Tarea
	 */
	function __construct()
	{
		parent::__construct(null, 'procesos/m_proceso', FALSE);
	}

	function log($text)
	{
		$time = date("d-m-Y H:i:s");
		print "[{$time}] - {$text}\n";
	}

	function precioscompra()
	{
		$this->log('Arreglando precios de compra < 0');
		$count = $this->reg->precioscompra();
		$this->log("Se han limpiado {$count} registros");
	}

	function trabajos()
	{
		/*
			--EXEC spLibrosArreglarPreciosCostes
			--EXEC spCalcularPlazosEnvioTODOS
			EXEC spCalcularTotalesDocumentos
			--EXEC spCalcularUltimaCompraProveedor
			EXEC spDefaultProvidersGenerator
			--EXEC spLibrosQuitarOfertasSinStock

			--Página Web
			EXEC spCalcularMostrarWeb
			EXEC spLibrosEnMaterias
			EXEC spLimpiarSinopsis
			EXEC spOSCCrearBestSellers
			EXEC spOSCCrearNovedades

			--OLTP
			EXEC BibliopolaOLTP..spValorarStock
			--EXEC BibliopolaOLTP..spGenerarTablasOptimizadas
			--EXEC BibliopolaOLTP..spCrearCompras
			--EXEC BibliopolaOLTP..spCrearMovimientos
			--EXEC BibliopolaOLTP..spCrearDevoluciones
			--EXEC BibliopolaOLTP..spCrearVentas
			--EXEC BibliopolaOLTP..spCrearVentasSecciones
			--EXEC BibliopolaOLTP..spActualizarSeries

			--EOI
			--EXEC spEOICalcularValoresTabla
			*/
		$procs = array(
			'spCalcularTotalesDocumentos',
			'spDefaultProvidersGenerator',
			'spCalcularMostrarWeb',
			'spLibrosEnMaterias',
			'spLimpiarSinopsis',
			'spOSCCrearBestSellers',
			'spOSCCrearNovedades',
			'BibliopolaOLTP..spValorarStock',
			'BibliopolaOLTP..spCrearCompras',
			'BibliopolaOLTP..spCrearMovimientos',
			'BibliopolaOLTP..spCrearDevoluciones',
			'BibliopolaOLTP..spCrearVentas',
			'BibliopolaOLTP..spCrearVentasSecciones');

		$this->log('Lanzando procesos...');
		foreach($procs as $p)
		{
			$this->log("+Ejecutando {$p}");
			$this->reg->exec($p);
			$this->log(" Finalizado {$p}");
		}
		$this->log('Procesos finalizados');
	}

	function trabajos_task($runner = null)
	{
		set_time_limit(0);

		ob_start();
		$this->precioscompra();
		$this->trabajos();
		$msg = ob_get_contents();
		ob_end_clean();
		$this->load->library('Mensajes');
		$this->userauth->set_username();
		$this->mensajes->usuario($runner, $msg);
	}
}

/* End of file tarea.php */
/* Location: ./system/application/controllers/sys/tarea.php */