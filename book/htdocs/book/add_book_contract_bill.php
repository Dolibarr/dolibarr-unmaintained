<?php
//Start of user code fichier htdocs/product/add_book_contract_bill.php
	
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
 * $Id: add_book_contract_bill.php,v 1.4 2010/09/23 16:20:01 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/add_book_contract_bill.php,v $
 *
 */

/**
   \file       htdocs/product/add_book_contract_bill.php
   \ingroup    product
   \brief      add_book_contract_bill
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

$service = new service_book_contract_bill($db) ;

if ($_POST["action"] == 'generate')
{
	
	if($service->generate($_GET['id'],$_POST) < 0)
	{
		$error = $service->getError();
	}
	else

	{
		header("location:show_book.php?id=".$_GET['id']);
	}
	

	$_GET['contract'] = $_POST['contract'];
	

}


if ($_POST["action"] == 'generateAll')
{
	
	//$service_bill = new service_book_contract_bill($db);
	if($service->generateAll($_POST) < 0)
	{
		$error = $service>getError();
	}
	else
	{
		header("location:index.php");
	}
	

}


/*********************************************************************
 *
 * Mode creation
 *
 *
 ************************************************************************/
llxHeader('',$langs->trans("add_book_contract_bill"),"add_book_contract_bill");

$head[0][0] = DOL_URL_ROOT.'/book/add_book_contract_bill.php';
$head[0][1] = $langs->trans("add_book_contract_bill");
$h++;
$a = 0;

dolibarr_fiche_head($head, 0, $langs->trans("book_contract_bill"));


$html = new Form($db);



if($user->rights->book->write)
{
	
	// file to call when to form is validate
	$action_form = "add_book_contract_bill.php";
	$oSmarty->assign('action_form', $action_form);


	
	// elements of the form
	$data = $service->getAttributesAdd();
	
	if(isset($_GET['id']))
	{
		$book = new Product($db);
		$book->fetch($_GET['id']);

		$ProductTitle = img_warning()." ".$langs->trans("GenerateMailFor",$book->ref,$book->libelle);
		$oSmarty->assign('contract', $_GET['contract']);
		$oSmarty->assign('product', $_GET['id']);
	}
	else
	{
		$ProductTitle = img_warning()." ".$langs->trans('GenerateMailForAllBooks');
	}
	
	//Ajout variables smarty
	$oSmarty->assign('button', $langs->trans('Generate'));
	$oSmarty->assign('data', $data);
	$oSmarty->assign('listFk', $listFk);
	$oSmarty->assign('error', $error);
	$oSmarty->assign('Generation', $ProductTitle);

	
	
}
else
{
	$oSmarty->assign('error', $langs->trans('ErrorForbidden'));
}
 	// Affichage du template après compilation
	$oSmarty->display(DOL_DOCUMENT_ROOT.'/book/tpl/add_book_contract_bill.tpl');


//End of user code
?>