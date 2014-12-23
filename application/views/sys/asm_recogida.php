<?php #echo '<?xml version="1.0" encoding="utf-8"?>
<Servicios uidcliente="<?php echo $uid; ?>">
  <Recogida codrecogida="">
    <Horarios>
      <Fecha dia="<?php echo format_date($dia);?>">
        <Horario desde="<?php echo $desde;?>" hasta="<?php echo $hasta;?>" />
      </Fecha>
    </Horarios>
    <RecogerEn>
      <Plaza></Plaza>
      <Nombre><?php echo $direccionrecoger['cTitular'];?></Nombre>
      <Direccion><?php echo $direccionrecoger['cCalle'];?></Direccion>
      <Poblacion><?php echo $direccionrecoger['cPoblacion'];?></Poblacion>
      <Provincia><?php echo $direccionrecoger['cRegion'];?></Provincia>
      <Pais></Pais>
      <CP><?php echo $direccionrecoger['cCP'];?></CP>
      <Departamento></Departamento>
      <NIF></NIF>
      <Telefono><?php echo $telefonorecoger;?></Telefono>
      <Movil></Movil>
      <Email><?php echo $emailrecoger;?></Email>
      <Observaciones></Observaciones>
    </RecogerEn>
    <Entregas>
      <Envio>
        <FechaPrevistaEntrega></FechaPrevistaEntrega>
        <Portes>P</Portes>
        <Servicio>37</Servicio>
        <Horario></Horario>
        <Pod></Pod>
        <Retorno></Retorno>
        <Declarado></Declarado>
        <DNINomb></DNINomb>
        <Destinatario>
          <Codigo></Codigo>
          <Plaza></Plaza>
          <Nombre><?php echo $direccionenviar['cTitular'];?></Nombre>
          <Direccion><?php echo $direccionenviar['cCalle'];?></Direccion>
          <Poblacion><?php echo $direccionenviar['cPoblacion'];?></Poblacion>
          <Provincia><?php echo $direccionenviar['cRegion'];?></Provincia>
          <Pais></Pais>
          <CP><?php echo $direccionenviar['cCP'];?></CP>
          <Departamento></Departamento>
          <NIF></NIF>
          <Telefono><?php echo $telefonoenviar;?></Telefono>
          <Movil><?php echo $telefonoenviar;?></Movil>
          <Email><?php echo $emailenviar;?></Email>
          <Observaciones><?php echo $observaciones;?></Observaciones>
          <ATT></ATT>
        </Destinatario>
        <Importes>
          <Reembolso><?php echo $reembolso;?></Reembolso>
        </Importes>
      </Envio>
    </Entregas>
    <Referencias>
      <Referencia tipo="C"><?php echo $ref;?></Referencia>
    </Referencias>
    <Importes>
      <Debidos></Debidos>
      <Desembolso></Desembolso>
    </Importes>
    <Seguro tipo="">
      <Descripcion></Descripcion>
      <Importe></Importe>
    </Seguro>
  </Recogida>
  </Servicios>