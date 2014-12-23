<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *	The following file contains functions for transforming search
 *	strings into boolean SQL.  To download the sample script and
 *	dataset that use these functions, reference:
 *	http://davidaltherr.net/web/php_functions/boolean/example.mysql.boolean.txt
 *
 * 	Copyright 2001 David Altherr
 *		altherda@email.uc.edu
 *		www.davidaltherr.net
 *
 *	All material granted free for use under MIT general public license
 * http://www.evolt.org/article/Boolean_Fulltext_Searching_with_PHP_and_MySQL/18/15665/index.html
 * @package		Bibliopola 5.0
 * @subpackage	Helpers
 * @category	Heleprs
 * @author		Alejandro López, David Altherr
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 *	:: boolean_mark_atoms($string) ::
 * 	used to identify all word atoms; works using simple
 *	string replacement process:
 *    		1. strip whitespace
 *    		2. apply an arbitrary function to subject words
 *    		3. represent remaining characters as boolean operators:
 *       		a. ' '[space] -> AND
 *       		b. ','[comma] -> OR
 *       		c. '-'[minus] -> NOT
 *    		4. replace arbitrary function with actual sql syntax
 *    		5. return sql string
 * @param string $string
 * @return string sql string
 */
function boolean_mark_atoms($string){
	$result=trim(utf8_decode($string));
	#echo $result .'<br/>';

	/*$result=preg_replace(
		"/\"(.*)\"/e",
		"boolean_sql_phrase(\"$1\")",
		$result);*/

	$result = preg_replace_callback(
		"/\"(.*)\"/",
		function($params) {
			return boolean_sql_phrase($params[1]);
			},
		$result);
	#echo $result .'<br/>'; die();

	$result=str_replace('&', ' ', $result);
	$result=str_replace('|', ',', $result);
	$result=preg_replace("/([[:space:]]{2,})/",' ',$result);

	/* convert normal boolean operators to shortened syntax */
	$result=preg_replace('/\snot\s/i', ' ~', $result);
	$result=preg_replace('/\sand\s/i', ' ', $result);
	$result=preg_replace('/\sor\s/i', ',', $result);
	$result=preg_replace('/([><=])\s*/', '$1', $result);
	#echo $result .'<br/>';

	/* strip excessive whitespace */
	$result=str_replace('( ', '(', $result);
	$result=str_replace(' )', ')', $result);
	$result=str_replace(', ', ',', $result);
	$result=str_replace(' ,', ',', $result);
	$result=str_replace('~ ', '~', $result);

	/* apply arbitrary function to all 'word' atoms */
	$result=preg_replace(
		"/([^~|&\s\(\),]*)/",
		"foo[('$0')]bar",
		$result);

	/* strip empty or erroneous atoms */
	$result=str_replace("foo[('')]bar", '', $result);
	$result=str_replace("foo[('~')]bar", '~', $result);

	/* add needed space */
	$result=str_replace(')foo[(', ') foo[(', $result);
	$result=str_replace(')]bar(', ')]bar (', $result);

	/* dispatch ' ' to ' AND ' */
	$result=str_replace(' ',' AND ',$result);

	/* dispatch ',' to ' OR ' */
	$result=str_replace(',',' OR ',$result);

	/* dispatch '-' to ' NOT ' */
	$result=str_replace('~',' NOT ',$result);

	#echo $result .'<br/>'; die();
	return utf8_encode($result);
}

/**
 * Protege las frases para que no se tomen como palabras sueltas
 * @param string $phrase Frase a enmascarar/desenmascar
 * @param bool $reverse FALSE: protege TRUE: vuelve al valor normal
 * @return string
 */
function boolean_sql_phrase($phrase, $reverse = FALSE)
{
	$replaces = array(' ', '&', '~', ',', ' and ', ' not ', ' or ', '(', ')');
	foreach ( $replaces as $k => $v )
	{
		if ($reverse)
		{
			$phrase = str_replace("__{$k}__", $v, $phrase);
		}
		else
		{
			$phrase = str_replace($v, "__{$k}__", $phrase);
		}
	}
	return $phrase;
}

/**
 *	Parses short words < 4 chars into proper SQL: special adaptive
 *	case to force return of records without using fulltext index
 *	keep in mind that allowing this functionality may have serious
 *	performance issues, especially with large datasets
 * @param string $string
 * @param string $match
 * @param string $type number, date, string
 * @return string
 */
function boolean_sql_where_short($string, $match, $type)
{
	#var_dump($string, $match);
	$string = boolean_sql_phrase($string, TRUE);
	$match_a = explode(',', $match);
	$comp = '<>=';
	$like_a = array();
	$ci = get_instance();
	#var_dump($string);
	for($ith=0;$ith<count($match_a);$ith++)
	{
		switch ($type)
		{
			case 'string':
				$like_a[$ith] = " {$match_a[$ith]} LIKE '%{$string}%' ";
				break;
			case 'date':
				//Parte
				preg_match("/([{$comp}]*)(.*)/", $string, $r);
				$cp = $r[1];
				$date = $r[2];
				$date = format_mssql_date(to_date($date));
				if ($cp == '') $cp = '=';
				if ($cp == '=')
				{
					$like_a[$ith] = " ({$match_a[$ith]} >= {$date} AND {$match_a[$ith]} < " . $ci->db->dateadd('d', 1, $date) . ")";
				}
				else
				{
					$like_a[$ith] = " {$match_a[$ith]} {$cp} {$date} ";
				}

				break;
			case 'number':
				if (strpos($comp, substr($string, 0, 1)) === FALSE)
				{
					$like_a[$ith] = " {$match_a[$ith]} = {$string} ";
				}
				else
				{
					$like_a[$ith] = " {$match_a[$ith]} {$string} ";
				}
				break;
			default:
				$like_a[$ith] = " {$match_a[$ith]} LIKE '%{$string}%' ";
				break;
		}
	}
	#var_dump($like_a);
	$like = implode(" OR ",$like_a);

	return $like;
}

class T 
{ 
   function replace($params) 
   { 
		return '('. boolean_sql_where_short("{$params[1]}","{$this->match}", "{$this->type}") . ')';
   } 
} 

/**
 *	:: boolean_sql_where($string,$match) ::
 * 	function used to transform identified atoms into mysql
 *	parseable boolean fulltext sql string; allows for
 *	nesting by letting the mysql boolean parser evaluate
 *	grouped statements
 * @param string $string
 * @param string $match
 * @return string
 */
function boolean_sql_where($string, $match, $type = 'string')
{
	#print "INI:"; var_dump($string);
	$string = str_replace("'", '_??_', $string);
	#$string = str_replace("!!", '~', $string);
	$result = boolean_mark_atoms($string);
	#echo "MARK: "; 	var_dump($result);
	#var_dump($result);
	$t = new T; 
	$t->match = $match;
	$t->type = $type;
	
	$result=preg_replace_callback(
		"/foo\[\(\'([^\)]{1,})\'\)\]bar/",
		array($t, 'replace'), 
		#" '('.boolean_sql_where_short(\"$1\",\"{$match}\", \"{$type}\").')' ",
		$result);
	/*$result=preg_replace(
		"/foo\[\(\'([^\)]{1,})\'\)\]bar/e",
		" '('.boolean_sql_where_short(\"$1\",\"{$match}\", \"{$type}\").')' ",
		$result);*/
	#echo "RESULT {$result} ";die();
	$result = str_replace('_??_', "'", $result);
	#var_dump($result); die();

	return $result;
}

/* End of file searchparser_helper.php */
/* Location: ./system/application/helpers/searchparser_helper.php */