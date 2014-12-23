
CRON: Tareas programadas
========================

Descripción
-----

La librería Cron simula el sistema cron de Linux. Utiliza el archivo <code>system/application/config/crontab.txt</code> para lanzar las tareas programadas.

El formato del archivo es el mismo que se utiliza en Linux [](http://es.wikipedia.org/wiki/Cron_(Unix)).
  
Se ha utilizado como base el código [](http://www.bitfolge.de/pseudocron).

Uso
----

La librería por si misma no se ejecuta periódicamente sino que debe ser llamada por algún proceso externo. Se llama al procedimiento <code>Cron::get_to_run</code> y devuelve las tareas que deben ser ejecutadas. Si externamente se ejecuta alguna de estas tareas se debe indciar con Cron::running.

@code
//Ejecuta los trabajos de cron
$jobs = $this->cron->get_to_run();
if (count($jobs) > 0)
{
	foreach($jobs as $job)
	{
		$data = array(
			'url' 		=> site_url($job['job']),
			'post'		=> array (
			'username'	=> $username,
			'password'	=> $this->userauth->get_password($username))
		);
		// Ejecuta tarea indicada en $data;
		$this->cron->running($job['job']);
  }
}
@endcode

El controlador Scheduler es el que usa esta librería y la llama periódicamente.

Debug
-----

Para entrar en modo debug se debe indicar un valor mayor de 0 en <code>Cron::debug</code>:
* _1_ - debug simple
* _2_ - debug extendido

Para leer el texto generado por el debug se debe llamar a la función <code>Cron::getTextDebug</code>.
