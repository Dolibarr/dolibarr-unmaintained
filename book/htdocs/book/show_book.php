<?php
//Start of user code fichier htdocs/product/show_book.php

/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 * Copyright (C) 2010      Auguria SARL         <info@auguria.org>
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
 * $Id: show_book.php,v 1.2 2010/06/07 14:16:32 pit Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/show_book.php,v $
 *
 */
 
/**
   \file       htdocs/book/show_book.php
   \ingroup    
   \brief      * to complete *
   \version    $Revision: 1.2 $
*/

/******************************* Includes (old content of pre.inc.php) ***********************/
/*require("./pre.inc.php");*/

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/book/lib/barcode.lib.php");
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

/******************************* /Includes ***********************/

// chargement des fichiers de langue nécessaires
$langs->load("products");
$langs->load("@Book");
$langs->load("main");
$langs->load("other");

llxHeader("",$langs->trans("CardProduct0"));


// Instanciation d'un l'objet Smarty
$oSmarty = new Smarty();

$oSmarty->template_dir = './tpl' ;
$oSmarty->compile_dir = './tpl_c' ;

//$oSmarty->debugging = true;

//Sous forme d'onglets
$service = new service_book($db) ;
$product = new Product($db,$_GET['id']);


$head=product_prepare_head($product, $user);
$titre=$langs->trans("CardProduct".$product->type);
$picto=($product->type==1?'service':'product');
dol_fiche_head($head, 'tabBook', $titre, 0, $picto);

if($user->rights->book->read)
{

	
	
	
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
		
		$deleted = $service->delete($_GET['id']) ;
		
		if($deleted==0){
		
			$oSmarty->assign('deleted',$langs->trans("deleted",$langs->trans("book"))) ;
			$oSmarty->assign('deleted_ok',$langs->trans("ok")) ;
			$oSmarty->assign('deleted_ok_link',DOL_URL_ROOT.'/product/index.php') ;
		} else {
			$oSmarty->assign('errors',$service->getErrors()) ;
		}
		
		
	} 

	/* ******************************************
	 *                   BOOK
	 ********************************************/


	if (isset($_GET['id']))
	{
				
		if($service->isLivre($_GET['id']))
		{
		
			// Get Data
			$data = $service->getAttributesShow($_GET['id']) ;
			$oSmarty->assign('data',$data) ;
			
			//Right to modify
			$edit = $user->rights->book->write ;



						
			$buttons["edit"] = array(
				"label" => $langs->trans('edit',$langs->trans('book')),
				"right" => $user->rights->book->write,
				"link" =>  DOL_URL_ROOT.'/book/edit_book.php?id='.$_GET["id"].'&action=edit_book'
			) ;				
		
		
			
			
			//Liste des compositions
			$service_compo = new service_product_composition($db);
			$oSmarty->assign("fields",$service_compo->getListDisplayedFields()) ;
			$oSmarty->assign("listesCompo",$service_compo->getCompoProductsDisplay($_GET['id'])) ;
			$oSmarty->assign("titre_compo",$langs->trans("listeComposition"));
			$oSmarty->assign("link",DOL_URL_ROOT.'/product/fiche.php?id=');		
	
			$conversion_eur_to_fr = 6.55957;
			
			//cout de revient total
			$factory_price_all['label'] = $langs->trans('FactoryPriceAll');
			$factory_price_all['value'] = $service_compo->getFactoryPriceAll($_GET['id']);
			$oSmarty->assign("factory_price_all",$factory_price_all);
			$oSmarty->assign("factory_price_all_value_ht_fr",price($factory_price_all['value']['HT'] * $conversion_eur_to_fr));
			$oSmarty->assign("factory_price_all_value_ttc_fr",price($factory_price_all['value']['TTC'] * $conversion_eur_to_fr));
			
			//cout de revient
			$factory_price['label'] = $langs->trans('FactoryPrice');
			$factory_price['value'] = $service_compo->getFactoryPrice($_GET['id']);
			$oSmarty->assign("factory_price",$factory_price);				
			$oSmarty->assign("factory_price_value_ht_fr",price($factory_price['value']['HT'] * $conversion_eur_to_fr));
			$oSmarty->assign("factory_price_value_ttc_fr",price($factory_price['value']['TTC'] * $conversion_eur_to_fr));

	
			/* ******************************************
			 *                 CONTRACTS
			 ********************************************/
			
			$oSmarty->assign("titre_contract",$langs->trans("Contrat"));	
			$oSmarty->assign("OldContracts",$langs->trans("OldContracts"));	
			//$contract = $service->getAttributesShow($_GET['id']) ;
			$contract = new service_book_contract($db) ;
			
			if ($data['book_contract']['value'] > 0)
			{
				// Get Data
				$data_contract = $contract->getAttributesShow($data['book_contract']['value']) ;
				$oSmarty->assign('data_contract',$data_contract) ;			
			}
			else
			{
				$oSmarty->assign('NoContract',$langs->trans("NoContract")) ;
			}
	
	
				
			//Buttons
			$buttons["generate"] = array(
				"label" => $langs->trans('YearlyMail'),
				"right" => $user->rights->book->write,
				"link" =>  DOL_URL_ROOT.'/book/add_book_contract_bill.php?id='.$_GET["id"]
			) ;		
	
			$buttons["new_contract"] = array(
				"label" => $langs->trans('newContract'),
				"right" => $user->rights->livre->delete,
				"link" =>  DOL_URL_ROOT.'/book/add_contract.php?product='.$_GET["id"]
			) ;		
		
	
			/* ******************************************
			 *                 MAIL CONTRACTS
			 ********************************************/
			
			$listes_facture = array() ; // lists of data to display for each entity
			$fields_facture = array() ; // array of strings containing the GET parameters, ready to be used in URL
			
			
			// list of book_contract_facture
			// object to manage the entity
			$service_bill = new service_book_contract_bill($db) ;
			$entityname = "book_contract_bill" ;
			
			// usefull variables (name of the show page of the entity, list of the columns to show)
			$fields_bill[$entityname] = $service_bill->getListDisplayedFields() ;
			// translation of the entity name
			$trans_bill[$entityname] = $langs->trans($entityname) ;
		
			// get the data from the service using the where and the order by clauses generated above
			$listes_bill[$entityname] = $service_bill->getAllListDisplayed($where,$orderby) ;

			$oSmarty->assign("ContractsBill",$langs->trans("ContractsBill")); 
			$oSmarty->assign("listes_bill",$listes_bill) ;
			$oSmarty->assign("fields_bill",$fields_bill) ;

			/* ******************************************
			 *                 OLD CONTRACTS
			 ********************************************/
		
			$listes_contracts = array() ; // lists of data to display for each entity
			$fields_contracts = array() ; // array of strings containing the GET parameters, ready to be used in URL
		
			// list of book_contract
			// object to manage the entity
			$service_book_contract = new service_book_contract($db) ;
			$entityname = "book_contract" ;
			
			// usefull variables (name of the show page of the entity, list of the columns to show)
			$fields_contracts[$entityname] = $service_book_contract->getListDisplayedFields() ;
	
			// translation of the entity name
			$trans[$entityname] = $langs->trans($entityname) ;
	
			// get the data from the service using the where and the order by clauses generated above
			$where = " book_contract.fk_product_book = ".$_GET['id']." AND book_contract.rowid != ".$data['book_contract']['value'];
	
			$listes_contracts[$entityname] = $service_book_contract->getAllListDisplayed($where,$orderby) ;
			
			$oSmarty->assign("fields_contracts",$fields_contracts) ;
			$oSmarty->assign("listes_contracts",$listes_contracts) ;
	
			
			$oSmarty->assign('buttons',$buttons) ;
		}
		else
		{
			$oSmarty->assign('NoBook',$langs->trans('NoBook'));
		}

	}
	
}
else
{
	$oSmarty->assign('errors', $langs->trans('ErrorForbidden'));
}
	$oSmarty->assign('missing',$langs->trans('not_found', $langs->trans('book'))) ;
	
	$oSmarty->display("show_book.tpl") ;

//End of user code

llxFooter('$Date: 2010/06/07 14:16:32 $ - $Revision: 1.2 $');

?>