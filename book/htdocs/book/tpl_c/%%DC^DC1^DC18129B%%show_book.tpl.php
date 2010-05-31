<?php /* Smarty version 2.6.26, created on 2010-03-16 14:34:55
         compiled from show_book.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'show_book.tpl', 150, false),)), $this); ?>

<!-- BEGIN SMARTY TEMPLATE -->
<?php if (isset ( $this->_tpl_vars['data'] )): ?>
	<table class="border" width="100%">
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['product_ref']['label']; ?>
</td>
			<td width="35%" colspan="3" style="font-weight: bold;"><?php echo $this->_tpl_vars['data']['product_ref']['value']; ?>
</td>
		</tr>
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['product_label']['label']; ?>
</td>
			<td width="85%" colspan="3"><?php echo $this->_tpl_vars['data']['product_label']['value']; ?>
</td>
			
		</tr>
	</table>
	
	
	<br>
	
	<table class="border" width="100%">
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['book_isbn']['label']; ?>
</td>
			<td width="35%" colspan="3"><?php echo $this->_tpl_vars['data']['book_isbn']['value']; ?>
</td>
		</tr>
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['book_isbn13']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['book_isbn13']['value']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['data']['book_ean13']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['book_ean13']['value']; ?>
</td>
	</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
	
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['product_envente']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['product_envente']['value']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['data']['product_weight']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['product_weight']['value']; ?>
 <?php echo $this->_tpl_vars['data']['product_weight_units']['value']; ?>
</td>
		</tr>
		
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['book_nbpages']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['book_nbpages']['value']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['data']['book_format']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['book_format']['value']; ?>
</td>
		</tr>
	
	</table>
	
	<br>
	
	<table class="border" width="100%">
	
		<tr>
	 		<td width="15%"><?php echo $this->_tpl_vars['data']['product_price']['label']; ?>
</td>
	 		<td width="35%"><?php echo $this->_tpl_vars['data']['product_price']['value']; ?>
 HT</td>
	 		<td width="15%"><?php echo $this->_tpl_vars['data']['product_price_ttc']['label']; ?>
</td>
	 		<td width="35%"><b><?php echo $this->_tpl_vars['data']['product_price_ttc']['value']; ?>
 TTC</b></td>
		</tr>
		
	    <tr>
	    	<td width="15%"><?php echo $this->_tpl_vars['factory_price']['label']; ?>
 HT </td>
	    	<td width="35%"><?php echo $this->_tpl_vars['factory_price']['value']['HT']; ?>
 HT  (<?php echo $this->_tpl_vars['factory_price_value_ht_fr']; ?>
 Fr)</td>
	    	<td width="15%"><?php echo $this->_tpl_vars['factory_price']['label']; ?>
 TTC</td>
	    	<td width="35%"><b><?php echo $this->_tpl_vars['factory_price']['value']['TTC']; ?>
 TTC</b>  (<?php echo $this->_tpl_vars['factory_price_value_ttc_fr']; ?>
 Fr)</td>
	    </tr>
	    <tr>
	    	<td width="15%"><?php echo $this->_tpl_vars['factory_price_all']['label']; ?>
 HT</td>
	    	<td width="35%"><?php echo $this->_tpl_vars['factory_price_all']['value']['HT']; ?>
 HT  (<?php echo $this->_tpl_vars['factory_price_all_value_ht_fr']; ?>
 Fr)</td>
	    	<td width="15%"><?php echo $this->_tpl_vars['factory_price_all']['label']; ?>
 TTC</td>
	    	<td width="35%"><b><?php echo $this->_tpl_vars['factory_price_all']['value']['TTC']; ?>
 TTC</b>  (<?php echo $this->_tpl_vars['factory_price_all_value_ttc_fr']; ?>
 Fr)</td>
	    </tr>
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['product_tva_tx']['label']; ?>
</td>
			<td colspan="3"><?php echo $this->_tpl_vars['data']['product_tva_tx']['value']; ?>
 %</td>
		</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data']['product_stock_reel']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['product_stock_reel']['value']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['data']['product_seuil_stock_alerte']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data']['product_seuil_stock_alerte']['value']; ?>
</td>
		</tr>
		<tr>
	  		<td width="15%"><?php echo $this->_tpl_vars['data']['product_sold']['label']; ?>
</td>
	 		<td width="35%" colspan="3"><?php echo $this->_tpl_vars['data']['product_sold']['value']; ?>
</td>
		</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
	 	<tr>
	  		<td width="15%" valign="top"><?php echo $this->_tpl_vars['data']['product_description']['label']; ?>
</td>
	  		<td width="85%" colspan="3"><?php echo $this->_tpl_vars['data']['product_description']['value']; ?>
</td>
	 	</tr>
	 	<tr>
	  		<td width="15%" valign="top"><?php echo $this->_tpl_vars['data']['product_note']['label']; ?>
</td>
	  		<td width="85%" colspan="3"><?php echo $this->_tpl_vars['data']['product_note']['value']; ?>
</td>
	 	</tr>
	</table>
	
	<?php if ($this->_tpl_vars['buttons']['edit']['right'] == 1): ?>
	<div class="tabsAction">
	       <a class="butAction" href="<?php echo $this->_tpl_vars['buttons']['edit']['link']; ?>
"><?php echo $this->_tpl_vars['buttons']['edit']['label']; ?>
</a>
	</div>
	<?php endif; ?>
	
	
		
	<div class="titre"><?php echo $this->_tpl_vars['titre_compo']; ?>
</div>
	<table class="noborder" width="100%">
	    <tr class="liste_titre">
	    <?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field']):
?>
	    	<td><?php echo $this->_tpl_vars['field']['label']; ?>
</td>
	   <?php endforeach; endif; unset($_from); ?>
	   	<td></td>
	    </tr>
	
	<?php $_from = $this->_tpl_vars['listesCompo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['obj']):
?>
	
		<tr class="<?php echo smarty_function_cycle(array('values' => "impair,pair"), $this);?>
">
			<?php $_from = $this->_tpl_vars['obj']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['att']):
?>
	    			    		<?php if (( strcmp ( $this->_tpl_vars['key'] , 'id_compo' ) != 0 ) && ( strcmp ( $this->_tpl_vars['key'] , 'id_product' ) != 0 )): ?><td><a href="<?php echo $this->_tpl_vars['link']; ?>
<?php echo $this->_tpl_vars['obj']['id_product']; ?>
"><?php echo $this->_tpl_vars['att']['value']; ?>
</a></td><?php endif; ?>
	    	<?php endforeach; endif; unset($_from); ?>    
	    	<td><?php if (! empty ( $this->_tpl_vars['img_edit'] )): ?><a href="show_composition.php?id=<?php echo $this->_tpl_vars['id']; ?>
&compo_product=<?php echo $this->_tpl_vars['obj']['id_compo']; ?>
&action=edit"><?php echo $this->_tpl_vars['img_edit']; ?>
</a><a href="show_composition.php?id=<?php echo $this->_tpl_vars['id']; ?>
&compo_product=<?php echo $this->_tpl_vars['obj']['id_compo']; ?>
&action=delete"><?php echo $this->_tpl_vars['img_delete']; ?>
</a><?php endif; ?></td>	
	    </tr>
	    <?php endforeach; endif; unset($_from); ?>  
	
	</table>
	
	</div>
	
		
	<div class="titre"><?php echo $this->_tpl_vars['titre_contract']; ?>
</div>
	<div class="tabBar">
	
	<?php if (isset ( $this->_tpl_vars['NoContract'] )): ?>
	<div class="titre"><?php echo $this->_tpl_vars['NoContract']; ?>
</div>
	<?php else: ?>
	<table class="border" width="100%">
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data_contract']['book_contract_ref']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data_contract']['book_contract_ref']['value']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['data_contract']['book_contract_fk_soc']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data_contract']['book_contract_fk_soc']['value']; ?>
</td>	</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
		<tr>
			<td width="15%"><?php echo $this->_tpl_vars['data_contract']['book_contract_date_deb']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data_contract']['book_contract_date_deb']['value']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['data_contract']['book_contract_date_fin']['label']; ?>
</td>
			<td width="35%"><?php echo $this->_tpl_vars['data_contract']['book_contract_date_fin']['value']; ?>
</td>
		</tr>
		<tr>
			<td><?php echo $this->_tpl_vars['data_contract']['book_contract_taux']['label']; ?>
</td>
			<td><?php echo $this->_tpl_vars['data_contract']['book_contract_taux']['value']; ?>
</td>
			<td><?php echo $this->_tpl_vars['data_contract']['book_contract_quantity']['label']; ?>
</td>
			<td><?php echo $this->_tpl_vars['data_contract']['book_contract_quantity']['value']; ?>
</td>
		</tr>
		<tr>
			<td><?php echo $this->_tpl_vars['data_contract']['product_droit_price']['label']; ?>
</td>
			<td><?php echo $this->_tpl_vars['data_contract']['product_droit_price']['value']; ?>
</td>
			<td><?php echo $this->_tpl_vars['data_contract']['product_droit_price_tcc']['label']; ?>
</td>
			<td><?php echo $this->_tpl_vars['data_contract']['product_droit_price_tcc']['value']; ?>
</td>
		</tr>
	
	</table>
	<?php endif; ?>
	
	<?php if ($this->_tpl_vars['buttons']['generate']['right'] == 1): ?>
	<div class="tabsAction">
	       <?php if (! isset ( $this->_tpl_vars['NoContract'] )): ?><a class="butAction" href="<?php echo $this->_tpl_vars['buttons']['generate']['link']; ?>
"><?php echo $this->_tpl_vars['buttons']['generate']['label']; ?>
</a><?php endif; ?>
	       <a class="butAction" href="<?php echo $this->_tpl_vars['buttons']['new_contract']['link']; ?>
"><?php echo $this->_tpl_vars['buttons']['new_contract']['label']; ?>
</a> 
	       	</div>
	<?php endif; ?>
	
		
	<div class="titre"><?php echo $this->_tpl_vars['ContractsBill']; ?>
</div>
	<table class="noborder" width="100%">
		<tr class="liste_titre">
	    <?php $_from = $this->_tpl_vars['fields_bill']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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
	
	<?php $_from = $this->_tpl_vars['listes_bill']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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
						<td <?php if ($this->_tpl_vars['att']['name'] == 'ref'): ?>width="200"<?php endif; ?>><?php if ($this->_tpl_vars['att']['link'] != ''): ?><A href="<?php echo $this->_tpl_vars['att']['link']; ?>
"><?php echo $this->_tpl_vars['att']['value']; ?>
</A><?php else: ?><?php echo $this->_tpl_vars['att']['value']; ?>
<?php endif; ?></td>
		<?php endforeach; endif; unset($_from); ?>    
		</tr>
		<?php endforeach; endif; unset($_from); ?>
	    <?php endforeach; endif; unset($_from); ?>  
	
	</table>
	
	
	<br>
	
		
	<div class="titre"><?php echo $this->_tpl_vars['OldContracts']; ?>
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
<?php else: ?>
<div class="titre"><?php echo $this->_tpl_vars['NoBook']; ?>
</div>
<?php endif; ?>






<!-- END SMARTY TEMPLATE -->