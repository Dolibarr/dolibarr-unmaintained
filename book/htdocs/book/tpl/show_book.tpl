{*
 * Copyright (C) 2006-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2008			Patrick Raguin			<patrick.raguin@auguria.net>
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
 * $Id: show_book.tpl,v 1.2 2010/06/07 14:16:32 pit Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/tpl/show_book.tpl,v $
 *}

<!-- BEGIN SMARTY TEMPLATE -->
{if isset($data)}
	<table class="border" width="100%">
		<tr>
			<td width="15%">{$data.product_ref.label}</td>
			<td width="35%" colspan="3" style="font-weight: bold;">{$data.product_ref.value}</td>
		</tr>
		<tr>
			<td width="15%">{$data.product_label.label}</td>
			<td width="85%" colspan="3">{$data.product_label.value}</td>
			
		</tr>
	</table>
	
	
	<br>
	
	<table class="border" width="100%">
		<tr>
			<td width="15%">{$data.book_isbn.label}</td>
			<td width="35%" colspan="3">{$data.book_isbn.value}</td>
		</tr>
		<tr>
			<td width="15%">{$data.book_isbn13.label}</td>
			<td width="35%">{$data.book_isbn13.value}</td>
			<td width="15%">{$data.book_ean13.label}</td>
			<td width="35%">{$data.book_ean13.value}</td>
	</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
	
		<tr>
			<td width="15%">{$data.product_envente.label}</td>
			<td width="35%">{$data.product_envente.value}</td>
			<td width="15%">{$data.product_weight.label}</td>
			<td width="35%">{$data.product_weight.value} {$data.product_weight_units.value}</td>
		</tr>
		
		<tr>
			<td width="15%">{$data.book_nbpages.label}</td>
			<td width="35%">{$data.book_nbpages.value}</td>
			<td width="15%">{$data.book_format.label}</td>
			<td width="35%">{$data.book_format.value}</td>
		</tr>
	
	</table>
	
	<br>
	
	<table class="border" width="100%">
	
		<tr>
	 		<td width="15%">{$data.product_price.label}</td>
	 		<td width="35%">{$data.product_price.value} HT</td>
	 		<td width="15%">{$data.product_price_ttc.label}</td>
	 		<td width="35%"><b>{$data.product_price_ttc.value} TTC</b></td>
		</tr>
		
	    <tr>
	    	<td width="15%">{$factory_price.label} HT </td>
	    	<td width="35%">{$factory_price.value.HT} HT  ({$factory_price_value_ht_fr} Fr)</td>
	    	<td width="15%">{$factory_price.label} TTC</td>
	    	<td width="35%"><b>{$factory_price.value.TTC} TTC</b>  ({$factory_price_value_ttc_fr} Fr)</td>
	    </tr>
	    <tr>
	    	<td width="15%">{$factory_price_all.label} HT</td>
	    	<td width="35%">{$factory_price_all.value.HT} HT  ({$factory_price_all_value_ht_fr} Fr)</td>
	    	<td width="15%">{$factory_price_all.label} TTC</td>
	    	<td width="35%"><b>{$factory_price_all.value.TTC} TTC</b>  ({$factory_price_all_value_ttc_fr} Fr)</td>
	    </tr>
		<tr>
			<td width="15%">{$data.product_tva_tx.label}</td>
			<td colspan="3">{$data.product_tva_tx.value} %</td>
		</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
		<tr>
			<td width="15%">{$data.product_stock_reel.label}</td>
			<td width="35%">{$data.product_stock_reel.value}</td>
			<td width="15%">{$data.product_seuil_stock_alerte.label}</td>
			<td width="35%">{$data.product_seuil_stock_alerte.value}</td>
		</tr>
		<tr>
	  		<td width="15%">{$data.product_sold.label}</td>
	 		<td width="35%" colspan="3">{$data.product_sold.value}</td>
		</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
	 	<tr>
	  		<td width="15%" valign="top">{$data.product_description.label}</td>
	  		<td width="85%" colspan="3">{$data.product_description.value}</td>
	 	</tr>
	 	<tr>
	  		<td width="15%" valign="top">{$data.product_note.label}</td>
	  		<td width="85%" colspan="3">{$data.product_note.value}</td>
	 	</tr>
	</table>
	
	{if $buttons.edit.right==1}
	<div class="tabsAction">
	       <a class="butAction" href="{$buttons.edit.link}">{$buttons.edit.label}</a>
	</div>
	{/if}
	
	
	{* Liste des élements composant l'article *}
	
	<div class="titre">{$titre_compo}</div>
	<table class="noborder" width="100%">
	    <tr class="liste_titre">
	    {foreach from=$fields item=field}
	    	<td>{$field.label}</td>
	   {/foreach}
	   	<td></td>
	    </tr>
	
	{foreach from=$listesCompo item=obj}
	
		<tr class="{cycle values="impair,pair"}">
			{foreach from=$obj key=key item=att}
	    		{* Affiche le produit *}
	    		{if (strcmp($key,'id_compo')!=0)&&(strcmp($key,'id_product')!=0)}<td><a href="{$link}{$obj.id_product}">{$att.value}</a></td>{/if}
	    	{/foreach}    
	    	<td>{if !empty($img_edit)}<a href="show_composition.php?id={$id}&compo_product={$obj.id_compo}&action=edit">{$img_edit}</a><a href="show_composition.php?id={$id}&compo_product={$obj.id_compo}&action=delete">{$img_delete}</a>{/if}</td>	
	    </tr>
	    {/foreach}  
	
	</table>
	
	</div>
	
	{* Contract *}
	
	<div class="titre">{$titre_contract}</div>
	<div class="tabBar">
	
	{if isset($NoContract)}
	<div class="titre">{$NoContract}</div>
	{else}
	<table class="border" width="100%">
		<tr>
			<td width="15%">{$data_contract.book_contract_ref.label}</td>
			<td width="35%">{$data_contract.book_contract_ref.value}</td>
			<td width="15%">{$data_contract.book_contract_fk_soc.label}</td>
			<td width="35%">{$data_contract.book_contract_fk_soc.value}</td>	</tr>
	</table>
	
	<br>
	
	<table class="border" width="100%">
		<tr>
			<td width="15%">{$data_contract.book_contract_date_deb.label}</td>
			<td width="35%">{$data_contract.book_contract_date_deb.value}</td>
			<td width="15%">{$data_contract.book_contract_date_fin.label}</td>
			<td width="35%">{$data_contract.book_contract_date_fin.value}</td>
		</tr>
		<tr>
			<td>{$data_contract.book_contract_taux.label}</td>
			<td>{$data_contract.book_contract_taux.value}</td>
			<td>{$data_contract.book_contract_quantity.label}</td>
			<td>{$data_contract.book_contract_quantity.value}</td>
		</tr>
		<tr>
			<td>{$data_contract.product_droit_price.label}</td>
			<td>{$data_contract.product_droit_price.value}</td>
			<td>{$data_contract.product_droit_price_tcc.label}</td>
			<td>{$data_contract.product_droit_price_tcc.value}</td>
		</tr>
	
	</table>
	{/if}
	
	{if $buttons.generate.right==1}
	<div class="tabsAction">
	       {if !isset($NoContract)}<a class="butAction" href="{$buttons.generate.link}">{$buttons.generate.label}</a>{/if}
	       <a class="butAction" href="{$buttons.new_contract.link}">{$buttons.new_contract.label}</a> 
	       {*<a class="butAction" href="{$buttons.edit_contract.link}">{$buttons.edit_contract.label}</a>*}
	</div>
	{/if}
	
	{* bill *}
	
	<div class="titre">{$ContractsBill}</div>
	<table class="noborder" width="100%">
		<tr class="liste_titre">
	    {foreach from=$fields_bill item=class_field}
	    	{foreach from=$class_field key=key item=field}
			<td>{$field.label}</td>
	    	{/foreach}
	   {/foreach}
		</tr>
	
	{foreach from=$listes_bill item=obj}
		{foreach from=$obj item=class_obj}
		<tr class="{cycle values="impair,pair"}">
		{foreach from=$class_obj key=key item=att}
			{* show bill *}
			<td {if $att.name=="ref"}width="200"{/if}>{if $att.link != ''}<A href="{$att.link}">{$att.value}</A>{else}{$att.value}{/if}</td>
		{/foreach}    
		</tr>
		{/foreach}
	    {/foreach}  
	
	</table>
	
	
	<br>
	
	{* Old Contracts *}
	
	<div class="titre">{$OldContracts}</div>
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
{else}
<div class="titre">{$NoBook}</div>
{/if}






<!-- END SMARTY TEMPLATE -->
