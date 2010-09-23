<?php
//Start of user code fichier htdocs/book/add_book.php
	
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
 * $Id: add_book.php,v 1.4 2010/09/23 16:20:01 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/add_book.php,v $
 *
 */

/**
   \file       htdocs/product/add_book.php
   \ingroup    product
   \brief      add_book
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

/******************************* /Includes ***********************/


// Instanciation of a Smarty object
$oSmarty = new Smarty();
if(!is_dir($oSmarty->template_dir)) mkdir($oSmarty->template_dir);	
if(!is_dir($oSmarty->compile_dir)) mkdir($oSmarty->compile_dir);	

 if ($_POST["action"] == 'add')
{
	

	$service = new service_book($db) ;
	$id = $service->add($_POST) ;
	
	if($id != -1)
	{
      	Header("Location: show_book.php?id=".$id);
	}
	else
	{
		$error = $service->getError();
	}
	

}

/*********************************************************************
 *
 * Creation mode
 *
 *
 ************************************************************************/
llxHeader('',$langs->trans("add_book"),"add_book");

$head[0][0] = DOL_URL_ROOT.'/book/add_book.php';
$head[0][1] = $langs->trans("add_book");
$h++;
$a = 0;

dolibarr_fiche_head($head, 0, $langs->trans("book"));


$html = new Form($db);


if($user->rights->book->write)
{
	
	//Action file of Form tag
	$action_form = "add_book.php";
	$oSmarty->assign('action_form', $action_form);

	
	//List of elements to fill in form

	
	$service = new service_book($db) ;
	$data = $service->getAttributesAdd($_POST);

	$oSmarty->assign('listFk', $listFk);
	
	//Product caracteristics
	$product_caracteristics["type"] = 0; //a book is a regular product
	$product_caracteristics["canvas"] = "view@book"; //canvas to the display book informations
	$product_caracteristics["finished"] = 1; //a book is not raw material, it's a finishe product
	
	
	//Adding Smarty variables
	$oSmarty->assign('button', $langs->trans('add',$langs->trans('book')));
	$oSmarty->assign('data', $data);
	$oSmarty->assign('error', $error);
	$oSmarty->assign('product_caracteristics', $product_caracteristics);

	
}
else
{
	$oSmarty->assign('error', $langs->trans('ErrorForbidden'));
}
 	//Template displaying after compilation
	$oSmarty->display(DOL_DOCUMENT_ROOT.'/book/tpl/add_book.tpl');


//End of user code
?>
