<?php #echo '<?xml version="1.0" encoding="utf-8"?>
<Servicios uidcliente="<?php echo $uid; ?>">
  <Envio codbarras="">
    <Fecha><?php echo format_date($dia);?></Fecha>
    <Bultos><?php echo isset($bultos)?$bultos:1;?></Bultos>
    <Remite>
      <Plaza></Plaza>
      <Nombre><?php echo $direccionrecoger['cTitular'];?></Nombre>
      <Direccion><?php echo $direccionrecoger['cCalle'];?></Direccion>
      <Poblacion><?php echo $direccionrecoger['cPoblacion'];?></Poblacion>
      <Provincia><?php echo $direccionrecoger['cRegion'];?></Provincia>
      <Pais></Pais>
      <CP><?php echo $this->utils->cleanCP($direccionrecoger['cCP']);?></CP>
      <Departamento></Departamento>
      <NIF></NIF>
      <Telefono><?php echo $telefonorecoger;?></Telefono>
      <Movil></Movil>
      <Email><?php echo $emailrecoger;?></Email>
      <Observaciones></Observaciones>
    </Remite>
    <Destinatario>
          <Codigo></Codigo>
          <Plaza></Plaza>
          <Nombre><?php echo $direccionenviar['cTitular'];?></Nombre>
          <Direccion><?php echo $direccionenviar['cCalle'];?></Direccion>
          <Poblacion><?php echo $direccionenviar['cPoblacion'];?></Poblacion>
          <Provincia><?php echo $direccionenviar['cRegion'];?></Provincia>
          <Pais></Pais>
          <CP><?php echo $this->utils->cleanCP($direccionenviar['cCP']);?></CP>
          <Departamento></Departamento>
          <NIF></NIF>
          <Telefono><?php echo $telefonoenviar;?></Telefono>
          <Movil><?php echo $telefonoenviar;?></Movil>
          <Email><?php echo $emailenviar;?></Email>
          <Observaciones><?php echo $observaciones;?></Observaciones>
          <ATT></ATT>
    </Destinatario>
    <Referencias>
      <Referencia tipo="C"><?php echo $ref;?></Referencia>
    </Referencias>
    <?php if (is_numeric($reembolso) && $reembolso > 0): ?>
    <Importes>
      <Reembolso><?php echo $reembolso;?></Reembolso>
    </Importes>
  <?php endif; ?>
  </Envio> 
  </Servicios>