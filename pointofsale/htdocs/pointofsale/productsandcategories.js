/**
 * Copyright (C) 2010 Denis Martin <denimartin@hotmail.fr>
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
 *   	\file       pointofsale/productsandcategories.js
 *		\ingroup    pointofsale
 *		\brief      JS file used to display products and categories in html lists and to manage dinamyc loading
 *		\version    $Id: productsandcategories.js,v 1.1 2010/10/29 16:40:50 hregis Exp $
 *		\author		dmartin
 */


var optionNoProd = new Option('- Aucun produit -', null) ;
optionNoProd.className = 'listcat' ;

var optionNoCat = new Option('- Aucune sous-categorie -', null) ;
optionNoCat.className = 'listcat' ;

var optionAllProd = new Option('Voir tous les produits', null) ;
optionAllProd.className = 'listcat' ;

var optionEmpty = new Option('\u00a0', '') ;
optionEmpty.className = 'listcat' ;

//JS Object for Categories
function Categorie(id, description, contents){
	this.id = id ;
	this.description = description ;
	this.contents = contents ;
	this.isCategory = true ;
}

//JS Object for product
function Product (id, libelle, ref, price) {
	this.id = id ;
	this.libelle = libelle ;
	this.ref = ref ;
	this.price = price ;
	this.isProduct = true ;
}

//Function that feeds the second list with categories, using the ID of the selected category in the first list
function feedSecondList(idCategorie) {
	window.document.form_cashdesk.thirdList.options.length =  0 ;
	window.document.form_cashdesk.thirdList.add(optionEmpty, null) ;
	window.document.form_cashdesk.secondList.options.length = 0 ;
	for(i in rootCategorie.contents) {
		if(rootCategorie.contents[i].id == idCategorie) {
			for(j in rootCategorie.contents[i].contents) {
				if(rootCategorie.contents[i].contents[j].id != null) {
					opt = new Option(rootCategorie.contents[i].contents[j].description, idCategorie+','+rootCategorie.contents[i].contents[j].id) ;
					opt.className = "listcat" ;
					window.document.form_cashdesk.secondList.add(opt, null) ;
				}
			}
			//Skip other categories
			break ;
		}
	}

	if(window.document.form_cashdesk.secondList.options.length == 0) {
		//No second-level category
		optionNoCat.value = idCategorie+',noCat' ;
		optionNoCat.selected = false ;
		window.document.form_cashdesk.secondList.add(optionNoCat, null) ;
	} else {
		//If there are second level categories, display 'all product' option
		optionAllProd.selected = false ;
		optionAllProd.value = idCategorie+',allProd' ;
		window.document.form_cashdesk.secondList.add(optionAllProd, null) ;
	}
}

//Function that feeds the third list with products, using the ID of the selected category in the second list
function feedThirdList(id) {
	selectedProduct("0") ;
	var i ;
	var j ;
	var k ;
	window.document.form_cashdesk.thirdList.options.length =  0 ;
	if(id != '') {
		arrId = id.split(',') ;
		//alert(arrId[0] + ' - ' + arrId[1]) ;
		if(arrId[1] == 'noCat') {
			//No second-level category
			window.document.form_cashdesk.thirdList.add(optionEmpty, null) ;
		}
		else if(arrId[1] == 'allProd') {
			//See all products
			for(i in rootCategorie.contents) {
				if(rootCategorie.contents[i].id == arrId[0]) {
					//Selected first-level category
					for(j in rootCategorie.contents[i].contents) {
						//Look for products in second-level categories
						for(k in rootCategorie.contents[i].contents[j].contents) {
							//Display each product
							if(rootCategorie.contents[i].contents[j].contents[k].isProduct) {
								opt = new Option(
										rootCategorie.contents[i].contents[j].contents[k].ref+' ('+
										rootCategorie.contents[i].contents[j].contents[k].price+' €)',
										rootCategorie.contents[i].contents[j].contents[k].id) ;
								opt.className = 'listcat' ;
								window.document.form_cashdesk.thirdList.add(opt, null) ;
							}
						}						
					}
					break ;
				}
			}
			if(window.document.form_cashdesk.thirdList.options.length == 0) {
				window.document.form_cashdesk.thirdList.add(optionNoProd, null) ;
			}
		}
		else {
			for(i in rootCategorie.contents) {
				if(rootCategorie.contents[i].id == arrId[0]) {
					//Selected first-level category
					for(j in rootCategorie.contents[i].contents) {
						if(rootCategorie.contents[i].contents[j].id == arrId[1]) {
							//Selected second-level category
							if(rootCategorie.contents[i].contents[j].contents.length != 0) {
								//There are products in selected category, add products.
								window.document.form_cashdesk.thirdList.options.length = 0 ;
								for(k in rootCategorie.contents[i].contents[j].contents) {
									if(rootCategorie.contents[i].contents[j].contents[k].id != null) {
										opt = new Option(
												rootCategorie.contents[i].contents[j].contents[k].ref+' ('+
												rootCategorie.contents[i].contents[j].contents[k].price+'€)',
												rootCategorie.contents[i].contents[j].contents[k].id) ;
										opt.className = "listcat" ;
										window.document.form_cashdesk.thirdList.add(opt, null) ;
									}
								}
							} else {
								//No products, add optionNoProd
								optionNoProd.selected = false ;
								window.document.form_cashdesk.thirdList.add(optionNoProd, null) ;
							}
							//Skip the other categories
							break ;
						}
					}
				}
				//Skip the other categories
				break ;
			}
			if(window.document.form_cashdesk.thirdList.options.length == 0) {
				window.document.form_cashdesk.thirdList.add(optionNoProd, null) ;
			}
		}
	}
}

function selectedProduct(idd) {
	//alert('selected product : ' + idd) ;
	window.document.form_cashdesk.selectedProductId.value = idd ;
}
