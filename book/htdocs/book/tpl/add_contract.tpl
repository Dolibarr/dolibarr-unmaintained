{* Start of user code fichier htdocs/product/templates/add_contract.tpl *}
	
	
	
	
{*  Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 *	Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 *  Copyright (C) 2008 Patrick Raguin <patrick.raguin@auguria.net> 
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


	<table class="border" width="100%">
		<tr>
			<td width="15%">{$dataBook.product_ref.label}</td>
			<td width="35%" colspan="3" style="font-weight: bold;">{$dataBook.product_ref.value}</td>
		</tr>
		<tr>
			<td width="15%">{$dataBook.product_label.label}</td>
			<td width="85%" colspan="3">{$dataBook.product_label.value}</td>
			
		</tr>
	</table>
	
	<br>


  <script language="javascript" type="text/javascript" src="ajax/functions.js"></script>
	
  <form action="{$action_form}" method="post" name="add">
  
  <input type="hidden" name="action" value="add">
  <input type="hidden" name="book_contract_fk_product_book" value="{$product_book}">
   <input type="hidden" name="book_contract_fk_user" value="{$user}">
   
  <table class="border" width="100%">
  
  {foreach from=$data item=attribut}

	 <tr>
  		<td>{$attribut.label}</td>
  		<td>{$attribut.html_input}</td>
  	</tr>
  {/foreach}


  {* for each foreign key *}
  {foreach from=$listFk item=fk_form}
 	 <tr>
  		<td width="15%">{$fk_form.label}</td>
  		<td>						
  		{* for each record *}
  	<select id="{$fk_form.entity}_{$fk_form.attribute}" name="{$fk_form.entity}_{$fk_form.attribute}">
  		{if $fk_form.null == 0}<option value=""></option>{/if}
  		{foreach from=$fk_form.values key=ID item=fk_form_attribut}
  			{* Modifié par Pierre Morin le 30/03/2010, spécialisation pour l'affichage des noms des sociétés seulement : <option value="{$ID}"{if $ID == $fk_form.value} selected{/if}>{foreach from=$fk_form_attribut item=attribut}{$attribut.value} {/foreach}</option>*}
  		 	<option value="{$ID}"{if $ID == $fk_form.value} selected{/if}>{$fk_form_attribut.nom.value}</option>		
  		 {/foreach}	
  	</select>	 	
  	<input type="text" id="input_{$fk_form.entity}_{$fk_form.attribute}" onkeyup="ajaxFilter('ajax/{$fk_form.entity}_{$fk_form.attribute}_query.php','input_{$fk_form.entity}_{$fk_form.attribute}','{$fk_form.entity}_{$fk_form.attribute}')" />
  		</td>
  	</tr> 	
  {/foreach}

  	<tr>
  		<td colspan="2" align="center"><input type="submit" value="{$button}" class="button"></td>
  	</tr>
  
  </table>
  </form> 
  
  
{* Old Contracts *}
<br>

<div class="titre">{$LastContracts}</div>
<table class="noborder" width="100%">
	<tr class="liste_titre">
    {foreach from=$fields_contracts item=class_field}
    	{foreach from=$class_field key=key item=field}
		<td>{$field.label}</td>
    	{/foreach}
   {/foreach}
	</tr>

{foreach from=$listes_contracts item=obj}
	{foreach from=$obj item=class_obj}
	<tr class="{cycle values="impair,pair"}">
	{foreach from=$class_obj key=key item=att}
		{* show contract *}
		<td>{$att.value}</td>
	{/foreach}    
	</tr>
	{/foreach}
    {/foreach}  

</table>
  
{* End of user code *}
