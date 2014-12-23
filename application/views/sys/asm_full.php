
<?xml version="1.0" encoding="utf-8"?>
<Servicios uidcliente="">
  <Envio codbarras=""> <!-- Si viene sin codigo de barras, siempre será una grabación nueva -->
    <Fecha></Fecha>
    <FechaPrevistaEntrega></FechaPrevistaEntrega>
    <Portes></Portes>
    <Servicio></Servicio>
    <Horario></Horario>
    <Bultos></Bultos>
    <Peso></Peso>
    <Volumen></Volumen>
    <Pod></Pod>
    <Retorno></Retorno>
    <Declarado></Declarado>
    <DNINomb></DNINomb>
    <Remite>
      <Plaza></Plaza>
      <Nombre></Nombre>
      <Direccion></Direccion>
      <Poblacion></Poblacion>
      <Provincia></Provincia>
      <Pais></Pais>
      <CP></CP>
      <Departamento></Departamento>
      <NIF></NIF>
      <Telefono></Telefono>
      <Movil></Movil>
      <Email></Email>
      <Observaciones></Observaciones>
    </Remite>
    <Destinatario>
      <Codigo></Codigo>
      <Plaza></Plaza>
      <Nombre></Nombre>
      <Direccion></Direccion>
      <Poblacion></Poblacion>
      <Provincia></Provincia>
      <Pais></Pais>
      <CP></CP>
      <Departamento></Departamento>
      <NIF></NIF>
      <Telefono></Telefono>
      <Movil></Movil>
      <Email></Email>
      <Observaciones></Observaciones>
      <ATT></ATT>
    </Destinatario>
    <Referencias> <!-- cualquier numero siempre y cuando sean de distinto tipo-->
      <Referencia tipo=""></Referencia>
    </Referencias>
    <Importes>
      <Debidos></Debidos>
      <Reembolso></Reembolso>
    </Importes>
    <Seguro tipo="">
      <Descripcion></Descripcion>
      <Importe></Importe>
    </Seguro>
    <Cliente>
      <Codigo></Codigo>
      <Plaza></Plaza>
      <Agente></Agente>
    </Cliente>
    <Tracking> <!-- Admite el envío de varios tracking de igual o distinto tipo en el mismo fichero-->
      <Estado tipo="">
        <Fecha></Fecha>
        <Hora></Hora>
        <Descripcion></Descripcion>
        <Observaciones></Observaciones>
        <Traduccion></Traduccion>
      </Estado>
      <Incidencia tipo="">
        <Fecha></Fecha>
        <Hora></Hora>
        <Descripcion></Descripcion>
        <Observaciones></Observaciones>
        <Traduccion></Traduccion>
      </Incidencia>
      <POD tipo="">
        <Fecha></Fecha>
        <Hora></Hora>
        <Firma></Firma>
        <DNI></DNI>
        <Observaciones></Observaciones>
        <Traduccion></Traduccion>
      </POD>
      <TrackingCliente tipo="">
        <Fecha></Fecha>
        <Hora></Hora>
        <Descripcion></Descripcion>
        <Observaciones></Observaciones>
      </TrackingCliente>
    </Tracking>
  </Envio> 

  
  <Recogida codrecogida="">
    <Horarios>
      <Fecha dia="">
        <Horario desde="" hasta="" />
      </Fecha>
    </Horarios>
    <Vehiculo></Vehiculo>
    <Ayudante></Ayudante>
    <klms></klms>
    <RecogerEn>
      <Plaza></Plaza>
      <Nombre></Nombre>
      <Direccion></Direccion>
      <Poblacion></Poblacion>
      <Provincia></Provincia>
      <Pais></Pais>
      <CP></CP>
      <Departamento></Departamento>
      <NIF></NIF>
      <Telefono></Telefono>
      <Movil></Movil>
      <Email></Email>
      <Observaciones></Observaciones>
    </RecogerEn>
    <Entregas> <!--admite n envios, uno por destinatario previsto-->
      <Envio> 
        <FechaPrevistaEntrega></FechaPrevistaEntrega>
        <Portes></Portes>
        <Horario></Horario>
        <Pod></Pod>
        <Retorno></Retorno>
        <Declarado></Declarado>
        <DNINomb></DNINomb>
        <Destinatario>
          <Codigo></Codigo>
          <Plaza></Plaza>
          <Nombre></Nombre>
          <Direccion></Direccion>
          <Poblacion></Poblacion>
          <Provincia></Provincia>
          <Pais></Pais>
          <CP></CP>
          <Departamento></Departamento>
          <NIF></NIF>
          <Telefono></Telefono>
          <Movil></Movil>
          <Email></Email>
          <Observaciones></Observaciones>
          <ATT></ATT>
        </Destinatario>
        <Importes>
          <Reembolso></Reembolso>
        </Importes>
      </Envio>
    </Entregas>
    <Referencias>
      <Referencia tipo=""></Referencia>
    </Referencias>
    <Importes>
      <Debidos></Debidos>
      <Desembolso></Desembolso>
    </Importes>
    <Seguro tipo="">
      <Descripcion></Descripcion>
      <Importe></Importe>
    </Seguro>
    <Cliente>
      <Codigo></Codigo>
      <Plaza></Plaza>
      <Agente></Agente>
    </Cliente>
    <Tracking>
      <Estado tipo="">
        <Fecha></Fecha>
        <Hora></Hora>
        <Descripcion></Descripcion>
        <Observaciones></Observaciones>
        <Traduccion></Traduccion>
      </Estado>
      <Incidencia tipo="">
        <Fecha></Fecha>
        <Hora></Hora>
        <Descripcion></Descripcion>
        <Observaciones></Observaciones>
        <Traduccion></Traduccion>
      </Incidencia>
      <TrackingCliente tipo="">
        <Fecha></Fecha>
        <Hora></Hora>
        <Descripcion></Descripcion>
        <Observaciones></Observaciones>
      </TrackingCliente>
    </Tracking>
  </Recogida>
  </Servicios>