--A SERVIR
ALTER TABLE Cat_Secciones_Libros DISABLE TRIGGER ALL
  
UPDATE Cat_Secciones_Libros
SET nStockServir =0
  
UPDATE Cat_Secciones_Libros
SET nStockServir = nCantidad
FROM Cat_Secciones_Libros s2 (NOLOCK)
    INNER JOIN
        (
        SELECT nIdLibro, nIdSeccion, SUM(nCantidad-nCantidadServida) nCantidad
        FROM Doc_LineasPedidoCliente lp (NOLOCK)
        WHERE lp.nIdEstado = 1
        GROUP BY nIdLibro, nIdSeccion
        ) s1
        ON s1.nIdLibro = s2.nIdLibro AND s1.nIdSeccion = s2.nIdSeccion
  
ALTER TABLE Cat_Secciones_Libros ENABLE TRIGGER ALL

--RESERVADO
ALTER TABLE Cat_Secciones_Libros DISABLE TRIGGER ALL
  
UPDATE Cat_Secciones_Libros
SET nStockReservado =0
  
UPDATE Cat_Secciones_Libros
SET nStockReservado = nCantidad
FROM Cat_Secciones_Libros s2 (NOLOCK)
    INNER JOIN
        (
            SELECT nIdLibro, nIdSeccion, SUM(nCantidadServida) nCantidad
            FROM Doc_LineasPedidoCliente lp (NOLOCK)
            WHERE lp.nIdEstado IN (6,3)
            GROUP BY nIdLibro, nIdSeccion
        ) s1
        ON s1.nIdLibro = s2.nIdLibro AND s1.nIdSeccion = s2.nIdSeccion
  
ALTER TABLE Cat_Secciones_Libros ENABLE TRIGGER ALL

--A DEVOLVER
ALTER TABLE Cat_Secciones_Libros DISABLE TRIGGER ALL
   
UPDATE Cat_Secciones_Libros
SET nStockADevolver =0 
WHERE nStockADevolver <> 0 
   
UPDATE Cat_Secciones_Libros
SET nStockADevolver = nCantidad
FROM Cat_Secciones_Libros s2 (NOLOCK)
    INNER JOIN
        (
            SELECT nIdLibro, nIdSeccion, SUM(nCantidad) nCantidad
            FROM Doc_LineasDevolucion ld (NOLOCK)
                INNER JOIN Doc_Devoluciones d (NOLOCK)
                    ON ld.nIdDevolucion = d.nIdDevolucion
            WHERE d.nIdEstado = 2
            GROUP BY nIdLibro, nIdSeccion
        ) s1
        ON s1.nIdLibro = s2.nIdLibro AND s1.nIdSeccion = s2.nIdSeccion
   
ALTER TABLE Cat_Secciones_Libros ENABLE TRIGGER ALL

--A PEDIR
ALTER TABLE Cat_Secciones_Libros DISABLE TRIGGER ALL
UPDATE Cat_Secciones_Libros
SET nStockAPedir =0
  
UPDATE Cat_Secciones_Libros
SET nStockAPedir = Cantidad
FROM Cat_Secciones_Libros s2
    INNER JOIN
(
SELECT nIdLibro, nIdSeccion, SUM(nCantidad) Cantidad
FROM Doc_LineasPedidoProveedor (NOLOCK)
WHERE nIdEstado = 1
GROUP BY nIdLibro, nIdSeccion
) s 
        ON s.nIdLibro = s2.nIdLibro AND s.nIdSeccion = s2.nIdSeccion
  
ALTER TABLE Cat_Secciones_Libros ENABLE TRIGGER ALL
  
--STOCK PENDIENTES RECIBIR
  
IF EXISTS (SELECT * FROM dbo.sysobjects WHERE id = object_id(N'[Tmp_StockPendienteRecibir]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
    DROP TABLE [Tmp_StockPendienteRecibir]
  
SELECT
    lp.nIdSeccion, 
    lp.nIdLibro, 
    SUM(lp.nCantidad - ISNULL(lp.nRecibidas, 0)) Faltan
    INTO Tmp_StockPendienteRecibir
FROM Doc_LineasPedidoProveedor lp (NOLOCK)
WHERE lp.nIdEstado IN (2, 4)
GROUP BY
    lp.nIdSeccion, 
    lp.nIdLibro
HAVING SUM(lp.nCantidad - ISNULL(lp.nRecibidas, 0)) <> 0
 
UPDATE Cat_Secciones_Libros
SET nStockRecibir = 0
  
ALTER TABLE Cat_Secciones_Libros DISABLE TRIGGER ALL
  
UPDATE Cat_Secciones_Libros
SET nStockRecibir = Faltan
FROM Cat_Secciones_Libros sl (NOLOCK)
    INNER JOIN (
        SELECT nIdSeccion, nIdLibro, Faltan
        FROM Tmp_StockPendienteRecibir r (NOLOCK)
    ) r
        ON sl.nIdSeccion = r.nIdSeccion AND sl.nIdLibro = r.nIdLibro
  
ALTER TABLE Cat_Secciones_Libros ENABLE TRIGGER ALL
