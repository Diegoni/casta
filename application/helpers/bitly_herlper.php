<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Helpers
 * @category	Heleprs
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Bit.ly is a great URL shortening service. I love their reliability, shortness of the URL, 
 * and the information they provide about a given URL. Recently Bit.ly updated their API to version 3 so 
 * I thought I’d update my original Bit.ly post. Here’s how you can create short URLs and expand short 
 * URLs using Bit.ly.
 * 
 * http://davidwalsh.name/bitly-api-php
 */
if ( ! function_exists('get_bitly_short_url'))
{
	/**
	 * returns the shortened url
	 * $short_url = get_bitly_short_url('http://davidwalsh.name/','davidwalshblog','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
	 * @param $url
	 * @param $login
	 * @param $appkey
	 * @param $format
	 * @return unknown_type
	 */
	function get_bitly_short_url($url, $login, $appkey, $format='txt')
	{
		$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
		return curl_get_result($connectURL);
	}
}
if ( ! function_exists('get_bitly_long_url'))
{
	/**
	 * get the long url from the short one
	 * returns expanded url
	 * $long_url = get_bitly_long_url($short_url,'davidwalshblog','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
	 * @param $url
	 * @param $login
	 * @param $appkey
	 * @param $format
	 * @return unknown_type
	 */
	function get_bitly_long_url($url, $login, $appkey, $format='txt')
	{
		$connectURL = 'http://api.bit.ly/v3/expand?login='.$login.'&apiKey='.$appkey.'&shortUrl='.urlencode($url).'&format='.$format;
		return curl_get_result($connectURL);
	}
}
if ( ! function_exists('curl_get_result'))
{
	/**
	 * returns a result form url 
	 * @param $url
	 * @return unknown_type
	 */
	function curl_get_result($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}

/* End of file bitly_helper.php */
/* Location: ./system/helpers/bitly_helper.php */