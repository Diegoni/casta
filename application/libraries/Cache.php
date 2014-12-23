<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Caché
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
 * Usar cache en fichero
 * @var int
 */
define('CACHE_FILE', 	1);

/**
 * Usar caché en memoria
 * @var int
 */
define('CACHE_MEMORY',	2);
/**
 * @page cache_page Sistema de Caché
 * @section cache_page_description Descripción
 * El sistema de caché puede trabajar con Ficheros, <a href="http://php.net/manual/en/book.apc.php">APC</a> o <a href="http://php.net/manual/en/book.memcache.php">MemCache</a>. 
 * 
 * Se define el tipo de caché en la fichero de configuración del sistema en la variable <code>bp.cache.memory</code> pudiendo ser <b>file</b>, <b>apc</b> o  <b>memcache</b>.
 * El sistema comprueba si está instalado <code>apc</code> o <code>memcache</code> si han sido seleccionados, y sino usa <code>file</code> por defecto.
 * 
 * Las variables se pueden almacenar en grupo y se pueden eliminar variables individuales o grupos completos.
 * 
 * @section cache_page_use Uso
 * @code
#Carga la librería
$this->load->library('cache');

# Comprueba si existe una variable den el grupo 'test'
$cache_id = 'variable';
if ($cache = $this->cache->fetch('test', $cache_id, CACHE_MEMORY))
{
	return $cache;
}

#la almacena con un periodo de vida de  60 segundos
$this->cache->store('test', $cache_id, $cache, 60, CACHE_MEMORY);

#elimina la variable de la cache
$this->cache->delete('test', $cache_id);

#elimina todas las variables del grupo 'test'
$this->cache->delete('test');
 * @endcode  
 * 
 * @section cache_page_refs Referencias
 * <ul>
 * <li>@ref Sabre_Cache_APC</li>
 * <li>@ref Sabre_Cache_MemCache</li>
 * <li>@ref Sabre_Cache_Filesystem</li>
 */
/**
 * Sistema de Caché
 * @author alexl
 *
 */
class Cache
{
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Módulo de caché en memoria
	 * @var Sabre_Cache_APC / Sabre_Cache_MemCache
	 */
	private $memory;
	/**
	 * Módulo de caché en disco
	 * @var Sabre_Cache_Filesystem
	 */
	private $file;

	/**
	 * 	Constructor
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->obj->load->plugin('cache');
		$memory = $this->obj->config->item('bp.cache.memory');
		if ($memory == 'auto') $memory = 'apc';
		if ($memory == 'apc' && !function_exists('apc_fetch')) $memory = 'memcache';
		if ($memory == 'memcache' && !class_exists('MemCache')) $memory = 'file';
		$this->memory = ($memory == 'apc')?new Sabre_Cache_APC() : (($memory == 'memcache')?new Sabre_Cache_MemCache():new Sabre_Cache_Filesystem());
		if ($memory == 'memcache')
		{
			$servers = $this->obj->config->item('bp.cache.memcache');
			if (isset($servers))
			{
				foreach ($servers as $server)
				{
					$this->memory->addServer($server['host'], $server['port'], $server['weight']);
				}
			}
		}

		$this->file = new Sabre_Cache_Filesystem();

		log_message('debug', 'Cache Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Obitien el nombre de un grupo
	 * @param string $group Grupo
	 * @return string
	 */
	private function _get_group_name($group)
	{
		return 'group_' . $group;
	}

	/**
	 * Obtiene el nombre de una clave
	 * @param string $group Grupo
	 * @param string $key Clave
	 */
	private function _get_key_name($group, $key)
	{
		return isset($group)? $key = ($group . $key): $key;
	}

	/**
	 * Obtiene los elementos de caché pertenecientes a un grupo
	 * @param string $group Nombre del grupo
	 * @param int $mode Modo de cache
	 * @return array
	 */
	private function _get_group($group, $mode)
	{
		$g_key = $this->_get_group_name($group);
		$g = (($mode == CACHE_FILE)?$this->file->fetch($g_key):$this->memory->fetch($g_key));
		if ($g === FALSE)
		{
			$g = array();
		}
		else
		{
			//$g = unserialize($g);
		}
		#print "<pre>GET {$group}\n"; var_dump($g); print '</pre>';
		return $g;
	}

	/**
	 * Borra un grupo completo de claves
	 * @param string $group Nombre del grupo
	 * @param int $mode Mode de caché
	 */
	private function _delete_group($group, $mode)
	{
		$g = $this->_get_group($group, $mode);
		if (isset($g))
		{
			foreach($g as $k)
			{
				#print (($mode == CACHE_FILE)?'File':'Memory') ." - Borrando {$group} -> {$k}<br/>";
				($mode == CACHE_FILE)?$this->file->delete($group . $k):$this->memory->delete($group . $k);
			}
		}
		// Fix un BUG de APC
		$this->_set_group($group, null, $mode);
		$g_key = $this->_get_group_name($group);

		return ($mode == CACHE_FILE)?$this->file->delete($g_key):$this->memory->delete($g_key);

	}

	/**
	 * Elimina una clave de un grupo
	 * @param string $group Grupo de la clave
	 * @param string $key Clave
	 * @param string $mode Tipo de cache
	 */
	private function _delete_group_key($group, $key, $mode)
	{
		$g = $this->_get_group($group, $mode);
		unset($g[$key]);
		$this->_set_group($group, $g, $mode);
	}

	/**
	 * Añade una clave al grupo
	 * @param $group
	 * @param $g
	 * @param $mode
	 */
	private function _set_group($group, $g, $mode)
	{
		$g_key = $this->_get_group_name($group);
		#print "<pre>SET {$group}\n"; var_dump($g); print '</pre>';

		//$g = serialize($g);

		($mode == CACHE_FILE)?$this->file->store($g_key, $g, PG_CACHE_NOLIMIT):$this->memory->store($g_key, $g, PG_CACHE_NOLIMIT);
	}

	/**
	 * Obtiene las claves de un grupo
	 * @param string $group Grupo
	 * @param string $key Clave
	 * @param string $mode Tipo de cache
	 */
	private function _set_group_key($group, $key, $mode)
	{
		$g = $this->_get_group($group, $mode);
		$g[$key] = $key;
		$this->_set_group($group, $g, $mode);
	}

	/**
	 * 	Get and return an item from the cache
	 *
	 * 	@param	Cache Id
	 * 	@param	Cache group Id
	 * 	@param	Should I check the expiry time?
	 * 	@return The object or NULL if not available
	 */
	function fetch($group, $key, $mode = CACHE_FILE)
	{
		if (isset($group))
		{
			//Fix de APC: Comprueba grupo
			$g = $this->_get_group($group, $mode);
			if (!isset($g)) return null;
			if (!in_array($key, $g))
			{
				return null;
			}
		}
		$key = $this->_get_key_name($group, $key);
		return ($mode == CACHE_FILE)?$this->file->fetch($key):$this->memory->fetch($key);
	}

	/**
	 * 	Remove an item from the cache
	 *
	 * 	@param	Cache Id
	 * 	@param 	Cache group Id
	 */
	function delete($group, $key, $mode = CACHE_FILE)
	{
		if (isset($group))
		{
			if (isset($key))
			{
				$this->_delete_group_key($group, $key, $mode);
			}
			else
			{
				return $this->_delete_group($group, $mode);
			}
		}
		$key = $this->_get_key_name($group, $key);

		return ($mode == CACHE_FILE)?$this->file->delete($key):$this->memory->delete($key);
	}


	/**
	 * 	Save an item to the cache
	 *
	 * 	@param	Cache id
	 * 	@param	Data object
	 * 	@param	Cache group id (optional)
	 * 	@param	Time to live for this item
	 */
	function store($group, $key, $data, $ttl = PG_CACHE_NOLIMIT, $mode = CACHE_FILE)
	{
		if (isset($group))
		{
			$this->_set_group_key($group, $key, $mode);
		}
		$key = $this->_get_key_name($group, $key);

		return ($mode == CACHE_FILE)?$this->file->store($key, $data, $ttl):$this->memory->store($key, $data, $ttl);
	}

	/**
	 * Limpia la caché
	 * @param int $mode Tipo de cache
	 */
	function clear($mode = CACHE_FILE)
	{
		return ($mode == CACHE_FILE)?$this->file->clear():$this->memory->clear();
	}
}
