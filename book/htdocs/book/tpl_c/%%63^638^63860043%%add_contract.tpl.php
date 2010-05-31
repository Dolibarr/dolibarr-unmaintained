<?php /* Smarty version 2.6.26, created on 2010-05-11 14:56:58
         compiled from /var/www/eclipse/dolibarrLast/htdocs/boo./tpl/add_contract.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', '/var/www/eclipse/dolibarrLast/htdocs/boo./tpl/add_contract.tpl', 105, false),)), $this); ?>
	
	
	
	
 
<?php if (isset ( $this->_tpl_vars['error'] )): ?>
<div class="error"><?php echo $this->_tpl_vars['error']; ?>
</div>
<?php endif; ?> 


	<table class="border" width="100%">
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['dataBook']['product_ref']['label']; ?>
</td>
			<td width="35%" colspan="3" style="font-weight: bold;"><?php echo $this->_tpl_vars['dataBook']['product_ref']['value']; ?>
</td>
		</tr>
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['dataBook']['product_label']['label']; ?>
</td>
			<td width="85%" colspan="3"><?php echo $this->_tpl_vars['dataBook']['product_label']['value']; ?>
</td>
			
		</tr>
	</table>
	
	<br>


  <script language="javascript" type="text/javascript" src="ajax/functions.js"></script>
	
  <form action="<?php echo $this->_tpl_vars['action_form']; ?>
" method="post" name="add">
  
  <input type="hidden" name="action" value="add">
  <input type="hidden" name="book_contract_fk_product_book" value="<?php echo $this->_tpl_vars['product_book']; ?>
">
   <input type="hidden" name="book_contract_fk_user" value="<?php echo $this->_tpl_vars['user']; ?>
">
   
  <table class="border" width="100%">
  
  <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['attribut']):
?>

	 <tr>
  		<td><?php echo $this->_tpl_vars['attribut']['label']; ?>
</td>
  		<td><?php echo $this->_tpl_vars['attribut']['html_input']; ?>
</td>
  	</tr>
  <?php endforeach; endif; unset($_from); ?>


    <?php $_from = $this->_tpl_vars['listFk']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['fk_form']):
?>
 	 <tr>
  		<td width="15%"><?php echo $this->_tpl_vars['fk_form']['label']; ?>
</td>
  		<td>						
  		  	<select id="<?php echo $this->_tpl_vars['fk_form']['entity']; ?>
_<?php echo $this->_tpl_vars['fk_form']['attribute']; ?>
" name="<?php echo $this->_tpl_vars['fk_form']['entity']; ?>
_<?php echo $this->_tpl_vars['fk_form']['attribute']; ?>
">
  		<?php if ($this->_tpl_vars['fk_form']['null'] == 0): ?><option value=""></option><?php endif; ?>
  		<?php $_from = $this->_tpl_vars['fk_form']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ID'] => $this->_tpl_vars['fk_form_attribut']):
?>
  			  		 	<option value="<?php echo $this->_tpl_vars['ID']; ?>
"<?php if ($this->_tpl_vars['ID'] == $this->_tpl_vars['fk_form']['value']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['fk_form_attribut']['nom']['value']; ?>
</option>		
  		 <?php endforeach; endif; unset($_from); ?>	
  	</select>	 	
  	<input type="text" id="input_<?php echo $this->_tpl_vars['fk_form']['entity']; ?>
_<?php echo $this->_tpl_vars['fk_form']['attribute']; ?>
" onkeyup="ajaxFilter('ajax/<?php echo $this->_tpl_vars['fk_form']['entity']; ?>
_<?php echo $this->_tpl_vars['fk_form']['attribute']; ?>
_query.php','input_<?php echo $this->_tpl_vars['fk_form']['entity']; ?>
_<?php echo $this->_tpl_vars['fk_form']['attribute']; ?>
','<?php echo $this->_tpl_vars['fk_form']['entity']; ?>
_<?php echo $this->_tpl_vars['fk_form']['attribute']; ?>
')" />
  		</td>
  	</tr> 	
  <?php endforeach; endif; unset($_from); ?>

  	<tr>
  		<td colspan="2" align="center"><input type="submit" value="<?php echo $this->_tpl_vars['button']; ?>
" class="button"></td>
  	</tr>
  
  </table>
  </form> 
  
  
<br>

<div class="titre"><?php echo $this->_tpl_vars['LastContracts']; ?>
</div>
<table class="noborder" width="100%">
	<tr class="liste_titre">
    <?php $_from = $this->_tpl_vars['fields_contracts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['class_field']):
?>
    	<?php $_from = $this->_tpl_vars['class_field']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['field']):
?>
		<td><?php echo $this->_tpl_vars['field']['label']; ?>
</td>
    	<?php endforeach; endif; unset($_from); ?>
   <?php endforeach; endif; unset($_from); ?>
	</tr>

<?php $_from = $this->_tpl_vars['listes_contracts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['obj']):
?>
	<?php $_from = $this->_tpl_vars['obj']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['class_obj']):
?>
	<tr class="<?php echo smarty_function_cycle(array('values' => "impair,pair"), $this);?>
">
	<?php $_from = $this->_tpl_vars['class_obj']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['att']):
?>
				<td><?php echo $this->_tpl_vars['att']['value']; ?>
</td>
	<?php endforeach; endif; unset($_from); ?>    
	</tr>
	<?php endforeach; endif; unset($_from); ?>
    <?php endforeach; endif; unset($_from); ?>  

</table>
  