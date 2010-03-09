{* Start of user code fichier htdocs/templates/show_product.tpl *}

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
{if !empty($errors)}
	<div class="error">
	{foreach from=$errors item=error}
		{$error}<br />
	{/foreach}
	</div>
{/if}

{if isset($delete_form)}
	{$delete_form}<br />
{/if}

{if isset($deleted)}
	<div class="warning">{$deleted}</div><br />
	{if isset($deleted_ok)}<a class="butAction" href="{$deleted_ok_link}">{$deleted_ok}</a>{/if}
{/if}

{* Show products *}
{if !empty($data) }
<table class="border" width="100%">
	{foreach from=$data item=field}
	<tr>
		<td width="35%">{$field.label}</td>
		<td>{$field.value}</td>
	</tr>
	{/foreach}
</table>

{else}
	{if !isset($deleted)}
		<div class="error">{$missing}</div>
	{/if}
{/if}
</br>

{* Liste des élements composant l'article *}
{if !empty($listesCompo) }
	<div class="titre">{$listeTitle}</div>
	<table class="noborder" width="100%">
	    <tr class="liste_titre">
	    {foreach from=$fields item=field}
	    	<td>{$field.label}</td>
	   {/foreach}
	   		<td></td>
	   		<td></td>
	    </tr>
	{foreach from=$listesCompo item=obj}
		<tr class="{cycle values="impair,pair"}">
{foreach from=$obj key=key item=att}{* Affiche le produit *}
			{if (strcmp($key,'id_compo')!=0)&&(strcmp($key,'id_product')!=0)}<td><a href="{$link}{$obj.id_product}">{$att.value}</a></td>{/if}
			
{/foreach}    
	    	<td>{if !empty($img_edit)}<a href="show_composition.php?id={$id}&compo_product={$obj.id_compo}&action=edit">{$img_edit}</a>{/if}</td>
	    	<td>{if !empty($img_edit)}<a href="show_composition.php?id={$id}&compo_product={$obj.id_compo}&action=delete">{$img_delete}</a>{/if}</td>	
	    </tr>
	    {/foreach}  

	    <tr>
	    	<td colspan="4"></td>
	    	<td>{$factory_price.label} :</td>
	    	<td>{$factory_price.value.HT} HT</td>
	    	<td>{$factory_price.value.TTC} TTC</td>
	    	<td></td>
	    </tr>
	    <tr>
	    	<td colspan="4"></td>
	    	<td>{$factory_price_all.label} :</td>
	    	<td>{$factory_price_all.value.HT} HT</td>
	    	<td>{$factory_price_all.value.TTC} TTC</td>
	    	<td></td>
	    </tr>
	</table>
	
{/if}


{if !empty($button_add) }
<br>
{* add sub-product *}	
<div class="titre">{$newCompo}</div>
<form action="show_composition.php?id={$id}" method="post" />
<input type="hidden" name="action" value="create" />
<input type="hidden" name="id_product" value="{$id}" />

<table class="border" width="100%">
{foreach from="$attributsAdd" item="attribut"}
	<tr>
		<td>{$attribut.label}</td>
		<td>{$attribut.input}</td>	
	</tr>
{/foreach}
	<tr><td colspan="2" align="center"><input type="submit" value="{$button_add}" class="button" name="edit">&nbsp; &nbsp; &nbsp; <input type="submit" value="{$button_cancel}" class="button" name="cancel"></td></tr>
</table>
</form>

{elseif !empty($button_edit)}

<div class="titre">{$editProduct}</div>
<form action="show_composition.php?id={$id}" method="post" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="{$id_compo}" />

<table class="border" width="100%">
{foreach from="$attributsEdit" item="attribut"}
	<tr>
		<td>{$attribut.label}</td>
		<td>{$attribut.input}</td>	
	</tr>
{/foreach}

	<tr><td colspan="2" align="center"><input type="submit" value="{$button_edit}" class="button" name="edit">&nbsp; &nbsp; &nbsp; <input type="submit" value="{$button_cancel}" class="button" name="cancel"></td></tr>
</table>
</form>

{elseif !isset($deleted)}
<div class="tabsAction">
<a class="butAction" href="show_composition.php?id={$id}&action=add">{$newCompo}</a>
</div>
{/if}
{* End of user code fichier htdocs/composition/templates/show_product.tpl *}
