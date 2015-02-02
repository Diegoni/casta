<?php
/**
* Log Parser
*
* Parse the logs as per the formats listed.
* Note the class is designed to parse one line at a time to avoid large memory usage
*
* @author 	Salil Kothadia <salil@in-source.com>
* @license 	GNU/GPL
* @version	1.0 [Jan 2009]
* @todo		add support to more patterns / logs
*/

class LogParser
{
	// Just for reference, to be used to build more/other log formats
  	private $Directives = array(
		'h' => '(\d+.\d+.\d+.\d+)',	//ip
		'l' => '(\S+)', //auth,
		'u' => '(\S+)', //username
		't' => '\[(\S+):(\d+:\d+:\d+) (\S+)\]',	//datetime [30/Jan/2009:13:47:09 +0530]
		'r' => '(\S+)', //request
		's' => '(\S+)', //status
		'b' => '(\S+)', //bytecount
		'v' => '(\S+)', //domain
		'i' => '.*?', 	//header_lines
  	);
	// Just for reference, to be used to build more/other log formats
	private $LogFormats = array(
		'common' => '%h %l %u %t \"%r\" %>s %b',
		'common_with_virtual' => '%v %h %l %u %t \"%r\" %>s %b',
		'combined' => '%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\"',
		'combined_with_virtual' => '%v %h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\"',
		'combined_with_cookies' => '%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\" \"%{Cookies}i\"',
	);
	// combine the directives and log formats to get the pattern
	private $Patterns = array(
		'common' => "/^(\S+) (\S+) (\S+) \[(\S+):(\d+:\d+:\d+) (\S+)\] \"(\S+) (\S+) (\S+)\" (\S+) (\S+)$/",
		'common_with_virtual' => "/^(\S+) (\S+) (\S+) (\S+) \[(\S+):(\d+:\d+:\d+) (\S+)\] \"(\S+) (\S+) (\S+)\" (\S+) (\S+)$/",
		'combined' => "/^(\S+) (\S+) (\S+) \[(\S+):(\d+:\d+:\d+) (\S+)\] \"(\S+) (\S+) (\S+)\" (\S+) (\S+) \"(\S+)\" (\".*?\")$/",
		'combined_with_virtual' => "/^(\S+) (\S+) (\S+) (\S+) \[(\S+):(\d+:\d+:\d+) (\S+)\] \"(\S+) (\S+) (\S+)\" (\S+) (\S+) \"(\S+)\" (\".*?\")$/",
		'combined_with_cookies' => "/^(\S+) (\S+) (\S+) \[(\S+):(\d+:\d+:\d+) (\S+)\] \"(\S+) (\S+) (\S+)\" (\S+) (\S+) \"(\S+)\" (\".*?\") (\".*?\")$/",
	);
	private $fp; // File pointer

	/**
	* Constructor
	*
	* Create new object
	*
	* @param	String		Log file path/name
	* @return	Object
	*/
  	public function __construct($FileName)
  	{
    	// open the file
    	if ( !$this->fp = fopen($FileName, 'r') )
    	{
			throw new Exception('File could not be read');
    	}
	}

	/**
	* Destructor
	*
	* Close the file  
	*
	* @return	void
	*/
	public function __destruct()
	{
		// close the file
		fclose($this->fp); 
	}

	/**
	* Read a log line
	*
	* @return	String
	*/
	public function GetLine()
	{
		return fgets($this->fp);
	}

	/**
	* Parse a log string
	*
	* @param	String		Log string
	* @param	String		Log format, Options: ['common', 'common_with_virtual', 'combined'(default), 'combined_with_virtual', 'combined_with_cookies']
	* @return	array
	*/
	public function Parse($String, $Format='combined')
	{
		$Return = array(); 
		$i = 0;
		
		preg_match($this->Patterns[$Format], $String, $Matches);

		if (isset($Matches[$i])) // check that it parsed OK
		{
			if($Format == 'common_with_virtual' || $Format == 'combined_with_virtual')
			{
				$Return['virtual_host'] = $Matches[++$i];
			}
			
			$Return['ip'] 		= $Matches[++$i];
			$Return['identity'] = $Matches[++$i]; // auth
			$Return['username'] = $Matches[++$i];
			$Return['date'] 	= $Matches[++$i];
			$Return['time'] 	= $Matches[++$i];
			$Return['timezone'] = $Matches[++$i];
			$Return['method'] 	= $Matches[++$i];
			$Return['path'] 	= $Matches[++$i];
			$Return['protocol'] = $Matches[++$i];
			$Return['status'] 	= $Matches[++$i];
			$Return['bytecount']= $Matches[++$i];
			$Return['referer'] 	= $Matches[++$i];
			$Return['browser'] 	= $Matches[++$i];
		}

		return $Return;
	}

};