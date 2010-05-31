<?php
//Start of user code fichier htdocs/product/index.php

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
 * $Id: index.php,v 1.1 2010/05/31 15:28:24 pit Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/index.php,v $
 *
 */
 
/**
   \file       htdocs/product/index.php
   \ingroup    
   \brief      * to complete *
   \version    $Revision: * to complete *
*/

/******************************* Includes (old content of pre.inc.php) ***********************/
/*require("./pre.inc.php");*/

require("../main.inc.php");
require(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require(DOL_DOCUMENT_ROOT."/book/lib/barcode.lib.php");
require(DOL_DOCUMENT_ROOT."/book/lib/book.lib.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/courrier_droit_editeur.class.php");
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
require(DOL_DOCUMENT_ROOT."/book/class/business/html.formbook.class.php");

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

llxHeader("",$langs->trans("Livre"));

print_fiche_titre($langs->trans("Livre"));

// Instanciation of a Smarty object
$oSmarty = new Smarty();

$oSmarty->template_dir = './tpl' ;
$oSmarty->compile_dir = './tpl_c' ;

//$oSmarty->debugging = true;

	
	// Global variables for template
	$oSmarty->assign("img_up",img_up("Z-A")) ;
	$oSmarty->assign("img_down",img_down("A-Z")) ;
	$oSmarty->assign("this_link",'index.php') ;
	$oSmarty->assign("img_search",DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png');
	$oSmarty->assign("img_searchclear",DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/searchclear.png');
	
	$listes = array() ; // lists of data to display for each entity
	$params = array() ; // lists of fields to display for each entity
	$fields = array() ; // array of strings containing the GET parameters, ready to be used in URL
	
	
	// list of book
	// object to manage the entity
	$service = new service_book($db) ;
	$entityname = "book" ;
	
	// usefull variables (name of the show page of the entity, list of the columns to show)
	$fields[$entityname] = $service->getListDisplayedFields() ;
	// will contain the get parameters for search data
	$params[$entityname] = '' ;
	// translation of the entity name
	$trans[$entityname] = $langs->trans($entityname) ;
	
	// Reset the search if button_clear is activated
	if($_GET["button_clear"]=="button_clear") $_GET = array() ;
	
	// specify a sort clause if asked
	$orderby = $service->getORDERBY($entityname, $fields) ;
	
	// specify a WHERE clause if filter are used, generate the params string too
	$where = $service->getWHERE($params,$entityname,$fields) ;
	
	// get the data from the service using the where and the order by clauses generated above
	// We are getting all
	$pages = $service->getAllListDisplayed($where,$orderby) ;
	
	// Page number to display
	$pageNumber = ($_GET["page"]=="")? 0 : $_GET["page"] ;
	// Total number of lines
	$nbtotal = count($pages);
	// We are dividing the table in sub-tables of $conf->liste_limit elements
	$pages = array_chunk($pages,$conf->liste_limit) ;
	// We are counting the number of elements on the page (for print_barre_liste)
	$num = count($pages[$pageNumber]) ;
	
	//We put only the content of the page to display
	$listes[$entityname] = $pages[$pageNumber] ; 
	
	print_barre_liste("", $pageNumber, "index.php", "&entity=".$entityname.$params[$entityname], $_GET['sortfield'], $_GET['sortorder'],'',$num+1,$nbtotal);
	
	//Buttons
	$buttons["generateAll"] = array(
		"label" => $langs->trans('YearlyMail'),
		"right" => $user->rights->book->write,
		"link" =>  DOL_URL_ROOT.'/book/add_book_contract_bill.php'
	) ;	
	
	$oSmarty->assign("buttons",$buttons);
	
	
	$oSmarty->assign("listes",$listes) ;
	$oSmarty->assign("imgProduct",img_object($langs->trans("Book"),"product")) ;
	$oSmarty->assign("fields",$fields) ;
	$oSmarty->assign("params",$params) ;
	$oSmarty->assign("types",$types) ;
	
	$oSmarty->display("index.tpl") ;

	
	//print_barre_liste("", $pageNumber, "index.php", "&entity=".$entityname.$params[$entityname], $_GET['sortfield'], $_GET['sortorder'],'',$num+1,$nbtotal);
	
	
	
	/*
	 * Generated documents management
	 */
	$filedir=$conf->book->dir_output;
	$urlsource=$_SERVER['PHP_SELF'].'?facid='.$fac->id;

	//class="pair" or "impair" for the tr
	$var = false ; $bc = array("pair","impair") ;
	
	// list of files
	$file_list=dol_dir_list($filedir."/droitauteur",'files',0,'droitauteur.*','\.meta$','name',SORT_DESC);;

	print '<br>' ;
	print_titre($langs->trans("Documents"));
	print '<table class="border" width="55%">';
		print '<tr class="liste_titre">' ;
			print '<td align="center">'.$langs->trans("File").'</td><td align="center" width="20%">'.$langs->trans("Size").'</td><td align="center" width="20%">'.$langs->trans("Date").'</td><td></td>' ;
		print '</tr>' ;
		// For each file, we add a line
		foreach($file_list as $file){
			$filepath = $filedir."/droitauteur/".$file["name"] ;
			$var = !$var ;
			print '<tr class="'.$bc[$var].'">' ;
				// Name of the file
				print '<td><a href="'.DOL_URL_ROOT . '/document.php?modulepart=book&amp;file=droitauteur/'.urlencode($file["name"]).'">'.img_pdf($file["name"],2)."&nbsp;".$file["name"].'</td>' ;
				// Size
				print '<td align="center" width="20%">'.filesize($filepath). ' bytes</td>' ;
				// Date
				print '<td align="center" width="20%">'.dolibarr_print_date(filemtime($filepath),'dayhour').'</td>' ;
				// Deleting
				print '<td align="center" width="10%"><a href="'.DOL_URL_ROOT.'/document.php?action=remove_file&amp;modulepart=book&amp;file='.urlencode($filepath).'&amp;urlsource='.urlencode($urlsource).'">'.img_delete().'</a></td>' ;
				
			print '</tr>' ;
		}
		
	print '</table>';
	
//End of user code
?>