<?php /* Smarty version 2.6.26, created on 2010-05-31 11:02:13
         compiled from edit_book.tpl */ ?>

<!-- BEGIN SMARTY TEMPLATE -->

<?php if (isset ( $this->_tpl_vars['error'] )): ?>
<div class="error"><?php echo $this->_tpl_vars['error']; ?>
</div>
<?php endif; ?> 
<form action="<?php echo $this->_tpl_vars['urlForm']; ?>
" method="POST">
<input type="hidden" name="action" value="update">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>
">
<input type="hidden" name="product_status" value="<?php echo $this->_tpl_vars['data']['product_status']['value']; ?>
">

<table class="border" width="100%">
 <tr>
   <td width="15%"><?php echo $this->_tpl_vars['data']['product_ref']['label']; ?>
</td>
   <td colspan="3"><?php echo $this->_tpl_vars['data']['product_ref']['input']; ?>
</td>
 </tr>

 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_label']['label']; ?>
</td>
  <td width="85%" colspan="3"><?php echo $this->_tpl_vars['data']['product_label']['input']; ?>
</td>
 </tr>
</table>

<br>
 

<table class="border" width="100%">
 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_isbn']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['book_isbn']['input']; ?>
 </td>
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_ean13']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['book_ean13']['input']; ?>
</td>
 </tr>

 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_isbn13']['label']; ?>
</td>
  <td colspan="3"><?php echo $this->_tpl_vars['data']['book_isbn13']['input']; ?>
</td>
 </tr>
</table>

<br>

<table class="border" width="100%">
 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_status']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['product_status']['input']; ?>
</td>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_weight']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['product_weight']['input']; ?>
 <?php echo $this->_tpl_vars['data']['product_weight_units']['input']; ?>
</td>
 </tr>
 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_nbpages']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['book_nbpages']['input']; ?>
</td>
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_format']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['book_format']['input']; ?>
</td>
 </tr>
</table> 

<br>

<table class="border" width="100%">

 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_price']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['product_price']['input']; ?>
 <?php echo $this->_tpl_vars['data']['product_price_base_type']['input']; ?>
</td>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_tva_tx']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['product_tva_tx']['input']; ?>
</td>
 </tr>
</table>

<br />

<table class="border" width="100%">
 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_seuil_stock_alerte']['label']; ?>
</td>
  <td cospan="3"><?php echo $this->_tpl_vars['data']['product_seuil_stock_alerte']['input']; ?>
</td>
 </tr>

</table>

<br />

<table class="border" width="100%">
 <tr>
  <td width="15%" valign="top"><?php echo $this->_tpl_vars['data']['product_description']['label']; ?>
</td>
  <td width="85%" colspan="3"><?php echo $this->_tpl_vars['data']['product_description']['input']; ?>
</td>
 </tr>

 <tr>
  <td width="15%" valign="top"><?php echo $this->_tpl_vars['data']['product_note']['label']; ?>
</td>
  <td width="85%" colspan="3"><?php echo $this->_tpl_vars['data']['product_note']['input']; ?>
</td>
 </tr>

</table>

<br>

<table class="border" width="100%">
	<tr>
		<td align="center">
      
        		<input type="submit" class="button"  value="<?php echo $this->_tpl_vars['buttons']['save']['value']; ?>
">
        		<input type="submit" class="button" name="cancel" value="<?php echo $this->_tpl_vars['buttons']['cancel']['value']; ?>
">

        	</td>
	</tr>
</table>








</form>


<!-- END SMARTY TEMPLATE -->