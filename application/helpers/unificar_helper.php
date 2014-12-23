<?php
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
 * @filesource
 */

/**
 * Ayuda a unificar registros
 * @param MY_Model $model Modelo de datos principal
 * @param array $tablas Tablas vinculadas
 * @param int $id1 Id del registro bueno
 * @param int $id2 Id del registro a desaparecer
 * @param string $idrel Nombre del campo que relaciona las tablas
 * @return bool, TRUE: Ok
 */
function unificar_do($model, $tablas, $id1, $id2, $idrel)
{
	// Tablas vinculadas
	foreach($tablas as $tabla)
	{
		$tablename = $tabla['tabla'];
			
		$idname = (isset($tabla['id']))?$tabla['id']:$idrel;
		if (!isset($tabla['alter']) || ($tabla['alter']!==FALSE))
		{
			if (!unificar_alter($model, $tablename, FALSE))
			{
				return FALSE;
			}
		}

		$model->db->flush_cache();
		$model->db->where("{$idname} IN ({$id2})");
		if (isset($tabla['where'])) $model->db->where($tabla['where']);
		$model->db->update($tablename, array($idname => (int) $id1));
		if ($model->_check_error())
		{
			if (!isset($tabla['alter']) || ($tabla['alter']!==FALSE))
			{
				unificar_alter($model, $tablename, TRUE);
				return FALSE;
			}
		}

		if (!isset($tabla['alter']) || ($tabla['alter']!==FALSE))
		{
			if (!unificar_alter($model, $tablename, TRUE))
			{
				return FALSE;
			}
		}
	}
	return TRUE;
}

/**
 * Activa/Desactiva triggers
 * @param MY_Model $model Modelo de datos principal
 * @param string $tablename Tabla
 * @param bool $enable TRUE: activa, FALSE: desactiva
 * @return bool, TRUE: Ok
 */
function unificar_alter($model, $tablename, $enable = TRUE)
{
	/*$model->db->flush_cache();
	$enable = ($enable?'ENABLE':'DISABLE');
	$model->db->query("ALTER TABLE {$tablename} {$enable} TRIGGER ALL");
	if ($model->_check_error())
	{
		return FALSE;
	}*/
	return TRUE;
}


/**
 * Ayuda a unificar registros relacionados
 * @param MY_Model $model Modelo de datos principal
 * @param string $tablename Tabla relacionada
 * @param string $idrel Nombre del campo que relaciona las tablas
 * @param int $id1 Id del registro bueno
 * @param int $id2 Id del registro a desaparecer
 * @return bool, TRUE: Ok
 */
function unificar_nn($model, $tablename, $idname, $idrel, $id1, $id2)
{
	// Ubicaciones
	$model->db->flush_cache();
	if ($model->db->dbdriver == 'mssql')
	{
		$model->db->where("{$idname} IN ({$id2})")
		->where("{$idrel} IN (SELECT {$idrel}
				FROM {$tablename} (NOLOCK)
				WHERE {$idname} = {$id1})")
		->delete($tablename);
	}
	else
	{
		$sql = "DELETE a
			FROM {$tablename} a
			    INNER JOIN {$tablename} b
			WHERE a.{$idname} IN ({$id2})
			    AND b.{$idname} = {$id1}
			    AND a.{$idrel} = b.{$idrel}";
		$model->db->query($sql);
	}
	if ($model->_check_error())
	{
		$model->db->trans_rollback();
		return FALSE;
	}
	return TRUE;
}

/**
 * Borra los datos de las tablas relacionadas
 * @param MY_Model $model Modelo de datos principal
 * @param string $idname Nombre del campo que relaciona las tablas
 * @param string $tablename Tabla relacionada
 * @param int $id Id del registro a eliminar
 * @return bool, TRUE: Ok
 */
function unificar_delete($model, $idname, $tablename, $id)
{
	if (!is_array($tablename)) $tablaname = array($tablename);
	foreach ($tablename as $t)
	{
		$model->db->flush_cache();
		$model->db->where("{$idname} IN ({$id})")
		->delete($t);
		if ($model->_check_error())
		{
			$model->db->trans_rollback();
			return FALSE;
		}
	}
	return TRUE;
}

/**
 * Borra la caché interna relacionadas con las tablas
 * @param array $tablas Tablas 
 * @return bool, TRUE: Ok
 */
function unificar_clear_cache($tablas)
{
	// Limpieza de caches
	$ci = get_instance();
	// Tablas vinculadas
	$c = 0;
	foreach($tablas as $tabla)
	{
		if (isset($tabla['model']))
		{
			$n = 'r'.$c;
			$ci->load->model($tabla['model'], $n);
			$ci->$n->clear_cache();
		}
	}
}

/* End of file unificar_helper.php */
/* Location: ./system/application/helpers/unificar_helper.php */