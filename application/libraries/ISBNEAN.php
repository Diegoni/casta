<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * PREFIJOS ISBN válidos
 * @var array
 */
/**
 * Prefijo base cuando es ISBN10
 * @var string
 */
define('ISBN_PREFIX_BASE', '978');

/**
 * Funciones de ayuda para los ISBN/EANS
 *
 */
class ISBNEAN
{
	/**
	 * Grupos para el primer guión
	 *
	 * @var array
	 */
	private $grupos = array(80000, 95000, 99500, 99900);

	/**
	 * Posiciones de los guiones según primer guión
	 *
	 * @var array
	 */
	private $guiones = array(
	'_0'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(85000, 89999, 5),
	array(900000, 949999, 6),
	array(9500000, 9999999, 7)
	),
	'_1'=> array(
	array(0, 9, 2),
	array(100, 399, 3),
	array(4000, 5499, 4),
	array(55000, 86979, 5),
	array(869800, 998999, 6),
	array(9990000, 9999999, 7),
	array(9990000, 9999999, 7)
	),
	'_2'=> array(
	array(0, 19, 2),
	array(200, 349, 3),
	array(400, 699, 3),
	array(7000, 8399, 4),
	array(84000, 89999, 5),
	array(35000, 39999, 5),
	array(900000, 949999, 6),
	array(9500000, 9999999, 7),
	array(9500000, 9999999, 7)
	),
	'_3'=> array(
	array(0, 2, 2),
	array(4, 19, 2),
	array(200, 699, 3),
	array(30, 33, 3),
	array(340, 369, 4),
	array(7000, 8499, 4),
	array(85000, 89999, 5),
	array(3700, 3999, 5),
	array(900000, 949999, 6),
	array(9500000, 9999999, 7),
	array(9500000, 9999999, 7),
	),
	'_4'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(85000, 89999, 5),
	array(900000, 949999, 6),
	array(9500000, 9999999, 7),
	),
	'_5'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(9200, 9299, 4),
	array(9500, 9799, 4),
	array(98000, 98999, 5),
	array(93000, 94999, 5),
	array(85000, 89999, 5),
	array(91000, 91999, 5),
	array(900000, 909999, 6),
	array(9900000, 9999999, 7),
	),
	'_7'=> array(
	array(0, 9, 2),
	array(100, 499, 3),
	array(5000, 7999, 4),
	array(80000, 89999, 5),
	array(900000, 999999, 6),
	),
	'_80'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(85000, 89999, 5),
	array(900000, 999999, 6),
	),
	'_81'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(85000, 89999, 5),
	array(900000, 999999, 6),
	),
	'_82'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8999, 4),
	array(90000, 98999, 5),
	array(990000, 999999, 6),
	),
	'_83'=> array(
	array(0, 19, 2),
	array(200, 599, 3),
	array(7000, 8499, 4),
	array(85000, 89999, 5),
	array(60000, 69999, 5),
	array(900000, 999999, 6),
	),
	'_84'=> array(
	array(0, 14, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(9000, 9199, 4),
	array(9700, 9999, 4),
	array(15000, 19999, 5),
	array(95000, 96999, 5),
	array(92400, 92999, 5),
	array(85000, 89999, 5),
	array(930000, 949999, 6),
	array(920000, 923999, 6),
	),
	'_85'=> array(
	array(0, 19, 2),
	array(200, 599, 3),
	array(7000, 8499, 4),
	array(85000, 89999, 5),
	array(60000, 69999, 5),
	array(98000, 99999, 5),
	array(900000, 979999, 6),
	),
	'_86'=> array(
	array(0, 29, 2),
	array(300, 699, 3),
	array(7000, 7999, 4),
	array(80000, 89999, 5),
	array(900000, 999999, 6),
	),
	'_87'=> array(
	array(0, 29, 2),
	array(400, 649, 3),
	array(7000, 7999, 4),
	array(85000, 94999, 5),
	array(970000, 999999, 6),
	),
	'_88'=> array(
	array(0, 19, 2),
	array(200, 599, 3),
	array(6000, 8499, 4),
	array(85000, 89999, 5),
	array(900000, 999999, 6),
	),
	'_89'=> array(
	array(0, 24, 2),
	array(250, 549, 3),
	array(5500, 8499, 4),
	array(85000, 94999, 5),
	array(950000, 999999, 6),
	),
	'_90'=> array(
	array(0, 19, 2),
	array(200, 499, 3),
	array(5000, 6999, 4),
	array(8500, 8999, 4),
	array(70000, 79999, 5),
	array(800000, 849999, 6),
	array(900000, 909999, 6),
	array(940000, 949999, 6),
	),
	'_91'=> array(
	array(0, 1, 1),
	array(20, 49, 2),
	array(500, 649, 3),
	array(7000, 7999, 4),
	array(85000, 94999, 5),
	array(970000, 999999, 6),
	),
	'_92'=> array(
	array(0, 5, 1),
	array(60, 79, 2),
	array(800, 899, 3),
	array(9000, 9499, 4),
	array(95000, 98999, 5),
	array(990000, 999999, 6),
	),
	'_93'=> array(
	array(0, 99, 2),
	array(100, 499, 3),
	),
	'_94'=> array(
	array(0, 599, 3),
	array(6000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_600'=> array(
	array(0, 9, 2),
	array(100, 499, 3),
	array(5000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_601'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 7999, 4),
	array(80000, 89999, 5),
	array(85, 99, 2),
	),
	'_602'=> array(
	array(0, 19, 2),
	array(200, 799, 3),
	array(8000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_603'=> array(
	array(0, 4, 2),
	array(5, 49, 2),
	array(500, 799, 3),
	array(8000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_604'=> array(
	array(0, 4, 2),
	array(50, 89, 2),
	array(900, 979, 3),
	array(9800, 9999, 4),
	),
	'_605'=> array(
	array(0, 9, 2),
	array(100, 399, 3),
	array(4000, 5999, 4),
	array(60000, 89999, 5),
	array(90, 99, 2),
	),
	'_606'=> array(
	array(0, 0, 2),
	array(10, 49, 2),
	array(500, 799, 3),
	array(8000, 9199, 4),
	array(92000, 99999, 5),
	),
	'_607'=> array(
	array(0, 39, 2),
	array(400, 749, 3),
	array(7500, 9499, 4),
	array(95000, 99999, 5),
	),
	'_608'=> array(
	array(0, 0, 2),
	array(10, 19, 2),
	array(200, 449, 3),
	array(4500, 6499, 4),
	array(65000, 69999, 5),
	array(7, 9, 2),
	),
	'_609'=> array(
	array(0, 39, 2),
	array(400, 799, 3),
	array(8000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_612'=> array(
	array(0, 29, 2),
	array(300, 399, 3),
	array(4000, 4499, 4),
	array(45000, 49999, 5),
	array(50, 99, 2),
	),
	'_613'=> array(
	array(0, 9, 2),
	),
	'_614'=> array(
	array(0, 39, 2),
	array(400, 799, 3),
	array(8000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_615'=> array(
	array(0, 9, 2),
	array(100, 499, 3),
	array(5000, 7999, 4),
	array(80000, 89999, 5),
	),
	'_616'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_617'=> array(
	array(0, 49, 2),
	array(500, 699, 3),
	array(7000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_950'=> array(
	array(0, 49, 2),
	array(500, 899, 3),
	array(9000, 9899, 4),
	array(99000, 99999, 5),
	),
	'_951'=> array(
	array(0, 1, 1),
	array(20, 54, 2),
	array(550, 889, 3),
	array(8900, 9499, 4),
	array(95000, 99999, 5),
	),
	'_952'=> array(
	array(0, 19, 2),
	array(60, 65, 2),
	array(89, 94, 2),
	array(200, 499, 3),
	array(5000, 5999, 4),
	array(6600, 6699, 4),
	array(9500, 9899, 4),
	array(7000, 7999, 4),
	array(99000, 99999, 5),
	array(67000, 69999, 5),
	),
	'_953'=> array(
	array(0, 0, 1),
	array(10, 14, 2),
	array(150, 599, 3),
	array(6000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_954'=> array(
	array(0, 29, 2),
	array(300, 799, 3),
	array(8000, 8999, 4),
	array(9300, 9999, 4),
	array(90000, 92999, 5),
	),
	'_955'=> array(
	array(0, 0, 1),
	array(20, 54, 2),
	array(550, 799, 3),
	array(8000, 9499, 4),
	array(1000, 1999, 4),
	array(95000, 99999, 5),
	),
	'_956'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 9999, 4),
	),
	'_957'=> array(
	array(0, 2, 2),
	array(5, 19, 2),
	array(31, 43, 2),
	array(21, 27, 2),
	array(440, 819, 3),
	array(8200, 9699, 4),
	array(2000, 2099, 4),
	array(300, 499, 4),
	array(97000, 99999, 5),
	array(28000, 30999, 5),
	),
	'_958'=> array(
	array(0, 59, 2),
	array(600, 799, 3),
	array(8000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_959'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	),
	'_960'=> array(
	array(0, 19, 2),
	array(200, 659, 3),
	array(690, 699, 3),
	array(6600, 6899, 4),
	array(85000, 99999, 5),
	),
	'_961'=> array(
	array(0, 19, 2),
	array(200, 599, 3),
	array(6000, 8999, 4),
	array(90000, 94999, 5),
	),
	'_962'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(900, 999, 3),
	array(8700, 8999, 4),
	array(7000, 8499, 4),
	array(85000, 86999, 5),
	),
	'_963'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(9000, 9999, 4),
	array(85000, 89999, 5),
	),
	'_964'=> array(
	array(0, 29, 2),
	array(300, 549, 3),
	array(5500, 8999, 4),
	array(90000, 99999, 5),
	),
	'_965'=> array(
	array(0, 19, 2),
	array(200, 599, 3),
	array(7000, 7999, 4),
	array(90000, 99999, 5),
	),
	'_966'=> array(
	array(0, 29, 2),
	array(300, 699, 3),
	array(7000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_967'=> array(
	array(0, 5, 1),
	array(60, 89, 2),
	array(900, 989, 3),
	array(9900, 9989, 4),
	array(99900, 99999, 5),
	),
	'_968'=> array(
	array(1, 39, 2),
	array(400, 499, 3),
	array(800, 899, 3),
	array(5000, 7999, 4),
	),
	'_969'=> array(
	array(0, 1, 1),
	array(20, 39, 2),
	array(400, 799, 3),
	array(8000, 9999, 4),
	),
	'_970'=> array(
	array(1, 59, 2),
	array(600, 899, 3),
	array(9000, 9099, 4),
	array(9700, 9999, 4),
	array(91000, 96999, 5),
	),
	'_971'=> array(
	array(6, 9, 2),
	array(10, 49, 2),
	array(2, 2, 2),
	array(0, 19, 3),
	array(500, 849, 3),
	array(8500, 9099, 4),
	array(300, 599, 4),
	array(91000, 99999, 5),
	),
	'_972'=> array(
	array(0, 1, 1),
	array(20, 54, 2),
	array(550, 799, 3),
	array(8000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_973'=> array(
	array(0, 1, 1),
	array(20, 54, 2),
	array(550, 769, 3),
	array(7700, 8499, 4),
	array(9000, 9499, 4),
	array(95000, 99999, 5),
	array(85000, 89999, 5),
	),
	'_974'=> array(
	array(0, 19, 2),
	array(200, 699, 3),
	array(7000, 8499, 4),
	array(9500, 9999, 4),
	array(85000, 89999, 5),
	array(90000, 94999, 5),
	),
	'_975'=> array(
	array(0, 24, 2),
	array(250, 599, 3),
	array(6000, 9199, 4),
	array(92000, 98999, 5),
	),
	'_976'=> array(
	array(0, 3, 1),
	array(40, 59, 2),
	array(600, 799, 3),
	array(8000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_977'=> array(
	array(0, 19, 2),
	array(200, 499, 3),
	array(700, 999, 3),
	array(5000, 6999, 4),
	),
	'_978'=> array(
	array(0, 199, 3),
	array(900, 999, 3),
	array(8000, 8999, 4),
	array(2000, 2999, 4),
	array(30000, 79999, 5),
	),
	'_979'=> array(
	array(0, 0, 1),
	array(20, 29, 2),
	array(400, 799, 3),
	array(8000, 9499, 4),
	array(3000, 3999, 4),
	array(95000, 99999, 5),
	),
	'_980'=> array(
	array(0, 19, 2),
	array(200, 599, 3),
	array(6000, 9999, 4),
	),
	'_981'=> array(
	array(0, 19, 2),
	array(200, 299, 3),
	array(3000, 9999, 4),
	),
	'_982'=> array(
	array(0, 9, 2),
	array(70, 89, 2),
	array(100, 699, 3),
	array(9000, 9999, 4),
	),
	'_983'=> array(
	array(0, 1, 2),
	array(50, 79, 2),
	array(800, 899, 3),
	array(20, 199, 3),
	array(2000, 3999, 4),
	array(9000, 9899, 4),
	array(99000, 99999, 5),
	array(40000, 49999, 5),
	),
	'_984'=> array(
	array(0, 39, 2),
	array(400, 799, 3),
	array(8000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_985'=> array(
	array(0, 39, 2),
	array(400, 599, 3),
	array(6000, 8999, 4),
	array(90000, 99999, 5),
	),
	'_986'=> array(
	array(0, 11, 2),
	array(120, 559, 3),
	array(5600, 7999, 4),
	array(80000, 99999, 5),
	),
	'_987'=> array(
	array(30, 49, 2),
	array(0, 9, 2),
	array(500, 899, 3),
	array(9000, 9499, 4),
	array(1000, 1999, 4),
	array(20000, 29999, 5),
	array(95000, 99999, 5),
	),
	'_988'=> array(
	array(0, 19, 2),
	array(200, 799, 3),
	array(8000, 9699, 4),
	array(97000, 99999, 5),
	),
	'_989'=> array(
	array(0, 1, 1),
	array(20, 54, 2),
	array(550, 799, 3),
	array(8000, 9499, 4),
	array(95000, 99999, 5),
	),
	'_9945'=> array(
	array(0, 39, 2),
	array(400, 849, 3),
	array(8500, 9999, 4),
	),
	'_9946'=> array(
	array(0, 1, 1),
	array(20, 39, 2),
	array(400, 899, 3),
	array(9000, 9999, 4),
	),
	'_9947'=> array(
	array(0, 1, 1),
	array(20, 79, 2),
	array(800, 999, 3),
	),
	'_9948'=> array(
	array(0, 39, 2),
	array(400, 849, 3),
	array(8500, 9999, 4),
	),
	'_9949'=> array(
	array(0, 0, 1),
	array(10, 39, 2),
	array(400, 899, 3),
	array(9000, 9999, 4),
	),
	'_9951'=> array(
	array(0, 39, 2),
	array(400, 849, 3),
	array(8500, 9999, 4),
	),
	'_9952'=> array(
	array(0, 1, 1),
	array(20, 39, 2),
	array(400, 799, 3),
	array(8000, 9999, 4),
	),
	'_9953'=> array(
	array(0, 0, 1),
	array(10, 39, 2),
	array(60, 89, 2),
	array(400, 599, 3),
	array(9000, 9999, 4),
	),
	'_9954'=> array(
	array(0, 1, 1),
	array(20, 39, 2),
	array(400, 799, 3),
	array(8000, 9999, 4),
	),
	'_9955'=> array(
	array(0, 39, 2),
	array(400, 929, 3),
	array(9300, 9999, 4),
	),
	'_9956'=> array(
	array(0, 0, 1),
	array(10, 39, 2),
	array(400, 899, 3),
	array(9000, 9999, 4),
	),
	'_9957'=> array(
	array(0, 39, 2),
	array(400, 849, 3),
	array(8500, 9999, 4),
	),
	'_9958'=> array(
	array(0, 0, 1),
	array(10, 49, 2),
	array(500, 899, 3),
	array(9000, 9999, 4),
	),
	'_9959'=> array(
	array(0, 1, 1),
	array(20, 79, 2),
	array(800, 949, 3),
	array(9500, 9999, 4),
	),
	'_9960'=> array(
	array(0, 59, 2),
	array(600, 899, 3),
	array(9000, 9999, 4),
	),
	'_9961'=> array(
	array(0, 2, 1),
	array(30, 69, 2),
	array(700, 949, 3),
	array(9500, 9999, 4),
	),
	'_9962'=> array(
	array(0, 54, 2),
	array(56, 59, 2),
	array(600, 849, 3),
	array(8500, 9999, 4),
	array(5500, 5599, 4),
	),
	'_9963'=> array(
	array(0, 2, 1),
	array(30, 54, 2),
	array(550, 749, 3),
	array(7500, 9999, 4),
	),
	'_9964'=> array(
	array(0, 6, 1),
	array(70, 94, 2),
	array(950, 999, 3),
	),
	'_9965'=> array(
	array(0, 39, 2),
	array(400, 899, 3),
	array(9000, 9999, 4),
	),
	'_9966'=> array(
	array(0, 69, 2),
	array(750, 959, 3),
	array(9600, 9999, 4),
	array(7000, 7499, 4),
	),
	'_9967'=> array(
	array(0, 39, 2),
	array(400, 899, 3),
	array(9000, 9999, 4),
	),
	'_9968'=> array(
	array(0, 49, 2),
	array(500, 939, 3),
	array(9400, 9999, 4),
	),
	'_9970'=> array(
	array(0, 39, 2),
	array(400, 899, 3),
	array(9000, 9999, 4),
	),
	'_9971'=> array(
	array(0, 5, 1),
	array(60, 89, 2),
	array(900, 989, 3),
	array(9900, 9999, 4),
	),
	'_9972'=> array(
	array(1, 1, 1),
	array(30, 59, 2),
	array(0, 9, 2),
	array(600, 899, 3),
	array(200, 249, 3),
	array(2500, 2999, 4),
	array(9000, 9999, 4),
	),
	'_9973'=> array(
	array(0, 0, 1),
	array(10, 69, 2),
	array(700, 969, 3),
	array(9700, 9999, 4),
	),
	'_9974'=> array(
	array(0, 2, 1),
	array(30, 54, 2),
	array(95, 99, 2),
	array(550, 749, 3),
	array(7500, 9499, 4),
	),
	'_9975'=> array(
	array(0, 4, 1),
	array(50, 89, 2),
	array(900, 949, 3),
	array(9500, 9999, 4),
	),
	'_9976'=> array(
	array(0, 5, 1),
	array(60, 89, 2),
	array(900, 989, 3),
	array(9990, 9999, 4),
	),
	'_9977'=> array(
	array(0, 89, 2),
	array(900, 989, 3),
	array(9900, 9999, 4),
	),
	'_9978'=> array(
	array(0, 29, 2),
	array(40, 94, 2),
	array(950, 989, 3),
	array(300, 399, 3),
	array(9900, 9999, 4),
	),
	'_9979'=> array(
	array(0, 4, 1),
	array(50, 75, 2),
	array(760, 899, 3),
	array(9000, 9999, 4),
	),
	'_9980'=> array(
	array(0, 3, 1),
	array(40, 89, 2),
	array(900, 989, 3),
	array(9900, 9999, 4),
	),
	'_9981'=> array(
	array(0, 9, 2),
	array(20, 79, 2),
	array(800, 949, 3),
	array(100, 159, 3),
	array(1600, 1999, 4),
	array(9500, 9999, 4),
	),
	'_9982'=> array(
	array(0, 79, 2),
	array(800, 889, 3),
	array(9900, 9999, 4),
	),
	'_9983'=> array(
	array(80, 94, 2),
	array(950, 989, 3),
	array(9900, 9999, 4),
	),
	'_9984'=> array(
	array(0, 49, 2),
	array(500, 899, 3),
	array(9000, 9999, 4),
	),
	'_9985'=> array(
	array(0, 4, 1),
	array(50, 79, 2),
	array(800, 899, 3),
	array(9000, 9999, 4),
	),
	'_9986'=> array(
	array(0, 39, 2),
	array(97, 99, 2),
	array(940, 969, 3),
	array(400, 899, 3),
	array(9000, 9399, 4),
	),
	'_9987'=> array(
	array(0, 39, 2),
	array(400, 879, 3),
	array(8800, 9999, 4),
	),
	'_9988'=> array(
	array(0, 2, 1),
	array(30, 54, 2),
	array(550, 749, 3),
	array(7500, 9999, 4),
	),
	'_9989'=> array(
	array(0, 0, 1),
	array(30, 59, 2),
	array(600, 949, 3),
	array(100, 199, 3),
	array(2000, 2999, 4),
	array(9500, 9999, 4),
	),
	'_99901'=> array(
	array(0, 49, 2),
	array(80, 99, 2),
	array(500, 799, 3),
	),
	'_99903'=> array(
	array(0, 1, 1),
	array(20, 89, 2),
	array(900, 999, 3),
	),
	'_99904'=> array(
	array(0, 5, 1),
	array(60, 89, 2),
	array(900, 999, 3),
	),
	'_99905'=> array(
	array(0, 3, 1),
	array(40, 79, 2),
	array(800, 999, 3),
	),
	'_99906'=> array(
	array(0, 2, 1),
	array(30, 59, 2),
	array(600, 999, 3),
	),
	'_99908'=> array(
	array(0, 0, 1),
	array(10, 89, 2),
	array(900, 999, 3),
	),
	'_99909'=> array(
	array(0, 3, 1),
	array(40, 94, 2),
	array(950, 999, 3),
	),
	'_99910'=> array(
	array(0, 2, 1),
	array(30, 89, 2),
	array(900, 999, 3),
	),
	'_99911'=> array(
	array(0, 59, 2),
	array(600, 999, 3),
	),
	'_99912'=> array(
	array(0, 4, 1),
	array(60, 89, 2),
	array(900, 999, 3),
	array(500, 599, 3),
	),
	'_99913'=> array(
	array(0, 2, 1),
	array(30, 35, 2),
	array(600, 604, 3),
	),
	'_99914'=> array(
	array(0, 4, 1),
	array(50, 89, 2),
	array(900, 949, 3),
	),
	'_99915'=> array(
	array(0, 4, 1),
	array(50, 79, 2),
	array(800, 999, 3),
	),
	'_99916'=> array(
	array(0, 2, 1),
	array(30, 69, 2),
	array(700, 999, 3),
	),
	'_99917'=> array(
	array(0, 2, 1),
	array(30, 89, 2),
	array(900, 999, 3),
	),
	'_99918'=> array(
	array(0, 3, 1),
	array(40, 89, 2),
	array(900, 999, 3),
	),
	'_99919'=> array(
	array(0, 2, 1),
	array(40, 69, 2),
	array(900, 999, 3),
	),
	'_99920'=> array(
	array(0, 4, 1),
	array(50, 89, 2),
	array(900, 999, 3),
	),
	'_99921'=> array(
	array(0, 1, 1),
	array(8, 8, 1),
	array(90, 99, 2),
	array(20, 69, 2),
	array(700, 799, 3),
	),
	'_99922'=> array(
	array(0, 3, 1),
	array(40, 69, 2),
	array(700, 999, 3),
	),
	'_99923'=> array(
	array(0, 1, 1),
	array(20, 79, 2),
	array(800, 999, 3),
	),
	'_99924'=> array(
	array(0, 2, 1),
	array(30, 79, 2),
	array(800, 900, 3),
	),
	'_99925'=> array(
	array(0, 3, 1),
	array(40, 79, 2),
	array(800, 999, 3),
	),
	'_99926'=> array(
	array(0, 0, 1),
	array(10, 59, 2),
	array(600, 999, 3),
	),
	'_99927'=> array(
	array(0, 2, 1),
	array(30, 59, 2),
	array(600, 999, 3),
	),
	'_99928'=> array(
	array(0, 0, 1),
	array(10, 79, 2),
	array(800, 999, 3),
	),
	'_99930'=> array(
	array(0, 4, 1),
	array(50, 79, 2),
	array(800, 999, 3),
	),
	'_99931'=> array(
	array(0, 4, 1),
	array(50, 79, 2),
	array(800, 999, 3),
	),
	'_99932'=> array(
	array(0, 0, 1),
	array(7, 7, 1),
	array(80, 99, 2),
	array(10, 59, 2),
	array(600, 699, 3),
	),
	'_99933'=> array(
	array(0, 2, 1),
	array(30, 59, 2),
	array(600, 999, 3),
	),
	'_99934'=> array(
	array(0, 1, 1),
	array(20, 79, 2),
	array(800, 999, 3),
	),
	'_99935'=> array(
	array(0, 2, 1),
	array(8, 8, 1),
	array(90, 99, 2),
	array(30, 59, 2),
	array(600, 799, 3),
	),
	'_99936'=> array(
	array(0, 0, 1),
	array(10, 59, 2),
	array(600, 999, 3),
	),
	'_99937'=> array(
	array(0, 1, 1),
	array(20, 59, 2),
	array(600, 999, 3),
	),
	'_99938'=> array(
	array(0, 2, 1),
	array(30, 59, 2),
	array(600, 999, 3),
	),
	'_99939'=> array(
	array(0, 5, 1),
	array(60, 89, 2),
	array(900, 999, 3),
	),
	'_99940'=> array(
	array(0, 0, 1),
	array(10, 69, 2),
	array(700, 999, 3),
	),
	'_99941'=> array(
	array(0, 2, 1),
	array(30, 89, 2),
	array(900, 999, 3),
	),
	'_99942'=> array(
	array(0, 4, 1),
	array(50, 79, 2),
	array(800, 999, 3),
	),
	'_99943'=> array(
	array(0, 2, 1),
	array(30, 59, 2),
	array(600, 999, 3),
	),
	'_99944'=> array(
	array(0, 4, 1),
	array(50, 79, 2),
	array(800, 999, 3),
	),
	'_99945'=> array(
	array(0, 5, 1),
	array(60, 89, 2),
	array(900, 999, 3),
	),
	'_99946'=> array(
	array(0, 2, 1),
	array(30, 59, 2),
	array(600, 999, 3),
	),
	'_99948'=> array(
	array(0, 4, 1),
	array(50, 79, 2),
	array(800, 999, 3),
	array(800, 999, 3),
	)
	);

	/**
	 * Constructor
	 * @return ISBNEAN
	 */
	function __construct()
	{
		log_message('debug', 'ISBNEAN Class Initialised');
	}

	/**
	 * Calcula el código de control de un ISBN 10
	 *
	 * @param string $isbn Código ISBN sin CC
	 * @return string (0-9, X)
	 */
	private function _chksum10($isbn)
	{
		$i = 10;
		$dig = 0;
		while ($i > 1)
		{
			$dig += (int)(substr($isbn, 10 - $i, 1)) * $i;
			$i--;
		}
		$dig = 11 - ($dig % 11);
		if ($dig == 11) $dig = 0;
		if ($dig == 10) $dig = "X";
		return $dig;
	}

	/**
	 * Calcula el código de control de un ISBN 13
	 *
	 * @param string $isbn Código ISBN sin CC
	 * @return string (0-9)
	 */
	private function _chksum13($isbn)
	{
		$isbn = trim($isbn);

		$par = 0; $impar = 0;
		for ($i = 0; $i < strlen($isbn); $i++)
		{
			$dig = (int)substr($isbn, $i, 1);
			if ((($i+1) % 2) == 1)
			{
				$impar += $dig;
			}
			else
			{
					
				$par += $dig;
			}
		}
		$cc = 10 - (($par * 3 + $impar) % 10);
		if ($cc == 10) $cc = 0;
		return $cc;
	}

	/**
	 * Obtiene el grupo para la separación de guión
	 *
	 * @param string $isbn Código ISBN (sin 978, ni 977)
	 * @param string $grp Devuelve el grupo de guion
	 * @param string $outer Devuelve la parte restante del grupo
	 */
	private function _isbn_grupo($isbn, &$grp, &$outer)
	{
		#echo '<pre>';
		//print_r($this->guiones['_6']);
		#print_r($this->guiones);
		#echo '</pre>'; die();
		$i = 1;
		do {
			$grp = substr($isbn, 0, $i);
			$outer = substr($isbn, $i, 9 - $i);
			#echo $grp . '<pre>';
			#var_dump($this->guiones['_'.$grp]);
			#echo '</pre>';
			if (isset($this->guiones['_'.$grp]))
			{
				$outer = substr($isbn, $i, 9 - $i);
				break;
			}
			$i++;
		} while ($i <= 5);
		#var_dump($grp);
		#var_dump($outer);
		#$grp = substr($isbn, 0, 5);
		//echo "GRP: $grp\n";
		/*foreach($this->grupos as $grupo)
		 {
			if ($grp < $grupo)
			{
			$grp = substr($isbn, 0, $i);
			$outer = substr($isbn, $i, 9 - $i);
			var_dump($grp);
			var_dump($outer);
			return;
			}
			$i++;
			}
			$grp = substr($isbn, 0, $i);
			$outer = substr($isbn, $i, 9 - $i);*/
	}

	/**
	 * Añade los guiones a un ISBN
	 *
	 * @param string $isbn ISBN sin 977/978
	 * @return string
	 */
	private function _isbn_guiones($isbn)
	{
		$grp = null;
		$outer = null;
		$this->_isbn_grupo($isbn, $grp, $outer);
		#var_dump($grp); var_dump($outer); die();
		if (!isset($this->guiones['_'.$grp])) return FALSE;
		$guiones = $this->guiones['_'.$grp];
		foreach($guiones as $guion)
		{
			$sel = (int)substr($outer, 0, $guion[2]);
			if (($sel >= $guion[0])&&($sel <= $guion[1]))
			{
				return $grp .'-' . substr($outer, 0, $guion[2]) . '-'.substr($outer, $guion[2]);
			}
		}
		return $isbn;
	}

	/**
	 * Comprueba si es un prefijo válido
	 * @param string $code Prefijo
	 * @return bool
	 */
	private function _is_prefix($code)
	{
		return in_array($code, array('978', '977'));
	}

	/**
	 * Convierte la entrada a EAN, si es un código de libro (978/977/ISBN10)
	 *
	 * @param string $code Código
	 * @return string
	 */
	function to_ean($code)
	{
		$code = $this->clean_code($code);
		#var_dump($code);
		$len = strlen($code);
		if (( $len != 13) && ($len != 10) && ( $len != 12) && ($len != 9))
		//if (!$this->is_isbn($code))
		{
			//No es un EAN/ISBN
			return null;
		}
		$base = ISBN_PREFIX_BASE;
		if (($len == 13)||($len == 12))
		{
			$base = substr($code, 0, 3);

			/*if (!$this->_is_prefix($base))
			{
				// no es un EAN/ISBN de libro
				return null;
			}*/
			//Quita los 978/977
			$code = substr($code, 3);
			$len = $len - 3;
		}
		if (preg_match("/(?=[0-9xX]{{$len}}$)/", $code)!= 1) return null;
		//Quita el código de control
		$code = substr($code, 0, 9);
		//Calcula en CC
		$ean = $base.$code;
		return $ean.$this->_chksum13($ean);
	}

	/**
	 * Convierte la entrada a un ISBN con guiones
	 *
	 * @param string $code ISBN10,ISBN13,EAN con o sin CC
	 * @param bool $isbn10 true: devuelve el ISBN13 y el ISBN10 en un array, false: solo el ISBN13 directo
	 * @return mixed
	 */
	function to_isbn($code, $isbn10 = FALSE)
	{
		$code = $this->clean_code($code);
		//$code = str_replace('-','', $code);
		$len = strlen($code);

		if (( $len != 13) && ($len != 10) && ( $len != 12) && ($len != 9))
		{
			//No es un EAN/ISBN
			return null;
		}
			
		if (preg_match("/(?=[0-9xX]{{$len}}$)/", $code)!= 1) return null;

		$base = ISBN_PREFIX_BASE;
		if (($len == 13)||($len == 12))
		{
			$base = substr($code, 0, 3);

			if (!$this->_is_prefix($base))
			{
				// no es un EAN/ISBN de libro
				return null;
			}
			//Quita los 978/977
			$code = substr($code, 3);
		}
		//Quita el código de control
		$code = substr($code, 0, 9);
		$isbn = $this->_isbn_guiones($code);
		if ($isbn === FALSE) return null;
		if ($isbn10)
		{
			return array(
				'isbn10' => $isbn.'-'.$this->_chksum10($code),
				'isbn13' => $base.'-'.$isbn.'-'.$this->_chksum13($base.$code)
			);
		}
		else
		{
			return $base.'-'.$isbn.'-'.$this->_chksum13($base.$code);
		}
	}

	/**
	 * Limpia un ISBN
	 * @param string $code Código ISBN
	 * @return string
	 */
	function clean_code($code)
	{
		return trim(preg_replace('/[-\s\.]/', '', $code));
	}

	/**
	 * Comprueba si un código es un ISBN
	 * @param string $code Código a comprobar
	 * @param bool $isbn10 Es un ISBN10
	 * @param bool $strict Comprueba que esté bien codificado
	 * @return bool
	 */
	function is_isbn($code, $isbn10 = FALSE, $strict = FALSE)
	{
		$code = $this->clean_code($code);
		if (((strlen($code) != 13) && !$isbn10) || ((strlen($code) != 10) && $isbn10)) return FALSE;
		$n = $isbn10?10:13;
		if (preg_match("/(?=[0-9xX]{{$n}}$)/", $code)!= 1) return FALSE;
		if (!$isbn10)
		{
			$base = substr($code, 0, 3);
			if (!$this->_is_prefix($base)) return FALSE;
		}

		// Comprueba que sea un ISBN bien formado
		if ($strict)
		{
			$code2 = $this->to_isbn($code, $isbn10);
			$code2 = str_replace('-', '', $code2);
			if ($code != $code2) return FALSE;
		}
		return TRUE;
	}

	/**
	 * Comprueba si un código es un EAN
	 * @param string $code Código a comprobar
	 * @return bool
	 */
	function is_ean($code)
	{
		$code = trim($code);
		$code2 = preg_replace('/[^\d]/', '', $code);
		if ((strlen($code) != 13) || ($code != $code2)) return FALSE;
		return TRUE;
	}

	/**
	 * Devuelve las partes de un ISBN: prefix, group, publisher, title, publisher_id
	 * http://www.isbn.org/standards/home/isbn/international/html/usm4.htm
	 * ISBN13: 978-0-88033-371-9
	 * ["prefix"] => string(3) "978"
	 * ["group"] => string(1) "0"
	 * ["publisher"] => string(5) "88033"
	 * ["title"] => string(3) "371"
	 * ["check"] => string(1) "9"
	 * ["publisher_id"] => string(11) "978-0-88033"
	 * @param string $isbn ISBN13
	 * @return array
	 */
	function isbnparts($isbn)
	{
		$parts = preg_split('/-/', $isbn);
		if (count($parts)!=5) return null;
		$data['prefix'] = $parts[0];
		$data['group'] 	= $parts[1];
		$data['publisher'] 	= $parts[2];
		$data['title'] 	= $parts[3];
		$data['check'] 	= $parts[4];
		$data['publisher_id'] = "{$parts[0]}-{$parts[1]}-{$parts[2]}";
		return $data;
	}

	/**
	 * Indica si un código es un poisible identificador de editorial
	 * @param string $isbn Código
	 * @return bool
	 */
	function is_publisher($isbn)
	{
		$parts = preg_split('/-/', $isbn);
		if (count($parts) != 3) return FALSE;
		if (!$this->_is_prefix($parts[0])) return FALSE;
		foreach($parts as $p)
		{
			$code2 = preg_replace('/[^\d]/', '', $p);
			if ($p != $code2) return FALSE;
		}
		return TRUE;
	}
}
/* End of file isnean.php */
/* Location: ./system/application/libraries/isbnean.php */