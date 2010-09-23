<?php
//Start of user code fichier htdocs/product/edit_book.php

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
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA require(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require(DOL_DOCUMENT_ROOT."/book/lib/book.lib.php");
//require(DOL_DOCUMENT_ROOT."/book/models/pdf/courrier_droit_editeur.class.php");
require(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require(DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.class.php");
require(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");

//Inclusion of necessary services and DAO
//Inclusion des DAO et des Services nécessaires
require(DOL_DOCUMENT_ROOT."/composition/class/business/service_product.class.php");
require(DOL_DOCUMENT_ROOT."/composition/class/data/dao_product_composition.class.php");
require(DOL_DOCUMENT_ROOT."/composition/class/business/service_product_composition.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract_bill.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract_bill.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract_societe.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract_societe.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/html.formbook.class.php");02111-1307, USA.
 *
 * $Id: edit_book.php,v 1.4 2010/09/23 16:20:01 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/edit_book.php,v $
 *
 */

/**
   \file       htdocs/product/edit_book.php
   \ingroup    product
   \brief      edit_book
   \version    $Revision: 1.4 $
*/

/******************************* Includes (old content of pre.inc.php) ***********************/
/*require("./pre.inc.php");*/

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/book/lib/book.lib.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");

//Inclusion of necessary services and DAO
//Inclusion des DAO et des Services nécessaires
require_once(DOL_DOCUMENT_ROOT."/composition/class/business/service_product.class.php");
require_once(DOL_DOCUMENT_ROOT."/composition/class/data/dao_product_composition.class.php");
require_once(DOL_DOCUMENT_ROOT."/composition/class/business/service_product_composition.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/data/dao_book.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/business/service_book.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract_bill.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract_bill.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract_societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract_societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/book/class/business/html.formbook.class.php");

//Loading of the necessary localization files
// chargement des fichiers de langue nécessaires
$langs->load("products");
$langs->load("@book");
$langs->load("main");
$langs->load("other");

//Smarty inclusion
// inclusion de Smarty
require_once(DOL_DOCUMENT_ROOT.'/includes/smarty/libs/Smarty.class.php');

require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");

/******************************* /Includes ***********************/




// Instanciation d'un l'objet Smarty
$oSmarty = new Smarty();
if(!is_dir($oSmarty->template_dir)) mkdir($oSmarty->template_dir);	
if(!is_dir($oSmarty->compile_dir)) mkdir($oSmarty->compile_dir);	

//$oSmarty->debugging = true;

//Sous forme d'onglets
$service = new service_book($db) ;
$product = new Product($db,$_GET['id']);


//$service = new service_book($db) ;

if ($_POST["action"] == 'update')
{
	if(!isset($_POST["cancel"]))
	{
		if($service->update($_POST) < 0)
	  	{
	  		$error = $service->getError();		
	  	}  
	  	else
	  	{
	  		Header("Location: show_book.php?id=".$_GET['id']);
	  	}
	  	
	  		
	}
	else
	{
		Header("Location: show_book.php?id=".$_GET['id']);
		
	}
	

}


llxHeader("",$langs->trans("CardProduct0"));

$head=product_prepare_head($product, $user);
$titre=$langs->trans("CardProduct".$product->type);
dolibarr_fiche_head($head, 'book', $titre);

/*********************************************************************
 *
 * Mode Edition
 *
 *
 ************************************************************************/
if($_GET['id'])
{
	

	$head[0][0] = DOL_URL_ROOT.'/book/edit_book.php?id='.$_GET['id'];
	$head[0][1] = $langs->trans("edit_book");
	$h++;
	$a = 0;

	$html = new Form($db);

	if($user->rights->book->write)
	{
		
		//Fichier action de la balise Form
		$action_form = "edit_book.php";
		$oSmarty->assign('action_form', $action_form);

		//Liste des �lements à saisir dans le formulaire
		
		$data = $service->getAttributesEdit($_GET['id'],$_POST);

		
		//Buttons
		$buttons = array(
					"save" => array(
								"value" => $langs->trans('save',$langs->trans(book))
									),
					"cancel" => array(
								"value" => $langs->trans('Cancel')			
									)
					);
		
		
		$oSmarty->assign('buttons', $buttons);
		$oSmarty->assign('data', $data);
		$oSmarty->assign('error', $error);
		$oSmarty->assign('urlForm', 'edit_book.php?id='.$_GET['id']);
			
	}
	else
	{
		$oSmarty->assign('error', $langs->trans('ErrorForbidden'));
	}
	  
 	// Affichage du template après compilation
	$oSmarty->display(DOL_DOCUMENT_ROOT.'/book/tpl/edit_book.tpl');

	
}
//End of user code
?>