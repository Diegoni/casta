delete from Doc_Devoluciones
where nIdDevolucion NOT IN (
	select nIdDevolucion
	from Doc_LineasDevolucion
)

delete from Doc_PedidosProveedor
where nIdPedido NOT IN (
	select nIdPedido
	from Doc_LineasPedidoProveedor
)