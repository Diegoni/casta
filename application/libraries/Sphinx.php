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

require_once(DIR_CONTRIB_PATH. 'sphinx' . DS . 'api' . DS . 'sphinxapi.php');

/**
 * Búsquedas en Sphinx
 * @author alexl
 *
 */
class Sphinx {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Objecto Sphinx para comunicarse con el servidor
	 * @var SphinxClient
	 */
	private $cl;
	/**
	 * Número de resultados a devolver
	 * @var int
	 */
	private $_matches;
	/**
	 * Máximo resultado a conservar en la memoria
	 * @var int
	 */
	private $_maxmatches;
	/**
	 * When to stop searching all together (if different from zero)
	 * @var int
	 */
	private $_cutoff;
	/**
	 * Último error
	 * @var string
	 */
	private $_error;

	/**
	 * Constructor
	 * @return Sphinx
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		$host 		= $this->obj->config->item('bp.sphinx.host');
		$port 		= (int) $this->obj->config->item('bp.sphinx.port');
		$weights 	= $this->obj->config->item('bp.sphinx.weights');

		$this->cl = new SphinxClient();
		$this->cl->SetServer($host, $port);
		//$this->cl->SetMatchMode(SPH_MATCH_EXTENDED);

		if (count($weights))
		{
			if (is_string(key($weights)))
			{
				$this->cl->SetFieldWeights($weights);
			}
			else
			{
				$this->cl->SetWeights($weights);
			}
		}

		$this->_matches 	= (int) $this->obj->config->item('bp.sphinx.matches');
		$this->_maxmatches 	= (int) $this->obj->config->item('bp.sphinx.maxmatches');
		$this->_cutoff 		= (int) $this->obj->config->item('bp.sphinx.cutoff');

		log_message('debug', 'Shpinx Class Initialised via '.get_class($this->obj));
	}

	function get_error()
	{
		return $this->_error;
	}

	function search($term, $start = null, $limit = null, $sort = null, $dir = null)
	{
		$this->cl->SetSortMode(SPH_SORT_RELEVANCE /*SPH_SORT_EXTENDED/*, '@relevance DESC, cTitulo ASC'*/);
		$this->cl->SetMatchMode ( SPH_MATCH_ANY );
		if (isset($sort) && ($sort != ''))
		{
			/*			$this->cl->SetSortMode(SPH_SORT_EXTENDED, $sort);
			 }

			 if (isset($dir) && ($dir != ''))
			 {*/
			#echo ($dir == 'ASC')?SPH_SORT_ATTR_ASC:SPH_SORT_ATTR_DESC;
			#$this->cl->SetSortMode(SPH_SORT_EXTENDED, $sort);
			#$this->cl->SetSortMode ( ($dir == 'ASC')?SPH_SORT_ATTR_ASC:SPH_SORT_ATTR_DESC, $sort );
		}

		$this->cl->SetLimits((isset($start)?(int)$start:0), (isset($limit)?(int)$limit:$this->_matches), $this->_maxmatches, $this->_cutoff);

		# search all indices
		$res = $this->cl->Query($term, "*");

		# display the results
		if ($res === FALSE)
		{
			$this->_error = $this->cl->GetLastError();
			return FALSE;
		}
		else
		{
			if ($this->cl->GetLastWarning())
			{
				$this->_error = $this->cl->GetLastWarning();
				return FALSE;
			}
		}

		//$data = $res;
		//$data['time'] 	= $res['time'];
		//$data['total'] 	= $res['total_found'];
		//$res['items'] 	= array();
		$res['from'] 	= $start;
		$res['to'] 		= $limit;

		return $res;
	}

	function query($term, $page = 1, $sortby = null, $groupby = null, $groupsort = null)
	{
		#$this->cl->SetSelect('');

		if (isset($groupby) && isset($groupsort))
		{
			$this->cl->SetGroupBy($groupby, SPH_GROUPBY_ATTR, $groupsort);
		}

		if (isset($sortby))
		{
			$this->cl->SetSortMode(SPH_SORT_EXTENDED, $sortby);
		}

		$this->cl->SetLimits(($page-1) * $this->_matches, $this->_matches, $this->_maxmatches, $this->_cutoff);

		# search all indices
		$res = $this->cl->Query($term, "*");
		# display the results
		if ($res === FALSE)
		{
			$this->_error = $this->cl->GetLastError();
			return -1;
		}
		else
		{
			if ($this->cl->GetLastWarning())
			{
				$this->_error = $this->cl->GetLastWarning();
				return -1;
			}
		}
		//$data = $res;
		//$data['time'] 	= $res['time'];
		//$data['total'] 	= $res['total_found'];
		//$res['items'] 	= array();
		$res['from'] 	= ($page - 1) * $this->_matches + 1;
		$res['to'] 	= $page * $this->_matches;
		$res['page'] 	= $page;
		//$data['matches'] = $res["matches"];
		if (isset($res["matches"]) && is_array($res["matches"]))
		{

		}
		return $res;
	}
}
/* End of file Sphinx.php */
/* Location: ./system/libraries/sphinx.php */