{* Start of user code fichier htdocs/product/stock/templates/fiche_locker.tpl *}

	
	
	
	
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
{if !empty($errors)}
	<div class="error">
	{foreach from=$errors item=error}
		{$error}<br>
	{/foreach}
	</div>
{/if}

{if isset($delete_form)}
	{$delete_form}<br>
{/if}

{if isset($deletion)}
	<div class="warning">{$deletion.message}</div><br>
	{if isset($deletion.link)}<a class="butAction" href="{$deletion.link}">{$deletion.ok}</a>{/if}
{/if}

{if !empty($data) }
<table class="border" width="100%">
	{foreach from=$data item=field}
	<tr>
		<td>{$field.label}</td>
		<td>{$field.value}</td>
	</tr>
	{/foreach}
</table>
	{if $buttons.edit.right || $buttons.delete.right}
<div class="tabsAction">
	{if $buttons.edit.right}<a href="{$buttons.edit.link}" class="butAction">{$buttons.edit.label}</a>{/if}
	{if $buttons.delete.right}<a href="{$buttons.delete.link}" class="butActionDelete">{$buttons.delete.label}</a>{/if}
	{/if}
</div>
{else}
	{if !isset($deletion)}
		<div class="error">{$missing}</div>
	{/if}
{/if}

{* End of user code fichier htdocs/product/stock/templates/fiche_locker.tpl *}
