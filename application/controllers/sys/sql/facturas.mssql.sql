DELETE FROM Doc_LineasAlbaranesSalida
WHERE nIdAlbaran IN (
    SELECT nIdAlbaran
    FROM Doc_AlbaranesSalida
    WHERE nIdEstado = 1
    AND nIdFactura IN (
        SELECT nIdFactura
        FROM Doc_Facturas
        WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
        )
    )
    
DELETE FROM Doc_AlbaranesSalida
WHERE nIdEstado = 1
AND nIdFactura IN (
    SELECT nIdFactura
    FROM Doc_Facturas
    WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
    )


DELETE FROM Doc_FacturasModosPago
WHERE nIdFactura IN (
    SELECT nIdFactura
    FROM Doc_Facturas
    WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
)

UPDATE Doc_AlbaranesSalida
SET nIdFactura = NULL
WHERE nIdFactura IN (
    SELECT nIdFactura
    FROM Doc_Facturas
    WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
)

UPDATE Doc_PedidosCliente
SET nIdFactura = NULL
WHERE nIdFactura IN (
    SELECT nIdFactura
    FROM Doc_Facturas
    WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
)

DELETE FROM Doc_Facturas
WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%

DELETE FROM Doc_LineasAlbaranesSalida2
WHERE nIdAlbaran IN (
    SELECT nIdAlbaran
    FROM Doc_AlbaranesSalida2
    WHERE nIdEstado = 1
    AND nIdFactura IN (
        SELECT nIdFactura
        FROM Doc_Facturas
        WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
        )
    )

DELETE FROM Doc_AlbaranesSalida2
WHERE nIdEstado = 1
AND nIdFactura IN (
    SELECT nIdFactura
    FROM Doc_Facturas
    WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
    )
 
DELETE FROM Doc_FacturasModosPago2
WHERE nIdFactura IN (
    SELECT nIdFactura
    FROM Doc_Facturas2
    WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
)
 
UPDATE Doc_AlbaranesSalida2
SET nIdFactura = NULL
WHERE nIdFactura IN (
    SELECT nIdFactura
    FROM Doc_Facturas2
    WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
)
 
DELETE FROM Doc_Facturas2
WHERE nIdEstado = 1 AND YEAR(dCreacion) <= %1%
 
