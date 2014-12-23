SinLib
====================

Descripción
-----------

[SINLI](http://www.fande.es/Sinli/sinli.asp) es un sistema de normalización de los documentos comerciales habitualmente intercambiados entre profesionales del sector: editores, distribuidores y libreros.

Todo el proceso de lectura y envío de documentos SINLI se implementa en la librería SinliLib. El controlador
Sinli controla los procesos de visualización, búsqueda y creación de documentos. 
El modelo de datos M_Sinli gestiona los datos.

Importación
-----------

El método SinliLib::check lee todas los emails del buzón de correos configurado en las variables

* sinli.mailbox.url
* sinli.mailbox.password
* sinli.mailbox.username

y crea todos los documentos en el directorio definido por la variable <code>DIR_SINLI_PATH</code>.
 
Procesa estos documentos y los inserta en la base de datos usando el modelo M_Sinli. Se insertan como un
array serializado, para que puedan ser procesados posteriormente.

Utliza las librerías [IMAP](http://www.php.net/manual/es/book.imap.php) para procesar los mensajes 
recibidos.

Actualmente se importan los siguientes tipos de documento

* ENVIO
* CAMPRE
* FACTUL
* ESTADO
* LIBROS
* ABONO

El sistema no realiza ninguna acción con los documentos recibidos, sino que los archiva en la base de datos
para ser utilizados cuando sea necesario.

Si la variable <code>sinli.debug</code> es <code>TRUE</code> no se eliminan ni los correos ni los ficheros 
adjuntos descargados.

Si el tipo de archivo no es soportado por el sistema o es erróneo se deja en el directorio de descarga.

Exportación
-----------

La exportación de documentos generar el documento de texto según el formato normalizado SINLI y realiza 
el envío usando la librería Emails.
 *
Se utilizan las siguientes variables:

sinli.identificacion
:Identificación SINLI de la librería

sinli.email
:Email de destino

sinli.emaildebug
:Emails a los que enviar en lugar del destino, en modo DEBUG

sinli.cc
:TRUE: Enviar copia de todos los emails al usuario que los genera


Para realizar un envío hay que llamar a la función SinliLib::send. Para generar un documento sin enviarlo
se puede llamar a la función SinliLib::export. 

Actualmente se generan los siguientes tipos de documento

* PEDIDO


La libería Sender, en el método Sender::send utiliza esta librería y se puede ver un ejemplo

@code
...
$this->obj->load->library('SinliLib');
$res = $this->obj->sinlilib->send($profile['sinlitipo'], $profile['data'], $profile['sinli'], $profile['sinliemail']);
if ($res === TRUE)
{
	return array(
			'success' => TRUE,
			'media' => $this->obj->lang->line('SINLI'),
			'dest' => $profile['sinli']
	);
}
else
{
	return array(
			'success' => FALSE,
			'media' => $this->obj->lang->line('SINLI'),
			'message' => $this->obj->sinlilib->get_error()
	);
}
... 
@endcode 
 
Debug
------


sinli.debug
:Indica que está en modo debug

sinli.emaildebug
:Emails a los que enviar en lugar del destino, en modo DEBUG


En modo DEBUG no se borran los emails del buzón de correo; se guarda una copia de los documentos descargados
en <code>DIR_SINLI_PATH</code>; y todos los envíos son a la cuenta de email de DEBUG. 
