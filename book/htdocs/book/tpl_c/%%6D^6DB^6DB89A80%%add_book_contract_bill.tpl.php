<?php /* Smarty version 2.6.26, created on 2010-05-31 12:13:39
         compiled from add_book_contract_bill.tpl */ ?>

 
<?php if (isset ( $this->_tpl_vars['error'] )): ?>
<div class="error"><?php echo $this->_tpl_vars['error']; ?>
</div>
<?php endif; ?> 




<br>
<?php if (isset ( $this->_tpl_vars['product'] )): ?>	
<form action="add_book_contract_bill.php?id=<?php echo $this->_tpl_vars['product']; ?>
" method="post">
<input type="hidden" name="action" value="generate">
<?php else: ?>
<form action="add_book_contract_bill.php" method="post">
<input type="hidden" name="action" value="generateAll">
<?php endif; ?>
<table class="border" width="100%">
		<tr>
		<td width="25%"><?php echo $this->_tpl_vars['data']['book_contract_bill_year']['label']; ?>
</td>
		<td><?php echo $this->_tpl_vars['data']['book_contract_bill_year']['html_input']; ?>
</td>
	</tr>

	<tr>
		<td colspan="2" align="center"><br><b><?php echo $this->_tpl_vars['Generation']; ?>
</b><br></td>
	
	</tr>
	

	<tr>
		<td colspan="2" align="center"><input type="submit" value="<?php echo $this->_tpl_vars['button']; ?>
" class="button"></td>
	</tr>
  
</table>
</form> 