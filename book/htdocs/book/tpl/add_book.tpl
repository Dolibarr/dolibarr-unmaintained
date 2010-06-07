{* Copyright (C) 2006-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2006-2007 Auguria SARL         <info@auguria.org>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * $Id: add_book.tpl,v 1.2 2010/06/07 14:16:32 pit Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/tpl/add_book.tpl,v $
 *}
<!-- BEGIN SMARTY TEMPLATE -->


{if isset($error)}
<div class="error">{$error}</div>
{/if} 
<table width="100%" border="0" class="notopnoleftnoright">
<tr>
	<td class="notopnoleftnoright" valign="middle">
    	<div class="titre">Nouveau Livre</div>
	</td>
</tr>
</table>

<form id="evolForm" action="add_book.php" method="post">
<input type="hidden" name="action" value="add">
<input type="hidden" name="product_type" value="{$product_caracteristics.type}">
<input type="hidden" name="product_canvas" value="{$product_caracteristics.canvas}">
<input type="hidden" name="product_finished" value="{$product_caracteristics.finished}">

<table class="border" width="100%">
 <tr>
   <td width="15%">{$data.product_ref.label}</td>
   <td colspan="3">{$data.product_ref.input}</td>
 </tr>

 <tr>
  <td width="15%">{$data.product_label.label}</td>
  <td width="85%" colspan="3">{$data.product_label.input}</td>
 </tr>
</table>

<br>
 

<table class="border" width="100%">
 <tr>
  <td width="15%">{$data.book_isbn.label}</td>
  <td width="35%">{$data.book_isbn.input} </td>
  <td width="15%">{$data.book_isbn13.label}</td>
  <td width="35%">{$data.book_isbn13.input}</td>
 </tr>

 <tr>
  <td width="15%">{$data.book_ean.label}</td>
  <td width="35%">{$data.book_ean.input}</td>
  <td width="15%">{$data.book_ean13.label}</td>
  <td width="35%">{$data.book_ean13.input}</td>
 </tr>
</table>

<br>

<table class="border" width="100%">
 <tr>
  <td width="15%">{$data.book_nbpages.label}</td>
  <td width="35%">{$data.book_nbpages.input}</td>
  <td width="15%">{$data.book_format.label}</td>
  <td width="35%">{$data.book_format.input}</td>
 </tr>
</table> 

<br>

<table class="border" width="100%">

 <tr>
  <td width="15%">{$data.product_price.label}</td>
  <td width="35%">{$data.product_price.input} {$data.product_price_base_type.input}</td>
  <td width="15%">{$data.product_tva_tx.label}</td>
  <td width="35%">{$data.product_tva_tx.input}</td>
 </tr>
</table>

<br />

<table class="border" width="100%">
 <tr>
  <td width="15%">{$data.product_seuil_stock_alerte.label}</td>
  <td width="35%">{$data.product_seuil_stock_alerte.input}</td>
  <td width="15%">{$data.product_weight.label}</td>
  <td>{$data.product_weight.input} {$data.product_weight_units.input}</td>
 </tr>
 <tr>
  <td width="15%">{$data.product_status.label}</td>
  <td colspan="3">{$data.product_status.input}</td>

 </tr>
</table>

<br />

<table class="border" width="100%">
 <tr>
  <td width="15%" valign="top">{$data.product_description.label}</td>
  <td width="85%" colspan="3">{$data.product_description.input}</td>
 </tr>

 <tr>
  <td width="15%" valign="top">{$data.product_note.label}</td>
  <td width="85%" colspan="3">{$data.product_note.input}</td>
 </tr>
 <tr>
  <td colspan="4" align="center">
   <input type="submit" class="button" value="Créer">
  </td>
 </tr>

</table>
</form>

<!-- END SMARTY TEMPLATE -->
