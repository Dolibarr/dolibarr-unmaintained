<?php
/* Copyright (C) 2010 Denis Martin  <denimartin@hotmail.fr>
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
 */

/**
 *   	\file       pointofsale/tpl/products_categorieslist.tpl.php
 *		\ingroup    pointofsale
 *		\brief      Template page to display product selection zone with 2 levels of categories to sort products.
 *		\version    $Id: products_categorieslists.tpl.php,v 1.1 2010/11/18 10:14:03 denismartin Exp $
 *		\author		dmartin
 */

?>

<input type="hidden" id="selectedProductId" name="selectedProductId" value="0"/>

<script type="text/javascript" src="productsandcategories.js"></script>

<script type="text/javascript">

//root category
var rootCategorie = new Categorie
(
	'<?php print $this->root_categorie->id ; ?>',
	'<?php print trim($this->root_categorie->description)?trim($this->root_categorie->description):trim($this->root_categorie->label) ; ?>',
	new Array
	(
<?php foreach($this->root_categorie->filles as $cat_1) { ?>
		//First level category
		new Categorie
		(
			'<?php print $cat_1->id; ?>',
			'<?php print trim($cat_1->description)?trim($cat_1->description):trim($cat_1->label) ; ?>',
			new Array
			(
<?php	foreach($cat_1->filles as $cat_2) { ?>
				//Second level category
				new Categorie
				(
					'<?php print $cat_2->id; ?>',
					'<?php print trim($cat_2->description)?trim($cat_2->description):trim($cat_2->label) ; ?>',
					new Array (
<?php 		foreach($cat_2->products as $prod) { ?>
						new Product ( '<?php print $prod->id ; ?>', '<?php print trim($prod->libelle) ; ?>', '<?php print $prod->ref ; ?>', '<?php print price($prod->price_ttc) ; ?>' )<?php if($cat_2->products[count($cat_2->products)-1] != $prod) print ",\n" ;?>

<?php 		} ?>					)
				)<?php if($cat_1->filles[count($cat_1->filles)-1] != $cat_2) print ',' ; ?> 
<?php	} ?>
			)
		)<?php if($this->root_categorie->filles[count($this->root_categorie->filles)-1] != $cat_1) print ',' ; ?> 
<?php } ?>
	)
) ;

</script>

<table width="100%" height="100%">
	<tr>
		<th width="33%" align="center"><p class="postitre"><?php print $langs->trans("Categories") ;?></p></th>
		<th width="33%" align="center"><p class="postitre"><?php print $langs->trans("SecondCategories") ;?></p></th>
		<th width="33%" align="center"><p class="postitre"><?php print $langs->trans("Products") ;?></p></th>
	</tr>
	
	<tr>
		<td width="33%" align="center">
			<select size="10" class="listcat" id="firstList" onClick="feedSecondList(this.options[this.selectedIndex].value);" <?php if($this->statut != BASKET_DRAFT) print 'disabled="disabled" ' ;?>>
<?php 

//Check if first level categories are defined
if($this->root_categorie->filles != -1 && count($this->root_categorie->filles) > 0 ) {
	foreach($this->root_categorie->filles as $cat) {
		print '<option class="listcat" value="'.$cat->id.'">' . $cat->description . '</option>' . "\n" ;
		
	}
}
else {
	//Print error msg
	print '<option class="listcat">- Aucune catégorie définie -</option>' ;
}

?>
			</select>		
		</td>
		
		<td width="33%" align="center">
			<select size="10" class="listcat" id="secondList" name="secondList" onClick="feedThirdList(this.options[this.selectedIndex].value);" <?php if($this->statut != BASKET_DRAFT) print 'disabled="disabled" ' ;?>>
				<option class="listcat" value="">&nbsp;</option>
			</select>
		</td>

		<td width="33%" align="center">
			<select size="10" class="listcat" id="thirdList" name="thirdList" onClick="selectedProduct(this.options[this.selectedIndex].value);" <?php if($this->statut != BASKET_DRAFT) print 'disabled="disabled" ' ;?>>
				<option class="listcat">&nbsp;</option>
			</select>
		</td>
		
	</tr>
</table>
<?php

