<?php /* Smarty version 2.6.26, created on 2010-05-10 14:10:47
         compiled from /var/www/eclipse/dolibarrLast/htdocs/boo./tpl/add_book.tpl */ ?>
<!-- BEGIN SMARTY TEMPLATE -->


<?php if (isset ( $this->_tpl_vars['error'] )): ?>
<div class="error"><?php echo $this->_tpl_vars['error']; ?>
</div>
<?php endif; ?> 
<table width="100%" border="0" class="notopnoleftnoright">
<tr>
	<td class="notopnoleftnoright" valign="middle">
    	<div class="titre">Nouveau Livre</div>
	</td>
</tr>
</table>

<form id="evolForm" action="add_book.php" method="post">
<input type="hidden" name="action" value="add">
<input type="hidden" name="product_type" value="<?php echo $this->_tpl_vars['product_caracteristics']['type']; ?>
">
<input type="hidden" name="product_canvas" value="<?php echo $this->_tpl_vars['product_caracteristics']['canvas']; ?>
">
<input type="hidden" name="product_finished" value="<?php echo $this->_tpl_vars['product_caracteristics']['finished']; ?>
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
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_isbn13']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['book_isbn13']['input']; ?>
</td>
 </tr>

 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_ean']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['book_ean']['input']; ?>
</td>
  <td width="15%"><?php echo $this->_tpl_vars['data']['book_ean13']['label']; ?>
</td>
  <td width="35%"><?php echo $this->_tpl_vars['data']['book_ean13']['input']; ?>
</td>
 </tr>
</table>

<br>

<table class="border" width="100%">
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
  <td width="35%"><?php echo $this->_tpl_vars['data']['product_seuil_stock_alerte']['input']; ?>
</td>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_weight']['label']; ?>
</td>
  <td><?php echo $this->_tpl_vars['data']['product_weight']['input']; ?>
 <?php echo $this->_tpl_vars['data']['product_weight_units']['input']; ?>
</td>
 </tr>
 <tr>
  <td width="15%"><?php echo $this->_tpl_vars['data']['product_status']['label']; ?>
</td>
  <td colspan="3"><?php echo $this->_tpl_vars['data']['product_status']['input']; ?>
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
 <tr>
  <td colspan="4" align="center">
   <input type="submit" class="button" value="Créer">
  </td>
 </tr>

</table>
</form>

<!-- END SMARTY TEMPLATE -->