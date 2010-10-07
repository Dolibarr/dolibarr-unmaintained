<?php
//Start of user code fichier htdocs/product/stock/fiche_locker.php
/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 * Copyright (C) 2008   < >  
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
 * $Id: fiche_locker.php,v 1.1 2010/10/07 12:02:25 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/locker/htdocs/fiche_locker.php,v $
 *
 */
 
/**
   \file       htdocs/product/stock/fiche_locker.php
   \ingroup    
   \brief      * to complete *
   \version    2.4
*/

//require("./pre.inc.php");
require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/locker/class/business/service_locker.class.php");
require_once(DOL_DOCUMENT_ROOT."/locker/class/data/dao_locker.class.php");
require_once(DOL_DOCUMENT_ROOT."/locker/lib/lib.php");
require_once(DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php');
require_once(DOL_DOCUMENT_ROOT.'/lib/stock.lib.php');

llxHeader("",$langs->trans("showCasiersTitle"),$langs->trans("showCasiersTitle"));

//Sous forme d'onglets
$locker = new dao_locker($db) ;
$entrepot = new Entrepot($db);
$locker->fetch($_GET["id"]) ;
$entrepot->fetch($locker->getFk_entrepot());

$head = stock_prepare_head($entrepot) ;

$h = count($head) ;
$head[$h][0] = DOL_URL_ROOT.'/product/stock/fiche_locker.php?id='.$_GET["id"] ;
$head[$h][1] = $langs->trans("showCasiersTitle");
$h++;

dolibarr_fiche_head($head,count($head)-1, $langs->trans("Warehouse").': '.$entrepot->libelle);


if($user->rights->stock->lire)
{
	/* ******************************************
	 * Demande de confirmation pour suppression
	 ********************************************/
	if ($_GET["action"] == 'delete')
	{
		$service = new service_locker($db) ;
		$smarty->assign('delete_form',$service->confirmDeleteForm()) ;
	}
	/* ***********
	 * Suppression
	 **************/
	if (($_POST["action"] == 'confirm_delete')&&($_POST["confirm"] == 'yes'))
	{
		$service = new service_locker($db) ;
		$deleted = $service->delete($_GET['id']) ;
		
		if($deleted==0){
			
			$deletion = array(
				"message" => $langs->trans("LockerDeleted",$locker->getLibelle()),
				"ok"	  => $langs->trans("Ok"),
				"link"	  => DOL_URL_ROOT.'/product/stock/liste_locker.php?id='.$entrepot->id
			) ;
			$smarty->assign('deletion',$deletion) ;

		} else {
			$smarty->assign('errors',$service->getErrors()) ;
		}
		
		
	} else {
	/* ******************************************
	 * Get the data to show
	 ********************************************/
	if (isset($_GET['id'])){
		
		$service = new service_locker($db) ;

		// Get Data
		$data = $service->getAttributesShow($_GET['id']) ;
		$smarty->assign('data',$data) ;
		
		
			
		//Right to edit
		$buttons["edit"] = array(
			"label" => $langs->trans('Edit',$langs->trans('locker')),
			"right" => $user->rights->stock->creer,
			"link" =>  DOL_URL_ROOT.'/locker/edit_locker.php?id='.$_GET["id"]
		) ;
		
		
		//Right to delete
		
		$buttons["delete"] = array(
			"label" => $langs->trans('Delete',$langs->trans('locker')),
			"right" => $user->rights->stock->supprimer,
			"link" =>  DOL_URL_ROOT.'/locker/fiche_locker.php?id='.$_GET["id"].'&action=delete'
		) ;

	$smarty->assign('buttons',$buttons) ;
	
	}
	}
}
else
{
	$smarty->assign('errors', $langs->trans('ErrorForbidden'));
}

$smarty->assign('missing',$langs->trans('not_found', $langs->trans('locker'))) ;

$smarty->display(DOL_DOCUMENT_ROOT."/locker/tpl/fiche_locker.tpl") ;

//End of user code

?>
