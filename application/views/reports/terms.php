<div id="terms"><?php if(isset($tNotasExternas) && (trim($tNotasExternas)!='')):?>
<h5><?php echo $this->lang->line('report-Comentarios');?></h5>
<div><?php echo $tNotasExternas;?></div>
<?php endif;?> <?php if(isset($texto_condiciones) && ($texto_condiciones != '')):?>
<h5><?php echo $this->lang->line('report-Condiciones');?></h5>
<div><?php echo $texto_condiciones;?></div>
<?php endif; ?>
<h5><?php echo $this->lang->line('report-Contacto');?></h5>
<div class="center"><?php echo $this->lang->line('report-Tel.');?>: <?php echo $this->config->item('company.telephone');?>&nbsp;/&nbsp;
<?php echo $this->lang->line('report-Fax.');?>: <?php echo $this->config->item('company.fax');?>
&nbsp;/&nbsp;<?php echo $this->lang->line('report-eMail');?>: <?php echo $this->config->item('company.email');?>
&nbsp;/&nbsp;<?php echo $this->lang->line('report-Web');?>: <?php echo $this->config->item('company.url');?><br />
</div>
</div>
