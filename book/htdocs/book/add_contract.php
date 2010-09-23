<?php
//Start of user code fichier htdocs/product/add_contract.php

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
 *
 * $Id: add_contract.php,v 1.4 2010/09/23 16:20:01 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/add_contract.php,v $
 *
 */

/**
   \file       htdocs/product/add_contract.php
   \ingroup    product
   \brief      add_contract
   \version    1.0
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

/******************************* /Includes ***********************/

// Instanciation d'un l'objet Smarty
$oSmarty = new Smarty();
if(!is_dir($oSmarty->template_dir)) mkdir($oSmarty->template_dir);	
if(!is_dir($oSmarty->compile_dir)) mkdir($oSmarty->compile_dir);	

$service = new service_book_contract($db) ;

if ($_POST["action"] == 'add')
{

	$newContract = $service->newContract($_POST);
	if($newContract < 0)
	{
		$error = $service->getError(); 
	}
	else
	{
		Header("Location: show_book.php?id=".$_POST['book_contract_fk_product_book']);
	}
}


/*********************************************************************
 *
 * Mode creation
 *
 *
 ************************************************************************/
llxHeader('',$langs->trans("add_contract"),"add_contract");

$head[0][0] = DOL_URL_ROOT.'/book/add_contract.php';
$head[0][1] = $langs->trans("add_contract");
$h++;
$a = 0;

dolibarr_fiche_head($head, 0, $langs->trans("book_contract"));


$html = new Form($db);



if($user->rights->book->write)
{
	
	$serviceBook = new service_book($db); 
	$dataBook = $serviceBook->getAttributesShow($_GET['product']) ;
	$oSmarty->assign('dataBook',$dataBook) ;

	
	// file to call when to form is validate
	$action_form = "add_contract.php?product=".$_GET['product'];
	$oSmarty->assign('action_form', $action_form);

	
	// elements of the form
	$data = $service->getAttributesAdd($_POST);

	// foreign keys to display in the form
	$listFk = $service->getAllFkDisplayed();
	
	//Ajout variables smarty
	$oSmarty->assign('button', $langs->trans('add',$langs->trans('book_contract')));
	$oSmarty->assign('data', $data);
	$oSmarty->assign('user', $user->id);
	$oSmarty->assign('product_book', $_GET['product']);
	$oSmarty->assign('listFk', $listFk);
	$oSmarty->assign('error', $error);
	$oSmarty->assign('LastContracts', $langs->trans('LastContracts'));

	
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
	$where = " book_contract.fk_product_book = ".$_GET['product'];

	$listes_contracts[$entityname] = $service_book_contract->getAllListDisplayed($where,$orderby) ;
	
	$oSmarty->assign("fields_contracts",$fields_contracts) ;
	$oSmarty->assign("listes_contracts",$listes_contracts) ;

}
else
{
	$oSmarty->assign('error', $langs->trans('ErrorForbidden'));
}
 	// Affichage du template après compilation
	$oSmarty->display(DOL_DOCUMENT_ROOT.'/book/tpl/add_contract.tpl');


//End of user code
?>