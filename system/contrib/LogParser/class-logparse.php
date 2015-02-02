<?php
/**
 * Log Parser for PEAR logfiles
 *
 * @package util.voorraadbeheer.acsi
 * @author Bram Gerritsen
 * @copyright Copyright (c) 2004-2005
 * @version 0.2.0
 * @access public
 **/
class LogParse
{
	var $logfile;
	var $_line = array();
	var $_filter = array();
	var $_fromDate;
	var $_toDate;
	var $_limit;
	var $_countLines = 0;
	var $_parsetime = 0;

	/**
	 * LogParse()
	 * Constructor
	 * @param string $logfile
	 * @return void
	 **/
	function __construct($logfile)
	{
		$this->logfile = $logfile;
	}

	/**
	 * _parseFile()
	 * Read the file and save all elements in the LINE array
	 * @return void
	 **/
	function parseFile()
	{
		if(file_exists($this->logfile))
		{
			$time = $this->_getMicrotime();
			$lines = file($this->logfile);
			for ($i=count($lines)-1; $i>=0; $i--)
			{
				$line = $lines[$i];
				$field_date = trim(substr($line,0,10));
				$field_time = trim(substr($line,11,8));
				$elements = explode("]", $line);
				if (count($elements) > 1)
				{
					$field_msg = trim($elements[1]);
				}
				else
				{
					$field_msg[] = "";
				}
				$elements = explode("[", $elements[0]);
				if (count($elements) > 1)
				{
					$field_prio = trim($elements[1]);
				}
				else
				{
					$field_prio = "";
				}
				$field_ident = trim(substr($elements[0],19));
				$show = true;
				if (isset($this->_filter) && is_array($this->_filter))
				{
					foreach($this->_filter as $field => $value)
					{
						$var = "field_".$field;
						if (!eregi($value,$$var))
						$show = false;
					}
				}
				$d = substr($field_date,4,2);
				$m = $this->_getMonthNumber(substr($field_date,0,3));
				if ($this->_fromDate != "" && $show == true)
				{
					$from_d = substr($this->_fromDate,0,2);
					$from_m = substr($this->_fromDate,3,2);
					if ($d >= $from_d && $m >= $from_m)
					$show = true;
					else
					$show = false;
				}
				if ($this->_toDate != "" && $show == true)
				{
					$to_d = substr($this->_toDate,0,2);
					$to_m = substr($this->_toDate,3,2);
					if ($d <= $to_d && $m <= $to_m)
					$show = true;
					else
					$show = false;
				}
				if ($show == true)
				{
					$this->_line[$this->_countLines]["date"] = $field_date;
					$this->_line[$this->_countLines]["time"] = $field_time;
					$this->_line[$this->_countLines]["prio"] = $field_prio;
					$this->_line[$this->_countLines]["ident"] = $field_ident;
					$this->_line[$this->_countLines]["msg"] = $field_msg;
					$this->_countLines++;
					if ($this->_countLines > $this->_limit) break;
				}
			}
			$this->_parsetime = $this->_getMicrotime() - $time;
		}
		else
		{
			$this->_Error('LogParse: File ('.$this->logfile.') not found');
		}
		//fclose ($fp);
		return $this->_line;
	}

	/**
	 * setFromDate()
	 * Define the from date
	 * @param	string $date
	 * @return void
	 **/
	function setFromDate($from)
	{
		$this->_fromDate = $from;
	}

	/**
	 * setToDate()
	 * Define the to date
	 * @param string $to
	 * @return void
	 **/
	function setToDate($to)
	{
		$this->_toDate = $to;
	}

	/**
	 * setLimit()
	 * Sets the line limit
	 * @param string $num
	 * @return void
	 **/
	function setLimit($num)
	{
		$this->_limit = $num;
	}

	/**
	 * addFilter()
	 * Define all the search criteria
	 * @param string $field	 (prio, ident, msg)
	 * @param string $value
	 * @return void
	 **/
	function addFilter($field, $value)
	{
		$new_val = "";
		if ($field == "prio")
		{
			$new_val .= "[";
			if (is_numeric($value))
			$new_val .= Log::priorityToString($value);
			else
			$new_val .= $value;
			$new_val .= "]";
		}
		$new_val = $value;
		$this->_filter[$field] = $new_val;
	}

	/**
	 * Output()
	 * Displays all the result data non formatted
	 * @return string
	 **/
	function Output()
	{
		$content = $this->_line;
		$text = 'Lineas: '. count($content) .'<br/>';
		for($i=0;$i<count($content);$i++)
		{
			$text .= $content[$i]["date"]." ".$content[$i]["time"]." ".$content[$i]["ident"]." [".$content[$i]["prio"]."] ".$content[$i]["msg"]."<br>";
		}
		return $text;
	}

	/**
	 * getParseTime()
	 * Displays the time needed to parse the file
	 * @return float
	 **/
	function getParseTime()
	{
		return $this->_parsetime;
	}

	/**
	 * getCountLines()
	 * Returns number of results
	 * @return int
	 **/
	function getCountLines()
	{
		return $this->_countLines;
	}

	/**
	 * _Error()
	 * Displays error messages
	 * @param string $error
	 * @return void
	 **/
	private function _Error($error)
	{
		echo $error;
		exit();
	}

	/**
	 * _getMonthNumber()
	 * Get the month-number based on the shortname
	 * @param string $month
	 * @return int
	 **/
	private function _getMonthNumber($month)
	{
		$month = strtolower($month);
		switch($month)
		{
			case "jan": return 1;
			case "feb": return 2;
			case "mar": return 3;
			case "apr": return 4;
			case "may": return 5;
			case "jun": return 6;
			case "jul": return 7;
			case "aug": return 8;
			case "sep": return 9;
			case "oct": return 10;
			case "nov": return 11;
			case "dec": return 12;
		}
	}

	/**
	 * _getMicrotime()
	 * Returns the current time
	 * @return int
	 **/
	private function _getMicrotime()
	{
		$microtime = explode(' ', microtime());
		return $microtime[1].substr($microtime[0],1);
	}
}
