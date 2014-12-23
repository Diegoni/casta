#A SERVIR
UPDATE Cat_Secciones_Libros
SET nStockServir = 0;
  
UPDATE Cat_Secciones_Libros s2 
    INNER JOIN
        (
        SELECT nIdLibro, nIdSeccion, SUM(nCantidad-nCantidadServida) nCantidad
        FROM Doc_LineasPedidoCliente lp 
        WHERE lp.nIdEstado = 1
        GROUP BY nIdLibro, nIdSeccion
        ) s1
        ON s1.nIdLibro = s2.nIdLibro AND s1.nIdSeccion = s2.nIdSeccion
SET nStockServir = nCantidad;

#RESERVADO
UPDATE Cat_Secciones_Libros
SET nStockReservado =0;
  
UPDATE Cat_Secciones_Libros s2
    INNER JOIN
        (
            SELECT nIdLibro, nIdSeccion, SUM(nCantidadServida) nCantidad
            FROM Doc_LineasPedidoCliente lp 
            WHERE lp.nIdEstado IN (6,3)
            GROUP BY nIdLibro, nIdSeccion
        ) s1
        ON s1.nIdLibro = s2.nIdLibro AND s1.nIdSeccion = s2.nIdSeccion
SET nStockReservado = nCantidad;

#A DEVOLVER
UPDATE Cat_Secciones_Libros
SET nStockADevolver =0 
WHERE nStockADevolver <> 0;
   
UPDATE Cat_Secciones_Libros s2
    INNER JOIN
        (
            SELECT nIdLibro, nIdSeccion, SUM(nCantidad) nCantidad
            FROM Doc_LineasDevolucion ld
                INNER JOIN Doc_Devoluciones d 
                    ON ld.nIdDevolucion = d.nIdDevolucion
            WHERE d.nIdEstado = 2
            GROUP BY nIdLibro, nIdSeccion
        ) s1
        ON s1.nIdLibro = s2.nIdLibro AND s1.nIdSeccion = s2.nIdSeccion
SET nStockADevolver = nCantidad;

#A PEDIR
UPDATE Cat_Secciones_Libros
SET nStockAPedir =0;
  
UPDATE Cat_Secciones_Libros s2
    INNER JOIN
        (
        SELECT nIdLibro, nIdSeccion, SUM(nCantidad) Cantidad
        FROM Doc_LineasPedidoProveedor 
        WHERE nIdEstado = 1
        GROUP BY nIdLibro, nIdSeccion
        ) s 
        ON s.nIdLibro = s2.nIdLibro AND s.nIdSeccion = s2.nIdSeccion
SET nStockAPedir = Cantidad;
  
#STOCK PENDIENTES RECIBIR
DROP TABLE IF EXISTS Tmp_StockPendienteRecibir;
CREATE TABLE Tmp_StockPendienteRecibir (
  `nIdLibro` INT(10) NULL,
  `nIdSeccion` INT(10) NULL,
  `Faltan` INT(10) NULL
)
ENGINE = MEMORY;
  
INSERT INTO Tmp_StockPendienteRecibir(nIdLibro, nIdSeccion, Faltan)
    SELECT
        lp.nIdLibro, 
        lp.nIdSeccion, 
        SUM(lp.nCantidad - IFNULL(lp.nRecibidas, 0)) Faltan
    FROM Doc_LineasPedidoProveedor lp 
    WHERE lp.nIdEstado IN (2, 4)
    GROUP BY
        lp.nIdSeccion, 
        lp.nIdLibro
    HAVING SUM(lp.nCantidad - IFNULL(lp.nRecibidas, 0)) <> 0;


UPDATE Cat_Secciones_Libros
SET nStockRecibir = 0;

UPDATE Cat_Secciones_Libros sl
    INNER JOIN Tmp_StockPendienteRecibir r  
        ON sl.nIdSeccion = r.nIdSeccion AND sl.nIdLibro = r.nIdLibro
SET nStockRecibir = Faltan;

