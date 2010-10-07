<?php
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
 * $Id: liste_locker.php,v 1.1 2010/10/07 12:02:25 cdelambert Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/locker/htdocs/liste_locker.php,v $
 *
 */
 
/**
   \file       htdocs/product/stock/liste_locker.php
   \ingroup    
   \brief      * to complete *
   \version    2.4
*/

//require("./pre.inc.php");
require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/locker/class/business/service_locker.class.php");
require_once(DOL_DOCUMENT_ROOT."/locker/class/data/dao_locker.class.php");
require_once(DOL_DOCUMENT_ROOT."/locker/lib/lib.php");
include_once(DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php');
include_once(DOL_DOCUMENT_ROOT.'/lib/stock.lib.php');

// Instanciation d'un l'objet Smarty
if($user->rights->stock->lire)
{
	
	// variables globales pour le template
	$smarty->assign("img_up",img_up("Z-A")) ;
	$smarty->assign("img_down",img_down("A-Z")) ;
	$smarty->assign("this_link",'liste_locker.php') ;
	$smarty->assign("img_search",DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png');
	$smarty->assign("img_searchclear",DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/searchclear.png');
	
	$smarty->assign("buttons",array(
		"add" => array(
			"label" => $langs->trans("AddLocker"),
			"link"	=> "add_locker.php?id=".$db->escape($_GET["id"]),
			"right" => $user->rights->stock->creer
		)
	)) ;
	
	$listes = array() ; // lists of data to display for each entity
	$params = array() ; // lists of fields to display for each entity
	$fields = array() ; // array of strings containing the GET parameters, ready to be used in URL
	
	
	// list of locker
		// object to manage the entity
		$service = new service_locker($db) ;
		$entityname = "locker" ;
		
		// usefull variables (name of the show page of the entity, list of the columns to show)
		$fields[$entityname] = $service->getListDisplayedFields() ;
		// will contain the get parameters for search data
		$params[$entityname] = '' ;
		// translation of the entity name
		$trans[$entityname] = $langs->trans($entityname) ;
		
		// Reset the search if button_clear is activated
		if($_GET["button_clear"]=="button_clear") $_GET = array() ;
		
		// specify a sort clause if asked
		$orderby = getORDERBY($entityname, $fields) ;
		
		// specify a WHERE clause if filter are used, generate the params string too
		$where = getWHERE($entityname,$fields,$params) ;
		if ($where!="")
			$where .= " AND " ;
		$where.= "fk_entrepot = ".$db->escape($_GET['id']) ;
		
		// get the data from the service using the where and the order by clauses generated above
		$listes[$entityname] = $service->getAllListDisplayed($where,$orderby) ;
		
	
	$smarty->assign("listes",$listes) ;
	$smarty->assign("fields",$fields) ;
	$smarty->assign("params",$params) ;
	$smarty->assign("types",$types) ;

	
}
else
{
	$smarty->assign('errors', $langs->trans('ErrorForbidden'));
}

if ($_GET["id"])
{
if ($mesg) print $mesg;

	$entrepot = new Entrepot($db);
	$result = $entrepot->fetch($_GET["id"]);
	if ($result < 0)
	{
		dolibarr_print_error($db);
	}
	
	llxHeader("","",$langs->trans("Locker"));
	
	$head = stock_prepare_head($entrepot) ;
	
	dolibarr_fiche_head($head, $hselected, $langs->trans("Warehouse").': '.$entrepot->libelle);
	
	$smarty->display(DOL_DOCUMENT_ROOT.'/locker/tpl/liste_locker.tpl');
	
	print "</div>" ;
} else {
	Header("Location: liste.php");
}

//End of user code
?>
