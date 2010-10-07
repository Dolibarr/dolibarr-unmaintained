{* Start of user code fichier htdocs/product/stock/templates/liste_locker.tpl *}
	
	
	
	
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
{if !isset($errors)}
{foreach from=$listes key=entityname item=liste}
	<form method="GET" action="{$smarty.server.PHP_SELF}">
	<input type="hidden" name="id" value="{$smarty.get.id}"></input>
	<table class="noborder" width="100%">
		<tr class="liste_titre">{strip}
	{foreach from=$fields.$entityname item=field}
	    	<td>{$field.label}<a href="{$this_link}?id={$smarty.get.id}&entity={$entityname}&sortfield={$field.attribute}&sortorder=asc{$params.$entityname}">{$img_down}</a><a href="{$this_link}?id={$smarty.get.id}&entity={$entityname}&sortfield={$field.attribute}&sortorder=desc{$params.$entityname}">{$img_up}</a></td>
	{/foreach}
	    <td></td>
	   </tr>{/strip}
	<input type="hidden" name="search" value="{$entityname}" />
		<tr class="liste_titre">{strip}
		{foreach from=$fields.$entityname item=field}
	    	{assign var='att_name' value=$entityname|cat:$field.attribute}
	    	<td class="liste_titre">{if ($field.type != 'datetime')&&($field.type != '')}<input class="flat" type="text" size="10" name="{$att_name}" value="{$smarty.get.$att_name}" />{/if}</td>
		{/foreach}
		    <td class="liste_titre" align="right">
		    	<input value="button_search" class="liste_titre" src="{$img_search}" name="button_search" alt="search" type="image">
		    	<input value="button_clear" class="liste_titre" src="{$img_searchclear}" name="button_clear" alt="clear" type="image">
		    </td>
	    </tr>
		{/strip}{foreach from=$liste item=obj}
		<tr class="{cycle values="impair,pair"}">{strip}
			{foreach from=$obj key=key item=att}
	    		<td>{if $att.link!=''}<a href="{$att.link}">{/if}{$att.value}{if $att.link!=''}</a>{/if}</td>
		{/foreach}
	    	<td></td>
	    </tr>{/strip}
{/foreach}
	</table>
	</form>
	<br>
{/foreach}

	{if $buttons.add.right}
<div class="tabsAction">
	{if $buttons.add.right}<a href="{$buttons.add.link}" class="butAction">{$buttons.add.label}</a>{/if}
</div>
	{/if}
{else}
	<div class="error">{$errors}</div>
{/if} 

{* End of user code fichier htdocs/product/stock/templates/liste_locker.tpl *}
