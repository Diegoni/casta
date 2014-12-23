update Cat_Fondo c
	inner join Doc_LineasAlbaranesSalida a
		on a.nIdLibro = c.nIdLibro
	inner join Doc_AlbaranesSalida b
		on a.nIdAlbaran = b.nIdAlbaran
	inner join (select MAX(nIdLinea) nIdLinea, nIdLibro
				from Doc_LineasAlbaranesEntrada
				group by nIdLibro
				) d
				on c.nIdLibro = d.nIdLibro
	inner join Doc_LineasAlbaranesEntrada e
		on e.nIdLinea = d.nIdLinea
set c.fPrecioCompra = e.fCoste
where c.fPrecioCompra = 0
	and e.fCoste > 0;


update Doc_LineasAlbaranesSalida a
inner join Doc_AlbaranesSalida b
		on a.nIdAlbaran = b.nIdAlbaran
	inner join Cat_Fondo c
		on a.nIdLibro = c.nIdLibro
set fCoste = c.fPrecioCompra
where a.fCoste = 0
	and c.fPrecioCompra > 0;