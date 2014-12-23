
update Cat_Fondo
set fPrecioCompra = e.fCoste
from Doc_LineasAlbaranesSalida a
	inner join Doc_AlbaranesSalida b
		on a.nIdAlbaran = b.nIdAlbaran
	inner join Cat_Fondo c
		on a.nIdLibro = c.nIdLibro
	inner join (select MAX(nIdLinea) nIdLinea, nIdLibro
				from Doc_LineasAlbaranesEntrada
				group by nIdLibro
				) d
				on c.nIdLibro = d.nIdLibro
	inner join Doc_LineasAlbaranesEntrada e
		on e.nIdLinea = d.nIdLinea
where /*b.nIdFactura = 2159541
	and a.fCoste = 0
	and*/ c.fPrecioCompra = 0
	and e.fCoste > 0



update Doc_LineasAlbaranesSalida
set fCoste = c.fPrecioCompra
from Doc_LineasAlbaranesSalida a
	inner join Doc_AlbaranesSalida b
		on a.nIdAlbaran = b.nIdAlbaran
	inner join Cat_Fondo c
		on a.nIdLibro = c.nIdLibro
where /*b.nIdFactura = 2159541
	and */a.fCoste = 0
	and c.fPrecioCompra > 0
	