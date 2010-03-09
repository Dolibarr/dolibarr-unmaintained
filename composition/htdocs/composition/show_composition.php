<?php
//Start of user code fichier htdocs/composition/show_composition.php

/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
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
   \file       htdocs/composition/show_composition.php
   \ingroup    
   \brief      * to complete *
   \version    $Id: show_composition.php,v 1.2 2010/03/09 15:48:56 cdelambert Exp $
*/

require("./pre.inc.php");

llxHeader("","",$langs->trans("CardProduct".$product->type));


// Instanciation d'un l'objet Smarty
$oSmarty = new Smarty();

$oSmarty->template_dir = './templates' ;
$oSmarty->compile_dir = $dolibarr_smarty_compile;

//$oSmarty->debugging = true;

//R�cup�ration des infos sur le produit
$product = new Product($db);
$product->fetch($_GET['id']);

//Affichage onglets
$head=product_prepare_head($product, $user);
$titre=$langs->trans("CardProduct".$product->type);
dolibarr_fiche_head($head, 'tabComposedProducts', $titre);

//V�rification des autorisations
if (!$user->rights->produit->lire) accessforbidden();


$html = new Form($db);

$service = new service_product_composition($db);
$service_product = new service_product($db) ;


/* ******************************************
 * Mise � jour composition
 ********************************************/

if ($_POST["action"] == 'update')
{

	if(!isset($_POST["cancel"]))
	{
		$error = $service->update($_POST);
		if($error != "")
		{
			$_GET['action'] = "edit";
			$_GET['compo_product'] = $_POST['id'];
			$errors[] = $error;
		}		
	}

	
}

/* ******************************************
 * Edition compo - affichage formulaire
 ********************************************/
if ($_GET["action"] == 'edit')
{
	$oSmarty->assign('button_edit',$langs->trans("Save")) ;
	$oSmarty->assign('button_cancel',$langs->trans("Cancel")) ;
	$oSmarty->assign('id_compo',$_GET['compo_product']) ;

	//Liste des produits pour la liste d�roulante
	$attributsEdit = $service->getAttributesEdit($_GET['compo_product']);
	$oSmarty->assign("attributsEdit",$attributsEdit) ;
	
	//Liste etat_stock
}



/* ******************************************
 * Ajout d'une nouvelle composition
 ********************************************/
if ($_POST["action"] == 'create')
{
	if(!isset($_POST["cancel"]))
	{	
		$id = $service->add($_POST);
		if($id == -1)
		{
			$errors[] = $service->getError();
			$_GET["action"] = 'add';
		}		
	}

}

/* ******************************************
 * Nouvelle composition - affichage formulaire
 ********************************************/
if ($_GET["action"] == 'add')
{
	$oSmarty->assign("button_add",$langs->trans("addCompo"));
	$oSmarty->assign('button_cancel',$langs->trans("Cancel")) ;
	
	$attributsAdd = $service->getAttributesAdd();
	$oSmarty->assign("attributsAdd",$attributsAdd) ;
	
	//Liste des produits pour la liste d�roulante
	$oSmarty->assign("liste_products","") ;
	
	//Liste etat_stock
	//$oSmarty->assign("liste_etatStock",$service->getListEtatStock());

}


/* ******************************************
 * Demande de confirmation pour suppression
 ********************************************/
if ($_GET["action"] == 'delete')
{
	$oSmarty->assign('delete_form',$service->confirmDeleteForm()) ;
}
/* ***********
 * Suppression
 **************/
if (($_POST["action"] == 'confirm_delete')&&($_POST["confirm"] == 'yes'))
{
	$deleted = $service->delete($_GET['compo_product']) ;
	
	if($deleted==0){
	
		$oSmarty->assign('deleted',$langs->trans("deleted",$langs->trans("product"))) ;
		$oSmarty->assign('deleted_ok',$langs->trans("ok")) ;
		$oSmarty->assign('deleted_ok_link',DOL_URL_ROOT.'/composition/show_composition.php?id='.$_GET['id']) ;
	} else {
		$oSmarty->assign('errors',$service->getErrors()) ;
	}
	
	
} else {

	if (isset($_GET['id']))
	{
		
		/* ******************************************
		 * Affichage informations sur le produit
		 ********************************************/		
		$oSmarty->assign('data',$service_product->getAttributesShowLight($_GET['id'])) ;
	
		//Modification et suppression
		if ($user->rights->produit->creer)
		{
			$oSmarty->assign("img_edit",img_edit());
			$oSmarty->assign("img_delete",img_delete());
		}
		
		/* ******************************************
		 * Liste des compositions
		 ********************************************/
		$oSmarty->assign("fields",$service->getListDisplayedFields()) ;
		$oSmarty->assign("listesCompo",$service->getCompoProductsDisplay($_GET['id'])) ;
		
		//cout de revient total
		$factory_price_all['label'] = $langs->trans('FactoryPriceAll');
		$factory_price_all['value'] = $service->getFactoryPriceAll($_GET['id']);
		$oSmarty->assign("factory_price_all",$factory_price_all);

		//cout de revient
		$factory_price['label'] = $langs->trans('FactoryPrice');
		$factory_price['value'] = $service->getFactoryPrice($_GET['id']);
		$oSmarty->assign("factory_price",$factory_price);		
		
		// variables pour le template
		$oSmarty->assign("listeTitle",$langs->trans("listeComposition"));
		$oSmarty->assign("addProduct",$langs->trans("newCompo"));
		$oSmarty->assign("product",$langs->trans("product"));
		$oSmarty->assign("qte",$langs->trans("qte"));
		$oSmarty->assign("etatStock",$langs->trans("etatStock"));
		$oSmarty->assign("link",DOL_URL_ROOT.'/product/fiche.php?id=');

		$oSmarty->assign("newCompo",$langs->trans("newCompo"));
		$oSmarty->assign("id",$_GET['id']);
		
		//Liste des erreurs
		$oSmarty->assign("errors",$errors);
		

	}
}

$oSmarty->assign('missing',$langs->trans('not_found', $langs->trans('product'))) ;

$oSmarty->display("show_composition.tpl") ;

//End of user code

llxFooter('$Date: 2010/03/09 15:48:56 $ - $Revision: 1.2 $');

?>
