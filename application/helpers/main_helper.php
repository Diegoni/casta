<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	

	function restarFechasFormulario($salida, $entrada){
		$array_entrada = explode("/", $entrada); 
		$array_salida = explode("/", $salida); 
			
		$timestamp1 = mktime(0,0,0,$array_entrada[1],$array_entrada[0],$array_entrada[2]); 
		$timestamp2 = mktime(4,12,0,$array_salida[1],$array_salida[0],$array_salida[2]); 

		$segundos_diferencia = $timestamp1 - $timestamp2; 

		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24); 

		$dias_diferencia = abs($dias_diferencia); 

		$dias_diferencia = floor($dias_diferencia); 

		return $dias_diferencia; 		
	}	
	
	function myTruncate($string, $limit, $break=".", $pad="...") { 
	
		if(strlen($string) <= $limit){
			return $string;	
		} 
		
		if(false !== ($breakpoint = strpos($string, $break, $limit))) {
			if($breakpoint < strlen($string)-1){
				$string = substr($string, 0, $breakpoint) . $pad; 
			} 
		} 
		
		return $string; 
	} 
							
	

