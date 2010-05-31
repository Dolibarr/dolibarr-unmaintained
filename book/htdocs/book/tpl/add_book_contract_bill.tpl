{* Start of user code fichier htdocs/product/templates/add_book_contract_bill.tpl *}

{*  Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 *	Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 *
 * 	This program is free software; you can redistribute it and/or modify
 * 	it under the terms of the GNU General Public License as published by
 * 	the Free Software Foundation; either version 2 of the License, or
 * 	(at your option) any later version.
 *
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU General Public License for more details.
 *
 * 	You should have received a copy of the GNU General Public License
 * 	along with this program; if not, write to the Free Software
 * 	Foundation, Inc., 59 Temple Place * Suite 330, Boston, MA 02111*1307, USA.
 *}
 
{if isset($error)}
<div class="error">{$error}</div>
{/if} 




<br>
{if isset($product)}	
<form action="add_book_contract_bill.php?id={$product}" method="post">
<input type="hidden" name="action" value="generate">
{else}
<form action="add_book_contract_bill.php" method="post">
<input type="hidden" name="action" value="generateAll">
{/if}
<table class="border" width="100%">
	{*<tr>
		<td width="25%">{$data.book_contract_bill_date_deb.label}</td>
		<td>{$data.book_contract_bill_date_deb.html_input}</td>
	</tr>
	<tr>
		<td>{$data.book_contract_bill_date_fin.label}</td>
		<td>{$data.book_contract_bill_date_fin.html_input}</td>
	</tr>*}
	<tr>
		<td width="25%">{$data.book_contract_bill_year.label}</td>
		<td>{$data.book_contract_bill_year.html_input}</td>
	</tr>

	<tr>
		<td colspan="2" align="center"><br><b>{$Generation}</b><br></td>
	
	</tr>
	

	<tr>
		<td colspan="2" align="center"><input type="submit" value="{$button}" class="button"></td>
	</tr>
  
</table>
</form> 
{* End of user code *}
