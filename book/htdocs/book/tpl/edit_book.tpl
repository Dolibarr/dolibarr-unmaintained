{*
 * Copyright (C) 2006-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2008	   Patrick Raguin		<patrick.raguin@auguria.net>
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
 * $Id: edit_book.tpl,v 1.2 2010/06/07 14:16:32 pit Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/tpl/edit_book.tpl,v $
 *}

<!-- BEGIN SMARTY TEMPLATE -->

{if isset($error)}
<div class="error">{$error}</div>
{/if} 
<form action="{$urlForm}" method="POST">
<input type="hidden" name="action" value="update">
<input type="hidden" name="id" value="{$smarty.get.id}">
<input type="hidden" name="product_status" value="{$data.product_status.value}">

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
  <td width="15%">{$data.book_ean13.label}</td>
  <td width="35%">{$data.book_ean13.input}</td>
 </tr>

 <tr>
  <td width="15%">{$data.book_isbn13.label}</td>
  <td colspan="3">{$data.book_isbn13.input}</td>
 </tr>
</table>

<br>

<table class="border" width="100%">
 <tr>
  <td width="15%">{$data.product_status.label}</td>
  <td width="35%">{$data.product_status.input}</td>
  <td width="15%">{$data.product_weight.label}</td>
  <td width="35%">{$data.product_weight.input} {$data.product_weight_units.input}</td>
 </tr>
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
  <td cospan="3">{$data.product_seuil_stock_alerte.input}</td>
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

</table>

<br>

<table class="border" width="100%">
	<tr>
		<td align="center">
      
        		<input type="submit" class="button"  value="{$buttons.save.value}">
        		<input type="submit" class="button" name="cancel" value="{$buttons.cancel.value}">

        	</td>
	</tr>
</table>








</form>


<!-- END SMARTY TEMPLATE -->
