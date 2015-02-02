<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	tools
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Procesos relacionados con los pedidos de cliente
 * @author alexl
 *
 */
class Pedidos extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Pedidos
	 */
	function __construct()
	{
		parent::__construct();
	}

	function dibalot8()
	{
		$libros = array(
		array('9788479028022',1,'1001 trucos para la pesca con mosca : consejos y ','Madrid : Tutor, cop. 2009','9788479028022'),
		array('9788439721468',1,'Barry Lyndon / William Thackeray ; traducción de Ana ','Thackeray, William M.','Barcelona : Mondadori, 2010'),
		array('9788496924567',1,'Breve historia del cine / Wheeler Winston Dixon & ','Dixon, Wheeler W.','Barcelona : Robinbook, cop. 2009'),
		array('9788467540192',1,'Clase de inglés / Lygia Bojunga Nunes ; traducción de ','Nunes, Lygia Bojunga','Boadilla del Monte : SM, DL 2010'),
		array('9788408090960',1,'Cómo cazamos al hombre del saco / Andreu Martín ; ','Martín, Andreu','Barcelona : Planetalector, 2010'),
		array('9782226195913',1,'Concerto à la mémoire d\'un ange / Eric-Emmanuel ','Schmitt, Eric-Emmanuel','Paris : Albin Michel, cop. 2010'),
		array('9788467033007',1,'Crypta / Care Santos','Santos, Care','Madrid : Espasa, cop. 2010'),
		array('',1,'Cuentos completos / Cortázar','Cortázar, Julio','Madrid : Alfaguara, cop. 2010'),
		array('9782356481962970',1,'Desvelando a Bin Laden / guión: Mohamed Sifaoui ; ','Sifaoui, Mohamed','[Bèlgica?] : 12 bis, cop. 2010'),
		array('',1,'El Color de los pájaros : un cuento popular de los ','Kerba, Muriel','Madrid : SM, 2006'),
		array('9788408090755',1,'El Diario secreto de Adrian Mole / Sue Townsend ; ','Townsend, Sue','Barcelona : Planetalector, 2010'),
		array('9788461205783',1,'El Esquí / Robert Puente','Puente, Robert','[Madrid : l\'autor], 2008'),
		array('9788426373861',1,'El Flautista de Hamelin / texto: Pepe Maestro ; ','Maestro, Pepe','Zaragoza : Edelvives, cop. 2010'),
		array('9788498500233',1,'El Laboratori de la joieria : 1940-1990 / edició a cura de','[Barcelona] : Ajuntament de ','9788498500233'),
		array('9788448038458',1,'El Palacio de las tempestades / varios autores','Barcelona : Timun Mas, 2010','9788448038458'),
		array('9788467513707',1,'El Pastor que deseaba ser padre : un cuento popular ','Gerner, Jochen','Madrid : SM, cop. 2007'),
		array('9788408090793',1,'El Poema de Peter Pan / Carmen Martín Anguita ; ','Martín Anguita, Carmen','Barcelona : Planetalector, 2010'),
		array('9788483831151',1,'El Teatro de la memoria / Leonardo Sciascia ; ','Sciascia, Leonardo','Barcelona : Tusquets, 2009'),
		array('9788492616480',1,'Entre Franco y Stalin : el difícil itinerario de los ','Puigsech Farràs, Josep','[Mataró] : El Viejo Topo, cop. 2009'),
		array('9788441525887',1,'Excel 2007 : gestión y empresa : edición 2009 / ','Manzo, Joseph M.','Madrid : Anaya Multimedia, cop. 2009'),
		array('9788484608073',1,'Guia práctica contra la depresión/ Enrique Rojas','Rojas, Enrique','Madrid : Temas de Hoy, 2009'),
		array('9788498033908',1,'Informe sobre les polítiques locals de consum a la ','Barcelona : Diputació de Barcelona, ','9788498033908'),
		array('9788441527034',1,'Internet para torpes : edición 2010 / Ana Martos Rubio','Martos, Ana','Madrid : Anaya Multimedia, cop. 2010'),
		array('8467506121',1,'Iván y la bruja : un cuento popular ruso / ilustrado por ','Chatellard, Isabelle','Boadilla del Monte : SM, 2006'),
		array('9788498232721',1,'La Autenticidad del deporte : fundamentos de ética ','Lamoneda Prieto, Javier','Sevilla : Wanceulen, 2010'),
		array('8467504188',1,'La Cigarra y el ratón : un cuento popular del Magreb / ','Frehring, Xavier','Boadilla del Monte : SM, 2005'),
		array('9788497544610',1,'La Gravedad : descubre qué es, cómo funciona y por ','Uzan, Jean-Philippe','Barcelona : Oniro, 2010'),
		array('9788498033441',1,'La Mediació ciutadana : una nova política pública : ','Barcelona : Diputació de Barcelona, ','9788498033441'),
		array('9788441527232',1,'La Nueva generación hacker / Nitesh Dhanjani, Billy ','Dhanjani, Nitesh','Madrid  : Anaya Multimedia, DL 2010'),
		array('9788449323065',1,'La Palabra justa : más de cien aforismos de todas las ','Rampin, Matteo','Barcelona [etc.] : Paidós, cop. 2009'),
		array('9788449323119',1,'La Paradoja del tiempo : la nueva psicología del tiempo ','Zimbardo, Philip G.','Barcelona [etc.] : Paidós, cop. 2009'),
		array('9788448253646',1,'La Producción cinematogràfica en España : Vicente ','Sempere, Isabel','València : Ediciones de la Filmoteca.'),
		array('8495580020',1,'La Serpiente emplumada / D.H. Lawrence ; traducción ','Lawrence, D. H.','[Barcelona] : Montesinos, 2000'),
		array('9788480165778',1,'Larousse árabe : método integral / Jack Smart, ','Smart, Jack','Barcelona : Larousse, cop. 2009'),
		array('9788480168458',1,'Larousse inglés : método express / Sheena ','Andromaque, Sheena','Barcelona : Larousse, 2008'),
		array('9788467488012',1,'Las Armas del metabarón / Jodorowsky, Charest, ','Jodorowsky, Alejandro','Barcelona : Planeta DeAgostini, cop.'),
		array('9782864325529',1,'Les Onze / Pierre Michon','Michon, Pierre','Lagrasse : Verdier, cop. 2009'),
		array('9788408090984',1,'Leyendas del planeta Thámyris / Joan Manuel Gisbert','Gisbert, Joan Manuel','Barcelona : Planetalector, 2010'),
		array('9788497769761',1,'Los Caminos de Santiago en moto / de Pedro Pardo','Pardo, Pedro','Madrid : Anaya Touring Club, 2010'),
		array('9788467513714',1,'Mabo y la hiena : un cuento popular de Mali / ilustrado ','Palayer, Caroline','Madrid : SM, 2007'),
		array('9788498238242',1,'Metodología del tenis de mesa : aproximación ','Sevilla : Wanceulen, 2009','9788498238242'),
		array('9788478338979',1,'Migrador nocturno / Salvador Sanz','Sanz, Salvador','Barcelona : La Cúpula, 2010'),
		array('9788498712292',1,'Nariz de oro / texto de Raquel Saiz ; ilustraciones de ','Saiz, Raquel','Pontevedra : OQO, cop. 2010'),
		array('9788439722526',1,'Notas al pie de Gaza / Joe Sacco','Sacco, Joe','Barcelona : Random House '),
		array('9788496924680',1,'Películas clave del cine erótico / Pedro Calleja ; prólogo','Calleja, Pedro','Barcelona [etc.] : Ma Non Troppo, '),
		array('9788496924741',1,'Películas clave del cine histórico / Enric Alberich ; ','Alberich, Enrique','Teià : Ma Non Troppo, cop. 2009'),
		array('9788492929146',1,'Perryn y la profecía del mago / Hilari Bell','Bell, Hilari','Barcelona : Versátil, 2010'),
		array('9788499470047',1,'Pinocho blues / Carlos Bribián','Bribián, Carlos','Barcelona : Glénat, DL 2010'),
		array('',1,'Portuguese : the complete language-learning kit / ','Harland, Michael','Oxford : Oxford University, 2008'),
		array('9788478338931',1,'Ranx /  dibujo: Stefano Tamburini, Tanino Liberatore ; ','Liberatore, Tanino','Barcelona : La Cúpula, 2010'),
		array('9788499470337',1,'Sèrie B / Deamo Bros','Deamo Bros','Barcelona : Glénat, 2010'),
		array('9788408090786',1,'Un Tiesto lleno de lápices / Juan Farias ; ilustraciones: ','Farias, Juan','Barcelona : Planetalector, 2010'),
		array('9788434235427',2,'Animación : nuevos proyectos y procesos creativos / ','Selby, Andrew','[Barcelona] : Parramón, 2009'),
		array('9788420649771',2,'Antología poética / Jorge Guillén ; selección e ','Guillén, Jorge','Madrid : Alianza, cop. 2010'),
		array('9788467541106',2,'Cómo enseñar a tus padres a disfrutar de los libros ','Serres, Alain','Boadilla del Monte : SM, 2010'),
		array('9781846052576',2,'Cross country / James Patterson','Patterson, James','London : Century, 2008'),
		array('9781407570631',2,'De gatito a gato / Steve Parker','Parker, Steve','Bath [etc.] : Parragon, 2009'),
		array('9781407570655',2,'De renacuajo a rana / Steve Parker','Parker, Steve','Bath : Parragon, 2009'),
		array('9788498236385',2,'El Aprendizaje y la mejora técnico-táctica en el fútbol ','Mas Rubio, Juan','Sevilla : Wanceulen, 2009'),
		array('9788466410915',2,'El Camí dels savis : filosofia antiga per a la vida ','Riso, Walter','Barcelona : Columna, 2009'),
		array('9788434235250',2,'El Dibujo humorístico / [Sergi Càmara]','Càmara, Sergi','Barcelona : Parramón, 2009'),
		array('9788467900569',2,'El Hombre retorcido / guión: Mike Mignola ; dibujo: ','Mignola, Mike','Barcelona : Norma, 2010'),
		array('9788496483934',2,'El Otro mundo / Horacio Vázquez-Rial','Vázquez Rial, Horacio','Barcelona : Sirpus, 2009'),
		array('9788425519338',2,'El Periquito / Immanuel Birmelin','Birmelin, Immanuel','L\'Hospitalet de Llobregat : Hispano '),
		array('9788493760175',2,'En mitad de la noche un canto / Jirí Kratochvil ; ','Kratochvíl, Jirí','Madrid : Impedimenta, 2010'),
		array('9788498675955',2,'Escuela de felicidad / conversaciones con: Boris ','Barcelona : RBA, 2009','9788498675955'),
		array('9788499470115',2,'Frank Cappa / Manfred Sommer','Sommer, Manfred','Barcelona : Glénat, DL 2010'),
		array('9788496646513',2,'G. Verdi : un álbum musical / Lene Mayer-Skumanz ; ','Mayer-Skumanz, Lene','Santa Marta de Tormes : Lóguez, '),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('',2,'Geronimo Stilton : English!','Stilton, Geronimo','Barcelona : Grup Editorial 62, 2009'),
		array('9788467521085',2,'Gramática de uso del español : teoría y práctica : con ','Aragonés, Luis','[Boadilla del Monte] : SM, DL 2009'),
		array('9788478979554',2,'Guía de campo de hackers : aprende a atacar y a ','Gómez López, Julio','Madrid : Ra-Ma, cop. 2010'),
		array('9788441527751',2,'Guía visual de Access 2010 / Miguel Pardo Niebla','Pardo Niebla, Miguel','Madrid : Anaya Multimedia, cop. 2010'),
		array('9788441527768',2,'Guía visual de PowerPoint 2010 / Rosario Gómez del ','Gómez del Castillo, Rosario','Madrid : Anaya Multimedia, cop. 2010'),
		array('9788498852882',2,'Incógnito / Ed Brubaker y Sean Phillips','Brubaker, Ed','Torroella de Montgrí : Panini, [2010]'),
		array('9788492650194',2,'Informática para la gestión y la administración : básico ','Menchén Peñuela, Antonio','Paracuellos de Jarama : StarBook, '),
		array('',2,'Irene y Pablo en casa / Mª Jesús del Olmo, José ','Olmo, María Jesús del','Boadilla del Monte : SM, cop. 2006'),
		array('8467508388',2,'Irene y Pablo en la ciudad / Mª Jesús del Olmo, José ','Olmo, María Jesús del','Boadilla del Monte : SM, cop. 2006'),
		array('9788478338986',2,'Kafka / Robert Crumb y David Mairowitz','Crumb, Robert','Barcelona : La Cúpula, cop. 2010'),
		array('8467504196',2,'La Diadema de Rocío : un cuento popular de China / ','Mourrain, Sébastien','Boadilla del Monte : SM, 2005'),
		array('9788498234626',2,'La Iniciación al pádel : cuaderno didáctico / Javier ','Castaño Ruiz, Javier','Sevilla : Wanceulen, 2009'),
		array('9788432298356',2,'La Ofensa / Ricardo Menéndez Salmón','Menéndez Salmón, Ricardo','Barcelona : Planeta, 2009'),
		array('9788467900552',2,'La Teoría del grano de arena / Schuiten, Peeters','Schuiten, François','Barcelona : Norma, DL 2010'),
		array('9788432298349',2,'Laura y Julio / Juan José Millás','Millás, Juan José','Barcelona : Planeta [etc.], 2009'),
		array('9788498853995',2,'Lobezno / Das Pastoras, Joseph Clark, Victor Gischler','Daspastoras','Torroella de Montgrí : Panini, 2010'),
		array('9788497769761',2,'Los Caminos de Santiago en moto / de Pedro Pardo','Pardo, Pedro','Madrid : Anaya Touring Club, 2010'),
		array('9788483226841',2,'Manual de apoyo psicológico / Cruz Roja Española','Madrid [etc.] : Pearson, cop. 2010','9788483226841'),
		array('9788479027124',2,'Manual de entrenamiento de fútbol base : cómo ','Page, Stuart','Madrid : Tutor, cop. 2008'),
		array('9788493738020',2,'Muntanyes verges : Mountain Wilderness de ','Barcelona : CIM, 2010','9788493738020'),
		array('9788497806114',2,'Paper i cola / Yolanda Falagán','Falagán, Yolanda','Saragossa : ItsImagical, DL 2009'),
		array('9788499470511',2,'Promesas rotas / Hiroshi Hirata','Hirata, Hiroshi','Barcelona : Glénat, 2010'),
		array('9788492724116',2,'Ray Harryhausen : creador de monstruos / [textos: ','Madrid : Maia, cop. 2009','9788492724116'),
		array('9788496699526',2,'Reflexología podal : masaje sobre las zonas reflejas ','Tejedor Samaniego, César','Madrid : Videocinco, DL 2010'),
		array('9788492650279',2,'Repare, configure y amplíe su PC : básico / José ','Cernuda Menéndez, José Higinio','Paracuellos de Jarama : StarBook, '),
		array('9782915807622',2,'Roma insólita y secreta / [Ginevra Lovatelli, Adriano ','Lovatelli, Ginevra','Tours : Jonglez, [2010]'),
		array('9788466640244',2,'Sin principio ni fin : las aventuras de un pequeño ','Avi, 1937-','Barcelona : Ediciones B, 2009'),
		array('9788498236972',2,'Táctica y técnica en la iniciación al baloncesto / ','Sevilla : Wanceulen, DL 2009','9788498236972'),
		array('9788461265602',2,'Técnica y pedagogía del esquí alpino : manual del ','Puente, Robert','Madrid : l\'autor, 2008'),
		array('9780297855255',2,'The Girl on the landing / Paul Torday','Torday, Paul','London : Weidenfeld & Nicolson, '),
		array('9780385616157',2,'The Other family / Joanna Trollope','Trollope, Joanna','London [etc.] : Doubleday, cop. 2010'),
		array('9788467901863',2,'Todo el polvo del camino / Wander Antunes & Jaime ','Antunes, Wander','Barcelona : Norma, 2010'),
		array('9788498676587',2,'Todo Marlowe / Raymond Chandler','Chandler, Raymond','Barcelona : RBA, 2009'),
		array('9788497874540',2,'Una Burgesia sense ànima : el franquisme i la traïció ','Vilanova, Francesc','Barcelona : Empúries, 2010'),
		array('9788496995253',2,'Vida i mort de la República espanyola / Henry Buckley ','Buckley, Henry','Vilafranca del Penedès : Andana, '),
		array('9788496974333',3,'Confianza en uno mismo / Ralph Waldo Emerson ; ','Emerson, Ralph Waldo','Madrid : Gadir, cop. 2009'),
		array('9788420651620',3,'Debo todo a tu olvido / Malika Mokeddem ; traducción ','Mokeddem, Malika','Madrid : Alianza, DL 2010'),
		array('9788479027391',3,'Ejercicios de entrenamiento para jóvenes futbolistas : ','Koger, Robert','Madrid : Tutor, cop. 2009'),
		array('9788498413816',3,'El Amor verdadero / José María Guelbenzu','Guelbenzu, José María','Madrid : Siruela, cop. 2010'),
		array('9788408088448',3,'El Club de la buena estrella / Amy Tan ; traducción de ','Tan, Amy','Barcelona : Planeta, 2009'),
		array('9788408089032',3,'El Salón de ámbar / Matilde Asensi','Asensi, Matilde','Barcelona : Planeta, 2009'),
		array('9788492865130',3,'Formas del amor / David Garnett ; traducción de ','Garnett, David','Cáceres : Periférica; 2010'),
		array('9788487520907',3,'Fútbol : 1380 juegos globales para el entrenamiento de','López López, Javier','Sevilla : Wanceulen, 2009'),
		array('8479024984978840',3,'Guía maestra del entrenamiento del ciclista : más ','Carmichael, Chris','Madrid : Tutor, DL 2005'),
		array('9788492663170',3,'Jernigan / David Gates ; prólogo de Rodrigo Fresán ; ','Gates, David','Barcelona : Libros del Asteroide, '),
		array('9788432228667',3,'La Sal de la vida / Anna Gavalda ; traducción del ','Gavalda, Anna','Barcelona : Seix Barral, 2010'),
		array('9788427035874',3,'Las Puertas templarias : un secreto medreval esrá a ','Sierra, Javier','Barcelona : Planeta, 2009'),
		array('9788447034840',3,'Legislación de régimen local','Espanya','Cizur Menor : Thomson/Civitas, '),
		array('',3,'Los Años del exterminio (1939-1945) / Saul ','Friedländer, Saul','Barcelona : Galaxia Gutenberg : '),
		array('9788441526525',3,'Mac OS Snow Leopard / María Guerrero','Guerrero, María','Madrid : Anaya Multimedia, cop. 2010'),
		array('9788468101408',3,'Manual de informática : oposiciones a corporaciones ','Álvarez Fernández, José Luis','Humanes de Madrid : CEP, 2010'),
		array('9788479027636',3,'Manual de natación total : emtrenamiento olímpico para ','Evans, Janet (Nadadora)','Madrid : Tutor, cop. 2009'),
		array('9788479027452',3,'Maratón : la más completa y actual guía de ','Higdon, Hal','Madrid : Tutor, cop. 2009'),
		array('9788492470136',3,'Mil nombres para el gozo : vivir en armonía con las ','Katie, Byron','Barcelona : La Liebre de Marzo, '),
		array('9788432298363',3,'Muerte en La Fenice / Donna Leon','Leon, Donna','Barcelona : Planeta, 2009'),
		array('9788474861990',3,'Náutica deportiva : curso de patrón de embarcaciones','Nadal de Ulher, Manuel','Barcelona : Noray, 2009'),
		array('9788432228698',3,'País de sombras/ Peter Matthiessen ; traducción del ','Matthiessen, Peter','Barcelona : Seix Barral, 2010'),
		array('9788423342617',3,'Planificación familiar / Karan Mahajan ; traducción de ','Mahajan, Karan','Barcelona : Destino, 2010'),
		array('9788466124195',3,'Taoshira / Julia Golding ; traducció de Josep Sampere','Golding, Julia','Barcelona : Cruïlla, 2010'),
		array('9788479027896',3,'Técnica individual de fútbol sala / Francisco Luque ','Luque Hoyos, Francisco','Madrid : Tutor, cop. 2009'),
		array('9788433972118',3,'Tiempo de vida / Marcos Giralt Torrente','Giralt Torrente, Marcos','Barcelona : Anagrama, 2010'),
		array('9788432212864',3,'Triste, solitario y final / Osvaldo Soriano ; prólogo de ','Soriano, Osvaldo','Barcelona : Seix Barral, 2010'),
		array('9788497769501',3,'Turismo de balnearios en España / [texto: Silvia Roba ','Madrid : Anaya Touring Club, 2010','9788497769501'),
		array('9788432232008',4,'Cómo no escribir una novela / Howard Mittelmark, ','Mittelmark, Howard','Barcelona : Seix Barral, 2010'),
		array('9788489624665',4,'El Arte de la pareja : saber asir, saber soltar / Ramiro ','Calle, Ramiro A.','Madrid : Kailas, cop. 2009'),
		array('9788498252521',4,'La Caputxeta vermella / [Perrault] ; il·lustracions: ','Perrault, Charles','Barcelona : Combel, 2007'),
		array('8480198915978840',4,'Nadar con bebés y niños pequeños : diversión en el ','Ahr, Barbara','Badalona : Paidotribo, cop. 2006'),
		array('9788478979684',4,'Windows 7 / Pablo Casla Villares, José Luis Raya ','Casla Villares, Pablo','Madrid : Ra-Ma, cop. 2010'),
		array('9788420011363',5,'Bienestar animal','Zaragoza : Acribia, 2010','9788420011363'),
		array('9788448068103',5,'Bon Jovi : when we are beautiful / conversaciones ','Griffin, Phil','Barcelona : Cúpula, 2010'),
		array('9788424907372',5,'Cartas filosóficas / Séneca ; presentación de Antonio ','Sèneca, Luci Anneu','Madrid : Gredos, 2010'),
		array('9788498853223',5,'Desgarrada / Whedon, Cassaday','Whedon, Joss','Torroella de Montgrí : Panini, DL 2009'),
		array('9788466784696',5,'El Eslabón de cristal / Andreu Martín y Jaume Ribera ; ','Martín, Andreu','Madrid : Anaya, 2010'),
		array('9788484834106',5,'El Origen de las fiestas : la cristianización del ','Domené Sánchez, Domingo','Madrid : Laberinto, cop. 2010'),
		array('9788435018807',5,'El Viejo León : Tolstoi, un retrato literario / Mauricio ','Wiesenthal, Mauricio','Barcelona : Edhasa, cop. 2010'),
		array('9788466219389',5,'Escuela de pintura del retrato / Pablo Comesaña','Comesaña, Pablo','Alcobendas : Libsa, cop. 2010'),
		array('9788480237543',5,'Estados Unidos : Costa Este / [texto: Manuel Monreal ','Monreal, Manuel','Madrid : Gaesa, DL 2010'),
		array('9788447032983',5,'Función pública : normas básicas / [edición preparada ','Espanya','[Madrid] : Thomson/Civitas, 2009'),
		array('9788497166522',5,'Irán por dentro : la otra historia : guía cultural de la ','Kavanagh, Alfred G.','[Palma de Mallorca] : Olañeta, [etc.], '),
		array('9788492422180',5,'La Homeopatía : ¡vaya timo! / Víctor-Javier Sanz','Sanz, Víctor-Javier','Pamplona : Laetoli, 2010'),
		array('',5,'La Lectura / Antonio Basanta Reyes ... [et al.]','Madrid : CSIC : Catarata, cop. 2010','0'),
		array('9788466784702',5,'La Princesa y el traidor / Andreu Martín y Jaume ','Martín, Andreu','Madrid : Anaya, 2010'),
		array('9788447034055',5,'Ley de enjuiciamiento civilLegislación sobre ','Espanya','Cizur Menor : Civitas [etc.], 2010'),
		array('9788499032733',5,'Lleis, etc.Leyes administrativas / edición preparada ','Espanya','Cizur Menor : Thomson Reuters : '),
		array('9788480166942',5,'Mi hijo aprende jugando / Marc Giner','Giner, Marc','Barcelona : Larousse, cop. 2010'),
		array('9788428215350',5,'Minerales y rocas / Rupert Hochleitner','Hochleitner, Rupert','Barcelona : Omega, cop. 2010'),
		array('9788497887182',5,'Orientación profesional / Benito Echeverría Samanes, ','Isus, Sofía','Barcelona : Universitat Oberta de '),
		array('9788492678303',5,'Peces de acuario / texto de Ivan Petrovicky','Petrovický, Ivan','Madrid : Tikal, [2010?]'),
		array('9788498291896',5,'Pica d\'Estats : 5 vías a la cumbre / Carles Gel','Gel, Carles','Madrid : Desnivel, 2010'),
		array('9788497856379',5,'Reservas de la biosfera de España / [textos Xabier ','Barcelona [etc.] : Lungwerg, cop. ','9788497856379'),
		array('9788461372577',5,'Tal com ho vaig viure [1927-1958] / Joan Reventós i ','Reventós, Joan','Barcelona : Fundació Rafael '),
		array('9788496960398',5,'Técnica contable / J. P. Tarango','Tarango, J. P.','[Barcelona] : Ceysa, 2010'),
		array('9788430951024',5,'Tractat de Maastricht (1992)Tratado de la Unión ','Madrid : Tecnos, 2010','9788430951024'),
		array('9788492963225',5,'Varsovia y Cracovia / Jordi Bastart','Bastart, Jordi','Barcelona : Alhena Media, 2010'),
		array('9788449323942',6,'¿Se creen que somos tontos? : 100 formas de ','Baggini, Julian','Barcelona [etc.] : Paidós, 2010'),
		array('9788497916882',6,'Garrotxa : 17 excursions en BTT / Sergi Lara','Lara, Sergi','Valls : Cossetània, 2004'),
		array('9788474109948',7,'¿Què vol dir integració? : nouvinguts i establerts a les ','Bilbeny, Norbert','Barcelona : La Magrana, 2010'),
		array('9788425519239',7,'Billar : modalidad a la banda / Valeriano Parera Sans','Parera Sans, Valeriano','L\'Hospitalet de Llobregat : Hispano '),
		array('9788492595419',7,'Coses que passen de tant en tant / Kestutis ','Kasparavicius, Kestutis','[Barcelona] : Thule, DL 2009'),
		array('9788437074115',7,'Ecologia viscuda / Jaume Terradas','Terradas, Jaume','València : Universitat de València, '),
		array('9788448926045',7,'El Dia del voltor / Maria Àngels Juanmiquel','Juanmiquel, Maria Àngels','Barcelona : Barcanova, 2010'),
		array('9788483431023',7,'El Magnetitzador : un esdeveniment familiar / Ernst ','Hoffmann, Ernst Theodor Amadeus','Barcelona : Bambú, 2010'),
		array('9788484183945',7,'El Meu petit manual d\'experiments : la vida i la terra : ','Pérez, Mélanie','Sant Boi de Llobregat : Zendrera '),
		array('9788497916769',7,'El Montseny : 50 itineraris a peu / Francesc Roma i ','Roma i Casanovas, Francesc','Barcelona : Cossetània, 2010'),
		array('9788483195116',7,'El Próspero negocio de la piratería en África / Miguel ','Salvatierra, Miguel','Madrid : Catarata : Casa África, 2010'),
		array('9788492874064',7,'El Retorn dels catalans / Patrícia Gabancho','Gabancho, Patrícia','Barcelona : Meteora, 2010'),
		array('9788429766509',7,'El Violí d\'Auschwitz / Maria Àngels Anglada','Anglada, Maria Àngels','Barcelona : Edicions 62, 2010'),
		array('9788497915663',7,'Els Animals de companyia / autor: Patrick David ; ','David, Patrick','Valls : Cossetània, 2010'),
		array('9788480903554',7,'Estanys d\'Andorra / F. Xavier Gregori, Rosa M. ','Gregori, F. Xavier','Granollers : Alpina, 2010'),
		array('9788493780937',7,'Guía literaria de Roma : edición y prólogo a cargo de ','Barcelona : Ático de los Libros, 2010','9788493780937'),
		array('9788497881258',7,'La Logopèdia / Anna Nolla, Anna Tàpias','Nolla Casals, Anna','Barcelona : UOC, 2010'),
		array('9788448925697',7,'La Pluja als llavis / Eulàlia Canal','Canal, Eulàlia','Barcelona : Barcanova, 2010'),
		array('9788497888448',7,'L\'Adopció / Montserrat Alguacil, Mercè Pañellas','Alguacil de Nicolas, Montserrat','Barcelona : UOC, 2009'),
		array('9788489625686',7,'L\'Ull del corb / Shane Peacock ; traducció de Carme ','Peacock, Shane','Barcelona : Castellnou, 2010'),
		array('9788496924895',7,'Mundo gótico / Gavin Baddeley ; traducción de Valeria ','Baddeley, Gavin','Teià : Ma Non Troppo, DL 2010'),
		array('9788426137647',7,'Picasso i Minou / P. I. Maltbie; il·lustrat per Pau Estrada','Maltbie, P. I.','Barcelona : Joventut, 2010'),
		array('9788492595457',7,'Receptes de pluja / Eva Manzano, Mónica Gutiérrez ','Manzano, Eva','Barcelona : Thule, 2010'),
		array('9788447919932',7,'Sant Jordi mata l\'aranya / Joaquim Carbó ; ','Carbó, Joaquim','Barcelona : Baula, 2010'),
		array('9788423207404',7,'Sant Jordi, patró de Catalunya / Pere Anguera','Anguera, Pere','Barcelona : Dalmau, 2010'),
		array('9788447919987',7,'Un Petit gegant / Montse Tobella','Tobella, Montserrat','Barcelona : Baula, 2010'),
		array('9788447920051',7,'Una Amiga de por! / Franziska Gehm ; traduït per ','Gehm, Franziska','Barcelona : Baula, 2010'),
		array('9788434469266',8,'50 cosas que hay que saber sobre genética / Mark ','Henderson, Mark','Barcelona : Ariel, 2010'),
		array('9788471538550',8,'Diccionario manual: español-chino, [chino-español]','Barcelona : Vox, 2010','9788471538550'),
		array('9788492902088',8,'El Velo / El Torres, Gabriel Hernández','Torres, El','Madrid : Dibbuks, cop. 2010'),
		array('9788497663571',8,'Espiadimonis, nàiades, sabaters i cuques de capsa : ','Manlleu : Centre d\'Estudis dels Rius ','9788497663571'),
		array('9788467901498',8,'Las Extraordinarias aventuras de Adèle Blanc-Sec 1 / ','Tardi, Jacques','Barcelona : Norma, 2010'),
		array('9788430950973',8,'Legislación laboral y de seguridad social / edición ','Espanya','Madrid : Tecnos, 2010'),
		array('9788492902132',8,'Los Impostores / Christian Cailleaux','Cailleaux, Christian','Madrid : Dibbuks, 2010'),
		array('9788498014464',8,'Telas para moda : guía de fibras naturales / Clive ','Hallett, Clive','Barcelona : Blume, 2010'),
		array('V. 5',9,'El Dulce hogar de Chi / Konami Kanata','Kanata, Konami','Barcelona : Glénat, DL 2009'),
		array('9788424913335',9,'Ética a Nicómaco / Aristóteles ; presentación de ','Aristòtil','Madrid : Gredos, 2010'),
		array('9788492748235',9,'Història d\'un eriçó / Asun Balzola ; traducció de Ricard','Balzola, Asun','Barcelona : Associació de Mestres '),
		array('9788475069227',9,'Justicia salvaje : la vida moral de los animales / Marc ','Bekoff, Marc','Madrid : Turner, DL 2010'),
		array('9788434469181',9,'Kluge : la azarosa construcción de la mente humana / ','Marcus, Gary F.','Barcelona : Ariel, 2010'),
		array('9786074002133',9,'La Pequeña tristeza / Anne Herbauts','Herbauts, Anne','Barcelona [etc.] : Océano Travesía, '),
		array('9788423207435',9,'L\'Evolució  del cervell / Enric Bufill','Bufill, Enric','Barcelona : Dalmau : Institut Català '),
		array('9788492750061',9,'Pomelo viaja / Ramona Bãdescu, Benjamin Chaud','Bãdescu, Ramona','Barcelona : Kókinos, cop. 2010'),
		array('9788444145105',9,'Un Cuerpo cambiante : todo lo que necesitas saber ','Bailey, Gerry','León : Everest, DL 2010'),
		array('9788466122658',9,'Víkings / text de Sylvie Baussier ; il·lustracions de Dan ','Baussier, Sylvie','Barcelona : Cruïlla, 2009'),
		array('9788493707910',10,'¿Has aprendido? : [Vermeer] / Cati Wajs','Wajs, Cati','[Zaragoza] : Laberinto de las Artes, '),
		array('9788447920150',10,'A jugar / Liesbet Slegers','Slegers, Liesbet','Barcelona : Baula, 2010'),
		array('9788492758791',10,'A mi no se\'m moren les plantes : una manera divertida ','Burés, Silvia','Barcelona : Angle, 2010'),
		array('9788429766523',10,'Afirma Pereira / Antonio Tabucchi ; traducció de ','Tabucchi, Antonio','Barcelona : Edicions 62, 2010'),
		array('9788461261314',10,'Ajedrez a tu alcance : de cero a cien años / Manuel ','Pérez Candelario, Manuel','Badajoz : AEEA, cop. 2008'),
		array('9788467901726',10,'Alícia en un món real / idea i guió: Isabel Franc ; ','Franc, Isabel','Barcelona : Norma, 2010'),
		array('9788493776701',10,'Anem a veure el pare! / Lawrence Schimel [text] ; Alba','Schimel, Lawrence','Barcelona : Ekaré, 2010'),
		array('9788484233275',10,'Animals i les seves famílies','Barcelona : Elfos, 2010','9788484233275'),
		array('9788425519321',10,'Aprende a bailar sevillanas / Susana Salvador','Salvador, Susana','L\'Hospitalet de Llobregat : Hispano '),
		array('9788493668259',10,'Árabe de supervivencia : guía de conversación con ','Catalán, Matías','Palma de Mallorca : Vessants, 2010'),
		array('9788499280028',10,'Armas de fuego : militares y deportivas del siglo XX / ','Madrid : Tikal, [s.a.]','9788499280028'),
		array('9788497544030',10,'Ciencia / Peter Moore','Moore, Peter D.','Barcelona : Oniro, cop. 2009'),
		array('9788431542207',10,'Cómo evitar un accidente de tráfico : guía para ','Bort Juan, Miquel','Barcelona [etc.] : De Vecchi, cop. '),
		array('9788496957787',10,'Constructores / Xulio Gutiérrrez ; ilustraciones de ','Gutiérrez, Xulio','Sevilla : Faktoria K de Libros, cop. '),
		array('9788424634391',10,'Cornèlius i el rebost d\'impossibles / Carles Sala i Vila ; ','Sala i Vila, Carles','Barcelona : La Galera, 2010'),
		array('9788484184362',10,'Creacions per a tot l\'any  Delphine Glachant','Glachant, Delphine','Barcelona : Zendrera Zariquiey, '),
		array('9788498741223',10,'Curso de doblar servilletas : paso a paso / Sieglinde ','Holl, Sieglinde','Madrid : Drac, cop. 2010'),
		array('9788424907389',10,'El Banquete / Platón ; presentación, traducción y notas','Plató','Madrid : Gredos, 2010'),
		array('9788492412549',10,'El Dubte / Pia Valentinis','Valentinis, Pia','Barcelona [etc.] : Libros del Zorro '),
		array('9788448830533',10,'El Fantasma de l\'òpera / basat en l\'obra original de ','Leroux, Gaston','Barcelona : Lumen, 2010'),
		array('9788466793025',10,'El Gato : (o cómo perdí la eternidad) / Jutta Richter ; ','Richter, Jutta','Madrid : Anaya, 2010'),
		array('9788484526308',10,'El Gran llibre de les famílies / Mary Hoffman ; ','Hoffman, Mary','[Barcelona] : Intermón Oxfam, 2010'),
		array('9788434236509',10,'El Gran llibre de relats de pirates i corsaris / textos: ','Vinyoli Sastre, Joan','Barcelona : Parramón, 2010'),
		array('9788483431047',10,'El Malalt imaginari / Molière ; traducció d\'Anna ','Molière','Barcelona : Bambú, 2010'),
		array('9788497799072',10,'El Nen i la mort : acompanyar els infants i els ','Esquerda i Aresté, Montse','Lleida : Pagès, 2010'),
		array('9788492750221',10,'El Secreto de Garmann / Stian Hole','Hole, Stian','[Madrid] : Kókinos, cop. 2010'),
		array('9788496726673',10,'Els Meus avis / Guido Van Genechten','Genechten, Guido van','Barcelona : Animallibres, 2009'),
		array('9788484233213',10,'Els Vaixells / [il·lustracions: Anne Ebert ; text: Andrea ','Erne, Andrea','Barcelona : Elfos, cop. 2010'),
		array('9788444145143',10,'Eres lo que comes / Felicia Law y otros autores ; ','León : Everest, DL 2010','9788444145143'),
		array('9788424907396',10,'Fedro / Platón ; presentación, traducción y notas de ','Plató','Madrid : Gredos, 2010'),
		array('9788466123747',10,'Formes / Thierry Laval','Laval, Thierry','Barcelona : Cruïlla, 2010'),
		array('9788403509535',10,'Ibiza / [textos: Silvia Castillo y Xescu Prats]','Castillo, Silvia','Madrid : El País/Aguilar, cop. 2010'),
		array('9788434019089',10,'La Administración tributaria y el ciudadano / Santiago ','Herrero Suazo, Santiago','Madrid : Boletín Oficial del Estado, '),
		array('9788478279111',10,'La Biblioteca escolar, hoy : un recurso estratégico ','Durban Roca, Glòria','Barcelona : Graó, 2010'),
		array('9788498677850',10,'La Ciencia del futuro : los mejores investigadores del ','Barcelona : RBA, 2010','9788498677850'),
		array('9788483068472',10,'La Evolución del talento : de Atapuerca a Silicon Valley','Bermúdez de Castro, José María','Barcelona : Debate, 2010'),
		array('V. 7',10,'La Leyenda de madre Sarah / Katsuhiro Otomo, ','Otomo, Katsuhiro','Barcelona : Norma, 2008'),
		array('9788467900118',10,'La Llacuna dels misteris / història i dibuix: Hugo Pratt ; ','Pratt, Hugo','Barcelona : Norma, 2010'),
		array('9788448925666',10,'La Moli i la Doli / Pep Molist ; il·lustracions de Valentí ','Molist, Pep','Barcelona : Barcanova, 2010'),
		array('9788426137869',10,'La Senyora dels llibres / Heather Henson; il·lustrat per ','Henson, Heather','Barcelona : Joventut, 2010'),
		array('9788447919970',10,'La Vaca cega i altres poemes / Joan Maragall ; ','Maragall, Joan','Barcelona : Baula, 2010'),
		array('9788483430965',10,'La Veritable confessió de Charlotte Doyle / Avi ; ','Avi, 1937-','Barcelona : Bambú, 2010'),
		array('9788403508750',10,'Las 22 mejores rutas por Cataluña / [textos y ','Esain, Guillermo','Madrid : El País/Aguilar, cop. 2010'),
		array('9788449439599',10,'Las Lavanderas locas / John Yeoman & Quentin Blake','Yeoman, John','Barcelona [etc.] : Océano Travesía, '),
		array('',10,'Les Cries dels animals','Barcelona : Cruïlla, 2002','0'),
		array('9788498832310',10,'Les Mateixes estrelles / Núria Martí Constans','Martí, Núria','[Barcelona] : Comissions Obreres '),
		array('9788429766493',10,'L\'Illa de l\'holandès / Ferran Torrent','Torrent, Ferran','Barcelona : Edicions 62, DL 2010'),
		array('9788423342600',10,'Los Años divinos : memorias del señor Bocaccio, el ','Regàs, Oriol','Barcelona : Destino, 2010'),
		array('9788499211060',10,'Los Signos de puntuación : para aprender el uso de la ','Ciruelo, Pilar','Barcelona : Octaedro, 2010'),
		array('9788499060460',10,'L\'Ovelleta Bela / Knister ; il·lustracions d\'Eve Tharlet ; ','Knister','Barcelona : Bruixola, cop. 2009'),
		array('9788429766271',10,'L\'Última nit a Twisted River / John Irving ; traducció de ','Irving, John','Barcelona : Edicions 62, 2010'),
		array('9788461412297',10,'Messi : la gloria del fútbol','[Barcelona] : Fundación Leo Messi, ','9788461412297'),
		array('9788467901283',10,'Mi nombre es Paddle... Kid Paddle / guión y dibujo: ','Midam','Barcelona : Norma, 2010'),
		array('9788408093015',10,'Misión : eliminar a toxina / Joann Sfar, Emmanuel ','Sfar, Joann','Barcelona : Planeta Junior, 2010'),
		array('9788466643979',10,'Mundial 2010 : una aventura de Mortadelo y Filemón / ','Ibáñez, Francisco','Barcelona [etc.] : Ediciones B, 2010'),
		array('9788477274780',10,'No hi ha terceres persones / Empar Moliner','Moliner, Empar','Barcelona : Quaderns Crema, 2010'),
		array('9788434236776',10,'Nyam, nyam! : [els meus contes infantils preferits] / ','Cousins, Lucy','Barcelona : Parramón, cop. 2010'),
		array('9788431541804',10,'Ortografía correcta del catalán / Escuela de Idiomas ','Niubò, Ramon','Barcelona : De Vecchi, cop. 2010'),
		array('9788484125396',10,'Papiroflèxia per a nens: senzills projectes d\'origami ','Pomarón, Sara','[Barcelona] : Salvatella, cop. 2010'),
		array('9788492819348',10,'Paul McCartney : la biografía / Peter Ames Carlin ; ','Carlin, Peter Ames','Barcelona : Viceversa,  cop. 2010'),
		array('9788484184119',10,'Personatges simpàtics / Godeleine de Rosamel','Rosamel, Godeleine de','Barcelona : Zendrera Zariquiey, '),
		array('9788498832419',10,'Pirates a la vista! / Núria Albertí ; música: Xavier Oró i ','Albertí, Núria','Barcelona : Abadia de Montserrat, '),
		array('9788484184201',10,'Reciclatge creatiu / Véronique Guillaume','Guillaume, Véronique','Barcelona : Zendrera Zariquiey, '),
		array('9788461267194',10,'Relatos españoles contemporáneos : [audiolibro nivel ','Madrid : Habla con Eñe, 2008','9788461267194'),
		array('9788448926076',10,'Sant Jordi i el drac / Núria Pradas ; il·lustracions de ','Pradas, Núria','Barcelona : Barcanova, 2010'),
		array('9788448926090',10,'Sóc un ós / Carles Sala i Vila','Sala i Vila, Carles','Barcelona : Barcanova, 2010'),
		array('9788484184102',10,'Targetes divertides : felicitacions, aniversaris, ','Barcelona : Zendrera Zariquiey, ','9788484184102'),
		array('9788447919871',10,'Terra a la vista, senyor Coc! / Jo Lodge','Lodge, Jo','Barcelona : Baula, 2010'),
		array('9788484703655',10,'Tinc pipí / Émile Jadoul','Jadoul, Émile','Barcelona : Corimbo,  2010'),
		array('9788448926038',10,'Tramuntana a la granja! / Carles Sala i Vila ; ','Sala i Vila, Carles','Barcelona : Barcanova, 2010'),
		array('',10,'Tres històries de neguit  / Baltasar Porcel','Porcel, Baltasar','Vic : Eumo ; Barcelona : Universitat '),
		array('9788441527362',10,'Tuenti / José Mendiola Zuriarráin','Mendiola Zuriarráin, José','Madrid : Anaya Multimedia, DL 2010'),
		array('9788408090434',10,'Una Ciudad llena de magia','Barcelona : Planeta Junior, cop. 2010','9788408090434'),
		array('9788495987686',10,'Vols ser el meu germà gran? / Carl Norac ; ','Norac, Carl','[Sant Cugat del Vallès] : Símbol, 2009'),
		array('9788492595341970',10,'Vulèvulà / Olga Molina','Molina Jiménez, Olga','Barcelona : Thule, 2009'),
		array('9788493366575840',10,'Xarxa catalana de senders i de rutes temàtiques / per ','Jerez i Amat, Josep M.','Barcelona : Associació Catalana de '),
		array('9788467900286',10,'Yakari  / Derib + Job','Derib','Barcelona : Norma, 2010'),
		array('9788447921140',10,'Yumi / Annelore Parot','Parot, Annelore','Barcelona : Baula, 2010'),
		array('',11,'Lulú, mujer desnuda / Étienne Davodeau','Davodeau, Étienne','Barcelona : La Cúpula, 2010'),
		);

		$idc = 4755;
		$dto = 15;
		$ids = 803;

		$this->_create_pedido($libros, $idc, $dto, $ids);
	}

	private function _create_pedido($libros, $idc, $dto, $ids)
	{
		error_reporting(E_ERROR);
		$pedido['nIdCliente'] = $idc;
		$this->load->model('catalogo/m_articulo');
		$this->load->library('ISBNEAN');
		$si = 0;
		$no = 0;
		echo '<pre>';
		foreach($libros as $l)
		{
			if ($this->isbnean->is_isbn($l[0]))
			{
				$isbn = $this->isbnean->to_ean($l[0]);
				$reg = $this->m_articulo->get(null, null, null, null, "nEAN={$isbn}");
				if (count($reg) == 1)
				{
					$reg = $reg[0];
					$pedido['lineas'][] = array(
						'nIdLibro' 		=> $reg['nIdLibro'],
						'nIdSeccion'	=> $ids,
						'fDescuento'	=> $dto,
						'nCantidad'		=> $l[1],
						'fCoste'		=> $reg['fPrecioCompra'],
						'fIVA'			=> $reg['fIVA'],
						'fPrecio'		=> $reg['fPrecio']
					);
					++$si;
				}
				else
				{
					echo utf8_decode("NO {$isbn} - {$l[0]} - {$l[1]} - {$l[2]} - {$l[3]}\n");
					++$no;
				}
			}
			else
			{
				echo utf8_decode("NO - {$l[0]} - {$l[1]} - {$l[2]} - {$l[3]}\n");
				++$no;
			}
		}
		echo "ENCONTRADOS: {$si}, NO ENCONTRADOS: {$no}\n";
		die();
		$this->load->model('ventas/m_pedidocliente');
		$idp = $this->m_pedidocliente->insert($pedido);
		if ($idp<0)
		{
			echo "ERROR Creando pedido: {$this->m_pedidocliente->error_message()}\n";
		}
		else
		{
			echo "Pedido creado: {$idp}\n";
		}
		echo '</pre>';
	}

	function bonpastor()
	{
		$libros = array(
		array(' 84-392-8082-3  ',1,'I*** Far. Farias, Juan. Crónicas de media tarde. Madrid: Gaviota, 1997. ISBN: 84-392-8082-3 / 84-392-1601-7 '),
		array(' 8432715009801  ',1,'IC Yos. Yoshizumi, Wataru. Random Walk. Barcelona: Planeta DeAgostini Comics. Vol. 2 i 3. ISBN: 8432715009801 / 8432715009818  desiderata de Laia Conom Pallás '),
		array('9788483577202',1,'IC Nee Neel, Julien. El Cementerio de los autobuses. Barcelona: Glénat, cop. 2009 [Lou! ; 2]. ISBN: 9788483577202 (Carla Sánchez Cárcel / 93 180 15 30)'),
		array(' 8472903036 ',1,'I** Gil. Gil Vila, Maria Àngels. M\'ho va dir la lluna. Barcelona: Bellaterra, 2005. ISBN / ISSN: 8472903036 '),
		array(' 8423316904 ',1,'I* Col. Cole, Babette. La Princesa enjogassada. Barcelona: Destino, 1990. ISBN/ISSN: 8423316904. '),
		array('978-84-479-1624-5      ',1,'La tieta Adela a Sevilla. Pradas, N. Baula. ISBN: 978-84-479-1624-5      '),
		array('978-84-263-5948-3',1,'Rumbo Sur. Edelvives, 2010. Alonso, M. ISBN: 978-84-263-5948-3'),
		array('978-84-7942-401-5  ',1,'Lalana, F. Indústries GON. Macmillan iberia, 2009. ISBN: 978-84-7942-401-5  '),
		array('978-84-661-2095-1',1,'Valat, Pierre-Marie. L\'Aigua. ISBN 13: 978-84-661-2095-1'),
		array('978-84-261-1864-6 ',1,'Al Llit, Joventut. Oxenbury, Helen. ISBN 13: 978-84-261-1864-6 '),
		array('978-84-263-5094-7',1,'¿Un caracol? (Col. Veo, veo. Edelvives. Genechten, Guido Van. ISBN 13: 978-84-263-5094-7'),
		array('978-84-207-8455-7',1,'Historias sin fin, Anaya. Mari, Iela. ISBN 13: 978-84-207-8455-7'),
		array('978-84-8470-026-5 ',1,'-Ashbé, Jeanne. Fins al vespre! Corimbo, 2001. ISBN 13: 978-84-8470-026-5 '),
		array('978-84-95987-69-3',1,'-Genechten, Guido Van. Una abraçada! Símbol, 2003. ISBN 13: 978-84-95987-69-3'),
		array('978-84-8470-156-9 ',1,'-Norac, Carl; Dubois, Claude K. M’estimes o no m’estimes? Corimbo, 2004. ISBN 13: 978-84-8470-156-9 '),
		array('978-84-246-2095-0',1,'-Albo, Pablo. L’espantaocells. La Galera, 2005. ISBN 13: 978-84-246-2095-0'),
		array('978-84-264-3788-4 ',1,'-Escala, Jaime; Solé Vendrell, Carme. Magenta la petita fada. Lumen, 2003. ISBN 13: 978-84-264-3788-4 '),
		array('978-84-666-1286-9 ',1,'-Faulkner, Keith. Al monstruo le gustan los libros. Ediciones B, 2004. ISBN 13: 978-84-666-1286-9 '),
		array('978-84-261-3463-9 ',1,'-Gutman, Anne; Hallensleben, Georg. La funció de màgia. Joventut, 2005. ISBN 13: 978-84-261-3463-9 '),
		array('978-84-88342-38-6 ',1,'-Jeram, Anita. Inés del revés. Kókinos, 2010. ISBN 13: 978-84-88342-38-6 '),
		array('978-84-8464-653-2 ',1,'-Léveque, Anne-Claire.; Corazza, Lynda. ¡Mira que paso! Kalandraka, 2007. ISBN 13: 978-84-8464-653-2 '),
		array('978-84-7655-451-7 ',1,'-Oram, Hiawyn. En el desván. Fondo de Cultura Económica, 2002. ISBN 13: 978-84-7655-451-7 '),
		array('978-84-661-1153-9 ',1,'-Schärer, Kathrin. El llebretó i la guineu. Cruïlla, 2005. ISBN 13: 978-84-661-1153-9 '),
		array('978-84-96573-29-1',1,'-Morandeira, Luisa. Mister Corb. OqO, 2006. ISBN 13: 978-84-96573-29-1'),
		array('978-84-246-1467-6 ',1,'-Desclot, Miquel. Fava, favera…. La Galera, 1998. ISBN 13: 978-84-246-1467-6 '),
		array('978-84-246-1476-8 ',1,'-Duran, Teresa. La guilla i el gall: rondalla popular catalana. La Galera, 1999. ISBN 13: 978-84-246-1476-8 '),
		array('978-84-7826-807-8 ',1,'-Muntaner, Joan Carles. En Joan petit. Abadia de Montserrat, 1997. ISBN 13: 978-84-7826-807-8 '),
		array('978-84-474-1118-4 ',1,'-Olivé, Pau. El conte de les mules. Cadí, 2003. ISBN 13: 978-84-474-1118-4 '),
		array('978-84-233-3231-1 ',1,'-Ahlberg, Janet; Ahlberg, Allan. El carter joliu, o, Unes cartes especials. Destino, 2006. ISBN 13: 978-84-233-3231-1 '),
		array('978-84-7629-997-5',1,'-Atxaga, Bernardo. La Xola i els lleons. Cruïlla, 1996. ISBN 13: 978-84-7629-997-5'),
		array('9681649036',1,'-Banyai, Istvan. Zoom. Fondo de Cultura Económica, 2005. ISBN: 9681649036 '),
		array('978-84-264-1365-9',1,'-Bernard, Fred. El tren amarillo. Lumen, 2003. ISBN 13: 978-84-264-1365-9'),
		array('978-84-261-2601-6',1,'-Meva mare es rara, la (1991). Gilmore, Rachna. ISBN 13: 978-84-261-2601-6'),
		array('968167359X ',1,'-Browne, Anthony. La meva mama. Fondo de cultura económica, 2005. ISBN: 968167359X '),
		array('978-84-667-2472-2 ',1,'-Heine, Helme. El coche de carreras. Anaya, 2010. ISBN 13: 978-84-667-2472-2 '),
		array('978-84-261-3883-5 ',1,'-Hicks, Barbara Jean. Nyic-nyec garranyec! Joventut, 2009. ISBN 13: 978-84-261-3883-5 '),
		array('978-84-88342-18-8 ',1,'-Kiss, Kathrin. ¿Qué hace un cocodrilo por la noche? Kókinos, 2006. ISBN 13: 978-84-88342-18-8 '),
		array('978-84-95987-04-4',1,'-Kitamura, Satoshi. Les aventures d’en Maulet. Símbol, 2003. ISBN 13: 978-84-95987-04-4'),
		array('8495730545',1,'-Lobel, Arnold. Sopa de ratolí. Kalandraka, 2004. ISBN: 8495730545'),
		array('978-84-261-3457-8 ',1,'-Maar, Paul. Jo sóc el més alt. Joventut, 2005. ISBN 13: 978-84-261-3457-8 '),
		array('978-84-932616-5-8 ',1,'-Masini, Beatrice. Una núvia graciosa, vistosa, preciosa. Tuscania, 2002. ISBN 13: 978-84-932616-5-8 '),
		array('9788481316230',1,'-Batiste, Enric. El secret de les titelles. Tàndem, 2006. ISBN: 9788481316230'),
		array('978-84-8418-165-1   ',1,'-Noguès, Jean-Come. El geni del gessamí. Zendrera Zariquiey, 2003 '),
		array('978-84-8470-224-5  ',1,'-Solotareff, Grégoire. El rey cocodrilo. Corimbo, 2006. 978-84-8470-224-5  '),
		array('978-84-661-0043-4',1,'-Ward, Helen. El rei dels ocells. Cruïlla, 2000. 978-84-661-0043-4'),
		array('978-84-661-0719-8',1,'-Albanell, Josep. Conte de riure, conte de plorar. Cruïlla, 2003 978-84-661-0719-8'),
		array('978-84-661-0607-8   ',1,'-Comelles, Salvador. El llop viu a dalt. Cruïlla, 2003 978-84-661-0607-8   '),
		array('978-84-9766-149-2  ',1,'-Correig, Montserrat. Sopa de pedres. Eumo, 2006. 978-84-9766-149-2  '),
		array('978-84-661-0641-2',1,'-Dalmases, Antoni. Les cuques ballaruques. Cruïlla, 2003. 978-84-661-0641-2'),
		array('978-84-661-1279-6',1,'-Márquez, Eduard. L’Andreu i el mirall de les ganyotes. Cruïlla, 2004. 978-84-661-1279-6'),
		array('9788497661485',1,'-Ollé, M.Àngels. La ratona. Eumo, 2006. 9788497661485'),
		array('978-84-95730-55-8  ',1,'-Ramón, Elisa. No és fácil, petit esquirol! Kalandraka, 2003. 978-84-95730-55-8  '),
		array('978-84-263-5528-7',1,'-Comotto, Agustín. Los viajes del abuelo. Edelvives, 2004 . 978-84-263-5528-7'),
		array('978-84-667-4710-3',1,'-Guerrero, Andrés. Gato negro, gato blanco. Anaya, 2005. 978-84-667-4710-3'),
		array('978-84-207-0017-5  ',1,'-Nesquens, Daniel. Diecisiete cuentos y dos pingüinos. Anaya, 2006. 978-84-207-0017-5  '),
		array('978-84-667-7684-4   ',1,'- Nesquens, Daniel. Marcos Mostaza dos. Anaya. 978-84-667-7684-4   '),
		array('978-84-667-8475-7   ',1,'- Nesquens, Daniel. Marcos Mostaza tres. Anaya . 978-84-667-8475-7   '),
		array('978-84-667-5195-7  ',1,'-Rubio, Antonio. Tres cuentos de Urraca. Anaya, 2006. 978-84-667-5195-7  '),
		array('978-84-263-5268-2',1,'-Ventura, Antonio. Dos lobos blancos. Edelvives, 2004 . 978-84-263-5268-2'),
		array('978-84-7844-853-1',1,'-Enquist, Per Olov.  La montaña de las tres cuevas. Siruela, 2005 . 978-84-7844-853-1'),
		array('978-84-348-9645-1  ',1,'-Fernández Paz, Agustín. Mi nombre es Skywalker. Ediciones SM, 2003 978-84-348-9645-1  '),
		array('978-84-661-1018-1  ',1,'-Friel, Maeve. Lliçons de vol. Cruïlla, 2004 . 978-84-661-1018-1  '),
		array('9788426135179. ',1,'-Goethe, Johann Wolfgang Von. El mar en calma i viatge feliç. Joventut, 2006 . 9788426135179. '),
		array('978-84-936-1856-8',1,'-Lee, Suzy. Espejo. Barbara Fiore, DL 2008. 978-84-936-1856-8.'),
		);

		$idc = 255036;
		$dto = 15;
		$ids = 907;

		$this->_create_pedido($libros, $idc, $dto, $ids);
	}
}
/* End of file pedidos */
/* Location: ./system/application/controllers/tools/pedidos.php */
