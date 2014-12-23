<?php

# bNoDto en Cat_Fondo
$sql = "SELECT * 
	FROM information_schema.COLUMNS 
	WHERE 
		TABLE_SCHEMA = 'Bibliopola' 
	AND TABLE_NAME = 'Cat_Fondo' 
	AND COLUMN_NAME = 'bNoDto'";

$query = $this->obj->db->query($sql);
$data = $this->obj->db->get_results($query);
if (count($data) == 0)
{
	$sql = "ALTER TABLE  `Bibliopola`.`Cat_Fondo`	
		ADD `bNoDto` TINYINT NULL";
	$this->obj->db->query($sql);
}

# cIdShipping en Documentos
$tables = array('Doc_Facturas', 'Doc_Facturas2', 'Doc_AlbaranesSalida', 'Doc_AlbaranesSalida2', 'Doc_Devoluciones');
foreach ($tables as $t)
{
	$sql = "SELECT * 
		FROM information_schema.COLUMNS 
		WHERE 
			TABLE_SCHEMA = 'Bibliopola' 
		AND TABLE_NAME = '{$t}' 
		AND COLUMN_NAME = 'cIdShipping'";

	$query = $this->obj->db->query($sql);
	$data = $this->obj->db->get_results($query);
	if (count($data) == 0)
	{
		$sql = "ALTER TABLE  `Bibliopola`.`{$t}`	
			ADD `cIdShipping` VARCHAR(50) NULL";
		$this->obj->db->query($sql);
	}
}