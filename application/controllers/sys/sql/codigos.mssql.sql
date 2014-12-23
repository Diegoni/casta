INSERT INTO Cat_Codigos_Fondo(nIdLibro, nCodigo, dCreacion, dAct, cCUser, cAUser)
select nIdLibro, nEAN, dCreacion, dAct, cCUser, cAUser
from Cat_Fondo
where nEAN NOT IN (
	select nCodigo FROM  Cat_Codigos_Fondo
)


INSERT INTO Cat_Codigos_Fondo(nIdLibro, nCodigo, dCreacion, dAct, cCUser, cAUser)
select nIdLibro, nIdLibro, dCreacion, dAct, cCUser, cAUser
from Cat_Fondo
where nIdLibro NOT IN (
	select nCodigo FROM  Cat_Codigos_Fondo
)
