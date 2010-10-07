{* Start of user code fichier htdocs/product/stock/templates/add_locker.tpl *}
{*  Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 *	Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 *  Copyright (C) 2008   < > 
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

  <script language="javascript" type="text/javascript" src="ajax/functions.js"></script>
	
  <form action="{$action_form}" method="post" name="add">
  
  <input type="hidden" name="action" value="add" />
   
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
  		<td>{$fk_form.label}</td>
  		<td>						
  		{* for each record *}
  	<select id="{$fk_form.entity}_{$fk_form.attribute}" name="{$fk_form.entity}_{$fk_form.attribute}">
  		{if $fk_form.null == 0}<option value=""></option>{/if}
  		{foreach from=$fk_form.values key=ID item=fk_form_attribut}
  			<option value="{$ID}"{if $ID == $fk_form.value} selected{/if}>{foreach from=$fk_form_attribut item=attribut}{$attribut.value} {/foreach}</option>		
  		 {/foreach}	
  	</select>	 						
  		</td>
  	</tr> 	
  {/foreach}

  	<tr>
  		<td colspan="2" align="center"><input type="submit" value="{$button}" class="button"></td>
  	</tr>
  
  </table>
  </form> 
{* End of user code *}
