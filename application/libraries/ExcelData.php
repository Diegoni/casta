<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
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
 * Conversor de EXCEL
 * @author alexl
 *
 */
class ExcelData
{

	/**
	 * Constructor
	 * @return ExcelData
	 */
	function __construct()
	{
		//require_once DIR_CONTRIB_PATH . 'Spreadsheet' . DS . 'Excel' . DS .
		// 'Writer.php';
		log_message('debug', 'ExcelData Class Initialised');
	}

	/**
	 * Crea una hoja de EXCEL usando las librerías PHPExcel con los campos del modelo indicado
	 * @param MY_Model $model Modelo de datos al que pertenecen los datos
	 * @param array $data Datos a exportar
	 * @param string $title Título de la hoja
	 * @param string $filename Nombre del fichero. Si no se indica se asigna uno
	 * @return Nombre del fichero 
	 */
	function data2($model, $data, $title, $filename = null)
	{
		require_once DIR_CONTRIB_PATH . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php';
		$obj = get_instance();

		set_time_limit(0);

		// Creating a workbook
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$title = str_replace('*', '', $title);
		$objPHPExcel->getActiveSheet()->setTitle($title);

		// Cabeceras
		$row = 1;
		$column = 1;
		foreach ($model as $k => $m)
		{
			//Cabecara
			$header = $obj->lang->line(isset($m[DATA_MODEL_DESCRIPTION]) ? $m[DATA_MODEL_DESCRIPTION] : $m[DATA_MODEL_FIELD]);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $header);
			++$column;
		}
		++$row;

		// Datos
		foreach ($data as $reg)
		{
			$column = 1;
			foreach ($reg as $field)
			{
				if (isset($field))
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $field);
				++$column;
			}
			++$row;
		}

		//Guarda
		if (!isset($filename))
			$filename = time() . '.xls';
		$obj->load->library('HtmlFile');
		$file = $obj->htmlfile->pathfile($filename);
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->setPreCalculateFormulas(false);
		$objWriter->save($file);

		return $filename;
	}

	/**
	 * Crea un fichero EXCEL
	 * @return array 'workbook' => Objeto EXCEL, 'filename' => Nombre del archivo
	 */
	function create()
	{
		require_once DIR_CONTRIB_PATH . 'Spreadsheet' . DS . 'Excel' . DS . 'Writer.php';
		$filename = time() . '.xls';
		$obj = get_instance();
		$obj->load->library('HtmlFile');
		$file = $obj->htmlfile->pathfile($filename);
		$workbook = new Spreadsheet_Excel_Writer($file);

		return array(
				'workbook' => $workbook,
				'filename' => $filename
		);
	}
	
	/**
	 * Añade una hoja a un fochero EXCEL
	 * @param array $wb 'workbook' => Objeto EXCEL, 'filename' => Nombre del archivo
	 * @param array $data Datos a exportar
	 * @param string $title Título de la hoja
	 * @return bool TRUE: Sin errores 
	 */
	function add($wb, $data, $title)
	{
		$obj = get_instance();

		// Creating a workbook
		$workbook = $wb['workbook'];
		$title = str_replace('*', '', $title);
		$worksheet = $workbook->addWorksheet($title);
		if (count($data) == 0)
			return FALSE;
		$row = 1;
		$columns = array();
		$column = 0;
		foreach ($data as $reg)
		{
			foreach ($reg as $k => $field)
			{
				if (isset($columns[$k]))
				{
					$column = $columns[$k];
				}
				else
				{
					$columns[$k] = count($columns) + 1;
					$column = count($columns);
					$worksheet->write(0, $column - 1, utf8_decode($obj->lang->line($k)));
				}
				if (isset($field))
					$worksheet->write($row, $column - 1, (is_string($field) ? utf8_decode($field) : $field));
				++$column;
			}
			++$row;
		}
		return TRUE;
	}

	/**
	 * Cierra el workbook
	 * @param array $wb 'workbook' => Objeto EXCEL, 'filename' => Nombre del archivo
	 * @return string
	 */
	function close($wb)
	{
		$wb['workbook']->close();
	}

	/**
	 * Devuelve el nombre del fichero abierto
	 * @param array $wb 'workbook' => Objeto EXCEL, 'filename' => Nombre del archivo
	 * @return string
	 */
	function get_filename($wb)
	{
		return $wb['filename'];
	}

	/**
	 * Lee un fichero EXCEL
	 * @param string $file Fichero a leer
	 * @return array: cells -> Datos, numCols -> No Columnas, numRows -> Num. Filas
	 */
	function read($file)
	{
		require_once DIR_CONTRIB_PATH . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		//$objReader = new PHPExcel_Reader_Excel2003();
		$objReader = PHPExcel_IOFactory::createReaderForFile($file);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($file);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$data = array();
		$columns = 0;
		foreach ($objWorksheet->getRowIterator() as $row)
		{
			$column = 0;
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			// This loops all cells,
			// even if it is not set.
			// By default, only cells
			// that are set will be
			// iterated.
			$column = array();
			foreach ($cellIterator as $cell)
			{
				$column[] = $cell->getValue();
			}
			$columns = max($columns, count($column));
			$data[] = $column;
		}
		$final = array(
				'cells' => $data,
				'numCols' => $columns,
				'numRows' => count($data)
		);
		$filter = $this->getFilter($final);
		return array_merge($final, $filter);
	}

	/*function getCellNumbers($alphaCell)
	{
		$numCol = 0;
		$letters = str_split(strtoupper(strrev($alphaCol)), 1);

		foreach ($letters as $level => $letter)
		{
			// ($level is numeric key)
			$letterNum = ord($letter) - ord('A') + 1;
			$numCol += $letterNum * pow(26, $level);
		}

		$numCol -= 1;
		$numRow -= 1;

		if (is_array($data) and isset($data[$numRow][$alphaCol]))
		{
			return $data[$numRow][$numCol];
		}

		return null;
		// not found
	}*/

	/**
	 * Transformar un filtro de columnas EXCEL a los valores numéricos
	 * @param string $filter <COL><ROW>:<COL><ROW>
	 * @return array from, to con los valores en texto y numéricos del filtro
	 */
	function getFilter($info, $filter = null, $sheet = 0)
	{
		$f = array();
		if (isset($filter))
		{
			$pattern = '/([a-zA-Z]*)([0-9]*)/';
			$t = preg_match_all($pattern, $filter, $r, PREG_SET_ORDER);
			foreach ($r as $c)
			{
				if ($c[0] != '')
					$f[] = $c;
				if (count($f) == 2)
					break;
			}
		}
		if (count($f) == 0)
		{
			$data = array(
					'from' => array(
							'A',
							'1',
							1,
							1
					),
					'to' => array(
							$this->columnLetter($info['numCols']),
							$info['numRows'],
							$info['numCols'],
							$info['numRows']
					)
			);
		}
		else
		if (count($f) == 1)
		{
			$data = array(
					'from' => array(
							'A',
							'1',
							1,
							1
					),
					'to' => array(
							$f[0][1],
							$f[0][2],
							$this->columnNumber($f[0][1]),
							$f[0][2]
					)
			);
		}
		else
		{
			$data = array(
					'from' => array(
							$f[0][1],
							$f[0][2],
							$this->columnNumber($f[0][1]),
							$f[0][2]
					),
					'to' => array(
							$f[1][1],
							$f[1][2],
							$this->columnNumber($f[1][1]),
							$f[1][2]
					)
			);
		}
		$data['filter'] = $data['from'][0] . $data['from'][1] . ':' . $data['to'][0] . $data['to'][1];

		return $data;
	}

	/**
	 * Transformar las letras de las columnas a número
	 * @param string $col Columna
	 * @return int
	 */
	function columnNumber($col)
	{
		$col = str_pad($col, 2, '0', STR_PAD_LEFT);
		$i = ($col{0} == '0') ? 0 : (ord($col{0}) - 64) * 26;
		$i += ord($col{1}) - 64;

		return $i - 1;
	}

	/**
	 * http://75.134.27.61:8101/wordpress/example-code-to-convert-a-number-to-excel-column-letter/
	 * @param int $c Columna
	 * @return string Columna en letra
	 */
	function columnLetter($c)
	{

		$c = intval($c);
		if ($c <= 0)
			return '';

		$letter = '';
		while ($c != 0)
		{
			$p = ($c - 1) % 26;
			$c = intval(($c - $p) / 26);
			$letter = chr(65 + $p) . $letter;
		}

		return $letter;
	}

	/**
	 * Genera un fichero EXCEL con el contenido de una tabla
	 * http://phpexcel.codeplex.com/discussions/275807
	 * 
	 * @link http://phpexcel.codeplex.com/discussions/275807
	 * @param  string $htmltable     HTML con la tabla
	 * @param  string $file     Fichero de salida
	 * @param  string $title    Título
	 * @param  string $user     Nombre del usuario
	 * @param  string $company  Nombre de la compañía
	 * @param  string $tablevar Nombre de las hojas
	 * @param  string $format  Formato de salida (Excel2007,)
	 * @param  string $limit    Número de páginas máximo
	 * @param  bool $debug    Modo DEBUG
	 * @return mixed TRUE: ok, string: error
	 */
	function table2excel($htmltable, $file, $title, $user, $company, $tablevar, $format = 'Excel2007', $limit = 12, $debug = FALSE)
	{
	  set_time_limit(0);
	  #echo 'SIP'; die();
	  if ($debug)
	  {
	    $handle = fopen(DIR_LOG_PATH . "exportdebug_log.txt", "w");
	    fwrite($handle, "\nDebugging On...");
	  }

	  #$htmltable = file_get_contents($file);
	  if(strlen($htmltable) == strlen(strip_tags($htmltable)) ) 
	  {     // anything left after we strip HTML?
	    return "Invalid HTML Table after Stripping Tags, nothing to Export.";
	  }
	  if($debug) 
	  {
	    fwrite($handle, "\n-------------------------------------------");
	    fwrite($handle, "\nHTML before prep: \n".$htmltable);
	    fwrite($handle, "\n-------------------------------------------");
	  }

	  $htmltable = strip_tags($htmltable, "<table><tr><th><thead><tbody><tfoot><td><br><b><span><input>");
	  $htmltable = str_replace("<br />", "\n", $htmltable);
	  $htmltable = str_replace("<br/>", "\n", $htmltable);
	  $htmltable = str_replace("<br>", "\n", $htmltable);
	  $htmltable = str_replace("&nbsp;", " ", $htmltable);
	  $htmltable = str_replace("\n\n", "\n", $htmltable);
	  if($debug) 
	  {
	    fwrite($handle, "\n-------------------------------------------");
	    fwrite($handle, "\nHTML after prep: \n".$htmltable);
	    fwrite($handle, "\n-------------------------------------------");
	  }
	  //
	  //  Create Document Object Model from HTML table contents
	  //
	  $dom = new domDocument;
	  $dom->loadHTML($htmltable);
	  if(!$dom) 
	  {
	    return "Invalid HTML DOM, nothing to Export";
	  }
	  $dom->preserveWhiteSpace = false;             // remove redundant whitespace
	  $tables = $dom->getElementsByTagName('table');
	  if(!is_object($tables)) 
	  {
	    return "Invalid HTML Table DOM, nothing to Export";
	  }
	  if($debug) 
	  {
	    fwrite($handle, "\nTable Count: ".$tables->length);
	  }
	  if($tables->length < 1) 
	  {
	    return "DOM Table Count is ".$tables->length.", nothing to Export.";
	  }
	  $tbcnt = $tables->length - 1;                 // count minus 1 for 0 indexed loop over tables
	  if($tbcnt > $limit) 
	  {
	    $tbcnt = $limit;
	  }
	  //
	  //
	  // Create new PHPExcel object with default attributes
	  //
		require_once DIR_CONTRIB_PATH . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php';
	  $objPHPExcel = new PHPExcel();
	  $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
	  $objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
	  $tm = date('YmdHis');
	  #$tfn = $user."_".$tm."_".$tablevar.".xlsx";
	  //$fname = "AuditLog/".$tfn;
	  #$fname = $tfn;
	  $objPHPExcel->getProperties()->setCreator($user)
	  ->setLastModifiedBy($user)
	  ->setTitle($title)
	  ->setSubject($title)
	  ->setDescription($title)
	  ->setCompany($company);
	  //
	  // Loop over tables in DOM to create an array, each table becomes a worksheet
	  //
	  for($z=0;$z<=$tbcnt;$z++) 
	  {
	    $maxcols = 0;
	    $totrows = 0;
	    $headrows = array();
	    $bodyrows = array();
	    $r = 0;
	    $h = 0;
	    $rows = $tables->item($z)->getElementsByTagName('tr');
	    $totrows = $rows->length;
	    if($debug) 
	    {
	      fwrite($handle, "\nTotal Rows: ".$totrows);
	    }
	    //
	    // Get TH values
	    //
	    foreach ($rows as $row) {
	        $ths = $row->getElementsByTagName('th');
	        if(is_object($ths)) {
	          if($ths->length > 0) {
	            $headrows[$h]['colcnt'] = $ths->length;
	            if($ths->length > $maxcols) {
	              $maxcols = $ths->length;
	            }
	            $nodes = $ths->length - 1;
	            for($x=0;$x<=$nodes;$x++) {
	              $thishdg = utf8_decode($ths->item($x)->nodeValue);
	              $headrows[$h]['th'][] = $thishdg;
	              $headrows[$h]['bold'][] = $this->findBoldText($this->innerHTML($ths->item($x)));
	              if($ths->item($x)->hasAttribute('style')) {
	                $style = $ths->item($x)->getAttribute('style');
	                $stylecolor = findStyleColor($style);
	                if($stylecolor == '') {
	                  $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
	                }else{
	                  $headrows[$h]['color'][] = $stylecolor;
	                }
	              }else{
	                $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
	              }
	              if($ths->item($x)->hasAttribute('colspan')) {
	                $headrows[$h]['colspan'][] = $ths->item($x)->getAttribute('colspan');
	              }else{
	                $headrows[$h]['colspan'][] = 1;
	              }
	              if($ths->item($x)->hasAttribute('align')) {
	                $headrows[$h]['align'][] = $ths->item($x)->getAttribute('align');
	              }else{
	                $headrows[$h]['align'][] = 'left';
	              }
	              if($ths->item($x)->hasAttribute('valign')) {
	                $headrows[$h]['valign'][] = $ths->item($x)->getAttribute('valign');
	              }else{
	                $headrows[$h]['valign'][] = 'top';
	              }
	              if($ths->item($x)->hasAttribute('bgcolor')) {
	                $headrows[$h]['bgcolor'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
	              }else{
	                $headrows[$h]['bgcolor'][] = 'FFFFFF';
	              }
	            }
	            $h++;
	          }
	        }
	    }
	    //
	    // Get TD values
	    //
	    foreach ($rows as $row) {
	        $tds = $row->getElementsByTagName('td');
	        if(is_object($tds)) {
	          if($tds->length > 0) {
	            //
	            // see if there are checkboxes within each TD for the row and only include checked rows in output
	            //
	            $nodes = $tds->length - 1;                // number of TD's present
	            $includerow = true;                       // include row? (true or false), default = true
	            for($x=0;$x<=$nodes;$x++) {               // td nodes
	              $allinputs = $tds->item($x)->getElementsByTagName('input');
	              if(is_object($allinputs)) {
	                for ($i = 0; $i < $allinputs->length; $i++) {
	                  if($debug) {
	                    if ($allinputs->item($i)->hasAttributes()) {
	                      foreach ($allinputs->item($i)->attributes as $attr) {
	                        $name = $attr->nodeName;
	                        $value = $attr->nodeValue;
	                        fwrite($handle, "Attribute '$name' :: '$value'\n");
	                      }
	                    }
	                  }
	                  if($allinputs->item($i)->hasAttribute('type')) {
	                    $type = $allinputs->item($i)->getAttribute('type');
	                    if($debug) { fwrite($handle, "\nType: ".$type."\n"); }
	                  }
	                  if($type == 'checkbox') {
	                    if($allinputs->item($i)->hasAttribute('value')) {
	                      $value = $allinputs->item($i)->getAttribute('value');
	                      if($debug) { fwrite($handle, "\nValue: ".$value."\n"); }
	                      if($value == '0') {
	                        $includerow = false;
	                      }
	                    }else{
	                      if($debug) {
	                      	fwrite($handle, "\nInput field found as Type: ".$type."  with empty Value: ".$value."\n"); 
	                      }
	                    }
	                  }
	                }
	              }
	            }
	            // end checkbox row exclusion
	            if($includerow) {                     // checkbox is checked
	              $bodyrows[$r]['colcnt'] = $tds->length;
	              if($tds->length > $maxcols) {
	                $maxcols = $tds->length;
	              }
	              $nodes = $tds->length - 1;
	              for($x=0;$x<=$nodes;$x++) {
	                $thistxt = utf8_decode($tds->item($x)->nodeValue);
	                #var_dump($thistxt, is_float($thistxt), is_numeric($thistxt), is_integer($thistxt)); die();
	                if (preg_match('/^-?(?:\d+|\d*\.\d+)$/', $thistxt) || preg_match('/^-?(?:\d+|\d*\,\d+)$/', $thistxt)) 
	                {
	                	#var_dump(str_replace(',', '.', $thistxt));
	                	$thistxt = (float) str_replace(',', '.', $thistxt);
	                	#var_dump($thistxt);
	                }
	                $bodyrows[$r]['td'][] = $thistxt;
	                $bodyrows[$r]['bold'][] = $this->findBoldText($this->innerHTML($tds->item($x)));
	                if($tds->item($x)->hasAttribute('style')) {
	                  $style = $tds->item($x)->getAttribute('style');
	                  $stylecolor = findStyleColor($style);
	                  if($stylecolor == '') {
	                    $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
	                  }else{
	                    $bodyrows[$r]['color'][] = $stylecolor;
	                  }
	                }else{
	                  $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
	                }
	                if($tds->item($x)->hasAttribute('colspan')) {
	                  $bodyrows[$r]['colspan'][] = $tds->item($x)->getAttribute('colspan');
	                }else{
	                  $bodyrows[$r]['colspan'][] = 1;
	                }
	                if($tds->item($x)->hasAttribute('align')) {
	                  $bodyrows[$r]['align'][] = $tds->item($x)->getAttribute('align');
	                }else{
	                  $bodyrows[$r]['align'][] = 'left';
	                }
	                if($tds->item($x)->hasAttribute('valign')) {
	                  $bodyrows[$r]['valign'][] = $tds->item($x)->getAttribute('valign');
	                }else{
	                  $bodyrows[$r]['valign'][] = 'top';
	                }
	                if($tds->item($x)->hasAttribute('bgcolor')) {
	                  $bodyrows[$r]['bgcolor'][] = str_replace("#", "", $tds->item($x)->getAttribute('bgcolor'));
	                }else{
	                  $bodyrows[$r]['bgcolor'][] = 'FFFFFF';
	                }
	              }
	              $r++;
	            }
	          }
	        }
	    }
	    if($z > 0) {
	      $objPHPExcel->createSheet($z);
	    }
	    $suf = $z + 1;
	    $tableid = $tablevar.$suf;
	    $wksheetname = ucfirst($tableid);
	    $objPHPExcel->setActiveSheetIndex($z);                      // each sheet corresponds to a table in html
	    $objPHPExcel->getActiveSheet()->setTitle($wksheetname);     // tab name
	    $worksheet = $objPHPExcel->getActiveSheet();                // set worksheet we're working on
	    $style_overlay = array('font' =>
	                      array('color' =>
	                        array('rgb' => '000000'),'bold' => false,),
	                            'fill' =>
	                                array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCFF')),
	                            'alignment' =>
	                                array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	                                           'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
	                            'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
	                                               'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
	                                               'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
	                                               'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
	                         );
	    $xcol = '';
	    $xrow = 1;
	    $usedhdrows = 0;
	    $heightvars = array(1=>'42', 2=>'42', 3=>'48', 4=>'52', 5=>'58', 6=>'64', 7=>'68', 8=>'76', 9=>'82');
	    $mergedcells = false;
	    for($h=0;$h<count($headrows);$h++) {
	      $th = $headrows[$h]['th'];
	      $colspans = $headrows[$h]['colspan'];
	      $aligns = $headrows[$h]['align'];
	      $valigns = $headrows[$h]['valign'];
	      $bgcolors = $headrows[$h]['bgcolor'];
	      $colcnt = $headrows[$h]['colcnt'];
	      $colors = $headrows[$h]['color'];
	      $bolds = $headrows[$h]['bold'];
	      $usedhdrows++;
	      $mergedcells = false;
	      for($t=0;$t<count($th);$t++) {
	        if($xcol == '') {$xcol = 'A';}else{$xcol++;}
	        $thishdg = $th[$t];
	        $thisalign = $aligns[$t];
	        $thisvalign = $valigns[$t];
	        $thiscolspan = $colspans[$t];
	        $thiscolor = $colors[$t];
	        $thisbg = $bgcolors[$t];
	        $thisbold = $bolds[$t];
	        $strbold = ($thisbold==true) ? 'true' : 'false';
	        if($thisbg == 'FFFFFF') {
	          $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
	        }else{
	          $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
	        }
	        $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
	        $style_overlay['alignment']['horizontal'] = $thisalign;
	        $style_overlay['font']['color']['rgb'] = $thiscolor;
	        $style_overlay['font']['bold'] = $thisbold;
	        $style_overlay['fill']['color']['rgb'] = $thisbg;
	        if($thiscolspan > 1) {                                                // spans more than 1 column
	          $mergedcells = true;
	          $lastxcol = $xcol;
	          for($j=1;$j<$thiscolspan;$j++) {                                    // count to last column in span
	            $lastxcol++;
	          }
	          $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
	          if($debug) {
	            fwrite($handle, "\nmergeCells: ".$xcol.":".$xrow." ".$lastxcol.":".$xrow);
	          }
	          $worksheet->mergeCells($cellRange);                                // merge the columns
	          $worksheet->setCellValue($xcol.$xrow, $thishdg);
	          $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
	          $worksheet->getStyle($cellRange)->getAlignment()->setWrapText(true);
	          $num_newlines = substr_count($thishdg, "\n");                       // count number of newline chars
	          if($num_newlines > 1) {
	            $rowheight = $heightvars[1];                                      // default to 35
	            if(array_key_exists($num_newlines, $heightvars)) {                // I couldn't find a PHPExcel method
	              $rowheight = $heightvars[$num_newlines];                        // to do this, so I look to see how
	            }else{                                                            // many newlines and just guess at
	              $rowheight = 75;                                                // row height
	            }
	            $worksheet->getRowDimension($xrow)->setRowHeight($rowheight);     // adjust heading row height
	            //$worksheet->getRowDimension($xrow)->setRowHeight(-1);           // this doesn't work in PHPExcel
	          }
	          if($debug) {
	            fwrite($handle, "\n".$cellRange." ColSpan:".$thiscolspan." Color:".$thiscolor." Align:".$thisalign." VAlign:".$thisvalign." BGColor:".$thisbg." Bold:".$strbold." cellValue: ".$thishdg);
	          }
	          $xcol = $lastxcol;
	        }else{
	          $worksheet->setCellValue($xcol.$xrow, $thishdg);
	          $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
	          if($debug) {
	            fwrite($handle, "\n".$xcol.":".$xrow." ColSpan:".$thiscolspan." Color:".$thiscolor." Align:".$thisalign." VAlign:".$thisvalign." BGColor:".$thisbg." Bold:".$strbold." cellValue: ".$thishdg);
	          }
	        }
	      }
	      $xrow++;
	      $xcol = '';
	    }
	    //Put an auto filter on last row of heading only if last row was not merged
	    if(!$mergedcells) {
	      $worksheet->setAutoFilter("A$usedhdrows:" . $worksheet->getHighestColumn() . $worksheet->getHighestRow() );
	    }
	    if($debug) {
	      fwrite($handle, "\nautoFilter: A".$usedhdrows.":".$worksheet->getHighestColumn().$worksheet->getHighestRow());
	    }
	    // Freeze heading lines starting after heading lines
	    $usedhdrows++;
	    $worksheet->freezePane("A$usedhdrows");
	    if($debug) {
	      fwrite($handle, "\nfreezePane: A".$usedhdrows);
	    }
	    //
	    // Loop thru data rows and write them out
	    //
	    $xcol = '';
	    $xrow = $usedhdrows;
	    for($b=0;$b<count($bodyrows);$b++) {
	      $td = $bodyrows[$b]['td'];
	      $colcnt = $bodyrows[$b]['colcnt'];
	      $colspans = $bodyrows[$b]['colspan'];
	      $aligns = $bodyrows[$b]['align'];
	      $valigns = $bodyrows[$b]['valign'];
	      $bgcolors = $bodyrows[$b]['bgcolor'];
	      $colors = $bodyrows[$b]['color'];
	      $bolds = $bodyrows[$b]['bold'];
	      for($t=0;$t<count($td);$t++) {
	        if($xcol == '') {$xcol = 'A';}else{$xcol++;}

	  		$thistext = $td[$t]; $thisalign = $aligns[$t]; $thisvalign = $valigns[$t]; $thiscolspan = $colspans[$t]; $thiscolor = $colors[$t]; $thisbg = $bgcolors[$t]; $thisbold = $bolds[$t]; $strbold = ($thisbold==true) ? 'true' : 'false';
	        if($thisbg == 'FFFFFF') {
	          $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
	        }else{
	          $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
	        }
	        $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
	        $style_overlay['alignment']['horizontal'] = $thisalign;
	        $style_overlay['font']['color']['rgb'] = $thiscolor;
	        $style_overlay['font']['bold'] = $thisbold;
	        $style_overlay['fill']['color']['rgb'] = $thisbg;
	        if($thiscolspan > 1) {                                              // spans more than 1 column
	          $lastxcol = $xcol;
	          for($j=1;$j<$thiscolspan;$j++) {                                  // count spanned columns
	            $lastxcol++;
	          }
	          $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
	          if($debug) {
	            fwrite($handle, "\nmergeCells: ".$xcol.":".$xrow." ".$lastxcol.":".$xrow);
	          }
	          $worksheet->mergeCells($cellRange);                               // merge columns in span
	          $worksheet->setCellValue($xcol.$xrow, $thistext);
	          $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
	          $worksheet->getStyle($cellRange)->getAlignment()->setWrapText(true);
	          $num_newlines = substr_count($thistext, "\n");                       // count number of newline chars
	          if($num_newlines > 1) {
	            $rowheight = $heightvars[1];                                      // default to 35
	            if(array_key_exists($num_newlines, $heightvars)) {                // I could not find a method in PHPExcel
	              $rowheight = $heightvars[$num_newlines];                        // that would set row height automatically
	            }else{                                                            // based on content, so I guess based
	              $rowheight = 75;                                                // on number of newlines in the content
	            }
	            $worksheet->getRowDimension($xrow)->setRowHeight($rowheight);     // adjust heading row height
	            //$worksheet->getRowDimension($xrow)->setRowHeight(-1);           // this doesn't work in PHPExcel
	          }
	          if($debug) {
	            fwrite($handle, "\n".$cellRange." ColSpan:".$thiscolspan." Color:".$thiscolor." Align:".$thisalign." VAlign:".$thisvalign." BGColor:".$thisbg." Bold:".$strbold." cellValue: ".$thistext);
	          }
	          //$worksheet->getRowDimension($xrow)->setRowHeight(-1);
	          $xcol = $lastxcol;
	        }else{
	          $worksheet->getColumnDimension($xcol)->setWidth(25);                // default width
	          $worksheet->setCellValue($xcol.$xrow, $thistext);
	          $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
	          if($debug) {
	            fwrite($handle, "\n".$xcol.":".$xrow." ColSpan:".$thiscolspan." Color:".$thiscolor." Align:".$thisalign." VAlign:".$thisvalign." BGColor:".$thisbg." Bold:".$strbold." cellValue: ".$thistext);
	          }
	        }
	      }
	      $xrow++;
	      $xcol = '';
	    }
	    // autosize columns to fit data
	    $azcol = 'A';
	    for($x=1;$x==$maxcols;$x++) {
	      $worksheet->getColumnDimension($azcol)->setAutoSize(true);
	      $azcol++;
	    }
	    if($debug) {
	      fwrite($handle, "\nHEADROWS: ".print_r($headrows, true));
	      fwrite($handle, "\nBODYROWS: ".print_r($bodyrows, true));
	    }
	  } // end for over tables
	  $objPHPExcel->setActiveSheetIndex(0);                      // set to first worksheet before close
	  //
	  // Write to Browser
	  //
	  if($debug) {
	    fclose($handle);
	  }
	  #header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	  #header("Content-Disposition: attachment;filename=$fname");
	  #header('Cache-Control: max-age=0');
	  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $format);
	  $objWriter->save($file);
	  return TRUE;
	}

	/**
	 * Extrae el HTML de un nodo
	 * @param  DOM $node Nodo
	 * @return string
	 */
	private function innerHTML($node) 
	{
		$doc = $node->ownerDocument;
		$frag = $doc->createDocumentFragment();
		foreach ($node->childNodes as $child) 
		{
			$frag->appendChild($child->cloneNode(TRUE));
		}
		return $doc->saveXML($frag);
	}

	/**
	 * Busca el color de un nodo
	 * @param  string $node [description]
	 * @return bool
	 */
	private function findSpanColor($node) 
	{
		$pos = stripos($node, "color:");       // ie: looking for style='color: #FF0000;'
		if ($pos === false) 
		{                  						//                        12345678911111
			return '000000';                    //                                 01234
		}
		$node = substr($node, $pos);           // truncate to color: start
		$start = "#";                          // looking for html color string
		$end = ";";                            // should end with semicolon
		$node = " ".$node;                     // prefix node with blank
		$ini = stripos($node,$start);          // look for #
		if ($ini === false) return "000000";   // not found, return default color of black
		$ini += strlen($start);                // get 1 byte past start string
		$len = stripos($node,$end,$ini) - $ini; // grab substr between start and end positions
		return substr($node,$ini,$len);        // return the RGB color without # sign
		}
		function findStyleColor($style) {
		  $pos = stripos($style, "color:");      // ie: looking for style='color: #FF0000;'
		  if ($pos === false) {                  //                        12345678911111
		    return '';                           //                                 01234
		  }
		  $style = substr($style, $pos);           // truncate to color: start
		  $start = "#";                          // looking for html color string
		  $end = ";";                            // should end with semicolon
		  $style = " ".$style;                     // prefix node with blank
		$ini = stripos($style,$start);          // look for #
		if ($ini === false) return "";         // not found, return default color of black
		$ini += strlen($start);                // get 1 byte past start string
		$len = stripos($style,$end,$ini) - $ini; // grab substr between start and end positions
		return substr($style,$ini,$len);        // return the RGB color without # sign
	}

	/**
	 * Decide si un nodo está en negrita
	 * @param  string $node Nodo DOM
	 * @return bool
	 */
	private function findBoldText($node) 
	{
	  $pos = stripos($node, "<b>");          // ie: looking for bolded text
	  if ($pos === false) {                  //                        12345678911111
	    return false;                        //                                 01234
	  }
	  return true;                           // found <b>
	}
}

/* End of file exceldata.php */
/* Location: ./system/libraries/exceldata.php */
