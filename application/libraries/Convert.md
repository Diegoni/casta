Convert
====================

Descripción
-----

El conversor de ficheros convierte los informes y documentos generados por el sistema, siempre en HTML, a los siguientes formatos de salida disponibles: 

* *ODT*: OpenOffice text document
* *DOCX*
* *RTF*


La librería utiliza [PANDOC](https://github.com/jgm/pandoc) como sistema de conversión, aunque también dispone del código necesario para usar las librerías [XMLmind XSL-FO Converter](http://www.xmlmind.com/foconverter/).

[Manual pandoc](http://johnmacfarlane.net/pandoc/README.html)

@code
$file = 'temporal.html';
$this->load->library('Convert');
$this->convert->rtf($file);
@endcode 
 

Configuracion
-----

:convert.path
Path de pandoc

:convert.parameters
Parámetros para la herramienta pandoc

:pdf.replaces
Reemplazos para el HTML original antes de ser convertido


