<?php /* Smarty version 2.6.26, created on 2010-04-06 16:34:49
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'index.tpl', 34, false),array('function', 'cycle', 'index.tpl', 43, false),)), $this); ?>
	
<?php if (! isset ( $this->_tpl_vars['errors'] )): ?>
<?php $_from = $this->_tpl_vars['listes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['entityname'] => $this->_tpl_vars['liste']):
?>
	
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>
">
	<table class="noborder" width="100%">
		<tr class="liste_titre"><?php echo ''; ?><?php $_from = $this->_tpl_vars['fields'][$this->_tpl_vars['entityname']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field_name'] => $this->_tpl_vars['field']):
?><?php echo '<td>'; ?><?php echo $this->_tpl_vars['field']['label']; ?><?php echo ''; ?><?php if ($this->_tpl_vars['field']['attribute'] != 'factoryPrice'): ?><?php echo '<a href="'; ?><?php echo $this->_tpl_vars['this_link']; ?><?php echo '?entity='; ?><?php echo $this->_tpl_vars['entityname']; ?><?php echo '&sortfield='; ?><?php echo $this->_tpl_vars['field_name']; ?><?php echo '&sortorder=asc'; ?><?php echo $this->_tpl_vars['params'][$this->_tpl_vars['entityname']]; ?><?php echo '">'; ?><?php echo $this->_tpl_vars['img_down']; ?><?php echo '</a><a href="'; ?><?php echo $this->_tpl_vars['this_link']; ?><?php echo '?entity='; ?><?php echo $this->_tpl_vars['entityname']; ?><?php echo '&sortfield='; ?><?php echo $this->_tpl_vars['field_name']; ?><?php echo '&sortorder=desc'; ?><?php echo $this->_tpl_vars['params'][$this->_tpl_vars['entityname']]; ?><?php echo '">'; ?><?php echo $this->_tpl_vars['img_up']; ?><?php echo '</a>'; ?><?php endif; ?><?php echo '</td>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '<td></td></tr>'; ?>

	<input type="hidden" name="search" value="<?php echo $this->_tpl_vars['entityname']; ?>
" />
		<tr class="liste_titre"><?php echo ''; ?><?php $_from = $this->_tpl_vars['fields'][$this->_tpl_vars['entityname']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field_name'] => $this->_tpl_vars['field']):
?><?php echo ''; ?><?php $this->assign('att_name', ((is_array($_tmp=$this->_tpl_vars['entityname'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['field_name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['field_name']))); ?><?php echo '<td class="liste_titre">'; ?><?php if (( $this->_tpl_vars['field']['type'] != 'datetime' ) && ( $this->_tpl_vars['field']['type'] != '' )): ?><?php echo '<input class="flat" type="text" size="10" name="'; ?><?php echo $this->_tpl_vars['att_name']; ?><?php echo '" value="'; ?><?php echo $_GET[$this->_tpl_vars['att_name']]; ?><?php echo '" />'; ?><?php endif; ?><?php echo '</td>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '<td class="liste_titre" align="right"><input value="button_search" class="liste_titre" src="'; ?><?php echo $this->_tpl_vars['img_search']; ?><?php echo '" name="button_search" alt="search" type="image"><input value="button_clear" class="liste_titre" src="'; ?><?php echo $this->_tpl_vars['img_searchclear']; ?><?php echo '" name="button_clear" alt="clear" type="image"></td></tr>'; ?>
<?php $_from = $this->_tpl_vars['liste']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['obj']):
?>
		<tr class="<?php echo smarty_function_cycle(array('values' => "impair,pair"), $this);?>
"><?php echo ''; ?><?php $_from = $this->_tpl_vars['obj']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['att']):
?><?php echo ''; ?><?php if (strcmp ( $this->_tpl_vars['key'] , 'ID' ) != 0): ?><?php echo '<td>'; ?><?php if ($this->_tpl_vars['att']['link'] != ''): ?><?php echo '<a href="'; ?><?php echo $this->_tpl_vars['att']['link']; ?><?php echo '">'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['att']['attribute'] == 'product_ref'): ?><?php echo ''; ?><?php echo $this->_tpl_vars['imgProduct']; ?><?php echo ''; ?><?php endif; ?><?php echo ' '; ?><?php echo $this->_tpl_vars['att']['value']; ?><?php echo ''; ?><?php if ($this->_tpl_vars['att']['link'] != ''): ?><?php echo '</a>'; ?><?php endif; ?><?php echo '</td>'; ?><?php endif; ?><?php echo ''; ?><?php endforeach; endif; unset($_from); ?><?php echo '<td></td></tr>'; ?>

<?php endforeach; endif; unset($_from); ?>
	</table>
	</form>
	<br>
	<div class="tabsAction">
	       <a class="butAction" href="<?php echo $this->_tpl_vars['buttons']['generateAll']['link']; ?>
"><?php echo $this->_tpl_vars['buttons']['generateAll']['label']; ?>
</a>
	</div>
	
	<br>
<?php endforeach; endif; unset($_from); ?>
<?php else: ?>
	<div class="error"><?php echo $this->_tpl_vars['errors']; ?>
</div>
<?php endif; ?> 
