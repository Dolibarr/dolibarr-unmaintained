<?php
//Start of user code fichier htdocs/product/stock/add_locker.php

	
	
	
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
 * $Id: add_locker.php,v 1.1 2010/10/07 10:24:20 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/locker/Attic/add_locker.php,v $
 *
 */

/**
   \file       htdocs/product/stock/add_locker.php
   \ingroup    product/stock
   \brief      add_casier
   \version    2.4
*/

//require("./pre.inc.php");
require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/locker/class/business/service_locker.class.php");
require_once(DOL_DOCUMENT_ROOT."/locker/class/data/dao_locker.class.php");
require_once(DOL_DOCUMENT_ROOT."/locker/lib/lib.php");
include_once(DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php');
include_once(DOL_DOCUMENT_ROOT.'/lib/stock.lib.php');

$service = new service_locker($db) ;

 if ($_POST["action"] == 'add')
{
	$id = $service->add($_POST) ;
	
	if($id != -1)
	{
		$casier = new dao_locker($db) ;
		$casier->fetch($id) ;
      	Header("Location: liste_locker.php?id=".$casier->getFk_entrepot());
	}
	else
	{
		$error = $_GET['error'];
	}
	

}


/*********************************************************************
 *
 * Mode creation
 *
 *
 ************************************************************************/
llxHeader('',$langs->trans("addCasiersTitle"),"addCasiersTitle");

$head[0][0] = DOL_URL_ROOT.'/product/stock/add_locker.php';
$head[0][1] = $langs->trans("addCasiersTitle");
$h++;
$a = 0;

dolibarr_fiche_head($head, 0, $langs->trans("Locker"));


$html = new Form($db);



if($user->rights->stock->creer)
{
	
	// file to call when to form is validate
	$action_form = "add_locker.php";
	$smarty->assign('action_form', $action_form);

	
	// elements of the form
	$data = $service->getAttributesAdd();

	// foreign keys to display in the form
	$listFk = $service->getAllFkDisplayed($db->escape($_GET['id']));
	
	//Ajout variables smarty
	$smarty->assign('button', $langs->trans('Add',$langs->trans('Locker')));
	$smarty->assign('data', $data);
	$smarty->assign('listFk', $listFk);
	$smarty->assign('error', $error);
	
}
else
{
	$smarty->assign('error', $langs->trans('ErrorForbidden'));
}
 	// Affichage du template apr?s compilation
	$smarty->display(DOL_DOCUMENT_ROOT.'/locker/tpl/add_locker.tpl');


//End of user code
?>
