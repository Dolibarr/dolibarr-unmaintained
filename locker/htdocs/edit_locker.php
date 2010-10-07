<?php
//Start of user code fichier htdocs/product/stock/edit_locker.php
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
 * $Id: edit_locker.php,v 1.1 2010/10/07 12:02:24 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/locker/htdocs/edit_locker.php,v $
 *
 */

/**
   \file       htdocs/product/stock/edit_locker.php
   \ingroup    product/stock
   \brief      edit_casier
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

if ($_POST["action"] == 'update')
{
	$error = $service->update($_POST) ;
  	if($error == '')
  	{
  	}  
}

/*********************************************************************
 *
 * Mode Edition
 *
 *
 ************************************************************************/
if($_GET['id'])
{
	llxHeader('',$langs->trans("editCasiersTitle"),$langs->trans("editCasiersTitle"));

	$head[0][0] = DOL_URL_ROOT.'/product/stock/edit_locker.php?id='.$_GET['id'];
	$head[0][1] = $langs->trans("editCasiersTitle");
	$h++;
	$a = 0;
	
	dolibarr_fiche_head($head, 0, $langs->trans("Locker"));
	
	
	$html = new Form($db);


	if($user->rights->stock->creer)
	{
	
		
		// file to call when to form is validate
		$action_form = "edit_locker.php";
		$smarty->assign('action_form', $action_form);


		// elements of the form
		$data = $service->getAttributesEdit($_GET['id']);
		
		// foreign keys to display in the form
		$listFk = $service->getAllFkDisplayed($_GET['id']);
		
		$smarty->assign('listFk', $listFk);
		$smarty->assign('button_edit', $langs->trans('edit',$langs->trans('Locker')));
		$smarty->assign('button_cancel', $langs->trans('Cancel'));
		$smarty->assign('tab_form', $data);
		$smarty->assign('id', $_GET['id']);
		$smarty->assign('error', $error);
			
	}
	else
	{
		$smarty->assign('error', $langs->trans('ErrorForbidden'));
	}
	  
 	// Affichage du template apr?s compilation
	$smarty->display(DOL_DOCUMENT_ROOT.'/locker/tpl/edit_locker.tpl');

	
}
//End of user code
?>
