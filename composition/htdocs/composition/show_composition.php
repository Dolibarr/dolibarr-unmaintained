<?php
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
 *    \file       htdocs/composition/show_composition.php
 *    \ingroup
 *    \brief      * to complete *
 *    \version    $Id: show_composition.php,v 1.7 2010/06/07 14:49:46 pit Exp $
 */

require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/composition/class/business/service_product.class.php");
require_once(DOL_DOCUMENT_ROOT."/composition/class/data/dao_product_composition.class.php");
require_once(DOL_DOCUMENT_ROOT."/composition/class/business/service_product_composition.class.php");
require_once(DOL_DOCUMENT_ROOT."/composition/lib/composition.lib.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");

$langs->load("bills");
$langs->load("products");
$langs->load("other");


llxHeader("",$langs->trans("CardProduct".$product->type));

$smarty->template_dir = './tpl' ;

//$oSmarty->debugging = true;

//Recuperation des infos sur le produit
$product = new Product($db);
$product->fetch($_GET['id']);

//Affichage onglets
$head=product_prepare_head($product, $user);
$titre=$langs->trans("CardProduct".$product->type);
$picto=($product->type==1?'service':'product');
dol_fiche_head($head, 'tabComposedProducts', $titre, 0, $picto);

//Verification des autorisations
if (!$user->rights->produit->lire) accessforbidden();


$html = new Form($db);

$service = new service_product_composition($db);
$service_product = new service_product($db) ;


/* ******************************************
 * Mise a jour composition
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
	$smarty->assign('button_edit',$langs->trans("Save")) ;
	$smarty->assign('button_cancel',$langs->trans("Cancel")) ;
	$smarty->assign('id_compo',$_GET['compo_product']) ;

	//Liste des produits pour la liste deroulante
	$attributsEdit = $service->getAttributesEdit($_GET['compo_product']);
	$smarty->assign("attributsEdit",$attributsEdit) ;
	
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
	$smarty->assign("button_add",$langs->trans("addCompo"));
	$smarty->assign('button_cancel',$langs->trans("Cancel")) ;
	
	$attributsAdd = $service->getAttributesAdd();
	$smarty->assign("attributsAdd",$attributsAdd) ;
	
	//Liste des produits pour la liste deroulante
	$smarty->assign("liste_products","") ;
	
	//Liste etat_stock
	//$smarty->assign("liste_etatStock",$service->getListEtatStock());

}


/* ******************************************
 * Demande de confirmation pour suppression
 ********************************************/
if ($_GET["action"] == 'delete')
{
	$smarty->assign('delete_form',$service->confirmDeleteForm()) ;
}
/* ***********
 * Suppression
 **************/
if (($_POST["action"] == 'confirm_delete')&&($_POST["confirm"] == 'yes'))
{
	$deleted = $service->delete($_GET['compo_product']) ;
	
	if($deleted==0){
	
		$smarty->assign('deleted',$langs->trans("deleted",$langs->trans("product"))) ;
		$smarty->assign('deleted_ok',$langs->trans("ok")) ;
		$smarty->assign('deleted_ok_link',DOL_URL_ROOT.'/composition/show_composition.php?id='.$_GET['id']) ;
	} else {
		$smarty->assign('errors',$service->getErrors()) ;
	}
	
	
} else {

	if (isset($_GET['id']))
	{
		
		/* ******************************************
		 * Affichage informations sur le produit
		 ********************************************/		
		$smarty->assign('data',$service_product->getAttributesShowLight($_GET['id'])) ;
	
		//Modification et suppression
		if ($user->rights->produit->creer)
		{
			$smarty->assign("img_edit",img_edit());
			$smarty->assign("img_delete",img_delete());
		}
		
		/* ******************************************
		 * Liste des compositions
		 ********************************************/
		$smarty->assign("fields",$service->getListDisplayedFields()) ;
		$smarty->assign("listesCompo",$service->getCompoProductsDisplay($_GET['id'])) ;
		
		//cout de revient total
		$factory_price_all['label'] = $langs->trans('FactoryPriceAll');
		$factory_price_all['value'] = $service->getFactoryPriceAll($_GET['id']);
		$smarty->assign("factory_price_all",$factory_price_all);

		//cout de revient
		$factory_price['label'] = $langs->trans('FactoryPrice');
		$factory_price['value'] = $service->getFactoryPrice($_GET['id']);
		$smarty->assign("factory_price",$factory_price);		
		
		// variables pour le template
		$smarty->assign("listeTitle",$langs->trans("listeComposition"));
		$smarty->assign("addProduct",$langs->trans("newCompo"));
		$smarty->assign("product",$langs->trans("product"));
		$smarty->assign("qte",$langs->trans("qte"));
		$smarty->assign("etatStock",$langs->trans("etatStock"));
		$smarty->assign("link",DOL_URL_ROOT.'/product/fiche.php?id=');

		$smarty->assign("newCompo",$langs->trans("newCompo"));
		$smarty->assign("id",$_GET['id']);
		
		//Liste des erreurs
		$smarty->assign("errors",$errors);
		

	}
}

$smarty->assign('missing',$langs->trans('not_found', $langs->trans('product'))) ;

$smarty->display("show_composition.tpl") ;

//End of user code

llxFooter('$Date: 2010/06/07 14:49:46 $ - $Revision: 1.7 $');

?>
